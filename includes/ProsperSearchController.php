<?php
/**
 * ProsperSearch Controller
 *
 * @package 
 * @subpackage 
 */
class ProsperSearchController
{
	public $searchModel;
    /**
     * the class constructor
     *
     * @package 
     * @subpackage 
     *
     */
    public function __construct()
    {		
		require_once(PROSPER_MODEL . '/Search.php');
		$this->searchModel = new Model_Search();
		
		$this->searchModel->init();

		add_shortcode('prosper_store', array($this, 'storecode'));
		add_shortcode('prosper_search', array($this->searchModel, 'searchShortcode'));
		add_action('wp_head', array($this->searchModel, 'ogMeta'));
		add_filter('wp_title', array($this->searchModel, 'prosperTitle'), 20, 3);
		
		if (is_admin())
		{
			add_action('admin_print_footer_scripts', array($this->searchModel, 'qTagsStore'));	
			add_action('admin_print_footer_scripts', array($this->searchModel, 'qTagsSearch'));	
		}
    }
	
	public function storecode()
	{
		ob_start();
		$this->storeShortcode();
		$store = ob_get_clean();
		return $store;
	}
	
	public function storeShortcode()
	{				
		$options 	 = $this->searchModel->_options;
		$phtml 		 = $this->searchModel->getSearchPhtml();
		$searchPage  = $phtml[0];
		$productPage = $phtml[1];

		if (get_query_var('storeUrl'))
		{    
			$this->searchModel->getStoreUrl(get_query_var('storeUrl'));	
		}

		if (get_query_var('prosperImg'))
		{    
			$this->searchModel->getImageUrl(get_query_var('prosperImg'));	
		}
	
		$this->searchModel->storeChecker();		
		$data = $this->searchModel->storeSearch();
				
		$params = $data['params'];
		$homeUrl = home_url();

		if (!empty($_POST))
		{
			if (get_query_var('cid'))
			{
				$data['url'] = $homeUrl . '/' . ($options['Base_URL'] ? $options['Base_URL'] : 'products');
			}
			
			if (strlen($_POST['state']) > 2)
			{
				$state = $this->searchModel->states[strtolower($_POST['state'])];
			}
			else
			{
				$state = $_POST['state'];
			}
			
			$postArray = array(
				'query' => $_POST['q'],
				'sort' 	=> $_POST['sort'],
				'state' => $state
			);

			if ($postArray['query'])
			{				
				$recentOptions = get_option('prosper_productSearch');
				$recentOptions['recentSearches'][] = $postArray['query'];
				if (count($recentOptions['recentSearches']) > $recentOptions['numRecentSearch'])
				{
					$remove = array_shift($recentOptions['recentSearches']);
				}
				
				update_option('prosper_productSearch', $recentOptions);				
			}
			
			$this->searchModel->getPostVars($postArray, $data);
		}

		if (get_query_var('cid'))
		{  
			$this->productPageAction($data, $homeUrl, $productPage, $options);
			return;
		}

		$type = isset($params['type']) ? $params['type'] : $data['startingType'];

		switch ($type)
		{
			case 'prod': 
				$data['params']['view'] = !$params['view'] ? $options['Product_View'] : $params['view'];
				$this->productAction($data, $homeUrl, 'product', $searchPage, $options);
				break;
			case 'coup':
				$data['params']['view'] = !$params['view'] ? $options['Coupon_View'] : $params['view'];
				$this->couponAction($data, $homeUrl, 'coupon', $searchPage, $options);
				break;
			case 'cele':
				$data['params']['view'] = !$params['view'] ? $options['Product_View'] : $params['view'];
				$this->celebrityAction($data, $homeUrl, 'celebrity', $searchPage, $options);
				break;
			case 'local':
				$data['params']['view'] = !$params['view'] ? $options['Coupon_View'] : $params['view'];
				$this->localAction($data, $homeUrl, 'local', $searchPage, $options);
				break;				
		}
	}
	
	public function productAction($data, $homeUrl, $type, $searchPage, $options)
	{			
		$filters 	    = $data['filters'];
		$params 	    = $data['params'];
		$typeSelector   = $data['typeSelector'];
		$target 	    = isset($options['Target']) ? '_blank' : '_self';

		if ($params['view'] === 'grid' && ($options['Grid_Img_Size'] > '125' || !$options['Grid_Img_Size']))
		{
			$imageSize = '250x250';
		}
		else
		{
			$imageSize = '125x125';
		}

		if ($options['Country'] === 'US')
		{
			$fetch = 'fetchProducts';
			$currency = 'USD';
		}
		elseif ($options['Country'] === 'CA')
		{
			$fetch = 'fetchCaProducts';
			$currency = 'CAD';
		}
		else 
		{
			$fetch = 'fetchUkProducts';
			$currency = 'GBP';
		}

		if (!$params['query'] && !$params['brand'] && !$params['merchant'] && $options['Starting_Query'])
		{
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url = preg_replace('/\/$/', '', $url) . '/query/' . htmlentities(rawurlencode($options['Starting_Query']));
			$q = $options['Starting_Query'];
		}
		else
		{
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url = preg_replace('/\/$/', '', $url);
			$q = rawurldecode($params['query']);
		}

		$query = $q ? stripslashes($q) : null;

		/*
		 * Get title for results line
		 */ 
		if ($query)
		{
			$title = '<strong>' . ucwords($query) . '</strong>';
			if ($params['brand'] || $params['merchant'])
			{
				$demolishUrl = str_replace(array('/page/' . $params['page'], '/query/' . $params['query']), '', $url);
			}
		}
		elseif ($params['brand'])
		{
			$title = ucwords(rawurldecode($params['brand'])) . '</strong>';
		}
		elseif ($params['merchant'])
		{
			$title = ucwords(rawurldecode($params['merchant'])) . '</strong>';
		}

		$sortArray = array(
			'Relevancy'			 => 'rel',
			'Price: High to Low' => 'price desc',
			'Price: Low to High' => 'price asc',
			'Merchant: A-Z' 	 => 'merchant asc',
			'Merchant: Z-A' 	 => 'merchant desc'			
		);

		if ($query || $filters['brands'] || $filters['merchants'])
		{			
			$settings = array(
				'query'          => $query,
				'sortBy'	     => $params['sort'] != 'rel' ? rawurldecode($params['sort']) : '',
				'groupBy'	     => 'productId',
				'filterBrand'    => $filters['brands'],
				'filterMerchant' => $filters['merchants'],
				'enableFacets'   => $options['Enable_Facets'] ? array('brand', 'merchant') : FALSE,
				'limit'			 => $options['Api_Limit'],
				'imageSize'		 => $imageSize
			);	

			$settings = array_filter($settings);

			$allData = $this->searchModel->apiCall($settings, $fetch);
		}

		if ($facets = $allData['facets'])
		{
			$filterArray = $this->searchModel->buildFacets($allData['facets'], $params, $url);
			
			$brands    = array_splice($filterArray['brand'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 10));
			$merchants = array_splice($filterArray['merchant'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 10));
		
			sort($filterArray['brand']);
			sort($filterArray['merchant']);
			
			$mainFilters = array('brand' => $brands, 'merchant' => $merchants);
			$secondaryFilters = array('brand' => $filterArray['brand'], 'merchant' => $filterArray['merchant']);
		}
		
		if ($results = $allData['results'])
		{
			$totalFound = $allData['total'];
		}
		else
		{
			$settings = array(
				'imageSize'	=> $imageSize
			);
			
			$allData   = $this->searchModel->trendsApiCall($settings, $fetch, array_map('trim', explode(',', $options['No_Results_Categories'])));
			$results   = $allData['results'];	
			$noResults = true;
			$trend     = 'Trending Products';
			header( $_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404 );

			if (!$results)
			{
				$newTrendsTitle = 'No Trending Products at this Time';
			}
		}

		require_once($searchPage);
	}

	public function couponAction($data, $homeUrl, $type, $searchPage, $options)
	{
		$filters 	     = $data['filters'];
		$params 	     = $data['params'];
		$typeSelector    = $data['typeSelector'];
		$fetch 			 = 'fetchCoupons';
		$target 	     = isset($options['Target']) ? '_blank' : '_self';
		$searchTitle     = 'Coupons';
		
		if (!$params['query'] && !$params['merchant'] && $options['Coupon_Query'])
		{
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url = preg_replace('/\/$/', '', $url) . '/query/' . htmlentities(rawurlencode($options['Coupon_Query']));
			$q = $options['Coupon_Query'];
		}
		else
		{
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url = preg_replace('/\/$/', '', $url);
			$q = rawurldecode($params['query']);
		}

		$query = $q ? stripslashes($q) : null;
	
		/*
		 * Get title for results line
		 */ 
		if ($query)
		{
			$title = '<strong>' . ucwords($query) . '</strong>';
			if ($params['merchant'])
			{
				$demolishUrl = str_replace(array('/page/' . $params['page'], '/query/' . $params['query']), '', $url);
			}
		}
		elseif ($params['merchant'])
		{
			$title = ucwords(rawurldecode($params['merchant'])) . '</strong>';
		}

		$sortArray = array(
			'Price: High to Low' 		  => 'price desc',
			'Price: Low to High' 		  => 'price asc',
			'Merchant: A-Z' 			  => 'merchant asc',
			'Merchant: Z-A' 			  => 'merchant desc',
			'Expiration Date: Descending' => 'expiration_date desc',
			'Expiration Date: Ascending'  => 'expiration_date asc',
			'Dollars Off: High to Low' 	  => 'dollarsOff desc',
			'Dollars Off: Low to High' 	  => 'dollarsOff asc',
			'Percent Off: High to Low' 	  => 'percentOff desc',
			'Percent Off: Low to High' 	  => 'percentOff asc',
			'Rank: High to Low' 	  	  => 'rank desc',
			'Rank: Low to High' 	  	  => 'rank asc'
		);
		
		if ($query || $filters['merchants'])
		{
			$settings = array(
				'query'           => $query,
				'sortBy'	      => rawurldecode($params['sort']),
				'filterMerchant'  => $filters['merchants'],
				'enableFacets'    => $options['Enable_Facets'] ? array('merchant') : FALSE,
				'limit'			  => $options['Api_Limit']
			);	
			
			$settings = array_filter($settings);

			$allData = $this->searchModel->apiCall($settings, $fetch, PROSPER_CACHE_COUPS);
		}
		
		if ($results = $allData['results'])
		{
			$totalFound = $allData['total'];

			if ($facets = $allData['facets'])
			{
				$filterArray = $this->searchModel->buildFacets($allData['facets'], $params, $url);
				
				$merchants = array_splice($filterArray['merchant'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 10));
			
				sort($filterArray['merchant']);
				
				$mainFilters 	  = array('merchant' => $merchants);
				$secondaryFilters = array('merchant' => $filterArray['merchant']);
			}				
		}
		else
		{
			$settings = array();
			
			$allData   = $this->searchModel->trendsApiCall($settings, $fetch);
			$results   = $allData['results'];	
			$noResults = true;
			$trend     = 'Trending Coupons';
			
			if (!$results)
			{
				$newTrendsTitle = 'No Trending Coupons at this Time';
			}
			header( $_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404 );				
		}

		require_once($searchPage);
	}

	public function localAction($data, $homeUrl, $type, $searchPage, $options)
	{
		$filters 	  = $data['filters'];
		$params 	  = $data['params'];
		$typeSelector = $data['typeSelector'];
		$filterZip 	  = rawurldecode($params['zip']);
		$filterCity   = rawurldecode($params['city']);
		$filterState  = rawurldecode($params['state']);
		$searchPost   = 'state';
		$target 	  = isset($options['Target']) ? '_blank' : '_self'; 
		$searchTitle  = 'Local Deals';

		if (!$filterState && $options['Local_Query'])
		{
			$localQuery = preg_split('/,/', $options['Local_Query']);

			if(count($localQuery) > 2)
			{
				$filterCity = $localQuery[0];

				if (strlen($localQuery[1]) == 2)
				{
					$filterState = $localQuery[0];
				}
				else
				{
					$filterState = $states[$localQuery[0]];
				}

				$url = $url . '/state/' . rawurlencode($filterState) . '/city/' . rawurlencode($filterCity);
			}
			elseif (strlen($localQuery[0]) == 2)
			{
				$filterState = $localQuery[0];
				$url = $url . '/state/' . rawurlencode($filterState);
			}
			else
			{
				$filterState = $states[$localQuery[0]];
				$url = $url . '/state/' . rawurlencode($filterState);
			}
		}
		else
		{
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url = preg_replace('/\/$/', '', $url);
		}
		
		if ($filterState)
		{
			$stateFull = strtolower($filterState);
			$state = $this->searchModel->states[$stateFull];
		}	
			
		$backStates = array_flip($this->searchModel->states);
	
		if (empty($params['state']) && isset($options['Geo_Locate']))
		{
			require_once(PROSPER_INCLUDE . '/geo/geoplugin.php');
			//locate the IP
			$geoplugin = new geoPlugin();
			$geoplugin->locate();

			$filterState = $geoplugin->region;
			$filterCity  = $geoplugin->city;

			header('Location: ' . $url . '/state/' . rawurlencode($filterState) . '/city/' . rawurlencode($filterCity));
			exit;
		}

		/*
		 * Get title for results line
		 */ 
		if ($params['city'])
		{
			$title = '<strong>' . ucwords(rawurldecode($params['city'])) . '</strong>';
		}
		elseif ($params['zip'])
		{
			$title = '<strong>' . ucwords(rawurldecode($params['zip'])) . '</strong>';
		}
		elseif ($params['state'])
		{
			$title = '<strong>' . ucwords(rawurldecode($backStates[$params['state']])) . '</strong>';			
		}	
	
		if ($params['view'] === 'grid' && ($options['Grid_Img_Size'] > '125' || !$options['Grid_Img_Size']))
		{
			$imageSize = '250x250';
		}
		else
		{
			$imageSize = '125x125';
		}
	
		$sortArray = array(
			'Price: High to Low' 		  => 'price desc',
			'Price: Low to High' 		  => 'price asc',
			'Merchant: A-Z' 			  => 'merchant asc',
			'Merchant: Z-A' 			  => 'merchant desc',
			'Expiration Date: Descending' => 'expirationDate desc',
			'Expiration Date: Ascending'  => 'expirationDate asc',
			'City: A-Z' 				  => 'city desc',
			'City: Z-A'					  => 'city asc',
			'ZipCode: High to Low' 		  => 'zipCode desc',
			'ZipCode: Low to High' 		  => 'zipCode asc',
		);	
				
		if ($filterZip || $filterCity || $filterState)
		{			
			$settings = array(
				'sortBy'	     => rawurldecode($params['sort']),
				'enableFacets'   => $options['Enable_Facets'] ? array('city', 'zipCode') : FALSE,
				'filterZipCode'  => $filterZip,
				'filterCity'     => $filterCity,
				'filterState'    => $filterState,
				'filterMerchant' => rawurldecode($params['merchant']),
				'limit'			 => $options['Api_Limit'],
				'imageSize'		 => $imageSize,
				'query'			 => $params['query']
			);	
		}
			
		$settings = array_filter($settings);

		$allData = $this->searchModel->apiCall($settings, 'fetchLocal', PROSPER_CACHE_COUPS);
		$results = $allData['results'];
		
		if ($results)
		{
			$totalFound = $allData['total'];
			
			if ($facets = $allData['facets'])
			{
				$filterArray = $this->searchModel->buildFacets($allData['facets'], $params, $url);
								
				$cities = array_splice($filterArray['city'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 10));
				$zips	= array_splice($filterArray['zip'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 10));
			
				sort($filterArray['city']);
				sort($filterArray['zip']);
				
				$mainFilters 	  = array('city' => $cities, 'zipCode' => $zips);
				$secondaryFilters = array('city' => $filterArray['city'], 'zipCode' => $filterArray['zip']);
			}
		}
		else
		{
			$settings = array(
				'filterState' => $filterState,
				'imageSize'   => $imageSize
			);
			
			$allData   = $this->searchModel->trendsApiCall($settings, 'fetchLocal');
			$results   = $allData['results'];
			$noResults = true;
			$trend 	   = 'Trending Local Deals';
			header( $_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404 );	

			if (!$results)
			{
				$newTrendsTitle = 'No Trending Local Deals at this Time';
			}
		}
		
		require_once($searchPage);
	}

	public function celebrityAction($data, $homeUrl, $type, $searchPage, $options)
	{
		$filters 	  = $data['filters'];
		$params 	  = $data['params'];
		$typeSelector = $data['typeSelector'];
		$target 	  = isset($options['Target']) ? '_blank' : '_self'; 

		if ($params['view'] === 'grid' && ($options['Grid_Img_Size'] > '125' || !$options['Grid_Img_Size']))
		{
			$imageSize = '250x250';
		}
		else
		{
			$imageSize = '125x125';
		}
		
		if (!$params['celebrity'] && $options['Celebrity_Query'])
		{
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url = preg_replace('/\/$/', '', $url) . 'celebrity/' . rawurlencode($options['Celebrity_Query']);
			$c = $options['Celebrity_Query'];
		}
		else
		{
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$c = rawurldecode($params['celebrity']);
			$q = rawurldecode($params['query']);
		}
			
		$query = stripslashes($q);	
			
		if ($params['celebrity'])
		{			
			$title = ucwords(rawurldecode($params['celebrity'])) . '</strong>';
			$title .= $params['query'] ? ' &raquo; ' . $params['query'] : '';
			if ($params['query'])
			{
				$demolishUrl = str_replace(array('/page/' . $params['page'], '/query/' . $params['query']), '', $url);
			}
		}
		elseif ($params['query'])
		{
			$title = '<strong>' . ucwords(rawurldecode($params['query'])) . '</strong>';
		}
		elseif ($params['brand'])
		{
			$title = '<strong>' . ucwords(rawurldecode($params['brand'])) . '</strong>';
		}
		elseif ($params['merchant'])
		{
			$title = '<strong>' . ucwords(rawurldecode($params['merchant'])) . '</strong>';
		}
			
		$sortArray = array(
			'Price: High to Low' => 'price desc',
			'Price: Low to High' => 'price asc'
		);

		$settings = array(
			'limit'  => 500,
			'sortBy' => 'celebrity asc'
		);
		
		$celebrities = $this->searchModel->apiCall($settings, 'fetchCelebrities');

		$filterArray = array();
		foreach ($celebrities['results'] as $celeb)
		{
			$filterArray[] = '<a href=' . str_replace('/page/' . $params['page'], '', $url) . '/celebrity/' . rawurlencode($celeb['celebrity']) . '>' . $celeb['celebrity'] . '</a>';
		}

		$mainFilters = array('celebrity' => $filterArray);

		if ($params['celebrity'] || $query || $filters['merchants'] || $filters['brands'])
		{
			$settings = array(
				'query'           => $query,
				'sortBy'	      => rawurldecode($params['sort']),
				'filterMerchant'  => $filters['merchants'],
				'filterCelebrity' => $params['celebrity'] ? rawurldecode($params['celebrity']) : '',
				'enableFacets'    => $options['Enable_Facets'] ? array('celebrity') : FALSE,
				'limit'			  => $options['Api_Limit'],
				'imageSize'		  => $imageSize,
				'filterBrand'	  => $filters['brands']
			);	
			
			$settings = array_filter($settings);

			$allData = $this->searchModel->apiCall($settings, 'fetchProducts');		
		}
		
		if ($results = $allData['results'])
		{		
			$totalFound = $allData['total'];		
		}
		else
		{
			$settings = array(
				'enableFacets' => 'productId',
				'imageSize'	   => $imageSize				
			);

			$allData   = $this->searchModel->trendsApiCall($settings, 'fetchProducts');
			$results   = $allData['results'];	
			$noResults = true;
			$trend 	   = 'Trending Products';
			header( $_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404 );

			if (!$results)
			{
				$newTrendsTitle = 'No Trending Products at this Time';
			}
		}	
		
		require_once($searchPage);	
	}
	
	public function productPageAction ($data, $homeUrl, $productPage, $options)
	{
		$params 	 = $data['params'];
		$prosperPage = get_query_var('prosperPage');
		$keyword 	 = rawurldecode(get_query_var('keyword'));
		$keyword 	 = str_replace(',SL,', '/', $keyword);
		$target 	 = isset($options['Target']) ? '_blank' : '_self';
		$type   	 = 'product';
		$backStates = array_flip($this->searchModel->states);
		
		if ('coupon' === $prosperPage)
		{
			$fetch   = 'fetchCoupons';
			$filter  = 'filterCouponId';
			$type    = 'coupon';
			$urltype = 'coup';
		}
		elseif ('local' === $prosperPage)
		{
			$fetch   = 'fetchLocal';
			$filter  = 'filterLocalId';
			$urltype = $type = 'local';
		}
		elseif ('celebrity' === $prosperPage)
		{
			$fetch   = 'fetchProducts';
			$filter  = 'filterCatalogId';
			$group   = 'productId';
			$brand   = true;
			$urltype = 'cele';
		}
		else
		{		
			if ($options['Country'] === 'US')
			{
				$fetch = 'fetchProducts';
				$currency = 'USD';
			}
			elseif ($options['Country'] === 'CA')
			{
				$fetch = 'fetchCaProducts';
				$currency = 'CAD';
			}
			else 
			{
				$fetch = 'fetchUkProducts';
				$currency = 'GBP';
			}
			
			$brand   = true;
			$filter  = 'filterCatalogId';
			$group   = 'productId';			
			$urltype = 'prod';
		}		
				
		$matchingUrl = $homeUrl . '/' . ($options['Base_URL'] ? $options['Base_URL'] : 'products') . '/type/' . $urltype;
		
		$match = '/' . str_replace('/', '\/', $matchingUrl) . '/i';
		if (preg_match($match, $_SERVER['HTTP_REFERER']))
		{
			$returnUrl = $_SERVER['HTTP_REFERER'];
		}
		else
		{
			$returnUrl = $matchingUrl . '/query/' . get_query_var('keyword');
		}

		/*
		/  MAIN RECORD
		*/
		$settings = array(
			'limit'        => 1,
			'enableFacets' => $options['Enable_Facets'],
			$filter		   => get_query_var('cid'),
			'imageSize'	   => $image ? $image : ''
		);

		$settings = array_filter($settings);

		$allData = $this->searchModel->apiCall($settings, $fetch);	
		$mainRecord = $allData['results'];
		
		if (empty($mainRecord))
		{
			header('Location: ' . $url . '/query/' . htmlentities(rawurlencode(get_query_var('keyword'))));
			exit;
		}

		if ($prosperPage === 'product' || $prosperPage === 'celebrity')
		{
			/*
			/  GROUPED RESULTS
			*/
			$settings2 = array(
				'limit'           => 10,
				'filterProductId' => $mainRecord[0]['productId'],
				'groupBy'		  => $group,
				'enableFullData'  => 0
			);

			$allData2 = $this->searchModel->apiCall($settings2, $fetch);
			$groupedResult = $allData2['results'];
					
			/*
			/  ALL RESULTS
			*/
			$settings3 = array(
				'limit'           => 10,
				'filterProductId' => $mainRecord[0]['productId'],
				'enableFullData'  => 0
			);
		
			$allData3 = $this->searchModel->apiCall($settings3, $fetch);
			$results = $allData3['results'];
		}
		if ($options['Similar_Limit'] > 0)
		{
			/*
			/  SIMILAR
			*/
			$settings4 = array(
				'limit'          => $options['Similar_Limit'],
				'query'		     => $mainRecord[0]['keyword'],
				'groupBy'	   	 => $group,
				'enableFullData' => 0,
				'imageSize'		 => $image ? $image : ''
			);

			if ($prosperPage = 'local')
			{	
				$fullState  = $backStates[$mainRecord[0]['state']];
				$settings4  = array_merge($settings4, array(
					'filterState' => $mainRecord[0]['state']
				));				
			}
			
			$allData4 = $this->searchModel->apiCall($settings4, $fetch);
			$similar = $allData4['results'];
		}
		
		if ($options['Same_Limit'] > 0 && $brand == true)
		{
			/*
			/  SAME BRAND
			*/
			$settings5 = array(
				'limit'       => $options['Same_Limit'],
				'groupBy'	  => $group,
				'imageSize'   => $image ? $image : '',
				'filterBrand' => $mainRecord[0]['brand']
			);

			$allData5 = $this->searchModel->apiCall($settings5, $fetch);
			$sameBrand = $allData5['results'];		
		}
		
		if ($options['Same_Limit_Merchant'] > 0)
		{ 
			/*
			/  SAME MERCHANT
			*/
			$settings6 = array(
				'limit'          => $options['Same_Limit_Merchant'],
				'groupBy'	   	 => $group,
				'imageSize'		 => $image ? $image : '',
				'filterMerchant' => $mainRecord[0]['merchant']
			);
			
			if ($prosperPage = 'local')
			{
				$fullState  = $backStates[$mainRecord[0]['state']];
				$settings6 = array_merge($settings6, array(
					'filterState' => $mainRecord[0]['state']
				));
			}
			
			$allData6 = $this->searchModel->apiCall($settings6, $fetch);
			$sameMerchant = $allData6['results'];		
		}
		
		require_once($productPage);	
	}
}
 
$prosperProductSearch = new ProsperSearchController;