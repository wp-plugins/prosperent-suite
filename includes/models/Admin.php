<?php
require_once(plugin_dir_path(__FILE__) . '/Base.php');
/**
 * Admin Abstract Model
 *
 * @package Model
 */
class Model_Admin extends Model_Base
{
	/**
	 * @var string $currentOption The option in use for the current admin page.
	 */
	public $currentOption = 'prosperSuite';

	protected $_pages = array(
		'Products' => '[prosper_store][/prosper_store]'
	);	
	
	/**
	 * @var array $adminPages Array of admin pages that the plugin uses.
	 */
	public $adminPages = array('prosper_general', 'prosper_productSearch', 'prosper_autoComparer', 'prosper_autoLinker', 'prosper_advanced', 'prosper_themes');
	
	public $_options;
			
	public function init()
	{		  
	    add_action( 'admin_enqueue_scripts', array($this, 'prefixEnqueueFAwesome' ));
	    add_action('admin_head', array($this, 'adminIntercom'));
	    if ( isset( $_GET['dismissOpenMessage'] ) && wp_verify_nonce( $_GET['nonce'], 'prosperhideOpenMessage' ) && current_user_can( 'manage_options' ) )
	    {
	        $genOptions = get_option('prosperSuite');
	        $genOptions['dismissOpenMessage'] = 1;
	        update_option('prosperSuite', $genOptions);
	    }
	    
		if ( isset( $_GET['add'] ) && wp_verify_nonce( $_GET['nonce'], 'prosper_add_setting' ) && current_user_can( 'manage_options' ) ) 
		{ 
			$this->addLinks();
			wp_redirect( admin_url( 'admin.php?page=prosper_productSearch&settings-updated=true' ) );
		}	
		
		if ( isset( $_GET['delete'] ) && wp_verify_nonce( $_GET['nonce'], 'prosper_delete_setting' ) && current_user_can( 'manage_options' ) ) 
		{
			$this->deleteLinks($_GET['delete']);
			wp_redirect( admin_url( 'admin.php?page=prosper_productSearch' ) );
		}		
		
		if ( isset( $_GET['deleteRecent'] ) && wp_verify_nonce( $_GET['nonce'], 'prosper_delete_recent' ) && current_user_can( 'manage_options' ) ) 
		{
			$this->deleteRecent($_GET['deleteRecent']);
			wp_redirect( admin_url( 'admin.php?page=prosper_productSearch' ) );
		}
		
		if ( isset( $_GET['clearCache'] ) && wp_verify_nonce( $_GET['nonce'], 'prosper_clear_cache' ) && current_user_can( 'manage_options' ) ) 
		{
			require_once(PROSPER_PATH . 'prosperMemcache.php');
			$cache = new Prosper_Cache();
			$cache->clearMemcache();
			
			wp_redirect( admin_url( 'admin.php?page=prosper_general&cacheCleared' ) );
		}
		
		if ( isset( $_GET['cacheCleared'] ))
		{
			echo '<div id="message" style="width:800px;" class="message updated"><p><strong>' . esc_html('Cache Cleared.') . '</strong></p></div>';
		}

        $shopOpts = get_option('prosper_productSearch');
		if (!$shopOpts['refreshFilters'])
		{
		    $shopOpts['ProsperCategories'] = str_replace(',', '|', $shopOpts['ProsperCategories']); 
		    $shopOpts['PositiveMerchant'] = str_replace(',', '|', $shopOpts['PositiveMerchant']);
		    $shopOpts['NegativeMerchant'] = str_replace(',', '|', $shopOpts['NegativeMerchant']);
		    update_option('prosper_productSearch', $shopOpts);
		}
    }		
    
    public function prefixEnqueueFAwesome()
    {
        wp_enqueue_style( 'prefix-font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css', array(), '4.0.3' );
    }    
    
	public function addLinks()
	{
		$options = get_option('prosper_autoLinker');

		$options['LinkAmount'] = intval($options['LinkAmount']) + 1;
		update_option('prosper_autoLinker', $options);

		$options['LTM'][$options['LinkAmount'] - 1] = true;

		update_option('prosper_autoLinker', $options);
	}	
	
	public function deleteRecent($optNum) 
	{	
		$options = get_option('prosper_productSearch');
		$intOptNum = intval($optNum);			
				
		array_splice($options['recentSearches'], $intOptNum, 1);
		update_option('prosper_productSearch', $options);
	}
	
	public function deleteLinks($optNum) 
	{	
		$options = get_option('prosper_autoLinker');
		$intLinks = intval($options['LinkAmount']);
		$intOptNum = intval($optNum);

		$newCase  = array();
		$newLimit = array();
		for ($i = 0; $i < $intLinks; $i++)
		{
			$newLimit[]  = $options['LTM'][$i] ? $options['LTM'][$i] : 0;
			$newCase[] = $options['Case'][$i] ? $options['Case'][$i] : 0;
		}

		$options['LTM'] = $newLimit;
		$options['Case'] = $newCase;	
				
		array_splice($options['Match'], $intOptNum, 1);
		array_splice($options['Query'], $intOptNum, 1);
		array_splice($options['PerPage'], $intOptNum, 1);
		array_splice($options['LTM'], $intOptNum, 1);
		array_splice($options['Case'], $intOptNum, 1);
		
		$options['LinkAmount'] = ($intLinks > 0 ? $intLinks - 1 : 0);

		update_option('prosper_autoLinker', $options);
	}
	
	public function prosperAdminCss()
	{
		wp_register_style( 'prospere_admin_style', PROSPER_URL . 'includes/css/admin.min.css', array(), $this->getVersion() );
        wp_enqueue_style( 'prospere_admin_style');
	}
	
	public function prosperSuiteMCEOpts()
	{
	    $options = get_option('prosperSuite');
	    $contentInsert = get_option('prosper_autoComparer');
	    
	    $contentInsertType = ($contentInsert['prosper_inserter_pages'] && $contentInsert['prosper_inserter_posts'] ? 'all' : ($contentInsert['prosper_inserter_posts'] ? 'post' : ($contentInsert['prosper_inserter_pages'] ? 'page' : '')));
	    
	    $currentScreen = get_current_screen();

	    $enabledOpts = array(
	        'prosperShop'   => $options['PSAct'],
	        'prosperInsert' => $options['PICIAct'],
	        //'autoLinker'    => $options['ALAct'],
	        'currentScreen' => $currentScreen->id,
	        'contentInsert' => $contentInsertType,
	        'apiKey'        => $options['Api_Key']
	    ); 
	    
	    echo '<script type="text/javascript">var prosperSuiteVars = ' . json_encode($enabledOpts) . '</script>';
	}
		
	/**
	 * Add a link to the settings page to the plugins list
	 *
	 * @staticvar string $this_plugin holds the directory & filename for the plugin
	 * @param array  $links array of links for the plugins, adapted when the current plugin is found.
	 * @param string $file  the filename for the current plugin, which the filter loops through.
	 * @return array $links
	 */
	public function addActionLink( $links, $file ) 
	{
		static $this_plugin;

		if ( empty( $this_plugin ) ) 
			$this_plugin = 'prosperent-suite/prosperent-suite.php';

		if ( $file == $this_plugin ) 
		{
			$settings_link = '<a href="' . admin_url( 'admin.php?page=prosper_general' ) . '">' . __( 'Settings', 'prosperent_suite' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
		
	/**
	 * Register all the options needed for the configuration pages.
	 */
	public function optionsInit() 
	{
		register_setting( 'prosperent_options', 'prosperSuite' );
		register_setting( 'prosperent_prosper_links_options', 'prosper_prosperLinks' );
		register_setting( 'prosperent_products_options', 'prosper_productSearch' );
		register_setting( 'prosperent_compare_options', 'prosper_autoComparer' );	
		register_setting( 'prosperent_advanced_options', 'prosper_advanced' );
		
		if ( function_exists( 'is_multisite' ) && is_multisite() ) 
		{
			if ( get_option( 'prosperSuite' ) == '1pseo_social' )
			{
				delete_option( 'prosperSuite' );
			}
			register_setting( 'prosperent_multisite_options', 'prosper_multisite' );
		}
	}
	
	public function _settingsHistory($status = 'activated')
	{				
		if (empty($this->_options))
		{
			$options = $this->getOptions();
		}
		else
		{
			$options = $this->_options;
		}	
	
		$pluginInfo = get_plugin_data(PROSPER_PATH . PROSPER_FILE);		
		
		$allVars = array(
			'apiKey' 			  => $options['Api_Key'],
		    'accessKey' 		  => $options['prosperAccess'],
			'httpHost' 			  => $_SERVER['HTTP_HOST'],
			'phpVersion' 		  => phpversion(),
			'wordpressVersion' 	  => get_bloginfo('version'),
			'status' 			  => $status,
			'pluginVersion' 	  => $pluginInfo['Version'],
			'privateNetwork'	  => file_exists(WP_CONTENT_DIR . '/prosperentPrivateNetwork.php') ? 1 : 0,
			'caching' 			  => $options['Enable_Caching'] ? 1 : 0,
			'prosperShop'		  => $options['PSAct'] ? 1 : 0,
			'facets' 			  => $options['Enable_Facets'] ? 1 : 0,
		    'categories'     	  => $options['ProsperCategories'] ? $options['ProsperCategories'] : null,
		    'negativeMerchants'	  => $options['NegativeMerchant'] ? $options['NegativeMerchant'] : null,
		    'positiveMerchants'	  => $options['PositiveMerchant'] ? $options['PositiveMerchant'] : null,
			'prosperInsert' 	  => $options['PICIAct'] ? 1 : 0,
			'contentInsert' 	  => ($options['prosper_inserter_posts'] || $options['prosper_inserter_pages']) ? 1 : 0,
			'linkerAmount'		  => $options['LinkAmount'],
			'prosperLinks' 	      => $options['PLAct'] ? 1 : 0,
			'linkOptimizer' 	  => $options['PL_LinkOpt'] ? 1 : 0,
			'relevancyThreshold'  => $options['relThresh'],
			'baseUrl' 			  => $options['Manual_Base'] ? 1 : 0,
			'baseUrlText' 		  => $options['Base_URL'],
			'theme' 			  => $options['Set_Theme'],
			'shortCodes'  		  => $options['shortCodesAccessed'] ? 1 : 0,
			'trendsWidget'		  => is_active_widget(false, false, 'prosper_top_products', true) ? 1 : 0,
		    'prosperInsertWidget' => is_active_widget(false, false, 'prosperproductinsert', true) ? 1 : 0,
			'searchWidget'		  => is_active_widget(false, false, 'prosperent_store', true) ? 1 : 0,
			'recentWidget'		  => is_active_widget(false, false, 'prosper_recent_searches', true) ? 1 : 0
		);
		
		$allVars['settingsHash'] = md5(implode(',', $allVars));

		$url = 'http://prosperent.com/morse/wpsettings';
		$vars = http_build_query($allVars);	

		$curl = curl_init();
		// Set options
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $vars,
			CURLOPT_HEADER => 0
		));

		$response = curl_exec( $curl );

		// Close request
		curl_close($curl);

		// Check for errors
		if (count($response['errors']))
		{
			return array();
		}
	}	
	
	function multisiteDefaults() 
	{
		$option = get_option( 'prosperSuite' );
		if ( function_exists( 'is_multisite' ) && is_multisite() && !is_array( $option ) ) 
		{
			$options = get_site_option( 'prosper_multi' );
			if ( is_array( $options ) && isset( $options['defaultblog'] ) && !empty( $options['defaultblog'] ) && $options['defaultblog'] != 0 ) 
			{
				foreach ( getProsperOptionsArray() as $prosper_option ) 
				{
					update_option( $prosper_option, get_blog_option( $options['defaultblog'], $prosper_option ) );
				}
			}
			$option['ms_defaults_set'] = true;
			update_option( 'prosperSuite', $option );
		}
	}

	/**
	 * Check whether the current user is allowed to access the configuration.
	 *
	 * @return boolean
	 */
	function grantAccess() 
	{
		if ( !function_exists( 'is_multisite' ) || !is_multisite() )
			return true;

		$options = get_site_option( 'prosper_multi' );
		if ( !is_array( $options ) || !isset( $options['access'] ) )
			return true;

		if ( $options['access'] == 'superadmin' && !is_super_admin() )
			return false;

		return true;
	}
	
	/**
	 * Create a Checkbox input field.
	 *
	 * @param string $var        The variable within the option to create the checkbox for.
	 * @param string $label      The label to show for the variable.
	 * @param bool   $label_left Whether the label should be left (true) or right (false).
	 * @param string $option     The option the variable belongs to.
	 * @param string $tooltip The tooltip for the option
	 * @param string $class   The class of the object.
	 * @return string
	 */
	public function activatedLights( $var, $label, $option = '', $tooltip = '', $class = 'prosper_checkbox')
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		if ( !isset( $options[$var] ) )
			$options[$var] = false;
		
		if ( $options[$var] === true )
			$options[$var] = 1;

	    if ( !empty( $label ) )
	        $label .= ':';
	    $output_label = '<span title="' . $tooltip . '"><label class="' . $class . '" for="' . esc_attr( $var ) . '">' . $label . '</label></span>';
	    $class        = $class;
	    
	    $output_input = '<input style="float:none!important;margin:0 0 0 8px!important;" class="' . $class . '" type="checkbox" value="1" id="' . esc_attr( $var ) . '" name="' . esc_attr( $option ) . '[' . esc_attr( $var ) . ']" '  . checked( $options[$var], 1, false ) . '/>';

		$output = $output_label . $output_input;

	    return $output . '<br class="clear" />';
	}	
	
	/**
	 * Create a Checkbox input field.
	 *
	 * @param string $var        The variable within the option to create the checkbox for.
	 * @param string $label      The label to show for the variable.
	 * @param bool   $label_left Whether the label should be left (true) or right (false).
	 * @param string $option     The option the variable belongs to.
	 * @param string $tooltip The tooltip for the option
	 * @param string $class   The class of the object.
	 * @return string
	 */
	public function checkbox( $var, $label, $label_left = false, $option = '', $tooltip = '', $class = 'prosper_checkbox') 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		if ( !isset( $options[$var] ) )
			$options[$var] = false;
		
		if ( $options[$var] === true )
			$options[$var] = 1;

		if ( $label_left !== false ) 
		{
		    if ( !empty( $label ) )
		        $label .= ':';
		    $output_label = '<span title="' . $tooltip . '"><label class="' . $class . '" for="' . esc_attr( $var ) . '">' . $label . '</label></span>';
		    $class        = $class;
		    
		    $output_input = '<input style="float:none!important;margin:0 0 0 8px!important;" class="' . $class . '" type="checkbox" value="1" id="' . esc_attr( $var ) . '" name="' . esc_attr( $option ) . '[' . esc_attr( $var ) . ']" '  . checked( $options[$var], 1, false ) . '/>';

			$output = $output_label . $output_input;
		} 
		else 
		{
		    $output_label = '<span title="' . $tooltip . '"><label class="' . $class . '" for="' . esc_attr( $var ) . '">' . $label . '</label></span>';
		    $class        = $class . ' double';
		    
		    $output_input = '<input class="' . $class . '" type="checkbox" value="1" id="' . esc_attr( $var ) . '" name="' . esc_attr( $option ) . '[' . esc_attr( $var ) . ']" '  . checked( $options[$var], 1, false ) . '/>';
		    
			$output = $output_input . $output_label;
		}

		return $output . '<br class="clear" />';
	}

	/**
	 * Create a Inline Checkbox input field.
	 *
	 * @param string $var        The variable within the option to create the checkbox for.
	 * @param string $label      The label to show for the variable.
	 * @param bool   $label_left Whether the label should be left (true) or right (false).
	 * @param string $option     The option the variable belongs to.
	 * @param string $tooltip The tooltip for the option
	 * @return string
	 */
	public function checkboxinline( $var, $label, $label_left = false, $arrayNum, $option = '') 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;
		
		$options = get_option( $option );
		
		if ( !isset( $options[$var][$arrayNum] ) )
			$options[$var][$arrayNum] = false;
			
		if ( $options[$var][$arrayNum] === true )
			$options[$var][$arrayNum] = 1;
			
		if ( $label_left !== false ) 
		{
			if ( !empty( $label_left ) )
				$label_left .= ':';
			$output_label = '<label class="prosper_checkboxinline" for="' . esc_attr( $var ) . '[' . $arrayNum . ']">' . $label . ':</label>';
			$class        = 'prosper_checkboxinline';
		} 
		else 
		{
			$output_label = '<label class="prosper_checkboxinline" for="' . esc_attr( $var ) . '[' . $arrayNum . ']">' . $label . '</label>';
			$class        = 'prosper_checkboxinline double';
		}

		$output_input = "<input class='$class' type='checkbox' value='1' id='" . esc_attr( $var ) . "' name='" . esc_attr( $option) . "[" . esc_attr( $var ) . "][" . $arrayNum . "]' " . checked( $options[$var][$arrayNum], 1, false ) . "/>";

		if ( $label_left !== false ) {
			$output = $output_label . $output_input;
		} else 
		{
			$output = $output_input . $output_label;
		}

		return $output;
	}
		
	/**
	 * Create a Text input field.
	 *
	 * @param string $var    The variable within the option to create the text input field for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 * @param string $tooltip The tooltip for the option
	 * @param string $class   The class of the object.
	 * @return string
	 */
	public function textinput( $var, $label, $option = '', $tooltip = '', $class = 'prosper_textinput') 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		$val = '';
		if ( isset( $options[$var] ) )
			$val = esc_attr( $options[$var] );

		return '<span title="' . $tooltip . '"><label class="' . $class . '" for="' . esc_attr( $var ) . '">' . $label . ':</label></span><input class="' . $class . '" type="text" id="' . esc_attr( $var ) . '" name="' . $option . '[' . esc_attr( $var ) . ']" value="' . $val . '"/>'. '<br class="clear" />';
	}

	/**
	 * Create a Text input field.
	 *
	 * @param string $var    The variable within the option to create the text input field for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.	 
	 * @param string $tooltip The tooltip for the option
	 * @return string
	 */
	public function textinputnewinline( $var, $arrayNum, $option = '', $tooltip = '' ) 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		$val = '';
		if ( isset( $options[$var][$arrayNum] ) )
			$val = esc_attr( $options[$var][$arrayNum] );
			
		return '<input class="prosper_textinput" style="width:auto;margin:2px;" type="text" id="' . esc_attr( $var ) . '" name="' . $option . '[' . $var . '][' . $arrayNum . ']" value="' . $val . '"/>' . $tooltip;	
	}
	
	/**
	 * Create a Text input field.
	 *
	 * @param string $var    The variable within the option to create the text input field for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.	 
	 * @param string $tooltip The tooltip for the option
	 * @return string
	 */
	public function textinputinline( $var, $label, $arrayNum, $option = '', $tooltip = '', $class = 'prosper_textinputinline' ) 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		$val = '';
		if ( isset( $options[$var][$arrayNum] ) )
			$val = esc_attr( $options[$var][$arrayNum] );
			
		return '<span title="' . $tooltip . '"><label class="' . $class . '" for="' . esc_attr( $var ) . '">' . $label . ':</label></span><input class="' . $class . '" type="text" id="' . esc_attr( $var ) . '" name="' . $option . '[' . $var . '][' . $arrayNum . ']" value="' . $val . '"/>';	
	}
	
	/**
	 * Create a hidden input field.
	 *
	 * @param string $var    The variable within the option to create the hidden input for.
	 * @param string $option The option the variable belongs to.
	 * @return string
	 */
	public function hidden( $var, $option = '' ) 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		$val = '';
		if ( isset( $options[$var] ) )
			$val = esc_attr( $options[$var] );

		return '<input type="hidden" id="hidden_' . esc_attr( $var ) . '" name="' . $option . '[' . esc_attr( $var ) . ']" value="' . $val . '"/>';
	}

	/**
	 * Create a Select Box.
	 *
	 * @param string $var     The variable within the option to create the select for.
	 * @param string $label   The label to show for the variable.
	 * @param array  $values  The select options to choose from.
	 * @param string $option  The option the variable belongs to.
	 * @param string $tooltip The tooltip for the option
	 * @param string $class   The class of the object.
	 * @return string
	 */
	public function select( $var, $label, $values, $option = '', $tooltip = '', $class = 'prosper_select' ) 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		$var_esc = esc_attr( $var );
		$output  = '<span title="' . $tooltip . '"><label class="' . $class . '" for="' . $var_esc . '">' . $label . ':</label></span>';
		$output .= '<select class="' . $class . '" name="' . $option . '[' . $var_esc . ']" id="' . $var_esc . '">';

		foreach ( $values as $value => $label ) {
			$sel = '';
			if ( isset( $options[$var] ) && $options[$var] == $value )
				$sel = 'selected="selected" ';

			if ( !empty( $label ) )
				$output .= '<option ' . $sel . 'value="' . esc_attr( $value ) . '">' . $label . '</option>';
		}
		$output .= '</select>';
		return $output . '<br class="clear"/>';
	}
	
	/**
	 * Create a Radio input field.
	 *
	 * @param string $var    The variable within the option to create the file upload field for.
	 * @param array  $values The radio options to choose from.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 * @return string
	 */
	public function radio( $var, $values, $label, $option = '', $tooltip ) 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		if ( !isset( $options[$var] ) )
			$options[$var] = false;

		$var_esc = esc_attr( $var );

		$output = '<span title="' . $tooltip . '"><label class="prosper_radio">' . $label . ':</label></span><span>';
		if (empty($label))
		{
			$output = '<label class="prosper_radio"></label>';
		}
		
		foreach ( $values as $key => $value ) {
			$key = esc_attr( $key );
			$output .= '<input type="radio" class="prosper_radio" id="' . $var_esc . '-' . $key . '" name="' . esc_attr( $option ) . '[' . $var_esc . ']" value="' . $key . '" ' . ( $options[$var] == $key ? ' checked="checked"' : '' ) . ' /> <label class="prosper_radiofor" for="' . $var_esc . '-' . $key . '">' . esc_attr( $value ) . '</label>';
		}
		$output .= '</span>';

		return $output;
	}

	/**
	 * Create a MultiCheckbox input field.
	 *
	 * @param string $var    The variable within the option to create the file upload field for.
	 * @param array  $values The checkbox options to choose from.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 * @param string $tooltip The tooltip for the option.
	 * @return string
	 */
	public function multiCheckbox( $var, $values, $label, $option = '' , $tooltip = '') 
	{
		if ( empty( $option ) )
			$option = $this->currentOption;

		$options = get_option( $option );

		if ( !isset( $options[$var] ) )
			$options[$var] = false;

		$var_esc = esc_attr( $var );

		$output = '<div style="width:100%"><ul style="list-style:none;margin:6px 0 5px 20px"><span title="' . $tooltip . '"><label class="prosper_radio">' . $label . ':</label></span><br><br><span style="margin-left:40px;">';
		if (empty($label))
		{
			$output = '<div style="width:100%"><ul style="list-style:none;margin:6px 0 5px 20px">';
		}

		$i = 0;
		
		foreach ( $values as $key => $value ) {

		    if (fmod($i, 5) == 0 )
		    {
		        $output .= '<br>';
		    }
			$key = esc_attr( $key );
			$output .= '<li style="display:inline-block;width:150px;"><input type="checkbox" class="prosper_radio" id="' . $var_esc . '-' . $key . '" name="' . esc_attr( $option ) . '[' . $var_esc . ']['.$key.']" value="' . $key . '" ' . ( $options[$var][$key] == $key ? ' checked="checked"' : '' ) . ' /> <label class="prosper_radiofor" for="' . $var_esc . '-' . $key . '">' . esc_attr( $value ) . '</label></li>';
			$i++;
		}
		
		$output .= '</span></ul></div>';

		return $output;
	}
	
	/**
     * Create a postbox widget.
	 *
	 * @param string $id      ID of the postbox.
	 * @param string $title   Title of the postbox.
	 * @param string $content Content of the postbox.
	 */
	public function postbox( $id, $title, $content ) 
	{
		?>
		<div id="<?php echo esc_attr( $id ); ?>" class="prosperbox">
			<h2><?php echo $title; ?></h2>
			<?php echo $content; ?>
		</div>
	<?php
	}


	/**
	 * Create a form table from an array of rows.
	 *
	 * @param array $rows Rows to include in the table.
	 * @return string
	 */
	public function form_table( $rows ) 
	{
		$content = '<table class="form-table">';
		foreach ( $rows as $row ) {
			$content .= '<tr><th valign="top" scrope="row">';
			if ( isset( $row['id'] ) && $row['id'] != '' )
				$content .= '<label for="' . esc_attr( $row['id'] ) . '">' . esc_html( $row['label'] ) . ':</label>';
			else
				$content .= esc_html( $row['label'] );
			if ( isset( $row['desc'] ) && $row['desc'] != '' )
				$content .= '<br/><small>' . esc_html( $row['desc'] ) . '</small>';
			$content .= '</th><td valign="top">';
			$content .= $row['content'];
			$content .= '</td></tr>';
		}
		$content .= '</table>';
		return $content;
	}
	
	public function adminIntercom ()
	{

	 }
	
	/**
	 * Generates the header for admin pages
	 *
	 * @param string $title          The title to show in the main heading.
	 * @param bool   $form           Whether or not the form should be included.
	 * @param string $option         The long name of the option to use for the current page.
	 * @param string $optionshort    The short name of the option to use for the current page.
	 * @param bool   $contains_files Whether the form should allow for file uploads.
	 */
	public function adminHeader( $title, $form = true, $option = 'prosperent_options', $optionshort = 'prosperSuite', $contains_files = false ) 
	{	    
	    global $current_user;
	    get_currentuserinfo();
	    $options = $this->getOptions();
        ?>
		<div class="wrap">
		<?php 
		if ('General Settings' == $title) : ?>
			<table><tr>
			<td><img style="max-width:200px;display:block;" src="<?php echo PROSPER_IMG . '/adminImg/prosperent-logo-black.png'; ?>"/></td>
			</tr><tr>
			<td><h1 style="font-size:23px;max-width:876px;font-weight:300;padding:0 15px 4px 0;margin-top:15px;line-height:29px;">Make money from ordinary links on your blog, add a shop, and insert products into your posts.</h1></td></tr><div style="clear:both"></div>
			</table>		
            <h2 style="display:inline;margin:0;padding:0;float:left;">&nbsp;</h2>
			
		<?php elseif ('Advanced Settings' == $title || 'ProsperThemes' == $title || 'MultiSite Settings' == $title ): ?>
			<table><tr><td><img src="<?php echo PROSPER_IMG . '/Gears-32.png'; ?>"/></td><?php echo '<td><h1 style="margin-left:8px;display:inline-block;font-size:34px;">' . $title . '</h1></td></tr></table><div style="clear:both"></div><h2 style="display:inline;margin:0;padding:0">&nbsp;</h2>';
		 else :?>
			<table><tr><td><img src="<?php echo PROSPER_IMG . '/adminImg/' . $title . '.png'; ?>"/></td><?php echo '<td><h1 style="margin-left:8px;display:inline-block;font-size:34px;">' . $title . '</h1></td></tr></table><div style="clear:both"></div><h2 style="display:inline;margin:0;padding:0">&nbsp;</h2>';
		endif; ?>
		
		<div id="prosper_content_top" class="postbox-container" style="min-width:400px; width:900px; max-width:950px;">
		<div class="metabox-holder">
		<div class="meta-box-sortables">
		<?php		
		if ( ( isset( $_GET['updated'] ) && $_GET['updated'] == 'true' ) || ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == 'true' ) ) {
			$msg = __( 'Settings updated', 'prosperent-suite' );

			echo '<div id="message" style="max-width:900px;" class="message updated"><p><strong>' . esc_html( $msg ) . '.</strong></p></div>';
			
			if ($options['anonymousData'])
			{
				$this->_settingsHistory('activated');
			}
		}

		if ($options['Enable_Caching'] && !extension_loaded('memcache'))
		{
			echo '<div style="max-width:900px;margin-bottom:12px;" class="update-nag">';
			echo '<span style="font-size:14px; padding-left:10px;">The <strong>memcached library</strong> is needed in order to use caching. </span></br>';
			echo '<span style="font-size:14px; padding-left:10px;">Caching will be <strong>skipped</strong> until it is installed.</span></br>'; 
			echo '</div>';
			
		}
		
		if ( $form ) 
		{
			echo '<form action="' . admin_url( 'options.php' ) . '" method="post" id="prosper-conf"' . ( $contains_files ? ' enctype="multipart/form-data"' : '' ) . '>';
			settings_fields( $option );
			$this->currentOption = $optionshort;
		}
	}
	
	/**
	 * Generates the footer for admin pages
	 *
	 * @param bool $submit Whether or not a submit button should be shown.
	 */
	public static function adminFooter( $submit = true ) 
	{
		if ( $submit ) 
		{
			?>
			<div class="submit"><input type="submit" class="button-primary prosperSaveSettings" name="submit" value="<?php _e( "Save Settings", 'prosperent-suite' ); ?>"/></div>
			<?php 
		} 
		?>
		</form>
		</div>
		</div>
		</div>
		</div>
	<?php
	}
}