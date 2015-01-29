<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'Themes', 'prosperent-suite' ), true, 'prosperent_themescss_options', 'prosper_themes' );

echo '<p class="prosper_settingDesc"  style="font-size:15px;">' . __( 'The main idea behind themes is to allow people to edit their stores how they see fit. Whether they want to change the CSS or alter the entire store. Themes will make that possible, even while the plugin gets updated.<br><br>To make your own theme, follow these simple instructions.<br><span style="font-size:13px;margin-left:2em;">First, make sure the <strong>prosperent-themes</strong> directory exists inside wp-content, if not create it.</span><br><span style="font-size:13px;margin-left:2em;">Next, create your own directory inside prosperent-themes and name it anything you\'d like.</span><br><span style="font-size:13px;margin-left:2em;">Now make any changes to the file of your choice below depending on what you are changing.</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; css file</strong> - change any of the styling within the plugin easily</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; product.php</strong> - this file controls the layout of the store</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; productPage.php</strong> - this file controls each product page view</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; insertProd.php</strong> - this file controls the layout of the ProsperInsert</span></span><br><span style="font-size:13px;margin-left:2em;">Now select your theme from below.</span><br><br>An ExampleTheme has loaded for you inside prosperent-themes. This is to give you a starting point and show you how easy it is to create your own.<br><br>Go to <a href="http://wordpress.prosperentdemo.com/themes/">WordPress Prosperent Demo: Themes</a> for more information. .', 'prosperent-suite' ) . '</p>';

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

//echo __( '<ol><li>Place new theme directories inside <strong>prosperent-themes</strong>.<a href="#" class="prosper_tooltip"><span style="max-width:465px;">New theme directories should contain one or all of the following: <br>&bull;&nbsp;<strong>product.php</strong>- this is for the product results<br>&bull;&nbsp;<strong>productPage.php</strong> - this is for the individual product pages<br>&bull;&nbsp;<strong>insertProd.php</strong> - this is for the ProsperInserter<br>&bull;&nbsp;a <strong>CSS file</strong> - contains the CSS for product.php, productPage.php and insertProd.php<br>(The CSS file can be named anything, the plugin is looking for the extension .css).</span></a></li><li>Select the desired theme.</li></ol>', 'prosperent-suite' );
//echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';


$prosperAdmin->adminFooter();