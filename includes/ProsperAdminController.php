<?php
/**
 * ProsperAdmin Controller
 *
 * @package 
 * @subpackage 
 */
class ProsperAdminController
{
	protected $_options;
	
    /**
     * the class constructor
     *
     * @package 
     * @subpackage 
     *
     */
    public function __construct()
    {		
		add_action('admin_menu', array($this, 'registerSettingsPage' ), 5);
		add_action( 'network_admin_menu', array( $this, 'registerNetworkSettingsPage' ) );
		
		require_once(PROSPER_MODEL . '/Admin.php');
		$prosperAdmin = new Model_Admin();
		
		$this->_options = $prosperAdmin->getOptions();
		add_action( 'admin_init', array( $prosperAdmin, 'optionsInit' ) );
		add_action( 'admin_enqueue_scripts', array( $prosperAdmin, 'prosperAdminCss' ));
		add_action( 'admin_enqueue_scripts', array( $prosperAdmin, 'prosperSuiteMCEOpts' ));
		add_action( 'admin_init', array( $prosperAdmin, 'init' ), 20 );
		add_filter( 'plugin_action_links', array( $prosperAdmin, 'addActionLink' ), 10, 2 );
		
		require_once(PROSPER_WIDGET . '/ProsperDashStats.php');
		$prosperDashWidget = new Widget_ProsperDashStats();
		add_action('wp_dashboard_setup', array($prosperDashWidget, 'init'));
		
		/*require_once(PROSPER_WIDGET . '/PostBoxMeta.php');
		$prosperPostBox = new ProsperPostBox();
		add_action('wp_dashboard_setup', array($prosperDashWidget, 'init'));*/
	}	
	
	/**
	 * Register the menu item and its sub menu's.
	 *
	 * @global array $submenu used to change the label on the first item.
	 */
	public function registerSettingsPage() 
	{
		add_menu_page(__('Prosperent Suite Settings', 'prosperent-suite'), __( 'Prosperent', 'prosperent-suite' ), 'manage_options', 'prosper_general', array( $this, 'generalPage' ), PROSPER_IMG . '/prosperentWhite.png' );
		
		if ($this->_options['PLAct'])
		{
		    add_submenu_page('prosper_general', __( 'ProsperLinks', 'prosperent-suite' ), __( 'ProsperLinks', 'prosperent-suite' ), 'manage_options', 'prosper_prosperLinks', array( $this, 'linksPage' ) );
		}
		if ($this->_options['PSAct'])
		{
			add_submenu_page('prosper_general', __('ProsperShop', 'prosperent-suite' ), __( 'ProsperShop', 'prosperent-suite' ), 'manage_options', 'prosper_productSearch', array( $this, 'productPage' ) );
		}
		if ($this->_options['PICIAct'])
		{
			add_submenu_page('prosper_general', __( 'ProsperInsert', 'prosperent-suite' ), __( 'ProsperInsert', 'prosperent-suite' ), 'manage_options', 'prosper_autoComparer', array( $this, 'inserterPage' ) );
		}
		add_submenu_page('prosper_general', __( 'Advanced Options', 'prosperent-suite' ), __( 'Advanced', 'prosperent-suite' ), 'manage_options', 'prosper_advanced', array( $this, 'advancedPage' ) );
		
		global $submenu;
		if (isset($submenu['prosper_general']))
			$submenu['prosper_general'][0][0] = __('General Settings', 'prosperent-suite' );		
	}	
	
	/**
	 * Register the settings page for the Network settings.
	 */
	function registerNetworkSettingsPage() 
	{
		add_menu_page( __('Prosperent Suite Settings', 'prosperent-suite'), __( 'Prosperent', 'prosperent-suite' ), 'delete_users', 'prosper_general', array( $this, 'networkConfigPage' ), PROSPER_IMG . '/prosperentWhite.png' );
	}
		
	/**
	 * Loads the form for the network configuration page.
	 */
	function networkConfigPage() 
	{
		require_once(PROSPER_VIEW . '/prosperadmin/network-phtml.php' );
	}
		
	/**
	 * Loads the form for the general settings page.
	 */
	public function generalPage() 
	{
		if ( isset( $_GET['page'] ) && 'prosper_general' == $_GET['page'] )
			require_once( PROSPER_VIEW . '/prosperadmin/general-phtml.php' );
	}
		
	/**
	 * Loads the form for the product search page.
	 */
	public function productPage() 
	{
		if ( isset( $_GET['page'] ) && 'prosper_productSearch' == $_GET['page'] )
			require_once( PROSPER_VIEW . '/prosperadmin/search-phtml.php' );
	}	
	
	/**
	 * Loads the form for the inserter page.
	 */
	public function inserterPage() 
	{	
		if ( isset( $_GET['page'] ) && 'prosper_autoComparer' == $_GET['page'] )
			require_once( PROSPER_VIEW . '/prosperadmin/inserter-phtml.php' );
	}
				
	/**
	 * Loads the form for the prosperLinks page.
	 */
	public function linksPage() 
	{
		if ( isset( $_GET['page'] ) && 'prosper_prosperLinks' == $_GET['page'] )
			require_once( PROSPER_VIEW . '/prosperadmin/prosperLinks-phtml.php' );
	}	
	
	/**
	 * Loads the form for the product search page.
	 */
	public function advancedPage() 
	{	
		if ( isset( $_GET['page'] ) && 'prosper_advanced' == $_GET['page'] )
			require_once( PROSPER_VIEW . '/prosperadmin/advanced-phtml.php' );
	}	
}

$prosperAdmin = new ProsperAdminController;