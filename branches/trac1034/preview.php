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
		'sortPrice'		 => '',
		'filterMerchant' => $_GET['merchant']
	));

	if (!$_GET['coup'])
	{
		$prosperentApi -> set_filterBrand($_GET['brand']);
		$prosperentApi -> fetch();
		$results = $prosperentApi -> getAllData();
	}
	else
	{
		$prosperentApi -> fetchCoupons();
		$coupons = $prosperentApi -> getAllData();
	}

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
	else if ($coupons)
	{
		?>
	   <div id="couponList">
			<?php
			// Loop to return coupons and corresponding information
			foreach ($coupons as $i => $record)
			{
				?>
				<div class="couponBlock">
					<div class="couponImage">
						<?php
						echo '<a href="' . $record['affiliate_url'] . '"><img src="' . $record['image_url'] . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"></a>';
						?>
					</div>
					<div class="couponContent">
						<div class="couponTitle">
							<?php
							echo '<a href="' . $record['affiliate_url'] . '">' . $record['keyword'] . '</a>';
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
								echo '<div class="couponExpire" style=""><span style="color:red; font-weight:bold;">Expires in ' . $interval . ' days!</span></div>';
							}
							else
							{
								echo '<div class="couponExpire"><span style="color:red; font-weight:bold;">Expires Soon!</span></div>';
							}
						}
						?>
						<div class="couponDescription">
							<?php 
							if (strlen($record['description']) > 200)
							{ 
								echo substr($record['description'], 0, 175) . '...';
							}
							else
							{
								echo $record['description'];
							}
							?>
						</div>
						<?php
						if ($record['coupon_code'])
						{
							echo '<div class="couponCode" style="font-weight:bold; padding:5px 0 0 0;">Coupon Code: <span class="code_cc" style="border:2px dashed #3079ed; padding: 2px 3px; font-weight:bold; -moz-border-radius: 8px; -webkit-border-radius: 8px; border-radius:8px;">' . $record['coupon_code'] . '</span></div>';
						}
						?>
					</div>
					<div class="couponVisit">
						<a href="<?php echo $record['affiliate_url']; ?>"><img style="background: none repeat scroll 0 0 transparent; border: medium none; box-shadow: none;" src="<?php echo plugins_url('/img/visit_store_button.png', __FILE__);?> "></a>
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
						