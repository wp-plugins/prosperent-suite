<?php
require_once('prosperent_suite.php');

add_action('widgets_init', create_function('', 'register_widget("ProsperStore_Widget");'));

class ProsperStore_Widget extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'prosperent_store_widget', 'description' => __( "Displays the Prosperent search bar") );
        parent::__construct('prosperent_store', __('Prosperent Store'), $widget_ops);
    }
	
	public function options()
	{
	    global $wpdb;
        $wpdb->hide_errors();
        $myrows = $wpdb->get_row("SELECT *
                    FROM $wpdb->options
                    WHERE option_name = 'prosper_prosperent_suite'", ARRAY_A);

        $options = unserialize($myrows['option_value']);
		return $options;
	}
	
    public function widget( $args, $instance )
    {
		$options = $this->options();
		
        extract($args);
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;
        ?>
        <form id="searchform" method="GET" action="<?php echo !$options['Base_URL'] ?  (!$options['Parent_Directory'] ? '/products' : '/' . $options['Parent_Directory'] . '/products') : '/' . $options['Base_URL']; ?>">
            <input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>" style="width:68%;">
            <input class="submit" type="submit" id="searchsubmit" value="Search">
        </form>
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
