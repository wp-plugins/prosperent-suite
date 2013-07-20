<?php
$base = $options['Base_URL'] ? $options['Base_URL'] : 'products';
$url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $base . '/type/local';

/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
    'api_key'         => $options['Api_Key'],
    'limit'           => 1,
    'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
    'enableFacets'    => true,
    'filterLocalId'   => get_query_var('cid')
));

/*
/  Fetching results and pulling back all data
/  To see which data is available to pull back login in to
/  Prosperent.com and click the API tab
*/
$prosperentApi -> fetchLocal();
$record = $prosperentApi -> getAllData();

$expires = strtotime($record[0]['expirationDate']);
$today = strtotime(date("Y-m-d"));
$interval = abs($expires - $today) / (60*60*24);

$city = null == $record[0]['city'] ? 'Online' : $record[0]['city'];
$zip = null == $record[0]['zipCode'] ? 'Online' : $record[0]['zipCode'];
$state = null == $record[0]['state'] ? 'Online' : $record[0]['state'];
?>
<div id="product">
    <div class="productBlock">
        <div class="productTitle"><a href="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record[0]['affiliate_url'])); ?>" target="<?php echo $target; ?>"><span itemprop="name"><?php echo preg_replace('/\(.+\)/i', '', $record[0]['keyword']); ?></span></a></div>
        <div class="productImage">
            <a href="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record[0]['affiliate_url'])); ?>" target="<?php echo $target; ?>"><span><img itemprop="image" src="<?php echo ($options['Image_Masking'] ? $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record[0]['image_url'])) : $record[0]['image_url']); ?>" title="<?php echo $record[0]['keyword']; ?>"></span></a>
        </div>
        <div class="productContent">
            <div class="productDescription" itemprop="description"><?php
                if (strlen($record[0]['description']) > 185)
                {
                    echo substr($record[0]['description'], 0, 185) . '...';
                }
                else
                    echo $record[0]['description'];
                {
                }
                ?>
            </div>
            <div class="productBrandMerchant">
                <?php
                if($record[0]['merchant'])
                {
                    echo '<span itemprop="brand" class="prodBrand"><u>Merchant</u>: <a href="' . $url . '/merchant/' . urlencode($record[0]['merchant']) . '"><cite itemprop="seller">' . $record[0]['merchant'] . '</cite></a></span><br>';
                }
                if ($record[0]['expirationDate'] && $interval <= 120)
                {
                    echo '<meta itemprop="priceValidUntil" content="' . $record[0]['expirationDate'] . '"/>';
                    echo '<span class="prodBrand"><u>Expires</u>: <strong>' . ($interval > 0 ? $interval . ' days left!' : 'Today!') . '</strong></span><br>';
                }
                if ($city || $state || $zip)
                {
                    if ('Online' == $city)
                    {
                        echo '<span class="prodBrand"><u>Location</u>: <strong>' . $city . ' Deal' . '</strong></span><br>';
                    }
                    else
                    {
                        echo '<span class="prodBrand"><u>Location</u>: <strong>' . $city . ($state ? ', ' . $state : '') . ($zip ? ' ' . $zip : '') . '</strong></span><br>';
                    }
                }
                if(empty($record[0]['priceSale']) || $record[0]['price'] <= $record[0]['priceSale'])
                {
                    echo '<p><span class="prodBrand"><u>Price</u>: <strong>$' . $record[0]['price'] . '</strong></span></p>';
                }
                //otherwise strike-through Price and list the priceSale
                else
                {
                    echo '<span class="prodBrand" style="font-size:16px;padding-top:8px;"><u>Sale Price</u>: <strong>$' . $record[0]['priceSale'] . '</strong></span><br>';
                    echo '<span class="prodBrand" style="color:#cc6600;font-size:16px;">Savings of <strong>$' . ($record[0]['price'] - $record[0]['priceSale']) . '!</strong></span>';
                }

                ?>
            </div>
        </div>
        <div class="clear"></div>
        <div style="display:block;">
            <table class="productResults">
                <thead>
                    <tr>
                        <th><strong>Store</strong></th>
                        <th><strong>City</strong></th>
                        <th><strong>Price</strong></th>
                        <th></th>
                    </tr>
                </thead>
                <?php
                echo '<tr>';
                echo '<td>' . $record[0]['merchant'] . '</td>';
                echo '<td>' . $city . '</td>';
                echo '<td>$' . ($record[0]['priceSale'] ? $record[0]['priceSale'] : $record[0]['price']) . '</td>';
                echo '<meta itemprop="priceCurrency" content="USD"/>';
                echo '<td><form style="margin:0; margin-bottom:5px;" action="' . $productPage . '/store/go/' . urlencode(str_replace(array('/', 'http://prosperent.com/store/product/'), array(',SL,', ''), $record[0]['affiliate_url'])) . '" target="' . $target. '" method="POST"><input type="submit" value="Visit Store"/></form></td>';
                echo '</tr>';
                ?>
            </table>
        </div>
    </div>
</div>
<div class="clear"></div>

<?php
$city = null == $record[0]['city'] ? 'null' : $record[0]['city'];
$state = null == $record[0]['state'] ? 'null' : $record[0]['state'];

/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
    'api_key'       => $options['Api_Key'],
    'limit'         => 8,
    'visitor_ip'    => $_SERVER['REMOTE_ADDR'],
    'enableFacets'  => true,
    'filterCity'    => $city,
    'filterState'   => $state,
    'filterLocalId' => '!' . $record[0]['localId']
));

$city = null == $record[0]['city'] ? 'Online' : $record[0]['city'];

$prosperentApi -> fetchLocal();
$sameCity = $prosperentApi -> getAllData();

if ($sameCity)
{
    echo '<div class="simTitle">Other Deals ' . ($city == 'Online' ? $city : 'in ' . $city) . '</div>';
    echo '<div id="simProd">';
    echo '<ul>';
    foreach ($sameCity as $cityProd)
    {
        if (empty($cityProd['localId']))
        {
            continue;
        }

        $price = $cityProd['price_sale'] ? $cityProd['price_sale'] : $cityProd['price'];
        $cityProd['image_url'] = $options['Image_Masking'] ? $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $cityProd['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $cityProd['image_url']);
        ?>
        <li>
            <div class="listBlock">
                <div class="prodImage">
                    <a href="<?php echo $productPage . '/local/' . urlencode(str_replace('/', ',SL,', $cityProd['keyword'])) . '/cid/' . $cityProd['localId']; ?>"><img src="<?php echo $cityProd['image_url']; ?>" title="<?php echo $cityProd['keyword']; ?>"></a>
                </div>
                <div class="prodContent">
                    <div class="prodTitle" style="text-align:center;font-size:12px;">
                        <a href="<?php echo $productPage . '/local/' . urlencode(str_replace('/', ',SL,', $cityProd['keyword'])) . '/cid/' . $cityProd['localId']; ?>" >
                            <?php
                            if (strlen($cityProd['keyword']) > 60)
                            {
                                echo substr($cityProd['keyword'], 0, 60) . '...';
                            }
                            else
                            {
                                echo $cityProd['keyword'];
                            }
                            ?>
                        </a>
                    </div>
                    <div class="prodPrice"><span>$<?php echo $price; ?></span></div>
                </div>
                <div class="clear"></div>
            </div>
        </li>
        <?php
    }
    echo '</ul>';
    echo '</div>';
}
