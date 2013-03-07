<?php
add_action('widgets_init', create_function('', 'register_widget("TopProducts_Widget");'));

class TopProducts_Widget extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'top_products_widget', 'description' => __( "Displays the top Products of Prosperent at the time"));
        parent::__construct('prosper_top_products', __('Top Products'), $widget_ops);
    }
	
	public function options()
	{	
		$optValues = get_option('prosper_prosperent_suite');
		return $optValues;
	}
	
    public function widget( $args, $instance )
    {
		$options = $this->options();
		
        extract($args);
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;
			// calculate date range
			$prevNumDays = 30;
			$startRange = date('Ymd', time() - 86400 * $prevNumDays);
			$endRange   = date('Ymd');

			// fetch trends from api
			require_once('Prosperent_Api.php');
			$api = new Prosperent_Api(array(
				'enableFacets' => 'productId'
			));

			$api->setDateRange('commission', $startRange, $endRange)
				->fetchTrends();

			// set productId as key in array
			foreach ($api->getFacets('productId') as $data)
			{
				$keys[] = $data['value'];
			}

			// fetch merchant data from api
			$api = new Prosperent_Api(array(
				'api_key'         => $options['Api_Key'],
				'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
				'filterProductId' => $keys,
				'limit' 	      => 5
			));

			$api->fetch();

			?>
			<table>
			<?php
			foreach ($api->getAllData() as $record)
			{
				echo '<tr><td>&bull;&nbsp;</td><td style="padding-bottom:4px; font-size:13px;"><a href="' . (!$options['Base_URL'] ?  '/products' : '/' . $options['Base_URL']) . '?q=' . urlencode($record['keyword']) . '">' . $record['keyword'] . '</a></td></tr>';
			}
			?>
			</table>
			<?php
			
        echo $after_widget;
    }

    public function update( $new_instance, $old_instance )
    {
        $new_instance = (array) $new_instance;
        $new_instance = wp_parse_args((array) $new_instance, array( 'title' => ''));
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    public function form( $instance )
    {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        $title = $instance['title'];
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <?php
    }
}
