<?php
require_once(PROSPER_MODEL . '/Base.php');
/**
 * Search Model
 *
 * @package Model
 */
class Model_Search extends Model_Base
{
	public $states = array(
		'alabama'		 =>'AL',
		'alaska'		 =>'AK',
		'arizona'		 =>'AZ',
		'arkansas'		 =>'AR',
		'california'	 =>'CA',
		'colorado'		 =>'CO',
		'connecticut'	 =>'CT',
		'DC'	 		 =>'DC',
		'delaware'		 =>'DE',
		'florida'		 =>'FL',
		'georgia'		 =>'GA',
		'hawaii'		 =>'HI',
		'idaho'		 	 =>'ID',
		'illinois'		 =>'IL',
		'indiana'		 =>'IN',
		'iowa'			 =>'IA',
		'kansas'		 =>'KS',
		'kentucky'		 =>'KY',
		'louisiana'		 =>'LA',
		'maine'			 =>'ME',
		'maryland'		 =>'MD',
		'massachusetts'	 =>'MA',
		'michigan'		 =>'MI',
		'minnesota'		 =>'MN',
		'mississippi'	 =>'MS',
		'missouri'		 =>'MO',
		'montana'		 =>'MT',
		'nebraska'		 =>'NE',
		'nevada'		 =>'NV',
		'new hampshire'	 =>'NH',
		'new jersey'	 =>'NJ',
		'new mexico'	 =>'NM',
		'new york'		 =>'NY',
		'north carolina' =>'NC',
		'north dakota'	 =>'ND',
		'ohio'			 =>'OH',
		'oklahoma'		 =>'OK',
		'oregon'		 =>'OR',
		'pennsylvania'	 =>'PA',
		'rhode island'   =>'RI',
		'south carolina' =>'SC',
		'south dakota'   =>'SD',
		'tennessee'      =>'TN',
		'texas'			 =>'TX',
		'utah'			 =>'UT',
		'vermont'		 =>'VT',
		'virginia'		 =>'VA',
		'washington'	 =>'WA',
		'west virginia'	 =>'WV',
		'wisconsin'		 =>'WI',
		'wyoming'		 =>'WY'
	);
	
	public $_options;
			
	public function init()
	{
		$this->_options = $this->getOptions();
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
	
	public function getStoreUrl($storeUrl)
	{
		$storeUrl = rawurldecode($storeUrl);
		$storeUrl = str_replace(',SL,', '/', $storeUrl);
		header('Location:http://prosperent.com/' . $storeUrl);
		exit;
	}
	
	public function getImageUrl($prosperImgUrl)
	{
		$prosperImgUrl = rawurldecode($prosperImgUrl);
		$prosperImgUrl = str_replace(',SL,', '/', $prosperImgUrl);
		header('Location:http://img1.prosperent.com/images/' . $prosperImgUrl);
		exit;
	}
	
	public function getPostVars($postArray, $data)
	{
		$postArray = array_filter($postArray);		
		$newUrl = str_replace(array('/' . key($postArray) . '/' . $data['params'][key($postArray)], '/page/' . $data['params']['page']), '', $data['url']);		
		header('Location: ' . $newUrl . '/' . key($postArray) . '/' . htmlentities(rawurlencode(reset($postArray))));
		exit;
	}
	
	public function getBrands($brand = null)
	{
		$filterBrands = array();

		if ($brand)
		{
			array_push($filterBrands, str_replace(',SL,', '/', $brand));
		}
		else
		{
			if($this->_options['Positive_Brand'])
			{
				$plusBrands = array_map('stripslashes', explode(',', $this->_options['Positive_Brand']));

				foreach ($plusBrands as $postive)
				{
					array_push($filterBrands, trim($postive));
				}
			}
			if($this->_options['Negative_Brand'])
			{
				$minusBrands = array_map('stripslashes', explode(',', $this->_options['Negative_Brand']));

				foreach ($minusBrands as $negative)
				{
					array_push($filterBrands, '!' . trim($negative));
				}
			}
		}
			
		return $filterBrands;
	}
	
	public function getMerchants($merchant = null)
	{
		$filterMerchants = array();

		if ($merchant)
		{
			array_push($filterMerchants, str_replace(',SL,', '/', $merchant));
		}
		else
		{
			if ($this->_options['Positive_Merchant'])
			{
				$plusMerchants = array_map('stripslashes', explode(',', $this->_options['Positive_Merchant']));

				foreach ($plusMerchants as $positive)
				{
					array_push($filterMerchants, trim($positive));
				}
			}
			if ($this->_options['Negative_Merchant'])
			{
				$minusMerchants = array_map('stripslashes', explode(',', $this->_options['Negative_Merchant']));

				foreach ($minusMerchants as $negative)
				{
					array_push($filterMerchants, '!' . trim($negative));
				}
			}
		}

		return $filterMerchants;
	}	
	
	public function buildFacets($facets, $params, $url)
	{
		$facetsNew = array();	
		foreach ($facets as $i => $facetArray)
		{		
			if ($i === 'zipCode')
			{
				$i = 'zip';
			}
			
			foreach ($facetArray as $facet)
			{							
				$facetsNew[$i][] = '<a href=' . str_replace('/page/' . $params['page'], '', $url) . '/' . $i . '/' . rawurlencode(str_replace('/', ',SL,', $facet['value'])) . '>' . $facet['value'] . '</a>';
			}
		}
		return $facetsNew;
	}
	
	public function getSearchPhtml()
	{		
		$phtml[0] = PROSPER_VIEW . '/prospersearch/product.php';
		$phtml[1] = PROSPER_VIEW . '/prospersearch/productPage.php';
				
		// Product Search CSS for results and search
		if ($this->_options['Set_Theme'] != 'Default')
		{
			$dir = PROSPER_THEME . '/' . $this->_options['Set_Theme'];
			if($newTheme = glob($dir . "/*.php"))
			{			
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
			}
		}

		return $phtml;
	}
	
	public function getEndpoints($params, $url)
	{
		$sepEnds = array();
		if ($this->_options['Product_Endpoint'])
		{
			$title = $this->_options['prodLabel'] ? $title = $this->_options['prodLabel'] : 'Products'; 
		
			$sepEnds['prod'] = '<a href="' . str_replace(array('/city/' . $params['city'], '/state/' . $params['state'], '/zip/' . $params['zip'], '/page/' . $params['page'], '/celebrity/' . $params['celebrity'], '/type/' . $params['type'], '/sort/' . $params['sort'], '/celebQuery/' . $params['celebQuery']), '', $url) . '">' . $title . '</a>';
		}
		
		if ($this->_options['Coupon_Endpoint'])
		{
			$title = $this->_options['coupLabel'] ? $title = $this->_options['coupLabel'] : 'Coupons'; 
		
			$sepEnds['coup'] = '<a href="' . str_replace(array('/city/' . $params['city'], '/state/' . $params['state'], '/zip/' . $params['zip'], '/page/' . $params['page'], '/celebrity/' . $params['celebrity'], '/type/' . $params['type'], '/brand/' . $params['brand'], '/sort/' . $params['sort'], '/celebQuery/' . $params['celebQuery']), '', $url) . '/type/coup">' . $title . '</a>';
		}
				
		if ($this->_options['Local_Endpoint'])
		{
			$title = $this->_options['localLabel'] ? $title = $this->_options['localLabel'] : 'Local Deals'; 
		
			$sepEnds['local'] = '<a href="' . str_replace(array('/page/' . $params['page'], '/celebrity/' . $params['celebrity'], '/type/' . $params['type'], '/brand/' . $params['brand'], '/query/' . $params['query'], '/sort/' . $params['sort'], '/merchant/' . $params['merchant'], '/celebQuery/' . $params['celebQuery']), '', $url) . '/type/local">' . $title . '</a>';
		}
		
		if ($this->_options['Celebrity_Endpoint'])
		{	
			if ($this->_options['celeLabel'])
			{
				$title = $this->_options['celeLabel'];
			}
			else
			{
				$title = count($sepEnds) < 3 ? 'Celebrity Products' : 'Celebrity'; 
			}
			
			$sepEnds['cele'] = '<a href="' . str_replace(array('/city/' . $params['city'], '/state/' . $params['state'], '/zip/' . $params['zip'], '/page/' . $params['page'], '/type/' . $params['type'], '/query/' . $params['query'], '/sort/' . $params['sort'], '/merchant/' . $params['merchant']), '', $url) . '/type/cele">' . $title . '</a>';
		}
		
		return $sepEnds;
	}
	
	public function getTypeSelector($sepEnds, $existingType = null)
	{
		if (count($sepEnds) > 1)
		{
			$newEnds = array_keys($sepEnds);
			$startingType = $newEnds[0];
			
			$type = $existingType ? $existingType : $startingType;
			
			$sepEnds[$type] = strip_tags($sepEnds[$type]);
		
			$typeSelector = '<div class="typeselector" style="display:inline-block; margin-top:9px;">' . implode(' | ', $sepEnds) . '</div>';
		}
		else
		{
			$typeSelector = '';
		}
		
		return $typeSelector;
	}	
	
	public function prosperPagination($results = '', $paged, $range = 8)
	{
		$limit = $this->_options['Pagination_Limit'] ? $this->_options['Pagination_Limit'] : 10;
		$pages = round(count($results) / $limit, 0);
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
			$newPage = home_url('/') . 'products/page/'; 
		}

		if(1 != $pages)
		{
			echo '<div class="pagination"><span>Page ' . $paged . ' of ' . $pages . '</span>';
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
	
	public function searchShortcode($atts, $content = null)
	{
		$options = $this->_options;
		
		$pieces = $this->shortCodeExtract($atts, $this->_shortcode);		

		if(get_query_var('queryParams'))
		{
			$params = $this->getUrlParams();
			$query = $params['query'];
		}		

		$base = $options['Base_URL'] ? $options['Base_URL'] : 'products';
		$url = home_url('/') . $base;

		if (is_page($base) && $_POST['q'])
		{
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$prodSubmit = preg_replace('/\/$/', '', $url);
			$newQuery = str_replace(array('/query/' . $query, '/query/' . rawurlencode($query)), array('', ''), $prodSubmit);
			header('Location: ' . $newQuery . '/query/' . rawurlencode(trim($_POST['q'])));
			exit;
		}
		elseif ($_POST['q'])
		{
			header('Location: ' . $url . '/query/' . rawurlencode(trim($_POST['q'])));
			exit;
		}		

		require_once(PROSPER_VIEW . '/prospersearch/searchShort.php');
	}
	
	public function ogMeta()
	{
		if(!preg_match('/^@/', $this->_options['Twitter_Site']))
		{
			$this->_options['Twitter_Site'] = '@' . $this->_options['Twitter_Site'];
		}
		if(!preg_match('/^@/', $this->_options['Twitter_Creator']))
		{
			$this->_options['Twitter_Creator'] = '@' . $this->_options['Twitter_Creator'];
		}

		$prosperPage = get_query_var('prosperPage');
		
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
		}
		else
		{
			if ($this->_options['Country'] === 'US')
			{
				$fetch = 'fetchProducts';
				$currency = 'USD';
			}
			elseif ($this->_options['Country'] === 'CA')
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
		}
		
		/*
		/  Prosperent API Query
		*/
		$settings = array(
			'limit' => 1,
			$filter => get_query_var('cid')
		);
				
		$allData = $this->apiCall($settings, $fetch);
		$record = $allData['results'];

		$page = $this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products';
		if (is_page($page) && get_query_var('cid'))
		{
			$priceSale = $record[0]['priceSale'] ? $record[0]['priceSale'] : $record[0]['price_sale'];
			// Open Graph: FaceBook
			echo '<meta property="og:url" content="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" />';
			echo '<meta property="og:site_name" content="' . get_bloginfo('name') . '" />';
			echo '<meta property="og:type" content="website" />';
			echo '<meta property="og:image" content="' . $record[0]['image_url'] . '" />';
			echo '<meta property="og:description" content="' . $record[0]['description'] . '" />';
			echo '<meta property="og:title" content="' . strip_tags($record[0]['keyword'] . ' - ' .  get_the_title($post) . ' - ' . get_bloginfo('name')) . '" />';

			// Twitter Cards
			echo '<meta name="twitter:card" content="product">';
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
				$opts = array_merge($options, array(
					'Base_URL' => get_post()->post_name	
				));				
			}
			else
			{
				$opts = array_merge($options, array(
					'Base_URL' => 'products'
				));		
			}
				
			update_option('prosper_advanced', $opts);

			$newOptions = get_option('prosper_advanced');
			$page       = isset($newOptions['Base_URL']) ? $newOptions['Base_URL'] . '/' : 'products/';
			$pageName   = isset($newOptions['Base_URL']) ? 'pagename=' . $newOptions['Base_URL'] : 'pagename=products';
			
			add_rewrite_rule('^([^/]+)/([^/]+)/cid/([a-z0-9A-Z]{32})/?$', 'index.php?' . $pageName . '&prosperPage=$matches[1]&keyword=$matches[2]&cid=$matches[3]', 'top');
			add_rewrite_rule('store/go/([^/]+)/?', 'index.php?' . $pageName . '&store&go&storeUrl=$matches[1]', 'top');
			add_rewrite_rule('img/([^/]+)/?', 'index.php?' . $pageName . '&prosperImg=$matches[1]', 'top');
			add_rewrite_rule($page . '(.+)', 'index.php?' . $pageName . '&queryParams=$matches[1]', 'top');
			
			$this->prosperFlushRules();			
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
		
		if ($params['state'])
		{
			$backStates = array_flip($this->states);
			$state = ucwords($backStates[$params['state']]) . ' Deals';
		}
		
		if ('coup' === $type)
		{
			$query = $params['query'] ? $params['query'] : ($this->_options['Coupon_Query'] ? $this->_options['Coupon_Query'] : '');
		}
		if ('cele' === $type)
		{		
			$query = $params['query'] ? $params['query'] : (($this->_options['Celebrity_Query'] && !$brand && !$merchant) ? $this->_options['Celebrity_Query'] : '');		
		}

		$query = ucwords(rawurldecode(str_replace('+', ' ', $query)));
		
		if (get_query_var('cid'))
		{ 
			$query = preg_replace('/\(.+\)/i', '', rawurldecode(get_query_var('keyword')));
			$query = str_replace(',SL,', '/', $query);
			$title = ucwords($query) . $sep . $title;
		}
		elseif (is_page($page))
		{
			if ('local' == $type)
			{
				switch ( $this->_options['Title_Structure'] )
				{
					case '0':
						$title =  $title;
						break;
					case '1':
						$title =  $title . $page_num . (($zip || $city || $state) ? $sep : '') . ($zip ? $zip : '') . ($zip && $city ? ' ' : '') . ($city ? $city : '') . (($zip && $state || $state && $city) ? ' ' : '') . ($state ? $state : '');
						break;
					case '2':
						$title = ($zip ? $zip : '') . ($zip && $city ? ' ' : '') . ($city ? $city : '') . (($zip && $state && !$city) ? ' ' : (($state && $city) ? ', ' : '')) . ($state ? $state : '') . (($zip || $city || $state) ? $sep : '') . $pagename . $page_num . $sep . $blogname;
						break;
					case '3':
						$title =  !$zip ? $title : ($zip ? $zip : '') . ($city ?  ' &raquo; ' . $city : '') . ($state ? ' &raquo; ' . $state : '') . $page_num;
						break;
					case '4':
						$title =  $title;
						break;
				}
			}
			else
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
		}

		return $title;
	}	
	
	public function storeSearch()
	{		
		if(get_query_var('queryParams'))
		{
			$params = $this->getUrlParams();
		}

		$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = preg_replace('/\/$/', '', $url);
		
		if(is_front_page())
		{
			$base = $this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products';
			$url = home_url('/') . $base;
		}

		$sepEnds 	  = $this->getEndpoints($params, $url);
		$typeSelector = $this->getTypeSelector($sepEnds, $params['type']);
		$newEnds 	  = array_keys($sepEnds);
		$brand    	  = isset($params['brand']) ? rawurldecode(stripslashes($params['brand'])) : '';
		$merchant 	  = isset($params['merchant']) ? rawurldecode(stripslashes($params['merchant'])) : '';		

		return array(
			'startingType' => $newEnds[0],
			'filters'	   => array(
				'brands' 	=> $this->getBrands($brand),
				'merchants' => $this->getMerchants($merchant)
			),
			'typeSelector' => $typeSelector,
			'params'	   => $params,
			'url'		   => $url			
		);
	}
}