<?php
echo '<a href="javascript:void(0);" id="prosperCloseCheck" onClick="closeMainPreview();"><i class="fa fa-times"></i></a>';
error_reporting(0);   
$params = array_filter($_GET); 
$type = $params['type'];
$view = $params[$type . 'view'];
$fetch = $params[$type . 'fetch'];

$endPoints = array(
	'fetchMerchant'	   => 'http://api.prosperent.com/api/merchant?',
	'fetchProducts'	   => 'http://api.prosperent.com/api/search?',
	'fetchTrends'	   => 'http://api.prosperent.com/api/trends?'
);

if ($params[$type . 'id'] && strpos($params[$type . 'id'], '~'))
{
    $id = explode('~', rtrim($params[$type . 'id'], '~'));

}
elseif ($params[$type . 'id'])
{
    $filterType = ($type == 'prod' ? 'Product' : 'Merchant') . 'Id';
    $id = explode(',', rtrim($params[$type . 'id'], ','));
}

$limit = 1;
if ($params[$type . 'limit'] > 1)
{
    $limit = $params[$type . 'limit'];
}
elseif ($id)
{
    $limit = count($id);
}

$mainSettings = array(
    'api_key'        => $params['apiKey'],
    'enableFacets'	 => 'FALSE'
);

if ($fetch === 'fetchProducts')
{
    $expiration = PROSPER_CACHE_PRODS;
    $recordId 	= 'catalogId';
    $currency = 'USD';
    	
    if ($view == 'pc')
    { 
        $idFilter = array();
        if (strlen($params[$type . 'id']) == 32 && strpos($params[$type . 'id'], '_'))
        {
            $idFilter = array('filter' . $filterType => $params[$type . 'id']);
        }
        elseif ($params[$type . 'id'])
        {
            $idFilter = array('query' => rtrim(str_replace('_', ' ', $params[$type . 'id']), '~'));
        }

        $settings = array(
            'query'              => (!$id ? trim(strip_tags($params[$type . 'q'] ? $params[$type . 'q'] : '')) : ''),
            'imageSize'		     => '250x250',
            'groupBy'            => 'merchant',
            'limit'              => 5
        );
        $curlUrls[0] = $endPoints[$fetch] . http_build_query (array_merge($mainSettings, $settings, $idFilter));
    }
    elseif (count($id))
    {
        foreach ($id as $i => $apart)
        {
            if ($filterType == 'ProductId')
            {
                $settings[$i] = array(
                    'imageSize'		  => '250x250',
                    'limit'           => 1,
                    'filterProductId' => $apart
                );
            }
            else
            {
                $filterType = 'Keyword';
                 
                $settings[$i] = array(
                    'imageSize'	=> '250x250',
                    'limit'     => 1,
                    'query'     => $apart
                );
            }
             
            $curlUrls[$i] = $endPoints[$fetch] . http_build_query (array_merge($mainSettings, $settings[$i]));
        }      
    }
    else
    {
        $settings = array(
            'imageSize'		   => '250x250',
            'limit'            => $limit,
            'query'            => $params[$type . 'q'] ? trim(strip_tags($params[$type . 'q'])) : '',
            'filterMerchantId' => $params['prodd'] ? str_replace(',', '|', $params['prodd']) : '',
            'filterBrand'	   => $params['prodb'] ? str_replace(',', '|', $params['prodb']) : '',
            'filterPriceSale'  => $params['onSale'] ? (($params['pricerangea'] || $params['pricerangeb']) ? $params['pricerangea'] . ',' . $params['pricerangeb'] : '0.01,') : '',
            'filterPrice' 	   => $params['onSale'] ? '' : (($params['pricerangea'] || $params['pricerangeb']) ? $params['pricerangea'] . ',' . $params['pricerangeb'] : ''),
        );

        $curlUrls[0] = $endPoints[$fetch] . http_build_query (array_merge($mainSettings, $settings));
    }
}
elseif ($fetch === 'fetchMerchant')
{
    $recordId 	 = 'merchantId';
    $type 		 = 'merchant';
    $pieces['v'] = 'grid';

    $settings = array(
        'imageSize'		   => '120x60',
        'limit'            => $limit,
        'filterMerchant'   => (!$id ? str_replace(',', '|', $params['merchantm']) : ''),
        'filterMerchantId' => $id,
        'filterCategory'   => !$id && $params['merchantcat'] ? '*' . $params['merchantcat'] . '*' : '',
        'imageType'		   => $params['imageType'] ? $params['imageType'] : 'original'
    );
    $curlUrls[0] = $endPoints[$fetch] . http_build_query (array_merge($mainSettings, $settings));
}

// array of curl handles
$curly = array();
// data to be returned
$result = array();

// multi handle
$mh = curl_multi_init();

// loop through $data and create curl handles
// then add them to the multi-handle
foreach ($curlUrls as $id => $url) 
{
	$curly[$id] = curl_init();

	curl_setopt_array($curly[$id], array(CURLOPT_URL => $url,
		CURLOPT_HEADER 		   => 0,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_TIMEOUT 	   => 30,
		CURLOPT_CONNECTTIMEOUT => 30
		)
	);

	curl_multi_add_handle($mh, $curly[$id]);
}

// execute the handles
$running = null;
do 
{
	curl_multi_exec($mh, $running);
} while($running > 0);


// get content and remove handles
foreach($curly as $id => $c) 
{
	$result[$id] = json_decode(curl_multi_getcontent($c), true);
	curl_multi_remove_handle($mh, $c);
}

// all done
curl_multi_close($mh);

// Check for errors
if (count($response['errors']))
{
	throw new Exception(implode('; ', $response['errors']));
}

$everything = array();
if (count($result) == 1)
{
    $everything = $result[0];
}
else
{
    foreach($result as $i => $record)
    {
        $everything['data'][$i] = $record['data'][0];
    }
}

if ($results = $everything['data'])
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
        							<a href="javascript:void(0);" onClick="return false;" rel="nofollow,nolink"><input type="submit" id="submit" class="submit button-primary" value="<?php echo ($params['prodvisit'] ? $params['prodvisit'] : 'Visit Store'); ?>"/></a>				
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
        				echo '<td class="prosperPCmercimg" itemprop="seller" style="vertical-align:middle;"><a href="' . $product['affiliate_url'] . '" rel="nolink"><img style="width:100px" src="http://images.prosperentcdn.com/images/logo/merchant/' . ($pieces['imgt'] ? $pieces['imgt'] : 'original') . '/120x60/' . $product['merchantId'] . '.jpg?prosp=&m=' . $product['merchant'] . '"/></a></td>';
        				echo '<td itemprop="price" style="vertical-align:middle;">$' . ($priceSale ? number_format($priceSale, 2, '.', ',') :  number_format($product['price'], 2, '.', ',')) . '</td>';
        				echo '<meta itemprop="priceCurrency" content="USD"/>';
        				echo '<td style="vertical-align:middle;"><div class="prosperVisit"><a itemprop="offerURL" href="' . $product['affiliate_url'] . '"  rel="nofollow,nolink"><input type="submit" type="submit" class="prosperVisitSubmit button-primary" value="' . ($params['prodvisit'] ? $params['prodvisit'] : 'Visit Store') . '"/></a></div></td>';
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
        						<a href="javascript:void(0);" onClick="return false;" rel="nofollow,nolink"><input id="submit" class="submit button-primary" type="submit" value="<?php echo ($params['prodvisit'] ? $params['prodvisit'] : 'Visit Store'); ?>"/></a>				
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

