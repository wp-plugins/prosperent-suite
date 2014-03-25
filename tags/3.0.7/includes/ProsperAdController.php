<?php
/**
 * ProsperAd Controller
 *
 * @package 
 * @subpackage 
 */
class ProsperAdController
{	
    /**
     * the class constructor
     *
     * @package 
     * @subpackage 
     *
     */
    public function __construct()
    {		
		require_once(PROSPER_MODEL . '/Ad.php');
		$prosperAd = new Model_Ad();

		add_shortcode('perform_ad', array($prosperAd, 'performAdShortCode'));		
		
		if (is_admin())
		{
			add_action('admin_print_footer_scripts', array($prosperAd, 'qTagsAd'));	
		}
    }

}
 
$perfAds = new ProsperAdController;