<?php
if (!$query && $options['Coupon_Query'])
{
	$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/query/' . urlencode($options['Coupon_Query']);
	$q = $options['Coupon_Query'];
}
else
{
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $q = $query;
}

$query = stripslashes($q);

$coupSubmit = preg_replace('/\/$/', '', $url);
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
	'api_key'        => $options['Api_Key'],
	'query'          => $query,
	'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
	'limit'          => $options['Api_Limit'],
	'enableFacets'   => $options['Enable_Facets'],
	'filterMerchant' => !$negativeMerchants ? $filterMerchant : $negativeMerchants
));

/*
/  Fetching results and pulling back all data
/  To see which data is available to pull back login in to
/  Prosperent.com and click the API tab
*/
$prosperentApi -> fetchCoupons();
$results = $prosperentApi -> getAllData();
$facets = $prosperentApi -> getFacets();
$totalFound = $prosperentApi -> getTotalRecordsFound();

echo $typeSelector;
?>

<div style="float:right;">
	<form id="searchform" method="POST" action="" style="margin:0;">
		<input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Coupons' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 6px;">
		<input class="submit" type="submit" value="Search" style="padding:5px;">
	</form>
</div>

<?php
if ($prosperentApi->get_enableFacets() == 1)
{
	$merchants = $facets['merchant'];

	if ($merchants)
	{
		$merchants1 = array_slice($merchants, 0, !$options['Merchant_Facets'] ? 10 : $options['Merchant_Facets'], true);
	}
	?>
	<table id="facets">
		<tr>
			<td class="merchants" style="width:98%; float:none;">
				<?php
				echo (empty($filterMerchant) ? '<div class="browseMerchants">Browse by Merchant: </div>' : '<div class="filteredMerchants">Filtered by Merchant: </div>');

				if (empty($facets['merchant']) && !$filterMerchant)
				{
					echo '<div class="noMerchants"">No Merchants Found</div>';
				}
				else if (!$filterMerchant && !empty($results))
				{
					$count = count($merchants1);
					foreach ($merchants1 as $i => $merchant)
					{
						echo '<a href=' . $coupSubmit . '/merchant/' . urlencode($merchant['value']) . '>' . $merchant['value'] . ' (' . $merchant['count'] . ')</a>';

						if ($i < ($count - 1))
						{
							echo ', ';
						}
					}
				}
				else
				{
					echo '<div style="min-height:35px;">';
					echo urldecode($filterMerchant);
					echo '</br><a href=' . str_replace('/merchant/' . $filterMerchant, '', $coupSubmit) . '>clear filter</a>';
					echo '<div style="margin-top:-50px;padding-left:150px;"><img src="' . $productPage  . '/img/' . urlencode(str_replace('/', ',SL,',  ('logos/120x60/' . $filterMerchant . '.png'))) . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
					echo '</div>';
				}
				?>
			</td>
		</tr>
	</table>
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
		<form id="searchform" method="POST" action="" style="margin:0;">
			<input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>" style="padding:4px 4px 6px;">
			<input class="submit" type="submit" value="Search" style="padding:5px;">
		</form>
	</div>
	<?php
	if ($filterMerchant)
	{
		echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace('/merchant/' . $filterMerchant, '', $coupSubmit) . '>clear the filter(s)</a></div>';
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
		'enableFacets'  => 'keyword',
		'filterCatalog' => 'coupons'
	));

	$api->setDateRange('commission', $startRange, $endRange)
		->fetchTrends();
		
	// set productId as key in array
	foreach ($api->getFacets('keyword') as $data)
	{
		$keys[] = $data['value'];
	}

	// fetch merchant data from api
	$api = new Prosperent_Api(array(
		'api_key'         => $options['Api_Key'],
		'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
		'filterKeyword'   => $keys,
		'limit' 	      => 15
	));

	$api->fetchCoupons();
	$results = $api->getAllData() ;		
	
	echo '<div class="totalFound">Browse these <strong>trending coupons</strong></div>';
	?>
	<form name="priceSorter" method="POST" action="" style="margin:0; float:right; padding:4px 13px 4px 0;">
		<label for="PriceSort" style="padding-right:4px; font-size:14px; float:left;">Sort By: </label>
		<select name="sort" onChange="priceSorter.submit();" style="display:inline;">
			<option> -- Select Option -- </option>
			<option value="rel">Relevancy</option>
			<option value="expiration_date+desc">Expiration Date: Descending</option>
			<option value="expiration_date+asc">Expiration Date: Ascending</option>		
		</select>
	</form>
	</br>
	<div id="couponList">
		<?php
		// Loop to return coupons and corresponding information
		foreach ($results as $i => $record)
		{
			?>
			<div class="<?php echo count($results) >= 2 ? 'couponBlock' : 'couponBlock0'; ?>">
				<div class="couponImage">
					<?php
					echo '<a href="' . $productPage . '/coupon/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['couponId'] . '"><img src="' . $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record['image_url'])) . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"></a>';
					?>
					<div class="couponVisit">
						<form style="margin:0; text-align:center;" method="POST" action="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('/', 'http://prosperent.com/store/product/'), array(',SL,', ''), $record['affiliate_url'])) . '" target="' . $target; ?>">
							<input type="submit" value="Visit Store"/>
						</form>
					</div>
				</div>
				<div class="couponContent">
					<div class="couponTitle">
						<?php
						echo '<a href="' . $productPage . '/coupon/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['couponId'] . '">' . $record['keyword'] . '</a>';
						?>
					</div>
					<?php
					if(!empty($record['expiration_date']))
					{
						$expires = strtotime($record['expiration_date']);
						$today = strtotime(date("Y-m-d"));
						$interval = abs($expires - $today) / (60*60*24);

						if ($interval <= 7 && $interval > 0)
						{
							echo '<div class="couponExpire"><span>Expires in ' . $interval . ' days!</span></div>';
						}
						else
						{
							echo '<div class="couponExpire"><span>Expires Soon!</span></div>';
						}
					}
					?>
					<div class="couponDescription">
						<?php
						echo $record['description'];
						?>
					</div>
					<?php
					if ($record['coupon_code'])
					{
						echo '<div class="couponCode">Coupon Code: <span class="code_cc">' . $record['coupon_code'] . '</span></div>';
					}
					?>
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
	echo '<div class="totalFound">' . $totalFound . ' coupons for <b>' . strtolower(urldecode($query)) . '</b></div>';

	// Gets the count of results for Pagination
	$productCount = count($results);

	// Pagination limit, can be changed
	$limit = !$options['Pagination_Limit'] ? 15 : $options['Pagination_Limit'];
	$ceiling = ceil(($productCount + 1) / $limit);

	$pages = round($productCount / $limit, 0);

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
	<form name="priceSorter" method="POST" action="" style="margin:0; float:right; padding:4px 13px 4px 0;">
		<label for="PriceSort" style="padding-right:4px; font-size:14px; float:left;">Sort By: </label>
		<select name="sort" onChange="priceSorter.submit();" style="display:inline;">
			<option> -- Select Option -- </option>
			<option value="rel">Relevancy</option>
			<option value="expiration_date+desc">Expiration Date: Descending</option>
			<option value="expiration_date+asc">Expiration Date: Ascending</option>		
		</select>
	</form>
	</br>
	<div id="couponList">
		<?php
		// Loop to return coupons and corresponding information
		foreach ($results as $i => $record)
		{
			?>
			<div class="<?php echo count($results) >= 2 ? 'couponBlock' : 'couponBlock0'; ?>">
				<div class="couponImage">
					<?php
					echo '<a href="' . $productPage . '/coupon/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['couponId'] . '"><img src="' . $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record['image_url'])) . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"></a>';
					?>
					<div class="couponVisit">
						<form style="margin:0; text-align:center;" method="POST" action="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('/', 'http://prosperent.com/store/product/'), array(',SL,', ''), $record['affiliate_url'])) . '" target="' . $target; ?>">
							<input type="submit" value="Visit Store"/>
						</form>
					</div>
				</div>
				<div class="couponContent">
					<div class="couponTitle">
						<?php
						echo '<a href="' . $productPage . '/coupon/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['couponId'] . '">' . $record['keyword'] . '</a>';
						?>
					</div>
					<?php
					if(!empty($record['expiration_date']))
					{
						$expires = strtotime($record['expiration_date']);
						$today = strtotime(date("Y-m-d"));
						$interval = abs($expires - $today) / (60*60*24);

						if ($interval <= 7 && $interval > 0)
						{
							echo '<div class="couponExpire"><span>Expires in ' . $interval . ' days!</span></div>';
						}
						else
						{
							echo '<div class="couponExpire"><span>Expires Soon!</span></div>';
						}
					}
					?>
					<div class="couponDescription">
						<?php
						echo $record['description'];
						?>
					</div>
					<?php
					if ($record['coupon_code'])
					{
						echo '<div class="couponCode">Coupon Code: <span class="code_cc">' . $record['coupon_code'] . '</span></div>';
					}
					?>
				</div>

			</div>
			<?php
		}
		?>
	</div>
	<?php
}

prosper_pagination($pages, $pages, $sendParams['page']);