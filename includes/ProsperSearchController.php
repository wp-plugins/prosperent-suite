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
		
		add_action('wp_head', array($this->searchModel, 'ogMeta'), 1);
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

		define('DONOTCACHEPAGE', true);
	
		$this->searchModel->storeChecker();		
		$data = $this->searchModel->storeSearch();
				
		$params = $data['params'];
		$homeUrl = home_url('', 'http');
		if (is_ssl())
		{
			$homeUrl = home_url('', 'https');
		}

		if (!empty($_POST))
		{
			if (get_query_var('cid'))
			{
				$data['url'] = $homeUrl . '/' . ($options['Base_URL'] ? $options['Base_URL'] : 'products');
			}
			
			if ($_POST['q'] && $_POST['type'] == 'local')
			{ 
				$_POST['state'] = $_POST['q'];
				unset($_POST['q']);
			}
			
			if (strlen($_POST['state']) > 2)
			{
				$state = $this->searchModel->states[strtolower($_POST['state'])];
			}
			else
			{
				$state = $_POST['state'];
			}
			
			if ($_POST['q'] && $_POST['type'] == 'cele')
			{
				$_POST['celebrity'] = $_POST['q'];
				unset($_POST['q']);
			}

			if ($_POST['onSale'] && $_POST['percentSliderMin'] == '0%')
			{
				$_POST['percentSliderMin'] = '0.01%';
			}

			$postArray = array(
				'type'   	=> $_POST['type'],
				'query' 	=> $_POST['q'],
				'sort' 	 	=> $_POST['sort'],
				'celebrity' => $_POST['celebrity'],
				'state'  	=> $state,
				'dR' 	 	=> ($_POST['priceSliderMin'] || $_POST['priceSliderMax'] ? str_replace('$', '' , $_POST['priceSliderMin'] . ',' . $_POST['priceSliderMax']) : ''),
				'pR' 	 	=> ($_POST['percentSliderMin'] || $_POST['percentSliderMax'] ? str_replace('%', '' , $_POST['percentSliderMin'] . ',' . $_POST['percentSliderMax']) :''),
				'merchant'  => stripslashes($_POST['merchant'])
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
		
		if ($options['Enable_Facets'] && $options['Enable_Sliders'])
		{
			$this->searchModel->sliderJs();
		}
		
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
		$filters 	  = $data['filters'];
		$params 	  = $data['params'];
		$typeSelector = $data['typeSelector'];
		$target 	  = isset($options['Target']) ? '_blank' : '_self';
		$pickedFacets = array();
		$curlUrls	  = array();
		$dollarSlider = 'Price Range';

		if ($params['dR'])
		{
			$priceSlider = explode(',', rawurldecode($params['dR']));
			$pickedFacets[] = '<a href="' . str_replace('/dR/' . $params['dR'], '', $data['url']) . '">$' . implode(' - $', $priceSlider) . ' <l style="font-size:12px;">&#215;</l></a>';
		}
		if ($params['pR'])
		{
			$percentSlider = explode(',', rawurldecode($params['pR']));
			$pickedFacets[] = '<a href="' . str_replace('/pR/' . $params['pR'], '', $data['url']) . '">' . implode('% - ', $percentSlider) . '% Off <l style="font-size:12px;">&#215;</l></a>';
		}

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

		$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		if (!$params['query'] && !$params['brand'] && !$params['category'] && !$params['merchant'] && $options['Starting_Query'])
		{			
			if (is_front_page())
			{
				$url .= (preg_match('/\/$/', $url) ? '' : '/') . ($options['Base_URL'] ? $options['Base_URL'] : 'products'); 
			}
			
			$url = preg_replace('/\/$/', '', $url) . '/query/' . htmlentities($options['Starting_Query']);
			$q = $options['Starting_Query'];
		}
		else
		{
			$url = preg_replace('/\/$/', '', $url);
			$q = rawurldecode($params['query']);
		}

		$query = $q ? stripslashes($q) : null;

		/*
		 * Get title for results line
		 */ 
		if ($query)
		{
			$title = ucwords($query);
			if (strlen($title) > 60)
			{
				$title = '<strong>' . substr($title, 0, 30) . '</strong>...';
			}
			else
				$title = '<strong>' . $title . '</strong>';
			if ($params['brand'] || $params['merchant'])
			{
				$demolishUrl = str_replace(array('/page/' . $params['page'], '/query/' . $params['query']), '', $url);
			}
		}
		elseif ($params['brand'])
		{
			$title =  '<strong>' . ucwords(str_replace('&', ' & ', rawurldecode($params['brand']))) . '</strong>';
		}
		elseif ($params['merchant'])
		{
			$title = '<strong>' . ucwords(str_replace('&', ' & ', rawurldecode($params['merchant']))) . '</strong>';
		}
		elseif ($params['category'])
		{
			$title = '<strong>' . ucwords(rawurldecode($params['category'])) . '</strong>';
		}

		$sortArray = array(
			'Default'			 => 'rel',
			'Price: High to Low' => 'price desc',
			'Price: Low to High' => 'price asc',
			'Merchant: A-Z' 	 => 'merchant asc',
			'Merchant: Z-A' 	 => 'merchant desc'			
		);

		if ($query || $filters['brand'] || $filters['merchant'] || $filters['category'])
		{			
			$settings = array(
				'page'			   => $params['page'],
				'query'            => $query,
				'sortBy'	       => $params['sort'] != 'rel' ? rawurldecode($params['sort']) : '',
				'filterBrand'      => implode('|', $filters['brand']),
				'filterMerchant'   => implode('|', $filters['merchant']),
				'filterCategory'   => implode('|', $filters['category']),
				'filterPrice'	   => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',				
				'limit'			   => $options['Pagination_Limit'],
				'imageSize'		   => $imageSize,
				'curlCall'		   => 'multi-product'
			);	
		
			$curlUrls['results'] = $this->searchModel->apiCall($settings, $fetch);			
		}

		if ($options['Enable_Facets'] && ($query || $filters['brand'] || $filters['merchant'] || $filters['category']))
		{
			$extraMerchants = array();
			if($options['Negative_Merchant'])
			{
				$minusBrands = array_map('stripslashes', explode(',', $options['Negative_Merchant']));
				foreach ($minusBrands as $negative)
				{
					$extraMerchants[] = '!' . trim($negative);
				}
			}

			if($options['Positive_Merchant'])
			{
				$plusMerchants = array_map('stripslashes', explode(',', $options['Positive_Merchant']));
				foreach ($plusMerchants as $plus)
				{
					$extraMerchants[] = trim($plus);
				}
			}
			
			if (!$query && $filters['merchant'])
			{
				$extraMerchants = array_merge($extraMerchants, $filters['merchant']);
			}

			$merchantFacetSettings = array(
				'query'            => $query,
				'enableFacets'     => 'merchant',
				'limit'			   => 1,
				'filterMerchant'   => $extraMerchants,				
				'filterCategory'   => $filters['category'],
				'filterBrand'	   => $filters['brand'],
				'filterPrice'	   => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
				'enableFullData'   => 'FALSE'
			);	

			$curlUrls['merchants'] = $this->searchModel->apiCall($merchantFacetSettings, $fetch);	

			$extraBrands = array();
			if($options['Positive_Brand'])
			{
				$plusBrands = array_map('stripslashes', explode(',', $options['Positive_Brand']));
				foreach ($plusBrands as $positive)
				{
					$extraBrands[] = trim($positive);
				}
			}

			if($options['Negative_Brand'])
			{
				$minusBrands = array_map('stripslashes', explode(',', $options['Negative_Brand']));
				foreach ($minusBrands as $negative)
				{
					$extraBrands[] = '!' . trim($negative);
				}
			}
			
			if (!$query && $filters['brand'])
			{
				$extraBrands = array_merge($extraBrands, $filters['brand']);
			}

			$brandFacetSettings = array(
				'query'            => $query,
				'enableFacets'     => 'brand',
				'limit'			   => 1,
				'filterMerchant'   => $filters['merchant'],				
				'filterCategory'   => $filters['category'],
				'filterBrand'	   => $extraBrands,
				'filterPrice'	   => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
				'enableFullData'   => 'FALSE'
			);	

			$curlUrls['brands'] = $this->searchModel->apiCall($brandFacetSettings, $fetch);
		}

		$everything = $this->searchModel->multiCurlCall($curlUrls, PROSPER_CACHE_PRODS, $settings);
		
		if ($everything['brands']['facets'] || $everything['merchants']['facets'])
		{			
			$allFilters = array_merge((array) $everything['brands']['facets'], (array) $everything['merchants']['facets']);
			$filterArray = $this->searchModel->buildFacets($allFilters, $params, $filters, $url);

			$pickedFacets = array_merge($pickedFacets, $filterArray['picked']);

			$brands    = array_splice($filterArray['all']['brand'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 7));
			$merchants = array_splice($filterArray['all']['merchant'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 7));
			//$category  = array_splice($filterArray['category'], 0, 10);
			
			ksort($filterArray['all']['brand']);
			ksort($filterArray['all']['merchant']);
			//sort($filterArray['category']);

			$mainFilters = array('brand' => $brands, 'merchant' => $merchants );
			$secondaryFilters = array('brand' => $filterArray['all']['brand'], 'merchant' => $filterArray['all']['merchant']);
		}
		
		if ($results = $everything['results']['data'])
		{
			$totalFound = $everything['results']['totalRecordsFound'];
			$totalAvailable = $everything['results']['totalRecordsAvailable'];
		}
		else
		{
			$settings = array(
				'imageSize'	=> $imageSize
			);

			$allData   = $this->searchModel->trendsApiCall($settings, $fetch, array_map('trim', explode(',', $options['No_Results_Categories'])));
			$results   = $allData['data'];	

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
		$pickedFacets 	 = array();
		$curlUrls		 = array();
		$dollarSlider	 = 'Dollars Off';
		
		if ($params['dR'])
		{
			$priceSlider = explode(',', rawurldecode($params['dR']));
			$pickedFacets[] = '<a href="' . str_replace('/dR/' . $params['dR'], '', $data['url']) . '">$' . implode(' - $', $priceSlider) . ' <l style="font-size:12px;">&#215;</l></a>';
		}
		if ($params['pR'])
		{
			$percentSlider = explode(',', rawurldecode($params['pR']));
			$pickedFacets[] = '<a href="' . str_replace('/pR/' . $params['pR'], '', $data['url']) . '">' . implode('% - ', $percentSlider) . '% Off <l style="font-size:12px;">&#215;</l></a>';
		}		
		
		$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		if (!$params['query'] && !$params['merchant'] && $options['Coupon_Query'])
		{			
			if (is_front_page())
			{
				$url .= (preg_match('/\/$/', $url) ? '' : '/') . ($options['Base_URL'] ? $options['Base_URL'] : 'products'); 
			}
			
			$url = preg_replace('/\/$/', '', $url) . '/query/' . htmlentities(rawurlencode($options['Coupon_Query']));
			$q = $options['Coupon_Query'];
		}
		else
		{
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
			$title = '<strong>' . ucwords(str_replace('&', ' & ', rawurldecode($params['merchant']))) . '</strong>';
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
		
		
		if ($query || $filters['merchant'] || $filters['category'])
		{		
			$settings = array(
				'page'			   => $params['page'],
				'query'            => $query,
				'sortBy'	       => rawurldecode($params['sort']),
				'filterMerchant'   => implode('|', $filters['merchant']),	
				'filterCategory'   => implode('|', $filters['category']),
				'filterMerchantId' => '!123473|!124147',
				'limit'			   => $options['Pagination_Limit'],
				'filterDollarsOff' => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
				'curlCall'		   => 'multi-coupon'
			);	
	
			$curlUrls['results'] = $this->searchModel->apiCall($settings, $fetch);
		}

		if ($options['Enable_Facets'] && ($query || $filters['merchant'] || $filters['category']))
		{
			$extraMerchants = array();
			if($options['Negative_Merchant'])
			{
				$minusMerchants = array_map('stripslashes', explode(',', $options['Negative_Merchant']));
				foreach ($minusMerchants as $negative)
				{
					$extraMerchants[] = '!' . trim($negative);
				}
			}

			if($options['Positive_Merchant'])
			{
				$plusMerchants = array_map('stripslashes', explode(',', $options['Positive_Merchant']));
				foreach ($plusMerchants as $plus)
				{
					$extraMerchants[] = trim($plus);
				}
			}
			
			if (!$query && $filters['merchant'])
			{
				$extraMerchants = array_merge($extraMerchants, $filters['merchant']);
			}
			
			$merchantFacetSettings = array(
				'query'            => $query,
				'enableFacets'     => 'merchant',						
				'filterCategory'   => $filters['category'],
				'limit'			   => 1,
				'filterMerchant'   => $extraMerchants,
				'filterMerchantId' => '!123473|!124147',
				'filterDollarsOff' => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
				'enableFullData'   => 'FALSE'
			);	

			$curlUrls['merchants'] = $this->searchModel->apiCall($merchantFacetSettings, $fetch);	
		}

		$everything = $this->searchModel->multiCurlCall($curlUrls, PROSPER_CACHE_COUPS, $settings);
		
		if ($everything['merchants']['facets'])
		{			
			$filterArray = $this->searchModel->buildFacets($everything['merchants']['facets'], $params, $filters, $url);

			$pickedFacets = array_merge($pickedFacets, $filterArray['picked']);

			$merchants = array_splice($filterArray['all']['merchant'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 7));
		
			sort($filterArray['all']['merchant']);
			
			$mainFilters 	  = array('merchant' => $merchants);
			$secondaryFilters = array('merchant' => $filterArray['all']['merchant']);
		}
		
		if ($results = $everything['results']['data'])
		{
			$totalFound = $everything['results']['totalRecordsFound'];
			$totalAvailable = $everything['results']['totalRecordsAvailable'];
		}
		else
		{
			$settings = array(
				'page'		=> $params['page']
			);
			
			$allData   = $this->searchModel->trendsApiCall($settings, $fetch);

			$results   = $allData['data'];	
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
		$fetch		  = 'fetchLocal';
		$settings 	  = array();
		$pickedFacets = array();
		$curlUrls     = array();
		$dollarSlider = 'Dollars Off';

		if ($params['dR'])
		{
			$priceSlider = explode(',', rawurldecode($params['dR']));
			$pickedFacets[] = '<a href="' . str_replace('/dR/' . $params['dR'], '', $data['url']) . '">$' . implode(' - $', $priceSlider) . ' <l style="font-size:12px;">&#215;</l></a>';
		}
		if ($params['pR'])
		{
			$percentSlider = explode(',', rawurldecode($params['pR']));
			$pickedFacets[] = '<a href="' . str_replace('/pR/' . $params['pR'], '', $data['url']) . '">' . implode('% - ', $percentSlider) . '% Off <l style="font-size:12px;">&#215;</l></a>';
		}		
		
		$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];		
		if (!$filterState && $options['Local_Query'])
		{
			if (is_front_page())
			{
				$url .= (preg_match('/\/$/', $url) ? '' : '/') . ($options['Base_URL'] ? $options['Base_URL'] : 'products'); 
			}
			
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
					$filterState = $this->searchModel->states[$localQuery[0]];
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
				$filterState = $this->searchModel->states[strtolower($localQuery[0])];
				$url = $url . '/state/' . rawurlencode($filterState);
			}
		}
		else
		{			
			$url = preg_replace('/\/$/', '', $url);
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
		if ($params['city'] || $filterCity)
		{
			$title = '<strong>' . ucwords(str_replace('&', ' & ', rawurldecode(($params['city'] ? $params['city'] : $filterCity)))) . '</strong>';
		}
		elseif ($params['zip'] || $filterZip)
		{
			$title = '<strong>' . ucwords(str_replace('&', ' & ', rawurldecode(($params['zip'] ? $params['zip'] : $filterZip)))) . '</strong>';
		}
		elseif ($params['state'] || $filterState)
		{
			$title = '<strong>' . ucwords(rawurldecode($backStates[($params['state'] ? $params['state'] : $filterState)])) . '</strong>';			
		}	
		elseif ($params['merchant'] || $filterMerchant)
		{
			$title = '<strong>' . ucwords(rawurldecode($params['merchant'] ? $params['merchant'] : $filterMerchant)) . '</strong>';			
		}	
	
		if ($params['view'] === 'grid' && ($options['Grid_Img_Size'] > '125' || !$options['Grid_Img_Size']))
		{
			$imageSize = '250x250';
		}
		else
		{
			$imageSize = '125x125';
		}
	
		$query = $params['state'] ? ucwords($backStates[$params['state']]) : '';
	
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

		if ($filterZip || $filterCity || $filterState || $filters['category'] || $params['merchant'])
		{			
			$settings = array(
				'sortBy'	     => rawurldecode($params['sort']),
				'filterZipCode'  => implode('|', $filters['zip']),
				'filterCity'     => implode('|', $filters['city']),
				'filterState'    => $filterState,
				'filterCategory' => implode('|', $filters['category']),
				'filterMerchant' => rawurldecode($params['merchant']),
				'limit'			 => $options['Pagination_Limit'],
				'imageSize'		 => $imageSize,
				'query'			 => $params['query'],
				'page'			 => $params['page'],
				'curlCall'		 => 'multi-local'
			);	

			$curlUrls['results'] = $this->searchModel->apiCall($settings, $fetch);
		}

		if ($options['Enable_Facets'] && ($filterZip || $filterCity || $filterState || $params['merchant']))
		{
			$zipCodeFacetSettings = array(
				'filterCity'       => $filters['city'],
				'filterState'      => $filterState,
				'filterMerchant'   => rawurldecode($params['merchant']),
				'enableFacets'     => 'zipCode',
				'limit'			   => 1,
				'filterCategory'   => $filters['category'],
				'filterDollarsOff' => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
				'enableFullData'   => 'FALSE'
			);	

			$curlUrls['zip'] = $this->searchModel->apiCall($zipCodeFacetSettings, $fetch);	
			
			$cityFacetSettings = array(
				'filterState'      => $filterState,
				'enableFacets'     => 'city',
				'limit'			   => 1,
				'filterCategory'   => $filters['category'],
				'filterZipCode'    => $filters['zip'],
				'filterDollarsOff' => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
				'enableFullData'   => 'FALSE'
			);	

			$curlUrls['city'] = $this->searchModel->apiCall($cityFacetSettings, $fetch);	
		}

		$everything = $this->searchModel->multiCurlCall($curlUrls, PROSPER_CACHE_COUPS, $settings);

		if ($everything['zip']['facets'] || $everything['city']['facets'])
		{			
			$filterArray = $this->searchModel->buildFacets(array_merge($everything['zip']['facets'], $everything['city']['facets']), $params, $filters, $url);

			$pickedFacets = array_merge($pickedFacets, $filterArray['picked']);

			$cities = array_splice($filterArray['all']['city'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 10));
			$zips	= array_splice($filterArray['all']['zip'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 10));
		
			sort($filterArray['all']['city']);
			sort($filterArray['all']['zip']);
			
			$mainFilters 	  = array('city' => $cities, 'zipCode' => $zips);
			$secondaryFilters = array('city' => $filterArray['all']['city'], 'zipCode' => $filterArray['all']['zip']);
		}
		
		if ($results = $everything['results']['data'])
		{
			$totalFound = $everything['results']['totalRecordsFound'];
			$totalAvailable = $everything['results']['totalRecordsAvailable'];
		}
		else
		{
			$settings = array(
				'filterState' => 'null',
				'imageSize'   => $imageSize,
				'page'		  => $params['page']
			);
			
			$allData   = $this->searchModel->trendsApiCall($settings, 'fetchLocal');
			$results   = $allData['data'];
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
		// Register AutoComplete Script
		wp_register_script( 'celebrityAutoComplete', PROSPER_JS . '/autosuggest.js', array('jquery', 'jquery-ui-autocomplete'), '3.1.7');
		wp_enqueue_script( 'celebrityAutoComplete' );
	
		$filters 	  = $data['filters'];
		$params 	  = $data['params'];
		$typeSelector = $data['typeSelector'];
		$target 	  = isset($options['Target']) ? '_blank' : '_self'; 
		$searchPost   = 'celebrity';
		$searchTitle  = 'Celebrities';
		$fetch		  = 'fetchCelebrities';
		$dollarSlider = 'Price Range';
		$pickedFacets = array();
		
		if ($params['view'] === 'grid' && ($options['Grid_Img_Size'] > '125' || !$options['Grid_Img_Size']))
		{
			$imageSize = '250x250';
		}
		else
		{
			$imageSize = '125x125';
		}
		
		$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		if (!$params['celebrity'] && $options['Celebrity_Query'])
		{
			if (is_front_page())
			{
				$url .= (preg_match('/\/$/', $url) ? '' : '/') . ($options['Base_URL'] ? $options['Base_URL'] : 'products'); 
			}
			
			$url = preg_replace('/\/$/', '', $url) . 'celebrity/' . rawurlencode($options['Celebrity_Query']);
			$c = $options['Celebrity_Query'];
		}
		elseif ($params['celebrity'] || $params['query'])
		{
			$c = rawurldecode($params['celebrity']);
			$q = rawurldecode($params['query']);
		}
		else
		{
			if (is_front_page())
			{
				$url .= (preg_match('/\/$/', $url) ? '' : '/') . ($options['Base_URL'] ? $options['Base_URL'] : 'products'); 
			}
		}
			
		$query = stripslashes($q);	
			
		if ($params['celebrity'])
		{			
			$title = '<strong>' .ucwords(rawurldecode($params['celebrity']));
			$title .= $params['query'] ? ' &raquo; ' . $params['query'] . '</strong>' : '</strong>';
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
			$title = '<strong>' . ucwords(str_replace('&', ' & ', rawurldecode($params['brand']))) . '</strong>';
		}
		elseif ($params['merchant'])
		{
			$title = '<strong>' . ucwords(str_replace('&', ' & ', rawurldecode($params['merchant']))) . '</strong>';
		}
			
		$sortArray = array(
			'Price: High to Low' => 'price desc',
			'Price: Low to High' => 'price asc'
		);

		if (!empty($params['celebrity']))
		{
			$settings = array(
				'limit'  		  => 1,
				'filterCelebrity' => rawurldecode($params['celebrity'])
			);
			
			$query = $params['celebrity'] ? ucwords(rawurldecode($params['celebrity'])) : '';
			
			$curlUrls['celebrity'] = $this->searchModel->apiCall($settings, 'fetchCelebrities');
		}

		if ($params['celebrity'] || $query || $filters['merchant'] || $filters['brand'])
		{
			$settings = array(
				'sortBy'	      => rawurldecode($params['sort']),
				'filterMerchant'  => implode('|', $filters['merchant']),
				'filterCelebrity' => $params['celebrity'] ? rawurldecode($params['celebrity']) : '',
				'limit'			  => $options['Pagination_Limit'],
				'imageSize'		  => $imageSize,
				'filterBrand'	  => implode('|', $filters['brand']),
				'page'			  => $params['page'],
				'curlCall'		  => 'multi-celebrity'
			);	
		
			$curlUrls['results'] = $this->searchModel->apiCall($settings, 'fetchProducts');
		}
		
		if ($options['Enable_Facets'] && ($params['celebrity'] || $query))
		{
			$extraMerchants = array();
			if($options['Negative_Merchant'])
			{
				$minusMerchants = array_map('stripslashes', explode(',', $options['Negative_Merchant']));
				foreach ($minusMerchants as $negative)
				{
					$extraMerchants[] = '!' . trim($negative);
				}
			}

			if($options['Positive_Merchant'])
			{
				$plusMerchants = array_map('stripslashes', explode(',', $options['Positive_Merchant']));
				foreach ($plusMerchants as $plus)
				{
					$extraMerchants[] = trim($plus);
				}
			}
			
			if (!$query && $filters['merchant'])
			{
				$extraMerchants = array_merge($extraMerchants, $filters['merchant']);
			}
		
			$merchantFacetSettings = array(
				'enableFacets'     => array('merchant'),
				'filterBrand'	   => $filters['brand'],
				'filterMerchant'   => $extraMerchants,
				'limit'			   => 1,
				'filterCelebrity'  => $params['celebrity'] ? rawurldecode($params['celebrity']) : '',
				'filterPrice'	   => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
				'enableFullData'   => 'FALSE'
			);	

			$curlUrls['merchants'] = $this->searchModel->apiCall($merchantFacetSettings, 'fetchProducts');	
		
			$extraBrands = array();
			if($options['Positive_Brand'])
			{
				$plusBrands = array_map('stripslashes', explode(',', $options['Positive_Brand']));
				foreach ($plusBrands as $positive)
				{
					$extraBrands[] = trim($positive);
				}
			}
			if($options['Negative_Brand'])
			{
				$minusBrands = array_map('stripslashes', explode(',', $options['Negative_Brand']));
				foreach ($minusBrands as $negative)
				{
					$extraBrands[] = '!' . trim($negative);
				}
			}
			
			if (!$query && $filters['brand'])
			{
				$extraBrands = array_merge($extraBrands, $filters['brand']);
			}
		
			$brandFacetSettings = array(
				'enableFacets'     => array('brand'),
				'limit'			   => 1,
				'filterCelebrity'  => $params['celebrity'] ? rawurldecode($params['celebrity']) : '',
				'filterMerchant'   => $filters['merchant'],
				'filterBrand'	   => $extraBrands,
				'filterPrice'	   => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
				'enableFullData'   => 'FALSE'
			);	

			$curlUrls['brands'] = $this->searchModel->apiCall($brandFacetSettings, 'fetchProducts');
		}

		$everything = $this->searchModel->multiCurlCall($curlUrls, PROSPER_CACHE_PRODS, $settings);
		
		if ($everything['merchants']['facets'])
		{			
			$allFilters = array_merge((array) $everything['brands']['facets'], (array) $everything['merchants']['facets']);
			$filterArray = $this->searchModel->buildFacets($allFilters, $params, $filters, $url);

			$pickedFacets = array_merge($pickedFacets, $filterArray['picked']);

			$brands    = array_splice($filterArray['all']['brand'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 7));
			$merchants = array_splice($filterArray['all']['merchant'], 0, ($options['Merchant_Facets'] ? $options['Merchant_Facets'] : 7));
			//$category  = array_splice($filterArray['category'], 0, 10);
			
			ksort($filterArray['all']['brand']);
			ksort($filterArray['all']['merchant']);
			//sort($filterArray['category']);

			$mainFilters = array('brand' => $brands, 'merchant' => $merchants );
			$secondaryFilters = array('brand' => $filterArray['all']['brand'], 'merchant' => $filterArray['all']['merchant']);
		}
		
		if ($results = $everything['results']['data'])
		{
			$totalFound = $everything['results']['totalRecordsFound'];
			$totalAvailable = $everything['results']['totalRecordsAvailable'];
			
			$celebrityInfo = $everything['celebrity']['data'][0];
		}
		else
		{
			$settings = array(
				'enableFacets' => 'productId',
				'imageSize'	   => $imageSize,
				'page'		   => $params['page']					
			);

			$allData   = $this->searchModel->trendsApiCall($settings, 'fetchProducts');
			$results   = $allData['data'];	
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
		$backStates  = array_flip($this->searchModel->states);
		$curlUrls    = array();
		$expiration  = PROSPER_CACHE_COUPS;

		if ('coupon' === $prosperPage)
		{
			$fetch   	= 'fetchCoupons';
			$filter  	= 'filterCouponId';
			$type   	= 'coupon';
			$urltype 	= 'coup';
		}
		elseif ('local' === $prosperPage)
		{
			$fetch   	= 'fetchLocal';
			$filter  	= 'filterLocalId';
			$urltype = $type = 'local';
		}
		elseif ('celebrity' === $prosperPage)
		{
			$fetch   	= 'fetchProducts';
			$filter  	= 'filterCatalogId';
			$group   	= 'productId';
			$brand   	= true;
			$urltype 	= 'cele';
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
			
			$brand   	= true;
			$filter  	= 'filterCatalogId';
			$group   	= 'productId';			
			$urltype 	= 'prod';				
			$expiration = PROSPER_CACHE_PRODS;
		}		
				
		$matchingUrl = $homeUrl . '/' . ($options['Base_URL'] ? $options['Base_URL'] : 'products');
		$match = '/' . str_replace('/', '\/', $matchingUrl) . '/i';
		if (preg_match($match, $_SERVER['HTTP_REFERER']) || preg_match('/type\/' . $urltype . '/i', $_SERVER['HTTP_REFERER']))
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
			$filter		   => get_query_var('cid'),
			'imageSize'	   => $image ? $image : '',
			'curlCall'	   => 'single-prodPage-' . $prosperPage
		);

		$maincUrl = $this->searchModel->apiCall($settings, $fetch);	
		$allData = $this->searchModel->singleCurlCall($maincUrl, $expiration, $settings);
		$mainRecord = $allData['data'];
		
		if (empty($mainRecord))
		{
			header('Location: ' . $url . '/query/' . rawurlencode(get_query_var('keyword')));
			exit;
		}

		if ($prosperPage === 'product' || $prosperPage === 'celebrity')
		{
			/*
			/  GROUPED RESULTS
			*/
			$settings2 = array(
				'limit'           => 10,
				'filterProductId' => $settings['filterProductId'] = $mainRecord[0]['productId'],
				'enableFullData'  => 'FALSE'
			);

			$curlUrls['groupedResult'] = $this->searchModel->apiCall($settings2, $fetch);
					
			/*
			/  ALL RESULTS
			*/
			$settings3 = array(
				'limit'           => 10,
				'filterProductId' => $settings['filterProductId'] = $mainRecord[0]['productId'],
				'enableFullData'  => 'FALSE'
			);
		
			$curlUrls['results'] = $this->searchModel->apiCall($settings3, $fetch);
		}
		if ($options['Similar_Limit'] > 0)
		{
			/*
			/  SIMILAR
			*/
			$settings4 = array(
				'limit'          => $settings['simProdLimit'] = $options['Similar_Limit'],
				'query'		     => $settings['query'] = $mainRecord[0]['keyword'],
				'enableFullData' => 'FALSE',
				'imageSize'		 => $image ? $image : ''
			);

			if ($prosperPage === 'local')
			{	
				$fullState  = $backStates[$mainRecord[0]['state']];
				$settings['filterState'] = $settings4['filterState']  = $mainRecord[0]['state'];				
			}
			
			$curlUrls['similar'] = $this->searchModel->apiCall($settings4, $fetch);
		}
		
		if ($options['Same_Limit'] > 0 && $brand == true)
		{
			/*
			/  SAME BRAND
			*/
			$settings5 = array(
				'limit'       => $settings['sameBrandLimit'] = $options['Same_Limit'],
				'imageSize'   => $image ? $image : '',
				'filterBrand' => $settings['filterBrand'] = $mainRecord[0]['brand']
			);

			$curlUrls['sameBrand'] = $this->searchModel->apiCall($settings5, $fetch);	
		}
		
		if ($options['Same_Limit_Merchant'] > 0)
		{ 
			/*
			/  SAME MERCHANT
			*/
			$settings6 = array(
				'limit'            => $settings['sameMerchantLimit'] = $options['Same_Limit_Merchant'],
				'imageSize'		   => $image ? $image : '',
				'filterMerchantId' => $settings['filterMerchantId'] = $mainRecord[0]['merchantId']
			);
			
			if ($prosperPage === 'local')
			{
				$fullState  = $backStates[$mainRecord[0]['state']];
				$settings['filterState'] = $settings6['filterState'] = $mainRecord[0]['state'];
			}
			
			$curlUrls['sameMerchant'] = $this->searchModel->apiCall($settings6, $fetch);		
		}

		$settings['curlCall'] = 'multi-prodPage';

		$allData = $this->searchModel->multiCurlCall($curlUrls, $expiration, $settings);

		$groupedResult = $allData['groupedResult']['data'];
		$results 	   = $allData['results']['data'];
		$similar 	   = $allData['similar']['data'];		
		$sameBrand     = $allData['sameBrand']['data'];
		$sameMerchant  = $allData['sameMerchant']['data'];

		require_once($productPage);	
	}
}
 
$prosperProductSearch = new ProsperSearchController;