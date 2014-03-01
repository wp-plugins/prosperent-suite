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
	}
}
 
$prosperIndex = new ProsperIndexController;