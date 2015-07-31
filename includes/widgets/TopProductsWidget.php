<?php
class TopProductsWidget extends WP_Widget
{
    public function __construct()
    {
	    parent::__construct(
			'prosper_top_products', 
			'Popular Products', 
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
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'Popular Products' : $instance['title'], $instance, $this->id_base );	

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
		$fetch = 'fetchProducts';
	    $currency = 'USD';

		if (!$sid)
		{
            $sid = $modelSearch->getSid($settings, array(
    		        'title' => $title,
    		        'type' => 'PopularProducts'
		        )
		    );
		}	
		
		$settings = array(
			'limit' 		 => $instance['numProd']  ? $instance['numProd'] : 5,
			'enableFullData' => $instance['showImages'] ? 1 : 0,
			'imageSize'		 => '500x500'
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
						<a href=<?php echo ($instance['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink"><span class="prosperLoad"><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
					</div>
					<div class="prodContent">
						<div class="prodTitle">
						    <a onClick="return false;" href=<?php echo $options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'; ?> rel="nolink" style="text-decoration:none;color:#646464"><?php echo $record['brand']; ?></a>
							<?php /*<a href=<?php echo ($options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"javascript:void(0);" onClick="prosperProdDetails();"'); ?> rel="nolink">
								<?php echo $keyword; ?>
							</a> */?>
						</div>   
						<div class="<?php echo ($priceSale ? 'prodPriceSale' : 'prodPrice'); ?>">  
						    <?php echo $price . '<span style="color:#666;font-size:12px;font-weight:normal;"> from</span><div style="display:inline;color:#666;font-size:14px;font-weight:normal;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;"> ' . $record['merchant'] . '</div>' ?>
						</div>												
					</div>								
					<div class="shopCheck prosperVisit">		
						<a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input id="submit" class="submit" type="submit" value="Visit Store<?php //echo $visitButton; ?>"/></a>				
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
					if ($instance['gotoMerchantBypass'])
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
		
			$this->adminModel->_settingsHistory();					
		}	
		
        $new_instance = (array) $new_instance;
        $new_instance = wp_parse_args((array) $new_instance, array( 'title' => '', 'categories' => '', 'merchants' => '', 'brands' => '', 'numProd' => '', 'useTitle' => '', 'showImages' => ''));
        $instance['title'] = strip_tags($new_instance['title']);
		$instance['categories'] = strip_tags($new_instance['categories']);
		$instance['merchants'] = strip_tags($new_instance['merchants']);
		$instance['brands'] = strip_tags($new_instance['brands']);
		$instance['numProd'] = strip_tags($new_instance['numProd']);
		$instance['useTitle'] = strip_tags($new_instance['useTitle']);
		$instance['showImages'] = strip_tags($new_instance['showImages']);
        return $instance;
    }

    public function form( $instance )
    {
        $instance   = wp_parse_args( (array) $instance, array( 'title' => '', 'categories' => '', 'merchants' => '', 'brands' => '', 'numProd' => 5, 'useTitle' => '', 'showImages' => '') );
        $title      = $instance['title'];
		$categories = $instance['categories'];
		$merchants  = $instance['merchants'];
		$brands	    = $instance['brands'];
		$numProd    = $instance['numProd'];
		$useTitle   = $instance['useTitle'];
		$showImages = $instance['showImages'];
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
		<p><label for="<?php echo $this->get_field_id('useTitle'); ?>"><?php _e('Use Page/Post Title as:'); ?></label><a href="#" class="prosper_tooltip"><span>Make sure the page/post titles are compatible. Some titles may result in little or no results. If checked this will be true for all pages/posts with the Top Products widget.</span></a>
		<div style="text-align:center;">
			<input type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="categories" <?php echo checked( esc_attr($useTitle), 'categories', false ); ?> /> <strong>Category</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="merchants" <?php echo checked( esc_attr($useTitle), 'merchants', false ); ?> /> <strong>Merchant</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="brands" <?php echo checked( esc_attr($useTitle), 'brands', false ); ?> /> <strong>Brand</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="" <?php echo checked( esc_attr($useTitle), '', false ); ?> /> <strong>None</strong>
		</div>
		<p><label for="<?php echo $this->get_field_id('showImages'); ?>"><?php _e('Display Images:'); ?></label><a href="#" class="prosper_tooltip"><span>Will show the trending product image instead of the title.</span></a>
        <input id="<?php echo $this->get_field_id('showImages'); ?>" name="<?php echo $this->get_field_name('showImages'); ?>" type="checkbox" value="1" <?php echo checked( esc_attr($showImages), 1, false ); ?> /></p>
		<br>
        <?php
    }
}
