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
	
	protected $_pages = array(
		'products' => '[prosper_store][/prosper_store]'
	);
	
	public function prosperActivate()
	{
		$this->_options = $this->getOptions();
		
		$this->prosperDefaultOptions();		
		if ($this->_options['Enable_PPS'])
		{
			$this->prosperStoreInstall();
			$this->prosperReroutes();
		}
		$this->prosperOptionActivateAdd();
	}
	
	public function prosperDeactivate()
	{		
		$this->prosperStoreRemove();
		$this->prosperFlushRules();
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
		if (!is_array(get_option('prosperSuite')))
		{
			$opt = array(
				'Target' => 1
			);	
			update_option('prosperSuite', $opt);
		}

		$productOptions = get_option('prosper_productSearch' );
		if (!is_array(get_option('prosper_productSearch' )))
		{			
			$opt = array(
				'Enable_PPS'       	 => 1,
				'Product_Endpoint' 	 => 1,
				'Country_Code'  	 => 'US',
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
				'Product_View'		 => 'grid'
			);
			update_option( 'prosper_productSearch', $opt );
		}
		elseif (!$productOptions['Product_View'])
		{
			$opt = array_merge($productOptions, array(
				'prodLabel'	   => 'Products',
				'coupLabel'	   => 'Coupons',
				'celeLabel'	   => 'Celebrity Products',
				'localLabel'   => 'Local Deals',
				'Product_View' => 'grid'
			));
			update_option( 'prosper_productSearch', $opt );
		}

		if (!is_array(get_option('prosper_performAds')))
		{
			$opt = array(
				'Enable_PA'   => 1,
				'Remove_Tags' => ''
			);		
			update_option( 'prosper_performAds', $opt );
		}

		if (!is_array(get_option('prosper_autoComparer')))
		{
			$opt = array(
				'Enable_AC'    => 1,
				'Link_to_Merc' => 1,
				'PI_Limit'	   => 1
			);				
			update_option( 'prosper_autoComparer', $opt );
		}

		if (!is_array(get_option('prosper_autoLinker')))
		{
			$opt = array(
				'Enable_AL' 		 => 1,
				'Auto_Link_Comments' => 0
			);			
			update_option( 'prosper_autoLinker', $opt );
		}

		if (!is_array(get_option('prosper_prosperLinks')))
		{
			$opt = array(
				'PL_LinkOpt' => 1,
				'PL_LinkAff' => 1
			);			
			update_option( 'prosper_prosperLinks', $opt );
		}
		
		if (!is_array(get_option('prosper_advanced')))
		{
			$opt = array(
				'Title_Structure' => 0,
				'Image_Masking'	  => 0,
				'URL_Masking'	  => 0,
				'Base_URL'		  => 'products'
			);			
			update_option( 'prosper_advanced', $opt );
		}
		
		if (!is_array(get_option('prosper_themes')))
		{
			$opt = array(
				'Set_Theme' => 'Default'
			);			
			update_option( 'prosper_advanced', $opt );
		}
	}
	
	public function prosperReroutes()
	{
		$this->prosperRewrite();
		$this->prosperFlushRules();
	}
	
	/**
	 * Flush the rewrite rules.
	 */
	public function prosperFlushRules()
	{
		flush_rewrite_rules();
	}	
	
	public function prosperStoreInstall()
	{
		foreach ($this->_pages as $i => $pages)
		{
			$pageTitle = $i;
			$pageName = 'Prosperent Search';

			// the menu entry...
			delete_option("prosperentStore" . ucfirst($pageTitle) . "Title");
			add_option("prosperentStore" . ucfirst($pageTitle) . "Title", $pageTitle, '', 'yes');
			// the slug...
			delete_option("prosperentStore" . ucfirst($pageName) . "Name");
			add_option("prosperentStore" . ucfirst($pageName) . "Name", $pageName, '', 'yes');
			// the id...
			delete_option("prosperent_store_pageId");
			add_option("prosperent_store_" . ucfirst($pageTitle) . "Id", '0', '', 'yes');

			$page = get_page_by_title($pageTitle);

			if (!$page)
			{
				// Create post object
				$proserStore = array(
					'post_title'     => $pageTitle,
					'post_content'   => $pages,
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'comment_status' => 'closed',
					'ping_status'    => 'closed'
				);

				// Insert the post into the database
				$pageId = wp_insert_post($proserStore);
			}
			else
			{
				// the plugin may have been previously active and the page may just be trashed...
				$pageId = $page->ID;

				//make sure the page is not trashed...
				$page->post_status = 'publish';
				$pageId = wp_update_post($page);
			}

			delete_option('prosperent_store_pageId');
			add_option('prosperent_store_pageId', $pageId);
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

	public function prosperRewrite()
	{
		$page     = $this->_options['Base_URL'] ? ($this->_options['Base_URL'] == 'null' ? '' : $this->_options['Base_URL'] . '/') : 'products/';
		$pageName = $this->_options['Base_URL'] ? ($this->_options['Base_URL'] == 'null' ? '' : 'pagename=' . $this->_options['Base_URL']) : 'pagename=products';
		
		add_rewrite_rule('^([^/]+)/([^/]+)/cid/([a-z0-9A-Z]{32})/?$', 'index.php?' . $pageName . '&prosperPage=$matches[1]&keyword=$matches[2]&cid=$matches[3]', 'top');
		add_rewrite_rule('store/go/([^/]+)/?', 'index.php?' . $pageName . '&store&go&storeUrl=$matches[1]', 'top');
		add_rewrite_rule('img/([^/]+)/?', 'index.php?' . $pageName . '&prosperImg=$matches[1]', 'top');
		add_rewrite_rule($page . '(.+)', 'index.php?' . $pageName . '&queryParams=$matches[1]', 'top');
		
		
		/*add_rewrite_rule('travel/([^/]+)/cid/([^/]+)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
		add_rewrite_rule('coupon/([^/]+)/cid/([^/]+)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
		add_rewrite_rule('product/([^/]+)/cid/([^/]+)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
		add_rewrite_rule('celebrity/([^/]+)/cid/([^/]+)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');*/
	}
}
