<style>
	.noResults {
		font-size:2.4em;
	}

	.noResults-secondary {
		font-size:14px;
	}
</style>
<?php
	error_reporting(0);

	require_once('Prosperent_Api.php');
	$prosperentApi = new Prosperent_Api(array(
		'api_key'        => '7b0a5297441c39be99fda92fc784b516',
		'query'          => $_GET['q'],
		'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
		'limit'          => 1,
		'groupBy'	     => 'productId',
		'filterMerchant' => $_GET['merchant'],
		'filterBrand'	 => $_GET['brand']
	));

	$prosperentApi -> fetch();
	$results = $prosperentApi -> getAllData();

	if ($results)
	{
		foreach ($results as $record)
		{
			$record['image_url'] = preg_replace('/\/images\/250x250\//', '/images/125x125/', $record['image_url'])
			?>
			<div class='productBlock'>
				<div class='productImage'>
					<a href='<?php echo $record['affiliate_url']; ?>'><span><img src='<?php echo $record['image_url']; ?>'  alt='<?php echo $record['keyword']; ?>' title='<?php echo $record['keyword']; ?>'></span></a>
				</div>					
				<div class='productContent'>
					<div class='productTitle'><a href='<?php echo $record['affiliate_url']; ?>'><span><?php echo $record['keyword']; ?></span></a></div>
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
								echo '<span class="brandIn"><u>Brand</u>: <cite>' . $record['brand'] . '</cite></span><br>';
							}
							if($record['merchant'] && !$filterMerchant)
							{
								echo '<span class="merchantIn"><u>Merchant</u>: <cite>' . $record['merchant'] . '</cite></span>';
							}
							?>
						</div>
					</div>
				</div>
			</div>
		<?php
		}
	}
	else
	{
		echo '<div class="noResults">No Results</div>';
		echo '<div class="noResults-secondary">Please try another search.</div>';
	}
						