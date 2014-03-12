<?php
/**
 * Base Abstract Model
 *
 * @package Model
 */
abstract class Model_Base
{
	protected $_options;
	
	protected $_version;
	
	public $widget;
	
	public function init()
	{
		$this->_options = $this->getOptions();
		$this->_version = $this->getVersion();
	
		if ($this->_options['Api_Key'] && strlen($this->_options['Api_Key']) == 32)
		{ 		
			add_action('wp_head', array($this, 'prosperHeaderScript'));
			
			if (isset($this->_options['Enable_Caching']) &&  (!file_exists(PROSPER_CACHE) || substr(decoct( fileperms(PROSPER_CACHE) ), 1) != '0777'))
			{
				add_action( 'admin_notices', array($this, 'prosperNoticeWrite' ));
			}
			
			require_once(PROSPER_PATH . 'ProsperentApi.php');

			if (isset($this->_options['Enable_PA']))
			{								
				require_once(PROSPER_INCLUDE . '/ProsperAdController.php');				
			}
			
			if (isset($this->_options['Enable_AC']))
			{
				require_once(PROSPER_INCLUDE . '/ProsperInsertController.php');
			}
			
			if (isset($this->_options['Enable_AL']))
			{
				require_once(PROSPER_INCLUDE . '/ProsperLinkerController.php');				
			}
			
			if (isset($this->_options['Enable_PPS']))
			{					
				require_once(PROSPER_INCLUDE . '/ProsperSearchController.php');
				if ($this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products')
				{
					add_action('wp_enqueue_scripts', array($this, 'productStoreJs'));	
				}
			}
			else
			{				
				add_action('admin_init', array($this, 'prosperStoreRemove'));
			}
			
			if (isset($this->_options['Enable_PPS']) || isset($this->_options['Enable_AC']))
			{
				add_action('wp_enqueue_scripts', array($this, 'prosperStylesheets'));	
			}
		}
		else
		{
			add_action( 'admin_notices', array($this, 'prosperBadSettings' ));
		}	
	}
	
	public function getVersion()
	{
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			
		$pluginFolder = get_plugins('/' . PROSPER_FOLDER);				
		return $pluginFolder[PROSPER_FILE]['Version'];
	}

	/**
	 * Retrieve all the options
	 *
	 * @return array of options
	 */
	public function getOptions($option = null)
	{
		if (!isset($this->_options))
		{
			$this->_options = array();
			foreach ($this->getProsperOptionsArray() as $opt)
			{ 
				$this->_options = array_merge($this->_options, (array) get_option($opt));
			}
		}

		return $this->_options;
	}
	
	public function setWidget($widget)
	{
		$this->widget = (string) $widget;
        return $this;
	}
	
	public function getWidget()
	{
		return $this->widget;
	}
	
	public function createWidget()
	{
		$pa = get_option('prosper_performAds');
		$ps = get_option('prosper_productSearch');
		
		$widgets = array();
		if (isset($pa['Enable_PA']))
		{
			$widgets[] = 'PerformAdWidget';		
		}
		if (isset($ps['Enable_PPS']))
		{
			$widgets = array_merge($widgets, array('ProsperStoreWidget', 'TopProductsWidget'));		
		}
		
		foreach ($widgets as $widget)
		{
			require_once(PROSPER_WIDGET . '/' . $widget . '.php');
			register_widget($widget);
		}		
	}
		
	public function prosperStylesheets()
	{
		$css = PROSPER_CSS . '/products.min.css';
		
		// Product Search CSS for results and search
		if ($this->_options['Set_Theme'] != 'Default')
		{
			$dir = PROSPER_THEME . '/' . $this->_options['Set_Theme'];
			if($newTheme = glob($dir . "/*.css"))
			{			
				$css = $newTheme[0];
			}
		}
	
		wp_register_style( 'prospere_main_style', $css, array(), $this->_version );
		wp_enqueue_style( 'prospere_main_style' );
	}	
	
	public function productStoreJs()
	{
		// Product Search CSS for results and search
		wp_register_script( 'productStoreJS', PROSPER_JS . '/productStore.js', array(), $this->_version );
		wp_enqueue_script( 'productStoreJS' );
	}	
	
	public function prosperBadSettings()
	{			
		$url = admin_url( 'admin.php?page=prosper_general' );
		echo '<div class="error" style="padding:6px 0;">';
		echo _e( '<span style="font-size:14px; padding-left:10px;">Your <strong>API Key</strong> is either incorrect or missing. </span></br>', 'my-text-domain' );
		echo _e('<span style="font-size:14px; padding-left:10px;">Please enter your <strong>Prosperent API Key</strong>.</span></br>', 'my-text-domain' ); 
		echo _e('<span style="font-size:14px; padding-left:10px;">Go to the Prosperent Suite <a href="' . $url . '">General Settings</a> and follow the directions to get your API Key.</span>', 'my-text-domain' );
		echo '</div>';		
	}
	
	public function prosperNoticeWrite() 
	{
		echo '<div class="error" style="padding:6px 0;">';
		echo _e( '<span style="font-size:14px; padding-left:10px;">Please create the <strong>prosperent_cache</strong> directory inside your <strong>wp_content</strong> directory and make it writable (0777). If you need assistance, <a href="http://codex.wordpress.org/Changing_File_Permissions">Changing File Permissions</a></span><span style="font-size:12px;"></span>', 'my-text-domain' );
		echo '</div>';	
	}
	
	/**
	 * Retrieve an array of all the options the plugin uses. It can't use only one due to limitations of the options API.
	 *
	 * @return array of options.
	 */
	public function getProsperOptionsArray()
	{
		$optarr = array('prosperSuite', 'prosper_productSearch', 'prosper_performAds', 'prosper_autoComparer', 'prosper_autoLinker', 'prosper_prosperLinks', 'prosper_advanced', 'prosper_themes');
        return apply_filters( 'prosper_options', $optarr );
	}
	
	public function shortCodeExtract($atts, $shortcode)
	{	
		return shortcode_atts(array(
			'q'   => '',
			'utt' => false,
			'utg' => false,
			'h'   => 90,
			'w'   => 'auto',			
			'c'   => 0,
			'b'   => '',
			'm'   => '',
			'l'   => 1,
			'cl'  => '',
			'ct'  => 'US',
			'id'  => '',			
			'gtm' => false,
			'v'   => 'list',
			'w'	  => 210,
			'ws'  => 'px',
			'css' => ''
		), $atts, $shortcode);
	}
	
	/**
	 * Flush the rewrite rules.
	 */
	public function prosperFlushRules()
	{
		flush_rewrite_rules();
	}	
	
	public function prosperCustomAdd()
	{
		// Add only in Rich Editor mode
		if (get_user_option('rich_editing') == 'true')
		{
			add_filter('mce_external_plugins', array($this, 'prosperTinyRegister'));
			add_filter('mce_buttons', array($this, 'prosperTinyAdd'));
		}
	}
	
	public function prosperTinyRegister($plugin_array)
	{		
		$plugin_array['prosperent'] = PROSPER_JS . '/prosperent.min.js?q=ver=1' . $this->_version;
		return $plugin_array;
	}	
	
	public function prosperTinyAdd($buttons)
	{
		array_push( $buttons, '|', 'prosperent');
		return $buttons;
	}
	
	public function qTagsProsper($id, $display, $arg1, $arg2)
	{
		?>
		<script type="text/javascript">
			QTags.addButton(<?php echo "'" . $id . "', '" . $display . "', '" . $arg1 . "', '" . $arg2 . "'"; ?>);
		</script>
		<?php
	}
	
	public function doOutputBuffer()
	{
		ob_start();
	}
	
	public function prosperHeaderScript()
	{				
		echo '<script type="text/javascript">var _prosperent={"campaign_id":"' . $this->_options['Api_Key'] . '", "pl_active":1, "pa_active":' . ($this->_options['Enable_PA'] ? 1 : 0) . ', "pl_phraselinker_active":0, "pl_linkoptimizer_active":' . ($this->_options['PL_LinkOpt'] ? 1 : 0) . ', "pl_linkaffiliator_active":' . ($this->_options['PL_LinkAff'] ? 1 : 0) . ', "platform":"wordpress"};</script><script async type="text/javascript" src="http://prosperent.com/js/prosperent.js"></script>';
	}
	
	public function prosperStoreRemove()
	{
		$pageTitle = get_option("prosperent_store_page_title");
		$pageName = get_option("prosperent_store_page_name");

		// the id of our page...
		$pageId = get_option('prosperent_store_page_id');
		if($pageId)
		{
			wp_delete_post($pageId); // this will trash, not delete
		}

		delete_option("prosperent_store_page_title");
		delete_option("prosperent_store_page_name");
		delete_option("prosperent_store_page_id");
	}
	
	public function apiCall ($settings, $fetch, $lifetime = '84600')
	{	
		if (empty($this->_options))
		{
			$options = $this->getOptions();
		}
		else
		{
			$options = $this->_options;
		}	
	
		$settings = array_merge($settings, array(
			'api_key' 	   => $options['Api_Key'],
			'visitor_ip'   => $_SERVER['REMOTE_ADDR']	
		));	

		if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
		{
			$settings = array_merge($settings, $this->apiCaching($lifetime));	
		}
		
		$prosperentApi = new Prosperent_Api($settings);

		$prosperentApi->$fetch();

		$results 	= $prosperentApi -> getAllData();
		$totalFound = $prosperentApi -> getTotalRecordsFound();
		$facets 	= $prosperentApi -> getFacets();
		
		return array('results' => $results, 'total' => $totalFound, 'facets' => $facets);
	}
	
	public function trendsApiCall ($settings, $fetch, $lifetime = '3600')
	{
		if (empty($this->_options))
		{
			$options = $this->getOptions();
		}
		else
		{
			$options = $this->_options;
		}	

		if ($fetch === 'fetchCoupons')
		{
			$filter  = 'filterCouponId';
			$catalog = 'coupons';
		}
		elseif ($fetch === 'fetchLocal')
		{
			$filter = 'filterLocalId';
			$catalog = 'local';
		}
		else
		{
			$filter = 'filterCatalogId';
			if ($this->_options['Country'] === 'US')
			{
				$catalog = 'US';
			}
			elseif ($this->_options['Country'] === 'CA')
			{
				$catalog = 'CA';
			}
			else 
			{
				$catalog = 'UK';
			}
		}
		
		// calculate date range
		$prevNumDays = 30;
		$startDate   = date('Ymd', time() - 86400 * $prevNumDays);
		$endDate     = date('Ymd');

		$apiCall = array(
			'api_key' 	   => $this->_options['Api_Key'],
			'visitor_ip'   => $_SERVER['REMOTE_ADDR'],	
			'limit'		   => 50,
			'enableFacets' => array('catalogId'),
			'filterCatalog' => $catalog
		);

		$api = new Prosperent_Api($apiCall);
	
		$api->setDateRange('commission', $startDate, $endDate)
			->fetchTrends();
		
		// set productId as key in array
		$keys = array();
		foreach ($api->getFacets('catalogId') as $data)
		{
			$keys[] = $data['value'];
		}

		if ($fetch === 'fetchCoupons')
		{
			$filter = 'filterCouponId';
		}
		elseif ($fetch === 'fetchLocal')
		{
			$filter = 'filterLocalId';
		}
		else
		{
			$filter = 'filterCatalogId';
		}

		// fetch merchant data from api
		$settings = array_merge(array(
			$filter		   => $keys,
			'limit' 	   => 15,
			'enableFacets' => FALSE
		), $settings);

		$results = $this->apiCall($settings, $fetch, $lifetime);

		return $results;
	}
	
	public function apiCaching($lifetime)
	{
		$cache = array(
			'cacheBackend'  => 'FILE',
			'cacheOptions'  => array(
				'cache_dir' => PROSPER_CACHE,
				'lifetime'	=> $lifetime
			)
		);	
		
		return $cache;
	}
}
