<?php
$target = $options['Target'] ? '_blank' : '_self';

require_once('Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
    'api_key'         => $options['Api_Key'],
    'filterProductId' => $results[0]['productId'],
    'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
    'limit'           => $cl,
    'sortPrice'		  => 'asc'
));

$prosperentApi -> fetch();
$result = $prosperentApi -> getAllData();
if (count($result) > 1)
{
    ?>
    <div id="productList">
        <?php
        foreach ($result as $record)
        {
            $record['image_url'] = preg_replace('/\/images\/250x250\//', '/images/75x75/', $record['image_url'])
            ?>
            <div class="<?php echo count($result) >= 2 ? 'productBlock' : 'productBlock0'; ?>">
                <div class="productImage" style="width:85px;">
                    <a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>"><span><img src="<?php echo $record['image_url']; ?>"  alt="<?php echo $record['keyword']; ?>" title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
                </div>
                <div class="productContent" style="width:52%;">
                    <div class="productTitle"><a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" class="prosperent-kw"><span><?php echo $record['keyword']; ?></span></a></div>
                    <div class="productBrandMerchant">
                        <?php
                        if($record['brand'])
                        {
                            echo '<span class="brandIn"><u>Brand</u>: <a href="" onclick="return false;"><cite>' . $record['brand'] . '</cite></a></span>';
                        }
                        if($record['merchant'])
                        {
                            echo '<span class="merchantIn"><u>Merchant</u>: <a href="" onclick="return false;"><cite>' . $record['merchant'] . '</cite></a></span>';
                        }
                        ?>
                    </div>
                </div>
                <div class="productEnd" style="width:190px;">
                    <?php
                    if(empty($record['price_sale']) || $record['price'] <= $record['price_sale'])
                    {
                        //we don't do anything
                        ?>
                        <div class="productPriceNoSale" style="float:left;"><span><?php echo '$' . $record['price']; ?></span></div>
                        <?php
                    }
                    //otherwise strike-through Price and list the Price_Sale
                    else
                    {
                        ?>
                        <div class="productPrice" style="float:left;"><span>$<?php echo $record['price']?></span></div>
                        <div class="productPriceSale" style="float:left;"><span>$<?php echo $record['price_sale']?></span></div>
                        <?php
                    }
                    ?>
                    <form style="margin:0;"  method="POST" action="<?php echo $record['affiliate_url'] . '" target="' . $target; ?>">
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
    ?>
    <div id="productList">
        <?php
        foreach ($results as $record)
        {
            $record['image_url'] = preg_replace('/\/images\/250x250\//', '/images/125x125/', $record['image_url'])
            ?>
            <div class="productBlock">
                <div class="productImage">
                    <a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>"><span><img src="<?php echo $record['image_url']; ?>"  alt="<?php echo $record['keyword']; ?>" title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
                </div>
                <div class="productContent">
                    <div class="productTitle"><a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" class="prosperent-kw"><span><?php echo $record['keyword']; ?></span></a></div>
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
                        if($record['brand'])
                        {
                            echo '<span class="brandIn"><u>Brand</u>: <a href="" onclick="return false;"><cite>' . $record['brand'] . '</cite></a></span>';
                        }
                        if($record['merchant'])
                        {
                            echo '<span class="merchantIn"><u>Merchant</u>: <a href="" onclick="return false;"><cite>' . $record['merchant'] . '</cite></a></span>';
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
                    <form style="margin:0;"  method="POST" action="<?php echo $record['affiliate_url'] . '" target="' . $target; ?>">
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
