<?php
error_reporting(0);   
$params = array_filter($_GET); 
$type = $params['type'];

$endPoints = array(
	'fetchMerchant'	   => 'http://api.prosperent.com/api/merchant?',
	'fetchProducts'	   => 'http://api.prosperent.com/api/search?',
	'fetchTrends'	   => 'http://api.prosperent.com/api/trends?'
);

if ($type == 'merchant')
{
	$fetch = 'fetchMerchant';
	$merchants = array_map('trim', explode(',', urldecode($params['merchantm'])));

	$settings = array(
		'filterMerchant' =>  $merchants[0] ? '*' . $merchants[0] . '*' : '',
	    'filterCategory' => $params['merchantcat'] ? '*' . $params['merchantcat'] . '*' : '',
	    'imageType'      => ($params['imageType'] ? trim($params['imageType']) : 'original'),
		'imageSize'		 => '120x60'
	);
}
else
{
	$fetch = 'fetchProducts';

	$merchantIds = array_map('trim', explode(',', $params['prodd']));
	$brands      = array_map('trim', explode(',', $params['prodb']));

	$settings = array(
		'query'            => trim($params['prodq'] ? $params['prodq'] : 'shoes'),
		'filterMerchantId' => $merchantIds,
		'filterBrand'      => $brands,
		'imageSize'		   => '250x250',
		'groupBy'	       => 'productId',
		'filterPriceSale'  => $params['onSale'] ? ($params['pricerangea'] || $params['pricerangeb'] ? $params['pricerangea'] . ',' . $params['pricerangeb'] : '0.01,') : '',
		'filterPrice' 	   => $params['onSale'] ? '' : ($params['pricerangea'] || $params['pricerangeb'] ? $params['pricerangea'] . ',' . $params['pricerangeb'] : '')
	);
}

$settings = array_merge(array(
	'api_key'        => '7b0a5297441c39be99fda92fc784b516',
	'limit'          => 120,
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
	?>
	<div id="productList">
    	<ul style="display:inline-block;padding:0;margin:0;">
        	<?php
        	if ($type == 'merchant')
        	{
        		foreach ($results as $record)
        		{	
        		    $prosperId = $record['merchantId'];
    				?>
    				<li id="<?php echo $prosperId; ?>" onClick="getIdofItem(this);" class="productSCFull" style="overflow:hidden;list-style:none;margin:6px;float:left;height:86px!important;width:136px!important;background-color:<?php echo ($params['imageType'] == 'white' ? $record['color1'] : '#fff'); ?>">
        				<div class="listBlock">
        					<div class="prodImage" style="text-align:center;">
        					    <span id="prosperCheckbox" style="position:relative;color:<?php echo ($params['imageType'] == 'white' ? '#000!important' : '#fff'); ?>"></span>            				
    				        	<span title="<?php echo $record['merchant']; ?>"><img class="newImage" style="height:60px!important;width:120px!important;" src='<?php echo $record['logoUrl']; ?>'  alt='<?php echo $record['merchant']; ?>' title='<?php echo $record['merchant']; ?>'/></span>  
    				        </div>		
			            </div>   
    				</li>
    				<?php       				
        		}
        	}
			else
			{
			    if ($params['prodview'] == 'pc')
			    {
			        foreach ($results as $record)
			        {			
		              $groupCount = $record['groupCount']; 
		                
			            $priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
			            $price 	   = $priceSale ? $priceSale : $record['price'];			        
			            $prosperId = $record['productId'];			        
			            ?>
        				<li id="<?php echo $prosperId; ?>" onClick="getIdofItem(this);" class="productSCFull" style="overflow:hidden;list-style:none;margin:6px;float:left;height:285px!important;width:210px!important;background-color:white;">
            				<div class="listBlock">
            					<div class="prodImage" style="text-align:center;">
            					    <span id="prosperCheckbox" style="position:relative;"></span>        				
        				        	<span title="<?php echo $record['keyword']; ?>"><img class="newImage" style="height:185px;width:185px;" src='<?php echo ($record['logoUrl'] ? $record['logoUrl'] : $record['image_url'] ); ?>'  alt='<?php echo $record['keyword']; ?>' title='<?php echo $record['keyword']; ?>'/></span>
        				        </div>
        				        <div class="prodContent" style="font-size:15px">
        				            <?php echo ($record['brand'] ? $record['brand'] : '&nbsp;'); ?>
            						<div class="prodTitle">
            						    <?php if ($priceSale): ?>
            						        <span style="color:#666;font-size:14px;text-decoration:line-through;">$<?php echo number_format($record['price'], 2); ?></span>
            						    <?php endif; 
            						    if ($record['groupCount'] > 1):?>
            						        <div class="prodPrice"><strong>$<?php echo number_format($record['minPrice'], 2); ?></strong><?php echo '<span class="merchantIn" style="color:#666;font-size:14px;"> from ' . $groupCount . ' stores</span>'; ?></div>
            						    <?php else: ?>
            							    <div class="prodPrice"><strong>$<?php echo number_format($price, 2); ?></strong><?php if ($record['merchant']){echo '<span class="merchantIn" style="color:#666;font-size:14px;"> from ' . $record['merchant'] . '</span>'; } ?></div>
            							<?php endif; ?>
            						</div>          						          						                   
            					</div>			
    			            </div> 
    			            <div id="prosperCheckbox"></div>
        				</li>
        				<?php 
			           	
			        }	
			    }
			    else
			    {			        
    			    foreach ($results as $record)
    			    {            			         
        			    $priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
        			    $price 	   = $priceSale ? $priceSale : $record['price']; 		   
        				$prosperId = $record['productId'];        				
        				?>
        				<li id="<?php echo $prosperId; ?>" onClick="getIdofItem(this);" class="productSCFull" style="overflow:hidden;list-style:none;margin:6px;float:left;height:275px!important;width:210px!important;background-color:white;">
            				<div class="listBlock">            				    	            				
            					<div class="prodImage" style="text-align:center;vertical-align:top;">    
            					    <span id="prosperCheckbox" style="position:relative;"></span>
        				        	<span title="<?php echo $record['keyword']; ?>"><img class="newImage" style="height:185px;width:185px;" src='<?php echo ($record['logoUrl'] ? $record['logoUrl'] : $record['image_url'] ); ?>'  alt='<?php echo $record['keyword']; ?>' title='<?php echo $record['keyword']; ?>'/></span>
        				        </div>
        				        <div class="prodContent" style="font-size:15px">
        				            <?php echo ($record['brand'] ? $record['brand'] : '&nbsp;'); ?>
            						<div class="prodTitle">            						    
            							<div class="prodPrice">
                							<?php if ($priceSale): ?>
                						        <span style="color:#666;font-size:14px;text-decoration:line-through;">$<?php echo number_format($record['price'], 2); ?></span>
                						    <?php endif; ?>
                							<strong>$<?php echo number_format($price, 2); ?></strong><?php if ($record['merchant']){echo '<span class="merchantIn" style="color:#666;font-size:14px;"> from ' . $record['merchant'] . '</span>'; } ?>
            							</div>
            						</div>          						          						                   
            					</div>	            						
    			            </div>     			            
        				</li>
        				<?php 
            		}	    
			    }    
        	}
        	?>
	   </ul>
	</div>
	
	<script type="text/javascript">
	jQuery(function(){var a=top.tinymce.activeEditor.windowManager.getParams();if(a&&(a=jQuery("<i "+a+">").attr("id"),"undefined"!=typeof a&&null!==a))return a=a.split(","),jQuery.each(a,function(a,b){"undefined"!=typeof b&&null!==b&&0<b.length&&(document.getElementById(b).className+=" highlight")}),!1});
    </script>
	<?php
}
else
{
	echo '<h2>No Results</h2>';
	echo '<div class="noResults-secondary">Please try another search.</div>';
}

