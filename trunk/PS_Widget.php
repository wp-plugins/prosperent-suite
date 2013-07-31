<?php
add_action('widgets_init', create_function('', 'register_widget("ProsperStore_Widget");'));

class ProsperStore_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'prosperent_store', // Base ID
            'Prosperent Store', // Name
            array('classname' => 'prosperent_store_widget', 'description' => __( "Displays the Prosperent search bar") ) // Args
        );
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

        extract($args);
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;

        $url = 'http://' . $_SERVER['HTTP_HOST'] . (!$options['Base_URL'] ? '/products' : '/' . $options['Base_URL']);
        $prodSubmit = preg_replace('/\/$/', '', $url);
        $newQuery = str_replace(array('/query/' . $query, '/query/' . urlencode($query)), array('', ''), $prodSubmit);

        if ($_POST['q'])
        {
            header('Location: ' . $newQuery . '/query/' . urlencode(trim($_POST['q'])));
        }
        ?>
        <form id="searchform" method="POST" action="">
            <input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>" style="width:60%; padding:4px 4px 7px; margin: 24px 0 0 20px;">
            <input type="submit" value="Search" style="padding:4px 4px 5px 4px;">
        </form>
        <?php

        echo $after_widget;
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
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
