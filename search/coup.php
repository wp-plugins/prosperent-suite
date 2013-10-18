<?php
if (!$query && $options['Coupon_Query'])
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url = preg_replace('/\/$/', '', $url) . '/query/' . htmlentities(rawurlencode($options['Coupon_Query']));
    $q = $options['Coupon_Query'];
}
else
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$url = preg_replace('/\/$/', '', $url);
    $q = $query;
}

$query = stripslashes($q);

$coupSubmit = $url;

if(is_front_page())
{
	$coupSubmit = site_url('/') . 'products';
}

if (isset($_POST['q']))
{
	$newQuery = str_replace(array('/query/' . $query, '/page/' . $pageNumber), array('', ''), $coupSubmit);
    header('Location: ' . $newQuery . '/query/' . htmlentities(rawurlencode($_POST['q'])));
    exit;
}

if (isset($_POST['sort']))
{
	$newSort = str_replace(array('/sort/' . $sendParams['sort'], '/page/' . $pageNumber), array('', ''), $coupSubmit);
    header('Location: ' . $newSort . '/sort/' . $_POST['sort']);
    exit;
}

$key = array_search('Zappos.com', $filterMerchants);
if ($key || 0 === $key)
{
    unset($filterMerchants[$key]);
}

$key2 = array_search('6pm', $filterMerchants);
if ($key2 || 0 === $key2)
{
    unset($filterMerchants[$key2]);
}

$filterCouponMerchants = array_merge($filterMerchants, array('!Zappos.com', '!6pm'));

/*
/  Prosperent API Query
*/
$settings = array(
    'api_key'        => $options['Api_Key'],
    'query'          => $query,
    'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
    'limit'          => $options['Api_Limit'],
    'enableFacets'   => $options['Enable_Facets'],
    'filterMerchant' => $filterCouponMerchants,
    'sortPrice'	     => $sort
);

if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
{	
	$settings = array_merge($settings, array(
		'cacheBackend'   => 'FILE',
		'cacheOptions'   => array(
			'cache_dir'  => PROSPER_CACHE,
			'lifetime'	 => 3600
		)
	));	
}

$prosperentApi = new Prosperent_Api($settings);
/*
/  Fetching results and pulling back all data
/  To see which data is available to pull back login in to
/  Prosperent.com and click the API tab
*/
$prosperentApi -> fetchCoupons();
$results = $prosperentApi -> getAllData();
$facets = $prosperentApi -> getFacets();
$totalFound = $prosperentApi -> getTotalRecordsFound();

echo $typeSelector;
?>

<div class="prosper_searchform">
    <form class="searchform" method="POST" action="">
        <input class="prosper_field" type="text" name="q" id="s" placeholder="<?php echo $options['Search_Bar_Text'] ? $options['Search_Bar_Text'] : 'Search Coupons'; ?>">
        <input class="prosper_submit" type="submit" value="Search">
    </form>
</div>

<?php
/*
/  If no results, or the user clicked search when 'Search Products...'
/  was in the search field, displays 'No Results'
*/
if (empty($results) || (empty($filterMerchants) && !$query))
{
    header( $_SERVER['SERVER_PROTOCOL']." 404 Not Found", true, 404 );
    echo '<div class="noResults">No Results</div>';

    if ($filterMerchant)
    {
        echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace('/merchant/' . $filterMerchant, '', $coupSubmit) . '>clear the filter(s)</a></div>';
    }
    else
    {
        echo '<div class="noResults-secondary">Please try your search again.</div>';
    }
    echo '<div class="noResults-padding"></div>';

    // calculate date range
    $prevNumDays = 30;
    $startRange = date('Ymd', time() - 86400 * $prevNumDays);
    $endRange   = date('Ymd');

    // fetch trends from api
    $settings = array(
        'enableFacets'  => 'keyword',
        'filterCatalog' => 'coupons'
    );
	
	if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
	{	
		$settings = array_merge($settings, array(
			'cacheBackend'   => 'FILE',
			'cacheOptions'   => array(
				'cache_dir'  => PROSPER_CACHE,
				'lifetime'	 => 3600
			)
		));	
	}
	
	$api = new Prosperent_Api($settings);

    $api->setDateRange('commission', $startRange, $endRange)
        ->fetchTrends();

    // set productId as key in array
    foreach ($api->getFacets('keyword') as $data)
    {
        $keys[] = $data['value'];
    }

    // fetch merchant data from api
    $settings = array(
        'api_key'         => $options['Api_Key'],
        'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
        'filterKeyword'   => $keys,
        'limit' 	      => 15
    );

	if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
	{	
		$settings = array_merge($settings, array(
			'cacheBackend'   => 'FILE',
			'cacheOptions'   => array(
				'cache_dir'  => PROSPER_CACHE,
				'lifetime'	 => 3600
			)
		));	
	}
	
	$api = new Prosperent_Api($settings);
	
    $api->fetchCoupons();
    $results = $api->getAllData() ;

    echo '<div class="totalFound">Browse these <strong>trending coupons</strong></div>';
    ?>

    <div id="couponList">
        <?php
        // Loop to return coupons and corresponding information
        foreach ($results as $i => $record)
        {
            ?>
            <div class="<?php echo $i > 0 ? 'couponBlock' : 'couponBlock0'; ?>">
                <div class="couponImage">
                    <?php
                    echo '<a href="' . $productPage . '/coupon/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['couponId'] . '"><img src="' . ($options['Image_Masking'] ? $productPage  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record['image_url'])) :  $record['image_url']) . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></a>';
                    ?>
                </div>
                <div class="couponContent">
                    <div class="couponTitle">
                        <?php
                        echo '<a href="' . $productPage . '/coupon/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['couponId'] . '">' . $record['keyword'] . '</a>';
                        ?>
                    </div>
                    <?php
                    if(!empty($record['expiration_date']))
                    {
                        $expires = strtotime($record['expiration_date']);
                        $today = strtotime(date("Y-m-d"));
                        $interval = abs($expires - $today) / (60*60*24);

                        if ($interval <= 7 && $interval > 0)
                        {
                            echo '<div class="couponExpire"><span>Expires in ' . $interval . ' days!</span></div>';
                        }
                        else
                        {
                            echo '<div class="couponExpire"><span>Expires Soon!</span></div>';
                        }
                    }
                    ?>
                    <div class="couponDescription">
                        <?php
                        echo $record['description'];
                        ?>
                    </div>
                    <?php
                    if ($record['coupon_code'])
                    {
                        echo '<div class="couponCode">Coupon Code: <span class="code_cc">' . $record['coupon_code'] . '</span></div>';
                    }
                    ?>
                </div>
				<div class="couponVisit">
					<form style="margin:0; text-align:center;" method="POST" action="<?php echo $productPage . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) . '" target="' . $target; ?>">
						<input type="submit" value="Visit Store"/>
					</form>
				</div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}
else
{
    if ($prosperentApi->get_enableFacets() == 1)
    {
        $merchants = $facets['merchant'];

        if ($merchants)
        {
            $merchants1 = array_slice($merchants, 0, !$options['Merchant_Facets'] ? 10 : $options['Merchant_Facets'], true);
        }
        ?>
        <table id="facets">
            <tr>
                <td class="merchants" style="width:98%; float:none;">
                    <?php
                    echo (empty($filterMerchant) ? '<div class="browseMerchants">Browse by Merchant: </div>' : '<div class="filteredMerchants">Filtered by Merchant: </div>');

                    if (empty($facets['merchant']) && !$filterMerchant)
                    {
                        echo '<div class="noMerchants"">No Merchants Found</div>';
                    }
                    else if (!$filterMerchant && !empty($results))
                    {
                        $count = count($merchants1);
                        foreach ($merchants1 as $i => $merchant)
                        {
                            echo '<a href=' . str_replace('/page/' . $pageNumber, '', $coupSubmit) . '/merchant/' . rawurlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a>';

                            if ($i < ($count - 1))
                            {
                                echo ', ';
                            }
                        }
                    }
                    else
                    {
                        echo '<div style="min-height:35px;">';
                        echo rawurldecode($filterMerchant);
                        echo '</br><a href=' . str_replace(array('/page/' . $page, '/merchant/' . $filterMerchant), array('', ''), $coupSubmit) . '>clear filter</a>';
                        echo '<div style="margin-top:-50px;padding-left:150px;"><img src="' . ($options['Image_Masking'] ? $productPage  . '/img/' . rawurlencode(str_replace('/', ',SL,',  ('logos/120x60/' . $filterMerchant . '.png'))) : 'http://img1.prosperent.com/images/logos/120x60/' . $filterMerchant . '.png') . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
                        echo '</div>';
                    }
                    ?>
                </td>
            </tr>
        </table>
        <div class="table-seperator"></div>
        <?php
    }

    if (!$query && !$filterMerchant)
    {
        echo '<div class="totalFound">' . $totalFound . ' coupons</div>';
    }
    else
    {
        echo '<div class="totalFound">' . $totalFound . ' coupons for <b>' . ucwords($query ? rawurldecode($query) : rawurldecode($filterMerchant)) . '</b>' . (($query && $filterMerchant) ? '<a style="font-size:11px;margin-top:-5px;" href=' . str_replace('/query/' . $query, '', $coupSubmit) . '> [x]</a>' : '') . '</div>';
    }

    // Gets the count of results for Pagination
    $productCount = count($results);

    // Pagination limit, can be changed
    $limit = !$options['Pagination_Limit'] ? 15 : $options['Pagination_Limit'];
    $ceiling = ceil(($productCount + 1) / $limit);

    $pages = round($productCount / $limit, 0);

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
    ?>
    <div class="prosper_priceSorter">
        <form class="sorterofprice" name="priceSorter" method="POST" action="" >
            <label for="PriceSort">Sort By: </label>
            <select name="sort" onChange="priceSorter.submit();">
                <option value="rel">Relevancy</option>
                <option <?php echo ($sort == 'expiration_date+desc' ? 'selected="true"' : ''); ?> value="expiration_date+desc">Expiration Date: Descending</option>
                <option <?php echo ($sort == 'expiration_date+asc' ? 'selected="true"' : ''); ?> value="expiration_date+asc">Expiration Date: Ascending</option>
            </select>
            <?php echo ($sort != 'rel' && '' != $sort) ? '<a style="font-size:11px;margin-top:-5px;" href=' . str_replace(array('/page/' . $pageNumber, '/sort/' . $sort), array('', ''), $coupSubmit) . '> [x]</a>' : ''; ?>
        </form>
    </div>

    <div id="couponList">
        <?php
        // Loop to return coupons and corresponding information
        foreach ($results as $i => $record)
        {
            ?>
            <div class="<?php echo $i > 0 ? 'couponBlock' : 'couponBlock0'; ?>">
                <div class="couponImage">
                    <?php
                    echo '<a href="' . $productPage . '/coupon/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['couponId'] . '"><img src="' . ($options['Image_Masking'] ? $productPage  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record['image_url'])) :  $record['image_url']) . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></a>';
                    ?>
                </div>
                <div class="couponContent">
                    <div class="couponTitle">
                        <?php
                        echo '<a href="' . $productPage . '/coupon/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['couponId'] . '">' . $record['keyword'] . '</a>';
                        ?>
                    </div>
                    <?php
                    if(!empty($record['expiration_date']))
                    {
                        $expires = strtotime($record['expiration_date']);
                        $today = strtotime(date("Y-m-d"));
                        $interval = abs($expires - $today) / (60*60*24);

                        if ($interval <= 7 && $interval > 0)
                        {
                            echo '<div class="couponExpire"><span>Expires in ' . $interval . ' days!</span></div>';
                        }
                        else
                        {
                            echo '<div class="couponExpire"><span>Expires Soon!</span></div>';
                        }
                    }
                    ?>
                    <div class="couponDescription">
                        <?php
                        echo $record['description'];
                        ?>
                    </div>
                    <?php
                    if ($record['coupon_code'])
                    {
                        echo '<div class="couponCode">Coupon Code: <span class="code_cc">' . $record['coupon_code'] . '</span></div>';
                    }
                    ?>
                </div>
				<div class="couponVisit">
					<form style="margin:0; text-align:center;" method="POST" action="<?php echo $productPage . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) . '" target="' . $target; ?>">
						<input type="submit" value="Visit Store"/>
					</form>
				</div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}

prosper_pagination($pages, $pageNumber);
