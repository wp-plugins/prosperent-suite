<?php
if (!$options['Enable_PPS'])
{
	if ($storeUrl = get_query_var('storeUrl'))
	{    
		$storeUrl = rawurldecode($storeUrl);
		$storeUrl = str_replace(',SL,', '/', $storeUrl);
		header('Location:http://prosperent.com/' . $storeUrl);
		exit;
	}
}

$target = $options['Target'] ? '_blank' : '_self';
$base = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products';
$prodSubmit = site_url('/') . $base;
$startUrl = site_url();

?>
<div id="productList">
	<?php
	foreach ($results as $i => $record)
	{
		$goToUrl = ($options['Enable_PPS'] && !$options['Link_to_Merc'] && $options['URL_Masking'] ? '"' . $startUrl . '/product/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId'] . '" rel="nofollow"' : ($options['Enable_PPS'] && $options['Link_to_Merc'] && $options['URL_Masking'] ? '"' . $startUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) . '" rel="nofollow"' : '"' . $record['affiliate_url'] . '" rel="nofollow"'));
		$formGoToUrl = $options['Enable_PPS'] && $options['URL_Masking'] ? $startUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) : $record['affiliate_url'];
		$brandGoToUrl = $options['Enable_PPS'] ? '"' . $prodSubmit . '/brand/' . rawurlencode($record['brand']) . '"' : '"' . $record['affiliate_url'] . '" rel="nofollow"';
		$merchantGoToUrl = $options['Enable_PPS'] ? '"' . $prodSubmit . '/merchant/' . rawurlencode($record['merchant']) . '"' : '"' . $record['affiliate_url'] . '" rel="nofollow"';
		$record['image_url'] = $options['Image_Masking'] ? $startUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $record['image_url']);
		?>
		<div class="<?php echo $i > 0 ? 'productBlock' : 'productBlock0'; ?>">
			<div class="productImage">
				<a href=<?php echo $goToUrl; ?>><span><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
			</div>
			<div class="productContent">
				<div class="productTitle"><a href=<?php echo $goToUrl; ?>><span><?php echo $record['keyword']; ?></span></a></div>
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
						echo '<span class="brandIn"><u>Brand</u>: <a href=' . $brandGoToUrl . '><cite>' . $record['brand'] . '</cite></a></span>';
					}
					if($record['merchant'])
					{
						echo '<span class="merchantIn"><u>Merchant</u>: <a href=' . $merchantGoToUrl . '><cite>' . $record['merchant'] . '</cite></a></span>';
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
				<form style="margin:0;" action="<?php echo $formGoToUrl . ' target="' . $target; ?>" method="POST" rel="nofollow">
					<input type="submit" value="Visit Store"/>
				</form>
			</div>
		</div>
		<?php
	}
	?>
</div>
<?php
