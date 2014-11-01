<?php
class ProsperStoreWidget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'prosperent_store', // Base ID
            'ProsperShop Search Bar', // Name
            array('classname' => 'prosperent_store_widget', 'description' => __( "Displays the ProsperShop Search Bar") ) // Args
        );
    }

    public function widget( $args, $instance )
    {
        $options = get_option('prosper_advanced');

        extract($args);
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;

        $base = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products';
        $url = home_url('/') . $base;

        if (isset($_POST['q']))
        {
			$recentOptions = get_option('prosper_productSearch');
			$recentOptions['recentSearches'][] = $_POST['q'];
			if (count($recentOptions['recentSearches']) > $recentOptions['numRecentSearch'])
			{
				$remove = array_shift($recentOptions['recentSearches']);
			}
			
			update_option('prosper_productSearch', $recentOptions);				
		
			$newQuery = str_replace(array('/query/' . $query, '/query/' . urlencode($query)), array('', ''), $url);
            header('Location: ' . $newQuery . '/query/' . urlencode(trim($_POST['q'])));
            exit;
        }
		
		$width = preg_replace('/px|em|%/i', '', $instance['width']);
		$width .= $instance['widthStyle'];
				
        ?>
        <form class="searchform" method="POST" action="" rel="nolink">
            <input class="prosper_field" type="text" name="q" id="s" placeholder="<?php echo isset($options['Search_Bar_Text']) ? $options['Search_Bar_Text'] : 'Search Products'; ?>" style="margin:14px 0 0 18px;width:<?php echo $width; ?>;">
            <input class="prosper_submit" id="submit" type="submit" value="Search" style="margin-top: 14px;">
        </form>
        <?php

        echo $after_widget;
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['width'] = (!empty($new_instance['width'])) ? strip_tags($new_instance['width']) : '52';
		$instance['widthStyle'] = (!empty($new_instance['widthStyle'])) ? strip_tags($new_instance['widthStyle']) : '';
        return $instance;
    }

    public function form( $instance )
    {
        $instance   = wp_parse_args( (array) $instance, array( 'title' => '', 'width' => '52', 'widthStyle' => '%') );
        $title 	    = $instance['title'];
		$width      = $instance['width'];
		$widthStyle = $instance['widthStyle'];
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Text Field Width:'); ?></label><a href="#" class="prosper_tooltip"><span>Width of the text field of the search bar. Use the buttons below to choose the best ending, (%, px, or em). If left empty, the default is 52%.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo esc_attr($width); ?>" /></p>
		<div style="text-align:center;">
			<input type="radio" name="<?php echo $this->get_field_name('widthStyle'); ?>" value="%" <?php echo checked( esc_attr($widthStyle), '%', false ); ?> /> <strong>%</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('widthStyle'); ?>" value="px" <?php echo checked( esc_attr($widthStyle), 'px', false ); ?> /> <strong>px</strong>
			<input style="margin-left:8px;" type="radio" name="<?php echo $this->get_field_name('widthStyle'); ?>" value="em" <?php echo checked( esc_attr($widthStyle), 'em', false ); ?> /> <strong>em</strong>
		</div>
		<br>
		<?php
    }
}
