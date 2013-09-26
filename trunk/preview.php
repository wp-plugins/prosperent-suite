<style>
    .noResults {
        font-size:2.4em;
    }

    .noResults-secondary {
        font-size:14px;
    }
	.couponBlock {
		padding-bottom:6px;
	}
	.productBlock {
		padding-bottom:6px;
	}
</style>
<?php
    error_reporting(0);     

	$merchant = explode(',', trim($_GET['merchant']));
	$brand = explode(',', trim($_GET['brand']));
	
	require_once('Prosperent_Api.php');
    $settings = array(
        'api_key'        => '7b0a5297441c39be99fda92fc784b516',
        'query'          => $_GET['q'],
        'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
        'limit'          => 100,
        'sortPrice'		 => '',
        'filterMerchant' => $merchant
    );
	
	if (file_exists('prosperent_cache') && substr(decoct(fileperms('prosperent_cache') ), 1) == '0777')
	{	
		$settings = array_merge($settings, array(
			'cacheBackend'   => 'FILE',
			'cacheOptions'   => array(
				'cache_dir'  => 'prosperent_cache'
			)
		));	
	}

	$prosperentApi = new Prosperent_Api($settings);

    if (!$_GET['coup'])
    {
        $prosperentApi -> set_filterBrand($brand);

		if ($_GET['merchant'] && $_GET['prodid'])
		{
			$prosperentApi -> set_filterCatalogId($_GET['prodid']);
		}
		elseif ($_GET['prodid'])
		{
			$prosperentApi -> set_filterProductId($_GET['prodid']);
		}
		
		switch ($_GET['country'])
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
		$results = $prosperentApi -> getAllData();		
    }
    else
    {
		$key = array_search('Zappos.com', $merchant);
		if ($key || 0 === $key)
		{
			unset($merchant[$key]);
		}

		$key2 = array_search('6pm', $merchant);
		if ($key2 || 0 === $key2)
		{
			unset($merchant[$key2]);
		}

		$merchants = array_merge($merchant, array('!Zappos.com', '!6pm'));
		
		$prosperentApi -> set_filterMerchant($merchants);
		$prosperentApi -> set_filterCouponId($_GET['prodid']);

        $prosperentApi -> fetchCoupons();
        $coupons = $prosperentApi -> getAllData();
    }

    if ($results)
    {
	    ?>
        <div id="productList">
        <?php
			foreach ($results as $record)
			{				
				$record['image_url'] = preg_replace('/\/images\/250x250\//', '/images/125x125/', $record['image_url'])
				?>
				<div id="<?php echo $_GET['merchant'] ? $record['catalogId'] : $record['productId']; ?>" onClick="getIdofItem(this);">
					<div class="productBlock">
						<div class='productImage'>
							<span><img src='<?php echo $record['image_url']; ?>'  alt='<?php echo $record['keyword']; ?>' title='<?php echo $record['keyword']; ?>'/></span></a>
						</div>
						<div class='productContent'>
							<div class='productTitle'><span><?php echo $record['keyword']; ?></span></a></div>
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
				</div>
			<?php
			}
		?>
		</div>
		<?php
    }
    else if ($coupons)
    {
        ?>
       <div id="couponList">
            <?php
            // Loop to return coupons and corresponding information
            foreach ($coupons as $record)
            {
                ?>
				<div id="<?php echo $record['couponId']; ?>" onClick="getIdofItem(this);">
					<div class="couponBlock">
						<div class="couponImage">
							<?php
							echo '<img src="' . $record['image_url'] . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/>';
							?>
						</div>
						<div class="couponContent">
							<div class="couponTitle">
								<?php
								echo $record['keyword'];
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
