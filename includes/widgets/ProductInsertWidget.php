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

		$target  = isset($options['Target']) ? '_blank' : '_self';

		if($instance['useTitle'])
		{
			$instance[$instance['useTitle']] = strtolower(get_the_title());
		}

		require_once(PROSPER_MODEL . '/Search.php');
		$modelSearch = new Model_Search();
		$modelSearch->getFetchEndpoints();

		$homeUrl = home_url('', 'http');
		if (is_ssl())
		{
			$homeUrl = home_url('', 'https');
		}

		$expiration = PROSPER_CACHE_PRODS;
		$type = 'product';
		$visitButton = 'Visit Store';
		$fetch = 'fetchProducts';
		$currency = 'USD';


		$settings = array(
			'limit' 		  => $instance['numProd']  ? $instance['numProd'] : 5,
			'query' 		  => $instance['query'] ? trim($instance['query']) : '',
			'imageSize'		  => '500x500',
			'filterCategory'  => $instance['categories'] ? array_map('trim', explode(',', $instance['categories'])) : '',
			'filterMerchant'  => $instance['merchants'] ? array_map('trim', explode(',', $instance['merchants'])) : '',
			'filterPriceSale' => $instance['onSale'] ? (($instance['priceRangea'] || $instance['priceRangeb']) ? $instance['priceRangea'] . ',' . $instance['priceRangeb'] : '0.01,') : '',
			'filterPrice' 	  => ($instance['onSale'] ? '' : (($instance['priceRangea'] || $instance['priceRangeb']) ? $instance['priceRangea'] . ',' . $instance['priceRangeb'] : '')),
		);

		$settings = array_filter($settings);

		if (!$sid)
		{
		    $sid = $modelSearch->getSid($settings, array(
    		        'title' => $title,
    		        'type' => 'ProsperInsert'
		        )
		    );
		}

		$curlUrls = $modelSearch->apiCall($settings, $fetch, $sid);
		$allData = $modelSearch->singleCurlCall($curlUrls, $expiration);

		if ($allData)
		{
			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;

			$gridImage = ($instance['imageSize'] ? preg_replace('/px|em|%/i', '', $instance['imageSize']) : 200) . 'px';

			echo '<div id="simProd" class="prosperSide" style="width:100%;">';
			echo '<ul style="width:100%;">';

			foreach ($allData['data'] as $record)
			{
				$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
				$price 	   = $priceSale ? '$' . number_format($priceSale, 2) . '' : '$' . number_format($record['price'], 2);
				$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
				$cid 	   = $record['catalogId'];
				?>
                <li style="float:none;margin:0;width:100%;">
					<div class="prodImage">
						<a href=<?php echo ($instance['goTo'] == 'merchant' ? '"' . $record['affiliate_url'] . '&interface=wp&subinterface=prosperinsert" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink"><span class="prosperLoad"><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
					</div>
					<div class="prodContent">
						<div class="prodTitle">
						    <a href=<?php echo $options['goTo'] == 'merchant' ? '"' . $record['affiliate_url'] . '&interface=wp&subinterface=prosperinsert" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'; ?> rel="nolink" style="text-decoration:none;color:#646464"><?php echo $record['brand']; ?></a>
						</div>
						<div class="<?php echo ($priceSale ? 'prodPriceSale' : 'prodPrice'); ?>">
						    <?php echo $price . '<span style="color:#666;font-size:12px;font-weight:normal;"> from</span><div style="display:inline;color:#666;font-size:14px;font-weight:normal;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;"> ' . $record['merchant'] . '</div>' ?>
						</div>
					</div>
					<div class="shopCheck prosperVisit">
						<a href="<?php echo $record['affiliate_url'] . '&interface=wp&subinterface=prosperinsert'; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input id="submit" class="submit" type="submit" value="Visit Store<?php //echo $visitButton; ?>"/></a>
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
        $new_instance = wp_parse_args((array) $new_instance, array( 'title' => '', 'query' => '', 'categories' => '', 'merchants' => '', 'brands' => '', 'priceRangea' => '', 'priceRangeb' => '', 'onSale' => '', 'numProd' => 5, 'goTo' => '', 'useTitle' => ''));
        $instance['title'] = strip_tags($new_instance['title']);
		$instance['query'] = strip_tags($new_instance['query']);
		$instance['categories'] = strip_tags($new_instance['categories']);
		$instance['merchants'] = strip_tags($new_instance['merchants']);
		$instance['brands'] = strip_tags($new_instance['brands']);
		$instance['priceRangea'] = strip_tags($new_instance['priceRangea']);
		$instance['priceRangeb'] = strip_tags($new_instance['priceRangeb']);
		$instance['onSale'] = strip_tags($new_instance['onSale']);
		$instance['numProd'] = strip_tags($new_instance['numProd']);
		$instance['goTo'] = strip_tags($new_instance['goTo']);
		$instance['useTitle'] = strip_tags($new_instance['useTitle']);
        return $instance;
    }

    public function form( $instance )
    {
        $instance    = wp_parse_args( (array) $instance, array( 'title' => '', 'query' => '', 'categories' => '', 'merchants' => '', 'brands' => '', 'priceRangea' => '', 'priceRangeb' => '', 'onSale' => '', 'numProd' => 5, 'goTo' => 'merchant', 'useTitle' => '', ) );
        $title       = $instance['title'];
		$query       = $instance['query'];
		$categories  = $instance['categories'];
		$merchants   = $instance['merchants'];
		$brands	     = $instance['brands'];
		$priceRangea = $instance['priceRangea'];
		$priceRangeb = $instance['priceRangeb'];
		$onSale	     = $instance['onSale'];
		$numProd     = $instance['numProd'];
		$goTo        = $instance['goTo'];
		$useTitle    = $instance['useTitle'];
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
        $&nbsp;<input class="widefat" id="<?php echo $this->get_field_id('priceRangea'); ?>" name="<?php echo $this->get_field_name('priceRangea'); ?>" type="text" value="<?php echo esc_attr($priceRangea); ?>" style="width:44%" />&nbsp;to&nbsp;$&nbsp;<input class="widefat" id="<?php echo $this->get_field_id('priceRangeb'); ?>" name="<?php echo $this->get_field_name('priceRangeb'); ?>" type="text" value="<?php echo esc_attr($priceRangeb); ?>" style="width:44%"/></p>
		<p><label for="<?php echo $this->get_field_id('onSale'); ?>"><?php _e('On Sale Only:'); ?></label><a href="#" class="prosper_tooltip"><span>Will show on sale items only. If you check this and have a price range, the price range will be for sale price.</span></a>
        <input id="<?php echo $this->get_field_id('onSale'); ?>" name="<?php echo $this->get_field_name('onSale'); ?>" type="checkbox" value="1" <?php echo checked( esc_attr($onSale), 1, false ); ?> /></p>
		<p><label for="<?php echo $this->get_field_id('numProd'); ?>"><?php _e('Number of Products to Show:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('numProd'); ?>" name="<?php echo $this->get_field_name('numProd'); ?>" type="text" value="<?php echo esc_attr($numProd); ?>" /></p>
		<label  style="display:inline" for="<?php echo $this->get_field_id('goTo'); ?>"><?php _e('Go To:'); ?></label><a href="#" class="prosper_tooltip"><span>Determines where to send the visitor when they click on a product.</span></a>
		<input type="radio" name="<?php echo $this->get_field_name('goTo'); ?>" value="merchant" <?php echo checked( esc_attr($goTo), 'merchant', false ); ?> /> <strong>Merchant</strong>
		<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('goTo'); ?>" value="prodPage" <?php echo checked( esc_attr($goTo), 'prodPage', false ); ?> /> <strong>Product Page</strong>
		<p><label for="<?php echo $this->get_field_id('useTitle'); ?>"><?php _e('Use Page/Post Title as:'); ?></label><a href="#" class="prosper_tooltip"><span>Make sure the page/post titles are compatible. Some titles may result in little or no results. If checked this will be true for all pages/posts with the Product Insert widget.</span></a></p>
		<div style="text-align:center;">
			<input type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="query" <?php echo checked( esc_attr($useTitle), 'query', false ); ?> /> <strong>Query</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="categories" <?php echo checked( esc_attr($useTitle), 'categories', false ); ?> /> <strong>Category</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="merchants" <?php echo checked( esc_attr($useTitle), 'merchants', false ); ?> /> <strong>Merchant</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="brands" <?php echo checked( esc_attr($useTitle), 'brands', false ); ?> /> <strong>Brand</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="" <?php echo checked( esc_attr($useTitle), '', false ); ?> /> <strong>None</strong>
		</div>
		<br>
        <?php
    }
}
