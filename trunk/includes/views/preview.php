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
		'filterMerchant' =>  '*' . $merchants[0] . '*'
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
		'imageSize'		 => '125x125'
	);
}
else 
{
	if ($params['country'] === 'UK')
	{
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
		'query'          => trim($params['prodq']),
		'filterMerchant' => $merchants,
		'filterBrand'    => $brands,
		'imageSize'		 => '125x125',
		'groupBy'	     => 'productId',
		'filterPriceSale' => $params['onSale'] ? '0.01,' : ''		
	);
}

$settings = array_merge(array(
	'api_key'        => '7b0a5297441c39be99fda92fc784b516',
	'limit'          => 100,
	'enableFacets'	 => FALSE
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
	<?php
		foreach ($results as $record)
		{			
			if ($type == 'coup')
			{
				$prosperId = $record['couponId'];
			}
			elseif ($type == 'merchant')
			{
				$prosperId = $record['merchantId'];
			}
			elseif ($type == 'local')
			{
				$prosperId = $record['localId'];
			}
			else
			{
				$prosperId = $record['productId'];
			}		
			?>
			<div id="<?php echo $prosperId; ?>" onClick="getIdofItem(this);" class="productSCFull">
				<div class="productBlock">
					<div class='productImage'>
						<span><img class="newImage" src='<?php echo ($record['logoUrl'] ? $record['logoUrl'] : $record['image_url'] ); ?>'  alt='<?php echo $record['keyword']; ?>' title='<?php echo $record['keyword']; ?>'/></span>
					</div>
					<div class='productContent'>
						<div class='productTitle'><span><?php echo $record['keyword']; ?></span></a></div>
						<?php
						if(!empty($record['expiration_date']))
						{
							$expires = strtotime($record['expiration_date']);
							$today = strtotime(date("Y-m-d"));
							$interval = abs($expires - $today) / (60*60*24);

							if ($interval <= 7 && $interval > 0)
							{
								echo '<div class="couponExpire" style=""><span style="color:red; font-weight:bold;">Expires in ' . $interval . ' days!</span></div>';
							}
							else
							{
								echo '<div class="couponExpire"><span style="color:red; font-weight:bold;">Expires Soon!</span></div>';
							}
						}
						if ($record['coupon_code'])
						{
							echo '<div class="couponCode" style="font-weight:bold; padding:5px 0 0 0;">Coupon Code: <span class="code_cc" style="border:2px dashed #3079ed; padding: 2px 3px; font-weight:bold; -moz-border-radius: 8px; -webkit-border-radius: 8px; border-radius:8px;">' . $record['coupon_code'] . '</span></div>';
						}
						
						if ($type != 'coup' && $type != 'local'): ?>
						<div class='productDescription'><?php
							if (strlen($record['description']) > 200)
							{
								echo substr($record['description'], 0, 175) . '...';
							}
							else
							{
								echo $record['description'];
							}
							?>
							<div class="productBrandMerchant">
								<?php
								if($record['brand'] && !$filterBrand)
								{
									echo '<span class="brandIn">Brand: <cite>' . $record['brand'] . '</cite></span><br>';
								}
								if($record['merchant'] && !$filterMerchant)
								{
									echo '<span class="merchantIn">Merchant: <cite>' . $record['merchant'] . '</cite></span><br>';
								}
								if($record['deepLinking'])
								{
									echo '<span class="merchantIn">DeepLinking: <cite>' . ($record['deepLinking'] == 1 ? 'Yes' : 'No') . '</cite></span><br>';
								}
								if($record['category'])
								{
									echo '<span class="merchantIn">Category: <cite>' . $record['category'] . '</cite></span><br>';
								}
								if($record['minPaymentPercentage'] || $record['maxPaymentPercentage'])
								{
									echo '<span class="merchantIn">Payment Percentage: <cite>' . $record['minPaymentPercentage'] . '% to ' . $record['maxPaymentPercentage'] . '%</cite></span><br>';
								}
								?>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php
		}
	?>
	</div>
	<?php
}
else
{
	echo '<div class="noResults">No Results</div>';
	echo '<div class="noResults-secondary">Please try another search.</div>';
}
