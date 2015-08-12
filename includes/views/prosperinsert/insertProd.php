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
            if ($record['deepLinking'] == 1 && $pieces['gtm'])
            {
                if ($record['domain'] == 'sportsauthority.com')
                {
                    $record['domain'] = $record['domain'] . '%2Fhome%2Findex.jsp';
                }

                $goToUrl = 'http://prosperent.com/api/linkaffiliator/redirect?apiKey=' . $this->_options['Api_Key'] . '&sid=' . $sid . '&url=' . rawurlencode('http://' . $record['domain']) . '&interface=wp';
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
	<div id="simProd" class="prosperInsert">
		<ul>
		<?php
		foreach ($results as $record)
		{
			$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
			$price 	   = $priceSale ? '$' . number_format($priceSale, 2) . '' : '$' . number_format($record['price'], 2);
			$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
			$cid 	   = $record[$recordId];

			if ($this->_options['PSAct'] && (!$pieces['gtm'] || $pieces['gtm'] === 'false' || $pieces['gtm'] === 'prodPage'))
			{
				$goToUrl = '"' . $homeUrl . '/product/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink"';
			}
			else
			{
				$goToUrl = '"' . $record['affiliate_url'] . '&interface=wp&subinterface=prosperinsert" rel="nofollow,nolink" class="shopCheck" target="' . $target . '"';
			}
			?>
		        <li id="<?php echo $cid; ?>" class="<?php echo $record['productId']; ?>" data-prosperKeyword="<?php echo $keyword; ?>">
					<div class="prodImage">
						<a href=<?php echo $goToUrl; ?>><span class="prosperLoad"><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
					</div>
					<div class="prodContent">
						<div class="prodTitle">
						    <a href=<?php echo $goToUrl; ?> style="text-decoration:none;color:#646464"><?php echo ($record['brand'] ? $record['brand'] : '&nbsp;'); ?></a>
							<div style="position:absolute;left:-9999em;height:1px;line-height:1px;"><?php echo $record['description']; ?> </div>
						</div>
						<div class="prodPrice">
						    <span class="prosperPrice"><?php echo $price; ?></span><span class="prosperExtra" style="display:inline-block;color:#666;font-size:14px;font-weight:normal;text-overflow:ellipsis;white-space:nowrap;-webkit-hyphens:auto;-moz-hyphens:auto;hyphens:auto;word-wrap:break-word;overflow:hidden;vertical-align:top;"><span style="color:#666;font-size:12px;font-weight:normal;">&nbsp;from </span><?php echo $record['merchant']; ?></span>
						</div>
					</div>
					<div class="shopCheck prosperVisit">
						<a href="<?php echo $record['affiliate_url']. '&interface=wp&subinterface=prosperinsert'; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input id="submit" class="submit" type="submit" value="<?php echo $pieces['vst']; ?>"/></a>
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
    	<table class="productResults" itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer" style="<?php echo ($params['prodImageType'] == 'white' ? 'color:white;' : 'background:white;'); ?>width:80%">
    		<?php
			foreach ($results as $product)
			{
			    $priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
			    $price 	   = $priceSale ? $priceSale : $record['price'];
			    $goToUrl   = '"' . $record['affiliate_url'] . '&interface=wp&subinterface=prosperinsert" rel="nofollow,nolink" class="shopCheck" target="_blank"';
			    if (!$keywordSet)
			    {
			        echo '<tr><td id="prosperPCKeyword" colspan="4" style="width:100%;text-align:center;font-size:1.5em">' . $product['keyword'] . '</td></tr>';
			        $keywordSet = true;
			    }

			    if (!$imageSet)
			    {
			       echo '<tr>';
				   echo '<td id="prosperPCImage" style="vertical-align:middle;width:50%;"><img style="text-align:center;" src="' . $product['image_url'] . '"/></td>';
				   $imageSet = true;
				   echo '<td id="prosperPCMerchants" style="vertical-align:middle;width:50%;"><table id="prosperPCAllMercs" style="border:none;width:100%;margin:0;' .($params['prodImageType'] == 'white' ? 'color:white;' : 'background:white;'). '>';
			    }

			    echo '<tr itemscope itemtype="http://data-vocabulary.org/Product">';
				echo '<td class="prosperPCmercimg" itemprop="seller" style="vertical-align:middle;"><a href="' . $product['affiliate_url'] . '&interface=wp&subinterface=prosperinsert" rel="nolink"><img style="width:100px" src="http://images.prosperentcdn.com/images/logo/merchant/' . ($pieces['imgt'] ? $pieces['imgt'] : 'original') . '/120x60/' . $product['merchantId'] . '.jpg?prosp=&m=' . $product['merchant'] . '"/></a></td>';
				echo '<td itemprop="price" style="vertical-align:middle;">$' . ($priceSale ? number_format($priceSale, 2, '.', ',') :  number_format($product['price'], 2, '.', ',')) . '</td>';
				echo '<meta itemprop="priceCurrency" content="USD"/>';
				echo '<td style="vertical-align:middle;"><div class="prosperVisit"><a itemprop="offerURL" href="' . $product['affiliate_url'] . '&interface=wp&subinterface=prosperinsert"  rel="nofollow,nolink"><input type="submit" type="submit" class="prosperVisitSubmit" value="' . ($params['prodvisit'] ? $params['prodvisit'] : 'Visit Store') . '"/></a></div></td>';
				echo '</tr>';

			}
			?>
			</table>
			</td>
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
			$cid = $record['catalogId'];
			$baseUrl = $homeUrl . '/' . ($this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products');

			if (($this->_options['PSAct'] && $page->post_status == 'publish') && (!$pieces['gtm'] || $pieces['gtm'] === 'false'))
			{
				$goToUrl = '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink"';
			}
			else
			{
				$goToUrl = '"' . $record['affiliate_url'] . '&interface=wp&subinterface=prosperinsert" rel="nofollow,nolink" class="shopCheck" target="' . $target . '"';
			}
			?>
			<div class="productBlock">
				<div class="productImage">
					<a href=<?php echo $goToUrl; ?>><span class="load"><img src="<?php echo $record['image_url']; ?>" title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
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
							echo '<span class="merchantIn"><u>Merchant</u>: <a href=' . ($this->_options['PSAct'] ? '"' . $baseUrl . '/merchant/' . rawurlencode($record['merchant']) . '" rel="nolink"' : $goToUrl) . '><cite>' . $record['merchant'] . '</cite></a></span>';
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
						<a href="<?php echo $record['affiliate_url'] . '&interface=wp&subinterface=prosperinsert'; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $pieces['vst']; ?>"/></a>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}
