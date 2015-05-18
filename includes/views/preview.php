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
	$merchants = array_map('trim', explode(',', $params['merchantm']));

	$settings = array(
		'filterMerchant' =>  $merchants[0] ? '*' . $merchants[0] . '*' : '',
	    'filterCategory' => $params['merchantcategory'] ? '*' . $params['merchantcategory'] . '*' : '',
	    'imageType'      => ($params['imageType'] ? trim($params['imageType']) : 'original'),
		'imageSize'		 => '120x60'
	);
}
else
{
	$fetch = 'fetchProducts';

	$merchants = array_map('trim', explode(',', $params['prodm']));
	$brands = array_map('trim', explode(',', $params['prodb']));

	$settings = array(
		'query'            => trim($params['prodq'] ? $params['prodq'] : 'shoes'),
		'filterMerchant'   => $merchants,
		'filterBrand'      => $brands,
		'imageSize'		   => '250x250',
		'groupBy'	       => 'productId',
		'filterPriceSale'  => $params['onSale'] ? ($params['pricerangea'] || $params['pricerangeb'] ? $params['pricerangea'] . ',' . $params['pricerangeb'] : '0.01,') : '',
		'filterPrice' 	   => $params['onSale'] ? '' : ($params['pricerangea'] || $params['pricerangeb'] ? $params['pricerangea'] . ',' . $params['pricerangeb'] : '')
	);
}

$settings = array_merge(array(
	'api_key'        => '7b0a5297441c39be99fda92fc784b516',
	'limit'          => 99,
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
	<h2>Results: <span style="font-size:15px;color:#000;">Select specific products or use the results in order.</span></h2>
	<div id="productList">
    	<ul style="display:inline-block;padding:0;margin:0;">
        	<?php
        	if ($type == 'merchant')
        	{
        		foreach ($results as $record)
        		{			
        		    if ($record['deepLinking'] == 1)
        		    {
        				$prosperId = $record['merchantId'];
        				?>
        				<li id="<?php echo $prosperId; ?>" onClick="getIdofItem(this);" class="productSCFull" style="overflow:hidden;list-style:none;margin:4px;float:left;height:80px!important;width:185px!important;background-color:white;">
            				<div class="listBlock">
            					<div class="prodImage" style="text-align:center;">        				
        				        	<span title="<?php echo $record['merchant']; ?>"><img class="newImage" style="height:60px!important;width:120px!important;" src='<?php echo $record['logoUrl']; ?>'  alt='<?php echo $record['merchant']; ?>' title='<?php echo $record['merchant']; ?>'/></span>
        				        	<div class="prodTitle">
            							<?php echo $record['merchant']; ?>
            						</div>   
        				        </div>		
    			            </div>   
        				</li>
        				<?php  
        		    }       				
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
    				<li id="<?php echo $prosperId; ?>" onClick="getIdofItem(this);" class="productSCFull" style="overflow:hidden;list-style:none;margin:4px;float:left;height:208px!important;width:185px!important;background-color:white;">
        				<div class="listBlock">
        					<div class="prodImage" style="text-align:center;">        				
    				        	<span title="<?php echo $record['keyword']; ?>"><img class="newImage" style="height:150px;width:150px;" src='<?php echo ($record['logoUrl'] ? $record['logoUrl'] : $record['image_url'] ); ?>'  alt='<?php echo $record['keyword']; ?>' title='<?php echo $record['keyword']; ?>'/></span>
    				        </div>
    				        <div class="prodContent">
    				            <?php echo ($record['brand'] ? $record['brand'] : '&nbsp;'); ?>
        						<div class="prodTitle">
        							<div class="prodPrice"><strong><?php echo ($currency == 'GBP' ? '&pound;' : '$') . number_format($price, 2); ?></strong><?php if ($record['merchant']){echo '<span class="merchantIn" style="color:#666"> from ' . $record['merchant'] . '</span>'; } ?></div>
        						</div>            						                   
        					</div>			
			            </div>   
    				</li>
    				<?php 
        		}	        
        	}
        	?>
	   </ul>
	</div>
	<?php
}
else
{
	echo '<h2>No Results</h2>';
	echo '<div class="noResults-secondary">Please try another search.</div>';
}
