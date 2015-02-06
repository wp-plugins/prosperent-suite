<?php
require_once(PROSPER_MODEL . '/Admin.php');
/**
 * ProsperLinker Controller
 *
 * @package 
 * @subpackage 
 */
class ProsperLinkerController extends Model_Admin
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
		require_once(PROSPER_MODEL . '/Linker.php');
		
		$prosperLinker = new Model_Linker();
	
		if(is_admin())
		{
			add_action('admin_print_footer_scripts', array($prosperLinker, 'qTagsLinker'));
		}
		else
		{
			add_shortcode('linker', array($prosperLinker, 'linkerShortcode'));
		}
    }		
}
 
$prosperLinker = new ProsperLinkerController;