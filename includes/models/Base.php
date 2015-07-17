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
		'fetchTrends'	   => 'http://api.prosperent.com/api/trends?',
		'fetchAnalyzer'	   => 'http://api.prosperent.com/api/content/analyzer?',
	    'fetchClicks'	   => 'http://api.prosperent.com/api/clicks?',
	    'fetchCommissions' => 'http://api.prosperent.com/api/commissions?'
	);
	
	private $_privateNetEndPoints = array(
		'fetchMerchant'    => 'http://192.168.1.104/api/merchant?',
		'fetchProducts'    => 'http://192.168.1.104/api/search?',
		'fetchTrends'      => 'http://192.168.1.104/api/trends?',
		'fetchAnalyzer'	   => 'http://192.168.1.104/api/content/analyzer?',
	    'fetchClicks'	   => 'http://192.168.1.104/api/clicks?',
	    'fetchCommissions' => 'http://192.168.1.104/api/commissions?'
	);	

	public function init()
	{	    
		if (extension_loaded('curl'))
		{
			$this->_options = $this->getOptions();
			$this->_version = $this->getVersion();	

			if ($this->_options['Api_Key'] && strlen($this->_options['Api_Key']) == 32)
			{ 				
				$this->_endPoints = $this->getFetchEndpoints();			

				if ($this->_options['PLAct'])
				{
					add_action('wp_head', array($this, 'prosperHeaderScript'));
				}
				
				if ((home_url() == 'http://shophounds.com' || home_url() == 'https://shophounds.com') && isset($this->_options['prosperSidText']))
				{
					$this->shopHounds();
				}						
				
				require_once(PROSPER_INCLUDE . '/ProsperInsertController.php');

				require_once(PROSPER_INCLUDE . '/ProsperLinkerController.php');				
		
				if (get_option('permalink_structure'))
				{	
					require_once(PROSPER_INCLUDE . '/ProsperSearchController.php');
					
					if ($this->_options['PSAct'])
					{
					    $this->prosperStoreInstall();
					}
					else
					{
					    add_action('admin_init', array($this, 'prosperStoreRemove'));
					}
				}
				else
				{
					add_action( 'admin_notices', array($this, 'prosperPermalinkStructure' ));
				}			
				
				add_action('wp_enqueue_scripts', array($this, 'prosperStylesheets'));	
				
				$advancedOpts = get_option('prosper_advanced');
				$generalOpts = get_option('prosperSuite');
				if (($generalOpts['prosperSid'] && !$advancedOpts['prosperSid']) || ($generalOpts['prosperSidText'] && !$advancedOpts['prosperSidText'])) 
				{
					$advancedOpts['prosperSid'] = $generalOpts['prosperSid'];
					$advancedOpts['prosperSidText'] = $generalOpts['prosperSidText'];
					update_option('prosper_advanced', $advancedOpts);
				}
				
				if ($this->_options['autoMinorUpdates'])
				{
					add_filter( 'auto_update_plugin', array( $this, 'autoUpdateProsperMinor' ), 1000, 2 );
				}
			}
			else
			{
				add_action( 'admin_notices', array($this, 'prosperBadSettings' ));
			}	
			
			add_action( 'wp_enqueue_scripts', array($this, 'prefixEnqueueFAwesome' ));
		}
		else
		{
			add_action( 'admin_notices', array($this, 'prosperNoCurlLoaded' ));
		}	

		add_shortcode('perform_ad', array($this, 'performAdShortCode'));
    }
	
    public function prefixEnqueueFAwesome() 
    {
        wp_enqueue_style( 'prefix-font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css', array(), '4.0.3' );
    }
    
    /**
     * Performs shortcode extraction for ProsperAds
     *
     * @param array  $atts    Attributes from the shortcode
     * @param string $content Content on the page/post
     */
    public function performAdShortCode($atts, $content = null)
    {
        return;
    }

	public function autoUpdateProsperMinor ( $update, $item )
	{
		if ( !is_object( $item ) || !isset( $item->new_version ) || !isset( $item->plugin ) )  
		{
			return $update;
		}		

		$currentParts = explode( '.', $this->_version );
		$updateParts = explode( '.', $item->new_version );

		// Only return true and update when the update is a minor version
		return ( ($updateParts[0] === $currentParts[0] && $updateParts[1] === $currentParts[1]) );
	}
	
	public function getVersion()
	{			
		if ( ! function_exists( 'get_plugin_data' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
		$pluginInfo = get_plugin_data(PROSPER_PATH . PROSPER_FILE);				
		return $pluginInfo['Version'];
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
	
	public function shopHounds()
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
	
		//wp_register_script('Beta', '', array('jquery', 'json2', 'jquery-ui-widget', 'jquery-ui-dialog', 'jquery-ui-tooltip', 'jquery-ui-autocomplete') );
		//wp_enqueue_script( 'Beta' );	
		//wp_enqueue_style('BetaCSS', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css');
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
		$genOpt = get_option('prosperSuite');

		$widgets = array();

		if (isset($genOpt['PSAct']))
		{
			$widgets = array_merge($widgets, array('ProsperStoreWidget', 'TopProductsWidget', 'RecentSearchesWidget'));		
		}
		if (isset($genOpt['PICIAct']))
		{
			$widgets[] = 'ProductInsertWidget';		
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
	
	public function prosperPermalinkStructure()
	{			
		echo '<div class="error" style="padding:6px 0;">';
		echo _e('<span style="font-size:14px; padding-left:10px;">Switch your <a href="' . admin_url( 'options-permalink.php') . '">PermaLinks</a> structure to anything other than Default.</span></br>', 'my-text-domain' );
		echo _e('<span style="font-size:14px; padding-left:10px;">The ProsperShop will not work correctly on the Default structure due to formatting of the  Prosperent Suite.</span></br>', 'my-text-domain' ); 
		echo '</div>';		
	}
	
	public function prosperNoCurlLoaded()
	{			
		echo '<div class="error" style="padding:6px 0;">';
		echo _e('<span style="font-size:14px; padding-left:10px;"><strong>cURL</strong> is not installed on your server.</span></br>', 'my-text-domain' );
		echo _e('<span style="font-size:14px; padding-left:10px;">You need cURL to run the Prosperent Suite.</span></br>', 'my-text-domain' ); 
		echo '</div>';		
	}
	
	public function prosperBadSettings()
	{			
	    
		$url = admin_url( 'admin.php?page=prosper_general' );
		echo '<div class="error" style="padding:6px 0;">';
		echo _e( '<span style="font-size:14px; padding-left:10px;">Your <strong>API Key</strong> is either incorrect or missing. </span></br>', 'my-text-domain' );
		
		echo _e('<span style="font-size:14px; padding-left:10px;">Please enter your <strong>Prosperent API Key</strong> by following the directions ' . ('prosper_general' == $_GET['page'] ? 'below' : 'in <a href="' . $url . '">General Settings</a>') . '.</span></br>', 'my-text-domain' ); 
		echo _e('<span style="font-size:14px; padding-left:10px;">Prosperent Suite will not work without this information.</span>', 'my-text-domain' );
		echo '</div>';		
	}
	
	/**
	 * Retrieve an array of all the options the plugin uses. It can't use only one due to limitations of the options API.
	 *
	 * @return array of options.
	 */
	public function getProsperOptionsArray()
	{
		$optarr = array('prosperSuite', 'prosper_productSearch', 'prosper_autoComparer', 'prosper_autoLinker', 'prosper_prosperLinks', 'prosper_advanced', 'prosper_themes');
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
			'k'		 => '', // keyword
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
			'pr'	 => '', // priceRange
		    'po'	 => '', // percentoff
			'gimgsz' => 200,	 // grid image size
			'sf'     => 'prod', // Search For, changes the endpoint
			'sbar'   => 'Search Products', // Search Bar Text
			'sbu'    => 'Search', // Search Button Text
			'vst'    => 'Visit Store', // Product Insert Visit Store text
			'celeb'  => '', // Celebrity Name,
			'noShow' => '', // Don't show the Product Insert on this page/post
			'imgt'   => '', // ImageType
		    'fb'     => '' // FallBack
		), $atts, $shortcode);
	}
	
	public function prosperReroutes()
	{
		$this->prosperRewrite();
		$this->prosperFlushRules();
	}
	
	public function prosperRewrite()
	{
		$options = get_option('prosper_advanced');

		$page     = $options['Base_URL'] ? $options['Base_URL'] . '/' : 'products/';
		$pageName = $options['Base_URL'] ? 'pagename=' . $options['Base_URL'] : 'pagename=products';

		add_rewrite_rule('^([^/]+)/([^/]+).cid.([a-z0-9A-Z]{32})/?$', 'index.php?' . $pageName . '&prosperPage=$matches[1]&keyword=$matches[2]&cid=$matches[3]', 'top');
		add_rewrite_rule($page . '(.+)', 'index.php?' . $pageName . '&queryParams=$matches[1]', 'top');
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
		/* For Use later if we start creating more pages
		$pages = array(
			'Products' => '[prosper_store][/prosper_store]'
		);
		*/
		
		$pageTitle = 'Products';
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

		$page = get_page_by_path(($this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products'));

		if (!$page)
		{
			// Create post object
			$proserStore = array(
				'post_title'     => $pageTitle,
				'post_content'   => '[prosper_store][/prosper_store]',
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
	
	public function prosperStoreRemove()
	{
	    $pageTitle = get_option("prosperentStoreProductsTitle");
	    $pageName = get_option("prosperentStoreProsperent SearchName");
	
	    // the id of our page...
	    $pageId = get_option('prosperent_store_pageId');
	    if($pageId)
	    {
	        wp_delete_post($pageId); // this will trash, not delete
	    }
	
	    delete_option("prosperent_store_page_title");
	    delete_option("prosperent_store_page_name");
	    delete_option("prosperent_store_pageId");
	    delete_option("prosperent_store_page_id");
	}
	
	
	public function prosperCustomAdd()
	{
		// Add only in Rich Editor mode
		if (get_user_option('rich_editing') == 'true' && ($this->_options['PSAct'] || $this->_options['PICIAct']) )
		{
			add_filter('mce_external_plugins', array($this, 'prosperTinyRegister'));
			add_filter('mce_buttons', array($this, 'prosperTinyAdd'));
		}
	}
	
	public function prosperTinyRegister($plugin_array)
	{		
		if (get_bloginfo('version') >= 3.9)
		{
			$plugin_array['prosperent'] = PROSPER_JS . '/prosperent3.9.min.js?ver=' . $this->_version . 2134;
		}
		else
		{
			$plugin_array['prosperent'] = PROSPER_JS . '/prosperent.min.js?ver=' . $this->_version. 21332;
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
	    if (wp_script_is('quicktags'))
	    {
    		?>
    		<script type="text/javascript">
    			QTags.addButton(<?php echo "'" . $id . "', '" . $display . "', '" . $arg1 . "', '" . $arg2 . "'"; ?>);
    		</script>
    		<?php
	    }
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
			$sidArray = array();
			foreach ($this->_options['prosperSid'] as $sidPiece)
			{
				if ('blogname' === $sidPiece)
				{
					$sidArray[] = get_bloginfo('name');
				}
				elseif ('interface' === $sidPiece)
				{
					$sidArray[] = $settings['interface'] ? $settings['interface'] : 'api';
				}
				elseif ('query' === $sidPiece)
				{
					$sidArray[] = $settings['query'];
				}
				elseif ('page' === $sidPiece)
				{
					$sidArray[] = get_the_title();
				}
				elseif ('widgetTitle' === $sidPiece)
				{
					$sidArray[] = get_the_title();
				}
				elseif ('widgetName' === $sidPiece)
				{
					$sidArray[] = get_the_title();
				}
				elseif ('authorId' === $sidPiece)
				{
					$sidArray[] = get_the_author_meta('ID');
				}
				elseif ('authorName' === $sidPiece)
				{
					$sidArray[] = get_the_author_meta('user_login');
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
		
		if ($sidArray)
		{
			$sidArray = array_filter($sidArray);
			$sid = implode('_', $sidArray);
		}
	
		echo '<script type="text/javascript">var _prosperent={"campaign_id":"' . $this->_options['Api_Key'] . '", "pl_active":' . (wp_script_is('loginCheck') ? 0 : 1) . ', "pl_sid":"' . $sid . '", "pl_phraselinker_active":0, "pl_linkoptimizer_active":' . ($this->_options['PL_LinkOpt'] ? 1 : 0) . ', "pl_linkaffiliator_active":1, "platform":"wordpress"};</script><script async type="text/javascript" src="//prosperent.com/js/prosperent.js"></script>';
	}
	
	public function apiCall ($settings, $fetch, $sid = '')
	{				
		if (empty($this->_endPoints))
		{
			$this->_endPoints = $this->getFetchEndpoints();
		}
	
		if (empty($this->_options))
		{
			$this->_options = $this->getOptions();
		}		

		if ($this->_options['prosperSid'] && !$sid)
		{
			$sidArray = array();
			foreach ($this->_options['prosperSid'] as $sidPiece)
			{
				if ('blogname' === $sidPiece)
				{
					$sidArray[] = get_bloginfo('name');
				}
				elseif ('interface' === $sidPiece)
				{
					$sidArray[] = $settings['interface'] ? $settings['interface'] : 'api';
				}
				elseif ('query' === $sidPiece)
				{
					$sidArray[] = $settings['query'];
				}
				elseif ('page' === $sidPiece)
				{
					$sidArray[] = get_the_title();
				}
				elseif ('widgetTitle' === $sidPiece)
				{
					$sidArray[] = get_the_title();
				}
				elseif ('widgetName' === $sidPiece)
				{
					$sidArray[] = get_the_title();
				}
				elseif ('authorId' === $sidPiece)
				{
					$sidArray[] = get_the_author_meta('ID');
				}
				elseif ('authorName' === $sidPiece)
				{
					$sidArray[] = get_the_author_meta('user_login');
				}
				elseif ('postId' === $sidPiece)
				{
				    $sidArray[] = get_the_ID();
				}
			}
		}
		if ($this->_options['prosperSidText'] && !$sid)
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
		
		if ($sidArray)
		{
			$sidArray = array_filter($sidArray);
			$sid = implode('_', $sidArray);
		}

		$settings = array_merge(array(
			'api_key' 		  	 => $this->_options['Api_Key'],
			'location'  	  	 => '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'referrer' 		  	 => $_SERVER['HTTP_REFERER'],
			'imageMaskDomain' 	 => $this->_options['ImageCname'],
			'clickMaskDomain' 	 => $this->_options['ClickCname'],
			'sid'			  	 => $sid,
			'relevancyThreshold' => $this->_options['relThresh']
		), $settings);	

		$settings = array_filter( $settings);

		// Set the URL
		$url = $this->_endPoints[$fetch] . http_build_query ($settings);

		return $url;
	}
	
	public function multiCurlCall ($urls = array(), $expiration = 86400, $settings = array())
	{		
		require_once(PROSPER_PATH . 'prosperMemcache.php');
		$cache = new Prosper_Cache(); 

		$result = $cache->get($settings);

		if ($result === FALSE)
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
				
			if ($result['data'])
			{
				$cache->set($settings, $result, $expiration);
			}			
		}

		return $result;
	}
	
	public function singleCurlCall ($url = '', $expiration = 86400, $settings = array())
	{	
		require_once(PROSPER_PATH . 'prosperMemcache.php');
		$cache = new Prosper_Cache();

		$response = $cache->get($settings);

		if ($response === FALSE)
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
				return array();
				throw new Exception(implode('; ', $response['errors']));
			}

			if ($response['data'])
			{
				$cache->set($settings, $response, $expiration);
			}			
		}

		return $response;
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
		
		$brandFilter = true;
		$filter = 'filterCatalogId';
		$catalog = 'US';
		
		// calculate date range
		$prevNumDays = 60;
		$startDate   = date('Ymd', time() - 86400 * $prevNumDays);
		$endDate     = date('Ymd');		
		
		$apiCall = array(
			'curlCall'			   => 'trends',
			'api_key' 	     	   => $this->_options['Api_Key'],
			'enableFacets'   	   => 'catalogId',
			'filterCommissionDate' => $startDate . ',' . $endDate,
			'filterCatalog'  	   => $catalog,
			'filterCategory' 	   => implode('|', $categories),			
			'filterMerchant'	   => implode('|', $merchants),
			'filterBrand'		   => $brandFilter ? implode('|', $brands) : ''			
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

			// fetch trend data from api
			$settings = array_merge(array(
				$filter		   => implode('|', $keys),
				'limit'		   => $options['Pagination_Limit'],
				'sid'		   => $sid,
				'curlCall'	   => 'single-trends'
			), $settings);

			$trendsUrl = $this->apiCall($settings, $fetch, $sid);
			
			$results = $this->singleCurlCall($trendsUrl, 86400, $settings);
		}
		
		return (array) $results;
	}
	
	public function trendsCurlCall($settings)
	{
		if (!$this->_endPoints)
		{
			$this->_endPoints = $this->getFetchEndpoints();
		}
		$url = $this->_endPoints['fetchTrends'] . http_build_query ($settings);

		require_once(PROSPER_PATH . 'prosperMemcache.php');
		$cache = new Prosper_Cache(); 

		$response = $cache->get($settings);

		if ($response === FALSE)
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
			if (count($response['errors']) || empty($response['facets']['catalogId']))
			{
				$count = count($settings);
				for ($i = 0; $i <= $count; $i++)
				{
					array_pop($settings);

					if(count($settings) < 5)
					{
						return;
					}
				
					$response = $this->trendsCurlCall($settings);

					if ($response['facets']['catalogId'])
					{
						break;
					}	 
				}
			}				
			
			if ($response['data'])
			{
				$cache->set($settings, $response);
			}
		}
		
		return $response;
	}
}
