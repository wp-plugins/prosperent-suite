<?php
/*  
 * Created by ekgrin
 */
class Prosper_Cache
{
    protected $_options;
    protected $_memcache = null;
	
    function __construct()
    {
        $this->_options = get_option('prosperSuite');

        if (!extension_loaded('memcache'))
        {
          return false;
        }

        $this->_memcache = new Memcache;	
		
		$advOptions = get_option('prosper_advanced');
        $this->_memcache->pconnect(($advOptions['MemcahceIP'] ? $advOptions['MemcahceIP'] : '127.0.0.1'), ($advOptions['MemcachePort'] ? $advOptions['MemcachePort'] : 11211));
    }

    private function _name($key)
    {
		$settings = array_filter($key);
		$cacheKey = '';

		foreach($settings as $i => $value)
		{
			$cacheKey .= $i . ':' . (string) $value; 
		}

        return md5($cacheKey);
    }

    public function get($key)
    {				
		if (empty($this->_options))
		{
			$this->_options = get_option('prosperSuite');
		}
		
        if (!$this->_options['Enable_Caching'] || !extension_loaded('memcache'))
        {
            return false;
        }

        $cache_path = $this->_name($key);

        $tmp = $this->_memcache->get($cache_path);
        if ( isset($tmp))
        {
            $cachedata = unserialize($tmp);
            return $cachedata;
        } else
        {
            return false;
        }
    }

    public function set($key, $data, $lifetime = 86400)
    {
		if (empty($this->_options))
		{
			$this->_options = get_option('prosperSuite');
		}
		
        if (!$this->_options['Enable_Caching'] || !extension_loaded('memcache'))
        {
            return false;
        }

        $cache_path = $this->_name($key);

        $flag = 0;

        $result = @$this->_memcache->set($cache_path,  serialize($data),  $flag, $lifetime);
        return $result;
    }
	
	public function clearMemcache()
	{
		$this->_memcache->flush();
	}
}
