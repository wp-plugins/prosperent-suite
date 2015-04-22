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
				'Target' => 1,
				'anonymousData' => 1,
				'prosperNewVersion' => 1
			);	
			update_option('prosperSuite', $prosperSuiteOpts);
		}
		elseif(!$prosperSuiteOpts['prosperNewVersion'])
		{
			$prosperSuiteOpts = array_merge($prosperSuiteOpts, array(
				'anonymousData' => 1,
				'prosperNewVersion' => 1
			));
			update_option('prosperSuite', $prosperSuiteOpts);
		}

		if (!is_array($productOptions = get_option('prosper_productSearch' )))
		{		
			$productOptions = array(
				'Enable_PPS'       	 => 1,
				'Product_Endpoint' 	 => 1,
				'Country'		  	 => 'US',
				'Coupon_Endpoint'    => 1,
				'Celebrity_Endpoint' => 0,
				'Local_Endpoint'     => 1,
				'Geo_Locate' 		 => 1,
				'Travel_Endpoint'    => 0,
				'Api_Limit' 		 => 50,
				'Pagination_Limit'   => 10,
				'Same_Limit'		 => 8,
				'Enable_Facets'      => 1,
				'Merchant_Facets'    => 10,
				'Brand_Facets' 		 => 10,
				'Starting_Query' 	 => 'shoes',
				'prodLabel'			 => 'Products',
				'coupLabel'			 => 'Coupons',
				'celeLabel'			 => 'Celebrity Products',
				'localLabel'	     => 'Local Deals',
				'Product_View'		 => 'list'
			);
			update_option( 'prosper_productSearch', $productOptions );
		}
		elseif (!$productOptions['Product_View'])
		{
			$productOptions = array_merge($productOptions, array(
				'prodLabel'	   => 'Products',
				'coupLabel'	   => 'Coupons',
				'celeLabel'	   => 'Celebrity Products',
				'localLabel'   => 'Local Deals',
				'Product_View' => 'grid'
			));
			update_option( 'prosper_productSearch', $productOptions );
		}

		if (!is_array(get_option('prosper_autoComparer')))
		{
			$PIopt = array(
				'Enable_AC'    => 1,
				'Link_to_Merc' => 1,
				'PI_Limit'	   => 1
			);				
			update_option( 'prosper_autoComparer', $PIopt );
		}

		if (!is_array(get_option('prosper_autoLinker')))
		{
			$ALopt = array(
				'Enable_AL' 		 => 1,
				'Auto_Link_Comments' => 0
			);			
			update_option( 'prosper_autoLinker', $ALopt );
		}

		if (!is_array(get_option('prosper_prosperLinks')))
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


			$PAopt = get_option('prosper_performAds');
			$prosperSuiteOpts = array_merge($prosperSuiteOpts, array(
				'PSAct'	  => ($productOptions['Enable_PPS'] == 1 ? 1 : 0),
				'PAAct'	  => ($PAopt['Enable_PA'] == 1 ? 1 : 0),
				'PICIAct' => ($PIopt['Enable_AC'] == 1 ? 1 : 0),
				'ALAct'	  => ($ALopt['Enable_AL'] == 1 ? 1 : 0),
				'PLAct'	  => (($PLopt['PL_LinkOpt'] == 1 || $PLAct['PL_LinkAff'] == 1) ? 1 : 0)
			));
			update_option('prosperSuite', $prosperSuiteOpts);
	
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
