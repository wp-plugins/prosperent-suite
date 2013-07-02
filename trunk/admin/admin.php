<?php
class Prosperent_Admin
{
	/**
	 * @var string $currentoption The option in use for the current admin page.
	 */
	var $currentoption = 'prosperSuite';

	/**
	 * @var array $adminpages Array of admin pages that the plugin uses.
	 */
	var $adminpages = array( 'prosper_general', 'prosper_productSearch', 'prosper_performAds', 'prosper_autoComparer', 'prosper_autoLinker', 'prosper_prosperLinks', 'prosper_advanced');


	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		add_action( 'admin_init', array( $this, 'options_init' ) );
		add_action('admin_menu', array($this, 'register_settings_page' ), 5);
		add_action( 'network_admin_menu', array( $this, 'register_network_settings_page' ) );
		add_filter( 'plugin_action_links', array( $this, 'add_action_link' ), 10, 2 );
		wp_enqueue_style( 'prospere_admin_style', PROSPER_URL . 'css/admin.css');
	}

	/**
	 * Register all the options needed for the configuration pages.
	 */
	public function options_init() 
	{
		register_setting( 'prosperent_options', 'prosperSuite' );
		register_setting( 'prosperent_products_options', 'prosper_productSearch' );
		register_setting( 'prosperent_perform_options', 'prosper_performAds' );
		register_setting( 'prosperent_compare_options', 'prosper_autoComparer' );
		register_setting( 'prosperent_linker_options', 'prosper_autoLinker' );
		//register_setting( 'prosperent_prosper_links_options', 'prosper_prosperLinks' );
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

	function multisite_defaults() 
	{
		$option = get_option( 'prosperSuite' );
		if ( function_exists( 'is_multisite' ) && is_multisite() && !is_array( $option ) ) 
		{
			$options = get_site_option( 'prosper_multi' );
			if ( is_array( $options ) && isset( $options['defaultblog'] ) && !empty( $options['defaultblog'] ) && $options['defaultblog'] != 0 ) 
			{
				foreach ( get_prosper_options_array() as $prosper_option ) 
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
	function grant_access() 
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
	 * Register the menu item and its sub menu's.
	 *
	 * @global array $submenu used to change the label on the first item.
	 */
	public function register_settings_page() 
	{
		add_menu_page(__('Prosperent Suite Settings', 'prosperent-suite'), __( 'Prosperent', 'prosperent-suite' ), 'manage_options', 'prosper_general', array( $this, 'generalPage' ), PROSPER_URL . 'img/prosperent.png' );
		add_submenu_page('prosper_general', __('Product Search', 'prosperent-suite' ), __( 'Product Search', 'prosperent-suite' ), 'manage_options', 'prosper_productSearch', array( $this, 'productPage' ) );
		add_submenu_page('prosper_general', __( 'Performance Ads', 'prosperent-suite' ), __( 'Performance Ads', 'prosperent-suite' ), 'manage_options', 'prosper_performAds', array( $this, 'performancePage' ) );
		add_submenu_page('prosper_general', __( 'Auto-Comparer', 'prosperent-suite' ), __( 'Auto-Comparer', 'prosperent-suite' ), 'manage_options', 'prosper_autoComparer', array( $this, 'comparerPage' ) );
		add_submenu_page('prosper_general', __( 'Auto-Linker', 'prosperent-suite' ), __( 'Auto-Linker', 'prosperent-suite' ), 'manage_options', 'prosper_autoLinker', array( $this, 'linkerPage' ) );
		//add_submenu_page('prosper_general', __( 'ProsperLinks', 'prosperent-suite' ), __( 'ProsperLinks', 'prosperent-suite' ), 'manage_options', 'prosper_prosperLinks', array( $this, 'linksPage' ) );
		add_submenu_page('prosper_general', __( 'Advanced Options', 'prosperent-suite' ), __( 'Advanced', 'prosperent-suite' ), 'manage_options', 'prosper_advanced', array( $this, 'advancedPage' ) );
		
		global $submenu;
		if (isset($submenu['prosper_general']))
			$submenu['prosper_general'][0][0] = __('General Settings', 'prosperent-suite' );
		
	}	
		
	/**
	 * Register the settings page for the Network settings.
	 */
	function register_network_settings_page() 
	{
		add_menu_page( __('Prosperent Suite Settings', 'prosperent-suite'), __( 'Prosperent', 'prosperent-suite' ), 'delete_users', 'prosper_general', array( $this, 'network_config_page' ), PROSPER_URL . 'img/prosperent.png' );
	}
		
	/**
	 * Loads the form for the network configuration page.
	 */
	function network_config_page() 
	{
		require( PROSPER_PATH . 'admin/pages/network.php' );
	}
		
	/**
	 * Loads the form for the general settings page.
	 */
	public function generalPage() 
	{
		if ( isset( $_GET['page'] ) && 'prosper_general' == $_GET['page'] )
			include( PROSPER_PATH . 'admin/pages/general.php' );
	}
		
	/**
	 * Loads the form for the product search page.
	 */
	public function productPage() 
	{
		if ( isset( $_GET['page'] ) && 'prosper_productSearch' == $_GET['page'] )
			include( PROSPER_PATH . 'admin/pages/productSearch.php' );
	}	
		
	/**
	 * Loads the form for the performance ads page.
	 */
	public function performancePage() 
	{
		if ( isset( $_GET['page'] ) && 'prosper_performAds' == $_GET['page'] )
			include( PROSPER_PATH . 'admin/pages/performanceAds.php' );
	}	
	
			/**
	 * Loads the form for the auto-comparer page.
	 */
	public function comparerPage() 
	{
		if ( isset( $_GET['page'] ) && 'prosper_autoComparer' == $_GET['page'] )
			include( PROSPER_PATH . 'admin/pages/autoComparer.php' );
	}
		
	/**
	 * Loads the form for the auto-linker page.
	 */
	public function linkerPage() 
	{
		if ( isset( $_GET['page'] ) && 'prosper_autoLinker' == $_GET['page'] )
			include( PROSPER_PATH . 'admin/pages/autoLinker.php' );
	}	
		
	/**
	 * Loads the form for the prosperLinks page.
	 */
	public function linksPage() 
	{
		if ( isset( $_GET['page'] ) && 'prosper_prosperLinks' == $_GET['page'] )
			include( PROSPER_PATH . 'admin/pages/prosperLinks.php' );
	}	
	
	/**
	 * Loads the form for the product search page.
	 */
	public function advancedPage() 
	{
		if ( isset( $_GET['page'] ) && 'prosper_advanced' == $_GET['page'] )
			include( PROSPER_PATH . 'admin/pages/advanced.php' );
	}	
	
	/**
	 * Add a link to the settings page to the plugins list
	 *
	 * @staticvar string $this_plugin holds the directory & filename for the plugin
	 * @param array  $links array of links for the plugins, adapted when the current plugin is found.
	 * @param string $file  the filename for the current plugin, which the filter loops through.
	 * @return array $links
	 */
	public function add_action_link( $links, $file ) 
	{
		static $this_plugin;

		if ( empty( $this_plugin ) ) $this_plugin = 'prosperent-suite/Prosper_Suite.php';

		if ( $file == $this_plugin ) 
		{
			$settings_link = '<a href="' . admin_url( 'admin.php?page=prosperent_dashboard' ) . '">' . __( 'Settings', 'prosperent_suite' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
	
	/**
	 * Retrieve options based on the option or the class currentoption.
	 *
	 * @since 1.2.4
	 *
	 * @param string $option The option to retrieve.
	 * @return array
	 */
	public function get_option( $option ) {
		if ( function_exists( 'is_network_admin' ) && is_network_admin() )
			return get_site_option( $option );
		else
			return get_option( $option );
	}

	/**
	 * Create a Checkbox input field.
	 *
	 * @param string $var        The variable within the option to create the checkbox for.
	 * @param string $label      The label to show for the variable.
	 * @param bool   $label_left Whether the label should be left (true) or right (false).
	 * @param string $option     The option the variable belongs to.
	 * @return string
	 */
	public function checkbox( $var, $label, $label_left = false, $option = '') 
	{
		if ( empty( $option ) )
			$option = $this->currentoption;

		$options = $this->get_option( $option );

		if ( !isset( $options[$var] ) )
			$options[$var] = false;
		
		if ( $options[$var] === true )
			$options[$var] = 1;

		if ( $label_left !== false ) {
			if ( !empty( $label_left ) )
				$label_left .= ':';
			$output_label = '<label class="checkbox" for="' . esc_attr( $var ) . '">' . $label_left . '</label>';
			$class        = 'checkbox';
		} else {
			$output_label = '<label for="' . esc_attr( $var ) . '">' . $label . '</label>';
			$class        = 'checkbox double';
		}

		$output_input = "<input class='$class' type='checkbox' value='1' id='" . esc_attr( $var ) . "' name='" . esc_attr( $option ) . "[" . esc_attr( $var ) . "]' " . checked( $options[$var], 1, false ) . '/>';

		if ( $label_left !== false ) {
			$output = $output_label . $output_input . '<label class="checkbox" for="' . esc_attr( $var ) . '">' . $label . '</label>';
		} else {
			$output = $output_input . $output_label;
		}

		return $output . '<br class="clear" />';
	}

		/**
	 * Create a Checkbox input field.
	 *
	 * @param string $var        The variable within the option to create the checkbox for.
	 * @param string $label      The label to show for the variable.
	 * @param bool   $label_left Whether the label should be left (true) or right (false).
	 * @param string $option     The option the variable belongs to.
	 * @return string
	 */
	public function geoCheckbox( $var, $label, $label_left = false, $option = '', $tooltip = '' ) 
	{
		if ( empty( $option ) )
			$option = $this->currentoption;

		$options = $this->get_option( $option );

		if ( !isset( $options[$var] ) )
			$options[$var] = false;
		
		if ( $options[$var] === true )
			$options[$var] = 1;

		if ( $label_left !== false ) {
			if ( !empty( $label_left ) )
				$label_left .= ':';
			$output_label = '<label class="geocheckbox" for="' . esc_attr( $var ) . '">' . $label_left . '</label>';
			$class        = 'geocheckbox';
		} else {
			$output_label = '<label for="' . esc_attr( $var ) . '">' . $label . '</label>';
			$class        = 'geocheckbox double';
		}

		$output_input = "<input class='$class' type='checkbox' value='1' id='" . esc_attr( $var ) . "' name='" . esc_attr( $option ) . "[" . esc_attr( $var ) . "]' " . checked( $options[$var], 1, false ) . '/>';

		if ( $label_left !== false ) {
			$output = $output_label . $output_input . '<label class="geocheckbox" for="' . esc_attr( $var ) . '">' . $label . '</label>' . $tooltip;
		} else {
			$output = $output_input . $output_label;
		}

		return $output . '<br class="clear" />';
	}
	
	/**
	 * Create a Text input field.
	 *
	 * @param string $var    The variable within the option to create the text input field for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 * @return string
	 */
	public function textinput( $var, $label, $option = '', $tooltip = '' ) 
	{
		if ( empty( $option ) )
			$option = $this->currentoption;

		$options = $this->get_option( $option );

		$val = '';
		if ( isset( $options[$var] ) )
			$val = esc_attr( $options[$var] );

		return '<label class="textinput" for="' . esc_attr( $var ) . '">' . $label . ':' . $tooltip . '</label><input class="textinput" type="text" id="' . esc_attr( $var ) . '" name="' . $option . '[' . esc_attr( $var ) . ']" value="' . $val . '"/>' . '<br class="clear" />';
	}

	/**
	 * Create a textarea.
	 *
	 * @param string $var    The variable within the option to create the textarea for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 * @param string $class  The CSS class to assign to the textarea.
	 * @return string
	 */
	public function textarea( $var, $label, $option = '', $class = '' ) {
		if ( empty( $option ) )
			$option = $this->currentoption;

		$options = $this->get_option( $option );

		$val = '';
		if ( isset( $options[$var] ) )
			$val = esc_attr( $options[$var] );


		return '<label class="textinput" for="' . esc_attr( $var ) . '">' . esc_html( $label ) . ':</label><textarea class="textinput ' . $class . '" id="' . esc_attr( $var ) . '" name="' . $option . '[' . esc_attr( $var ) . ']">' . $val . '</textarea>' . '<br class="clear" />';
	}

	/**
	 * Create a hidden input field.
	 *
	 * @param string $var    The variable within the option to create the hidden input for.
	 * @param string $option The option the variable belongs to.
	 * @return string
	 */
	public function hidden( $var, $option = '' ) {
		if ( empty( $option ) )
			$option = $this->currentoption;

		$options = $this->get_option( $option );

		$val = '';
		if ( isset( $options[$var] ) )
			$val = esc_attr( $options[$var] );

		return '<input type="hidden" id="hidden_' . esc_attr( $var ) . '" name="' . $option . '[' . esc_attr( $var ) . ']" value="' . $val . '"/>';
	}

	/**
	 * Create a Select Box.
	 *
	 * @param string $var    The variable within the option to create the select for.
	 * @param string $label  The label to show for the variable.
	 * @param array  $values The select options to choose from.
	 * @param string $option The option the variable belongs to.
	 * @return string
	 */
	public function select( $var, $label, $values, $option = '', $tooltip = '' ) {
		if ( empty( $option ) )
			$option = $this->currentoption;

		$options = $this->get_option( $option );

		$var_esc = esc_attr( $var );
		$output  = '<label class="select" for="' . $var_esc . '">' . $label . ':' . $tooltip . '</label>';
		$output .= '<select class="select" name="' . $option . '[' . $var_esc . ']" id="' . $var_esc . '">';

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
	 * Create a Select Box.
	 *
	 * @param string $var    The variable within the option to create the select for.
	 * @param string $label  The label to show for the variable.
	 * @param array  $values The select options to choose from.
	 * @param string $option The option the variable belongs to.
	 * @return string
	 */
	public function selectCountry( $var, $label, $values, $option = '' ) {
		if ( empty( $option ) )
			$option = $this->currentoption;

		$options = $this->get_option( $option );

		$var_esc = esc_attr( $var );
		$output  = '<label class="selectCountry" for="' . $var_esc . '">' . $label . ':</label>';
		$output .= '<select class="selectCountry" name="' . $option . '[' . $var_esc . ']" id="' . $var_esc . '">';

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
	 * Create a File upload field.
	 *
	 * @param string $var    The variable within the option to create the file upload field for.
	 * @param string $label  The label to show for the variable.
	 * @param string $option The option the variable belongs to.
	 * @return string
	 */
	public function file_upload( $var, $label, $option = '' ) {
		if ( empty( $option ) )
			$option = $this->currentoption;

		$options = $this->get_option( $option );

		$val = '';
		if ( isset( $options[$var] ) && strtolower( gettype( $options[$var] ) ) == 'array' ) {
			$val = $options[$var]['url'];
		}

		$var_esc = esc_attr( $var );
		$output  = '<label class="select" for="' . $var_esc . '">' . esc_html( $label ) . ':</label>';
		$output .= '<input type="file" value="' . $val . '" class="textinput" name="' . esc_attr( $option ) . '[' . $var_esc . ']" id="' . $var_esc . '"/>';

		// Need to save separate array items in hidden inputs, because empty file inputs type will be deleted by settings API.
		if ( !empty( $options[$var] ) ) {
			$output .= '<input class="hidden" type="hidden" id="' . $var_esc . '_file" name="prosper_local[' . $var_esc . '][file]" value="' . esc_attr( $options[$var]['file'] ) . '"/>';
			$output .= '<input class="hidden" type="hidden" id="' . $var_esc . '_url" name="prosper_local[' . $var_esc . '][url]" value="' . esc_attr( $options[$var]['url'] ) . '"/>';
			$output .= '<input class="hidden" type="hidden" id="' . $var_esc . '_type" name="prosper_local[' . $var_esc . '][type]" value="' . esc_attr( $options[$var]['type'] ) . '"/>';
		}
		$output .= '<br class="clear"/>';

		return $output;
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
	public function radio( $var, $values, $label, $option = '' ) {
		if ( empty( $option ) )
			$option = $this->currentoption;

		$options = $this->get_option( $option );

		if ( !isset( $options[$var] ) )
			$options[$var] = false;

		$var_esc = esc_attr( $var );

		$output = '<br/><label class="select">' . $label . ':</label>';
		foreach ( $values as $key => $value ) {
			$key = esc_attr( $key );
			$output .= '<input type="radio" class="radio" id="' . $var_esc . '-' . $key . '" name="' . esc_attr( $option ) . '[' . $var_esc . ']" value="' . $key . '" ' . ( $options[$var] == $key ? ' checked="checked"' : '' ) . ' /> <label class="radio" for="' . $var_esc . '-' . $key . '">' . esc_attr( $value ) . '</label>';
		}
		$output .= '<br/>';

		return $output;
	}

	/**
	 * Create a postbox widget.
	 *
	 * @param string $id      ID of the postbox.
	 * @param string $title   Title of the postbox.
	 * @param string $content Content of the postbox.
	 */
	public function postbox( $id, $title, $content ) {
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
	public function form_table( $rows ) {
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
	
	/**
	 * Generates the header for admin pages
	 *
	 * @param string $title          The title to show in the main heading.
	 * @param bool   $form           Whether or not the form should be included.
	 * @param string $option         The long name of the option to use for the current page.
	 * @param string $optionshort    The short name of the option to use for the current page.
	 * @param bool   $contains_files Whether the form should allow for file uploads.
	 */
	public function admin_header( $title, $form = true, $option = 'prosperent_options', $optionshort = 'prosperSuite', $contains_files = false ) {
		?>
		<div class="wrap">
		<?php
		if ( ( isset( $_GET['updated'] ) && $_GET['updated'] == 'true' ) || ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == 'true' ) ) {
			$msg = __( 'Settings updated', 'prosperent-suite' );

			echo '<div id="message" style="width:94%;" class="message updated"><p><strong>' . esc_html( $msg ) . '.</strong></p></div>';
		}
		?>
		<a href="http://prosperent.com/">
			<div id="prosper-icon"
				 style="background: url('<?php echo PROSPER_URL; ?>img/Gears-32.png') no-repeat;"
				 class="icon32">
				<br/>
			</div>
		</a>
		<h2 id="prosper-title"><?php _e( "Prosperent Suite: ", 'prosperent-suite' ); echo $title; ?></h2>
		<div id="prosper_content_top" class="postbox-container" style="min-width:400px; max-width:600px; padding: 0 20px 0 0;">
		<div class="metabox-holder">
		<div class="meta-box-sortables">
		<?php
		if ( $form ) {
			echo '<form action="' . admin_url( 'options.php' ) . '" method="post" id="prosper-conf"' . ( $contains_files ? ' enctype="multipart/form-data"' : '' ) . '>';
			settings_fields( $option );
			$this->currentoption = $optionshort;
		}

	}

	/**
	 * Generates the footer for admin pages
	 *
	 * @param bool $submit Whether or not a submit button should be shown.
	 */
	public function admin_footer( $submit = true ) 
	{
		if ( $submit ) 
		{
			?>
			<div class="submit"><input type="submit" class="button-primary" name="submit"
									   value="<?php _e( "Save Settings", 'prosperent-suite' ); ?>"/></div>
			<?php 
		} 
		?>
		</form>
		</div>
		</div>
		</div>
		<?php $this->admin_sidebar(); ?>
		</div>
	<?php
	}
	
}

global $prosper_admin;
$prosper_admin = new Prosperent_Admin();