<?php
class ProductInsertWidget extends WP_Widget
{
    public function __construct()
    {
	    parent::__construct(
			'prosperproductinsert', 
			'ProsperInsert', 
			array('classname' => 'productInsert_widget', 'description' => __( "Display products using a query, merchant or brand."))
		);
    }	

    public function widget( $args, $instance )
    {	
		$allOptions = array('prosperSuite', 'prosper_productSearch', 'prosper_advanced');
		$options = array();
		foreach ($allOptions as $opt)
		{ 
			$options = array_merge($options, (array) get_option($opt));
		}

        extract($args);			
        $title = apply_filters( 'widget_title', (empty( $instance['title'] ) ? 'Related Products' : $instance['title']), $instance, $this->id_base );

		if($instance['useTitle'])
		{ 
			$instance[$instance['useTitle']] = strtolower(get_the_title());
		}			

		require_once(PROSPER_MODEL . '/Search.php');
		$modelSearch = new Model_Search();
		$modelSearch->getFetchEndpoints();
					
		if ($instace['coupons'])
		{
			$expiration = PROSPER_CACHE_COUPS;
			$fetch = 'fetchCoupons';
		}
		else
		{
			$expiration = PROSPER_CACHE_PRODS;
			if ($options['Country'] === 'US')
			{
				$fetch = 'fetchProducts';
			}
			elseif($options['Country'] === 'UK')
			{
				$fetch = 'fetchUkProducts';
			}
			else
			{
				$fetch = 'fetchCaProducts';
			}
		}
	
		if (($instance['imageSize'] > 125 || !$instance['imageSize']))
		{
			$imageSize = '250x250';
		}
		else
		{
			$imageSize = '125x125';
		}
	
		$settings = array(
			'limit' 		  => $instance['numProd']  ? $instance['numProd'] : 5,
			'query' 		  => $instance['query'] ? trim($instance['query']) : '',
			'imageSize'		  => $imageSize,
			'filterCategory'  => $instance['categories'] ? array_map('trim', explode(',', $instance['categories'])) : '',
			'filterMerchant'  => $instance['merchants'] ? array_map('trim', explode(',', $instance['merchants'])) : '',
			'filterPriceSale' => $instance['onSale'] ? (($instance['priceRangea'] || $instance['priceRangeb']) ? $instance['priceRangea'] . ',' . $instance['priceRangeb'] : '0.01,') : '',
			'filterPrice' 	  => ($instance['onSale'] ? '' : (($instance['priceRangea'] || $instance['priceRangeb']) ? $instance['priceRangea'] . ',' . $instance['priceRangeb'] : '')),
		);

		if (!$instance['coupons'])
		{
			$settings['filterBrand'] = $instance['brands'] ? array_map('trim', explode(',', $instance['brands'])) : '';
		}
		
		$settings = array_filter($settings);
		
		$curlUrls = $modelSearch->apiCall($settings, $fetch);

		$allData = $modelSearch->singleCurlCall($curlUrls, $expiration);
		
		if ($allData)
		{		
			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;		
			
			$gridImage = ($instance['imageSize'] ? preg_replace('/px|em|%/i', '', $instance['imageSize']) : 200) . 'px';
				
			$classLoad = ($type === 'coupon' || $gridImage < 120) ? 'class="loadCoup"' : 'class="load"';
			echo '<div id="simProd" style="width:100%">';
			echo '<ul>';	

			foreach ($allData['data'] as $record)
			{
				if (is_ssl())
				{
					$record['image_url'] = str_replace('http', 'https', $record['image_url']);
				}
				
				$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
				$price 	   = $priceSale ? '<div class="prodPriceSale">' . ($currency == 'GBP' ? '&pound;' : '$') . $priceSale . '</div>' : '<div class="prodPrice">' . ($currency == 'GBP' ? '&pound;' : '$') . $record['price'] . '</div>';
				$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
				$cid 	   = $type === 'coupon' ? $record['couponId'] : ($type === 'local' ? $record['localId'] : $record['catalogId']);
				?>
					<li style="float:none;">
						<div class="listBlock">
							<div class="prodImage">
								<a href=<?php echo ($options['imageMercLink'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink"><span <?php echo $classLoad . ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px;margin:0 15px"'); ?>><img <?php echo ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px;"'); ?> src="<?php echo $options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('https://img1.prosperent.com/images/', 'http://img1.prosperent.com/images/', '/'), array('', '', ',SL,'), $record['image_url'])) : $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
							</div>
								<?php
								if ($record['promo'])
								{					
									echo '<div class="promo"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink">' . $record['promo'] . '!</a></span></div>';
								}
								elseif($record['expiration_date'] || $record['expirationDate'])
								{
									$expirationDate = $record['expirationDate'] ? $record['expirationDate'] : $record['expiration_date'];			
									$expires = strtotime($expirationDate);
									$today = strtotime(date("Y-m-d"));
									$interval = ($expires - $today) / (60*60*24);

									if ($interval <= 20 && $interval > 0)
									{
										echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink">' . $interval . ' Day' . ($interval > 1 ? 's' : '') . ' Left!</a></span></div>';
									}
									elseif ($interval <= 0)
									{
										echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink">Ends Today!</a></span></div>';
									}
									else
									{
										echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink">Expires Soon!</a></span></div>';
									}
								}
								elseif ($type == 'coupon' || $type == 'local')
								{
									echo '<div class="promo">&nbsp;</div>';
								}
								?>
							<div class="prodContent">
								<div class="prodTitle">
									<a href=<?php echo ($options['titleMercLink'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink">
										<?php echo $keyword; ?>
									</a>
								</div>     
								<?php if ($price && $type != 'coupon' && $type != 'local'){ echo $price; } ?>												
							</div>
							
							<div class="prosperVisit">					
								<form class="shopCheck" action="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" method="POST" rel="nofollow,nolink">
									<input type="submit" value="Visit Store"/>
								</form>
							</div>	
						</div>			
					</li>
				<?php
			}

			echo '</ul>';
			echo '</div>';

			echo $after_widget;
		}
		else { return; }
    }

    public function update( $new_instance, $old_instance )
    {
		if (is_active_widget(false, false, $this->id_base, true) )
		{
			require_once(PROSPER_MODEL . '/Admin.php');
			$this->adminModel = new Model_Admin();
		
			$this->adminModel->_settingsHistory();					
		}
	
        $new_instance = (array) $new_instance;
        $new_instance = wp_parse_args((array) $new_instance, array( 'title' => '', 'query' => '', 'categories' => '', 'merchants' => '', 'brands' => '', 'priceRangea' => '', 'priceRangeb' => '', 'onSale' => '', 'imageSize' => 125, 'numProd' => 5, 'goToMerch' => '', 'useTitle' => '', 'coupons' => ''));
        $instance['title'] = strip_tags($new_instance['title']);
		$instance['query'] = strip_tags($new_instance['query']);
		$instance['categories'] = strip_tags($new_instance['categories']);
		$instance['merchants'] = strip_tags($new_instance['merchants']);
		$instance['brands'] = strip_tags($new_instance['brands']);
		$instance['priceRangea'] = strip_tags($new_instance['priceRangea']);
		$instance['priceRangeb'] = strip_tags($new_instance['priceRangeb']);
		$instance['onSale'] = strip_tags($new_instance['onSale']);
		$instance['imageSize'] = strip_tags($new_instance['imageSize']);
		$instance['numProd'] = strip_tags($new_instance['numProd']);
		$instance['goToMerch'] = strip_tags($new_instance['goToMerch']);
		$instance['useTitle'] = strip_tags($new_instance['useTitle']);
		$instance['coupons'] = strip_tags($new_instance['coupons']);
        return $instance;
    }

    public function form( $instance )
    {
        $instance    = wp_parse_args( (array) $instance, array( 'title' => '', 'query' => '', 'categories' => '', 'merchants' => '', 'brands' => '', 'priceRangea' => '', 'priceRangeb' => '', 'onSale' => '', 'imageSize' => 125, 'numProd' => 5, 'goToMerch' => '', 'useTitle' => '', 'coupons' => '') );
        $title       = $instance['title'];
		$query       = $instance['query'];
		$categories  = $instance['categories'];
		$merchants   = $instance['merchants'];
		$brands	     = $instance['brands'];
		$priceRangea = $instance['priceRangea'];
		$priceRangeb = $instance['priceRangeb'];
		$onSale	     = $instance['onSale'];
		$imageSize   = $instance['imageSize'];
		$numProd     = $instance['numProd'];
		$goToMerch   = $instance['goToMerch'];
		$useTitle    = $instance['useTitle'];
		$coupons     = $instance['coupons'];
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('query'); ?>"><?php _e('Query:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('query'); ?>" name="<?php echo $this->get_field_name('query'); ?>" type="text" value="<?php echo esc_attr($query); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:'); ?></label><a href="#" class="prosper_tooltip"><span>Filters the results by category. Comma separated list.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" type="text" value="<?php echo esc_attr($categories); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('merchants'); ?>"><?php _e('Merchants:'); ?></label><a href="#" class="prosper_tooltip"><span>Filters the results by merchant. Comma separated list.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('merchants'); ?>" name="<?php echo $this->get_field_name('merchants'); ?>" type="text" value="<?php echo esc_attr($merchants); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('brands'); ?>"><?php _e('Brands:'); ?></label><a href="#" class="prosper_tooltip"><span>Filters the results by brand. Comma separated list.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('brands'); ?>" name="<?php echo $this->get_field_name('brands'); ?>" type="text" value="<?php echo esc_attr($brands); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('priceRangea'); ?>"><?php _e('Price Range:'); ?></label><a href="#" class="prosper_tooltip"><span>Enter a price range for the products.</span></a><br>
        <input class="widefat" id="<?php echo $this->get_field_id('priceRangea'); ?>" name="<?php echo $this->get_field_name('priceRangea'); ?>" type="text" value="<?php echo esc_attr($priceRangea); ?>" style="width:48.5%" />&nbsp;&nbsp;&nbsp;<input class="widefat" id="<?php echo $this->get_field_id('priceRangeb'); ?>" name="<?php echo $this->get_field_name('priceRangeb'); ?>" type="text" value="<?php echo esc_attr($priceRangeb); ?>" style="width:48.5%"/></p>
		<p><label for="<?php echo $this->get_field_id('onSale'); ?>"><?php _e('On Sale Only:'); ?></label><a href="#" class="prosper_tooltip"><span>Will show on sale items only. If you check this and have a price range, the price range will be for sale price.</span></a>
        <input id="<?php echo $this->get_field_id('onSale'); ?>" name="<?php echo $this->get_field_name('onSale'); ?>" type="checkbox" value="1" <?php echo checked( esc_attr($onSale), 1, false ); ?> /></p>
		<p><label for="<?php echo $this->get_field_id('imageSize'); ?>"><?php _e('Image Size:'); ?></label><a href="#" class="prosper_tooltip"><span>Enter the image size for your widget.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('imageSize'); ?>" name="<?php echo $this->get_field_name('imageSize'); ?>" type="text" value="<?php echo esc_attr($imageSize); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('numProd'); ?>"><?php _e('Number of Products to Show:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('numProd'); ?>" name="<?php echo $this->get_field_name('numProd'); ?>" type="text" value="<?php echo esc_attr($numProd); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('goToMerch'); ?>"><?php _e('Go To Merchant:'); ?></label><a href="#" class="prosper_tooltip"><span>When clicked, the link will go to the merchant page instead of the product page.</span></a>
        <input id="<?php echo $this->get_field_id('goToMerch'); ?>" name="<?php echo $this->get_field_name('goToMerch'); ?>" type="checkbox" value="1" <?php echo checked( esc_attr($goToMerch), 1, false ); ?> /></p>
		<p><label for="<?php echo $this->get_field_id('useTitle'); ?>"><?php _e('Use Page/Post Title as:'); ?></label><a href="#" class="prosper_tooltip"><span>Make sure the page/post titles are compatible. Some titles may result in little or no results. If checked this will be true for all pages/posts with the Product Insert widget.</span></a>
		<div style="text-align:center;">
			<input type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="query" <?php echo checked( esc_attr($useTitle), 'query', false ); ?> /> <strong>Query</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="categories" <?php echo checked( esc_attr($useTitle), 'categories', false ); ?> /> <strong>Category</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="merchants" <?php echo checked( esc_attr($useTitle), 'merchants', false ); ?> /> <strong>Merchant</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="brands" <?php echo checked( esc_attr($useTitle), 'brands', false ); ?> /> <strong>Brand</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="" <?php echo checked( esc_attr($useTitle), '', false ); ?> /> <strong>None</strong>
		</div>
		<p><label for="<?php echo $this->get_field_id('coupons'); ?>"><?php _e('Display Coupons:'); ?></label><a href="#" class="prosper_tooltip"><span>Will display coupons instead of products (will not use anything entered in brand).</span></a>
        <input id="<?php echo $this->get_field_id('coupons'); ?>" name="<?php echo $this->get_field_name('coupons'); ?>" type="checkbox" value="1" <?php echo checked( esc_attr($coupons), 1, false ); ?> /></p>
		<br>
        <?php
    }
}
