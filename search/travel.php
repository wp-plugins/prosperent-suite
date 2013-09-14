<?php
$travelSubmit = preg_replace('/\/$/', '', $submitUrl);
$newQuery = str_replace('/query/' . $query, '', $travelSubmit);

if ($_POST['country'] || $_POST['state'] || $_POST['city']) 
{
	header('Location: ' . $newQuery . ($_POST['country'] ? '/country/' . urlencode($_POST['country']) : '') . ($_POST['state'] ? '/state/' . urlencode($_POST['state']) : '') . ($_POST['city'] ? '/city/' . urlencode($_POST['city']) : ''));
}

/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
	'api_key'        => $options['Api_Key'],
	'filterCountry'	 => $filterCountry,
	'filterCity'	 => $filterCity,
	'filterState'	 => $filterState,
	'limit'          => $options['Api_Limit'],
	'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
	'sortPrice'	     => $sort,
	'groupBy'	     => 'inventoryId',
	'enableFacets'   => $options['Enable_Facets'],
	'filterBrand'    => $filterBrand,
	'filterMerchant' => $filterMerchant
));

$prosperentApi -> fetchTravel();
$results = $prosperentApi -> getAllData();

$totalFound = $prosperentApi -> getTotalRecordsFound();
$facets = $prosperentApi -> getFacets();

echo $typeSelector;
?>
<div style="width 100% display:inline-block;">
	<form id="searchform" method="POST" action="" style="margin:0;">
		<input class="field" type="text" name="country" id="s" placeholder="Search Country" style="padding:4px 4px 4px;">
		<input class="field" type="text" name="state" id="s" placeholder="Search State" style="padding:4px 4px 4px;">
		<input class="field" type="text" name="city" id="s" placeholder="Search City" style="padding:4px 4px 4px;">
		<select name="distance" style="display:inline;padding:4px 4px 4px;width:80px;">
			<option>Within</option>
			<option value="10">10miles</option>
			<option value="25">25miles</option>
			<option value="50">50miles</option>		
			<option value="100">100miles</option>	
		</select>
		<input type="submit" value="Search" style="padding:5px; font-size:12px;">
	</form>
</div>
<?php
if ($prosperentApi->get_enableFacets() == 1)
{
	$brands = $facets['brand'];
	$merchants = $facets['merchant'];

	if ($brands)
	{
		$brands1 = array_splice($brands, 0, $options['Brand_Facets'] ? $options['Brand_Facets'] : 10);
		$brands2 = $brands;

		$brandNames = array();
		foreach ($brands2 as $brand)
		{
			$brandNames[] = ucfirst($brand['value']);
		}

		array_multisort($brandNames, SORT_REGULAR, $brands2);
	}

	if ($merchants)
	{
		$merchants1 = array_splice($merchants, 0, $options['Merchant_Facets'] ? $options['Merchant_Facets'] : 10);
		$merchants2 = $merchants;

		$merchantNames = array();
		foreach ($merchants2 as $merchant)
		{
			$merchantNames[] = ucfirst($merchant['value']);
		}

		array_multisort($merchantNames, SORT_STRING, $merchants2);
	}

	?>
	<table id="facets">
		<tr>
			<td class="brands">
				<?php
				echo (empty($filterBrand) ? '<div class="browseBrands">Browse by Brand: </div>' : '<div class="filteredBrand">Filtered by Brand: </div>');
				if (empty($facets['brand']) && !$filterBrand)
				{
					echo '<div class="noBrands">No Brands Found</div>';
				}
				else if (!$filterBrand)
				{
					$count = count($brands1);
					foreach ($brands1 as $i => $brand)
					{
						echo '<a href=' . str_replace(array('&brand=', '?brand='), array('', '?'), $submitUrl) . '&brand=' . urlencode($brand['value']) . '>' . $brand['value'] . ' (' . $brand['count'] . ')</a>';

						if ($i < ($count - 1))
						{
							echo ', ';
						}
					}
					if ($brands2)
					{
						if ($filterMerchant)
						{
							?>
							</br>
							<a onclick="toggle_visibility('brandList'); toggle_hidden('moreBrands'); toggle_visibility('hideBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreBrands" style="display:block;">More Brands <img src="<?php echo plugins_url('/img/arrow_down_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
							<a onclick="toggle_hidden('brandList'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="hideBrands" style="display:none;">Hide Brands <img src="<?php echo plugins_url('/img/arrow_up_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
							<?php
						}
						else
						{
							?>
							</br>
							<a onclick="toggle_visibility('brandList'); toggle_hidden('merchantList'); toggle_hidden('moreBrands'); toggle_visibility('hideBrands'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreBrands" style="display:block;">More Brands <img src="<?php echo plugins_url('/img/arrow_down_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
							<a onclick="toggle_hidden('brandList'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="hideBrands" style="display:none;">Hide Brands <img src="<?php echo plugins_url('/img/arrow_up_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
							<?php
						}
					}
				}
				else
				{
					echo '<div style="min-height:35px;">';
					echo $filterBrand;
					echo '</br><a href=' . str_replace(array('&brand=' . urlencode($filterBrand), '?brand=' . urlencode($filterBrand)), array('', '?'), $submitUrl) . '>clear filter</a>';
					echo '<div style="margin-top:-50px;padding-left:150px;"><img src="http://img1.prosperent.com/images/brandlogos/120x60/' . urlencode($filterBrand) . '.png" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
					echo '</div>';
				}
				?>
			</td>
			<td class="merchants">
				<?php
				echo (empty($filterMerchant) ? '<div class="browseMerchants">Browse by Merchant: </div>' : '<div class="filteredMerchants">Filtered by Merchant: </div>');

				if (empty($facets['merchant']) && !$filterMerchant)
				{
					echo '<div class="noMerchants">No Merchants Found</div>';
				}
				else if (!$filterMerchant)
				{
					$count = count($merchants1);
					foreach ($merchants1 as $i => $merchant)
					{
						echo '<a href=' . str_replace(array('&merchant=', '?merchant='), array('', '?'), $submitUrl) . '&merchant=' . urlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a>';

						if ($i < ($count - 1))
						{
							echo ', ';
						}
					}
					if ($merchants2)
					{
						if ($filterBrand)
						{
							?>
							</br>
							<a onclick="toggle_visibility('merchantList'); toggle_hidden('moreMerchants'); toggle_visibility('hideMerchants'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreMerchants" style="display:block;">More Merchants <img src="<?php echo plugins_url('/img/arrow_down_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
							<a onclick="toggle_hidden('merchantList'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); " style="cursor:pointer; font-size:12px;"><span id="hideMerchants" style="display:none;">Hide Merchants <img src="<?php echo plugins_url('/img/arrow_up_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
							<?php
						}
						else
						{
							?>
							</br>
							<a onclick="toggle_visibility('merchantList'); toggle_hidden('brandList'); toggle_hidden('moreMerchants'); toggle_visibility('hideMerchants'); toggle_hidden('hideBrands'); toggle_visibility('moreBrands'); return false;" style="cursor:pointer; font-size:12px;"><span id="moreMerchants" style="display:block;">More Merchants <img src="<?php echo plugins_url('/img/arrow_down_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
							<a onclick="toggle_hidden('merchantList'); toggle_hidden('hideMerchants'); toggle_visibility('moreMerchants'); " style="cursor:pointer; font-size:12px;"><span id="hideMerchants" style="display:none;">Hide Merchants <img src="<?php echo plugins_url('/img/arrow_up_small.png', __FILE__); ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></span></a>
							<?php
						}
					}
				}
				else
				{
					echo '<div style="min-height:35px;">';
					echo $filterMerchant;
					echo '</br><a href=' . str_replace(array('&merchant=' . urlencode($filterMerchant), '?merchant=' . urlencode($filterMerchant)), array('', '?'), $submitUrl) . '>clear filter</a>';
					echo '<div style="margin-top:-50px;padding-left:150px;"><img src="http://img1.prosperent.com/images/logos/120x60/' . urlencode($filterMerchant) . '.png" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
					echo '</div>';
				}
				?>
			</td>
		</tr>
	</table>
	<?php
	if ($brands2)
	{
		?>
		<table id="brandList" style="display:none; font-size:11px; width:100%; table-layout:fixed;">
			<?php
			echo '<th style="padding:3px 0 0 5px; font-size:13px;">More Brands: </th>';

			foreach ($brands2 as  $i => $brand)
			{
				if ($i == 0 || $i % 5 == 0 && $i >= 5)
				{
					echo '<tr>';
				}

				echo '<td style="width:1%; padding:5px; height:30px;"><a href=' . str_replace(array('&brand=', '?brand='), array('', '?'), $submitUrl) . '&brand=' . urlencode($brand['value']) . '>' . $brand['value'] . ' (' . $brand['count'] . ')</a></td>';

				if ($i % 5 == 4 && $i >= 9)
				{
					echo '</tr>';
				}
			}
			?>
		</table>
		<?php
	}
	if ($merchants2)
	{
		?>
		<table id="merchantList" style="display:none; font-size:11px; width:100%;">
			<?php
			echo '<th style="padding:3px 0 0 5px; font-size:13px;">More Merchants: </th>';

			foreach ($merchants2 as $i => $merchant)
			{
				if ($i == 0 || $i % 4 == 0 && $i >= 4)
				{
					echo '<tr>';
				}

				echo '<td style="padding:5px; height:30px; width:1%;"><a href=' . str_replace(array('&merchant=', '?merchant='), array('', '?'), $submitUrl) . '&merchant=' . urlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a></td>';

				if ($i % 4 == 3 && $i >= 7)
				{
					echo '</tr>';
				}
			}
			?>
		</table>
		<?php
	}
	?>
	<div class="table-seperator"></div>
	<?php
}
else
{
	?>
	<div class="table-seperator"></div>
	<?php
}

/*
/  If no results, or the user clicked search when 'Search Products...'
/  was in the search field, displays 'No Results'
*/
if (empty($results))
{
	echo '<div class="noResults">No Results</div>';
	?>
	<div style="padding:10px 0;">
		<form id="searchform" method="GET" action="" style="margin:0;">
			<input type="hidden" name="brand" value="<?php echo $filterBrand; ?>">
			<input type="hidden" name="merchant" value="<?php echo $filterMerchant; ?>">
			<input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 6px;">
			<input class="submit" type="submit" value="Search" style="padding:5px;">
		</form>
	</div>
	<?php
	if ($filterBrand || $filterMerchant)
	{
		echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace(array('&merchant=' . urlencode($filterMerchant), '?merchant=' . urlencode($filterMerchant), '&brand=' . urlencode($filterBrand), '?brand=' . urlencode($filterBrand)), array('', '?', '', '?'), $submitUrl) . '>clear the filter(s)</a> or browse the trending products.</div>';
	}
	else
	{
		echo '<div class="noResults-secondary">Please try your search again or browse the trending products.</div>';
	}
	echo '<div class="noResults-padding"></div>';
	
	// calculate date range
	$prevNumDays = 30;
	$startRange = date('Ymd', time() - 86400 * $prevNumDays);
	$endRange   = date('Ymd');

	// fetch trends from api
	require_once('Prosperent_Api.php');
	$api = new Prosperent_Api(array(
		'enableFacets' => 'productId'
	));

	$api->setDateRange('commission', $startRange, $endRange)
		->fetchTrends();

	// set productId as key in array
	foreach ($api->getFacets('productId') as $data)
	{
		$keys[] = $data['value'];
	}

	// fetch merchant data from api
	$api = new Prosperent_Api(array(
		'api_key'         => $options['Api_Key'],
		'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
		'filterProductId' => $keys,
		'limit' 	      => 15
	));

	$api->fetch();
	$results = $api->getAllData() ;		
	?>

	<div id="productList">
		<?php
		// Loop to return Products and corresponding information
		foreach ($results as $i => $record)
		{
			$record['image_url'] = preg_replace('/\/images\/250x250\//', '/images/125x125/', $record['image_url'])
			?>
			<div class="<?php echo count($results) >= 2 ? 'productBlock' : 'productBlock0'; ?>">
				<div class="productImage">
					<a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>"><span><img src="<?php echo $record['image_url']; ?>"  alt="<?php echo $record['keyword']; ?>" title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
				</div>
				<div class="productContent">
					<div class="productTitle"><a href="<?php $record['affiliate_url']; ?>" target="<?php echo $target; ?>"><span><?php echo $record['keyword']; ?></span></a></div>
					<div class="productDescription"><?php
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
						if($record['brand'] && !$filterBrand)
						{
							echo '<span class="brandIn"><u>Brand</u>: <a href="' . str_replace(array('&brand=', '?brand='), array('', '?'), $submitUrl) . '&brand=' . urlencode($record['brand']) . '"><cite>' . $record['brand'] . '</cite></a></span>';
						}
						if($record['merchant'] && !$filterMerchant)
						{
							echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . str_replace(array('&merchant=', '?merchant='), array('', '?'), $submitUrl) . '&merchant=' . urlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
						}
						?>
					</div>
				</div>
				<div class="productEnd">
					<?php
					if(empty($record['price_sale']) || $record['price'] <= $record['price_sale'])
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
						<div class="productPrice"><span>$<?php echo $record['price']?></span></div>
						<div class="productPriceSale"><span>$<?php echo $record['price_sale']?></span></div>
						<?php
					}
					?>
					<form style="margin:0;" action="<?php echo $record['affiliate_url'] . '" target="' . $target; ?>" method="POST">
						<input type="submit" value="Visit Store"/>
					</form>
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
	echo '<div class="totalFound">' . $totalFound . ' results for <b>' . $filterMerchant ? urldecode($filterMerchant) : strtolower($query) . '</b></div>';
	?>

	<form name="priceSorter" method="GET" action="<?php echo $submitUrl; ?>" style="margin:0; float:right; padding:4px 13px 4px 0;">
		<input type="hidden" name="q" value="<?php echo $query;?>">
		<input type="hidden" name="brand" value="<?php echo $filterBrand;?>">
		<input type="hidden" name="merchant" value="<?php echo $filterMerchant;?>">
		<input type="hidden" name="type" value="<?php echo $type; ?>">
		<label for="PriceSort" style="padding-right:4px; font-size:14px; float:left;">Sort By: </label>
		<select name="sort" onChange="priceSorter.submit();" style="display:inline;">
			<option> -- Select Option -- </option>
			<option value="">Relevancy</option>
			<option value="desc">Price: High to Low</option>
			<option value="asc">Price: Low to High</option>
		</select>
	</form>
	</br>

	<?php

		// Gets the count of results for Pagination
		$productCount = count($results);

		// Pagination limit, can be changed
		$limit = !$options['Pagination_Limit'] ? 10 : $options['Pagination_Limit'];

		$pages = round($productCount / $limit, 0);
		$ceiling = ceil(($productCount + 1) / $limit);

		if ($pageNumber  < 1)
		{
			$pageNumber  = 1;
		}
		else if ($pageNumber  > $ceiling)
		{
			$pageNumber  = $ceiling;
		}

		$limitLower = ($pageNumber  - 1) * $limit;

		// Breaks the array into smaller chunks for each page depending on $limit
		$results = array_slice($results, $limitLower, $limit, true);
	
	?>

	<div id="productList">
		<?php
		// Loop to return Products and corresponding information
		foreach ($results as $i => $record)
		{
			$record['image_url'] = preg_replace('/\/images\/250x250\//', '/images/125x125/', $record['image_url'])
			?>
			<div class="<?php echo count($results) >= 2 ? 'productBlock' : 'productBlock0'; ?>">
				<div class="productImage">
					<a href="<?php echo $productPage . '/travel/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; //$record['affiliate_url']; ?>" target="<?php echo $target; ?>"><span><img src="<?php echo $record['image_url']; ?>"  alt="<?php echo $record['keyword']; ?>" title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
				</div>
				<div class="productContent">
					<div class="productTitle"><a href="<?php echo $productPage . '/travel/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId'];//$record['affiliate_url']; ?>" target="<?php echo $target; ?>"><span><?php echo $record['keyword']; ?></span></a></div>
					<div class="productDescription"><?php
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
						if($record['brand'] && !$filterBrand)
						{
							echo '<span class="brandIn"><u>Brand</u>: <a href="' . str_replace(array('&brand=', '?brand='), array('', '?'), $submitUrl) . '&brand=' . urlencode($record['brand']) . '"><cite>' . $record['brand'] . '</cite></a></span>';
						}
						if($record['merchant'] && !$filterMerchant)
						{
							echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . str_replace(array('&merchant=', '?merchant='), array('', '?'), $submitUrl) . '&merchant=' . urlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
						}
						?>
					</div>
				</div>
				<div class="productEnd">
					<?php
					if(empty($record['price_sale']) || $record['price'] <= $record['price_sale'])
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
						<div class="productPrice"><span>$<?php echo $record['price']?></span></div>
						<div class="productPriceSale"><span>$<?php echo $record['price_sale']?></span></div>
						<?php
					}
					?>
					<form style="margin:0;" action="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('/', 'http://prosperent.com/store/product/'), array(',SL,', ''), $record['affiliate_url'])) . '" target="' . $target; ?>" method="POST">
						<input type="submit" value="Visit Store"/>
					</form>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}

prosper_pagination($pages, $pages, $sendParams['page']);