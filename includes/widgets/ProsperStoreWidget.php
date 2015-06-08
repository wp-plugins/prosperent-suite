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
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'Search Products' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;

        $base = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products';
        $url = home_url('/') . $base;

        if (isset($_POST['q']))
        {
			$recentOptions = get_option('prosper_productSearch');
			$recentOptions['recentSearches'][] = $_POST['q'] ? $_POST['q'] : $recentOptions['Starting_Query'];
			if (count($recentOptions['recentSearches']) > $recentOptions['numRecentSearch'])
			{
				$remove = array_shift($recentOptions['recentSearches']);
			}
			
			update_option('prosper_productSearch', $recentOptions);				
		
			$queryString = '';
			if ($query = (trim($_POST['q'] ? $_POST['q'] : $recentOptions['Starting_Query'])))
			{
				$queryString = '/query/' . rawurlencode($query);
			}
		
			$newQuery = str_replace(array('/query/' . $query, '/query/' . urlencode($query)), array('', ''), $url);
            header('Location: ' . $newQuery . $queryString);
            exit;
        }
					
		$searchBarText = '';
		if ($instance['sBarText'])
		{
			$searchBarText = trim($instance['sBarText']);
		}
        ?>
        <div class="prosper_searchform">        
			<form id="prosperSearchForm" class="searchform" method="POST" action="" rel="nolink">
                <div class="prosperSearchInputs">	   
    				<input id="s" class="prosper_field" type="text" name="q" placeholder="<?php echo $searchBarText; ?>">
    				<span class="prosperSearchRight">				    
    				    <button class="prosper_submit submit" id="searchsubmit" type="submit" name="submit">
    				        <i class="fa fa-search"></i>
    				    </button>
    				</span>
			    </div>	
			</form>		
		</div>
        <?php

        echo $after_widget;
    }

    public function update( $new_instance, $old_instance )
    {
		if (is_active_widget(false, false, $this->id_base, true) )
		{
			require_once(PROSPER_MODEL . '/Admin.php');
			$this->adminModel = new Model_Admin();
		
			$this->adminModel->_settingsHistory();					
		}
		
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['sBarText'] = ( ! empty( $new_instance['sBarText'] ) ) ? strip_tags( $new_instance['sBarText'] ) : 'Search Products';
        return $instance;
    }

    public function form( $instance )
    {
        $instance   = wp_parse_args( (array) $instance, array( 'title' => '', 'searchFor' => 'prod', 'sBarText' => 'Search Products', 'width' => '52', 'widthStyle' => '%') );
		$title 	    = $instance['title'];
		$sBarText   = $instance['sBarText'];
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('sBarText'); ?>"><?php _e('Search Bar Placeholder Text:'); ?></label><a href="#" class="prosper_tooltip"><span>Changes the search bar placeholder text. Will default to 'Search' and your choice from above.</span></a>
        <input class="widefat" id="<?php echo $this->get_field_id('sBarText'); ?>" name="<?php echo $this->get_field_name('sBarText'); ?>" type="text" value="<?php echo esc_attr($sBarText); ?>" /></p>
		<br>
		<?php
    }
}
