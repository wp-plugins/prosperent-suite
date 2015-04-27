<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$options = get_option('prosperSuite');

$prosperAdmin->adminHeader( __( 'General Settings', 'prosperent-suite' ), true, 'prosperent_options', 'prosperSuite' );

echo '<p class="prosper_settingDesc" style="font-size:14px;">' . __( 'Go to <a href="http://wordpress.prosperentdemo.com">WordPress Prosperent Demo</a> for more information and tutorials.', 'prosperent-suite' ) . '</p>';

echo __( '<ol><li><a href="http://prosperent.com/join?utm_source=' . urlencode(home_url()) . '&utm_medium=direct&utm_campaign=wp-suite-signup" target="_blank">Sign Up (It\'s free)</a>, if you haven\'t already.</li><li>Go to the <a href="http://prosperent.com/account/wordpress" target="_blank">Prosperent WordPress Install</a> screen.</li><li>Either Create a New Installation or use the API Key from a previous setup.</li><li>Copy the API Key, and paste it into the box below.</li><li>Save your Settings!</li></ol>', 'prosperent-suite' );

echo $prosperAdmin->textinput( 'Api_Key', __( '<strong>Prosperent API Key</strong>', 'prosperent-suite' ), '');
echo '<p class="prosper_descb">' . __( '', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Enable/Disable...', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'PSAct', __( '<span style="font-size:18px;line-height:1.2em;">ProsperShop</span>', 'prosperent-suite' ) );
echo '<p class="prosper_desc" style="padding-bottom:2px;">' . __( "", 'prosperent-suite' ) . '</p>';

if ($options['PAAct'])
{
	echo $prosperAdmin->checkbox( 'PAAct', __( '<span style="font-size:18px;line-height:1.2em;">ProsperAds</span>', 'prosperent-suite' ) );
	echo '<p class="prosper_desc" style="padding-bottom:2px;">' . __( "", 'prosperent-suite' ) . '</p>';
}

echo $prosperAdmin->checkbox( 'PICIAct', __( '<span style="font-size:18px;line-height:1.2em;">ProsperInsert and Content Inserter</span>', 'prosperent-suite' ) );
echo '<p class="prosper_desc" style="padding-bottom:2px;">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'ALAct', __( '<span style="font-size:18px;line-height:1.2em;">AutoLinker</span>', 'prosperent-suite' ) );
echo '<p class="prosper_desc" style="padding-bottom:2px;">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'PLAct', __( '<span style="font-size:18px;line-height:1.2em;">ProsperLinks</span>', 'prosperent-suite' ) );
echo '<p class="prosper_descb" style="padding-bottom:2px;">' . __( "", 'prosperent-suite' ) . '</p>';

if ($options['PSAct'] || $options['PICIAct'] || $options['ALAct'])
{
	echo '<h2 class="prosper_h2">' . __( 'Links', 'prosperent-suite' ) . '</h2>';
	echo $prosperAdmin->checkbox( 'Target', __( 'Open Links in New Window/Tab', 'prosperent-suite' ));
	echo '<p class="prosper_descb">' . __( "<strong>Checked</strong> : opens link in a new window/tab <br><strong>Unchecked</strong> : opens link in the same window<br><strong>Will Not Change the Functionality of ProsperAds or ProsperLinks</strong>", 'prosperent-suite' ) . '</p>';

	echo '<h2 class="prosper_h2">' . __( 'Caching', 'prosperent-suite' ) . '</h2>';
	echo $prosperAdmin->checkbox( 'Enable_Caching', __( 'Turn on Caching', 'prosperent-suite' ));
	if ($options['Enable_Caching'] &&  extension_loaded('memcache'))
	{
		echo '<a style="margin:10px 0 6px 35px; vertical-align:baseline;" class="button-secondary" href="' . admin_url( 'admin.php?page=prosper_general&clearCache&nonce='. wp_create_nonce( 'prosper_clear_cache' )) . '">' . __( 'Clear Memcache', 'prosperent-suite' ) . '</a>';
	}
	echo '<p class="prosper_descb">' . __( 'Caching now uses <strong>Memcache</strong>. You may have to install Memcache on your server.<br>If you have set up an alternate IP and port go to <a href="' . admin_url( 'admin.php?page=prosper_advanced') . '">Advanced Settings</a> to change these.', 'prosperent-suite' ) . '</p>';
}
echo '<h2 class="prosper_h2">' . __( 'Help Us, Help You', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'anonymousData', __( 'Send data back to Prosperent', 'prosperent-suite' ));
echo '<p class="prosper_desc">' . __( "This will help us better serve you by knowing which features are used the most and understanding how to make the plugin better for everyone and helping with support when needed.", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->hidden( 'ProsperFirstTimeOperator' );
echo $prosperAdmin->hidden( 'prosperNewVersion' );
echo $prosperAdmin->hidden( 'shortCodesAccessed' );
echo $prosperAdmin->hidden( 'prosperNoOptions' );

$prosperAdmin->adminFooter();