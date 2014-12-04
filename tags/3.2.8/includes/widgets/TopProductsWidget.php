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

        extract($args);	
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;			

		if($instance['useTitle'])
		{ 
			$instance[$instance['useTitle']] = strtolower(get_the_title());
		}			

		require_once(PROSPER_MODEL . '/Search.php');
		$modelSearch = new Model_Search();
		$modelSearch->getFetchEndpoints();
					
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
		
		$settings = array(
			'limit' 		 => $instance['numProd']  ? $instance['numProd'] : 5,
			'enableFullData' => 0,
			'imageSize'		 => '125x125'
		);
		
		$categories = $instance['categories'] ? array_map('trim', explode(',', $instance['categories'])) : '';
		$merchants  = $instance['merchants'] ? array_map('trim', explode(',', $instance['merchants'])) : '';
		$brands     = $instance['brands'] ? array_map('trim', explode(',', $instance['brands'])) : '';
		
		$allData = $modelSearch->trendsApiCall($settings, $fetch, $categories, $merchants, $brands, $sid);			

		?>
		<table>
			<?php
			if ($allData)
			{
				foreach ($allData['data'] as $record)
				{
					if ($instance['goToMerch'] && $options['URL_Masking'])
					{
						$goToUrl = home_url() . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url']));
					}
					elseif ($instance['goToMerch'])
					{
						$goToUrl = $record['affiliate_url'];
					}
					else
					{
						$goToUrl = home_url() . '/product/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId'];
					}
					
					echo '<tr><td>&bull;&nbsp;</td><td style="padding-bottom:4px; font-size:13px;"><a href="' . $goToUrl . '" rel="nolink">' . preg_replace('/\(.+\)/i', '', $record['keyword']) . '</a></td></tr>';

				}
			}
			else
			{
				echo '<tr><td>No Trending Products</td></tr>';
			}
			?>
		</table>
		<?php

        echo $after_widget;
    }

    public function update( $new_instance, $old_instance )
    {
        $new_instance = (array) $new_instance;
        $new_instance = wp_parse_args((array) $new_instance, array( 'title' => '', 'categories' => '', 'merchants' => '', 'brands' => '', 'numProd' => '', 'goToMerch' => '', 'showImages' => '', 'useTitle' => ''));
        $instance['title'] = strip_tags($new_instance['title']);
		$instance['categories'] = strip_tags($new_instance['categories']);
		$instance['merchants'] = strip_tags($new_instance['merchants']);
		$instance['brands'] = strip_tags($new_instance['brands']);
		$instance['numProd'] = strip_tags($new_instance['numProd']);
		$instance['goToMerch'] = strip_tags($new_instance['goToMerch']);
		$instance['showImages'] = strip_tags($new_instance['showImages']);
		$instance['useTitle'] = strip_tags($new_instance['useTitle']);
        return $instance;
    }

    public function form( $instance )
    {
        $instance   = wp_parse_args( (array) $instance, array( 'title' => '', 'categories' => '', 'merchants' => '', 'brands' => '', 'numProd' => 5, 'goToMerch' => '', 'showImages' => '', 'useTitle' => '') );
        $title      = $instance['title'];
		$categories = $instance['categories'];
		$merchants  = $instance['merchants'];
		$brands	    = $instance['brands'];
		$numProd    = $instance['numProd'];
		$goToMerch  = $instance['goToMerch'];
		$showImages = $instance['showImages'];
		$useTitle   = $instance['useTitle'];
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
		<?php /* 
		<p><label for="<?php echo $this->get_field_id('showImages'); ?>"><?php _e('Display Image:'); ?></label><a href="#" class="prosper_tooltip"><span>Will show the trending product image instead of the title.</span></a>
        <input id="<?php echo $this->get_field_id('showImages'); ?>" name="<?php echo $this->get_field_name('showImages'); ?>" type="checkbox" value="1" <?php echo checked( esc_attr($showImages), 1, false ); ?> /></p>
		*/ ?>
		<p><label for="<?php echo $this->get_field_id('useTitle'); ?>"><?php _e('Use Page/Post Title as:'); ?></label><a href="#" class="prosper_tooltip"><span>Make sure the page/post titles are compatible. Some titles may result in little or no results. If checked this will be true for all pages/posts with the Top Products widget.</span></a>
		<div style="text-align:center;">
			<input type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="categories" <?php echo checked( esc_attr($useTitle), 'categories', false ); ?> /> <strong>Category</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="merchants" <?php echo checked( esc_attr($useTitle), 'merchants', false ); ?> /> <strong>Merchant</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="brands" <?php echo checked( esc_attr($useTitle), 'brands', false ); ?> /> <strong>Brand</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('useTitle'); ?>" value="" <?php echo checked( esc_attr($useTitle), '', false ); ?> /> <strong>None</strong>
		</div>
		<br>
        <?php
    }
}
