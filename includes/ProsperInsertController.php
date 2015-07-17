<?php
/**
 * ProsperInsert Controller
 *
 * @package 
 * @subpackage 
 */
class ProsperInsertController
{
	
	public $newQuery;
	
    /**
     * the class constructor
     *
     * @package 
     * @subpackage 
     *
     */
    public function __construct()
    {		
		require_once(PROSPER_MODEL . '/Inserter.php');
		$prosperInserter = new Model_Inserter();

		if(is_admin())
		{
			add_action('admin_print_footer_scripts', array($prosperInserter, 'qTagsInsert'));
		}
		else
		{ 
			add_shortcode('compare', array($prosperInserter, 'inserterShortcode'));
			add_shortcode('prosperInsert', array($prosperInserter, 'inserterShortcode'));
			add_shortcode('prosperNewQuery', array($prosperInserter, 'newQueries'));
			add_shortcode('contentInsert', array($prosperInserter, 'newQueries'));
		}

		if (isset($prosperInserter->_options['prosper_inserter_posts']) || isset($prosperInserter->_options['prosper_inserter_pages']))
		{			
			add_filter('the_content', array($prosperInserter, 'contentInserter'), 2);			
		}
    }
}
 
$prosperInsert = new ProsperInsertController;