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

		while (current($postArray)) 
		{ 
			if (key($postArray) == 'type' && $data['params']['type'] != current($postArray))
			{
				$newUrl = str_replace(array('/pR/' . $data['params']['pR'], '/dR/' . $data['params']['dR'], '/city/' . $data['params']['city'], '/state/' . $data['params']['state'], '/zip/' . $data['params']['zip'], '/page/' . $data['params']['page'], '/celebrity/' . $data['params']['celebrity'], '/sort/' . $data['params']['sort'], '/celebQuery/' . $data['params']['celebQuery'], '/cid/' . $data['params']['cid']), '', $newUrl);

			}	
			elseif ($data['params']['type'] == $postArray['type'] && $data['params']['query'] != current($postArray) && key($postArray) == 'query')
			{
				$newUrl = str_replace(array('/pR/' . $data['params']['pR'], '/dR/' . $data['params']['dR'], '/city/' . $data['params']['city'], '/state/' . $data['params']['state'], '/zip/' . $data['params']['zip'], '/page/' . $data['params']['page'], '/brand/' . $data['params']['brand'], '/merchant/' . $data['params']['merchant'], '/cid/' . $data['params']['cid']), '', $newUrl);
			}
			elseif ($data['params']['type'] == $postArray['type'] && $data['params']['state'] != current($postArray) && key($postArray) == 'state')
			{
				$newUrl = str_replace(array('/pR/' . $data['params']['pR'], '/dR/' . $data['params']['dR'], '/city/' . $data['params']['city'], '/zip/' . $data['params']['zip'], '/page/' . $data['params']['page'], '/brand/' . $data['params']['brand'], '/merchant/' . $data['params']['merchant'], '/cid/' . $data['params']['cid']), '', $newUrl);
			}
			elseif ($data['params']['type'] == $postArray['type'] && $data['params']['celebrity'] != current($postArray) && key($postArray) == 'celebrity')
			{
				$newUrl = str_replace(array('/pR/' . $data['params']['pR'], '/dR/' . $data['params']['dR'], '/city/' . $data['params']['city'], '/zip/' . $data['params']['zip'], '/page/' . $data['params']['page'], '/brand/' . $data['params']['brand'], '/merchant/' . $data['params']['merchant'], '/query/' . $data['params']['query'], '/cid/' . $data['params']['cid']), '', $newUrl);
			}
		
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
			//$filterMerchants = $merchants;
			$merchants = array_combine($merchants, $merchants);
		}

		if ($this->_options['PositiveMerchant'])
		{
			$plusMerchants = explode(',', $this->_options['PositiveMerchant']);
			foreach ($plusMerchants as $positive)
			{
				array_push($filterMerchants, trim($positive));
			}
		}
		
		if ($this->_options['NegativeMerchant'])
		{
			$minusMerchants = explode(',', $this->_options['NegativeMerchant']);

			foreach ($minusMerchants as $negative)
			{
				array_push($filterMerchants, '!' . trim($negative));
			}
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
		    $positiveCategories = array_map('stripslashes', explode(',', $this->_options['ProsperCategories']));
		    foreach ($positiveCategories as $category)
		    {
		        array_push($filterCategory, trim(str_replace('_', ' ', $category)));
		    }
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
			wp_register_script('productPhp', PROSPER_JS . '/productPHP.js', array('jquery', 'json2'));
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
	
	public function prosperPages($results, $pageNumber)
	{		
		// Pagination limit, can be changed
		$limit = $this->_options['Pagination_Limit'] ? $this->_options['Pagination_Limit'] : 10;
		if (count($results) > $limit)
		{
			$ceiling = ceil((count($results) + 1) / $limit);

			if ($pageNumber  < 1)
			{
				$pageNumber  = 1;
			}
			else if ($pageNumber  > $ceiling)
			{
				$pageNumber  = $ceiling;
			}

			$limitLower = ($pageNumber  - 1) * $limit;

			
			// Breaks the array into smaller chunks for each page depending on $limit
			$results = array_slice($results, $limitLower, $limit, true);
		}
		
		return $results;
	}
	
	public function sliderJs()
	{
		wp_register_script( 'rangeSlider', PROSPER_JS . '/prosperSlider.js', array('jquery', 'jquery-ui-slider', 'jquery-ui-dialog'), $this->_version, 1);
		wp_enqueue_script( 'rangeSlider' );	
		
		wp_register_style('jqueryUIcss', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.min.css');
		wp_enqueue_style('jqueryUIcss');		
	}		
	
	public function productStoreJs()
	{
		wp_register_script( 'productStoreJS', PROSPER_JS . '/productStore.js', array(), $this->_version, 1 );
		wp_enqueue_script( 'productStoreJS' );
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
	    $params = $this->getUrlParams();

		if(!preg_match('/^@/', $this->_options['Twitter_Site']))
		{
			$this->_options['Twitter_Site'] = '@' . $this->_options['Twitter_Site'];
		}
		if(!preg_match('/^@/', $this->_options['Twitter_Creator']))
		{
			$this->_options['Twitter_Creator'] = '@' . $this->_options['Twitter_Creator'];
		}

		$prosperPage = get_query_var('prosperPage');
		$expiration  = PROSPER_CACHE_PRODS;
		$fetch       = 'fetchProducts';
		$currency    = 'USD';
		$filter      = 'filterCatalogId';
		
		if ($this->_options['OG_Image'] > '250' || !$this->_options['OG_Image'])
		{
			$imageSize = '500x500';				
		}
		else
		{
			$imageSize = '250x250';
			if ($this->_options['OG_Image'] < '200')
			{
				$this->_options['OG_Image'] = 200;
			}
		}
		
		/*
		/  Prosperent API Query
		*/
		$settings = array(
			'limit' 	=> 1,			
		    'page'      => $params['page'],
			'imageSize' => $imageSize,
			'curlCall'	=> 'single-productPage-' . $prosperPage
		);
		
		$cid = $params['cid'] ? $params['cid'] : (get_query_var('cid') ? get_query_var('cid') : '');
		if (!$cid)
		{
		    $settings['query'] = $params['query'];
		}
		else
		{
		    $settings[$filter] = $cid;
		} 

		$curlUrl = $this->apiCall($settings, $fetch);
		$allData = $this->singleCurlCall($curlUrl, 0);
		$record = $allData['data'];		    
		    
		if ($record)
		{
    		$priceSale = $record[0]['priceSale'] ? $record[0]['priceSale'] : $record[0]['price_sale'];
    		// Open Graph: FaceBook
    		echo '<meta property="og:url" content="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" />';
    		echo '<meta property="og:site_name" content="' . get_bloginfo('name') . '" />';
    		echo '<meta property="og:type" content="website" />';
    		echo '<meta property="og:image" content="' . $record[0]['image_url'] . '" />';
    		echo '<meta property="og:image:width" content="' . ($this->_options['OG_Image'] ? $this->_options['OG_Image'] : 300) . '" />';
    		echo '<meta property="og:image:height" content="' . ($this->_options['OG_Image'] ? $this->_options['OG_Image'] : 300) . '" />';
    		echo '<meta property="og:description" content="' . $record[0]['description'] . '" />';
    		echo '<meta property="og:title" content="' . strip_tags($record[0]['keyword'] . ' - ' .  get_the_title($post) . ' - ' . get_bloginfo('name')) . '" />';
    
    		// Twitter Cards
    		echo '<meta name="twitter:card" content="record[0]">';
    		echo '<meta name="twitter:site" content="' . $this->_options['Twitter_Site'] . '" />';
    		echo '<meta name="twitter:creator" content="' . $this->_options['Twitter_Creator'] . '"/>';
    		echo '<meta name="twitter:image" content="' . $record[0]['image_url'] . '" />';
    		echo '<meta name="twitter:data1" content="' . ((!$priceSale || $record[0]['price'] <= $priceSale) ? $record[0]['price'] : $priceSale) . '">';
    		echo '<meta name="twitter:label1" content="Price">';
    		echo '<meta name="twitter:data2" content="' . $record[0]['brand'] . '">';
    		echo '<meta name="twitter:label2" content="Brand">';
    		echo '<meta name="twitter:description" content="' . $record[0]['description'] . '" />';
    		echo '<meta name="twitter:title" content="' . strip_tags($record[0]['keyword'] . ' - ' .  get_the_title($post) . ' - ' . get_bloginfo('name')) . '" />';
		}
	}	
		
	public function storeChecker()
	{
		$options = get_option('prosper_advanced');

		if (!isset($options['Manual_Base']) && (empty($options['Base_URL']) || $options['Base_URL'] != get_post()->post_name))
		{
			if (!is_front_page())
			{
				$options['Base_URL'] = get_post()->post_name;				
			}
			else
			{
				$options['Base_URL'] = 'products';		
			}
				
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
		$brand 	  	 = ucwords(rawurldecode($params['brand']));
		$merchant 	 = ucwords(rawurldecode($params['merchant']));
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
		if(get_query_var('queryParams'))
		{
			$params = str_replace('%7C', '~', $this->getUrlParams());	
		}
		
		$url = 'http://' . $_SERVER['HTTP_HOST'] . str_replace('%7C', '~', $_SERVER['REQUEST_URI']);
		$url = preg_replace('/\/$/', '', $url);

		if ($related)
		{
		    $prosperLastValue = end($params);
		    $prosperLastKey = key($params);
		    unset($params[$prosperLastKey]);
		    $url = str_replace('/' . $prosperLastKey . '/' . $prosperLastValue, $url); 
		}
		
		if(is_front_page())
		{
			$base = $this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products';
			$url = home_url('/') . $base;
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