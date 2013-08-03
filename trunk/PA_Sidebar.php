<?php
add_action('widgets_init', create_function('', 'register_widget("Performance_Ad_Sidebar_Widget");'));

class Performance_Ad_Sidebar_Widget extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'performanceAds_sb_widget', 'description' => __("Adds a Performance Ad in the sidebar."));
        parent::__construct('performance_ad_sb', __('Performance Ads (sidebar)'), $widget_ops);
    }

    public function get_prosper_options_array()
    {
        $optarr = array( 'prosperSuite', 'prosper_productSearch', 'prosper_performAds', 'prosper_autoComparer', 'prosper_autoLinker', 'prosper_prosperLinks', 'prosper_advanced' );

        return apply_filters( 'prosper_options', $optarr );
    }

    public function options()
    {
        static $options;

        if (!isset($options))
        {
            $options = array();
            foreach ($this->get_prosper_options_array() as $opt)
            {
                $options = array_merge($options, (array) get_option($opt));
            }
        }
        return $options;
    }

    public function widget( $args, $instance )
    {
        $options = $this->options();

        $posttags = get_the_tags();
        $count=0;
        if ($posttags)
        {
            foreach($posttags as $tag)
            {
                $count++;
                if (1 == $count)
                {
                    $tag = $tag->name;
                }
            }
        }

        $fallback = isset($tag) ? $tag : $options['sidebar_fallBack'] ? $options['sidebar_fallBack'] : '';

        extract($args);
        $title = apply_filters('widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);

        /*echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;*/
        ?>
        <script data-cfasync="false">
            <!--
            prosperent_pa_uid = <?php echo json_encode($options['UID']); ?>;
            prosperent_pa_width = <?php echo json_encode($options['SWW'] == 'auto' ? '' : $options['SWW']); ?>;
            prosperent_pa_height = <?php echo json_encode($options['SWH']); ?>;
            prosperent_pa_fallback_query = <?php echo json_encode($fallback); ?>;
            //-->
        </script>
        <script data-cfasync="false" src="http://prosperent.com/js/ad.js"></script>
        <br>
        <?php
        //echo $after_widget;
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
