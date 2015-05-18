<?php
if ($pieces['v'] === 'grid')
{
	$gridImage = ($pieces['gimgsz'] ? preg_replace('/\s?(px|em|%)/i', '', $pieces['gimgsz']) : 200) . 'px';
	$classLoad = ($type === 'merchant' ? '' : ($gridImage < 120 ? 'class="loadCoup"' : 'class="load"'));
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
			
			$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
			$price 	   = $priceSale ? $priceSale : $record['price'];
			$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
			$cid 	   = $record[$recordId];
			
			if ($this->_options['PSAct'] && (!$pieces['gtm'] || $pieces['gtm'] === 'false'))
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
				<li <?php echo 'style="width:' . $gridImage . '!important;"'; ?>>
					<div class="listBlock">
						<div class="prodImage">
							<a href=<?php echo $goToUrl; ?>><span <?php echo $classLoad . ($type != 'merchant' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px"'); ?>><img <?php echo ($type != 'merchant' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px"'); ?> src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
						</div>						
						<div class="prodContent">
							<div class="prodTitle">
								<a href=<?php echo $goToUrl; ?> >
									<?php echo $keyword; ?>
								</a>
							</div>                    
							<?php if ($price): ?>
							<div class="prodPrice"><?php echo '$' . $price; ?></div>
							<?php endif; ?>
						</div>

						<?php if ($type != 'merchant') : ?>
						<div class="shopCheck prosperVisit">		
							<a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $pieces['vst']; ?>"/></a>				
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
					
			$cid = $record['catalogId'];
			$baseUrl = $homeUrl . '/' . ($options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products');

			if (($this->_options['PSAct'] && $page->post_status == 'publish') && (!$pieces['gtm'] || $pieces['gtm'] === 'false'))
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
					<a href=<?php echo $goToUrl; ?>><span class="load"><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
				</div>
				<div class="productContent">
					<div class="productTitle"><a href=<?php echo $goToUrl; ?>><span><?php echo $record['keyword']; ?></span></a></div>
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
					<div class="productBrandMerchant">
						<?php
						if($record['brand'])
						{
							echo '<span class="brandIn"><u>Brand</u>: <a href=' . ($this->_options['PSAct'] ? '"' . $baseUrl  . '/brand/' . rawurlencode($record['brand']) . '" rel="nolink"' : $goToUrl) . '><cite>' . $record['brand'] . '</cite></a></span>';
						}
						if($record['merchant'])
						{
							echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . ($this->_options['PSAct'] ? '"' . $baseUrl . '/merchant/' . rawurlencode($record['merchant']) . '" rel="nolink"' : $goToUrl) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
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
							<div class="productPriceNoSale"><span><?php echo '$' . $record['price']; ?></span></div>
							<?php
						}
						//otherwise strike-through Price and list the Price_Sale
						else
						{
							?>
							<div class="productPrice"><span><?php echo '$' . $record['price']?></span></div>
							<div class="productPriceSale"><span><?php echo '$' . $priceSale?></span></div>
							<?php
						}
					}
					?>
					<div class="shopCheck prosperVisit">		
						<a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $pieces['vst']; ?>"/></a>				
					</div>	
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php 
} 
