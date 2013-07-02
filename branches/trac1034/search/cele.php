<?php
if (!$celeb && $options['Celebrity_Query'])
{
	$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . 'celeb/' . urlencode($options['Celebrity_Query']);
	$c = $options['Celebrity_Query'];
}
else
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $c = $celeb;
	$q = $query;
}

$query = stripslashes($q);
$celeb = stripslashes($c);
$celeSubmit = preg_replace('/\/$/', '', $url);
$newQuery = str_replace('/query/' . $query, '', $coupSubmit);
$newSort = str_replace('/sort/' . $sendParams['sort'], '', $prodSubmit);

if ($_POST['q']) 
{
	header('Location: ' . $newQuery . '/query/' . urlencode($_POST['q']));
}

if ($_POST['sort']) 
{
	header('Location: ' . $newSort . '/sort/' . $_POST['sort']);
}

/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
	'api_key'         => $options['Api_Key'],
	'filterCelebrity' => $celeb ? $celeb : $celebDecode,
	'query'			  => $query,
	'filterBrand'	  => $filterBrand,
	'filterMerchant'  => $filterMerchant,
	'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
	'limit'           => $options['Api_Limit'],
	'sortPrice'	   	  => $sort,
	'enableFacets'    => $options['Enable_Facets'],
));

$celebrityApi = new Prosperent_Api(array(
	'api_key'         => $options['Api_Key'],
	'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
	'limit'           => 500,
	'sortPrice'	   	  => 'celebrity asc'
));

/*
/  Fetching results and pulling back all data
/  To see which data is available to pull back login in to
/  Prosperent.com and click the API tab
*/
$prosperentApi -> fetch();
$results = $prosperentApi -> getAllData();
$totalFound = $prosperentApi -> getTotalRecordsFound();
$celebrityApi->fetchCelebrities();
$celebrityResults = $celebrityApi -> getData();

echo $typeSelector;
?>

<div style="float:right;">
	<form id="searchform" method="POST" action="" style="margin:0;">
		<input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 6px;">
		<input class="submit" type="submit" value="Search" style="padding:5px;">
	</form>
</div>

<table id="facets">
	<tr>
		<td class="merchants" style="width:98%; float:none;">
			<?php
			echo (empty($celeb) ? '<div class="browseMerchants">Browse by Celebrity: </div>' : '<div class="filteredMerchants">Filtered by Celebrity: </div>');

			if (!$celeb)
			{
				foreach ($celebrityResults as $celebs)
				{
					echo '<a style="font-size:12px;" href="' . $celeSubmit . '/celeb/' . urlencode($celebs['celebrity']) . '">' . $celebs['celebrity'] . '</a><span style="font-size:12px; font-weight:bold;"> | </span>';
				}
			}
			else
			{
				echo '<div style="min-height:35px;">';
				echo $celebDecode;
				echo '</br><a href=' . str_replace('/celeb/' . $celeb, '', $celeSubmit) . ' >clear filter</a>';
				echo '<div style="margin-top:-50px;padding-left:150px;"><img src="' . $productPage  . '/img/' . str_replace('/', ',SL,',  ('celebrity/100x100/' . $celebDecode . '.jpg'))  . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
			echo '</div>';
			}
			?>
		</td>
	</tr>
</table>
<?php

/*
/  If no results displays 'No Results'
*/
if (empty($results))
{
	echo '<div class="noResults">No Results</div>';
	?>
	<div style="padding:10px 0;">
		<form id="searchform" method="POST" action="" style="margin:0;">
			<input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 6px;">
			<input class="submit" type="submit" value="Search" style="padding:5px;">
		</form>
	</div>
	<?php
	if ($filterMerchant)
	{
		echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace('/merchant/' . $filterMerchant, '', $prodSubmit) . '>clear the filter(s)</a>.</div>';
	}
	else
	{
		echo '<div class="noResults-secondary">Please try your search again.</div>';
	}
	echo '<div class="noResults-padding"></div>';
		
	// calculate date range
	$prevNumDays = 30;
	$startRange = date('Ymd', time() - 86400 * $prevNumDays);
	$endRange   = date('Ymd');

	// fetch trends from api
	require_once(PROSPER_PATH . 'Prosperent_Api.php');
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
	
	echo '<div class="totalFound">Browse these <strong>trending products</strong></div>';
	?>

	<div id="productList">
		<?php
		// Loop to return Products and corresponding information
		foreach ($results as $i => $record)
		{
			$record['image_url'] = $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url'])));
			?>
			<div class="<?php echo count($results) >= 2 ? 'productBlock' : 'productBlock0'; ?>">
				<div class="productImage">
					<a href="<?php echo $productPage . '/celebrity/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
				</div>
				<div class="productContent">
					<div class="productTitle"><a href="<?php echo $productPage . '/celebrity/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><?php echo $record['keyword']; ?></span></a></div>
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
							echo '<span class="brandIn"><u>Brand</u>: <a href="' . $celeSubmit . '/brand/' . urlencode($record['brand']) . '"><cite>' . $record['brand'] . '</cite></a></span>';
						}
						if($record['merchant'] && !$filterMerchant)
						{
							echo '<span class="merchantIn"><u>Merchant</u>: <a href="' . $celeSubmit . '/merchant/' . urlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a></span>';
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
					<form style="margin:0;" action="<?php echo $productPage . '/store/go/' . urlencode(str_replace('/', ',SL,', $record['affiliate_url'])) . '" target="' . $target; ?>" method="POST">
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
	// Gets the count of results for Pagination
	$productCount = count($results);

	// Pagination limit, can be changed
	$limit = !$options['Pagination_Limit'] ? 15 : $options['Pagination_Limit'];

	$pages = round($productCount / $limit, 0);

	if ($pageNumber  < 1)
	{
		$pageNumber  = 1;
	}
	else if ($pageNumber  > ceil(($productCount + 1) / $limit))
	{
		$pageNumber  = ceil(($productCount + 1) / $limit);
	}

	$limitLower = ($pageNumber  - 1) * $limit;

	// Breaks the array into smaller chunks for each page depending on $limit
	$results = array_slice($results, $limitLower, $limit, true);

	echo '<div class="totalFound">' . $totalFound . ' results for <b>' . ucwords($celebDecode) . '</b></div>';
	?>

	<form name="priceSorter" method="POST" action="" style="margin:0; float:right; padding:4px 13px 4px 0;">
		<label for="PriceSort" style="padding-right:4px; font-size:14px; float:left;">Sort By: </label>
		<select name="sort" onChange="priceSorter.submit();" style="display:inline;">
			<option> -- Select Option -- </option>
			<option value="">Relevancy</option>
			<option value="desc">Price: High to Low</option>
			<option value="asc">Price: Low to High</option>
		</select>
	</form>

	<div id="productList">
		<?php
		// Loop to return Products and corresponding information
		foreach ($results as $i => $record)
		{
			$record['image_url'] = $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url'])));
			?>
			<div class="<?php echo count($results) >= 2 ? 'productBlock' : 'productBlock0'; ?>">
				<div class="productImage">
					<a href="<?php echo $productPage . '/celebrity/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><img src="<?php echo $record['image_url']; ?>" title="<?php echo $record['keyword']?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
				</div>
				<div class="productContent">
					<div class="productTitle"><a href="<?php echo $productPage . '/celebrity/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId']; ?>"><span><?php echo $record['keyword']?></span></a></div>
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
						<div class="productPrice"><span>$<?php echo $record['price']; ?></span></div>
						<div class="productPriceSale"><span>$<?php echo $record['price_sale']; ?></span></div>
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