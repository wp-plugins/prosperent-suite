<?php
$url = 'http://' . $_SERVER['HTTP_HOST'] . '/products/type/' . $type;

switch ($options['Country'])
{
	case 'UK':
		$currency = 'GBP';
		break;
	case 'CA':
		$currency = 'CAD';
		break;
	default:
		$currency = 'USD';
		break;
}

/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
	'api_key'         => $options['Api_Key'],
	'limit'           => 1,
	'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
	'enableFacets'    => $options['Enable_Facets'],
	'filterCatalogId' => get_query_var('cid')
));

/*
/  Fetching results and pulling back all data
/  To see which data is available to pull back login in to
/  Prosperent.com and click the API tab
*/
switch ($options['Country'])
{
	case 'UK':
		$prosperentApi -> fetchUkProducts();
		$currency = 'GBP';
		break;
	case 'CA':
		$prosperentApi -> fetchCaProducts();
		$currency = 'CAD';
		break;
	default:
		$prosperentApi -> fetchProducts();
		$currency = 'USD';
		break;
}
$record = $prosperentApi -> getAllData();

/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
	'api_key'         => $options['Api_Key'],
	'limit'           => 10,
	'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
	'enableFacets'    => $options['Enable_Facets'],
	'filterProductId' => $record[0]['productId'],
	'groupBy'		  => 'productId'
));

switch ($options['Country'])
{
	case 'UK':
		$prosperentApi -> fetchUkProducts();
		$currency = 'GBP';
		break;
	case 'CA':
		$prosperentApi -> fetchCaProducts();
		$currency = 'CAD';
		break;
	default:
		$prosperentApi -> fetchProducts();
		$currency = 'USD';
		break;
}
$result = $prosperentApi -> getAllData();
?>
<div id="product" itemscope itemtype="http://data-vocabulary.org/Product">
	<div class="productBlock">
		<div class="productTitle"><a href="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record[0]['affiliate_url'])); ?>" target="<?php echo $target; ?>"><span itemprop="name"><?php echo preg_replace('/\(.+\)/i', '', $record[0]['keyword']); ?></span></a></div>
		<div class="productImage">
			<a itemprop="offerURL" href="<?php echo $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record[0]['affiliate_url'])); ?>" target="<?php echo $target; ?>"><img itemprop="image" src="<?php echo $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record[0]['image_url'])); ?>" title="<?php echo $record[0]['keyword']; ?>" ></a>
		</div>
		<div class="productContent">
			<div class="productDescription" itemprop="description"><?php
				if (strlen($record[0]['description']) > 200)
				{
					echo substr($record[0]['description'], 0, 200) . '...';
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
							echo '<a href="' . $url . '/query/' . urlencode($category) . '"><cite itemprop="category">' . $category . '</cite></a>';
						} 
						else 
						{
							echo '<a href="' . $url . '/query/' . urlencode($category) . '+' . urlencode($query) . '"><cite itemprop="category">' . $category . '</cite></a>';
						}						
						
						if ($i < ($catCount - 1))
						{
							echo ' > ';
						}
					}					
					echo '</span><br>';
				}
				if($record[0]['upc'])
				{
					echo '<span itemprop="identifier" content="upc:' . $record[0]['upc'] . '" class="prodBrand"><u>UPC</u>: ' . $record[0]['upc'] . '</span><br>';
				}
				if($record[0]['brand'])
				{
					echo '<span class="prodBrand"><u>Brand</u>: <a href="' . $url . '/brand/' . urlencode($result[0]['brand']) . '"><cite itemprop="brand">' . $record[0]['brand'] . '</cite></a></span><br>';
				}
				echo '<p><span class="prodPrice">As low as <strong>' . ($currency == 'GBP' ? '&pound;' : '$') . ($result[0]['minPriceSale'] ? $result[0]['minPriceSale'] : $result[0]['minPrice']) . '</strong> at <strong>' . $result[0]['groupCount'] . ($result[0]['groupCount'] > 1 ? ' stores' : ' store') . '</strong></span></p>';
				?>
			</div>
		</div>
		<div class="clear"></div>
		<div class="allResults">
			<table class="productResults" itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">
				<thead>
					<tr>
						<th><strong>Store</strong></th>
						<th><strong>Price</strong></th>
						<th></th>
					</tr>
				</thead>
				<?php
				if ($result[0]['groupCount'] > 1)
				{
					require_once(PROSPER_PATH . 'Prosperent_Api.php');
					$prosperentApi = new Prosperent_Api(array(
						'api_key'         => $options['Api_Key'],
						'limit'           => 10,
						'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
						'enableFacets'    => $options['Enable_Facets'],
						'filterProductId' => $record[0]['productId']
					));
					
					switch ($options['Country'])
					{
						case 'UK':
							$prosperentApi -> fetchUkProducts();
							$currency = 'GBP';
							break;
						case 'CA':
							$prosperentApi -> fetchCaProducts();
							$currency = 'CAD';
							break;
						default:
							$prosperentApi -> fetchProducts();
							$currency = 'USD';
							break;
					}
					$result = $prosperentApi -> getAllData();				
				}

				foreach ($result as $product)
				{
					echo '<tr>';
					echo '<td itemprop="seller">' . $product['merchant'] . '</td>';
					echo '<td itemprop="price">' . ($currency == 'GBP' ? '&pound;' : '$') . ($product['priceSale'] ? $product['priceSale'] : $product['price']) . '</td>';
					echo '<meta itemprop="priceCurrency" content="' . $currency . '"/>';
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
	'api_key'      => $options['Api_Key'],
	'limit'        => 8,
	'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
	'query'		   => $record[0]['keyword'],
	'groupBy'	   => 'productId'
));

switch ($options['Country'])
{
	case 'UK':
		$prosperentApi -> fetchUkProducts();
		$currency = 'GBP';
		break;
	case 'CA':
		$prosperentApi -> fetchCaProducts();
		$currency = 'CAD';
		break;
	default:
		$prosperentApi -> fetchProducts();
		$currency = 'USD';
		break;
}
$similar = $prosperentApi -> getAllData();
echo '<div class="clear"></div>';
echo '<div class="simTitle">Similar Products</div>';
echo '<div id="simProd">';
echo '<ul>';

foreach ($similar as $prod)
{
	$price = $prod['price_sale'] ? $prod['price_sale'] : $prod['price'];
	$prod['image_url'] = $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $prod['image_url'])));
	?>
		<li>
		<div class="listBlock">
			<div class="prodImage">
				<a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $prod['catalogId']; ?>"><span><img src="<?php echo $prod['image_url']; ?>" title="<?php echo $prod['keyword']; ?>" style="background: none repeat scroll 0 0 transparent; border: medium none;"></span></a>
			</div>
			<div class="prodContent">
				<div class="prodTitle" style="text-align:center;font-size:12px;">
					<a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $prod['catalogId']; ?>" >
						<span>
							<?php			
							if (strlen($prod['keyword']) > 60)
							{
								echo substr($prod['keyword'], 0, 60) . '...';
							}
							else
							{
								echo $prod['keyword']; 
							}
							?>
						</span>
					</a>
				</div>
				<div class="prodPrice"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $price; ?></span></div>
			</div>
			<div class="clear"></div>
		</div>
		</li>
	<?php
}	
echo '</ul>';
echo '</div>';
echo '<div class="clear"></div>';
/*
/  Prosperent API Query
*/
require_once(PROSPER_PATH . 'Prosperent_Api.php');
$prosperentApi = new Prosperent_Api(array(
	'api_key'      => $options['Api_Key'],
	'limit'        => 8,
	'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
	'enableFacets' => $options['Enable_Facets'],
	'filterBrand'  => $record[0]['brand'],
	'groupBy'	   => 'productId'
));

switch ($options['Country'])
{
	case 'UK':
		$prosperentApi -> fetchUkProducts();
		$currency = 'GBP';
		break;
	case 'CA':
		$prosperentApi -> fetchCaProducts();
		$currency = 'CAD';
		break;
	default:
		$prosperentApi -> fetchProducts();
		$currency = 'USD';
		break;
}

$sameBrand = $prosperentApi -> getAllData();

echo '<div class="simTitle">Other Products from ' . $record[0]['brand'] . '</div>';
echo '<div id="simProd">';
echo '<ul>';
foreach ($sameBrand as $brandProd)
{
	$price = $brandProd['price_sale'] ? $brandProd['price_sale'] : $brandProd['price'];
	$brandProd['image_url'] = $productPage  . '/img/'. urlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $brandProd['image_url'])));
	?>
		<li>
		<div class="listBlock">
			<div class="prodImage">
				<a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $brandProd['catalogId']; ?>"><span><img src="<?php echo $brandProd['image_url']; ?>" title="<?php echo $brandProd['keyword']; ?>"></span></a>
			</div>
			<div class="prodContent">
				<div class="prodTitle" style="text-align:center;font-size:12px;">
					<a href="<?php echo $productPage . '/product/' . urlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $brandProd['catalogId']; ?>" >
						<span>
							<?php			
							if (strlen($brandProd['keyword']) > 60)
							{
								echo substr($brandProd['keyword'], 0, 60) . '...';
							}
							else
							{
								echo $brandProd['keyword']; 
							}
							?>
						</span>
					</a>
				</div>
				<div class="prodPrice"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $price; ?></span></div>
			</div>
			<div class="clear"></div>
		</div>
		</li>
	<?php
}	
echo '</ul>';
echo '</div>';