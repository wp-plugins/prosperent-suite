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
		require_once(PROSPER_MODEL . '/Activate.php');
		$prosperActivate = new Model_Activate();
		
		add_action('widgets_init', array($prosperActivate, 'createWidget'), 4);	
		
		register_activation_hook(PROSPER_PATH . PROSPER_FILE, array($prosperActivate, 'prosperActivate'));
		register_deactivation_hook(PROSPER_PATH . PROSPER_FILE, array($prosperActivate, 'prosperDeactivate'));

		add_action('admin_init', array($prosperActivate, 'prosperActivateRedirect'));
		add_action('admin_init', array($prosperActivate, 'prosperCustomAdd'));
		add_action('init', array($prosperActivate, 'doOutputBuffer'));	
		add_action('init', array($prosperActivate, 'prosperQueryTag'), 1);
		add_action('init', array($prosperActivate, 'init'));
		add_action('template_redirect', array($this, 'checkToFix'), 1);
		
		$rules = get_option('rewrite_rules');
		if (!$rules['store/go/([^/]+)/?'])
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
}
 
$prosperIndex = new ProsperIndexController;