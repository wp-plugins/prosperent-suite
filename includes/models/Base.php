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
	
	protected $_endPoints;
	
	public $widget;
	
	private $_allEndPoints = array(
		'fetchMerchant'	   => 'http://api.prosperent.com/api/merchant?',
		'fetchProducts'	   => 'http://api.prosperent.com/api/search?',
		'fetchUkProducts'  => 'http://api.prosperent.com/api/uk/search?',
		'fetchCaProducts'  => 'http://api.prosperent.com/api/ca/search?',
		'fetchCoupons'	   => 'http://api.prosperent.com/api/coupon/search?',
		'fetchLocal'	   => 'http://api.prosperent.com/api/local/search?',
		'fetchCelebrities' => 'http://api.prosperent.com/api/celebrity?',
		'fetchTrends'	   => 'http://api.prosperent.com/api/trends?'
	);
	
	private $_privateNetEndPoints = array(
		'fetchMerchant'    => 'http://10.0.0.2/api/merchant?',
		'fetchProducts'    => 'http://10.0.0.2/api/search?',
		'fetchUkProducts'  => 'http://10.0.0.2/api/uk/search?',
		'fetchCaProducts'  => 'http://10.0.0.2/api/ca/search?',
		'fetchCoupons'     => 'http://10.0.0.2/api/coupon/search?',
		'fetchLocal'       => 'http://10.0.0.2/api/local/search?',
		'fetchCelebrities' => 'http://10.0.0.2/api/celebrity?',
		'fetchTrends'      => 'http://10.0.0.2/api/trends?'
	);	
	
	public function init()
	{
		$this->_options = $this->getOptions();
		$this->_version = $this->getVersion();	

		if ($this->_options['Api_Key'] && strlen($this->_options['Api_Key']) == 32)
		{ 				
			$this->_endPoints = $this->getFetchEndpoints();
			add_action('wp_head', array($this, 'prosperHeaderScript'));
			
			if ((home_url() == 'http://shophounds.com' || home_url() == 'https://shophounds.com') && isset($this->_options['prosperSidText']))
			{
				if (preg_match('/(^\$_(SESSION|COOKIE))\[(\'|")(.+?)(\'|")\]/', $this->_options['prosperSidText'], $regs))
				{
					if ($regs[1] == '$_SESSION')
					{
						$cookie = $_SESSION[$regs[4]];
					}
					elseif ($regs[1] == '$_COOKIE')
					{
						$cookie = $_COOKIE[$regs[4]];
					}					
				}
				if (!isset($cookie))
				{
					wp_register_script( 'loginCheck', PROSPER_JS . '/shopCheck.js', array('jquery'), $this->_version);
					wp_enqueue_script( 'loginCheck' );	
				}
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

	public function getFetchEndpoints()
	{
		$this->_endPoints = $this->_allEndPoints;
		if (file_exists(WP_CONTENT_DIR . '/prosperentPrivateNetwork.php'))
		{
			$this->_endPoints = $this->_privateNetEndPoints;
		}
		return $this->_endPoints;
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
			$widgets = array_merge($widgets, array('ProsperStoreWidget', 'TopProductsWidget', 'RecentSearchesWidget'));		
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
				$css = str_replace(' ', '%20', (content_url(preg_replace('/.*\/wp-content/i', '', $newTheme[0]))));
			}
		}

		wp_register_style( 'prospere_main_style', $css, array(), $this->_version );
		wp_enqueue_style( 'prospere_main_style' );
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
			'q'      => '', // query
			'utt'    => 0, // use Title as Topics
			'utg'    => 0, // use Tags as Topics
			'h'      => 90, // height
			'w'      => 'auto',	// width	
			'c'      => 0, // use coupons, deprecated
			'b'      => '', // brand
			'm'      => '', // merchant
			'l'      => 1, // limit
			'cl'     => '', // comparison limit, deprecated
			'ct'     => 'US', // country
			'id'     => '',  // product/catalog id	
			'gtm'    => 0, // go to merchant
			'v'      => 'list', // view
			'w'	     => '', // width
			'ws'     => 'px', // width style (px, em, %)
			'css'    => '', // additional css
			'state'  => '', // state
			'city'   => '', // city 
			'z'	 	 => '', // zipCode
			'ft'  	 => 'fetchProducts', // fetch method
			'sale'   => 0, // on sale products only
			'gimgsz' => 200	 // grid image size		
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
		if (get_bloginfo('version') >= 3.9)
		{
			$plugin_array['prosperent'] = PROSPER_JS . '/prosperent3.9.min.js?ver=' . $this->_version;
		}
		else
		{
			$plugin_array['prosperent'] = PROSPER_JS . '/prosperent.min.js?ver=' . $this->_version;
		}
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
		$sidArray = array();
		if ($this->_options['prosperSid'])
		{
			foreach ($this->_options['prosperSid'] as $sidPiece)
			{
				switch ($sidPiece)
				{
					case 'blogname':
						$sidArray[] = get_bloginfo('name');
						break;
					case 'interface':
						$sidArray[] = $settings['interface'] ? $settings['interface'] : 'api';
						break;
					case 'query':
						$sidArray[] = $settings['query'];
						break;
					case 'page':
						$sidArray[] = get_the_title();
						break;						
				}
			}
		}
		if ($this->_options['prosperSidText'])
		{
			if (preg_match('/(^\$_(SERVER|SESSION|COOKIE))\[(\'|")(.+?)(\'|")\]/', $this->_options['prosperSidText'], $regs))
			{
				if ($regs[1] == '$_SERVER')
				{
					$sidArray[] = $_SERVER[$regs[4]];
				}
				elseif ($regs[1] == '$_SESSION')
				{
					$sidArray[] = $_SESSION[$regs[4]];
				}
				elseif ($regs[1] == '$_COOKIE')
				{
					$sidArray[] = $_COOKIE[$regs[4]];
				}					
			}
			elseif (!preg_match('/\$/', $this->_options['prosperSidText']))
			{
				$sidArray[] = $this->_options['prosperSidText'];
			}
		}
		
		if (!empty($sidArray))
		{
			$sidArray = array_filter($sidArray);
			$sid = implode('_', $sidArray);
		}
	
		echo '<script type="text/javascript">var _prosperent={"campaign_id":"' . $this->_options['Api_Key'] . '", "pl_active":' . (wp_script_is('loginCheck') ? 0 : 1) . ', "pl_sid":"' . $sid . '", "pa_active":' . ($this->_options['Enable_PA'] ? 1 : 0) . ', "pl_phraselinker_active":0, "pl_linkoptimizer_active":' . ($this->_options['PL_LinkOpt'] ? 1 : 0) . ', "pl_linkaffiliator_active":' . ($this->_options['PL_LinkAff'] ? 1 : 0) . ', "platform":"wordpress"};</script><script async type="text/javascript" src="//prosperent.com/js/prosperent.js"></script>';
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
	
	
	public function apiCall ($settings, $fetch, $sid = '')
	{				
		if (empty($this->_endPoints))
		{
			$this->_endPoints = $this->getFetchEndpoints();
		}
	
		if (empty($this->_options))
		{
			$options = $this->getOptions();
		}
		else
		{
			$options = $this->_options;
		}		
		
		$sidArray = array();
		if ($options['prosperSid'] && !$sid)
		{
			foreach ($options['prosperSid'] as $sidPiece)
			{
				switch ($sidPiece)
				{
					case 'blogname':
						$sidArray[] = get_bloginfo('name');
						break;
					case 'interface':
						$sidArray[] = $settings['interface'] ? $settings['interface'] : 'api';
						break;
					case 'query':
						$sidArray[] = $settings['query'];
						break;
					case 'page':
						$sidArray[] = get_the_title();
						break;						
				}
			}
		}
		if ($options['prosperSidText'] && !$sid)
		{
			if (preg_match('/(^\$_(SERVER|SESSION|COOKIE))\[(\'|")(.+?)(\'|")\]/', $options['prosperSidText'], $regs))
			{
				if ($regs[1] == '$_SERVER')
				{
					$sidArray[] = $_SERVER[$regs[4]];
				}
				elseif ($regs[1] == '$_SESSION')
				{
					$sidArray[] = $_SESSION[$regs[4]];
				}
				elseif ($regs[1] == '$_COOKIE')
				{
					$sidArray[] = $_COOKIE[$regs[4]];
				}					
			}
			elseif (!preg_match('/\$/', $options['prosperSidText']))
			{
				$sidArray[] = $options['prosperSidText'];
			}
		}
		
		if (!empty($sidArray))
		{
			$sidArray = array_filter($sidArray);
			$sid = implode('_', $sidArray);
		}
		
		if ($sid)
		{
			$settings['sid'] = $sid;
		}
		
		if ($options['relThresh'])
		{
			$settings['relevancyThreshold'] =  $options['relThresh'];
		}

		$settings = array_merge($settings, array(
			'api_key' => $options['Api_Key'],
			'location' => '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'referrer' => $_SERVER['HTTP_REFERER']
		));	

		// Set the URL
		$url = $this->_endPoints[$fetch] . http_build_query ($settings);

		return $url;
	}
	
	public function multiCurlCall ($urls = array())
	{	
		$curlCount = count($urls);
		if ($curlCount < 1)
		{
			return array();
		}

		// array of curl handles
		$curly = array();
		// data to be returned
		$result = array();

		// multi handle
		$mh = curl_multi_init();

		// loop through $data and create curl handles
		// then add them to the multi-handle
		foreach ($urls as $id => $url) 
		{
			$curly[$id] = curl_init();

			curl_setopt_array($curly[$id], array(CURLOPT_URL => $url,
				CURLOPT_HEADER 		   => 0,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_TIMEOUT 	   => 30,
				CURLOPT_CONNECTTIMEOUT => 30
				)
			);

			curl_multi_add_handle($mh, $curly[$id]);
		}

		// execute the handles
		$running = null;
		do 
		{
			curl_multi_exec($mh, $running);
		} while($running > 0);


		// get content and remove handles
		foreach($curly as $id => $c) 
		{
			$result[$id] = json_decode(curl_multi_getcontent($c), true);
			curl_multi_remove_handle($mh, $c);
		}

		// all done
		curl_multi_close($mh);

		return $result;
	}
	
	
	public function singleCurlCall ($url = '')
	{	
		$curl = curl_init();

		// Set options
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_TIMEOUT => 30
		));

		// Send the request
		$response = curl_exec($curl);

		// Close request
		curl_close($curl);

		// Convert the json response to an array
		$response = json_decode($response, true);

		// Check for errors
		if (count($response['errors']))
		{
			throw new Exception(implode('; ', $response['errors']));
		}

		/*if ($options['Enable_Caching'] && file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) >= 0755)
		{
			$settings = array_merge($settings, $this->apiCaching($lifetime));	
		}*/		
		
		return $response;
		//return array('results' => $response['data'], 'totalAvailable' => $response['totalRecordsAvailable'], 'total' => $response['totalRecordsFound'], 'facets' => $response['facets']);
	}	
	
	public function trendsApiCall ($settings, $fetch, $categories = '', $merchants = '', $brands = '', $sid = '')
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
			$brandFilter = true;
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
		$prevNumDays = 60;
		$startDate   = date('Ymd', time() - 86400 * $prevNumDays);
		$endDate     = date('Ymd');		
		
		$apiCall = array(
			'api_key' 	     	   => $this->_options['Api_Key'],
			'enableFacets'   	   => array('catalogId'),
			'filterCommissionDate' => $startDate . ',' . $endDate,
			'filterCatalog'  	   => $catalog,
			'filterCategory' 	   => $categories,			
			'filterMerchant'	   => $merchants,
			'filterBrand'		   => $brandFilter ? $brands : ''
		);

		$apiCall = array_filter($apiCall);

		// Set the URL
		$response = $this->trendsCurlCall($apiCall);

		if ($response)
		{				
			// set productId as key in array
			$keys = array();
			foreach ($response['facets']['catalogId'] as $i => $data)
			{
				if ($i < 50)
				{
					$keys[] = $data['value'];
				}
				else
				{
					break;
				}
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

			// fetch trend data from api
			$settings = array_merge(array(
				$filter		   => $keys,
				'limit'		   => $options['Pagination_Limit'],
				'sid'		   => $sid
			), $settings);

			$trendsUrl = $this->apiCall($settings, $fetch, $lifetime, $sid);
			$results = $this->singleCurlCall($trendsUrl);
		}
		
		return (array) $results;
	}
	
	public function trendsCurlCall($settings)
	{
		$url = $this->_endPoints['fetchTrends'] . http_build_query ($settings);

		$curl = curl_init();

		// Set options
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_TIMEOUT => 30
		));

		// Send the request
		$response = curl_exec($curl);

		// Close request
		curl_close($curl);

		// Convert the json response to an array
		$response = json_decode($response, true);

		// Check for errors
		if (count($response['errors']) || empty($response['facets']['catalogId']))
		{
			$count = count($settings);
			for ($i = 0; $i <= $count; $i++)
			{
				array_pop($settings);

				if(count($settings) < 5)
				{
					return ;
				}
			
				$response = $this->trendsCurlCall($settings);

				if ($response['facets']['catalogId'])
				{
					break;
				}	 
			}
		}

		return $response;
	}
}
