<?php
if ($pieces['v'] === 'grid')
{
	echo '<div id="simProd">';
    echo '<ul>';
    foreach ($results as $record)
    {
		$record['affiliate_url'] = $this->_options['URL_Masking'] ? $homeUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) : $record['affiliate_url'];
		$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
        $price 	   = $priceSale ? $priceSale : $record['price'];
		$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
		$cid 	   = $record['couponId'] ? $record['couponId'] : $record['catalogId'];
		
		if ($pieces['gtm'] && $this->_options['URL_Masking'])
		{
			$goToUrl = '"' . $record['affiliate_url'] . '" rel="nofollow" target="' . $target . '"';
		}
		elseif ($this->_options['Enable_PPS'] && !$pieces['gtm']) 
		{
			$goToUrl = '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '"';
		}		
		else
		{
			$goToUrl = '"' . $record['affiliate_url'] . '" rel="nofollow" target="' . $target . '"';
		}
        ?>
            <li <?php echo ($params['type'] === 'coup' ? 'class="coupBlock"' : ''); ?>>
            <div class="listBlock">
                <div class="prodImage">
                    <a href=<?php echo $goToUrl; ?>><span <?php echo ($params['type'] === 'coup' ? 'class="loadCoup"' : 'class="load"'); ?>><img <?php echo ($params['type'] != 'coup' ? 'class="gridImg"' : ''); ?> src="<?php echo $this->_options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record['image_url'])) : $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
                </div>
                <div class="prodContent">
                    <div class="prodTitle">
                        <a href=<?php echo $goToUrl; ?> >
                            <?php
                            if (strlen($keyword) > 42)
                            {
                                echo substr($keyword, 0, 42) . '...';
                            }
                            else
                            {
                                echo $keyword;
                            }
                            ?>
                        </a>
                    </div>                    
                </div>
				<?php if ($price && $params['type'] != 'coup'): ?>
				<div class="prodPrice"><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $price; ?></div>
				<?php endif; ?>
			</div>
			
            </li>
        <?php
    }
    echo '</ul>';
    echo '</div>';
}
else
{ 
	?>
	<div id="productList" style="border:none;border-top:1px solid #ddd;">
		<?php
		// Loop to return Products and corresponding information
		foreach ($results as $record)
		{						
			$record['affiliate_url'] = $this->_options['URL_Masking'] ? $homeUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) : $record['affiliate_url'];
			$cid = $record['couponId'] ? $record['couponId'] : $record['catalogId'];
			$baseUrl = $homeUrl . '/' . ($options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products');

			if ($pieces['gtm'] && $this->_options['URL_Masking'])
			{
				$goToUrl = '"' . $record['affiliate_url'] . '" rel="nofollow" target="' . $target . '"';
			}
			elseif ($this->_options['Enable_PPS'] && !$pieces['gtm']) 
			{
				$goToUrl = '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '"';
			}		
			else
			{
				$goToUrl = '"' . $record['affiliate_url'] . '" rel="nofollow" target="' . $target . '"';
			}
			?>
			<div class="productBlock">
				<div class="productImage">
					<a href=<?php echo $goToUrl; ?>><span <?php echo ($imageLoader ? 'class="loadCoup"' : 'class="load"'); ?>><img src="<?php echo $this->_options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record['image_url'])) : $record['image_url'];; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
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
							echo '<div class="couponExpire"><span>Expires in ' . $interval . ' days!</span></div>';
						}
						elseif ($interval <= 0)
						{
							echo '<div class="couponExpire"><span>Expires Today!</span></div>';
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
							echo '<span class="brandIn"><u>Brand</u>: <a href="' . ($this->_options['Enable_PPS'] ? $baseUrl  . '/brand/' . rawurlencode($record['brand']) : $goToUrl) . '"><cite>' . $record['brand'] . '</cite></a></span>';
						}
						if($record['merchant'])
						{
							echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . ($this->_options['Enable_PPS'] ? $baseUrl . '/merchant/' . rawurlencode($record['merchant']) : $goToUrl) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
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
					?>
					<div class="prosperVisit">
						<form action="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" method="POST" rel="nofollow">
							<input type="submit" value="Visit Store"/>
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
