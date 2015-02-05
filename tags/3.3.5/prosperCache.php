<?php
class Prosper_JG_Cache 
{    
	protected $_options;
	
    function __construct($dir)
    {
        $this->dir = $dir;
		$this->_options = get_option('prosperSuite');
    }
	
    private function _name($key)
    {
        return sprintf("%s/%s", $this->dir, sha1($key));
    }
	
    public function get($key, $expiration = 3600)
    {        
        if ( !is_dir($this->dir) || !is_writable($this->dir) || !$this->_options['Enable_Caching'])
        {
            return FALSE;
        }
        
        $cache_path = $this->_name($key);
        
        if (!@file_exists($cache_path))
        {
            return FALSE;
        }
        
        if (filemtime($cache_path) < (time() - $expiration))
        {
            $this->clear($key);
            return FALSE;
        }
        
        if (!$fp = @fopen($cache_path, 'rb'))
        {
            return FALSE;
        }
        
        flock($fp, LOCK_SH);
        
        $cache = '';
        
        if (filesize($cache_path) > 0)
        {
            $cache = unserialize(fread($fp, filesize($cache_path)));
        }
        else
        {
            $cache = NULL;
        }

        flock($fp, LOCK_UN);
        fclose($fp);
        
        return $cache;
    }
    
    public function set($key, $data)
    {                
        if ( !is_dir($this->dir) || !is_writable($this->dir) || !$this->_options['Enable_Caching'])
        {
            return FALSE;
        }
        
        $cache_path = $this->_name($key);

        if ( ! $fp = fopen($cache_path, 'wb'))
        {
            return FALSE;
        }

        if (flock($fp, LOCK_EX))
        {
            fwrite($fp, serialize($data));
            flock($fp, LOCK_UN);
        }
        else
        {
            return FALSE;
        }
        fclose($fp);

        return TRUE;
    }
    
    
    public function clear($key)
    {
        $cache_path = $this->_name($key);
        
        if (file_exists($cache_path))
        {
            unlink($cache_path);
            return TRUE;
        }
        
        return FALSE;
    }
}
