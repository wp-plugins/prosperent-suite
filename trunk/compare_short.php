<?php
$target = $options['Target'] ? '_blank' : '_self';
$base = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products';
$prodSubmit = site_url('/') . $base;
$startUrl = site_url();

if ($cl)
{
	$settigns = array(
		'api_key'         => $options['Api_Key'],
		'filterProductId' => $results[0]['productId'],
		'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
		'limit'           => $cl,
		'sortPrice'		  => 'asc'
	);
	
	if (file_exists(PROSPER_PATH . 'prosperent_cache') && substr(decoct( fileperms(PROSPER_PATH . 'prosperent_cache') ), 1) == '0777')
	{	
		$settings = array_merge($settings, array(
			'cacheBackend'   => 'FILE',
			'cacheOptions'   => array(
				'cache_dir'  => PROSPER_PATH . 'prosperent_cache'
			)
		));	
	}
	
	$prosperentApi = new Prosperent_Api($settings);

	switch ($ct)
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
	
if (isset($result) && count($result) > 1)
{
    ?>
    <div id="productList">
        <?php
        foreach ($result as $i => $record)
        {
            $record['image_url'] = $options['Image_Masking'] ? $startUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $record['image_url']);
			?>
			<div class="<?php echo $i > 0 ? 'productBlock' : 'productBlock0'; ?>">
				<div class="productImage">
					<a href="<?php echo $startUrl . '/product/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
				</div>
				<div class="productContent">
					<div class="productTitle"><a href="<?php echo $startUrl . '/product/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><?php echo $record['keyword']; ?></span></a></div>
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
							echo '<span class="brandIn"><u>Brand</u>: <a href="' . $prodSubmit . '/brand/' . rawurlencode($record['brand']) . '"><cite>' . $record['brand'] . '</cite></a></span>';
						}
						if($record['merchant'] && !$filterMerchant)
						{
							echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . $prodSubmit . '/merchant/' . rawurlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
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
					<form style="margin:0;" action="<?php echo $startUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) . '" target="' . $target; ?>" method="POST">
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
        foreach ($results as $i => $record)
        {
			$record['image_url'] = $options['Image_Masking'] ? $startUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $record['image_url']);
			?>
			<div class="<?php echo $i > 0 ? 'productBlock' : 'productBlock0'; ?>">
				<div class="productImage">
					<a href="<?php echo $startUrl . '/product/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
				</div>
				<div class="productContent">
					<div class="productTitle"><a href="<?php echo $startUrl . '/product/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><?php echo $record['keyword']; ?></span></a></div>
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
							echo '<span class="brandIn"><u>Brand</u>: <a href="' . $prodSubmit . '/brand/' . rawurlencode($record['brand']) . '"><cite>' . $record['brand'] . '</cite></a></span>';
						}
						if($record['merchant'])
						{
							echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . $prodSubmit . '/merchant/' . rawurlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
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
					<form style="margin:0;" action="<?php echo $startUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) . '" target="' . $target; ?>" method="POST">
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
