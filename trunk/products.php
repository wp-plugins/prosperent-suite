<script type="text/javascript">
    <!--
    function toggle_visibility(id)
    {
        var e = document.getElementById(id);
        if(e.style.display == 'none')
        e.style.display = 'block';
    }
    function toggle_hidden(id)
    {
        var e = document.getElementById(id);
        if(e.style.display == 'block')
        e.style.display = 'none';
    }    
    function showFullDesc(id)
    {
        var e = document.getElementById(id);
        if(e.style.display == 'none')
        e.style.display = '';
    }    
    function hideMoreDesc(id)
    {
        var e = document.getElementById(id);
        if(e.style.display == 'inline-block')
        e.style.display = 'none';
    }  
	//-->
</script>
<?php
if ($storeUrl = get_query_var('storeUrl'))
{    
	$storeUrl = rawurldecode($storeUrl);
	$storeUrl = str_replace(',SL,', '/', $storeUrl);
	header('Location:http://prosperent.com/' . $storeUrl);
	exit;
}

if ($prosperImgUrl = get_query_var('prosperImg'))
{    
	$prosperImgUrl = rawurldecode($prosperImgUrl);
	$prosperImgUrl = str_replace(',SL,', '/', $prosperImgUrl);
	header('Location:http://img1.prosperent.com/images/' . $prosperImgUrl);
	exit;
}

if (preg_match('/\?/', $_SERVER['REQUEST_URI']))
{  
	$base = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products';
	$pageNumber = preg_replace('/(.*)(\/page\/)(\d+)(\/.*)/i', '$3', $_SERVER['REQUEST_URI']);
	$queryStrings = preg_replace('/(\/products\/)|' . $base . '|(\/page\/)(\d+)|(\?)|(\/)/', '', (preg_split('/&/',$_SERVER['REQUEST_URI'])));

	foreach ($queryStrings as $query)
	{
		if (preg_match('/q/i', $query))
		{
			$queryMatch = preg_split('/=/', $query);
			$newQueryString = 'query/' . $queryMatch[1] . '/';
		}
		elseif(preg_match('/merchant/i', $query))
		{
			$queryMatch = preg_split('/=/', $query);
			$newMerchantString = 'merchant/' . $queryMatch[1] . '/';
		}
		elseif(preg_match('/brand/i', $query))
		{
			$queryMatch = preg_split('/=/', $query);
			$newBrandString = 'brand/' . $queryMatch[1] . '/';
		}
		elseif(preg_match('/celeb/i', $query))
		{
			$queryMatch = preg_split('/=/', $query);
			$newCelebString = 'celeb/' . $queryMatch[1] . '/';
		}
	}	
	
	if($pageNumber > 1)
	{
		$newPageString = 'page/' . $pageNumber . '/';
	}
	
	header('Location:' . site_url('/') . $base . '/' . $newQueryString . $newMerchantString . $newBrandString . $newCelebString . $newPageString);
	exit;
}

function prosper_pagination($pages = '', $paged, $range = 8)
{
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
		$newPage = site_url('/') . 'products/page/'; 
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

if(get_query_var('queryParams'))
	$params = array_reverse(explode('/', get_query_var('queryParams')));

$sendParams = array();
if (!empty($params))
{
	$params = array_reverse($params);
	foreach ($params as $k => $p)
	{
		//if the number is even, grab the next index value
		if (!($k & 1))
		{
			$sendParams[$p] = $params[$k + 1];
		}
	}
}

$sort           = isset($sendParams['sort']) ? ($sendParams['sort'] == 'rel' ? '' : $sendParams['sort']) : $options['Default_Sort'];
$filterMerchant = isset($sendParams['merchant']) ? stripslashes($sendParams['merchant']) : '';
$filterBrand    = isset($sendParams['brand']) ? stripslashes($sendParams['brand']) : '';
$filterZip      = isset($sendParams['zip']) ? stripslashes($sendParams['zip']) : '';
$filterCity     = isset($sendParams['city']) ? stripslashes($sendParams['city']) : '';
$filterCountry  = isset($sendParams['country']) ? stripslashes($sendParams['country']) : '';
$filterState    = isset($sendParams['state']) ? stripslashes($sendParams['state']) : '';
$pageNumber     = isset($sendParams['page']) ? stripslashes($sendParams['page']) : 1;
$celeb          = isset($sendParams['celeb']) ? $sendParams['celeb'] : '';
$celebQuery     = isset($sendParams['celebQuery']) ? $sendParams['celebQuery'] : '';
$type           = isset($sendParams['type']) ? $sendParams['type'] : '';
$target 	    = isset($options['Target']) ? '_blank' : '_self';
$query			= isset($sendParams['query']) ? rawurldecode($sendParams['query']) : '';
$city			= rawurldecode($filterCity);
$country		= rawurldecode($filterCountry);
$decodeState	= rawurldecode($filterState);
$zip			= rawurldecode($filterZip);
$celebDecode    = rawurldecode($celeb);
$merchantDecode = rawurldecode($filterMerchant);
$brandDecode 	= rawurldecode($filterBrand);

$productPage = 'http://' . $_SERVER['HTTP_HOST'];

$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$url = preg_replace('/\/$/', '', $url);

if(is_front_page())
{
	$url = site_url('/') . 'products';
}

$endpoints = array();
foreach ($options as $i => $option)
{
	if (preg_match('/_Endpoint/', $i))
	{
		$endpoints[$i] = $option;
	}
}

$endpoints = array_keys($endpoints);

$sepEnds = array();
foreach ($endpoints as $endpoint)
{
	$celebrityTitle = count($endpoints) <= 3 ? 'Celebrity Products' : 'Celebrity'; 
	switch ($endpoint)
	{
		case 'Product_Endpoint':
			$sepEnds['prod'] = '<a href="' . str_replace(array('/city/' . $filterCity, '/state/' . $filterState, '/zip/' . $filterZip, '/page/' . $pageNumber, '/celeb/' . $celeb, '/type/' . $type, '/sort/' . $sort), array('', '', '', '', '', '', ''), $url) . '">Products</a>';
			break;
		case 'Coupon_Endpoint':
			$sepEnds['coup'] = '<a href="' . str_replace(array('/city/' . $filterCity, '/state/' . $filterState, '/zip/' . $filterZip, '/page/' . $pageNumber, '/celeb/' . $celeb, '/type/' . $type, '/brand/' . $filterBrand, '/sort/' . $sort), array('', '', '', '', '', '', '', ''), $url) . '/type/coup">Coupons</a>';
			break;
		case 'Celebrity_Endpoint':
			$sepEnds['cele'] = '<a href="' . str_replace(array('/city/' . $filterCity, '/state/' . $filterState, '/zip/' . $filterZip, '/page/' . $pageNumber, '/type/' . $type, '/query/' . $query, '/sort/' . $sort, '/merchant/' . $filterMerchant), array('', '', '', '', '', '', '', '',), $url) . '/type/cele">' . $celebrityTitle . '</a>';
			break;
		case 'Local_Endpoint':
			$sepEnds['local'] = '<a href="' . str_replace(array('/page/' . $pageNumber, '/celeb/' . $celeb, '/type/' . $type, '/brand/' . $filterBrand, '/query/' . $query, '/sort/' . $sort), array('', '', '', '', '', ''), $url) . '/type/local">Local Deals</a>';
			break;
		/*case 'Travel_Endpoint':
			$sepEnds['travel'] = '<a href="' . $url . '/type/travel">Travel Deals</a>';
			break;
		*/
	}
}

$newEnds = array_keys($sepEnds);
$startingType = $newEnds[0];

$type = !isset($sendParams['type']) ? $startingType : $sendParams['type'];

$sepEnds[$type] = strip_tags($sepEnds[$type]);

$typeSelector = '<div class="typeselector" style="display:inline-block; margin-top:9px;">' . implode(' | ', $sepEnds) . '</div>';

$filterBrands = array();
$filterMerchants = array();

if ($brandDecode)
{
	array_push($filterBrands, $brandDecode);
}
if ($merchantDecode)
{
	array_push($filterMerchants, $merchantDecode);
}
if($options['Positive_Brand'])
{
    $plusBrands = explode(',', stripslashes($options['Positive_Brand']));

    foreach ($plusBrands as $postive)
    {
        array_push($filterBrands, trim($postive));
    }
}
if ($options['Positive_Merchant'])
{
    $plusMerchants = explode(',', stripslashes($options['Positive_Merchant']));

    foreach ($plusMerchants as $positive)
    {
        array_push($filterMerchants, trim($positive));
    }
}
if($options['Negative_Brand'])
{
    $minusBrands = explode(',', stripslashes($options['Negative_Brand']));

    foreach ($minusBrands as $negative)
    {
        array_push($filterBrands, '!' . trim($negative));
    }
}
if ($options['Negative_Merchant'])
{
    $minusMerchants = explode(',', stripslashes($options['Negative_Merchant']));

    foreach ($minusMerchants as $negative)
    {
        array_push($filterMerchants, '!' . trim($negative));
    }
}

if (get_query_var('cid'))
{            
	$keyword = rawurldecode(get_query_var('keyword'));
	$keyword = str_replace(',SL,', '/', $keyword);

	$urlParts = preg_split('/\//', $_SERVER['REQUEST_URI']);

	switch ($urlParts[1])
	{ 
		case 'coupon':
			include(PROSPER_PATH . 'search/coupon.php');
			break;
		case 'local':
			include(PROSPER_PATH . 'search/localPage.php');
			break;
		case 'travel':
			include(PROSPER_PATH . 'search/travelPage.php');
			break;
		case 'celebrity':
			include(PROSPER_PATH . 'search/celebrity.php');
			break;			
		default:
			include(PROSPER_PATH . 'search/product.php');
			break;
	}
	return;
}

switch ($type)
{
	case 'coup':
		include(PROSPER_PATH . 'search/coup.php');
		break;
	case 'cele':
		include(PROSPER_PATH . 'search/cele.php');
		break;
	case 'local':
		include(PROSPER_PATH . 'search/local.php');
		break;
	case 'travel':
		include(PROSPER_PATH . 'search/travel.php');
		break;
	default: 
		include(PROSPER_PATH . 'search/prod.php');
		break;
		
}
