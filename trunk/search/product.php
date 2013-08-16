<?php
$base = $options['Base_URL'] ? $options['Base_URL'] : 'products';
$url = site_url('/') . $base;

switch ($options['Country'])
{
    case 'UK':
        $currency = 'GBP';
        break;
    case 'CA':
        $currency = 'CAD';
        break;
    default:
        $currency = 'USD';
        break;
}

/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
    'api_key'         => $options['Api_Key'],
    'limit'           => 1,
    'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
    'enableFacets'    => $options['Enable_Facets'],
    'filterCatalogId' => get_query_var('cid')
));

/*
/  Fetching results and pulling back all data
/  To see which data is available to pull back login in to
/  Prosperent.com and click the API tab
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
$record = $prosperentApi -> getAllData();

/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
    'api_key'         => $options['Api_Key'],
    'limit'           => 10,
    'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
    'enableFacets'    => $options['Enable_Facets'],
    'filterProductId' => $record[0]['productId'],
    'groupBy'		  => 'productId'
));

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
$result = $prosperentApi -> getAllData();
?>
<div id="product" itemscope itemtype="http://data-vocabulary.org/Product">
    <div class="productBlock">
        <div class="productTitle"><a href="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record[0]['affiliate_url'])); ?>" target="<?php echo $target; ?>" rel="nofollow"><cite itemprop="name"><?php echo preg_replace('/\(.+\)/i', '', $record[0]['keyword']); ?></cite></a></div>
        <div class="productImage">
            <a itemprop="offerURL" href="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record[0]['affiliate_url'])); ?>" target="<?php echo $target; ?>" rel="nofollow"><img itemprop="image" src="<?php echo ($options['Image_Masking'] ? $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record[0]['image_url'])) : $record[0]['image_url']); ?>" title="<?php echo $record[0]['keyword']; ?>" ></a>
        </div>
        <div class="productContent">
            <div class="productDescription" itemprop="description"><?php
                if (strlen($record[0]['description']) > 200)
                {
                    echo substr($record[0]['description'], 0, 200);
					?>
					<div id="moreDesc" style="display:inline-block;"> ... <a style="cursor:pointer;" onclick="showFullDesc('fullDesc'); hideMoreDesc('moreDesc');">more</a></div><span id="fullDesc" style="display:none; -moz-hyphens: manual;font-style:normal;"><?php echo substr($record[0]['description'], 200); ?></span>
					<?php
                }
                else
                {
                    echo $record[0]['description'];
                }
                ?>
            </div>
            <div class="productBrandMerchant">
                <?php
                if($record[0]['category'])
                {	
					$record[0]['category'] = preg_replace('/\/$/', '', $record[0]['category']);
					$record[0]['category'] = preg_replace('/([a-z0-9])(?=[A-Z])/', '$1-$2', $record[0]['category']);
                    $categoryList = preg_split('/(:|>|<|;|\.|-|\/)/i', $record[0]['category']);
					$catCount = count($categoryList);

                    echo '<div><u>Category</u>: ';
                    foreach ($categoryList as $i => $category)
                    {
                        $category = trim($category);
                        if (preg_match('/' . $query . '/i', $category))
                        {
                            echo '<a href="' . $url . '/query/' . urlencode($category) . '"><cite itemprop="category">' . $category . '</cite></a>';
                        }
                        else
                        {
                            echo '<a href="' . $url . '/query/' . urlencode($category) . '+' . urlencode($query) . '"><cite itemprop="category">' . $category . '</cite></a>';
                        }

                        if ($i < ($catCount - 1))
                        {
                            echo ' > ';
                        }
                    }
                    echo '</div>';
                }
                if($record[0]['upc'])
                {
                    echo '<div itemprop="identifier" content="upc:' . $record[0]['upc'] . '" class="prodBrand"><u>UPC</u>: ' . $record[0]['upc'] . '</div><br>';
                }
                if($record[0]['brand'])
                {
                    echo '<div class="prodBrand"><u>Brand</u>: <a href="' . $url . '/brand/' . urlencode($result[0]['brand']) . '"><cite itemprop="brand">' . $record[0]['brand'] . '</cite></a></div><br>';
                }
                echo '<meta itemprop="currency" content="' . $currency . '" />';
                echo '<p><div class="prodPrice" itemscope itemtype="http://data-vocabulary.org/Offer-aggregate">As low as <strong>' . ($currency == 'GBP' ? '&pound;' : '$') . '<cite itemprop="lowprice">' . ($result[0]['minPriceSale'] ? $result[0]['minPriceSale'] : $result[0]['minPrice']) . '</cite>' . '</strong> at <strong><cite itemprop="offerCount">' . $result[0]['groupCount'] . '</cite>' . ($result[0]['groupCount'] > 1 ? ' stores' : ' store') . '</strong></div></p>';
                ?>
            </div>
        </div>
        <div class="clear"></div>
        <div class="allResults">
            <table class="productResults" itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">
                <thead>
                    <tr>
                        <th><strong>Store</strong></th>
                        <th><strong>Price</strong></th>
                        <th></th>
                    </tr>
                </thead>
                <?php
                if ($result[0]['groupCount'] > 1)
                {
                    require_once(PROSPER_PATH . 'Prosperent_Api.php');
                    $prosperentApi = new Prosperent_Api(array(
                        'api_key'         => $options['Api_Key'],
                        'limit'           => 10,
                        'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
                        'enableFacets'    => $options['Enable_Facets'],
                        'filterProductId' => $record[0]['productId']
                    ));

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
                    $result = $prosperentApi -> getAllData();
                }

                foreach ($result as $product)
                {
                    echo '<tr itemscope itemtype="http://data-vocabulary.org/Product">';
                    echo '<td itemprop="seller">' . $product['merchant'] . '</td>';
                    echo '<td itemprop="price">' . ($currency == 'GBP' ? '&pound;' : '$') . ($product['price_sale'] ? $product['price_sale'] : $product['price']) . '</td>';
                    echo '<meta itemprop="priceCurrency" content="' . $currency . '"/>';
                    echo '<td itemprop="offerURL" itemscope itemtype="http://data-vocabulary.org/Offer-aggregate"><form style="margin:0; margin-bottom:5px;" action="' . $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $product['affiliate_url'])) . '" target="' . $target. '" method="POST"><input type="submit" value="Visit Store"/></form></td>';
                    echo '</tr>';
                }

                ?>
            </table>
        </div>
    </div>
</div>

<?php
/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
    'api_key'      => $options['Api_Key'],
    'limit'        => $options['Same_Limit'],
    'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
    'query'		   => $record[0]['keyword'],
    'groupBy'	   => 'productId'
));

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
$similar = $prosperentApi -> getAllData();
if ($similar)
{
    echo '<div class="clear"></div>';
    echo '<div class="simTitle">Similar Products</div>';
    echo '<div id="simProd">';
    echo '<ul>';

    foreach ($similar as $prod)
    {
        $price = $prod['price_sale'] ? $prod['price_sale'] : $prod['price'];
        $prod['image_url'] = $options['Image_Masking'] ? $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $prod['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $prod['image_url']);
        ?>
            <li>
            <div class="listBlock">
                <div class="prodImage">
                    <a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $prod['catalogId']; ?>"><div><img src="<?php echo $prod['image_url']; ?>" title="<?php echo $prod['keyword']; ?>"></div></a>
                </div>
                <div class="prodContent">
                    <div class="prodTitle">
                        <a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $prod['catalogId']; ?>" >
                            <?php
                            if (strlen($prod['keyword']) > 60)
                            {
                                echo substr($prod['keyword'], 0, 60) . '...';
                            }
                            else
                            {
                                echo $prod['keyword'];
                            }
                            ?>
                        </a>
                    </div>
                    <div class="prodPrice"><div><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $price; ?></div></div>
                </div>
                <div class="clear"></div>
            </div>
            </li>
        <?php
    }
    echo '</ul>';
    echo '</div>';
}
echo '<div class="clear"></div>';
/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
    'api_key'      => $options['Api_Key'],
    'limit'        => $options['Same_Limit'],
    'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
    'enableFacets' => $options['Enable_Facets'],
    'filterBrand'  => $record[0]['brand'],
    'groupBy'	   => 'productId'
));

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

$sameBrand = $prosperentApi -> getAllData();
if ($sameBrand)
{
    echo '<div class="simTitle">Other Products from ' . $record[0]['brand'] . '</div>';
    echo '<div id="simProd">';
    echo '<ul>';
    foreach ($sameBrand as $brandProd)
    {
        $price = $brandProd['price_sale'] ? $brandProd['price_sale'] : $brandProd['price'];
        $brandProd['image_url'] = $options['Image_Masking'] ? $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $brandProd['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $brandProd['image_url']);
        ?>
            <li>
            <div class="listBlock">
                <div class="prodImage">
                    <a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $brandProd['catalogId']; ?>"><img src="<?php echo $brandProd['image_url']; ?>" title="<?php echo $brandProd['keyword']; ?>"></a>
                </div>
                <div class="prodContent">
                    <div class="prodTitle" style="text-align:center;font-size:12px;">
                        <a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $brandProd['catalogId']; ?>" >
                            <?php
                            if (strlen($brandProd['keyword']) > 60)
                            {
                                echo substr($brandProd['keyword'], 0, 60) . '...';
                            }
                            else
                            {
                                echo $brandProd['keyword'];
                            }
                            ?>
                        </a>
                    </div>
                    <div class="prodPrice"><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $price; ?></div>
                </div>
                <div class="clear"></div>
            </div>
            </li>
        <?php
    }
    echo '</ul>';
    echo '</div>';
}
