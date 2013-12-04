<?php
if (!$celeb && $options['Celebrity_Query'])
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url = preg_replace('/\/$/', '', $url);
    $url .= 'celeb/' . rawurlencode($options['Celebrity_Query']);
    $c = $options['Celebrity_Query'];
}
else
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $c = $celeb;
    $cq = $celebQuery;
}

$celebQuery = stripslashes($cq);
$celeb = stripslashes($c);
$celeSubmit = preg_replace('/\/$/', '', $url);

if(is_front_page())
{
	$celeSubmit = home_url('/') . 'products';
}

if (isset($_POST['cq']))
{
	$newQuery = str_replace(array('/query/' . $query, '/celebQuery/' . $celebQuery, '/page/' . $pageNumber), array('', '', ''), $celeSubmit);
    header('Location: ' . $newQuery . '/celebQuery/' . rawurlencode($_POST['cq']));
    exit;
}

if (isset($_POST['sort']))
{
	$newSort = str_replace(array('/sort/' . $sendParams['sort'], '/page/' . $pageNumber), array('', ''), $celeSubmit);
    header('Location: ' . $newSort . '/sort/' . $_POST['sort']);
    exit;
}

/*
/  Prosperent API Query
*/
$settings = array(
    'api_key'         => $options['Api_Key'],
    'filterCelebrity' => $celeb ? $celebDecode : '',
    'query'			  => $celebQuery,
    'filterBrand'	  => $filterBrand,
    'filterMerchant'  => $filterMerchant,
    'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
    'limit'           => $options['Api_Limit'],
    'sortPrice'	   	  => $sort,
    'enableFacets'    => $options['Enable_Facets']
);

if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
{	
	$settings = array_merge($settings, array(
		'cacheBackend'   => 'FILE',
		'cacheOptions'   => array(
			'cache_dir'  => PROSPER_CACHE
		)
	));	
}

$prosperentApi = new Prosperent_Api($settings);

if (!$celeb)
{
	$settings = array(
		'api_key'         => $options['Api_Key'],
		'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
		'limit'           => 500,
		'sortPrice'	   	  => 'celebrity asc'
	);

	if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
	{	
		$settings = array_merge($settings, array(
			'cacheBackend'   => 'FILE',
			'cacheOptions'   => array(
				'cache_dir'  => PROSPER_CACHE
			)
		));	
	}
	
	$celebrityApi = new Prosperent_Api($settings);
	
	$celebrityApi->fetchCelebrities();
	$celebrityResults = $celebrityApi -> getData();
}

/*
/  Fetching results and pulling back all data
/  To see which data is available to pull back login in to
/  Prosperent.com and click the API tab
*/
$prosperentApi -> fetch();
$results = $prosperentApi -> getAllData();
$totalFound = $prosperentApi -> getTotalRecordsFound();

echo $typeSelector;
?>

<div class="prosper_searchform">
    <form class="searchform" method="POST" action="">
        <input class="prosper_field" type="text" name="cq" id="s" placeholder="<?php echo isset($options['Search_Bar_Text']) ? $options['Search_Bar_Text'] : 'Search Products'; ?>">
        <input class="prosper_submit" type="submit" value="Search">
    </form>
</div>

<table id="facets">
    <tr>
        <td class="merchants" style="width:98%; float:none;">
            <?php
            echo (empty($celeb) ? '<div class="browseMerchants">Browse by Celebrity: </div>' : '<div class="filteredMerchants">Filtered by Celebrity: </div>');

            if (!$celeb)
            {
                foreach ($celebrityResults as $celebs)
                {
                    echo '<a style="font-size:12px;" href="' . str_replace('/page/' . $pageNumber, '', $celeSubmit) . '/celeb/' . rawurlencode($celebs['celebrity']) . '">' . $celebs['celebrity'] . '</a><span style="font-size:12px; font-weight:bold;"> | </span>';
                }
            }
            else
            {
                echo '<div style="min-height:35px;">';
                echo $celebDecode;
                echo '</br><a href=' . str_replace(array('/page/' . $page, '/merchant/' . $filterMerchant, '/brand/' . $filterBrand, '/celeb/' . $celeb), array('', '', '', ''), $celeSubmit) . ' >clear filter</a>';
                echo '<div style="margin-top:-50px;padding-left:150px;"><img src="' . ($options['Image_Masking'] ? $productPage  . '/img/' . str_replace('/', ',SL,',  ('celebrity/100x100/' . $celeb . '.jpg')) : 'http://img1.prosperent.com/images/celebrity/100x100/' . $celebDecode . '.jpg') . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
                echo '</div>';
            }
            ?>
        </td>
    </tr>
</table>
<?php

/*
/  If no results displays 'No Results'
*/
if (empty($results) && $celeb)
{
    header( $_SERVER['SERVER_PROTOCOL']." 404 Not Found", true, 404 );
    echo '<div class="noResults">No Results</div>';

    if ($filterMerchant || $filterBrand || $query)
    {
        echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace(array('/merchant/' . $filterMerchant, '/brand/' . $filterBrand, '/query/' . $query), array('', '', ''), $celeSubmit) . '>clear the filter(s)</a>.</div>';
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
        'enableFacets' => 'productId'
    );

	if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
	{	
		$settings = array_merge($settings, array(
			'cacheBackend'   => 'FILE',
			'cacheOptions'   => array(
				'cache_dir'  => PROSPER_CACHE
			)
		));	
	}
	
	$api = new Prosperent_Api($settings);
	
    $api->setDateRange('commission', $startRange, $endRange)
        ->fetchTrends();

    // set productId as key in array
    foreach ($api->getFacets('productId') as $data)
    {
        $keys[] = $data['value'];
    }

    // fetch merchant data from api
    $settings = array(
        'api_key'         => $options['Api_Key'],
        'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
        'filterProductId' => $keys,
        'limit' 	      => $options['Api_Limit']
    );

	if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
	{	
		$settings = array_merge($settings, array(
			'cacheBackend'   => 'FILE',
			'cacheOptions'   => array(
				'cache_dir'  => PROSPER_CACHE
			)
		));	
	}
	
	$api = new Prosperent_Api($settings);
	
    $api->fetch();
    $results = $api->getAllData() ;

    // Gets the count of results for Pagination
    $productCount = count($results);

    // Pagination limit, can be changed
    $limit = !$options['Pagination_Limit'] ? 15 : $options['Pagination_Limit'];

    $pages = round($productCount / $limit, 0);

    if ($pageNumber  < 1)
    {
        $pageNumber  = 1;
    }
    else if ($pageNumber  > ceil(($productCount + 1) / $limit))
    {
        $pageNumber  = ceil(($productCount + 1) / $limit);
    }

    $limitLower = ($pageNumber  - 1) * $limit;

    // Breaks the array into smaller chunks for each page depending on $limit
    $results = array_slice($results, $limitLower, $limit, true);

    echo '<div class="totalFound">Browse these <strong>trending products</strong></div>';
    ?>

    <div id="productList">
        <?php
        // Loop to return Products and corresponding information
        foreach ($results as $i => $record)
        {
            $record['image_url'] 	 = $options['Image_Masking'] ? $productPage  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $record['image_url']);
            $record['affiliate_url'] = $options['URL_Masking'] ? $productPage . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) : $record['affiliate_url'];
			?>
            <div class="<?php echo $i > 0 ? 'productBlock' : 'productBlock0'; ?>">
                <div class="productImage">
                    <a href="<?php echo $productPage . '/celebrity/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                </div>
                <div class="productContent">
                    <div class="productTitle"><a href="<?php echo $productPage . '/celebrity/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><?php echo $record['keyword']; ?></span></a></div>
                    <div class="productDescription"><?php
                        if (strlen($record['description']) > 200)
                        {
                            echo substr($record['description'], 0, 200) . '...';
                        }
                        else
                        {
                            echo $record['description'];
                        }
                        ?>
                    </div>
                    <div class="productBrandMerchant">
                        <?php
                        if($record['brand'] && !$filterBrand)
                        {
                            echo '<span class="brandIn"><u>Brand</u>: <a href="' . str_replace('/page/' . $pageNumber, '', $celeSubmit) . '/brand/' . rawurlencode($record['brand']) . '"><cite>' . $record['brand'] . '</cite></a></span>';
                        }
                        if($record['merchant'] && !$filterMerchant)
                        {
                            echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . str_replace('/page/' . $pageNumber, '', $celeSubmit) . '/merchant/' . rawurlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
                        }
                        ?>
                    </div>
                </div>
                <div class="productEnd">
                    <?php
                    if(empty($record['price_sale']) || $record['price'] <= $record['price_sale'])
                    {
                        //we don't do anything
                        ?>
                        <div class="productPriceNoSale"><span><?php echo '$' . $record['price']; ?></span></div>
                        <?php
                    }
                    //otherwise strike-through Price and list the Price_Sale
                    else
                    {
                        ?>
                        <div class="productPrice"><span>$<?php echo $record['price']?></span></div>
                        <div class="productPriceSale"><span>$<?php echo $record['price_sale']?></span></div>
                        <?php
                    }
                    ?>
                    <form style="margin:0;" action="<?php echo $record['affiliate_url'] . '" target="' . $target; ?>" method="POST" rel="nofollow">
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
    // Gets the count of results for Pagination
    $productCount = count($results);

    // Pagination limit, can be changed
    $limit = !$options['Pagination_Limit'] ? 15 : $options['Pagination_Limit'];

    $pages = round($productCount / $limit, 0);

    if ($pageNumber  < 1)
    {
        $pageNumber  = 1;
    }
    else if ($pageNumber  > ceil(($productCount + 1) / $limit))
    {
        $pageNumber  = ceil(($productCount + 1) / $limit);
    }

    $limitLower = ($pageNumber  - 1) * $limit;

    // Breaks the array into smaller chunks for each page depending on $limit
    $results = array_slice($results, $limitLower, $limit, true);

	if ($celeb)
	{
		echo '<div class="totalFound">' . $totalFound . ' results for <b>' . ucwords($celebDecode ? ($celebQuery ? $celebDecode . ' + ' . $celebQuery : $celebDecode) : ($celebQuery ? rawurldecode($celebQuery) : ($filterBrand ? rawurldecode($filterBrand) : rawurldecode($filterMerchant)))) . '</b>' . (($celebQuery && $celebDecode) ? '<a style="font-size:11px;margin-top:-5px;" href=' . str_replace('/celebQuery/' . $celebQuery, '', $celeSubmit) . '> [x]</a>' : '') . '</div>';
	?>

    <div class="prosper_priceSorter" style="margin-top:10px;">
        <form class="sorterofprice" name="priceSorter" method="POST" action="" >
            <label for="PriceSort">Sort By: </label>
            <select name="sort" onChange="priceSorter.submit();">
                <option value="rel">Relevancy</option>
                <option <?php echo ($sort == 'desc' ? 'selected="true"' : ''); ?> value="desc">Price: High to Low</option>
                <option <?php echo ($sort == 'asc' ? 'selected="true"' : ''); ?> value="asc">Price: Low to High</option>
            </select>
            <?php echo ($sort != 'rel' && '' != $sort) ? '<a style="font-size:11px;margin-top:-5px;" href=' . str_replace(array('/page/' . $pageNumber, '/sort/' . $sort), array('', ''), $celeSubmit) . '> [x]</a>' : ''; ?>
        </form>
    </div>

    <div id="productList">
        <?php
		
        // Loop to return Products and corresponding information
        foreach ($results as $i => $record)
        {
            $record['image_url'] 	 = $options['Image_Masking'] ? $productPage  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $record['image_url']);
            $record['affiliate_url'] = $options['URL_Masking'] ? $productPage . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) : $record['affiliate_url'];
			?>
            <div class="<?php echo $i > 0 ? 'productBlock' : 'productBlock0'; ?>">
                <div class="productImage">
                    <a href="<?php echo $productPage . '/celebrity/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><img src="<?php echo $record['image_url']; ?>" title="<?php echo $record['keyword']?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                </div>
                <div class="productContent">
                    <div class="productTitle"><a href="<?php echo $productPage . '/celebrity/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><?php echo $record['keyword']?></span></a></div>
                    <div class="productDescription"><?php
                        if (strlen($record['description']) > 200)
                        {
                            echo substr($record['description'], 0, 200) . '...';
                        }
                        else
                        {
                            echo $record['description'];
                        }
                        ?>
                    </div>
                </div>
                <div class="productEnd">
                    <?php
                    if(empty($record['price_sale']) || $record['price'] <= $record['price_sale'])
                    {
                        //we don't do anything
                        ?>
                        <div class="productPriceNoSale"><span><?php echo '$' . $record['price']; ?></span></div>
                        <?php
                    }
                    //otherwise strike-through Price and list the Price_Sale
                    else
                    {
                        ?>
                        <div class="productPrice"><span>$<?php echo $record['price']; ?></span></div>
                        <div class="productPriceSale"><span>$<?php echo $record['price_sale']; ?></span></div>
                        <?php
                    }
                    ?>
                    <form style="margin:0;" action="<?php echo $record['affiliate_url'] . '" target="' . $target; ?>" method="POST" rel="nofollow">
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
}

prosper_pagination($pages, $pageNumber);
