<?php
$url = 'http://' . $_SERVER['HTTP_HOST'] . '/products/type/coup';

/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
	'api_key'         => $options['Api_Key'],
	'limit'           => 1,
	'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
	'enableFacets'    => $options['Enable_Facets'],
	'filterCouponId' => get_query_var('cid')
));

/*
/  Fetching results and pulling back all data
/  To see which data is available to pull back login in to
/  Prosperent.com and click the API tab
*/
$prosperentApi -> fetchCoupons();
$record = $prosperentApi -> getAllData();

$expires = strtotime($record[0]['expiration_date']);
$today = strtotime(date("Y-m-d"));
$interval = abs($expires - $today) / (60*60*24);
?>

<div id="coupon" itemscope itemtype="http://data-vocabulary.org/Product">
	<div class="productBlock">
		<div class="productTitle"><a href="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record[0]['affiliate_url'])); ?>" target="<?php echo $target; ?>"><span itemprop="name"><?php echo preg_replace('/\(.+\)/i', '', $record[0]['keyword']); ?></span></a></div>
		<div class="productImage">
			<a itemprop="offerURL" href="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record[0]['affiliate_url'])); ?>" target="<?php echo $target; ?>"><img itemprop="image" src="<?php echo $record[0]['image_url']; ?>"  alt="<?php echo $record[0]['keyword']; ?>" title="<?php echo $record[0]['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></a>
		</div>
		<div class="productContent">
			<div class="productDescription" itemprop="description"><?php
				if (strlen($record[0]['description']) > 240)
				{
					echo substr($record[0]['description'], 0, 240) . '...';
				}
				else
				{
					echo $record[0]['description'];
				}
				?>
			</div>
			<div class="productBrandMerchant">
				<?php
				if($record[0]['category'])
				{				
					$categoryList = preg_split('/(:|>|<|;|\.|-)/i', $record[0]['category']);
					$catCount = count($categoryList);
					
					echo '<span><u>Category</u>: '; 
					foreach ($categoryList as $i => $category)
					{
						$category = trim($category);					
						if (preg_match('/' . $query . '/i', $category)) 
						{
							echo '<a href="' . $url . '/query/' . urlencode($category) . '" target="' . $target . '"><cite itemprop="category">' . $category . '</cite></a>';
						} 
						else 
						{
							echo '<a href="' . $url . '/query/' . urlencode($category) . '+' . urlencode($query) . '" target="' . $target . '"><cite itemprop="category">' . $category . '</cite></a>';
						}						
						
						if ($i < ($catCount - 1))
						{
							echo ' > ';
						}
					}					
					echo '</span><br>';
				}
				if($record[0]['merchant'])
				{
					echo '<span class="prodBrand"><u>Merchant</u>: <a href="' . $url . '/merchant/' . urlencode($record[0]['merchant']) . '" target="' . $target . '"><cite itemprop="seller">' . $record[0]['merchant'] . '</cite></a></span><br>';
				}
				if($record[0]['coupon_code'])
				{
					echo '<span class="prodBrand"><u>Coupon Code</u>: <strong>' . $record[0]['coupon_code'] . '</strong></span><br>';
				}
				if ($record[0]['expiration_date'] && $interval <= 120)
				{
					echo '<meta itemprop="priceValidUntil" content="' . $record[0]['expiration_date'] . '"/>';
					echo '<p><span class="prodPrice"><u>Expires</u>: <strong>' . $interval . ' days left!</strong></span></p>';
				}
				?>
			</div>
		</div>
		<div class="clear"></div>
		<div class="allResults">
			<table class="productResults">
				<thead>
					<tr>
						<th><strong>Store</strong></th>
						<th><strong>Coupon Code</strong></th>
						<th><strong>Expires</strong></th>
						<th></th>
					</tr>
				</thead>
				<?php			
				foreach ($record as $product)
				{
					$expires = strtotime($product['expirationDate']);
					$today = strtotime(date("Y-m-d"));
					$interval = abs($expires - $today) / (60*60*24);
				
					echo '<tr>';
					echo '<td>' . $product['merchant'] . '</td>';
					echo '<td>' . ($product['coupon_code'] ? $product['coupon_code'] : '---') . '</td>';
					if ($interval <= 7 && $interval > 0)
					{
						echo '<td>' . $interval . ' days</td>';
					}
					else
					{
						echo '<td>---</td>';
					}					
					echo '<td><form style="margin:0; margin-bottom:5px;" action="' . $product['affiliate_url'] . '" target="' . $target. '" method="POST"><input type="submit" value="Visit Store"/></form></td>';
					echo '</tr>';
				}
				?>
			</table>		
		</div>
	</div>
</div>

<?php
/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
	'api_key'        => $options['Api_Key'],
	'limit'          => 8,
	'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
	'enableFacets'   => $options['Enable_Facets'],
	'filterMerchant' => $record[0]['merchant'],
));

$prosperentApi -> fetchCoupons();
$sameMerch = $prosperentApi -> getAllData();

echo '<div class="clear"></div>';
echo '<div class="simTitle">Other Coupons from ' . $record[0]['merchant'] . '</div>';
echo '<div id="simCoup">';
echo '<ul>';
foreach ($sameMerch as $merchCoup)
{
	$expires = strtotime($merchCoup['expiration_date']);
	$today = strtotime(date("Y-m-d"));
	$interval = abs($expires - $today) / (60*60*24);
	
	?>
		<li>
		<div class="listBlock">
			<div class="prodContent">
				<div class="prodImage">
					<a href="<?php echo $productPage . '/coupon/' . urlencode(str_replace('/', ',SL,', $merchCoup['keyword'])) . '/cid/' . $merchCoup['couponId']; ?>"><span><img src="<?php echo $merchCoup['image_url']; ?>"  alt="<?php echo $merchCoup['keyword']; ?>" title="<?php echo $merchCoup['keyword']; ?>"></span></a>
				</div>
				<div class="prodTitle">
					<a href="<?php echo $productPage . '/coupon/' . urlencode(str_replace('/', ',SL,', $merchCoup['keyword'])) . '/cid/' . $merchCoup['couponId']; ?>" >
						<span>
							<?php			
							if (strlen($merchCoup['keyword']) > 60)
							{
								echo substr($merchCoup['keyword'], 0, 60) . '...';
							}
							else
							{
								echo $merchCoup['keyword']; 
							}
							?>
						</span>
					</a>
				</div>
				<?php
				if ($interval <= 7 && $interval > 0)
				{
					echo '<div class="prodPrice"><span> ' . $interval . ' days left!</span></div>';
				}
				?>
			</div>
			<div class="clear"></div>
		</div>
		</li>

	<?php
}	
echo '</ul>';
echo '</div>';