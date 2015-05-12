<?php
error_reporting(0);   
$params = array_filter($_GET); 
$type = $params['type'];

$endPoints = array(
	'fetchMerchant'	   => 'http://api.prosperent.com/api/merchant?',
	'fetchProducts'	   => 'http://api.prosperent.com/api/search?',
	'fetchUkProducts'  => 'http://api.prosperent.com/api/uk/search?',
	'fetchCaProducts'  => 'http://api.prosperent.com/api/ca/search?',
	'fetchCoupons'	   => 'http://api.prosperent.com/api/coupon/search?',
	'fetchLocal'	   => 'http://api.prosperent.com/api/local/search?',
	'fetchCelebrities' => 'http://api.prosperent.com/api/celebrity?',
	'fetchTrends'	   => 'http://api.prosperent.com/api/trends?'
);

$states = array(
	'alabama'		 =>'AL',
	'alaska'		 =>'AK',
	'arizona'		 =>'AZ',
	'arkansas'		 =>'AR',
	'california'	 =>'CA',
	'colorado'		 =>'CO',
	'connecticut'	 =>'CT',
	'DC'	 		 =>'DC',
	'delaware'		 =>'DE',
	'florida'		 =>'FL',
	'georgia'		 =>'GA',
	'hawaii'		 =>'HI',
	'idaho'		 	 =>'ID',
	'illinois'		 =>'IL',
	'indiana'		 =>'IN',
	'iowa'			 =>'IA',
	'kansas'		 =>'KS',
	'kentucky'		 =>'KY',
	'louisiana'		 =>'LA',
	'maine'			 =>'ME',
	'maryland'		 =>'MD',
	'massachusetts'	 =>'MA',
	'michigan'		 =>'MI',
	'minnesota'		 =>'MN',
	'mississippi'	 =>'MS',
	'missouri'		 =>'MO',
	'montana'		 =>'MT',
	'nebraska'		 =>'NE',
	'nevada'		 =>'NV',
	'new hampshire'	 =>'NH',
	'new jersey'	 =>'NJ',
	'new mexico'	 =>'NM',
	'new york'		 =>'NY',
	'north carolina' =>'NC',
	'north dakota'	 =>'ND',
	'ohio'			 =>'OH',
	'oklahoma'		 =>'OK',
	'oregon'		 =>'OR',
	'pennsylvania'	 =>'PA',
	'rhode island'   =>'RI',
	'south carolina' =>'SC',
	'south dakota'   =>'SD',
	'tennessee'      =>'TN',
	'texas'			 =>'TX',
	'utah'			 =>'UT',
	'vermont'		 =>'VT',
	'virginia'		 =>'VA',
	'washington'	 =>'WA',
	'west virginia'	 =>'WV',
	'wisconsin'		 =>'WI',
	'wyoming'		 =>'WY'
);

if ($type == 'coup')
{
	$fetch = 'fetchCoupons';
	$merchants = array_map('trim', explode(',', $params['coupm']));

	$key = array_search('Zappos.com', $merchants);
	if ($key || 0 === $key)
	{
		unset($merchants[$key]);
	}

	$key2 = array_search('6pm', $merchants);
	if ($key2 || 0 === $key2)
	{
		unset($merchants[$key2]);
	}

	$merchants = array_merge($merchants, array('!Zappos.com', '!6pm'));

	$settings = array(
		'query'          => trim($params['coupq']),
		'filterMerchant' => $merchants,
		'imageSize'		 => '120x60'
	);

}
elseif ($type == 'merchant')
{
	$fetch = 'fetchMerchant';
	$merchants = array_map('trim', explode(',', $params['merchantm']));

	$settings = array(
		'filterMerchant' =>  '*' . $merchants[0] . '*',
	    'imageType'      => ($params['imageType'] ? trim($params['imageType']) : 'original'),
		'imageSize'		 => '120x60'
	);

}
elseif ($type == 'local')
{
	$fetch = 'fetchLocal';
	
	if (strlen($params['state']) > 2)
	{
		$state = $states[strtolower(trim($params['state']))];
	}
	else
	{
		$state = trim($params['state']);
	}
	
	$merchants = array_map('trim', explode(',', $params['localm']));

	$settings = array(
		'query'          => trim($params['localq']),
		'filterMerchant' => $merchants,
		'filterState'	 => $state,
		'filterCity'	 => trim($params['city']),
		'filterZipCode'	 => trim($params['zipCode']),
		'imageSize'		 => '250x250'
	);
}
else
{
	if ($params['country'] === 'UK')
	{
		$currency = 'GBP';
		$fetch = 'fetchUkProducts';
	}
	elseif ($params['country'] === 'CA')
	{
		$fetch = 'fetchCaProducts';
	}
	else 
	{		
		$fetch = 'fetchProducts';
	}

	$merchants = array_map('trim', explode(',', $params['prodm']));
	$brands = array_map('trim', explode(',', $params['prodb']));

	$settings = array(
		'query'            => trim($params['prodq'] ? $params['prodq'] : 'shoes'),
		'filterMerchant'   => $merchants,
		'filterCelebrity'  => $params['prodcelebname'],
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
        		foreach ($results as $record)
        		{			
        		    $priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
        		    $price 	   = $priceSale ? $priceSale : $record['price'];
        		    
        			if ($type == 'coup')
        			{
        				$prosperId = $record['couponId'];
        				?>
        				<li id="<?php echo $prosperId; ?>" onClick="getIdofItem(this);" class="productSCFull" style="overflow:hidden;list-style:none;margin:4px;float:left;height:105px!important;width:185px!important;background-color:white;">
            				<div class="listBlock">
            					<div class="prodImage" style="text-align:center;">        				
        				        	<span title="<?php echo $record['keyword']; ?>"><img class="newImage" style="height:60px;width:120px;" src='<?php echo ($record['logoUrl'] ? $record['logoUrl'] : $record['image_url'] ); ?>'  alt='<?php echo $record['keyword']; ?>' title='<?php echo $record['keyword']; ?>'/></span>
        				        </div>
        				        <div class="prodContent">
            						<div class="prodTitle">
            							<div class="prodPrice"><strong><?php echo ($price ? ($currency == 'GBP' ? '&pound;' : '$') . number_format($price, 2) : ($record['promo'] ? $record['promo'] : '')) ; ?></strong><?php if ($record['merchant']){echo '<span class="merchantIn" style="color:#666"> from ' . $record['merchant'] . '</span>'; } ?></div>
            						</div>            						                   
            					</div>			
    			            </div>   
        				</li>
        				<?php 
        			}
        			elseif ($type == 'merchant' && $record['deepLinking'] == 1)
        			{
        				$prosperId = $record['merchantId'];
        				?>
        				<li id="<?php echo $prosperId; ?>" onClick="getIdofItem(this);" class="productSCFull" style="overflow:hidden;list-style:none;margin:4px;float:left;height:60px!important;width:185px!important;background-color:white;">
            				<div class="listBlock">
            					<div class="prodImage" style="text-align:center;">        				
        				        	<span title="<?php echo $record['keyword']; ?>"><img class="newImage" style="height:60px;width:120px;" src='<?php echo ($record['logoUrl'] ? $record['logoUrl'] : $record['image_url'] ); ?>'  alt='<?php echo $record['keyword']; ?>' title='<?php echo $record['keyword']; ?>'/></span>
        				        </div>		
    			            </div>   
        				</li>
        				<?php         				
        			}
        			elseif ($type == 'local')
        			{
        				$prosperId = $record['localId'];
        				?>
        				<li id="<?php echo $prosperId; ?>" onClick="getIdofItem(this);" class="productSCFull" style="overflow:hidden;list-style:none;margin:4px;float:left;height:208px!important;width:185px!important;background-color:white;">
            				<div class="listBlock">
            					<div class="prodImage" style="text-align:center;">        				
        				        	<span title="<?php echo $record['keyword']; ?>"><img class="newImage" style="height:150px;width:150px;" src='<?php echo ($record['logoUrl'] ? $record['logoUrl'] : $record['image_url'] ); ?>'  alt='<?php echo $record['keyword']; ?>' title='<?php echo $record['keyword']; ?>'/></span>
        				        </div>
        				        <div class="prodContent">
        				            <?php echo ($record['city'] ? $record['city'] . ', ' : ''); ?>
        				            <?php echo ($record['zipCode'] ? $record['zipCode'] : '&nbsp;'); ?>
            						<div class="prodTitle">
            							<?php if ($record['merchant']){echo '<span class="merchantIn" style="color:#666"> from ' . $record['merchant'] . '</span>'; } ?>
            						</div>            						                   
            					</div>			
    			            </div>   
        				</li>
        				<?php 
        			}
        			else
        			{
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
        
        			?>
        			
    				
        		<?php
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
