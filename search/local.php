<?php
$states = array(
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

if (!$filterState && $options['Local_Query'])
{
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
            $filterState = $states[$localQuery[0]];
        }

        $url = $url . '/state/' . urlencode($filterState) . '/city/' . urlencode($filterCity);
    }
    elseif (strlen($localQuery[0]) == 2)
    {
        $filterState = $localQuery[0];
        $url = $url . '/state/' . urlencode($filterState);
    }
    else
    {
        $filterState = $states[$localQuery[0]];
        $url = $url . '/state/' . urlencode($filterState);
    }
}

$stateFull = strtolower($_POST['state']);
$state = $states[$stateFull];
$backStates = array_flip($states);

if (empty($state) || !$state || NULL == $state)
{
    $state = 'noResult';
}

$localSubmit = preg_replace('/\/$/', '', $url);
$newQuery = str_replace(array('/city/' . $filterCity, '/state/' . $filterState, '/zip/' . $filterZip, '/page/' . $pageNumber), array('', '', '', ''), $localSubmit);
$newSort = str_replace(array('/sort/' . $sendParams['sort'], '/page/' . $pageNumber), array('', ''), $localSubmit);

if ($_POST['state'])
{
    header('Location: ' . $newQuery . '/state/' . $state);
    exit;
}

if ($_POST['sort'])
{
    header('Location: ' . $newSort . '/sort/' . $_POST['sort']);
    exit;
}

if (empty($filterState) && $options['Geo_Locate'])
{
    require_once(PROSPER_PATH . 'geo/geoplugin.class.php');
    //locate the IP
    $geoplugin = new geoPlugin();
    $geoplugin->locate();

    $filterState = $geoplugin->region;
    $filterCity  = $geoplugin->city;
    header('Location: ' . $newQuery . '/state/' . urlencode($filterState) . '/city/' . urlencode($filterCity));
    exit;
}

/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
    'api_key'        => $options['Api_Key'],
    'limit'          => $options['Api_Limit'],
    'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
    'sortPrice'	     => $sort,
    'enableFacets'   => array('city', 'zipCode'),
    'filterZipCode'  => $filterZip,
    'filterCity'     => $filterCity,
    'filterState'    => $filterState,
    'filterMerchant' => stripslashes($sendParams['merchant'])
));

$prosperentApi -> fetchLocal();
$results = $prosperentApi -> getAllData();

$totalFound = $prosperentApi -> getTotalRecordsFound();
$facets = $prosperentApi -> getFacets();

if ($filterState && ((!$filterCity || !$filterZip) || ($filterCity == 'Online' || $filterZip == 'Online')))
{
    require_once(PROSPER_PATH . 'Prosperent_Api.php');
    $prosperentApi = new Prosperent_Api(array(
        'api_key'        => $options['Api_Key'],
        'limit'          => $options['Api_Limit'],
        'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
        'sortPrice'	     => $sort,
        'enableFacets'   => array('city', 'zipCode'),
        'filterZipCode'  => 'null',
        'filterCity'     => 'null',
        'filterState'    => 'null',
        'filterMerchant' => stripslashes($sendParams['merchant'])
    ));

    $prosperentApi -> fetchLocal();
    $onlineResults = $prosperentApi -> getAllData();

    $totalOnlineFound = $prosperentApi -> getTotalRecordsFound();
    $onlineFacets = $prosperentApi -> getFacets();

    $results = array_merge_recursive($results, $onlineResults);
    $totalFound = $totalFound + $totalOnlineFound;
    $facets = array_merge_recursive($onlineFacets, $facets);
}

echo $typeSelector;
?>
<div class="prosper_searchform">
    <form class="searchform" method="POST" action="">
        <input class="field" type="text" name="state" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>">
        <input class="submit" type="submit" value="Search">
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

    if ($filterCity || $filterMerchant || $filterState || $filterZip)
    {
        echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace(array('/merchant/' . $filterMerchant, '/city/' . $filterCity, '/zip/' . $filterZip, '/state/' . $filterState), array('', '', '', ''), $localSubmit) . '>clear the filter(s)</a>.</div>';
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
        'enableFacets'  => 'keyword',
        'filterCatalog' => 'local'
    ));

    $api->setDateRange('commission', $startRange, $endRange)
        ->fetchTrends();

    // set productId as key in array
    foreach ($api->getFacets('keyword') as $data)
    {
        $keys[] = $data['value'];
    }

    // fetch merchant data from api
    $api = new Prosperent_Api(array(
        'api_key'         => $options['Api_Key'],
        'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
        'filterKeyword'	   => $keys,
        'limit' 	      => 15
    ));

    $api->fetchLocal();
    $results = $api->getAllData() ;

    if($results)
    {
        echo '<div class="totalFound">Browse these <strong>trending local deals</strong></div>';
        ?>

        <div id="productList">
            <?php
            // Loop to return Products and corresponding information
            foreach ($results as $i => $record)
            {
                if($record['city'] == null)
                {
                    $record['city'] = 'Online';
                }
                if($record['zipCode'] == null)
                {
                    $record['zipCode'] = 'Online';
                }

                if (empty($record['merchant']))
                {
                    continue;
                }

                $record['image_url'] = $options['Image_Masking'] ? $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $record['image_url']);
                ?>
                <div class="<?php echo count($results) >= 2 ? 'productBlock' : 'productBlock0'; ?>">
                    <div class="productImage">
                        <a href="<?php echo $productPage . '/local/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['localId']; ?>" ><span><img src="<?php echo $record['image_url']; ?>" title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
                    </div>
                    <div class="productContent">
                        <div class="productTitle"><a href="<?php echo $productPage . '/local/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['localId']; ?>" ><span><?php echo $record['keyword']; ?></span></a></div>
                        <div id="couponList" class="couponExpire"><?php echo preg_match('/no sales campaign/i', $record['promo']) ? '' : $record['promo']; ?></div>
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
                            if ($record['city'] && 'Online' != $record['city'])
                            {
                                echo '<span class="brandIn"><u>City</u>: <a href="' . str_replace(array('/page/' . $pageNumber, '/city/' . $filterCity), array('', ''), $localSubmit) . '/city/' . urlencode($record['city']) . '"><cite>' . $record['city'] . '</cite></a></span>';
                            }
                            if ($record['state'])
                            {
                                echo '<span class="merchantIn"><u>State</u>: <a href="' . str_replace(array('/page/' . $pageNumber, '/state/' . $filterState), array('', ''), $localSubmit) . '/state/' . urlencode($record['state']) . '"><cite>' . ucwords($backStates[$record['state']]) . '</cite></a></span><br>';
                            }
                            if ('Online' == $record['city'] && 'Online' == $record['zipCode'])
                            {
                                echo '<span class="brandIn"><u>State</u>: <a href="#"><cite>Online Offer</cite></a></span>';
                            }
                            if ($record['zipCode'] && 'Online' != $record['zipCode'])
                            {
                                echo '<span class="brandIn"><u>Zip Code</u>: <a href="' . str_replace(array('/page/' . $pageNumber, '/zip/' . $filterZip), array('', ''), $localSubmit) . '/zip/' . urlencode($record['zipCode']) . '"><cite>' . urlencode($record['zipCode']) . '</cite></a></span>';
                            }
                            if ($record['merchant'])
                            {
                                echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . str_replace(array('/page/' . $pageNumber, '/merchant/' . $filterMerchant), array('', ''), $localSubmit) . '/merchant/' . urlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="productEnd">
                        <?php
                        if(empty($record['priceSale']) || $record['price'] <= $record['priceSale'])
                        {
                            //we don't do anything
                            ?>
                            <div class="productPriceNoSale"><span><?php echo '$' . $record['price']; ?></span></div>
                            <?php
                        }
                        //otherwise strike-through Price and list the priceSale
                        else
                        {
                            ?>
                            <div class="productPrice"><span>$<?php echo $record['price']?></span></div>
                            <div class="productPriceSale"><span>$<?php echo $record['priceSale']?></span></div>
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
}
else
{
    $cities = $facets['city'];
    $zipCodes = $facets['zipCode'];

    if ($cities)
    {
        $cities1 = array_splice($cities, 0, $options['Brand_Facets'] ? $options['Brand_Facets'] : 10);
        $cities2 = $cities;

        $cityNames = array();
        foreach ($cities2 as $cityFacet)
        {
            $cityNames[] = ucfirst($cityFacet['value']);
        }

        array_multisort($cityNames, SORT_REGULAR, $cities2);
    }

    if ($zipCodes)
    {
        $zipCodes1 = array_splice($zipCodes, 0, $options['Merchant_Facets'] ? $options['Merchant_Facets'] : 10);
        $zipCodes2 = $zipCodes;

        $zipCodeNames = array();
        foreach ($zipCodes2 as $zipFacets)
        {
            $zipCodeNames[] = ucfirst($zipFacets['value']);
        }

        array_multisort($zipCodeNames, SORT_STRING, $zipCodes2);
    }

    ?>
    <table id="facets">
        <tr>
            <td class="brands">
                <?php
                echo (empty($filterCity) ? '<div class="browseBrands">Browse by City: </div>' : '<div class="filteredBrand">Filtered by City: </div>');
                if (empty($facets['city']) && !$filterCity && !$city)
                {
                    echo '<div class="noBrands">No Cities Found</div>';
                }
                else if (!$filterCity)
                {
                    $count = count($cities1);
                    $countminus = $count - 1;
                    foreach ($cities1 as $i => $cityFacet)
                    {
                        if($cityFacet['value'] == 'null')
                        {
                            $cityFacet['value'] = 'Online';
                        }

                        echo '<a href=' . str_replace('/page/' . $pageNumber, '', $localSubmit) . '/city/' . urlencode($cityFacet['value']) . '>' . $cityFacet['value'] . ' (' . $cityFacet['count'] . ')</a>';

                        if ($i < $countminus)
                        {
                            echo ', ';
                        }
                    }
                    if ($cities2)
                    {
                        if ($filterZipCode)
                        {
                            ?>
                            </br>
                            <a onclick="toggle_visibility('brandList'); toggle_hidden('moreBrands'); toggle_visibility('hideBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreBrands" style="display:block;">More Cities <img src="<?php echo PROSPER_URL . 'img/arrow_down_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <a onclick="toggle_hidden('brandList'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="hideBrands" style="display:none;">Hide Cities <img src="<?php echo PROSPER_URL . 'img/arrow_up_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <?php
                        }
                        else
                        {
                            ?>
                            </br>
                            <a onclick="toggle_visibility('brandList'); toggle_hidden('merchantList'); toggle_hidden('moreBrands'); toggle_visibility('hideBrands'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreBrands" style="display:block;">More Cities <img src="<?php echo PROSPER_URL . 'img/arrow_down_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <a onclick="toggle_hidden('brandList'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="hideBrands" style="display:none;">Hide Cities <img src="<?php echo PROSPER_URL . 'img/arrow_up_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <?php
                        }
                    }
                }
                else
                {
                    echo '<div style="min-height:35px;">';
                    echo urldecode($filterCity);
                    echo '</br><a href=' . str_replace(array('/page/' . $pageNumber, '/city/' . $filterCity), array('', ''), $localSubmit) . '>clear filter</a>';
                    echo '</div>';
                }
                ?>
            </td>
            <td class="merchants">
                <?php
                echo (empty($filterZip) ? '<div class="browseMerchants">Browse by Zip Code: </div>' : '<div class="filteredMerchants">Filtered by Zip Code: </div>');

                if (empty($facets['zipCode']) && !$filterZip)
                {
                    echo '<div class="noMerchants">No Zip Codes Found</div>';
                }
                else if (!$filterZip)
                {
                    $count = count($zipCodes1);
                    $countminus = $count - 1;
                    foreach ($zipCodes1 as $i => $zipFacet)
                    {
                        if($zipFacet['value'] == 'null')
                        {
                            $zipFacet['value'] = 'Online';
                        }

                        echo '<a href=' . str_replace('/page/' . $pageNumber, '', $localSubmit) . '/zip/' . urlencode($zipFacet['value']) . '>' . $zipFacet['value'] . ' (' . $zipFacet['count'] . ')</a>';

                        if ($i < $countminus)
                        {
                            echo ', ';
                        }
                    }
                    if ($zipCodes2)
                    {
                        if ($filterCity)
                        {
                            ?>
                            </br>
                            <a onclick="toggle_visibility('merchantList'); toggle_hidden('moreMerchants'); toggle_visibility('hideMerchants'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreMerchants" style="display:block;">More Zip Codes <img src="<?php echo PROSPER_URL . 'img/arrow_down_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <a onclick="toggle_hidden('merchantList'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); " style="cursor:pointer; font-size:12px;"><span id="hideMerchants" style="display:none;">Hide Zip Codes <img src="<?php echo PROSPER_URL . 'img/arrow_up_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <?php
                        }
                        else
                        {
                            ?>
                            </br>
                            <a onclick="toggle_visibility('merchantList'); toggle_hidden('brandList'); toggle_hidden('moreMerchants'); toggle_visibility('hideMerchants'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreMerchants" style="display:block;">More Zip Codes <img src="<?php echo PROSPER_URL . 'img/arrow_down_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <a onclick="toggle_hidden('merchantList'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); " style="cursor:pointer; font-size:12px;"><span id="hideMerchants" style="display:none;">Hide Zip Codes <img src="<?php echo PROSPER_URL . 'img/arrow_up_small.png'; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                            <?php
                        }
                    }
                }
                else
                {
                    echo '<div style="min-height:35px;">';
                    echo $zip;
                    echo '</br><a href=' . str_replace(array('/page/' . $pageNumber, '/zip/' . $filterZip), array('', ''), $localSubmit) . '>clear filter</a>';
                    echo '</div>';
                }
                ?>
            </td>
        </tr>
    </table>
    <?php
    if ($cities2)
    {
        ?>
        <table id="brandList" style="display:none; font-size:11px; width:100%; table-layout:fixed;">
            <?php
            echo '<th style="padding:3px 0 0 5px; font-size:13px;float:left;">More Cities: </th>';

            foreach ($cities2 as  $i => $cityFacet)
            {
                if($cityFacet['value'] == null)
                {
                    $cityFacet['value'] = 'Online';
                }

                if ($i == 0 || $i % 5 == 0 && $i >= 5)
                {
                    echo '<tr>';
                }

                echo '<td style="width:1%; padding:5px; height:30px;"><a href=' . str_replace('/page/' . $pageNumber, '', $localSubmit) . '/city/' . urlencode($cityFacet['value']) . '>' . $cityFacet['value'] . ' (' . $cityFacet['count'] . ')</a></td>';

                if ($i % 5 == 4 && $i >= 9)
                {
                    echo '</tr>';
                }
            }
            ?>
        </table>
        <?php
    }
    if ($zipCodes2)
    {
        ?>
        <table id="merchantList" style="display:none; font-size:11px; width:100%;">
            <?php
            echo '<th style="padding:3px 0 0 5px; font-size:13px;float:left;">More Zip Codes: </th>';

            foreach ($zipCodes2 as $i => $zipFacet)
            {
                if($zipFacet['value'] == null)
                {
                    $zipFacet['value'] = 'Online';
                }

                if ($i == 0 || $i % 4 == 0 && $i >= 4)
                {
                    echo '<tr>';
                }

                echo '<td style="padding:5px; height:30px; width:1%;"><a href=' . str_replace('/page/' . $pageNumber, '', $localSubmit) . '/zip/' . urlencode($zipFacet['value']) . '>' . $zipFacet['value'] . ' (' . $zipFacet['count'] . ')</a></td>';

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
    echo '<div class="totalFound">' . $totalFound . ' results for <strong>' . (!$filterCity ? (!$filterZip ? (!$filterState ? 'Online Deals' : ('noResult' != $filterState ? ucwords($backStates[$decodeState]) : 'Online Deals')) : ($filterZip == 'Online' ? 'Online Deals' : ('zip code: ' . $zip))) : (($filterCity == 'Online' || $filterZip == 'Online') ? 'Online Deals' : (ucwords($city) . ', ' . ucwords($decodeState)))) . '</strong></div>';
    ?>

    <div class="prosper_priceSorter">
        <form class="sorterofprice" name="priceSorter" method="POST" action="" >
            <label for="PriceSort">Sort By: </label>
            <select name="sort" onChange="priceSorter.submit();">
                <option value="rel">Relevancy</option>
                <option <?php echo ($sort == 'desc' ? 'selected="true"' : ''); ?> value="desc">Price: High to Low</option>
                <option <?php echo ($sort == 'asc' ? 'selected="true"' : ''); ?> value="asc">Price: Low to High</option>
                <option <?php echo ($sort == 'expirationDate+desc' ? 'selected="true"' : ''); ?> value="expirationDate+desc">Expiration Date: Descending</option>
                <option <?php echo ($sort == 'expirationDate+asc' ? 'selected="true"' : ''); ?> value="expirationDate+asc">Expiration Date: Ascending</option>
            </select>
            <?php echo ($sort != 'rel' && '' != $sort) ? '<a style="font-size:11px;margin-top:-5px;" href=' . str_replace(array('/page/' . $pageNumber, '/sort/' . $sort), array('', ''), $localSubmit) . '> [x]</a>' : ''; ?>
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
            if($record['city'] == null)
            {
                $record['city'] = 'Online';
            }
            if($record['zipCode'] == null)
            {
                $record['zipCode'] = 'Online';
            }

            if (empty($record['merchant']))
            {
                continue;
            }

            $record['image_url'] = $options['Image_Masking'] ? $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $record['image_url']);
            ?>
            <div class="<?php echo count($results) >= 2 ? 'productBlock' : 'productBlock0'; ?>">
                <div class="productImage">
                    <a href="<?php echo $productPage . '/local/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['localId']; ?>" ><span><img src="<?php echo $record['image_url']; ?>" title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
                </div>
                <div class="productContent">
                    <div class="productTitle"><a href="<?php echo $productPage . '/local/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['localId']; ?>" ><span><?php echo $record['keyword']; ?></span></a></div>
                    <div id="couponList" class="couponExpire"><?php echo preg_match('/no sales campaign/i', $record['promo']) ? '' : $record['promo']; ?></div>
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
                        if ($record['city'] && 'Online' != $record['city'])
                        {
                            echo '<span class="brandIn"><u>City</u>: <a href="' . str_replace(array('/page/' . $pageNumber, '/city/' . $filterCity), array('', ''), $localSubmit) . '/city/' . urlencode($record['city']) . '"><cite>' . $record['city'] . '</cite></a></span>';
                        }
                        if ($record['state'])
                        {
                            echo '<span class="merchantIn"><u>State</u>: <a href="' . str_replace(array('/page/' . $pageNumber, '/state/' . $filterState), array('', ''), $localSubmit) . '/state/' . urlencode($record['state']) . '"><cite>' . ucwords($backStates[$record['state']]) . '</cite></a></span><br>';
                        }
                        if ('Online' == $record['city'] && 'Online' == $record['zipCode'])
                        {
                            echo '<span class="brandIn"><u>State</u>: <a href="#"><cite>Online Offer</cite></a></span>';
                        }
                        if ($record['zipCode'] && 'Online' != $record['zipCode'])
                        {
                            echo '<span class="brandIn"><u>Zip Code</u>: <a href="' . str_replace(array('/page/' . $pageNumber, '/zip/' . $filterZip), array('', ''), $localSubmit) . '/zip/' . urlencode($record['zipCode']) . '"><cite>' . urlencode($record['zipCode']) . '</cite></a></span>';
                        }
                        if ($record['merchant'])
                        {
                            echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . str_replace(array('/page/' . $pageNumber, '/merchant/' . $filterMerchant), array('', ''), $localSubmit) . '/merchant/' . urlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
                        }
                        ?>
                    </div>
                </div>
                <div class="productEnd">
                    <?php
                    if(empty($record['priceSale']) || $record['price'] <= $record['priceSale'])
                    {
                        //we don't do anything
                        ?>
                        <div class="productPriceNoSale"><span><?php echo '$' . $record['price']; ?></span></div>
                        <?php
                    }
                    //otherwise strike-through Price and list the priceSale
                    else
                    {
                        ?>
                        <div class="productPrice"><span>$<?php echo $record['price']?></span></div>
                        <div class="productPriceSale"><span>$<?php echo $record['priceSale']?></span></div>
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
