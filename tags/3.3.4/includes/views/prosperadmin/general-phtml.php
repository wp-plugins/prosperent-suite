<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$options = get_option('prosperSuite');

$prosperAdmin->adminHeader( __( 'General Settings', 'prosperent-suite' ), true, 'prosperent_options', 'prosperSuite' );

echo '<p class="prosper_settingDesc" style="font-size:14px;">' . __( 'Go to <a href="http://wordpress.prosperentdemo.com">WordPress Prosperent Demo</a> for more information and tutorials.', 'prosperent-suite' ) . '</p>';

echo __( '<ol><li><a href="http://prosperent.com/join" target="_blank">Sign Up (It\'s free)</a>, if you haven\'t already.</li><li>Go to the <a href="http://prosperent.com/account/wordpress" target="_blank">Prosperent WordPress Install</a> screen.</li><li>Either Create a New Installation or use the API Key from a previous setup.</li><li>Copy the API Key, and paste it into the box below.</li><li>Save your Settings!</li></ol>', 'prosperent-suite' );

echo $prosperAdmin->textinput( 'Api_Key', __( '<strong>Prosperent API Key</strong>', 'prosperent-suite' ), '');
echo '<p class="prosper_descb">' . __( '', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Links', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Target', __( 'Open Links in New Window/Tab', 'prosperent-suite' ));
echo '<p class="prosper_descb">' . __( "<strong>Checked</strong> : opens link in a new window/tab <br><strong>Unchecked</strong> : opens link in the same window<br><strong>Will Not Change the Functionality of Performance Ads or ProsperLinks</strong>", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Caching', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Enable_Caching', __( 'Turn on Caching', 'prosperent-suite' ));
/*if ($options['Enable_Caching'] &&  (file_exists(PROSPER_CACHE) || substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777'))
{
	echo '<a style="margin:10px 0 6px 35px; vertical-align:baseline;" class="button-secondary" href="' . admin_url( 'admin.php?page=prosper_general&clearCache&nonce='. wp_create_nonce( 'prosper_clear_cache' )) . '">' . __( 'Clear Cache', 'prosperent-suite' ) . '</a>';
}*/
if ($options['Enable_Caching'])
{
	shell_exec('mkdir ' . PROSPER_CACHE);
	if (!file_exists(PROSPER_CACHE))
	{
		echo '<div class="update-nag" style="padding:6px 0;">';
		echo _e( '<span style="font-size:14px; padding-left:10px;">The plugin was <strong>unable</strong> to create the <strong>prosperent-cache</strong> directory inside <strong>wp-content</strong>.</span><br><br>', 'my-text-domain' );
		echo _e( '<span style="font-size:14px; padding-left:10px;">Please create a <strong>prosperent-cache</strong> directory inside <strong>wp-content</strong> for caching to work properly.</span><br>', 'my-text-domain' );	
		echo '</div>';		
	}
}

echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
echo $prosperAdmin->hidden( 'ProsperFirstTimeOperator' );

$prosperAdmin->adminFooter();