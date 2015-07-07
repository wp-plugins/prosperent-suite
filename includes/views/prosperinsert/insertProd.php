<?php
if ($type == 'merchant' )
{
    ?>
	<div style="clear:both;"></div>
	<div id="simProd">
		<ul>
		<?php
        foreach ($results as $record)
        {
            if (is_ssl())
            {
                $record['image_url'] = str_replace('http', 'https', $record['image_url']);
            }

            if ($record['deepLinking'] == 1 && $pieces['gtm'])
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
            ?>
            <li style="overflow:hidden;list-style:none;margin:9px;float:left;height:76px!important;width:136px!important;">
            	<div class="listBlock">
            		<div class="prodImage" style="text-align:center;margin:8px;">
                    	<a href="<?php echo $goToUrl; ?>"><span title="<?php echo $record['merchant']; ?>"><img class="newImage" style="height:60px!important;width:120px!important;" src='<?php echo $record['logoUrl']; ?>'  alt='<?php echo $record['merchant']; ?>' title='<?php echo $record['merchant']; ?>'/></span></a>
        	        </div>		
                </div>   
        	</li>
        	<?php
        } 
        ?>
		</ul>
    </div>
	<?php
}
elseif ($pieces['v'] === 'grid')
{
	?>
	<div style="clear:both;"></div>
	<div id="simProd">
		<ul>
		<?php
		foreach ($results as $record)
		{
			if (is_ssl())
			{
				$record['image_url'] = str_replace('http', 'https', $record['image_url']);
			}		
			
			$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
			$price 	   = $priceSale ? $priceSale : $record['price'];
			$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
			$cid 	   = $record[$recordId];

			if ($this->_options['PSAct'] && (!$pieces['gtm'] || $pieces['gtm'] === 'false' || $pieces['gtm'] === 'prodPage'))
			{
				$goToUrl = '"' . $homeUrl . '/product/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink"';
			}		
			else
			{
				$goToUrl = '"' . $record['affiliate_url'] . '" rel="nofollow,nolink" class="shopCheck" target="' . $target . '"';
			}
			?>
		        <li class="" style="overflow:hidden;list-style:none;margin:6px;float:left;height:285px!important;width:210px!important;">
    				<div class="listBlock">
    					<div class="prodImage" style="text-align:center;">     				
				        	<a href=<?php echo $goToUrl; ?>><span title="<?php echo $record['keyword']; ?>"><img class="newImage" style="height:185px;width:185px;" src='<?php echo $record['image_url']; ?>'  alt='<?php echo $record['keyword']; ?>' title='<?php echo $record['keyword']; ?>'/></span></a>
				        </div>
				        <div class="prodContent" style="font-size:15px">
				            <div class="prodTitle">
				            <a href=<?php echo $goToUrl; ?>><?php echo ($record['brand'] ? $record['brand'] : '&nbsp;'); ?></a> 
				            </div>		
    						<div class="prodPrice">
                                <strong>$<?php echo number_format($price, 2); ?></strong><?php if ($record['merchant']){echo '<span class="merchantIn" style="color:#666;font-size:14px;"> from ' . $record['merchant'] . '</span>'; } ?>
    						</div>          						          						                   
    					</div>	
						<div class="shopCheck prosperVisit">		
							<a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $pieces['vst']; ?>"/></a>				
						</div>		
		            </div> 
				</li>
			<?php
		}
		?>
		</ul>
    </div>
	<?php
}
elseif ($pieces['v'] === 'pc')
{
    ?>
	<div id="product">
    	<table class="productResults" itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer" style="<?php echo ($params['prodImageType'] == 'white' ? 'color:white;' : 'background:white;'); ?>width:45%">        		
    		
    		<?php	           
			foreach ($results as $product)
			{						
			    $priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
			    $price 	   = $priceSale ? $priceSale : $record['price'];
			    $goToUrl   = '"' . $record['affiliate_url'] . '" rel="nofollow,nolink" class="shopCheck" target="_blank"';	
			    if (!$keywordSet)
			    {
			        echo '<td colSpan="3"><div id="prosperPCKeyword">' . $product['keyword'] . '</div></td></tr>';
			        $keywordSet = true;
			    }	
			    if (!$imageSet)						
			    {
				   echo '<tr><td colSpan="3><div id="prosperPCImage"><img style="text-align:center;" src="' . $product['image_url'] . '"/></div></td>';
				   $imageSet = true;
			    }
			    
				echo '<tr itemscope itemtype="http://data-vocabulary.org/Product">';
				echo '<td itemprop="seller""><a href="' . $product['affiliate_url'] . '" rel="nolink"><img style="width:80px;height:40px;" src="http://images.prosperentcdn.com/images/logo/merchant/' . ($pieces['imgt'] ? $pieces['imgt'] : 'original') . '/120x60/' . $product['merchantId'] . '.jpg?prosp=&m=' . $product['merchant'] . '"/></a></td>';
				echo '<td itemprop="price" style="vertical-align:middle;">$' . ($priceSale ? number_format($priceSale, 2, '.', ',') :  number_format($product['price'], 2, '.', ',')) . '</td>';
				echo '<meta itemprop="priceCurrency" content="USD"/>';
				echo '<td style="vertical-align:middle;"><div class="prosperVisit"><a itemprop="offerURL" href="' . $product['affiliate_url'] . '"  rel="nofollow,nolink"><input type="submit" type="submit" class="prosperVisitSubmit" value="' . ($params['prodvisit'] ? $params['prodvisit'] : 'Visit Store') . '"/></a></div></td>';
				echo '</tr>';
			}
			?>
			</tr>
		</table>
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
