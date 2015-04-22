<?php
class TopProductsWidget extends WP_Widget
{
    public function __construct()
    {
	    parent::__construct(
			'prosper_top_products', 
			'Top Products', 
			array('classname' => 'top_products_widget', 'description' => __( "Displays the top Products of Prosperent at the time"))
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

		$target  = isset($options['Target']) ? '_blank' : '_self';
		
        extract($args);	
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'Top Products' : $instance['title'], $instance, $this->id_base );	

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
		
		if ($options['Country'] === 'US')
		{
			$fetch = 'fetchProducts';
			$currency = 'USD';
		}
		elseif($options['Country'] === 'UK')
		{
			$fetch = 'fetchUkProducts';
			$currency = 'GBP';
		}
		else
		{
			$fetch = 'fetchCaProducts';
			$currency = 'CAD';
		}
		
		$sidArray = array();
		if ($options['prosperSid'] && !$sid)
		{
			foreach ($options['prosperSid'] as $sidPiece)
			{
				switch ($sidPiece)
				{
					case 'blogname':
						$sidArray[] = get_bloginfo('name');
						break;
					case 'interface':
						$sidArray[] = $settings['interface'] ? $settings['interface'] : 'api';
						break;
					case 'query':
						$sidArray[] = $settings['query'];
						break;
					case 'page':
						$sidArray[] = get_the_title();
						break;						
				}
			}
		}
		if ($options['prosperSidText'] && !$sid)
		{
			if (preg_match('/(^\$_(SERVER|SESSION|COOKIE))\[(\'|")(.+?)(\'|")\]/', $options['prosperSidText'], $regs))
			{
				if ($regs[1] == '$_SERVER')
				{
					$sidArray[] = $_SERVER[$regs[4]];
				}
				elseif ($regs[1] == '$_SESSION')
				{
					$sidArray[] = $_SESSION[$regs[4]];
				}
				elseif ($regs[1] == '$_COOKIE')
				{
					$sidArray[] = $_COOKIE[$regs[4]];
				}					
			}
			elseif (!preg_match('/\$/', $options['prosperSidText']))
			{
				$sidArray[] = $options['prosperSidText'];
			}
		}
		
		if (!empty($sidArray))
		{
			$sidArray = array_filter($sidArray);
			$sid = implode('_', $sidArray);
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
			'limit' 		 => $instance['numProd']  ? $instance['numProd'] : 5,
			'enableFullData' => $instance['showImages'] ? 1 : 0,
			'imageSize'		 => $imageSize
		);
		
		$categories = $instance['categories'] ? array_map('trim', explode(',', $instance['categories'])) : '';
		$merchants  = $instance['merchants'] ? array_map('trim', explode(',', $instance['merchants'])) : '';
		$brands     = $instance['brands'] ? array_map('trim', explode(',', $instance['brands'])) : '';
		
		$allData = $modelSearch->trendsApiCall($settings, $fetch, $categories, $merchants, $brands, $sid);			

		if ($allData && $instance['showImages'])
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
				$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
				$price 	   = $priceSale ? '<div class="prodPriceSale">' . ($currency == 'GBP' ? '&pound;' : '$') . number_format($priceSale, 2) . '</div>' : '<div class="prodPrice">' . ($currency == 'GBP' ? '&pound;' : '$') . number_format($record['price'], 2) . '</div>';
				$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
				$cid 	   = $type === 'coupon' ? $record['couponId'] : ($type === 'local' ? $record['localId'] : $record['catalogId']);
				?>
					<li style="float:none;">
						<div class="listBlock">
							<div class="prodImage">
								<a href=<?php echo ($instance['goToMerch'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink"><span <?php echo $classLoad . ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px;margin:0 15px"'); ?>><img <?php echo ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px;"'); ?> src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
							</div>								
							<div class="prodContent">
								<div class="prodTitle">
									<a href=<?php echo ($instance['goToMerch'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink">
										<?php echo $keyword; ?>
									</a>
								</div>     
								<?php echo $price; ?>												
							</div>
														
							<div class="shopCheck prosperVisit">		
								<a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="Visit Store"/></a>				
							</div>	
						</div>			
					</li>
				<?php
			}

			echo '</ul>';
			echo '</div>';	
			echo $after_widget;			
		}
		elseif ($allData)
		{
			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;		
			?>
			<table>
				<?php
				foreach ($allData['data'] as $record)
				{
					if ($instance['goToMerch'])
					{
						$goToUrl = $record['affiliate_url'];
					}
					else
					{
						$goToUrl = home_url() . '/product/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId'];
						$target = '_self';
					}
					
					echo '<tr><td>&bull;&nbsp;</td><td style="padding-bottom:4px; font-size:13px;"><a href="' . $goToUrl . '" rel="nolink" target="' . $target . '">' . preg_replace('/\(.+\)/i', '', $record['keyword']) . '</a></td></tr>';

				}
				?>
			</table>
			<?php

			echo $after_widget;
		}
		else
		{
			return;
		}		
    }

    public function update( $new_instance, $old_instance )
    {
		if (is_active_widget(false, false, $this->id_base, true) )
		{
			require_once(PROSPER_MODEL . '/Admin.php');
			$this->adminModel = new Model_Admin();
		
			$this->adminModel->_settingsHistory('activated', array('trendsWidget' => 1));					
		}	
		
        $new_instance = (array) $new_instance;
        $new_instance = wp_parse_args((array) $new_instance, array( 'title' => '', 'categories' => '', 'merchants' => '', 'brands' => '', 'numProd' => '', 'goToMerch' => '', 'useTitle' => '', 'showImages' => '', 'imageSize' => 125));
        $instance['title'] = strip_tags($new_instance['title']);
		$instance['categories'] = strip_tags($new_instance['categories']);
		$instance['merchants'] = strip_tags($new_instance['merchants']);
		$instance['brands'] = strip_tags($new_instance['brands']);
		$instance['numProd'] = strip_tags($new_instance['numProd']);
		$instance['goToMerch'] = strip_tags($new_instance['goToMerch']);
		$instance['useTitle'] = strip_tags($new_instance['useTitle']);
		$instance['showImages'] = strip_tags($new_instance['showImages']);
		$instance['imageSize'] = strip_tags($new_instance['imageSize']);
        return $instance;
    }

    public function form( $instance )
    {
        $instance   = wp_parse_args( (array) $instance, array( 'title' => '', 'categories' => '', 'merchants' => '', 'brands' => '', 'numProd' => 5, 'goToMerch' => '', 'useTitle' => '', 'showImages' => '', 'imageSize' => 125) );
        $title      = $instance['title'];
		$categories = $instance['categories'];
		$merchants  = $instance['merchants'];
		$brands	    = $instance['brands'];
		$numProd    = $instance['numProd'];
		$goToMerch  = $instance['goToMerch'];
		$useTitle   = $instance['useTitle'];
		$showImages = $instance['showImages'];
		$imageSize  = $instance['imageSize'];
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:'); ?></label><a href="#" class="prosper_tooltip"><span>Filters the top products by category. Comma separated list.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" type="text" value="<?php echo esc_attr($categories); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('merchants'); ?>"><?php _e('Merchants:'); ?></label><a href="#" class="prosper_tooltip"><span>Filters the top products by merchant. Comma separated list.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('merchants'); ?>" name="<?php echo $this->get_field_name('merchants'); ?>" type="text" value="<?php echo esc_attr($merchants); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('brands'); ?>"><?php _e('Brands:'); ?></label><a href="#" class="prosper_tooltip"><span>Filters the top products by brand. Comma separated list.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('brands'); ?>" name="<?php echo $this->get_field_name('brands'); ?>" type="text" value="<?php echo esc_attr($brands); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('numProd'); ?>"><?php _e('Number of Products to Show:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('numProd'); ?>" name="<?php echo $this->get_field_name('numProd'); ?>" type="text" value="<?php echo esc_attr($numProd); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('goToMerch'); ?>"><?php _e('Go To Merchant:'); ?></label><a href="#" class="prosper_tooltip"><span>When clicked, the link will go to the merchant page instead of the product page.</span></a>
        <input id="<?php echo $this->get_field_id('goToMerch'); ?>" name="<?php echo $this->get_field_name('goToMerch'); ?>" type="checkbox" value="1" <?php echo checked( esc_attr($goToMerch), 1, false ); ?> /></p>
		<p><label for="<?php echo $this->get_field_id('useTitle'); ?>"><?php _e('Use Page/Post Title as:'); ?></label><a href="#" class="prosper_tooltip"><span>Make sure the page/post titles are compatible. Some titles may result in little or no results. If checked this will be true for all pages/posts with the Top Products widget.</span></a>
		<div style="text-align:center;">
			<input type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="categories" <?php echo checked( esc_attr($useTitle), 'categories', false ); ?> /> <strong>Category</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="merchants" <?php echo checked( esc_attr($useTitle), 'merchants', false ); ?> /> <strong>Merchant</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="brands" <?php echo checked( esc_attr($useTitle), 'brands', false ); ?> /> <strong>Brand</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="" <?php echo checked( esc_attr($useTitle), '', false ); ?> /> <strong>None</strong>
		</div>
		<p><label for="<?php echo $this->get_field_id('showImages'); ?>"><?php _e('Display Image:'); ?></label><a href="#" class="prosper_tooltip"><span>Will show the trending product image instead of the title.</span></a>
        <input id="<?php echo $this->get_field_id('showImages'); ?>" name="<?php echo $this->get_field_name('showImages'); ?>" type="checkbox" value="1" <?php echo checked( esc_attr($showImages), 1, false ); ?> /></p>
		<p><label for="<?php echo $this->get_field_id('imageSize'); ?>"><?php _e('Image Size:'); ?></label><a href="#" class="prosper_tooltip"><span>Enter the image size for your widget.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('imageSize'); ?>" name="<?php echo $this->get_field_name('imageSize'); ?>" type="text" value="<?php echo esc_attr($imageSize); ?>" /></p>
		<br>
        <?php
    }
}
