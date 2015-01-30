<?php
$base = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products';
$url = site_url('/') . $base . '/type/cele';

/*
/  Prosperent API Query
*/
$settings = array(
	'api_key'         => $options['Api_Key'],
	'limit'           => 1,
	'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
	'enableFacets'    => $options['Enable_Facets'],
	'filterCatalogId' => get_query_var('cid')
);

if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
{	
	$settings = array_merge($settings, array(
		'cacheBackend'   => 'FILE',
		'cacheOptions'   => array(
			'cache_dir'  => PROSPER_CACHE
		)
	));	
}

$prosperentApi = new Prosperent_Api($settings);

/*
/  Fetching results and pulling back all data
/  To see which data is available to pull back login in to
/  Prosperent.com and click the API tab
*/
$prosperentApi -> fetch();
$record = $prosperentApi -> getAllData();

if (empty($record))
{
	header('Location: ' . $url . '/query/' . htmlentities(rawurlencode(get_query_var('keyword'))));
    exit;
}

/*
/  Prosperent API Query
*/
$settings = array(
	'api_key'         => $options['Api_Key'],
	'limit'           => 10,
	'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
	'enableFacets'    => $options['Enable_Facets'],
	'filterProductId' => $record[0]['productId'],
	'groupBy'		  => 'productId'
);

if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
{	
	$settings = array_merge($settings, array(
		'cacheBackend'   => 'FILE',
		'cacheOptions'   => array(
			'cache_dir'  => PROSPER_CACHE
		)
	));	
}

$prosperentApi = new Prosperent_Api($settings);

$prosperentApi -> fetch();

$result = $prosperentApi -> getAllData();
?>
<div id="product" itemscope itemtype="http://data-vocabulary.org/Product">
	<div class="productBlock">
		<div class="productTitle"><a href="<?php echo $productPage . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record[0]['affiliate_url'])); ?>" target="<?php echo $target; ?>" rel="nofollow"><span itemprop="name"><?php echo preg_replace('/\(.+\)/i', '', $record[0]['keyword']); ?></span></a></div>
		<div class="productImage">
			<a itemprop="offerURL" href="<?php echo $productPage . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record[0]['affiliate_url'])); ?>" target="<?php echo $target; ?>" rel="nofollow"><img itemprop="image" src="<?php echo ($options['Image_Masking'] ? $productPage  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record[0]['image_url'])) : $record[0]['image_url']); ?>" title="<?php echo $record[0]['keyword']; ?>" alt="<?php echo $record[0]['keyword']; ?>" /></a>
		</div>
		<div class="productContent">
			<div class="productDescription" itemprop="description"><?php
				if (strlen($record[0]['description']) > 185)
				{
                    echo substr($record[0]['description'], 0, 185);
					?>
					<span id="moreDesc" style="display:inline-block;"> ... <a style="cursor:pointer;" onclick="showFullDesc('fullDesc'); hideMoreDesc('moreDesc');">more</a></span><p id="fullDesc" style="display:none; -moz-hyphens: manual;"><?php echo substr($record[0]['description'], 185); ?></p>
					<?php
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
							echo '<a href="' . $url . '/query/' . rawurlencode($category) . '/celeb/' . rawurlencode($record[0]['celebrity'][0]) . '"><cite itemprop="category">' . $category . '</cite></a>';
						} 
						else 
						{
							echo '<a href="' . $url . '/query/' . rawurlencode($category) . '+' . rawurlencode($query) . '/celeb/' . rawurlencode($record[0]['celebrity'][0]) . '"><cite itemprop="category">' . $category . '</cite></a>';
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
					echo '<span class="prodBrand"><u>Brand</u>: <a href="' . $url . '/brand/' . rawurlencode($result[0]['brand']) . '/celeb/' . rawurlencode($record[0]['celebrity'][0]) . '"><cite itemprop="brand">' . $record[0]['brand'] . '</cite></a></span><br>';
				}
				echo '<p><span class="prodPrice">As low as <strong>' . '$' . ($result[0]['minPriceSale'] ? $result[0]['minPriceSale'] : $result[0]['minPrice']) . '</strong> at <strong>' . $result[0]['groupCount'] . ($result[0]['groupCount'] > 1 ? ' stores' : ' store') . '</strong></span></p>';
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
					$settings = array(
						'api_key'         => $options['Api_Key'],
						'limit'           => 10,
						'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
						'enableFacets'    => $options['Enable_Facets'],
						'filterProductId' => $record[0]['productId'],
						'enableFullData'  => 0
					);			
					
					if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
					{	
						$settings = array_merge($settings, array(
							'cacheBackend'   => 'FILE',
							'cacheOptions'   => array(
								'cache_dir'  => PROSPER_CACHE
							)
						));	
					}

					$prosperentApi = new Prosperent_Api($settings);

					$prosperentApi -> fetch();
					$result = $prosperentApi -> getAllData();				
				}
				
				foreach ($record as $product)
				{
					echo '<tr>';
					echo '<td itemprop="seller">' . $product['merchant'] . '</td>';
					echo '<td itemprop="price">' . '$' . ($product['priceSale'] ? $product['priceSale'] : $product['price']) . '</td>';
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
$settings = array(
	'api_key'      	 => $options['Api_Key'],
	'limit'        	 => $options['Same_Limit'],
	'visitor_ip'   	 => $_SERVER['REMOTE_ADDR'],
	'query'		   	 => $record[0]['keyword'],
	'groupBy'	   	 => 'productId',
	'enableFullData' => 0
);

if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
{	
	$settings = array_merge($settings, array(
		'cacheBackend'   => 'FILE',
		'cacheOptions'   => array(
			'cache_dir'  => PROSPER_CACHE
		)
	));	
}

$prosperentApi = new Prosperent_Api($settings);

$prosperentApi -> fetch();
$similar = $prosperentApi -> getAllData();

if ($similar)
{
	echo '<div class="clear"></div>';
	echo '<div class="simTitle">Similar Products</div>';
	echo '<div id="simProd">';
	echo '<ul>';

	foreach ($similar as $prod)
	{
		$price = $prod['price_sale'] ? $prod['price_sale'] : $prod['price'];
		$prod['image_url'] = $options['Image_Masking'] ? $productPage  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $prod['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $prod['image_url']);
		?>
			<li>
			<div class="listBlock">
				<div class="prodImage">
					<a href="<?php echo $productPage . '/celebrity/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $prod['catalogId']; ?>"><img src="<?php echo $prod['image_url']; ?>" title="<?php echo $prod['keyword']; ?>" alt="<?php echo $prod['keyword']; ?>" /></a>
				</div>
				<div class="prodContent">
					<div class="prodTitle" style="text-align:center;font-size:12px;">
						<a href="<?php echo $productPage . '/celebrity/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $prod['catalogId']; ?>" >
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
}
echo '<div class="clear"></div>';

/*
/  Prosperent API Query
*/
$settings = array(
	'api_key'     	 => $options['Api_Key'],
	'limit'        	 => $options['Same_Limit'],
	'visitor_ip'   	 => $_SERVER['REMOTE_ADDR'],
	'enableFacets' 	 => $options['Enable_Facets'],
	'filterBrand'  	 => $record[0]['brand'],
	'groupBy'	   	 => 'productId',
	'enableFullData' => 0
);

if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
{	
	$settings = array_merge($settings, array(
		'cacheBackend'   => 'FILE',
		'cacheOptions'   => array(
			'cache_dir'  => PROSPER_CACHE
		)
	));	
}

$prosperentApi = new Prosperent_Api($settings);

$prosperentApi -> fetchProducts();
$currency = 'USD';

$sameBrand = $prosperentApi -> getAllData();

if ($sameBrand)
{
	echo '<div class="clear"></div>';
	echo '<div class="simTitle">Other Products from ' . $record[0]['brand'] . '</div>';
	echo '<div id="simProd">';
	echo '<ul>';
	foreach ($sameBrand as $brandProd)
	{
		$price = $brandProd['price_sale'] ? $brandProd['price_sale'] : $brandProd['price'];
		$brandProd['image_url'] = $options['Image_Masking'] ? $productPage  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $brandProd['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $brandProd['image_url']);
		?>
			<li>
			<div class="listBlock">
				<div class="prodImage">
					<a href="<?php echo $productPage . '/celebrity/' . rawurlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $brandProd['catalogId']; ?>"><img src="<?php echo $brandProd['image_url']; ?>" title="<?php echo $brandProd['keyword']; ?>" alt="<?php echo $brandProd['keyword']; ?>" /></a>
				</div>
				<div class="prodContent">
					<div class="prodTitle" style="text-align:center;font-size:12px;">
						<a href="<?php echo $productPage . '/celebrity/' . rawurlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $brandProd['catalogId']; ?>" >
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
						</a>
					</div>
					<div class="prodPrice"><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $price; ?></div>
				</div>
				<div class="clear"></div>
			</div>
			</li>
		<?php
	}	
	echo '</ul>';
	echo '</div>';
}