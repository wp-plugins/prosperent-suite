<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'Themes and CSS', 'prosperent-suite' ), true, 'prosperent_themescss_options', 'prosper_themes' );

echo '<h2 class="prosper_h2">' . __( 'Change the Theme for the Search', 'prosperent-suite' ) . '</h2>';
echo __( '<ol><li>Create a <strong>prosperent-theme</strong> directory inside your <strong>wp-content</strong> directory.</li><li>Import new theme directories.<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>New theme directories should contain one or all of the following: <br>&bull;&nbsp;<strong>product.php</strong><br>&bull;&nbsp;<strong>productPage.php</strong><br>&bull;&nbsp;a <strong>css file</strong> <br>(The CSS file can be named anything, the plugin is looking for the extension .css).</span></a></li><li>Select the desired theme below.</li></ol>', 'prosperent-suite' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

if (file_exists(PROSPER_THEME))
{
	$dir    = PROSPER_THEME;
	$themesDir = scandir($dir);
	unset($themesDir[0], $themesDir[1]);
	
	$themesDir = array_merge(array('Default' => 'default'), $themesDir);
	
	$themes = array();
	foreach ($themesDir as $theme)
	{
		$themes[$theme] = ucwords($theme);
		
	}

	echo $prosperAdmin->select( 'Set_Theme', __( 'Select Theme to Use', 'prosperent-suite' ),  $themes);
	echo '<p class="prosper_descb">' . __( "Select <strong>Default</strong> if you want to use the default theme.", 'prosperent-suite' ) . '</p>';
}
else
{
		echo '<div class="update-nag" style="padding:6px 0;">';
		echo _e( '<span style="font-size:14px; padding-left:10px;">There is an issue with your <strong>prosperent-theme</strong> directory. </span><br>', 'my-text-domain' );
		echo _e('<span style="font-size:14px; padding-left:10px;">Check that it is inside your <strong>wp-content</strong> directory and spelt correctly.</span></br>', 'my-text-domain' ); 		
		echo '</div>';		
}

$prosperAdmin->adminFooter();