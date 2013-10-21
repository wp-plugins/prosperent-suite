<?php
add_action('widgets_init', create_function('', 'register_widget("Performance_Ad_Widget");'));

class Performance_Ad_Widget extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'performanceAds_sb_widget', 'description' => __("Adds a Performance Ad as a widget. Settings are in the widget."));
        parent::__construct('performance_ad_sb', __('Performance Ads'), $widget_ops);
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

		$fallback = array();
		if ($instance['usetags'])
		{
			$posttags = get_the_tags();
			if ($posttags) 
			{
				foreach($posttags as $tag) 
				{
					$fallback[] = strtolower($tag->name); 
				}
			}
		}

		if($instance['topic'])
		{
			$newFallback = explode(',', $instance['topic']);
			foreach ($newFallback as $fall)
			{
				$fall = strtolower(trim($fall));
				array_push($fallback, $fall);
			}
		}
		
		if($instance['usetitle'])
		{
			array_push($fallback, strtolower(get_the_title()));
		}
		
		if ($options['Remove_Tags'])
		{
			$removeTags = explode(',', $options['Remove_Tags']);			
			$fbacks = array_flip($fallback);

			foreach ($removeTags as $remove)
			{ 
				$remove = strtolower(trim($remove));
				if(isset($fbacks[$remove]))
				{
					unset($fbacks[$remove]);
				}
			}	
			$fallback = array_flip($fbacks);					
		}

        $fallback = implode(",", $fallback);
		$height = $instance['height'] ? ($instance['height'] == 'auto' ? '100%' : preg_replace('/px|em|%/i', '', $instance['height']) . 'px') : 200 . 'px';
		$width = $instance['width'] ? ($instance['width'] == 'auto' ? '100%' : preg_replace('/px|em|%/i', '', $instance['width']) . 'px') : '100%';

        ?>
		<div class="prosperent-pa" style="height: <?php echo $height; ?>; width: <?php echo $width; ?>;" pa_topics="<?php echo $fallback; ?>" ></div>
		<br>
        <?php
    }

    public function update( $new_instance, $old_instance )
    {
        $new_instance = (array) $new_instance;
        $new_instance = wp_parse_args((array) $new_instance, array( 'width' => '', 'height' => '', 'topic' => '', 'usetags' => '', 'usetitle' => ''));
        $instance['width'] = strip_tags($new_instance['width']);
		$instance['height'] = strip_tags($new_instance['height']);
		$instance['topic'] = strip_tags($new_instance['topic']);
		$instance['usetags'] = strip_tags($new_instance['usetags']);
		$instance['usetitle'] = strip_tags($new_instance['usetitle']);
        return $instance;
    }

    public function form( $instance )
    {
        $instance = wp_parse_args( (array) $instance, array( 'height' => 200, 'width' => 'auto', 'topic' => '', 'usetags' => 0, 'usetitle' => 0) );
		$width = $instance['width'];
		$height = $instance['height'];
		$fallback = $instance['topic'];
		$usetags = $instance['usetags'];
		$usetitle = $instance['usetitle'];
			
        ?>		
		<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:'); ?></label><a href="#" class="prosper_tooltip_widget"><img border="0" src="<?php echo PROSPER_URL . '/img/help.png'; ?>"><span>Using <strong>auto</strong> will scale the ad to fit.<br>Minimum = 77</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo esc_attr($width); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:'); ?></label><a href="#" class="prosper_tooltip_widget"><img border="0" src="<?php echo PROSPER_URL . '/img/help.png'; ?>"><span>Using <strong>auto</strong> will scale the ad to fit.<br>Minimum = 54</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo esc_attr($height); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('topic'); ?>"><?php _e('Topic:'); ?></label><a href="#" class="prosper_tooltip_widget"><img border="0" src="<?php echo PROSPER_URL . '/img/help.png'; ?>"><span><strong>Seperate by commas. Max 3 (including title and tags if used).</strong> A topic is either a generic term that summarizes your site or a specific product.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('topic'); ?>" name="<?php echo $this->get_field_name('topic'); ?>" type="text" value="<?php echo esc_attr($fallback); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('usetags'); ?>"><?php _e('Use Tags as Topic:'); ?></label><a href="#" class="prosper_tooltip_widget"><img border="0" src="<?php echo PROSPER_URL . '/img/help.png'; ?>"><span>Will add post tags to the fallback list if there are any.</span></a>
        <input id="<?php echo $this->get_field_id('usetags'); ?>" name="<?php echo $this->get_field_name('usetags'); ?>" type="checkbox" value="1" <?php echo checked( esc_attr($usetags), 1, false ); ?> /></p>
		<p><label for="<?php echo $this->get_field_id('usetitle'); ?>"><?php _e('Use Title as Topic:'); ?></label><a href="#" class="prosper_tooltip_widget"><img border="0" src="<?php echo PROSPER_URL . '/img/help.png'; ?>"><span>Will add page title to the fallback list.</span></a>
        <input id="<?php echo $this->get_field_id('usetitle'); ?>" name="<?php echo $this->get_field_name('usetitle'); ?>" type="checkbox" value="1" <?php echo checked( esc_attr($usetitle), 1, false ); ?> /></p>
        <?php
    }
}
