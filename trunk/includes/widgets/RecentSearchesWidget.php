<?php
class RecentSearchesWidget extends WP_Widget
{
    public function __construct()
    {
	    parent::__construct(
			'prosper_recent_searches', 
			'Recent Searches', 
			array('classname' => 'recent_searches_widget', 'description' => __( "Displays the most recent searches of the ProsperShop."))
		);
    }

    public function widget( $args, $instance )
    {
        $options = get_option('prosper_productSearch');
		$advancedOptions = get_option('prosper_advanced');

        extract($args);
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;
        if ( $title)
            echo $before_title . $title . $after_title;			

		if ($options['numRecentSearch'] != $instance['numRecentSearch'])
		{
			$opts = array_merge($options, array('numRecentSearch' => $instance['numRecentSearch']));
			update_option('prosper_productSearch', $opts);
		}
            
		?>
		<table>
			<?php
			$base = $advancedOptions['Base_URL'] ? $advancedOptions['Base_URL'] : 'products';
			$url = home_url('/') . $base  . '/query/';
			if ($options['recentSearches'])
			{
				foreach ($options['recentSearches'] as $query)
				{
					echo '<tr><td>&bull;&nbsp;</td><td style="padding-bottom:4px; font-size:13px;"><a href="' . $url . rawurlencode(str_replace('/', ',SL,', $query)) . '" rel="nolink">' . ucwords(preg_replace('/\(.+\)/i', '', $query)) . '</a></td></tr>';
				}
			}
			else
			{
				echo 'No Recent Searches to Display.';
			}
			?>
		</table>
		<?php

        echo $after_widget;
    }

    public function update( $new_instance, $old_instance )
    {
        $new_instance = (array) $new_instance;
        $new_instance = wp_parse_args((array) $new_instance, array( 'title' => '', 'numRecentSearch' => ''));
        $instance['title'] = strip_tags($new_instance['title']);
		$instance['numRecentSearch'] = strip_tags($new_instance['numRecentSearch']);
        return $instance;
    }

    public function form( $instance )
    {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'numRecentSearch' => 5) );
        $title   = $instance['title'];
		$numRecentSearch = $instance['numRecentSearch'];
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('numRecentSearch'); ?>"><?php _e('Number of Recent Searches to Show:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('numRecentSearch'); ?>" name="<?php echo $this->get_field_name('numRecentSearch'); ?>" type="text" value="<?php echo esc_attr($numRecentSearch); ?>" /></p>
        <?php
    }
}
