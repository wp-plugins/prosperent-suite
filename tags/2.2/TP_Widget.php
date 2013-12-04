<?php
add_action('widgets_init', create_function('', 'register_widget("TopProducts_Widget");'));

class TopProducts_Widget extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'top_products_widget', 'description' => __( "Displays the top Products of Prosperent at the time"));
        parent::__construct('prosper_top_products', __('Top Products'), $widget_ops);
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
            // calculate date range
            $prevNumDays = 30;
            $startRange = date('Ymd', time() - 86400 * $prevNumDays);
            $endRange   = date('Ymd');

            // fetch trends from api
            require_once(PROSPER_PATH . 'Prosperent_Api.php');
            $api = new Prosperent_Api(array(
                'enableFacets'  => 'productId',
                'filterCatalog' => $options['Country']
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

            switch ($options['Country'])
            {
                case 'UK':
                    $api -> fetchUkProducts();
                    break;
                case 'CA':
                    $api -> fetchCaProducts();
                    break;
                default:
                    $api -> fetchProducts();
                    break;
            }
            ?>
            <table>
            <?php
            foreach ($api->getAllData() as $record)
            {
                echo '<tr><td>&bull;&nbsp;</td><td style="padding-bottom:4px; font-size:13px;"><a href="' . home_url() . '/product/' . urlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['catalogId'] . '">' . $record['keyword'] . '</a></td></tr>';
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
