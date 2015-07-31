<?php
/**
 * ProsperIndex Controller
 *
 * @package 
 * @subpackage 
 */
class ProsperIndexController
{	
    public function __construct()
    {			
        add_action('init', array($this, 'banBaidu'), 1);
        
		require_once(PROSPER_MODEL . '/Activate.php');
		$prosperActivate = new Model_Activate();
		
		add_action('load_textdomain', array($prosperActivate, 'doOutputBuffer'), 1);
		add_action('widgets_init', array($prosperActivate, 'createWidget'), 4);	
		
		register_activation_hook(PROSPER_PATH . PROSPER_FILE, array($prosperActivate, 'prosperActivate'));
		register_deactivation_hook(PROSPER_PATH . PROSPER_FILE, array($prosperActivate, 'prosperDeactivate'));

		add_action('admin_init', array($prosperActivate, 'prosperActivateRedirect'));
		add_action('admin_init', array($prosperActivate, 'prosperCustomAdd'));
		
		add_action('init', array($prosperActivate, 'prosperQueryTag'), 1);
		add_action('init', array($prosperActivate, 'init'));
		
		add_action('template_redirect', array($this, 'checkToFix'), 1);

		$rules = get_option('rewrite_rules');
		if (!$rules['^([^/]+)/([^/]+).cid.([a-z0-9A-Z]{32})/?$'])
		{
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if (!is_plugin_active('simple-urls/plugin.php'))
			{
				add_action( 'init', array($prosperActivate, 'prosperReroutes' ));
			}
		}		
	}
	
	public function checkToFix()
	{		
		if(get_query_var('queryParams') || get_query_var('cid') || get_query_var('keyword'))
		{
			if (has_action('wpseo_head'))
			{
				add_filter('wpseo_canonical', array($this, 'fixWpSeoCanonical'));
				return;
			}
		
			remove_action('wp_head', 'rel_canonical');
			add_action('wp_head', array($this, 'prosperFixCanonical'));
		}
	}

	public function fixWpSeoCanonical()
	{
		$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];		
		return $url;
	}
	
	public function prosperFixCanonical()
	{
		$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];			
		return '<link rel="canonical" href="' . esc_url($url) . '" />';
	}
	
	/*
	 * Credit:
	 * Plugin: WP-Ban
	 * Author: Lester 'GaMerZ' Chan
	 */
	public function banBaidu()
	{
	    $bannedIpRange = '180.76.0.0-180.76.255.255';
	    
	    $ip = $_SERVER['REMOTE_ADDR'];
	    if( strpos( $ip, ',' ) !== false ) 
	    {
	        $ip = explode( ',', $ip );
	        $ip = $ip[0];
	    }
	    $ip = ip2long(esc_attr( $ip ));
	    
	    $range = explode('-', $bannedIpRange);
	    $rangeStart = ip2long($range[0]);
	    $rangeEnd = ip2long($range[1]);
	    if($ip !== false && $ip >= $rangeStart && $ip <= $rangeEnd) 
	    {	   			
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n".
            	 '<html xmlns="http://www.w3.org/1999/xhtml" '.get_language_attributes().'>'."\n".
                	 '<head>'."\n".
                    	 '<meta http-equiv="Content-Type" content="text/html; charset='.get_option('blog_charset').'" />'."\n".
                    	 '<title>%SITE_NAME% - %SITE_URL%</title>'."\n".
                	 '</head>'."\n".
                	 '<body>'."\n".
                    	 '<div id="banContainer">'."\n".
                    	 '<p style="text-align: center; font-weight: bold;">This IP has been banned as it falls into the Baidu IP Range.</p>'."\n".
                    	 '</div>'."\n".
                	 '</body>'."\n".
            	 '</html>';
			exit;
    	}  	
	}
}
 
$prosperIndex = new ProsperIndexController;