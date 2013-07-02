<?php
$url = 'http://' . $_SERVER['HTTP_HOST'] . '/products/type/' . $type;

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
$prosperentApi -> fetch();
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

$prosperentApi -> fetch();
$result = $prosperentApi -> getAllData();

?>
<div id="product">
	<div class="productBlock">
		<div class="productTitle"><a href="<?php echo $record[0]['affiliate_url']; ?>" target="<?php echo $target; ?>"><span itemprop="name"><?php echo preg_replace('/\(.+\)/i', '', $record[0]['keyword']); ?></span></a></div>
		<div class="productImage">
			<a href="<?php echo $record[0]['affiliate_url']; ?>" target="<?php echo $target; ?>"><span><img itemprop="image" src="<?php echo $record[0]['image_url']; ?>"  alt="<?php echo $record[0]['keyword']; ?>" title="<?php echo $record[0]['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
		</div>
		<div class="productContent">
			<div class="productDescription" itemprop="description"><?php
				if (strlen($record[0]['description']) > 200)
				{
					echo substr($record[0]['description'], 0, 200) . '...';
				}
				else
				{
					echo $record[0]['description'];
				}
				?>
			</div>
			<div class="productBrandMerchant">
				<?php
				if($record[0]['brand'] && !$filterBrand)
				{
					echo '<span itemprop="brand" class="prodBrand"><u>Brand</u>: <a href="' . $url . '/brand/' . urlencode($result[0]['brand']) . '" target="' . $target . '"><cite>' . $record[0]['brand'] . '</cite></a></span>';
				}
				echo '<p><span class="prodPrice">As low as <strong>' . ($currency == 'GBP' ? '£' : '$') . ($result[0]['minPriceSale'] ? $result[0]['minPriceSale'] : $result[0]['minPrice']) . '</strong> at <strong>' . $result[0]['groupCount'] . ($result[0]['groupCount'] > 1 ? ' stores' : ' store') . '</strong></span></p>';
				?>
			</div>
		</div>
		<div class="clear"></div>
		<div style="display:block;">
			<table class="productResults" style="border:none;">
				<thead style="border-bottom:#ddd solid 1px;">
					<tr>
						<th><strong>Store</strong></th>
						<th><strong>Price</strong></th>
						<th></th>
					</tr>
				</thead>
				<?php			

				foreach ($record as $product)
				{
					echo '<tr style="border-bottom:1px solid #ddd">';
					echo '<td style="text-align:center; padding:4px 4px;">' . $product['merchant'] . '</td>';
					echo '<td style="text-align:center; padding:4px 4px;">' . ($currency == 'GBP' ? '£' : '$') . ($product['priceSale'] ? $product['priceSale'] : $product['price']) . '</td>';
					echo '<meta itemprop="priceCurrency" content="' . $currency . '"/>';
					echo '<td style="text-align:center; padding:4px 4px;"><form style="margin:0; margin-bottom:5px;" action="' . $product['affiliate_url'] . '" target="' . $target. '" method="POST"><input type="submit" value="Visit Store"/></form></td>';
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
	'limit'        => 8,
	'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
	'query'		   => $record[0]['keyword'],
	'groupBy'	   => 'productId'
));

$prosperentApi -> fetch();
$similar = $prosperentApi -> getAllData();
echo '<div class="clear"></div>';
echo '<div style="margin-top:30px;font-size:16px;font-weight:bold;">Similar Products</div>';
echo '<div id="similarProd" style="padding:0 0.5em;border-top:2px solid #ddd;">';
echo '<ul style="list-style:none; margin:0; padding:0;  display:inline; text-align:left;">';

foreach ($similar as $prod)
{
	$prod['image_url'] = preg_replace('/\/images\/250x250\//', '/images/125x125/', $prod['image_url'])
	?>
		<li style="  list-style:none; margin:0px;  font-size:12px;  display:inline;  height:185px;  float:left;  overflow:hidden;  padding:4px 0;">
		<div class="productBlock9302" style="width:146px; margin: 0 auto;">
			<div class="productImage" style="margin: 0 10px; height:130px;">
				<a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $prod['catalogId'];//$prod['affiliate_url']; ?>"><span><img src="<?php echo $prod['image_url']; ?>"  alt="<?php echo $prod['keyword']; ?>" title="<?php echo $prod['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
			</div>
			<div class="productContent">
				<div class="productTitle" style="text-align:center;font-size:12px;"><a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $prod['catalogId'];//$prod['affiliate_url']; ?>"><span><?php echo preg_replace('/\(.+\)/i', '', $prod['keyword']); ?></span></a></div>
				<?php
				if(empty($prod['price_sale']) || $prod['price'] <= $prod['price_sale'])
				{
					//we don't do anything
					?>
					<div class="productPriceNoSale" style="font-size:12px;text-align:center;"><span><?php echo '$' . $prod['price']; ?></span></div>
					<?php
				}
				//otherwise strike-through Price and list the Price_Sale
				else
				{
					?>
					<div class="productPriceSale"style="font-size:12px;text-align:center;"><span>$<?php echo $prod['price_sale']?></span></div>
					<?php
				}
				?>
			</div>
			<div class="clear"></div>
		</div>
		</li>

	<?php
}	
echo '</ul>';
echo '</div>';
echo '<div class="clear"></div>';
/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
	'api_key'      => $options['Api_Key'],
	'limit'        => 8,
	'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
	'enableFacets' => $options['Enable_Facets'],
	'filterBrand'  => $record[0]['brand'],
	'groupBy'	   => 'productId'
));

$prosperentApi -> fetch();
$sameBrand = $prosperentApi -> getAllData();

echo '<div style="margin-top:30px;font-size:16px;font-weight:bold;">Other Products from ' . $record[0]['brand'] . '</div>';
echo '<div id="sameBrand" style="padding:0 0.5em; border-top:2px solid #ddd;">';
echo '<ul style="list-style:none; margin:0; padding:0;display:inline; text-align:left;">';
foreach ($sameBrand as $brandProd)
{
	$brandProd['image_url'] = preg_replace('/\/images\/250x250\//', '/images/125x125/', $brandProd['image_url'])
	?>
		<li style="list-style:none; margin:0px;  font-size:12px;  display:inline;  height:185px;  float:left;  overflow:hidden;  padding:4px 0;">
		<div class="productBlock9302" style="width:146px; margin: 0 auto;">
			<div class="productImage" style="margin: 0 10px; height:130px;">
				<a href="<?php echo $productPage . '/travel/' . urlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $brandProd['catalogId'];//$brandProd['affiliate_url']; ?>"><span><img src="<?php echo $brandProd['image_url']; ?>"  alt="<?php echo $brandProd['keyword']; ?>" title="<?php echo $brandProd['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
			</div>
			<div class="productContent">
				<div class="productTitle" style="text-align:center;font-size:12px;"><a href="<?php echo $productPage . '/travel/' . urlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $brandProd['catalogId'];//$brandProd['affiliate_url']; ?>" ><span><?php echo preg_replace('/\(.+\)/i', '', $brandProd['keyword']); ?></span></a></div>
				<?php
				if(empty($brandProd['price_sale']) || $brandProd['price'] <= $brandProd['price_sale'])
				{
					//we don't do anything
					?>
					<div class="productPriceNoSale" style="font-size:12px;text-align:center;"><span><?php echo '$' . $brandProd['price']; ?></span></div>
					<?php
				}
				//otherwise strike-through Price and list the Price_Sale
				else
				{
					?>
					<div class="productPriceSale"style="font-size:12px;text-align:center;"><span>$<?php echo $brandProd['price_sale']?></span></div>
					<?php
				}
				?>
			</div>
			<div class="clear"></div>
		</div>
		</li>

	<?php
}	
echo '</ul>';
echo '</div>';