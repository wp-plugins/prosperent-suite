<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'ProsperThemes', 'prosperent-suite' ), true, 'prosperent_themescss_options', 'prosper_themes' );

if (!file_exists(PROSPER_THEME))
{
	echo '<div class="update-nag" style="padding:6px 0;margin:0;margin-bottom:20px;">';
	echo _e( '<span style="font-size:14px; padding-left:10px;">The plugin was <strong>unable</strong> to create the <strong>prosperent-themes</strong> directory inside <strong>wp-content</strong>.</span><br><br>', 'my-text-domain' );
	echo _e( '<span style="font-size:14px; padding-left:10px;">Please create a <strong>prosperent-themes</strong> directory inside <strong>wp-content</strong>.</span><br>', 'my-text-domain' );	
	echo '</div>';
}

echo '<p class="prosper_settingDesc"  style="font-size:15px;">' . __( '<span style="font-size:14px;font-weight:bold;">Themes allow you to change the look of either the ProsperShop or ProsperInsert.</span><br><br>Themes will allow you to easily edit your ProsperShop and ProsperInsert how you want. You can change the layout and styling in your own theme and it will last even when the plugin is updated.<br><br>To make your own theme, follow these simple instructions.<br><span style="font-size:13px;margin-left:2em;">First, make sure the <strong>prosperent-themes</strong> directory exists inside wp-content, if not create it.</span><br><span style="font-size:13px;margin-left:2em;">Next, create your own directory inside prosperent-themes and name it anything you\'d like.</span><br><span style="font-size:13px;margin-left:2em;">Now make any changes to the file of your choice below depending on what you are changing.</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; css file</strong> - change any of the styling within the plugin easily</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; product.php</strong> - this file controls the layout of the store</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; productPage.php</strong> - this file controls each product page view</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; insertProd.php</strong> - this file controls the layout of the ProsperInsert</span></span><br><span style="font-size:13px;margin-left:2em;">Now select your theme from below.</span><br><br>An ExampleTheme should be loaded for you inside prosperent-themes. This is to give you a starting point and show you how easy it is to create your own.<br><br>Go to <a href="http://wordpress.prosperentdemo.com/themes/">WordPress Prosperent Demo: Themes</a> for more information. .', 'prosperent-suite' ) . '</p>';

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
		'product.php' => PROSPER_VIEW . '/prospersearch/themes/Default/product.php',
		'productPage.php' => PROSPER_VIEW . '/prospersearch/productPage.php'
	);
	foreach ($examples as $i => $exPart)
	{
		copy($exPart, PROSPER_THEME . '/ExampleTheme/' . $i);
	}
	
	if (file_exists(PROSPER_THEME))
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

echo $prosperAdmin->select( 'Set_Theme', __( '<strong style="font-size:14px;white-space: nowrap;">Shop/Insert Theme</strong>', 'prosperent-suite' ),  $themes, '', 'Select Default if you want to use the default theme.');
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

$prosperAdmin->adminFooter();