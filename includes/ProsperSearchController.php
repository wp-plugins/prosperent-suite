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

		add_shortcode('prosper_store', array($this, 'storeShortcode'));
		add_shortcode('prosper_search', array($this->searchModel, 'searchShortcode'));
		add_action('wp_head', array($this->searchModel, 'ogMeta'));
		add_filter('wp_title', array($this->searchModel, 'prosperTitle'), 20, 3);
		
		if (is_admin())
		{
			add_action('admin_print_footer_scripts', array($this->searchModel, 'qTagsStore'));	
			add_action('admin_print_footer_scripts', array($this->searchModel, 'qTagsSearch'));	
		}
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
			
			$this->searchModel->getPostVars($postArray, $data);
		}

		$data['params']['view'] = !$params['view'] ? $options['Product_View'] : $params['view'];

		if (get_query_var('cid'))
		{  
			$this->productPageAction($data, $homeUrl,$productPage, $options);
			return;
		}

		$type = isset($params['type']) ? $params['type'] : $data['startingType'];

		switch ($type)
		{
			case 'prod': 
				$this->productAction($data, $homeUrl, 'product', $searchPage, $options);
				break;
			case 'coup':
				$this->couponAction($data, $homeUrl, 'coupon', $searchPage, $options);
				break;
			case 'cele':
				$this->celebrityAction($data, $homeUrl, 'celebrity', $searchPage, $options);
				break;
			case 'local':
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
			$title = ucwords($query) . '</strong>';
			$title .= ($params['merchant'] || $params['brand']) ? '<a class="xDemolish" href=' . str_replace(array('/page/' . $params['page'], '/query/' . $params['query']), '', $url) . '> [x]</a>' : '';
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
			'Relevancy'			 => '',
			'Price: High to Low' => 'price desc',
			'Price: Low to High' => 'price asc',
			'Merchant: A-Z' 	 => 'merchant asc',
			'Merchant: Z-A' 	 => 'merchant desc'			
		);

		if ($query || $filters['brands'] || $filters['merchants'])
		{
			$settings = array(
				'query'          => $query,
				'sortBy'	     => rawurldecode($params['sort']),
				'groupBy'	     => 'productId',
				'filterBrand'    => $filters['brands'],
				'filterMerchant' => $filters['merchants'],
				'enableFacets'   => $options['Enable_Facets'] ? array('brand', 'merchant') : FALSE,
				'limit'			 => $options['Api_Limit'],
				'imageSize'		 => $params['view'] === 'grid' ? '250x250' : '125x125'
			);	
			
			$settings = array_filter($settings);
			
			$allData = $this->searchModel->apiCall($settings, $fetch);
		}

		if ($facets = $allData['facets'])
		{
			$filterArray = $this->searchModel->buildFacets($allData['facets'], $params, $url);
			
			$brands 	= array_splice($filterArray['brand'], 0, $this->_options['Merchant_Facets'] ? $this->_options['Merchant_Facets'] : 10);
			$merchants = array_splice($filterArray['merchant'], 0, $this->_options['Merchant_Facets'] ? $this->_options['Merchant_Facets'] : 10);
		
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
				'imageSize'	=> $params['view'] === 'grid' ? '250x250' : '125x125'
			);
			
			$allData   = $this->searchModel->trendsApiCall($settings, $fetch);
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
		//$filterCelebrity = $params['celeb'];
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
			$q = $params['query'];
		}

		$query = $q ? stripslashes($q) : null;
	
		/*
		 * Get title for results line
		 */ 
		if ($query)
		{
			$title = ucwords($query) . '</strong>';
			$title .= ($params['merchant'] || $params['brand']) ? '<a class="xDemolish" href=' . str_replace(array('/page/' . $params['page'], '/query/' . $params['query']), '', $url) . '> [x]</a>' : '';
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
				//'filterCelebrity' => rawurldecode($params['celeb']),
				'enableFacets'    => $options['Enable_Facets'] ? array('merchant') : FALSE,
				'limit'			  => $options['Api_Limit']
			);	
			
			$settings = array_filter($settings);

			$allData = $this->searchModel->apiCall($settings, $fetch, '3600');
		}
		
		if ($results = $allData['results'])
		{
			$totalFound = $allData['total'];

			if ($facets = $allData['facets'])
			{
				$filterArray = $this->searchModel->buildFacets($allData['facets'], $params, $url);
				
				$merchants = array_splice($filterArray['merchant'], 0, $this->_options['Merchant_Facets'] ? $this->_options['Merchant_Facets'] : 10);
			
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
		if ($filterCity === 'Online' || $filterZip === 'Online')
		{
			$title = 'Online Deals</strong>';
		}
		elseif ($params['city'])
		{
			$title = ucwords(rawurldecode($params['city'])) . '</strong>';
		}
		elseif ($params['zip'])
		{
			$title = ucwords(rawurldecode($params['zip'])) . '</strong>';
		}
		elseif ($params['state'])
		{
			$title = ucwords(rawurldecode($backStates[$params['state']])) . '</strong>';
			$title .= '<a class="xDemolish" href=' . str_replace(array('/page/' . $params['page'], '/state/' . $params['state']), '', $url) . '> [x]</a>';
		}	
		else
		{
			$title = 'Online Deals</strong>';
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
				
		$settings = array(
			'sortBy'	     => rawurldecode($params['sort']),
			'enableFacets'   => $options['Enable_Facets'] ? array('city', 'zipCode') : FALSE,
			'filterZipCode'  => $filterZip,
			'filterCity'     => $filterCity,
			'filterState'    => $filterState,
			'filterMerchant' => rawurldecode($params['merchant']),
			'limit'			 => $options['Api_Limit'],
			'imageSize'		 => '125x125'
		);	
		
		if ((!$filterCity && !$filterZip && !$params['state']) || $filterCity == 'Online' || $filterZip == 'Online')
		{
			$settings = array_merge($settings, array(			
				'filterZipCode'  => 'null',
				'filterCity'     => 'null',
				'filterState'    => 'null'
			));
		}
		
		$settings = array_filter($settings);

		$allData = $this->searchModel->apiCall($settings, 'fetchLocal', '3600');
		$results = $allData['results'];
		
		if ($results)
		{
			$totalFound = $allData['total'];
			
			if ($facets = $allData['facets'])
			{
				$filterArray = $this->searchModel->buildFacets($allData['facets'], $params, $url);
								
				$cities = array_splice($filterArray['city'], 0, $this->_options['Merchant_Facets'] ? $this->_options['Merchant_Facets'] : 10);
				$zips	 = array_splice($filterArray['zip'], 0, $this->_options['Merchant_Facets'] ? $this->_options['Merchant_Facets'] : 10);
			
				sort($filterArray['city']);
				sort($filterArray['zip']);
				
				$mainFilters 	  = array('city' => $cities, 'zipCode' => $zips);
				$secondaryFilters = array('city' => $filterArray['city'], 'zipCode' => $filterArray['zip']);
			}
		}
		elseif(!$results) 
		{
			$settings = array(
				'filterZipCode'  => 'null',
				'filterCity'     => 'null',
				'filterState'    => 'null',
				'limit'			 => $options['Api_Limit'],
				'imageSize'		 => '125x125'
			);	
		
		
			$settings = array_filter($settings);

			$allData = $this->searchModel->apiCall($settings, 'fetchLocal', '3600');
			$results   = $allData['results'];
			$noResults = true;
			$trend 	   = 'Online Deals';
		}
		else
		{
			$settings = array(
				'imageSize' => '125x125'
			);
			
			$allData   = $this->searchModel->trendsApiCall($settings, 'fetchLocal');
			$results   = $allData['results'];
			$noResults = true;
			$trend 	   = 'local deals';
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

		if (!$params['celebrity'] && $options['Celebrity_Query'])
		{
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url = preg_replace('/\/$/', '', $url) . 'celebrity/' . rawurlencode($options['Celebrity_Query']);
			$c = $options['Celebrity_Query'];
		}
		else
		{
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$c = $params['celebrity'];
			$q = $params['query'];
		}
			
		$query = stripslashes($q);	
			
		if ($params['celebrity'])
		{			
			$title = ucwords(rawurldecode($params['celebrity'])) . '</strong>';
			$title .= $params['query'] ? ' &raquo; ' . $params['query'] . '<a class="xDemolish" href=' . str_replace(array('/page/' . $params['page'], '/query/' . $params['query']), '', $url) . '> [x]</a>' : '';
		}
		elseif ($params['query'])
		{
			$title = ucwords(rawurldecode($params['query'])) . '</strong>';
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

		if ($params['celebrity'] || $query || $filters['merchant'])
		{
			$settings = array(
				'query'           => $query,
				'sortBy'	      => rawurldecode($params['sort']),
				'filterMerchant'  => $filters['merchants'],
				'filterCelebrity' => $params['celebrity'] ? rawurldecode($params['celebrity']) : '',
				'enableFacets'    => $options['Enable_Facets'] ? array('celebrity') : FALSE,
				'limit'			  => $options['Api_Limit'],
				'imageSize'		  => $params['view'] === 'grid' ? '250x250' : '125x125'
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
				'imageSize'	   => $params['view'] === 'grid' ? '250x250' : '125x125'				
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
				
		$matchingUrl = $homeUrl . '/' . ($options['Base_URL'] ? $options['Base_URL'] : 'products');
		$match = '/' . str_replace('/', '\/', $matchingUrl) . '/i';
		if (preg_match($match, $_SERVER['HTTP_REFERER']))
		{
			$returnUrl = $_SERVER['HTTP_REFERER'];
		}
		else
		{
			$returnUrl = $matchingUrl . '/query/' . get_query_var('keyword');
		}
		
		if ('coupon' === $prosperPage)
		{
			$fetch  = 'fetchCoupons';
			$filter = 'filterCouponId';
		}
		elseif ('local' === $prosperPage)
		{
			$fetch  = 'fetchLocal';
			$filter = 'filterLocalId';
			$image  = '125x125';
		}
		elseif ('celebrity' === $prosperPage)
		{
			$fetch  = 'fetchProducts';
			$filter = 'filterCatalogId';
			$group  = 'productId';
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
			$filter = 'filterCatalogId';
			$group  = 'productId';
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

			$allData4 = $this->searchModel->apiCall($settings4, $fetch);
			$similar = $allData4['results'];
		}
		
		if ($options['Same_Limit'] > 0)
		{
			/*
			/  SAME BRAND/ MERCHANT
			*/
			$settings5 = array(
				'limit'          => $options['Same_Limit'],
				'groupBy'	   	 => $group,
				'enableFullData' => 0,
				'imageSize'		 => $image ? $image : ''
			);
			
			if ($params['type'] === 'prod' || $params['type'] === 'cele')
			{
				$settings5 = array_merge($settings5, array('filterBrand' => $mainRecord[0]['brand']));
				$otherName = $mainRecord[0]['brand'];
			}
			else
			{
				$settings5 = array_merge($settings5, array('filterMerchant' => $mainRecord[0]['merchant']));
				$otherName = $mainRecord[0]['merchant'];
			}
			
			$settings5 = array_filter($settings5);

			$allData5 = $this->searchModel->apiCall($settings5, $fetch);
			$sameBrand = $allData5['results'];		
		}
		
		require_once($productPage);	
	}
}
 
$prosperProductSearch = new ProsperSearchController;