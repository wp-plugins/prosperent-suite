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
		
		if (!$this->searchModel->_options['PSAct'])
		{
		    return;
		}
		
		add_shortcode('prosper_store', array($this, 'storecode'));
		add_shortcode('prosper_search', array($this->searchModel, 'searchShortcode'));
		
		add_action('wp_head', array($this->searchModel, 'ogMeta'), 1);
		add_filter('wp_title', array($this->searchModel, 'prosperTitle'), 20, 3);
		
		if (is_admin())
		{
			add_action('admin_print_footer_scripts', array($this->searchModel, 'qTagsStore'));	
			add_action('admin_print_footer_scripts', array($this->searchModel, 'qTagsSearch'));	
		}

		add_action( 'wp_enqueue_scripts', array($this->searchModel, 'prosperShopVars' ));
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

		if (!$options['PSAct'])
		{
		    return;
		}
		
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

			if ($_POST['onSale'] && $_POST['percentSliderMin'] == '0%')
			{
				$_POST['percentSliderMin'] = '0.01%';
			}

			$postArray = array(
				'query' 	=> $_POST['q'],
				'sort' 	 	=> $_POST['sort'],
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
		
		if ($options['Enable_Facets'] && $options['Enable_Sliders'])
		{
			//$this->searchModel->sliderJs();
		}		

		$data['params']['view'] = !$params['view'] ? $options['Product_View'] : $params['view'];
		$this->productAction($data, $homeUrl, 'product', $searchPage, $options);			
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
		$url		  = $data['url'];
		$visitButton  = 'Visit Store';
		//global $wp;
		$currentUrl = rtrim(home_url( $_SERVER['REQUEST_URI']/*$wp->request*/ ), '/');

		if ($params['view'] === 'grid' && ($options['Grid_Img_Size'] > '125' || !$options['Grid_Img_Size']))
		{
			$imageSize = '250x250';
		}
		else
		{
			$imageSize = '125x125';
		}

		$fetch = 'fetchProducts';
		$currency = 'USD';
		
		if (!$params['query'] && !$params['brand'] && !$params['category'] && !$params['merchant'] && $options['Starting_Query'])
		{						
			$url .= '/query/' . htmlentities($options['Starting_Query']);
			$q = $options['Starting_Query'];
		}
		else
		{
			$q = rawurldecode($params['query']);
		}

		$query = $q ? stripslashes(str_replace(',SL,', '/', $q)) : null;

		/*
		 * Backwards compatibility for old endpoints
		 */
		if (!$query)
		{
		    if ($params['celebrity'])
		    {
		        $query = $params['celebrity'];
		    }
		    elseif ($params['state'])
		    {
		        $query = $params['state'];
		    }
		    elseif ($params['city'])
		    {
		        $query = $params['city'];
		    }
		}
				
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
		elseif ($params['merchant'])
		{
			$title =  '<strong>' . ucwords(rawurldecode(str_replace(',SL,', '/', $params['brand']))) . '</strong>';
			if (strpos($params['merchant'], '~'))
			{
				$title = '<strong>the Chosen Merchants</strong>';
			}	
		}
		elseif ($params['brand'])
		{
			$title = '<strong>' . ucwords(rawurldecode(str_replace(',SL,', '/', $params['merchant']))) . '</strong>';
			if (strpos($params['merchant'], '~'))
			{
				$title = '<strong>the Chosen Brands</strong>';
			}			
		}
		elseif ($params['category'])
		{
			$title = '<strong>' . ucwords(rawurldecode(str_replace(',SL,', '/', $params['category']))) . '</strong>';
		}
		else
		{
			$title = '<strong>Products</strong>';
		}

		$dir = 'asc';
		$icon = '<i class="fa fa-sort"></i>';
		$sortedParam = 'rel';
		
		if ($params['sort'])
		{
		    $sortedParam         = str_replace(array('asc', 'desc', ' '), '', rawurldecode($params['sort']));
		    $sortedDir           = str_replace(array('price', 'merchant', ' '), '', rawurldecode($params['sort']));
		    ${dir . $sortedParam}  = $sortedDir == 'asc' ? 'desc' : 'asc';
            ${icon . $sortedParam} = '<i class="fa fa-sort-' . ($sortedParam == 'price' ? 'numeric-' : 'alpha-') . $sortedDir . '"></i>';
            $sortUrl             = rtrim(str_replace('/sort/' . $params['sort'], '', $currentUrl), '/');
		}

		$sortArray = array(
			'Relevance'			                                  => 'rel',
			'Price ' . ($iconprice ? $iconprice : $icon)          => 'price ' . ($dirprice ? $dirprice : $dir),
			'Merchant ' . ($iconmerchant ? $iconmerchant : $icon) => 'merchant ' . ($dirmerchant ? $dirmerchant : $dir) 
		);

		if ($params['dR'])
		{
		    $priceSlider = explode(',', rawurldecode($params['dR']));
		    $pickedFacets[] = '<a href="' . str_replace('/dR/' . $params['dR'], '', $url) . '">$' . implode(' - $', $priceSlider) . ' <l style="font-size:12px;">&#215;</l></a>';
		}
		if ($params['pR'])
		{
		    $percentSlider = explode(',', rawurldecode($params['pR']));
		    $pickedFacets[] = '<a href="' . str_replace('/pR/' . $params['pR'], '', $url) . '">' . implode('% - ', $percentSlider) . '% Off <l style="font-size:12px;">&#215;</l></a>';
		}	

		if ($query || $filters['brand']['appliedFilters'] || $filters['merchant']['appliedFilters'] || $filters['category']['allFilters'] ||$filters['category']['appliedFilters'] || $filters['merchant']['allFilters'] || $filters['brand']['allFilters'])
		{		
			$settings = array(
				'page'			   => $params['page'],
				'query'            => $query,
				'sortBy'	       => $params['sort'] != 'rel' ? rawurldecode($params['sort']) : '',
				'filterBrand'      => ($filters['brand']['appliedFilters'] ? implode('|', $filters['brand']['appliedFilters']) : ($filters['brand']['allFilters'] ? implode('|', $filters['brand']['allFilters']) : '')),
				'filterMerchant'   => ($filters['merchant']['appliedFilters'] ? implode('|', $filters['merchant']['appliedFilters']) : ($filters['merchant']['allFilters'] ? implode('|', $filters['merchant']['allFilters']) : '')),
				'filterCategory'   => ($filters['category']['appliedFilters'] ? implode('|', $filters['category']['appliedFilters']) : ($filters['category']['allFilters'] ? implode('|', $filters['category']['allFilters']) : '')),
				'filterPrice'	   => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',				
				'limit'			   => $options['Pagination_Limit'],
				'imageSize'		   => $imageSize,
				'curlCall'		   => 'multi-product'
			);	

			$curlUrls['results'] = $this->searchModel->apiCall($settings, $fetch);			
		}

		if ($options['Enable_Facets'] && ($query || $filters['brand']['appliedFilters'] || $filters['merchant']['appliedFilters'] || $filters['category']['appliedFilters']))
		{
			$merchantFacetSettings = array(
				'query'            => $query,
				'enableFacets'     => 'merchant',
				'limit'			   => 1,
				'filterMerchant'   => $filters['merchant']['allFilters'],				
				'filterCategory'   => $filters['category']['appliedFilters'],
				'filterBrand'	   => ($filters['brand']['appliedFilters'] ? implode('|', $filters['brand']['appliedFilters']) : ($filters['brand']['allFilters'] ? implode('|', $filters['brand']['allFilters']) : '')),
				'filterPrice'	   => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
				'enableFullData'   => 'FALSE',
			    'imageSize'        => '75x75',
			);	

			$curlUrls['merchants'] = $this->searchModel->apiCall($merchantFacetSettings, $fetch);	

			$brandFacetSettings = array(
				'query'            => $query,
				'enableFacets'     => 'brand',
				'limit'			   => 1,
				'filterMerchant'   => ($filters['merchant']['appliedFilters'] ? implode('|', $filters['merchant']['appliedFilters']) : ($filters['merchant']['allFilters'] ? implode('|', $filters['merchant']['allFilters']) : '')),				
				'filterCategory'   => $filters['category']['appliedFilters'],
				'filterBrand'	   => $filters['brand']['allFilters'],
				'filterPrice'	   => $params['dR'] ? rawurldecode($params['dR']) : '',
				'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
			    'imageSize'        => '75x75',
				'enableFullData'   => 'FALSE'
			);	
			
			$curlUrls['brands'] = $this->searchModel->apiCall($brandFacetSettings, $fetch);
			
		    $settingsHigh = array(
		        'page'			   => 1,
		        'query'            => $query,
		        'sortBy'	       => 'price desc',
		        'filterBrand'      => ($filters['brand']['appliedFilters'] ? implode('|', $filters['brand']['appliedFilters']) : ($filters['brand']['allFilters'] ? implode('|', $filters['brand']['allFilters']) : '')),
		        'filterMerchant'   => ($filters['merchant']['appliedFilters'] ? implode('|', $filters['merchant']['appliedFilters']) : ($filters['merchant']['allFilters'] ? implode('|', $filters['merchant']['allFilters']) : '')),
		        'filterCategory'   => implode('|', $filters['category']['appliedFilters']),
		        'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
		        'limit'			   => 1,
		        'enableFullData'   => 'FALSE',
			    'imageSize'        => '75x75',
		    );
		
		    $settingsLow = array(
		        'page'			   => 1,
		        'query'            => $query,
		        'sortBy'	       => 'price asc',
		        'filterBrand'      => ($filters['brand']['appliedFilters'] ? implode('|', $filters['brand']['appliedFilters']) : ($filters['brand']['allFilters'] ? implode('|', $filters['brand']['allFilters']) : '')),
		        'filterMerchant'   => ($filters['merchant']['appliedFilters'] ? implode('|', $filters['merchant']['appliedFilters']) : ($filters['merchant']['allFilters'] ? implode('|', $filters['merchant']['allFilters']) : '')),
		        'filterCategory'   => implode('|', $filters['category']['appliedFilters']),
		        'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : '',
		        'limit'			   => 1,
		        'enableFullData'   => 'FALSE',
			    'imageSize'        => '75x75',
		    );
		
		    $curlUrls['highRange'] = $this->searchModel->apiCall($settingsHigh, $fetch);
		    $curlUrls['lowRange'] = $this->searchModel->apiCall($settingsLow, $fetch);			
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
			
			$brands = array_merge($brands, $filterArray['all']['brand']);
			$merchants = array_merge($merchants, $filterArray['all']['merchant']);
			//sort($filterArray['category']);

			$mainFilters = array('brand' => $brands, 'merchant' => $merchants );
			
			$highRange = $everything['highRange']['data'][0]['price'];
			$lowRange = $everything['lowRange']['data'][0]['price'];
		}

		if ($results = $everything['results']['data'])
		{	
		    
			$totalFound = (!$trend ? $everything['results']['totalRecordsFound'] : 0);	
			$totalAvailable = $everything['results']['totalRecordsAvailable'];
		}
		else
		{		    
		    if ($query && ($filters['brand']['appliedFilters'] || $filters['merchant']['appliedFilters'] || $filters['category']['appliedFilters']))
		    {
		        //$data['url'] = str_replace(array('/pR/' . $data['params']['pR'], '/dR/' . $data['params']['dR'], '/page/' . $data['params']['page'], '/brand/' . $data['params']['brand'], '/merchant/' . $data['params']['merchant']), '', $data['url']);
		        //unset($data['params']['merchant'], $data['filters']['merchant'], $data['params']['brand'], $data['filters']['brand'], $data['params']['category'], $data['filters']['category'], $data['params']['page']);

		        $data = $this->searchModel->storeSearch(true);
		        

		    
		        $this->productAction($data, $homeUrl, $type, $searchPage, $options);
		        return;
		    }

			if (!$results)
			{
				$settings = array(
					'imageSize'	=> $imageSize
				);

				$allData   = $this->searchModel->trendsApiCall($settings, $fetch, array_map('trim', explode(',', $options['No_Results_Categories'])));
				$results   = $allData['data'];	

				$noResults = true;
				$trend     = 'Popular Products';
				header( $_SERVER['SERVER_PROTOCOL'] . " 404 Not Found", true, 404 );
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
		$visitButton = 'Visit Store';	
		$fetch       = 'fetchProducts';
		$brand   	 = true;
		$filter  	 = 'filterCatalogId';
		$group   	 = 'productId';			
		$urltype 	 = 'prod';				
		$expiration  = PROSPER_CACHE_PRODS;
	
				
		$matchingUrl = $homeUrl . '/' . ($options['Base_URL'] ? $options['Base_URL'] : 'products') . '/type/' . $urltype;
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
			header('Location: ' . $matchingUrl . '/query/' . get_query_var('keyword'));
			exit;
		}

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

		/*
		/  SIMILAR
		*/
		$settings4 = array(
			'limit'              => 6,
			'query'		         => $settings['query'] = $mainRecord[0]['keyword'],
			'enableFullData'     => 'FALSE',
			'imageSize'		     => '250x250',
		    'relevancyThreshold' => $settings['relevancyThreshold'] = '1'			    
		);
		
		$curlUrls['similar'] = $this->searchModel->apiCall($settings4, $fetch);

		$settings['curlCall'] = 'multi-prodPage';

		$allData = $this->searchModel->multiCurlCall($curlUrls, $expiration, $settings);

		$groupedResult = $allData['groupedResult']['data'];
		$results 	   = $allData['results']['data'];
		$similar 	   = $allData['similar']['data'];		
		
		require_once($productPage);	
	}
}
 
$prosperProductSearch = new ProsperSearchController;