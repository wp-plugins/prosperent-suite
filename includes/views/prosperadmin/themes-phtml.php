<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'ProsperThemes', 'prosperent-suite' ), true, 'prosperent_themescss_options', 'prosper_themes' );

echo '<p class="prosper_settingDesc"  style="font-size:15px;">' . __( '<span style="font-size:16px;font-weight:bold;">Themes can change the look of either the ProsperShop or the ProsperInsert.</span><br><br>Themes will allow anyone to easily edit their ProsperShop and ProsperInsert how they see fit. Whether they want to change the layout, styling, or change the entire store. Themes will make that possible, even when the plugin is updated.<br><br>To make your own theme, follow these simple instructions.<br><span style="font-size:13px;margin-left:2em;">First, make sure the <strong>prosperent-themes</strong> directory exists inside wp-content, if not create it.</span><br><span style="font-size:13px;margin-left:2em;">Next, create your own directory inside prosperent-themes and name it anything you\'d like.</span><br><span style="font-size:13px;margin-left:2em;">Now make any changes to the file of your choice below depending on what you are changing.</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; css file</strong> - change any of the styling within the plugin easily</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; product.php</strong> - this file controls the layout of the store</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; productPage.php</strong> - this file controls each product page view</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; insertProd.php</strong> - this file controls the layout of the ProsperInsert</span></span><br><span style="font-size:13px;margin-left:2em;">Now select your theme from below.</span><br><br>An ExampleTheme should be loaded for you inside prosperent-themes. This is to give you a starting point and show you how easy it is to create your own.<br><br>Go to <a href="http://wordpress.prosperentdemo.com/themes/">WordPress Prosperent Demo: Themes</a> for more information. .', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Change the Theme for the Search', 'prosperent-suite' ) . '</h2>';

$themesDir = array();
if (file_exists(PROSPER_THEME))
{
	$dir    = PROSPER_THEME;
	$themesDir = scandir($dir);
	unset($themesDir[0], $themesDir[1]);
}
else
{
	shell_exec('mkdir ' . PROSPER_THEME);
	shell_exec('mkdir ' . PROSPER_THEME . '/ExampleTheme');
	
	$examples = array(
		'products.css' => PROSPER_CSS . '/products.css',
		'product.php' => PROSPER_VIEW . '/prospersearch/themes/Default/original.php',
		'productPage.php' => PROSPER_VIEW . '/prospersearch/original.php'
	);
	foreach ($examples as $i => $exPart)
	{
		copy($exPart, PROSPER_THEME . '/ExampleTheme/' . $i);
	}
	
	if (!file_exists(PROSPER_THEME))
	{
		echo '<div class="update-nag" style="padding:6px 0;">';
		echo _e( '<span style="font-size:14px; padding-left:10px;">The plugin was <strong>unable</strong> to create the <strong>prosperent-themes</strong> directory inside <strong>wp-content</strong>.</span><br><br>', 'my-text-domain' );
		echo _e( '<span style="font-size:14px; padding-left:10px;">Please create a <strong>prosperent-themes</strong> directory inside <strong>wp-content</strong>.</span><br>', 'my-text-domain' );	
		echo '</div>';		
	}
	else
	{
		wp_redirect( admin_url( 'admin.php?page=prosper_themes' ) );
	}
}

$mainThemesDir = scandir(PROSPER_VIEW . '/prospersearch/themes');
unset($mainThemesDir[0], $mainThemesDir[1]);

$themesDir = array_merge($mainThemesDir, $themesDir);

$themes = array();
foreach ($themesDir as $theme)
{
	$themes[$theme] = ucwords($theme);
}

echo $prosperAdmin->select( 'Set_Theme', __( 'Select Theme to Use', 'prosperent-suite' ),  $themes);
echo '<p class="prosper_desc">' . __( "Select <strong>Default</strong> if you want to use the default theme.", 'prosperent-suite' ) . '</p>';

$prosperAdmin->adminFooter();