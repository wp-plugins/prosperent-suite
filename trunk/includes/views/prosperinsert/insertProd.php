<?php
if ($pieces['v'] === 'grid')
{
	$gridImage = ($pieces['gimgsz'] ? preg_replace('/\s?(px|em|%)/i', '', $pieces['gimgsz']) : 200) . 'px';
	$classLoad = ($type === 'coupon' || $type === 'merchant' || $gridImage < 120) ? 'class="loadCoup"' : 'class="load"';
	?>
	<div style="clear:both;"></div>
	<div id="simProd">
		<ul>
		<?php
		foreach ($results as $record)
		{
			$record['image_url'] = ($record['image_url'] ? $record['image_url'] : $record['logoUrl']);
			if (is_ssl())
			{
				$record['image_url'] = str_replace('http', 'https', $record['image_url']);
			}		

			$record['affiliate_url'] = $this->_options['URL_Masking'] ? $homeUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) : $record['affiliate_url'];
			$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
			$price 	   = $priceSale ? $priceSale : $record['price'];
			$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
			$cid 	   = $record[$recordId];
			
			if ($this->_options['Enable_PPS'] && (!$pieces['gtm'] || $pieces['gtm'] === 'false'))
			{
				$goToUrl = '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink"';
				if ($type === 'merchant')
				{
					$goToUrl = '"' . $homeUrl . '/' . $type . '/' . rawurlencode($record['merchant']) . '" rel="nolink"';
				}				
			}		
			else
			{
				$goToUrl = '"' . $record['affiliate_url'] . '" rel="nofollow,nolink" class="shopCheck" target="' . $target . '"';
				
				if ($type === 'merchant')
				{
					if ($record['deepLinking'] == 1)
					{
						if ($record['domain'] == 'sportsauthority.com')
						{
							$record['domain'] = $record['domain'] . '%2Fhome%2Findex.jsp';
						}
					
						$goToUrl = 'http://prosperent.com/api/linkaffiliator/redirect?apiKey=' . $this->_options['Api_Key'] . '&sid=' . $sid . '&url=' . rawurlencode('http://' . $record['domain']);							
					}
					else					
					{
						$goToUrl = '"' . $homeUrl . '/' . $base . '/merchant/' . rawurlencode($record['merchant']) . '" rel="nolink"';				
					}
				}
			}
			?>
				<li <?php echo ($type === 'coupon' ? 'class="coupBlock"' : 'style="width:' . $gridImage . '!important;"'); ?>>
					<div class="listBlock">
						<div class="prodImage">
							<a href=<?php echo $goToUrl; ?>><span <?php echo $classLoad . (($type != 'coupon' && $type != 'merchant') ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px"'); ?>><img <?php echo (($type != 'coupon' && $type != 'merchant') ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px"'); ?> src="<?php echo $this->_options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('https://img1.prosperent.com/images/', 'http://img1.prosperent.com/images/', '/'), array('', '', ',SL,'), $record['image_url'])) : $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
						</div>
						<?php
						if ($record['promo'])
						{					
							echo '<div class="promo"><span><a href=' . $goToUrl . '>' . $record['promo'] . '!</a></span></div>';
						}
						elseif($record['expiration_date'] || $record['expirationDate'])
						{
							$expirationDate = $record['expirationDate'] ? $record['expirationDate'] : $record['expiration_date'];
							$expires = strtotime($expirationDate);
							$today = strtotime(date("Y-m-d"));
							$interval = ($expires - $today) / (60*60*24);

							if ($interval <= 20 && $interval > 0)
							{
								echo '<div class="couponExpire"><span><a href=' . $goToUrl . '>' . $interval . ' Day' . ($interval > 1 ? 's' : '') . ' Left!</a></span></div>';
							}
							elseif ($interval <= 0)
							{
								echo '<div class="couponExpire"><span><a href=' . $goToUrl . '>Ends Today!</a></span></div>';
							}
							else
							{
								echo '<div class="couponExpire"><span><a href=' . $goToUrl . '>Expires Soon!</a></span></div>';
							}
						}
						elseif ($type == 'coupon' || $type == 'local')
						{
							echo '<div class="promo">&nbsp;</div>';
						}
						?>
						<div class="prodContent">
							<div class="prodTitle">
								<a href=<?php echo $goToUrl; ?> >
									<?php echo $keyword; ?>
								</a>
							</div>                    
							<?php if ($price && $type != 'coupon' && $type != 'local'): ?>
							<div class="prodPrice"><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $price; ?></div>
							<?php endif; ?>
						</div>

						<?php if ($type != 'merchant') : ?>
						<div class="prosperVisit">
							<form class="shopCheck" action="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" method="POST" rel="nofollow,nolink">
								<input type="submit" value="<?php echo $pieces['vst']; ?>"/>
							</form>
						</div>	
						<?php endif; ?>
					</div>			
				</li>
			<?php
		}
		?>
		</ul>
    </div>
	<?php
}
else
{ 
	?>
	<div id="productList" style="border:none;border-top:1px solid #ddd;">
		<?php
		// Loop to return Products and corresponding information
		foreach ($results as $record)
		{			
			if (is_ssl())
			{
				$record['image_url'] = str_replace('http', 'https', $record['image_url']);
			}
		
			$record['affiliate_url'] = $this->_options['URL_Masking'] ? $homeUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) : $record['affiliate_url'];
			$cid = $record['couponId'] ? $record['couponId'] : $record['catalogId'];
			$baseUrl = $homeUrl . '/' . ($options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products');

			if ($this->_options['Enable_PPS'] && (!$pieces['gtm'] || $pieces['gtm'] === 'false'))
			{
				$goToUrl = '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink"';
			}		
			else
			{
				$goToUrl = '"' . $record['affiliate_url'] . '" rel="nofollow,nolink" class="shopCheck" target="' . $target . '"';
			}
			?>
			<div class="productBlock">
				<div class="productImage">
					<a href=<?php echo $goToUrl; ?>><span <?php echo ($imageLoader ? 'class="loadCoup"' : 'class="load"'); ?>><img src="<?php echo $this->_options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('https://img1.prosperent.com/images/', 'http://img1.prosperent.com/images/', '/'), array('', '', ',SL,'), $record['image_url'])) : $record['image_url'];; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
				</div>
				<div class="productContent">
					<?php
					if ($record['promo'])
					{					
						echo '<div class="promo"><span>' . $record['promo'] . '</span></div>' . (($record['expiration_date'] || $record['expirationDate']) ? '&nbsp;&nbsp;&mdash;&nbsp;&nbsp;' : '');
					}
					
					if($record['expiration_date'] || $record['expirationDate'])
					{
						$expirationDate = $record['expirationDate'] ? $record['expirationDate'] : $$record['expiration_date'];
						$expires = strtotime($expirationDate);
						$today = strtotime(date("Y-m-d"));
						$interval = ($expires - $today) / (60*60*24);

						if ($interval <= 20 && $interval > 0)
						{
							echo '<div class="couponExpire"><span>' . $interval . ' Day' . ($interval > 1 ? 's' : '') . ' Left!</span></div>';
						}
						elseif ($interval <= 0)
						{
							echo '<div class="couponExpire"><span>Ends Today!</span></div>';
						}
						else
						{
							echo '<div class="couponExpire"><span>Expires Soon!</span></div>';
						}
					}	
					?>
					<div class="productTitle"><a href=<?php echo $goToUrl; ?>><span><?php echo $record['keyword']; ?></span></a></div>
					<?php
					if ($type != 'coupon')
					{ 
						?>
						<div class="productDescription">
							<?php
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
						<?php
					}	
					
					if ($record['coupon_code'])
					{
						echo '<div class="couponCode">Coupon Code: <span class="code_cc">' . $record['coupon_code'] . '</span></div>';
					}							
					?>				
					<div class="productBrandMerchant">
						<?php
						if($record['brand'])
						{
							echo '<span class="brandIn"><u>Brand</u>: <a href=' . ($this->_options['Enable_PPS'] ? '"' . $baseUrl  . '/brand/' . rawurlencode($record['brand']) . '" rel="nolink"' : $goToUrl) . '><cite>' . $record['brand'] . '</cite></a></span>';
						}
						if($record['merchant'])
						{
							echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . ($this->_options['Enable_PPS'] ? '"' . $baseUrl . '/merchant/' . rawurlencode($record['merchant']) . '" rel="nolink"' : $goToUrl) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
						}				
						?>
					</div>
				</div>
				<div class="productEnd">
					<?php
					if ($record['price_sale'] || $record['price'] || $record['priceSale'])
					{
						$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
						
						if(empty($priceSale) || $record['price'] <= $priceSale)
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
							<div class="productPriceSale"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $priceSale?></span></div>
							<?php
						}
					}
					echo 'here';
					?>
					<div class="prosperVisit">
						<form class="shopCheck" action="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" method="POST" rel="nofollow,nolink">
							<input type="submit" value="<?php echo $pieces['vst']; ?>"/>
						</form>
					</div>	
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php 
} 
