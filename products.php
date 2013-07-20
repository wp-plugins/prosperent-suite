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
    //-->
</script>
<?php
if ($storeUrl = get_query_var('storeUrl'))
{
    $storeUrl = urldecode($storeUrl);
    $storeUrl = str_replace(',SL,', '/', $storeUrl);
    header('Location:http://prosperent.com/' . $storeUrl);
}

if ($prosperImgUrl = get_query_var('prosperImg'))
{
    $prosperImgUrl = urldecode($prosperImgUrl);
    $prosperImgUrl = str_replace(',SL,', '/', $prosperImgUrl);
    header('Location:http://img1.prosperent.com/images/' . $prosperImgUrl);
}

$options = $this->get_option();

if (preg_match('/\?/', $_SERVER['REQUEST_URI']))
{
    $pageNumber = preg_replace('/(.*)(\/page\/)(\d+)(\/.*)/i', '$3', $_SERVER['REQUEST_URI']);
    $queryStrings = preg_replace('/(\/products\/)|' . $options["Base_URL"]. '|(\/page\/)(\d+)|(\?)|(\/)/', '', (preg_split('/&/',$_SERVER['REQUEST_URI'])));

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
    echo $newQueryString . $newMerchantString . $newBrandString . $newCelebString . $newPageString;

    header('Location:http://' . $_SERVER['HTTP_HOST'] . (!$options['Base_URL'] ?  '/products' : ($options['Base_URL'] == 'null' ? '/' : '/' . $options['Base_URL'])) . '/' . $newQueryString . $newMerchantString . $newBrandString . $newCelebString . $newPageString);
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

    if(1 != $pages)
    {
        echo "<div class=\"pagination\"><span>Page ".$paged." of ".$pages."</span>";
        if($paged > 2 && $paged <= $pages) echo "<a href='" .get_pagenum_link(1)."'>&laquo; First</a>";
        if($paged > 1) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";

        for ($i = $paged; $i <= $pages; $i++)
        {
            if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
            {
                echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
            }
        }

        if ($paged < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";
        if ($paged < $pages && $paged < $pages-1) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
        echo "</div>";
    }
}

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
$filterMerchant = stripslashes($sendParams['merchant']);
$filterBrand    = stripslashes($sendParams['brand']);
$filterZip      = stripslashes($sendParams['zip']);
$filterCity     = stripslashes($sendParams['city']);
$filterCountry  = stripslashes($sendParams['country']);
$filterState    = stripslashes($sendParams['state']);
$pageNumber     = stripslashes($sendParams['page']);;
$celeb          = $sendParams['celeb'];
$celebQuery     = $sendParams['celebQuery'];
$type           = $sendParams['type'];
$target 	    = $options['Target'] ? '_blank' : '_self';
$query			= $sendParams['query'];
$city			= urldecode($filterCity);
$country		= urldecode($filterCountry);
$decodeState	= urldecode($filterState);
$zip			= urldecode($filterZip);
$celebDecode    = urldecode($celeb);

/*
if (($filterBrand || $filterMerchant || $filterZip || $filterCity || $filterState || $filterCountry) && !$query)
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $q = '';
}
elseif (!$query && $options['Starting_Query'] && $type != 'local' && $type != 'travel')
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . 'query/' . urlencode($options['Starting_Query']);
    $q = $options['Starting_Query'];
}
else
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $q = $query;
}

$query = stripslashes($q);

$url = preg_replace('/\/$/', '', $url);
$submitUrl = preg_replace('/\/page\/\d+/i', '', $url);
$newUrl = str_replace(array('/type/' . $type, '//' . $sort, //), array('',), $submitUrl);
$newUrlNoQuery = str_replace('/query/' . $q,  '', $newUrl);
*/
$productPage = 'http://' . $_SERVER['HTTP_HOST'];

$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$url = preg_replace('/\/$/', '', $url);

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
            $sepEnds['prod'] = '<a href="' . str_replace(array('/city/' . $filterCity, '/state/' . $filterState, '/zip/' . $filterZip, '/page/' . $pageNumber, '/celeb/' . $celeb, '/type/' . $type), array('', '', '', '', '', ''), $url) . '/type/prod">Products</a>';
            break;
        case 'Coupon_Endpoint':
            $sepEnds['coup'] = '<a href="' . str_replace(array('/city/' . $filterCity, '/state/' . $filterState, '/zip/' . $filterZip, '/page/' . $pageNumber, '/celeb/' . $celeb, '/type/' . $type, '/brand/' . $filterBrand), array('', '', '', '', '', '', ''), $url) . '/type/coup">Coupons</a>';
            break;
        case 'Celebrity_Endpoint':
            $sepEnds['cele'] = '<a href="' . str_replace(array('/city/' . $filterCity, '/state/' . $filterState, '/zip/' . $filterZip, '/page/' . $pageNumber, '/type/' . $type, '/query/' . $query), array('', '', '', '', '', '',), $url) . '/type/cele">' . $celebrityTitle . '</a>';
            break;
        case 'Local_Endpoint':
            $sepEnds['local'] = '<a href="' . str_replace(array('/page/' . $pageNumber, '/celeb/' . $celeb, '/type/' . $type, '/brand/' . $filterBrand, '/query/' . $query), array('', '', '', '', ''), $url) . '/type/local">Local Deals</a>';
            break;
        /*case 'Travel_Endpoint':
            $sepEnds['travel'] = '<a href="' . $url . '/type/travel">Travel Deals</a>';
            break;
        */
    }
}

$keys = array_keys($sepEnds);
$startingType = $keys[0];

$type = !$sendParams['type'] ? $startingType : $sendParams['type'];

$sepEnds[$type] = strip_tags($sepEnds[$type]);

$typeSelector = '<div class="typeselector" style="display:inline-block; margin-top:9px;">' . implode(' | ', $sepEnds) . '</div>';

$filterBrands = array();
$filterMerchants = array();

if ($filterBrand)
{
    array_push($filterBrands, $filterBrand);
}
if ($filterMerchant)
{
    array_push($filterMerchants, $filterMerchant);
}
if($options['Positive_Brand'])
{
    $plusBrands = explode(',', stripslashes($options['Positive_Brand']));

    foreach ($plusBrands as $postive)
    {
        array_push($filterBrands, urlencode(trim($postive)));
    }
}
if ($options['Positive_Merchant'])
{
    $plusMerchants = explode(',', stripslashes($options['Positive_Merchant']));

    foreach ($plusMerchants as $positive)
    {
        array_push($filterMerchants, urlencode(trim($positive)));
    }
}
if($options['Negative_Brand'])
{
    $minusBrands = explode(',', stripslashes($options['Negative_Brand']));

    foreach ($minusBrands as $negative)
    {
        array_push($filterBrands, '!' . urlencode(trim($negative)));
    }
}
if ($options['Negative_Merchant'])
{
    $minusMerchants = explode(',', stripslashes($options['Negative_Merchant']));

    foreach ($minusMerchants as $negative)
    {
        array_push($filterMerchants, '!' . urlencode(trim($negative)));
    }
}


if (get_query_var('cid'))
{
    $keyword = urldecode(get_query_var('keyword'));
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
}
elseif ('coup' == $type)
{
    include(PROSPER_PATH . 'search/coup.php');
}
elseif ('cele' == $type)
{
    include(PROSPER_PATH . 'search/cele.php');
}
elseif ('local' == $type)
{
    include(PROSPER_PATH . 'search/local.php');
}
elseif ('travel' == $type)
{
    include(PROSPER_PATH . 'search/travel.php');
}
else
{
    include(PROSPER_PATH . 'search/prod.php');
}
