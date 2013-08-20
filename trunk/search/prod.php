<?php
if (!$query && !$filterBrand && !$filterMerchant && $options['Starting_Query'])
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url = preg_replace('/\/$/', '', $url);
    $url .= '/query/' . htmlentities(urlencode($options['Starting_Query']));
    $q = $options['Starting_Query'];
}
else
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $q = $query;
}

$query = stripslashes($q);

$prodSubmit = preg_replace('/\/$/', '', $url);
$newQuery = str_replace(array('/query/' . $query, '/query/' . urlencode($query), '/page/' . $pageNumber), array('', '', ''), $prodSubmit);
$newSort = str_replace(array('/sort/' . $sendParams['sort'], '/page/' . $pageNumber), array('', ''), $prodSubmit);

if ($_POST['q'])
{
    header('Location: ' . $newQuery . '/query/' . htmlentities(urlencode($_POST['q'])));
    exit;
}

if ($_POST['sort'])
{
    header('Location: ' . $newSort . '/sort/' . $_POST['sort']);
    exit;
}

/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
    'api_key'        => $options['Api_Key'],
    'query'          => $query,
    'limit'          => $options['Api_Limit'],
    'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
    'sortPrice'	     => $sort,
    'groupBy'	     => 'productId',
    'enableFacets'   => $options['Enable_Facets'],
    'filterBrand'    => $filterBrands,
    'filterMerchant' => $filterMerchants
));

/*
/  Fetching results and pulling back all data
*/
switch ($options['Country'])
{
    case 'UK':
        $prosperentApi -> fetchUkProducts();
        $currency = 'GBP';
        break;
    case 'CA':
        $prosperentApi -> fetchCaProducts();
        $currency = 'CAD';
        break;
    default:
        $prosperentApi -> fetchProducts();
        $currency = 'USD';
        break;
}
$results = $prosperentApi -> getAllData();

$totalFound = $prosperentApi -> getTotalRecordsFound();
$facets = $prosperentApi -> getFacets();

echo $typeSelector;
?>

<div class="prosper_searchform">
    <form class="searchform" method="POST" action="">
        <input class="prosper_field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>">
        <input class="prosper_submit" type="submit" value="Search">
    </form>
</div>
<?php
/*
/  If no results, or the user clicked search when 'Search Products...'
/  was in the search field, displays 'No Results'
*/
if (empty($results))
{
    header( $_SERVER['SERVER_PROTOCOL']." 404 Not Found", true, 404 );
    echo '<div class="noResults">No Results</div>';

    if ($filterBrand || $filterMerchant)
    {
        echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace(array('/merchant/' . $filterMerchant, '/brand/' . $filterBrand), array('', ''), $prodSubmit) . '>clear the filter(s)</a>.</div>';
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
    require_once(PROSPER_PATH . 'Prosperent_Api.php');
    $api = new Prosperent_Api(array(
        'enableFacets' => 'productId'
    ));

    $api->setDateRange('commission', $startRange, $endRange)
        ->fetchTrends();

    // set productId as key in array
    foreach ($api->getFacets('productId') as $data)
    {
        $keys[] = $data['value'];
    }

    // fetch merchant data from api
    $api = new Prosperent_Api(array(
        'api_key'         => $options['Api_Key'],
        'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
        'filterProductId' => $keys,
        'limit' 	      => 15
    ));

    $api->fetch();
    $results = $api->getAllData() ;

    echo '<div class="totalFound">Browse these <strong>trending products</strong></div>';
    ?>
    <div id="productList">
        <?php
        // Loop to return Products and corresponding information
        foreach ($results as $i => $record)
        {
            $record['image_url'] = $options['Image_Masking'] ? $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $record['image_url']);
            ?>
            <div class="<?php echo count($results) >= 2 ? 'productBlock' : 'productBlock0'; ?>">
                <div class="productImage">
                    <a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
                </div>
                <div class="productContent">
                    <div class="productTitle"><a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><?php echo $record['keyword']; ?></span></a></div>
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
                            echo '<span class="brandIn"><u>Brand</u>: <a href="' . str_replace('/page/' . $pageNumber, '', $prodSubmit) . '/brand/' . urlencode($record['brand']) . '"><cite>' . $record['brand'] . '</cite></a></span>';
                        }
                        if($record['merchant'] && !$filterMerchant)
                        {
                            echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . str_replace('/page/' . $pageNumber, '', $prodSubmit) . '/merchant/' . urlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
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
                        <div class="productPriceNoSale"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $record['price']; ?></span></div>
                        <?php
                    }
                    //otherwise strike-through Price and list the Price_Sale
                    else
                    {
                        ?>
                        <div class="productPrice"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $record['price']?></span></div>
                        <div class="productPriceSale"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $record['price_sale']?></span></div>
                        <?php
                    }
                    ?>
                    <form style="margin:0;" action="<?php echo $productPage . '/store/go/' . urlencode(str_replace('/', ',SL,', $record['affiliate_url'])) . '" target="' . $target; ?>" method="POST">
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
    $brands = $facets['brand'];
    $merchants = $facets['merchant'];

    if ($brands)
    {
        $brands1 = array_splice($brands, 0, $options['Brand_Facets'] ? $options['Brand_Facets'] : 10);
        $brands2 = $brands;

        $brandNames = array();
        foreach ($brands2 as $brand)
        {
            $brandNames[] = ucfirst($brand['value']);
        }

        array_multisort($brandNames, SORT_REGULAR, $brands2);
    }

    if ($merchants)
    {
        $merchants1 = array_splice($merchants, 0, $options['Merchant_Facets'] ? $options['Merchant_Facets'] : 10);
        $merchants2 = $merchants;

        $merchantNames = array();
        foreach ($merchants2 as $merchant)
        {
            $merchantNames[] = ucfirst($merchant['value']);
        }

        array_multisort($merchantNames, SORT_STRING, $merchants2);
    }

    ?>
    <table id="facets">
        <tr>
            <td class="brands">
                <?php
                echo (empty($filterBrand) ? '<div class="browseBrands">Browse by Brand: </div>' : '<div class="filteredBrand">Filtered by Brand: </div>');
                if (empty($facets['brand']) && !$filterBrand)
                {
                    echo '<div class="noBrands">No Brands Found</div>';
                }
                else if (!$filterBrand)
                {
                    $count = count($brands1);
                    foreach ($brands1 as $i => $brand)
                    {
                        echo '<a href=' . str_replace('/page/' . $pageNumber, '', $prodSubmit) . '/brand/' . urlencode($brand['value']) . '>' . $brand['value'] . ' (' . $brand['count'] . ')</a>';

                        if ($i < ($count - 1))
                        {
                            echo ', ';
                        }
                    }
                    if ($brands2)
                    {
                        if ($filterMerchant)
                        {
                            ?>
                            </br>
                            <a onclick="toggle_visibility('brandList'); toggle_hidden('moreBrands'); toggle_visibility('hideBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreBrands" style="display:block;">More Brands <img src="<?php echo PROSPER_URL . 'img/arrow_down_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <a onclick="toggle_hidden('brandList'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="hideBrands" style="display:none;">Hide Brands <img src="<?php echo PROSPER_URL . 'img/arrow_up_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <?php
                        }
                        else
                        {
                            ?>
                            </br>
                            <a onclick="toggle_visibility('brandList'); toggle_hidden('merchantList'); toggle_hidden('moreBrands'); toggle_visibility('hideBrands'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreBrands" style="display:block;">More Brands <img src="<?php echo PROSPER_URL . 'img/arrow_down_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <a onclick="toggle_hidden('brandList'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="hideBrands" style="display:none;">Hide Brands <img src="<?php echo PROSPER_URL . 'img/arrow_up_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <?php
                        }
                    }
                }
                else
                {
                    echo '<div style="min-height:35px;">';
                    echo urldecode($filterBrand);
                    echo '</br><a href=' . str_replace(array('/page/' . $pageNumber, '/brand/' . $filterBrand), array('', ''), $prodSubmit) . '>clear filter</a>';
                    echo '<div style="margin-top:-50px;padding-left:150px;"><img src="' . ($options['Image_Masking'] ? $productPage  . '/img/' . urlencode(str_replace('/', ',SL,',  ('brandlogos/120x60/' . $filterBrand . '.png'))) : 'http://img1.prosperent.com/images/brandlogos/120x60/' . $filterBrand . '.png') . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
                    echo '</div>';
                }
                ?>
            </td>
            <td class="merchants">
                <?php
                echo (empty($filterMerchant) ? '<div class="browseMerchants">Browse by Merchant: </div>' : '<div class="filteredMerchants">Filtered by Merchant: </div>');

                if (empty($facets['merchant']) && !$filterMerchant)
                {
                    echo '<div class="noMerchants">No Merchants Found</div>';
                }
                else if (!$filterMerchant)
                {
                    $count = count($merchants1);
                    foreach ($merchants1 as $i => $merchant)
                    {
                        echo '<a href=' . str_replace('/page/' . $pageNumber, '', $prodSubmit) . '/merchant/' . urlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a>';

                        if ($i < ($count - 1))
                        {
                            echo ', ';
                        }
                    }
                    if ($merchants2)
                    {
                        if ($filterBrand)
                        {
                            ?>
                            </br>
                            <a onclick="toggle_visibility('merchantList'); toggle_hidden('moreMerchants'); toggle_visibility('hideMerchants'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreMerchants" style="display:block;">More Merchants <img src="<?php echo PROSPER_URL . 'img/arrow_down_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <a onclick="toggle_hidden('merchantList'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); " style="cursor:pointer; font-size:12px;"><span id="hideMerchants" style="display:none;">Hide Merchants <img src="<?php echo PROSPER_URL . 'img/arrow_up_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <?php
                        }
                        else
                        {
                            ?>
                            </br>
                            <a onclick="toggle_visibility('merchantList'); toggle_hidden('brandList'); toggle_hidden('moreMerchants'); toggle_visibility('hideMerchants'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreMerchants" style="display:block;">More Merchants <img src="<?php echo PROSPER_URL . 'img/arrow_down_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <a onclick="toggle_hidden('merchantList'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); " style="cursor:pointer; font-size:12px;"><span id="hideMerchants" style="display:none;">Hide Merchants <img src="<?php echo PROSPER_URL . 'img/arrow_up_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <?php
                        }
                    }
                }
                else
                {
                    echo '<div style="min-height:35px;">';
                    echo urldecode($filterMerchant);
                    echo '</br><a href=' . str_replace(array('/page/' . $pageNumber, '/merchant/' . $filterMerchant), array('', ''), $prodSubmit) . '>clear filter</a>';
                    echo '<div style="margin-top:-50px;padding-left:150px;"><img src="' . ($options['Image_Masking'] ? $productPage  . '/img/' . urlencode(str_replace('/', ',SL,',  ('logos/120x60/' . $filterMerchant . '.png'))) : 'http://img1.prosperent.com/images/logos/120x60/' . $filterMerchant . '.png') . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
                    echo '</div>';
                }
                ?>
            </td>
        </tr>
    </table>
    <?php
    if ($brands2)
    {
        ?>
        <table id="brandList" style="display:none; font-size:11px; width:100%; table-layout:fixed;">
            <?php
            echo '<th style="padding:3px 0 0 5px; font-size:13px;">More Brands: </th>';

            foreach ($brands2 as  $i => $brand)
            {
                if ($i == 0 || $i % 5 == 0 && $i >= 5)
                {
                    echo '<tr>';
                }

                echo '<td style="width:1%; padding:5px; height:30px;"><a href=' . str_replace('/page/' . $pageNumber, '', $prodSubmit) . '/brand/' . urlencode($brand['value']) . '>' . $brand['value'] . ' (' . $brand['count'] . ')</a></td>';

                if ($i % 5 == 4 && $i >= 9)
                {
                    echo '</tr>';
                }
            }
            ?>
        </table>
        <?php
    }
    if ($merchants2)
    {
        ?>
        <table id="merchantList" style="display:none; font-size:11px; width:100%;">
            <?php
            echo '<th style="padding:3px 0 0 5px; font-size:13px;">More Merchants: </th>';

            foreach ($merchants2 as $i => $merchant)
            {
                if ($i == 0 || $i % 4 == 0 && $i >= 4)
                {
                    echo '<tr>';
                }

                echo '<td style="padding:5px; height:30px; width:1%;"><a href=' . str_replace('/page/' . $pageNumber, '', $prodSubmit) . '/merchant/' . urlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a></td>';

                if ($i % 4 == 3 && $i >= 7)
                {
                    echo '</tr>';
                }
            }
            ?>
        </table>
        <?php
    }
    ?>
    <div class="table-seperator"></div>
    <?php
}
else
{
    ?>
    <div class="table-seperator" style="margin-bottom:5px;"></div>
    <?php
}

    echo '<div class="totalFound">' . $totalFound . ' results for <b>' . ucwords($query ? urldecode($query) : ($filterBrand ? urldecode($filterBrand) : urldecode($filterMerchant))) . '</b>' . ($query && ($filterMerchant || $filterBrand) ? '<a style="font-size:11px;margin-top:-5px;" href=' . str_replace(array('/page/' . $pageNumber, '/query/' . $query), array('', ''), $prodSubmit) . '> [x]</a>' : '') . '</div>';
    ?>

    <div class="prosper_priceSorter">
        <form class="sorterofprice" name="priceSorter" method="POST" action="" >
            <label for="PriceSort">Sort By: </label>
            <select name="sort" onChange="priceSorter.submit();">
                <option value="rel">Relevancy</option>
                <option <?php echo ($sort == 'desc' ? 'selected="true"' : ''); ?> value="desc">Price: High to Low</option>
                <option <?php echo ($sort == 'asc' ? 'selected="true"' : ''); ?> value="asc">Price: Low to High</option>
            </select>
            <?php echo ($sort != 'rel' && '' != $sort) ? '<a style="font-size:11px;margin-top:-5px;" href=' . str_replace(array('/page/' . $pageNumber, '/sort/' . $sort), array('', ''), $prodSubmit) . '> [x]</a>' : ''; ?>
        </form>
    </div>

    <?php
    // Gets the count of results for Pagination
    $productCount = count($results);

    // Pagination limit, can be changed
    $limit = !$options['Pagination_Limit'] ? 10 : $options['Pagination_Limit'];

    $pages = round($productCount / $limit, 0);
    $ceiling = ceil(($productCount + 1) / $limit);

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

    <div id="productList">
        <?php
        // Loop to return Products and corresponding information
        foreach ($results as $i => $record)
        {
            $record['image_url'] = $options['Image_Masking'] ? $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $record['image_url']);
            ?>
            <div class="<?php echo count($results) >= 2 ? 'productBlock' : 'productBlock0'; ?>">
                <div class="productImage">
                    <a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
                </div>
                <div class="productContent">
                    <div class="productTitle"><a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><?php echo $record['keyword']; ?></span></a></div>
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
                            echo '<span class="brandIn"><u>Brand</u>: <a href="' . str_replace('/page/' . $pageNumber, '', $prodSubmit) . '/brand/' . urlencode($record['brand']) . '"><cite>' . $record['brand'] . '</cite></a></span>';
                        }
                        if($record['merchant'] && !$filterMerchant)
                        {
                            echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . str_replace('/page/' . $pageNumber, '', $prodSubmit) . '/merchant/' . urlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
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
                        <div class="productPriceNoSale"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $record['price']; ?></span></div>
                        <?php
                    }
                    //otherwise strike-through Price and list the Price_Sale
                    else
                    {
                        ?>
                        <div class="productPrice"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $record['price']?></span></div>
                        <div class="productPriceSale"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $record['price_sale']?></span></div>
                        <?php
                    }
                    ?>
                    <form style="margin:0;" action="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) . '" target="' . $target; ?>" method="POST">
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

prosper_pagination($pages, $sendParams['page']);
