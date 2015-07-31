<?php
require_once(PROSPER_MODEL . '/Base.php');
/**
 * Search Model
 *
 * @package Model
 */
class Model_Search extends Model_Base
{
	public $_options;
			
	public function init()
	{
		$this->_options = $this->getOptions();			
	}
			
	public function prosperShopVars()
	{  
	    $keys = array(
	        'api' => $this->_options['Api_Key'],
	        'img' => PROSPER_IMG,
	        'imk' => $this->_options['ImageCname'],
	        'cmk' => $this->_options['ClickCname'],
	        'vbt' => $this->_options['VisitStoreButton']
	    );
	     
	    echo '<script type="text/javascript">var _prosperShop = ' . json_encode($keys) . '</script>';
	}
	
	public function qTagsStore()
	{	
		$id 	 = 'prosperStore';
		$display = 'ProsperShop';
		$arg1 	 = '[prosper_store]';
		$arg2 	 = '[/prosper_store]';		
	
		$this->qTagsProsper($id, $display, $arg1, $arg2);
	}

	public function qTagsSearch()
	{	
		$id 	 = 'prosperSearch';
		$display = 'Prosper Search Bar';
		$arg1 	 = '[prosper_search w="WIDTH" css="ADDITIONAL CSS"]';
		$arg2 	 = '[/prosper_search]';		
	
		$this->qTagsProsper($id, $display, $arg1, $arg2);
	}	
			
	public function getUrlParams()
	{
	    $params = explode('/', get_query_var('queryParams'));

		$sendParams = array();
		foreach ($params as $k => $p)
		{
			//if the number is even, grab the next index value
			if (!($k & 1))
			{
				$sendParams[$p] = $params[$k + 1];
			}
		}

		return $sendParams;
	}
	
	public function getPostVars($postArray, $data)
	{
		$postArray = array_filter($postArray);		
		$newUrl = $data['url'];
		if (preg_match('/\/\?gclid=.+/i', $newUrl))
		{
			$newUrl = preg_replace('/\/\?gclid=.+/i', '', $newUrl);
		}
		
		$newUrl = str_replace(array(
    		    '/pR/' . $data['params']['pR'],
    		    '/dR/' . $data['params']['dR'],
    		    '/city/' . $data['params']['city'],
    		    '/state/' . $data['params']['state'],
    		    '/zip/' . $data['params']['zip'],
    		    '/page/' . $data['params']['page'],
    		    '/celebrity/' . $data['params']['celebrity'],
    		    '/sort/' . $data['params']['sort'],
    		    '/celebQuery/' . $data['params']['celebQuery'],
    		    '/cid/' . $data['params']['cid'],
    		    '/type/' . $data['params']['type']
    		), '', $newUrl
		);
		
		while (current($postArray)) 
		{ 
			$newUrl = str_replace('/' . key($postArray) . '/' . $data['params'][key($postArray)], '', $newUrl);
			$newUrl = $newUrl . '/' . key($postArray) . '/' . htmlentities(rawurlencode(current($postArray)));
			next($postArray);
		}
		
		header('Location: ' . $newUrl);
		exit;
	}
	
	public function getBrands($brand = null)
	{
        $brands = array();
	    
		if ($brand)
		{
			$brands = explode('~', str_replace(',SL,', '/', $brand));
			$brands = array_combine($brands, $brands);
		}
		
		return array('appliedFilters' => $brands);
	}
	
	public function getMerchants($merchant = null)
	{
		$filterMerchants = array();
	    $merchants = array();

		if ($merchant)		
		{
			$merchants = explode('~', str_replace(',SL,', '/', $merchant));
			$merchants = array_combine($merchants, $merchants);
		}

		if ($this->_options['PositiveMerchant'])
		{
		    $this->_options['PositiveMerchant'] = rtrim($this->_options['PositiveMerchant'], '|');
			$filterMerchants = array_map('trim', explode('|', $this->_options['PositiveMerchant']));
		}
		
		if ($this->_options['NegativeMerchant'])
		{
		    $this->_options['NegativeMerchant'] = '!' . str_replace('|', '|!', rtrim($this->_options['NegativeMerchant'], '|'));
			$filterMerchants = array_merge($filterMerchants, array_map('trim', explode('|', $this->_options['NegativeMerchant'])));
		}

		return array('appliedFilters' => $merchants, 'allFilters' => $filterMerchants);
	}	
	
	public function getCategories($category = null)
	{
		$filterCategory = array();
		$categories = array();

		if ($category)		
		{
		    $categories = explode('~', '*' . str_replace(',SL,', '/', $category). '*');
		    $categories = array_combine($categories, $categories);
		}

		if ($this->_options['ProsperCategories'])
		{
		    $this->_options['ProsperCategories'] = rtrim(str_replace(array('_', '|'), array(' ', '*|'), $this->_options['ProsperCategories']), '|');
		    $filterCategory = array_map('trim', explode('|', $this->_options['ProsperCategories']));
		}

		return array('appliedFilters' => $categories, 'allFilters' => $filterCategory);
	}	
	
	public function buildFacets($facets, $params, $filters, $url)
	{
		if (preg_match('/\/\?gclid=.+/i', $url))
		{
			$url = preg_replace('/\/\?gclid=.+/i', '', $url);
		}

		$facetsNew = array();
		$facetsPicked = array();
		foreach ($facets as $i => $facetArray)
		{	
			if ($i === 'zipCode')
			{
				$i = 'zip';
			}

			foreach ($facetArray as $facet)
			{			
				if ($filters[$i]['appliedFilters'][$facet['value']])
				{
					if (count($filters[$i]['appliedFilters']) > 1)
					{
						$newFilters = $filters[$i]['appliedFilters'];
						unset($newFilters[$facet['value']]);
						$facetsNew[$i][$facet['value']] = '<li class="prosperActive"><a href="' . (str_replace(array('/cid/' . $params['cid'], '/page/' . $params['page'], '/' . $i . '/' . $params[$i]),  '', $url) . '/' . $i . '/' . rawurlencode(implode('~', $newFilters))) . '"' . ($this->_options['noFollowFacets'] ? ' rel="nofollow,nolink"' : ' rel="nolink"') . '><i class="fa fa-times"></i><span>' . $facet['value'] . '</span></a></li>';						
						$facetsPicked[] = '<span class="activeFilters"><a href="' . (str_replace(array('/page/' . $params['page'], '/' . $i . '/' . $params[$i]),  '', $url) . '/' . $i . '/' . rawurlencode(implode('~', $newFilters))) . '"' . ($this->_options['noFollowFacets'] ? ' rel="nofollow,nolink"' : ' rel="nolink"') . '><i style="padding-right:3px;" class="fa fa-times"></i>' . $facet['value'] . '</a></span>';
					}
					else
					{
						$facetsNew[$i][$facet['value']] = '<li class="prosperActive"><a href="' . str_replace(array('/cid/' . $params['cid'], '/page/' . $params['page'], '/' . $i . '/' . $params[$i]),  '', $url) . '"' . ($this->_options['noFollowFacets'] ? ' rel="nofollow,nolink"' : ' rel="nolink"') . '><i class="fa fa-times"></i>' . $facet['value'] . '</a></li>';						
						$facetsPicked[] = '<span class="activeFilters"><a href="' . str_replace(array('/page/' . $params['page'], '/' . $i . '/' . $params[$i]),  '', $url) . '"' . ($this->_options['noFollowFacets'] ? ' rel="nofollow,nolink"' : ' rel="nolink"') .'> <i style="padding-right:3px;" class="fa fa-times"></i>' . $facet['value'] . '</a></span>';
					}
				}
				elseif ($facet['value'])
				{
					$facetsNew[$i][$facet['value']] = '<li class="prosperFilter"><a href="' . (str_replace(array('/cid/' . $params['cid'], '/page/' . $params['page'], '/' . $i . '/' . $params[$i]),  '', $url) . '/' . $i . '/' . rawurlencode(str_replace('/', ',SL,', $facet['value']))) .($params[$i] ? '~' .  $params[$i] : '') . '"' . ($this->_options['noFollowFacets'] ? ' rel="nofollow,nolink"' : ' rel="nolink"') . '><i class="fa fa-times"></i><span>' . $facet['value'] . '</span></a></li>';
				}
			}
		}

		$facetFull = array('picked' => $facetsPicked, 'all' => $facetsNew);

		return $facetFull;
	}
	
	public function getSearchPhtml()
	{		
		$phtml[0] = PROSPER_VIEW . '/prospersearch/themes/Default/product.php';
		$phtml[1] = PROSPER_VIEW . '/prospersearch/productPage.php';
				
		// Product Search CSS for results and search
		if ($this->_options['Set_Theme'] != 'Default')
		{
			$dir = PROSPER_THEME . '/' . $this->_options['Set_Theme'];
			if (file_exists($dir))
			{			
				$newTheme = glob($dir . "/*.php");
			}
			else
			{
				$newTheme = glob(PROSPER_VIEW . '/prospersearch/themes/' . $this->_options['Set_Theme'] . "/*.php");
			}

			foreach ($newTheme as $theme)
			{
				if (preg_match('/product.php/i', $theme))
				{
					$phtml[0] = $theme;
				}
				elseif (preg_match('/productPage.php/i', $theme))
				{
					$phtml[1] = $theme;
				}				
			}
			
			if ($this->_options['Set_Theme'] == 'SingleFile')
			{				
				wp_register_script('Beta', '', array('jquery', 'json2', 'jquery-ui-widget', 'jquery-ui-dialog', 'jquery-ui-tooltip', 'jquery-ui-autocomplete') );
				wp_enqueue_script( 'Beta' );	
				wp_enqueue_style('BetaCSS', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css');
			}
		}
		else
		{
			wp_register_script('productPhp', PROSPER_JS . '/productPHP.js', array('jquery', 'json2'), $this->_version, 1);
			wp_enqueue_script( 'productPhp');
		}

		return $phtml;
	}
	
	
	public function prosperPagination($totalAvailable = '', $paged, $range = 8)
	{
		$limit = $this->_options['Pagination_Limit'] ? $this->_options['Pagination_Limit'] : 10;
		$pages = round($totalAvailable / $limit, 0);
		if(empty($paged)) $paged = 1;

		if($pages == '')
		{
			$pages = $wp_query->max_num_pages;
			if(!$pages)
			{
				$pages = 1;
			}
		}

		if (is_front_page())
		{
			$baseUrl = $this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products';
			$newPage = home_url('/') . $baseUrl . '/page/'; 
		}

		if(1 != $pages)
		{
			echo '<div class="prosperPagination"><span>Page ' . $paged . ' of ' . $pages . '</span>';
			if($paged > 2 && $paged <= $pages) echo '<a href="' . (!$newPage ? get_pagenum_link(1) : $newPage . 1) . '">&laquo; First</a>';
			if($paged > 1) echo '<a href="' . (!$newPage ? get_pagenum_link($paged - 1) : $newPage . ($paged - 1)) . '">&lsaquo; Previous</a>';

			for ($i = $paged; $i <= $pages; $i++)
			{
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
				{
					echo ($paged == $i)? '<span class="current">' . $i . '</span>' : '<a href="' . (!$newPage ? get_pagenum_link($i) : $newPage . $i) . '" class="inactive">' . $i . '</a>';
				}
			}
				
			if ($paged < $pages) echo '<a href="' . (!$newPage ? get_pagenum_link($paged + 1) : $newPage . ($paged + 1)) . '">Next &rsaquo;</a>';
			if ($paged < $pages && $paged < $pages-1) echo '<a href="' . (!$newPage ? get_pagenum_link($pages) : $newPage . $pages) . '">Last &raquo;</a>';
			echo '</div>';
		}
	}
	
	public function searchShortcode($atts, $content = null)
	{
		$options = $this->_options;
		
		if (!$options['PSAct'])
		{
		    return;
		}
		
		$pieces = $this->shortCodeExtract($atts, $this->_shortcode);		

		if(get_query_var('queryParams'))
		{
			$params = $this->getUrlParams();
			$query = $params['query'];
		}		

		$base = $options['Base_URL'] ? $options['Base_URL'] : 'products';
		$url = home_url('/') . $base;

		if (!is_page($base))
		{
			$action = $base;
		}
		
		$queryString = '';
		if ($query = (trim($_POST['q'] ? $_POST['q'] : $options['Starting_Query'])))
		{
			$queryString = '/query/' . rawurlencode($query);
		}
		
		if (is_page($base) && isset($_POST['q']))
		{
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$prodSubmit = preg_replace('/\/$/', '', $url);
			$newQuery = str_replace(array('/query/' . $query, '/query/' . rawurlencode($query)), '', $prodSubmit);
			header('Location: ' . $newQuery . '/type/' . ($pieces['sf'] ? $pieces['sf'] : 'prod') . $queryString);
			exit;
		}
		elseif (isset($_POST['q']))
		{
			header('Location: ' . $url . '/type/' . ($pieces['sf'] ? $pieces['sf'] : 'prod') . $queryString);
			exit;
		}		

		ob_start();
		require_once(PROSPER_VIEW . '/prospersearch/searchShort.php');
		$search = ob_get_clean();
		return $search;
	}
	
	public function ogMeta()
	{	  	    
	    $currentId = get_the_ID();
	    $storeId = (int) get_option('prosperent_store_pageId');
	    
	    if ($currentId === $storeId)
	    {
    		$prosperPage = get_query_var('prosperPage');
    		$expiration  = PROSPER_CACHE_PRODS;
    		$fetch       = 'fetchProducts';
    		
            $imageSize = '500x500';
    
    		$cid = $params['cid'] ? $params['cid'] : (get_query_var('cid') ? get_query_var('cid') : '');
    		if ($cid)
    		{
        		$settings = array(
        			'limit' 	      => 1,
        			'imageSize'       => $imageSize,
        			'curlCall'	      => 'single-productPage-' . $prosperPage,
        		    'filterCatalogId' => $cid
        		);
    		}
    		else
    		{		    
    		    $data    = $this->storeSearch();
    		    $filters = $data['filters'];
    		    $params  = $data['params'];
    		    $query   = $params['query'] ? $params['query'] : ($this->_options['Starting_Query'] ? $this->_options['Starting_Query'] : '');
    		    
    		    $settings = array(
        		    'limit'			   => $this->_options['Pagination_Limit'],
        		    'imageSize'		   => $imageSize,
        		    'curlCall'		   => 'multi-product',
        		    'page'			   => $params['page'],
        		    'query'            => $query,
        		    'sortBy'	       => $params['sort'] != 'rel' ? rawurldecode($params['sort']) : '',
        		    'filterBrand'      => ($filters['brand']['appliedFilters'] ? implode('|', $filters['brand']['appliedFilters']) : ($filters['brand']['allFilters'] ? implode('|', $filters['brand']['allFilters']) : '')),
        		    'filterMerchant'   => ($filters['merchant']['appliedFilters'] ? implode('|', $filters['merchant']['appliedFilters']) : ''),
        		    'filterMerchantId' => ($filters['merchant']['appliedFilters'] ? '' : ($filters['merchant']['allFilters'] ? implode('|', $filters['merchant']['allFilters']) : '')),
        		    'filterCategory'   => ($filters['category']['appliedFilters'] ? implode('|', $filters['category']['appliedFilters']) : ($filters['category']['allFilters'] ? implode('|', $filters['category']['allFilters']) : '')),
        		    'filterPrice'	   => $params['dR'] ? rawurldecode($params['dR']) : '',
        		    'filterPercentOff' => $params['pR'] ? rawurldecode($params['pR']) : ''
    		    );
    		}
    
    		$curlUrl = $this->apiCall($settings, $fetch);
    
    		$allData = $this->singleCurlCall($curlUrl, 0);
    		$record = $allData['data'];		    
    
    		if ($record)
    		{ 
        		$priceSale = $record[0]['priceSale'] ? $record[0]['priceSale'] : $record[0]['price_sale'];
        		// Open Graph: FaceBook/Pintrest
        		echo '<meta property="og:url" content="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" />';
        		echo '<meta property="og:site_name" content="' . get_bloginfo('name') . '" />';
        		echo '<meta property="og:type" content="product" />';
        		echo '<meta property="og:image" content="' . $record[0]['image_url'] . '" />';
        		echo '<meta property="og:image:width" content="' . ($this->_options['OG_Image'] ? $this->_options['OG_Image'] : 300) . '" />';
        		echo '<meta property="og:image:height" content="' . ($this->_options['OG_Image'] ? $this->_options['OG_Image'] : 300) . '" />';
        		echo '<meta property="og:description" content="' . htmlentities($record[0]['description']) . '" />';
        		echo '<meta property="product:availability" content="instock" />';
        		echo '<meta property="product:category" content="' . htmlentities($record[0]['category']) . '" />';
        		echo '<meta property="product:brand" content="' . htmlentities($record[0]['brand']) . '" />';
        		echo '<meta property="product:retailer_title" content="' . htmlentities($record[0]['merchant']) . '" />';
        		echo '<meta property="og:title" content="' . htmlentities(strip_tags($record[0]['keyword'])) . '" />';
        		echo '<meta property="product:price:amount" content="' . $record[0]['price'] . '" />';
                echo '<meta property="product:price:currency" content="USD" />';
                echo $priceSale ? '<meta property="product:sale_price:amount" content="' . $priceSale . '" />' : '';
                echo $priceSale ? '<meta property="product:sale_price:currency" content="USD" />' : '';
        
        		// Twitter Cards
        		if ($this->_options['Twitter_Site'])
        		{
                    if(!preg_match('/^@/', $this->_options['Twitter_Site']))
                    {
                        $this->_options['Twitter_Site'] = '@' . $this->_options['Twitter_Site'];
                    }
                    if(!preg_match('/^@/', $this->_options['Twitter_Creator']))
                    {
                        $this->_options['Twitter_Creator'] = '@' . $this->_options['Twitter_Creator'];
                    }
            		echo '<meta name="twitter:card" content="summary_large_image">';
            		echo '<meta name="twitter:site" content="' . $this->_options['Twitter_Site'] . '" />';
            		echo '<meta name="twitter:creator" content="' . $this->_options['Twitter_Creator'] . '"/>';
            		echo '<meta name="twitter:image" content="' . $record[0]['image_url'] . '" />';
            		echo '<meta name="twitter:data1" content="' . ((!$priceSale || $record[0]['price'] <= $priceSale) ? $record[0]['price'] : $priceSale) . '">';
            		echo '<meta name="twitter:label1" content="Price">';
            		echo '<meta name="twitter:data2" content="' . $record[0]['brand'] . '">';
            		echo '<meta name="twitter:label2" content="Brand">';
            		echo '<meta name="twitter:description" content="' . htmlentities($record[0]['description']) . '" />';
            		echo '<meta name="twitter:title" content="' . htmlentities(strip_tags($record[0]['keyword'])) . '" />';
        		}
    		}
    		else
    		{
    		    echo '<meta name="robots" content="noindex,nofollow">';
    		}
	    }
	}	
		
	public function storeChecker()
	{
		$options = get_option('prosper_advanced');
		
		$currentId = get_the_ID();
		$storeId = get_option('prosperent_store_pageId');

		if ($currentId != $storeId && !is_front_page())
		{
		    wp_delete_post($storeId);
		    delete_option("prosperent_store_page_title");
		    delete_option("prosperent_store_page_name");
		    delete_option("prosperent_store_page_id");		    
		    delete_option('prosperent_store_pageId');
		    
		    add_option('prosperent_store_page_title', get_post()->post_title);
		    add_option('prosperent_store_page_name', get_post()->post_name);
		    add_option('prosperent_store_pageId', $currentId);
		    
		    $options['Base_URL'] = get_post()->post_name;
		    
		    update_option('prosper_advanced', $options);
		    $this->prosperReroutes();
		}
	}
	
	public function prosperTitle($title, $sep, $seplocation)
	{ 
		if ( is_feed() )
		{
			return $title;
		}

		if(get_query_var('queryParams'))
		{
			$params = $this->getUrlParams();
		}

		$sep      	 = ' ' . (!$this->_options['Title_Sep'] ? !$sep ? '|' : trim($sep) : trim($this->_options['Title_Sep'])) . ' ';
		$page     	 = $this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products';
		$page_num 	 = $params['page'] ? ' Page ' . $params['page'] : '';
		$pagename 	 = get_the_title();
		$blogname 	 = get_bloginfo();
		$brands      = explode('|', ucwords(rawurldecode($params['brand'])));
		$brand 	  	 = $brands[0];
		$merchants   = explode('|', ucwords(rawurldecode($params['merchant'])));
		$merchant 	 = $merchants[0];
		$type 	  	 = $params['type'];
		$query 	  	 = $params['query'] ? $params['query'] : (($this->_options['Starting_Query'] && !$brand && !$merchant) ? $this->_options['Starting_Query'] : '');
		$city 	  	 = ucwords(rawurldecode($params['city']));
		$zip 		 = ucwords(rawurldecode($params['zip']));
		$celeb 		 = ucwords(rawurldecode($params['celebrity']));

		$query = ucwords(rawurldecode(str_replace('+', ' ', $query)));
		
		if (get_query_var('cid'))
		{ 
			$query = preg_replace('/\(.+\)/i', '', rawurldecode(get_query_var('keyword')));
			$query = str_replace(',SL,', '/', $query);
			$title = ucwords($query) . $sep . $title;
		}
		elseif (is_page($page))
		{
			switch ( $this->_options['Title_Structure'] )
			{
				case '0':
					$title =  $title;
					break;
				case '1':
					$title =  $blogname . $sep . $pagename . $page_num . (($query || $brand || $merchant) ? $sep : '') . ($query ? $query : '') . ($query && $brand ? ' &raquo; ' : '') . ($brand ? $brand : '') . (($query && $merchant || $merchant && $brand) ? ' &raquo; ' : '') . ($merchant ? $merchant : '');
					break;
				case '2':
					$title = ($query ? $query : '') . ($query && $brand ? ' &raquo; ' : '') . ($brand ? $brand : '') . (($query && $merchant || $merchant && $brand) ? ' &raquo; ' : '') . ($merchant ? $merchant : '') . (($query || $brand || $merchant) ? $sep : '') . $pagename . $page_num . $sep . $blogname;
					break;
				case '3':
					$title =  !$query ? $title : ($query ? $query : '') . ($brand ?  ' &raquo; ' . $brand : '') . ($merchant ? ' &raquo; ' . $merchant : '') . $page_num;
					break;
				case '4':
					$title =  $title;
					break;
			}
		}
		
		return $title;
	}	
	
	public function storeSearch($related = false)
	{		
	    $base = $this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products';

		if(get_query_var('queryParams'))
		{
			$params = str_replace('%7C', '~', $this->getUrlParams());	
		}

		$url = home_url() . str_replace('%7C', '~', $_SERVER['REQUEST_URI']);
		$url = preg_replace('/\/$/', '', $url);

		if(is_front_page())
		{			
			$url = home_url('/') . $base;
		}
		
		if ($related)
		{		
		    $prosperLastValue = end($params);
		    $prosperLastKey = key($params);
		    unset($params[$prosperLastKey]);
		    $newParams = implode('/', $params);
		    set_query_var('queryParams', $newParams);
		    $url = home_url('/') . $base . ($newParams ? '/' . $newParams : '');
		}
		
		$brand    	  = isset($params['brand']) ? str_replace('|', '~', rawurldecode(stripslashes($params['brand']))) : '';
		$merchant 	  = isset($params['merchant']) ? str_replace('|', '~', rawurldecode(stripslashes($params['merchant']))) : '';		
		$category 	  = isset($params['category']) ? str_replace('|', '~', rawurldecode(stripslashes($params['category']))) : '';	

		return array(
			'filters'	   => array(
				'brand' 	=> $this->getBrands($brand),
				'merchant'  => $this->getMerchants($merchant),
				'category'	=> $this->getCategories($category)
			),
			'params'	   => $params,
			'url'		   => $url,
			'merchantUrl'  => $merchantUrl,
		    'related'      => $related
		);
	}
}