<?php
error_reporting(0);   
$params = array_filter($_GET); 
$type = $params['type'];
$view = $params['prodview'];

$endPoints = array(
	'fetchMerchant'	   => 'http://api.prosperent.com/api/merchant?',
	'fetchProducts'	   => 'http://api.prosperent.com/api/search?',
	'fetchTrends'	   => 'http://api.prosperent.com/api/trends?'
);

if ($type == 'merchant')
{
	$fetch = 'fetchMerchant';
	$merchants  = array_map('trim', explode(',', urldecode($params['merchantm'])));
	$merchantId = array_map('trim', explode(',', urldecode($params['merchantid'])));

	$settings = array(
		'filterMerchant'   =>  (!$merchantId ? ($merchants[0] ? '*' . $merchants[0] . '*' : '') : ''),
	    'filterMerchantId' =>  $merchantId,
	    'filterCategory'   => $params['merchantcat'] ? '*' . $params['merchantcat'] . '*' : '',
	    'imageType'        => ($params['imageType'] ? trim($params['imageType']) : 'original'),
		'imageSize'		   => '120x60'
	);
}
else
{
	$fetch = 'fetchProducts';

	$merchants   = array_map('trim', explode(',', $params['prodm']));
	$merchantIds = array_map('trim', explode(',', $params['prodd']));
	$brands      = array_map('trim', explode(',', $params['prodb']));
	$productIds   = array_map('trim', explode(',', $params['prodid']));

	$settings = array(
		'query'            => trim($params['prodq'] ? $params['prodq'] : 'shoes'),
		'filterProductId'  => $productIds,
	    'filterMerchantId' => $merchantIds,
	    'filterMerchant'   => $merchants,
		'filterBrand'      => $brands,
		'imageSize'		   => '250x250',
		'groupBy'	       => 'productId',
	    'limit'            => count($productIds),
		'filterPercentOff' => $params['percentrangea'] || $params['percentrangeb'] ? $params['percentrangea'] . ',' . $params['percentrangeb'] : '',
	    'filterPriceSale'  => $params['onSale'] ? ($params['pricerangea'] || $params['pricerangeb'] ? $params['pricerangea'] . ',' . $params['pricerangeb'] : '0.01,') : '',
		'filterPrice' 	   => $params['onSale'] ? '' : ($params['pricerangea'] || $params['pricerangeb'] ? $params['pricerangea'] . ',' . $params['pricerangeb'] : '')
	);
	
	if ($view == 'pc')
	{
	    $productIds = array_map('trim', explode(',', $params['prodid']));
	    
    	$settings = array(
    	    'query'           => trim($params['prodq'] ? $params['prodq'] : 'shoes'),
    	    'filterProductId' => $productIds,
    	    'imageSize'		  => '250x250',
    	    'groupBy'         => 'merchant',
            'limit'           => 5,    	    
    	);
	}
}

$settings = array_merge(array(
	'api_key'        => '7b0a5297441c39be99fda92fc784b516',
	'enableFacets'	 => 'FALSE'
), $settings);

// Set the URL
$url = $endPoints[$fetch] . http_build_query ($settings);

$curl = curl_init();

// Set options
curl_setopt_array($curl, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => $url,
	CURLOPT_CONNECTTIMEOUT => 30,
	CURLOPT_TIMEOUT => 30
));

// Send the request
$response = curl_exec($curl);

// Close request
curl_close($curl);

// Convert the json response to an array
$response = json_decode($response, true);

// Check for errors
if (count($response['errors']))
{
	throw new Exception(implode('; ', $response['errors']));
}

if ($results = $response['data'])
{
	if ($type == 'merchant')
	{
		foreach ($results as $record)
		{			
			$prosperId = $record['merchantId'];
			?>
			<li id="<?php echo $prosperId; ?>" onClick="getIdofItem(this);" class="productSCFull" style="overflow:hidden;list-style:none;margin:9px;float:left;height:76px!important;width:136px!important;background-color:<?php echo ($params['imageType'] == 'white' ? $record['color1'] : '#fff'); ?>"">
				<div class="listBlock">
					<div class="prodImage" style="text-align:center;margin:8px;">        				
			        	<span title="<?php echo $record['merchant']; ?>"><img class="newImage" style="height:60px!important;width:120px!important;" src='<?php echo $record['logoUrl']; ?>'  alt='<?php echo $record['merchant']; ?>' title='<?php echo $record['merchant']; ?>'/></span>
			        </div>		
	            </div>   
			</li>
			<?php       				
		}
	}
	else
	{
    	if ($view === 'grid')
        {
        	$gridImage = ($params['gimgsz'] ? preg_replace('/\s?(px|em|%)/i', '', $params['gimgsz']) : 200) . 'px';
        	$classLoad = $gridImage < 120 ? 'class="loadCoup"' : 'class="load"';
        	?>
        	<div id="simProd">
        		<ul>
        		<?php
        		foreach ($results as $record)
        		{
        			$record['image_url'] = ($record['image_url'] ? $record['image_url'] : $record['logoUrl']);	
        			
        			$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
        			$price 	   = $priceSale ? '$' . number_format($priceSale, 2) . '' : '$' . number_format($record['price'], 2);
        			$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
        			$cid 	   = $record[$recordId];

        			?>
        				<li <?php echo 'style="width:' . $gridImage . '!important;"'; ?>>
        					<div class="listBlock">
        						<div class="prodImage">
        							<a href="javascript:void(0);" onClick="return false;"><span <?php echo $classLoad . ($type != 'merchant' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px"'); ?>><img <?php echo ($type != 'merchant' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px"'); ?> src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
        						</div>						
        						<div class="prodContent">
        							<div class="prodTitle">
        								<a href="javascript:void(0);" onClick="return false;">
        									<?php echo $keyword; ?>
        								</a>
        							</div>                    
        							<?php if ($price): ?>
        							<div class="prodPrice">
						                <?php echo $price . '<span style="color:#666;font-size:12px;font-weight:normal;"> from</span><br><div style="color:#666;font-size:14px;font-weight:normal;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;width:' . ($gridImage - 5) . 'px!important;">' . $record['merchant'] . '</div>' ?>
						            </div>	        							
        							<?php endif; ?>
        						</div>
        
        						<?php if ($type != 'merchant') : ?>
        						<div class="shopCheck prosperVisit">		
        							<a href="javascript:void(0);" onClick="return false;" rel="nofollow,nolink"><input type="submit" id="submit" class="submit" value="<?php echo ($params['prodvisit'] ? $params['prodvisit'] : 'Visit Store'); ?>"/></a>				
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
        elseif ($view === 'pc')
        {  
            $gridImage = ($params['gimgsz'] ? preg_replace('/\s?(px|em|%)/i', '', $params['gimgsz']) : 200) . 'px';
            $classLoad = ($type === 'merchant' ? '' : ($gridImage < 120 ? 'class="loadCoup"' : 'class="load"'));
            ?>
        	<div id="product">
            	<table class="productResults" itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer" style="<?php echo ($params['prodImageType'] == 'white' ? 'color:white;' : 'background:white;'); ?>width:45%">        		
            		<?php	           
    				foreach ($results as $product)
    				{						
    				    $priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
    				    $price 	   = $priceSale ? $priceSale : $record['price'];
    				    $goToUrl   = '"' . $record['affiliate_url'] . '" rel="nofollow,nolink" class="shopCheck" target="_blank"';		
    				    if (!$imageSet)						
    				    {
    					   echo '<tr><td colSpan="3><div id="prosperPCImage" sty><img style="text-align:center;" src="' . $product['image_url'] . '"/></div></td></tr>';
    					   $imageSet = true;
    				    }
    				    if (!$keywordSet)
    				    {
    					   echo '<tr><td colSpan="3"><div id="prosperPCKeyword">' . $product['keyword'] . '</div></td></tr>';
    					   $keywordSet = true;
    				    }
    					echo '<tr itemscope itemtype="http://data-vocabulary.org/Product">';
    					echo '<td itemprop="seller" style="vertical-align:middle;"><a href="javascript:void(0);" onClick="return false;" rel="nolink"><img style="width:80px;height:40px;" src="http://images.prosperentcdn.com/images/logo/merchant/' . $params['prodImageType'] . '/120x60/' . $product['merchantId'] . '.jpg?prosp=&m=' . $product['merchant'] . '"/></a></td>';
    					echo '<td itemprop="price" style="vertical-align:middle;">$' . ($priceSale ? number_format($priceSale, 2, '.', ',') :  number_format($product['price'], 2, '.', ',')) . '</td>';
    					echo '<meta itemprop="priceCurrency" content="USD"/>';
    					echo '<td style="vertical-align:middle;"><div class="prosperVisit"><a itemprop="offerURL" href="javascript:void(0);" onClick="return false;" rel="nofollow,nolink"><input type="submit" type="submit" class="prosperVisitSubmit" value="' . ($params['prodvisit'] ? $params['prodvisit'] : 'Visit Store') . '"/></a></div></td>';
    					echo '</tr>';
    				}
    				?>
    			</table>
			</div>
        	<?php
        }
        else
        { 
        	?>
        	<div id="productList" style="width:98%;margin:0 auto;border:none;border-top:1px solid #ddd;background-color:white;margin-top:5px;">
        		<?php
        		// Loop to return Products and corresponding information
        		foreach ($results as $record)
        		{			       					
        			?>
        			<div class="productBlock">
        				<div class="productImage">
        					<a href="javascript:void(0);" onClick="return false;"><span class="load"><img style="height:135px;width:135px;" src="<?php echo $record['image_url']; ?>" title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
        				</div>
        				<div class="productContent">
        					<div class="productTitle"><a href="javascript:void(0);" onClick="return false;"><span><?php echo $record['keyword']; ?></span></a></div>
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
        							echo '<span class="brandIn"><u>Brand</u>: <a href="javascript:void(0);" onClick="return false;"><cite>' . $record['brand'] . '</cite></a></span>';
        						}
        						if($record['merchant'])
        						{
        							echo '<span class="merchantIn"><u>Merchant</u>: <a href="javascript:void(0);" onClick="return false;"><cite>' . $record['merchant'] . '</cite></a></span>';
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
        						<a href="javascript:void(0);" onClick="return false;" rel="nofollow,nolink"><input id="submit" class="submit" type="submit" value="<?php echo ($params['prodvisit'] ? $params['prodvisit'] : 'Visit Store'); ?>"/></a>				
        					</div>	
        				</div>
        			</div>
        			<?php
        		}
        		?>
        	</div>
        	<?php 
        } 
	}
	?>
	<div style="clear:both;"></div>
	<cite style="margin:12px;display:block;float:left;font-size:14px;font-weight:bold;">* Some styling may be different when it is on your blog.</cite>
	<?php 
}
else
{
	echo '<h2>No Results</h2>';
	echo '<div class="noResults-secondary">Please try another search.</div>';
}

