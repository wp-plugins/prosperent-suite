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
        $options = get_option('prosper_productSearch');

        extract($args);	
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;			
			
		require_once(PROSPER_MODEL . '/Search.php');
		$modelSearch = new Model_Search();
					
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
		
		$settings = array(
			'limit' 		 => $instance['numProd']  ? $instance['numProd'] : 5,
			'enableFullData' => 0
		);
		
		$allData = $modelSearch->trendsApiCall($settings, $fetch, array_map('trim', explode(',', $instance['categories'])));			
		
		?>
		<table>
			<?php
			foreach ($allData['results'] as $record)
			{
				echo '<tr><td>&bull;&nbsp;</td><td style="padding-bottom:4px; font-size:13px;"><a href="' . home_url() . '/product/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId'] . '" rel="nolink">' . preg_replace('/\(.+\)/i', '', $record['keyword']) . '</a></td></tr>';
			}
			?>
		</table>
		<?php

        echo $after_widget;
    }

    public function update( $new_instance, $old_instance )
    {
        $new_instance = (array) $new_instance;
        $new_instance = wp_parse_args((array) $new_instance, array( 'title' => '', 'categories' => '', 'numProd' => ''));
        $instance['title'] = strip_tags($new_instance['title']);
		$instance['categories'] = strip_tags($new_instance['categories']);
		$instance['numProd'] = strip_tags($new_instance['numProd']);
        return $instance;
    }

    public function form( $instance )
    {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'categories' => '', 'numProd' => 5) );
        $title    = $instance['title'];
		$categories = $instance['categories'];
		$numProd  = $instance['numProd'];
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:'); ?></label><a href="#" class="prosper_tooltip"><span>Filters the top products by category. Comma seperated list.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" type="text" value="<?php echo esc_attr($categories); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('numProd'); ?>"><?php _e('Number of Products to Show:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('numProd'); ?>" name="<?php echo $this->get_field_name('numProd'); ?>" type="text" value="<?php echo esc_attr($numProd); ?>" /></p>
        <?php
    }
}
