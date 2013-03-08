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
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if(!is_plugin_active('jetpack/jetpack.php'))
{
    function prosper_pagination($pages = '', $range)
    {
        global $paged, $wp_query;
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
            if($paged > 2 && $paged <= $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
            if($paged > 1) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";

            for ($i=1; $i <= $pages; $i++)
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
}

$options = $this->options();

$sort           = isset($_GET['sort']) ? $_GET['sort'] : $options['Default_Sort'];
$filterMerchant = stripslashes($_GET['merchant']);
$filterBrand    = stripslashes($_GET['brand']);
$pageNumber     = preg_replace('/(.*)(\/page\/)(\d+)(\/.*)/i', '$3', $_SERVER['REQUEST_URI']);
$celeb          = $_GET['celeb'];
$type           = $_GET['type'];
$target 	    = $options['Target'] ? '_blank' : '_self';

if (!isset($_GET['q']) && $options['Starting_Query'])
{
    if (preg_match('/\?/' , $_SERVER['REQUEST_URI']))
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&q=' . $options['Starting_Query'];
        $q = $options['Starting_Query'];
    }
    else
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?q=' . $options['Starting_Query'];
        $q = $options['Starting_Query'];
    }
}
else
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $q = $_GET['q'];
}

$submitUrl = preg_replace('/\/page\/\d+/i', '', $url);
$newUrl = str_replace(array('?type=' . $type, '&type=' . $type), array('?', ''), $url);

if (!preg_match('/\?/', $newUrl))
{
    $newUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['[REDIRECT_URL'] . '?' . $query;
}

$query = stripslashes($q);

if($options['Negative_Brand'])
{
    $minusBrands = explode(',', stripslashes($options['Negative_Brand']));

    foreach ($minusBrands as $negative)
    {
        $negativeBrands[] = '!' . trim($negative);
    }

    array_unshift($negativeBrands, $filterBrand);
}
if ($options['Negative_Merchant'])
{
    $minusMerchants = explode(',', stripslashes($options['Negative_Merchant']));

    foreach ($minusMerchants as $negative)
    {
        $negativeMerchants[] = '!' . trim($negative);
    }

    array_unshift($negativeMerchants, $filterMerchant);
}

if ('prod' == $type || empty($type))
{
    /*
    /  Prosperent API Query
    */
    require_once('Prosperent_Api.php');
    $prosperentApi = new Prosperent_Api(array(
        'api_key'        => $options['Api_Key'],
        'query'          => $query,
        'limit'          => $options['Api_Limit'],
        'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
        'sortPrice'	     => $sort,
        'groupBy'	     => 'productId',
        'enableFacets'   => $options['Enable_Facets'],
        'filterBrand'    => !$negativeBrands ? $filterBrand : $negativeBrands,
        'filterMerchant' => !$negativeMerchants ? $filterMerchant : $negativeMerchants
    ));

    /*
    /  Fetching results and pulling back all data
    /  To see which data is available to pull back login in to
    /  Prosperent.com and click the API tab
    */
    $prosperentApi -> fetch();
    $results = $prosperentApi -> getAllData();

    $totalFound = $prosperentApi -> getTotalRecordsFound();
    $facets = $prosperentApi -> getFacets();

    echo '<div class="typeselector" style="display:inline-block;margin-top:9px;">';
    echo '<span>Products</span>&nbsp;|';
    echo '&nbsp;<a href="' . $submitUrl . '&type=coup">Coupons</a>';
    if ($options['Celebrity_Endpoint'])
    {
        echo '&nbsp;|&nbsp;<a href="' . $submitUrl . '&type=cele">Celebrity</a>';
    }
    echo '</div>';
    ?>

    <div style="float:right;">
        <form id="searchform" method="GET" action="<?php echo $submitUrl; ?>" style="margin:0;">
            <input type="hidden" name="brand" value="<?php echo $filterBrand;?>">
            <input type="hidden" name="merchant" value="<?php echo $filterMerchant;?>">
            <input type="hidden" name="type" value="<?php echo !$type ? 'prod' : $type; ?>">
            <input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 6px;">
            <input type="submit" value="Search" style="padding:5px; font-size:12px;">
        </form>
    </div>
    <?php
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
                            echo '<a href=' . str_replace(array('&brand=', '?brand='), array('', '?'), $submitUrl) . '&brand=' . urlencode($brand['value']) . '>' . $brand['value'] . ' (' . $brand['count'] . ')</a>';

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
                                <a onclick="toggle_visibility('brandList'); toggle_hidden('moreBrands'); toggle_visibility('hideBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreBrands" style="display:block;">More Brands <img src="<?php echo plugins_url('/img/arrow_down_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                                <a onclick="toggle_hidden('brandList'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="hideBrands" style="display:none;">Hide Brands <img src="<?php echo plugins_url('/img/arrow_up_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                                <?php
                            }
                            else
                            {
                                ?>
                                </br>
                                <a onclick="toggle_visibility('brandList'); toggle_hidden('merchantList'); toggle_hidden('moreBrands'); toggle_visibility('hideBrands'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreBrands" style="display:block;">More Brands <img src="<?php echo plugins_url('/img/arrow_down_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                                <a onclick="toggle_hidden('brandList'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="hideBrands" style="display:none;">Hide Brands <img src="<?php echo plugins_url('/img/arrow_up_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                                <?php
                            }
                        }
                    }
                    else
                    {
                        echo '<div style="min-height:35px;">';
                        echo $filterBrand;
                        echo '</br><a href=' . str_replace(array('&brand=' . urlencode($filterBrand), '?brand=' . urlencode($filterBrand)), array('', '?'), $submitUrl) . '>clear filter</a>';
                        echo '<div style="margin-top:-50px;padding-left:150px;"><img src="http://img1.prosperent.com/images/brandlogos/120x60/' . urlencode($filterBrand) . '.png" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
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
                            echo '<a href=' . str_replace(array('&merchant=', '?merchant='), array('', '?'), $submitUrl) . '&merchant=' . urlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a>';

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
                                <a onclick="toggle_visibility('merchantList'); toggle_hidden('moreMerchants'); toggle_visibility('hideMerchants'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreMerchants" style="display:block;">More Merchants <img src="<?php echo plugins_url('/img/arrow_down_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                                <a onclick="toggle_hidden('merchantList'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); " style="cursor:pointer; font-size:12px;"><span id="hideMerchants" style="display:none;">Hide Merchants <img src="<?php echo plugins_url('/img/arrow_up_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                                <?php
                            }
                            else
                            {
                                ?>
                                </br>
                                <a onclick="toggle_visibility('merchantList'); toggle_hidden('brandList'); toggle_hidden('moreMerchants'); toggle_visibility('hideMerchants'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreMerchants" style="display:block;">More Merchants <img src="<?php echo plugins_url('/img/arrow_down_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                                <a onclick="toggle_hidden('merchantList'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); " style="cursor:pointer; font-size:12px;"><span id="hideMerchants" style="display:none;">Hide Merchants <img src="<?php echo plugins_url('/img/arrow_up_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
                                <?php
                            }
                        }
                    }
                    else
                    {
                        echo '<div style="min-height:35px;">';
                        echo $filterMerchant;
                        echo '</br><a href=' . str_replace(array('&merchant=' . urlencode($filterMerchant), '?merchant=' . urlencode($filterMerchant)), array('', '?'), $submitUrl) . '>clear filter</a>';
                        echo '<div style="margin-top:-50px;padding-left:150px;"><img src="http://img1.prosperent.com/images/logos/120x60/' . urlencode($filterMerchant) . '.png" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
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

                    echo '<td style="width:1%; padding:5px; height:30px;"><a href=' . str_replace(array('&brand=', '?brand='), array('', '?'), $submitUrl) . '&brand=' . urlencode($brand['value']) . '>' . $brand['value'] . ' (' . $brand['count'] . ')</a></td>';

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

                    echo '<td style="padding:5px; height:30px; width:1%;"><a href=' . str_replace(array('&merchant=', '?merchant='), array('', '?'), $submitUrl) . '&merchant=' . urlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a></td>';

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
        <div class="table-seperator"></div>
        <?php
    }

    /*
    /  If no results, or the user clicked search when 'Search Products...'
    /  was in the search field, displays 'No Results'
    */
    if (empty($results))
    {
        echo '<div class="noResults">No Results</div>';
        ?>
        <div style="padding:10px 0;">
            <form id="searchform" method="GET" action="" style="margin:0;">
                <input type="hidden" name="brand" value="<?php echo $filterBrand; ?>">
                <input type="hidden" name="merchant" value="<?php echo $filterMerchant; ?>">
                <input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 6px;">
                <input class="submit" type="submit" value="Search" style="padding:5px;">
            </form>
        </div>
        <?php
        if ($filterBrand || $filterMerchant)
        {
            echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace(array('&merchant=' . urlencode($filterMerchant), '?merchant=' . urlencode($filterMerchant), '&brand=' . urlencode($filterBrand), '?brand=' . urlencode($filterBrand)), array('', '?', '', '?'), $submitUrl) . '>clear the filter(s)</a>.</div>';
        }
        else
        {
            echo '<div class="noResults-secondary">Please try your search again</div>';
        }
        echo '<div class="noResults-padding"></div>';
    }
    else
    {
        echo '<div class="totalFound">' . $totalFound . ' results for <b>' . strtolower($query) . '</b></div>';
        ?>

        <form name="priceSorter" method="GET" action="<?php echo $submitUrl; ?>" style="margin:0; float:right; padding:4px 13px 4px 0;">
            <input type="hidden" name="q" value="<?php echo $query;?>">
            <input type="hidden" name="brand" value="<?php echo $filterBrand;?>">
            <input type="hidden" name="merchant" value="<?php echo $filterMerchant;?>">
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <label for="PriceSort" style="padding-right:4px; font-size:14px; float:left;">Sort By: </label>
            <select name="sort" onChange="priceSorter.submit();" style="display:inline;">
                <option> -- Select Option -- </option>
                <option value="">Relevancy</option>
                <option value="desc">Price: High to Low</option>
                <option value="asc">Price: Low to High</option>
            </select>
        </form>
        </br>

        <?php
        if (!is_plugin_active('jetpack/jetpack.php'))
        {
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
        }
        ?>

        <div id="productList">
            <?php
            // Loop to return Products and corresponding information
            foreach ($results as $i => $record)
            {
                $record['image_url'] = preg_replace('/\/images\/250x250\//', '/images/125x125/', $record['image_url'])
                ?>
                <div class="<?php echo count($results) >= 2 ? 'productBlock' : 'productBlock0'; ?>">
                    <div class="productImage">
                        <a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>"><span><img src="<?php echo $record['image_url']; ?>"  alt="<?php echo $record['keyword']; ?>" title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
                    </div>
                    <div class="productContent">
                        <div class="productTitle"><a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>"><span><?php echo $record['keyword']; ?></span></a></div>
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
                                echo '<span class="brandIn"><u>Brand</u>: <a href="' . str_replace(array('&brand=', '?brand='), array('', '?'), $submitUrl) . '&brand=' . urlencode($record['brand']) . '"><cite>' . $record['brand'] . '</cite></a></span>';
                            }
                            if($record['merchant'] && !$filterMerchant)
                            {
                                echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . str_replace(array('&merchant=', '?merchant='), array('', '?'), $submitUrl) . '&merchant=' . urlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
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
                        <form style="margin:0;" action="<?php echo $record['affiliate_url'] . '" target="' . $target; ?>" method="POST">
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

elseif ('coup' == $type)
{
    /*
    /  Prosperent API Query
    */
    require_once('Prosperent_Api.php');
    $prosperentApi = new Prosperent_Api(array(
        'api_key'        => $options['Api_Key'],
        'query'          => $query,
        'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
        'limit'          => $options['Api_Limit'],
        'sortPrice'	 	 => $sort,
        'enableFacets'   => $options['Enable_Facets'],
        'filterMerchant' => !$negativeMerchants ? $filterMerchant : $negativeMerchants
    ));

    /*
    /  Fetching results and pulling back all data
    /  To see which data is available to pull back login in to
    /  Prosperent.com and click the API tab
    */
    $prosperentApi -> fetchCoupons();
    $results = $prosperentApi -> getAllData();
    $facets = $prosperentApi -> getFacets();
    $totalFound = $prosperentApi -> getTotalRecordsFound();

    echo '<div class="typeselector" style="display:inline-block;margin-top:9px;">';
    echo '<a href="' . $submitUrl . '&type=prod">Products</a>&nbsp;|';
    echo '&nbsp;<span>Coupons</span>';
    if ($options['Celebrity_Endpoint'])
    {
        echo '&nbsp;|&nbsp;<a href="' . $submitUrl . '&type=cele">Celebrity</a>';
    }

    echo '</div>';
    ?>

    <div style="padding-bottom:10px; float:right;">
        <form id="searchform" method="GET" action="<?php echo $submitUrl; ?>" style="margin:0;">
            <input type="hidden" name="merchant" value="<?php echo $filterMerchant;?>">
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Coupons' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 6px;">
            <input class="submit" type="submit" value="Search" style="padding:5px;">
        </form>
    </div>

    <?php
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
                            echo '<a href=' . str_replace(array('&merchant=', '?merchant='), array('', '?'), $submitUrl) . '&merchant=' . urlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a>';

                            if ($i < ($count - 1))
                            {
                                echo ', ';
                            }
                        }
                    }
                    else
                    {
                        echo '<div style="min-height:35px;">';
                        echo $filterMerchant;
                        echo '</br><a href=' . str_replace(array('&merchant=' . urlencode($filterMerchant), '?merchant=' . urlencode($filterMerchant)), array('', '?'), $submitUrl) . '>clear filter</a>';
                        echo '<div style="margin-top:-50px;padding-left:150px;"><img src="http://img1.prosperent.com/images/logos/120x60/' . urlencode($filterMerchant) . '.png" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
                        echo '</div>';
                    }
                    ?>
                </td>
            </tr>
        </table>
        <?php
    }

    /*
    /  If no results, or the user clicked search when 'Search Products...'
    /  was in the search field, displays 'No Results'
    */
    if (empty($results))
    {
        echo '<div class="noResults">No Results</div>';

        ?>
        <div style="padding:10px 0;">
            <form id="searchform" method="GET" action="" style="margin:0;">
                <input type="hidden" name="merchant" value="<?php echo $filterMerchant;?>">
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Coupons' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 6px;">
                <input class="submit" type="submit" value="Search" style="padding:5px;">
            </form>
        </div>
        <?php
        if ($filterBrand || $filterMerchant)
        {
            echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace(array('&merchant=' . urlencode($filterMerchant), '?merchant=' . urlencode($filterMerchant), '&brand=' . urlencode($filterBrand), '?brand=' . urlencode($filterBrand)), array('', '?', '', '?'), $submitUrl) . '>clear the filter(s)</a>.</div>';
        }
        else
        {
            echo '<div class="noResults-secondary">Please try your search again</div>';
        }
        echo '<div class="noResults-padding"></div>';
    }
    else
    {
        echo '<div class="totalFound">' . $totalFound . ' coupons for <b>' . strtolower($query) . '</b></div>';

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

        <div id="couponList">
            <?php
            // Loop to return coupons and corresponding information
            foreach ($results as $i => $record)
            {
                ?>
                <div class="<?php echo count($results) >= 2 ? 'couponBlock' : 'couponBlock0'; ?>">
                    <div class="couponImage">
                        <?php
                        echo '<a href="' . $record['affiliate_url'] . '" target="' . $target . '"><img src="' . $record['image_url'] . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"></a>';
                        ?>
                    </div>
                    <div class="couponContent">
                        <div class="couponTitle">
                            <?php
                            echo '<a href="' . $record['affiliate_url'] . '" target="' . $target . '">' . $record['keyword'] . '</a>';
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
                        <form style="margin:0;" method="POST" action="<?php echo $record['affiliate_url'] . '" target="' . $target; ?>">
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

elseif ('cele' == $type)
{
    /*
    /  Prosperent API Query
    */
    require_once('Prosperent_Api.php');
    $prosperentApi = new Prosperent_Api(array(
        'api_key'         => $options['Api_Key'],
        'filterCelebrity' => $celeb,
        'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
        'limit'           => $options['Api_Limit'],
        'sortPrice'	   	  => $sort,
        'enableFacets'    => $options['Enable_Facets'],
    ));

    $celebrityApi = new Prosperent_Api(array(
        'api_key'         => $options['Api_Key'],
        'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
        'limit'           => 500,
        'sortPrice'	   	  => 'celebrity asc'
    ));

    /*
    /  Fetching results and pulling back all data
    /  To see which data is available to pull back login in to
    /  Prosperent.com and click the API tab
    */
    $prosperentApi -> fetch();
    $results = $prosperentApi -> getAllData();
    $totalFound = $prosperentApi -> getTotalRecordsFound();
    $celebrityApi->fetchCelebrities();
    $celebrityResults = $celebrityApi -> getData();

    echo '<div class="typeselector" style="display:inline-block;margin-top:9px;">';
    echo '<a href="' . $submitUrl . '&type=prod">Products</a>&nbsp;|';
    echo '&nbsp;<a href="' . $submitUrl . '&type=coup">Coupons</a>&nbsp;|';
    echo '&nbsp;<span>Celebrity</span>';
    echo '</div>';
    ?>

    <div style="padding:10px 0; float:right;">
        <form id="searchform" method="GET" action="<?php echo $submitUrl; ?>" style="margin:0;">
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <input class="field" type="text" name="celeb" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Celebrity' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 6px;">
            <input class="submit" type="submit" value="Search" style="padding:5px;">
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
                        echo '<a style="font-size:12px;" href="' . str_replace(array('?q=' . urlencode($query), '&q=' . urlencode($query)), array('?', ''), $submitUrl) . '&celeb=' . urlencode($celebs['celebrity']) . '">' . $celebs['celebrity'] . '</a><span style="font-size:12px; font-weight:bold;"> | </span>';
                    }
                }
                else
                {
                    echo '<div style="min-height:35px;">';
                    echo $celeb;
                    echo '</br><a href=' . str_replace(array('&celeb=' . urlencode($celeb), '?celeb=' . urlencode($celeb)), array('', '?'), $submitUrl) . ' >clear filter</a>';
                    echo '<div style="margin-top:-50px;padding-left:150px;"><img src="http://img1.prosperent.com/images/celebrity/100x100/' . urlencode($celeb) . '.jpg" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
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
    if (empty($results))
    {
        echo '<div class="noResults">No Results</div>';
        ?>
        <div style="width:200px; padding-bottom:10px;">
            <form id="searchform" method="GET" action="">
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <input class="field" type="text" name="celeb" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Celebrity' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 6px;">
                <input class="submit" type="submit" value="Search" style="padding:5px;">
            </form>
        </div>
        <?php
        if ($filterBrand || $filterMerchant)
        {
            echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace(array('&merchant=' . urlencode($filterMerchant), '?merchant=' . urlencode($filterMerchant), '&brand=' . urlencode($filterBrand), '?brand=' . urlencode($filterBrand)), array('', '?', '', '?'), $submitUrl) . '>try another celebrity</a>.</div>';
        }
        else
        {
            echo '<div class="noResults-secondary">Please try your search again</div>';
        }
        echo '<div class="noResults-padding"></div>';
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

        echo '<div class="totalFound">' . $totalFound . ' results for <b>' . ucwords($celeb) . '</b></div>';
        ?>

        <form name="priceSorter" method="GET" action="<?php echo $submitUrl; ?>" style="margin:0; float:right; padding:4px 13px 4px 0;">
            <input type="hidden" name="celeb" value="<?php echo $celeb;?>">
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <label for="PriceSort" style="padding-right:4px; font-size:14px; float:left;">Sort By: </label>
            <select name="sort" onChange="priceSorter.submit();" style="display:inline;">
                <option> -- Select Option -- </option>
                <option value="">Relevancy</option>
                <option value="desc">Price: High to Low</option>
                <option value="asc">Price: Low to High</option>
            </select>
        </form>

        <div id="productList">
            <?php
            // Loop to return Products and corresponding information
            foreach ($results as $i => $record)
            {
                $record['image_url'] = preg_replace('/\/images\/250x250\//', '/images/125x125/', $record['image_url'])
                ?>
                <div class="<?php echo count($results) >= 2 ? 'productBlock' : 'productBlock0'; ?>">
                    <div class="productImage">
                        <a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>"><span><img src="<?php echo $record['image_url']?>"  alt="<?php echo $record['keyword']?>" title="<?php echo $record['keyword']?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
                    </div>
                    <div class="productContent">
                        <div class="productTitle"><a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>"><span><?php echo $record['keyword']?></span></a></div>
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
                        <form style="margin:0;" action="<?php echo $record['affiliate_url'] . '" target="' . $target; ?>" method="POST">
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
if(!is_plugin_active('jetpack/jetpack.php'))
{
    prosper_pagination($pages, $pages);
}
