<?php
require_once(PROSPER_MODEL . '/Base.php');
/**
 * Base Abstract Model
 *
 * @package Model
 */
class Model_Activate extends Model_Base
{
	protected $_options;
		
	public function prosperActivate()
	{
		$this->_options = $this->getOptions();
		
		$this->prosperDefaultOptions();		
		$this->prosperOptionActivateAdd();	
		if ($this->_options['PSAct'])
		{
			$this->prosperStoreInstall();
			$this->prosperReroutes();
		}
		$this->settingsPrompt('activated');
	}
	
	public function prosperDeactivate()
	{		
		$this->prosperStoreRemove();
		$this->prosperFlushRules();			
		$this->settingsPrompt('deactivated');
	}
	
	public function settingsPrompt($status)
	{
		$this->_options = $this->getOptions();
		if ($this->_options['anonymousData'])
		{
			require_once(PROSPER_MODEL . '/Admin.php');
			$this->adminModel = new Model_Admin();
			
			$this->adminModel->_settingsHistory($status);
		}
	}
	
	public function prosperOptionActivateAdd() 
	{
		add_option('prosperActivationRedirect', true);
	}

	public function prosperActivateRedirect() 
	{
		if (get_option('prosperActivationRedirect', false)) 
		{
			delete_option('prosperActivationRedirect');
			if(!isset($_GET['activate-multi']))
			{
				wp_redirect( admin_url( 'admin.php?page=prosper_general' ) );
			}
		}
	}
	
	public function prosperDefaultOptions()
	{
		if (!is_array($prosperSuiteOpts = get_option('prosperSuite')))
		{
			$prosperSuiteOpts = array(
				'Target'            => 1,
				'anonymousData'     => 1,
				'prosperNewVersion' => 1,
			    'prosperNoOptions'  => 1,
				'PSAct'	  		    => 1,
				'PAAct'	  		    => 1,
				'PICIAct' 		    => 1,
				'ALAct'	  		    => 1,
				'PLAct'	  		    => 1
			);	
			update_option('prosperSuite', $prosperSuiteOpts);
		}
		elseif(!$prosperSuiteOpts['prosperNewVersion'])
		{
			$prosperSuiteOpts = array_merge($prosperSuiteOpts, array(
				'anonymousData' => 1,
				'prosperNewVersion' => 1,
			    'prosperNoOptions' => 1,
		        'PSAct'	  		   => 1,
		        'PAAct'	  		   => 1,
		        'PICIAct' 		   => 1,
		        'ALAct'	  		   => 1,
		        'PLAct'	  		   => 1
			));
			update_option('prosperSuite', $prosperSuiteOpts);
		}
		elseif (!$prosperSuiteOpts['prosperNoOptions'])
		{
		    $prosperSuiteOpts = array_merge($prosperSuiteOpts, array(
		        'prosperNoOptions' => 1,
		        'PSAct'	  		   => 1,
		        'PAAct'	  		   => 1,
		        'PICIAct' 		   => 1,
		        'ALAct'	  		   => 1,
		        'PLAct'	  		   => 1
		    ));
		
		    update_option('prosperSuite', $prosperSuiteOpts);
		}

		if (!is_array($productOptions = get_option('prosper_productSearch' )))
		{		
			$productOptions = array(
				'Product_Endpoint' 	  => 1,
				'Country'		  	  => 'US',
				'Pagination_Limit'    => 10,
				'Same_Limit_Merchant' => 4,
				'Similar_Limit'		  => 0,
				'Same_Limit'		  => 0,
				'Enable_Facets'       => 1,
				'Merchant_Facets'     => 10,
				'Brand_Facets' 		  => 10,
				'Starting_Query' 	  => 'shoes',
				'prodLabel'			  => 'Products',
				'Product_View'		  => 'list',
				'MCoupon_Limit'		  => 4
			);
			update_option( 'prosper_productSearch', $productOptions );
		}

		if (!is_array($PIopt = get_option('prosper_autoComparer')))
		{
			$PIopt = array(
				'Link_to_Merc' => 1,
				'PI_Limit'	   => 1
			);				
			update_option( 'prosper_autoComparer', $PIopt );
		}

		if (!is_array($ALopt = get_option('prosper_autoLinker')))
		{
			$ALopt = array(
				'Auto_Link_Comments' => 0
			);			
			update_option( 'prosper_autoLinker', $ALopt );
		}

		if (!is_array($PLopt = get_option('prosper_prosperLinks')))
		{
			$PLopt = array(
				'PL_LinkOpt' => 1,
				'PL_LinkAff' => 1
			);			
			update_option( 'prosper_prosperLinks', $PLopt );
		}
		
		if (!is_array($advOpts = get_option('prosper_advanced')))
		{
			$advOpts = array(
				'Title_Structure' => 0,
				'Base_URL'		  => 'products',
				'Image_Masking'	  => 0,
				'URL_Masking'	  => 0,
				'MemcacheIP'	  => '127.0.0.1',
				'MemcachePort'    => '11211'
			);			
			update_option( 'prosper_advanced', $advOpts );
		}
		elseif (!$advOpts['MemcacheIP'] || !$advOpts['MemcachePort'])
		{
			$advOpts = array_merge($advOpts, array(
				'Image_Masking'	=> 0,
				'URL_Masking'	=> 0,
				'MemcacheIP'	=> '127.0.0.1',
				'MemcachePort'	=> '11211'
			));
			update_option( 'prosper_advanced', $advOpts );
		}
		
		if (!is_array(get_option('prosper_themes')))
		{
			$PTopt = array(
				'Set_Theme' => 'Default'
			);			
			update_option( 'prosper_themes', $PTopt );
		}
	}
	
	public function prosperQueryTag()
	{
		$GLOBALS['wp']->add_query_var( 'prosperPage' );
		$GLOBALS['wp']->add_query_var( 'keyword' );
		$GLOBALS['wp']->add_query_var( 'cid' );
		$GLOBALS['wp']->add_query_var( 'storeUrl' );
		$GLOBALS['wp']->add_query_var( 'queryParams' );
		$GLOBALS['wp']->add_query_var( 'prosperImg' );
	}
}
