<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$options = get_option('prosper_advanced');
$genOptions = get_option('prosperSuite');

$prosperAdmin->adminHeader( __( 'Advanced Settings', 'prosperent-suite' ), true, 'prosperent_advanced_options', 'prosper_advanced' );

echo '<p class="prosper_settingDesc" style="font-size:15px;">' . __( 'These settings are <strong>not required</strong>. ', 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Option_Delete', __( '<strong style="font-size:14px">Delete Options on Uninstall</strong>', 'prosperent-suite' ), true );
echo '<p class="prosper_descb">' . __( "On reinstallation, some options will be added automatically.", 'prosperent-suite' ) . '</p><br>';

if ($genOptions['PSAct'] || $genOptions['PICIAct'])
{
	echo $prosperAdmin->textinput( 'MemcacheIP', __( '<strong style="font-size:14px">Memcache IP</strong>', 'prosperent-suite' ));
	echo '<p class="prosper_desc">' . __( "Enter your Memcache IP if it differs from the default of 127.0.0.1", 'prosperent-suite' ) . '</p><br>';

	echo $prosperAdmin->textinput( 'MemcachePort', __( '<strong style="font-size:14px">Memcache Port</strong>', 'prosperent-suite' ));
	echo '<p class="prosper_descb">' . __( "Enter your Memcache Port if it differs from the default of 11211", 'prosperent-suite' ) . '</p><br>';

	echo '<p class="prosper_settingDescb" style="font-size:14px;">' . __( 'CNAMES will need to be added to your Server\'s DNS Settings first. This <a href="http://community.prosperent.com/showthread.php?2442-Image-url-s-and-CNAME-masking">post</a> shoes how to set up a CNAME.<br><span style="font-size:14px;font-weight:bold;">DO NOT forget the http:// or https://</span>', 'prosperent-suite' ) . '</p>';
	echo $prosperAdmin->textinput( 'ImageCname', __( '<strong style="font-size:14px">Image CNAME</strong>', 'prosperent-suite' ), '');
	echo '<p class="prosper_desc">' . __( "Adding an Image CNAME will make all the images point to that domain.", 'prosperent-suite' ) . '</p><br>';

	echo $prosperAdmin->textinput( 'ClickCname', __( '<strong style="font-size:14px">Click CNAME</strong>', 'prosperent-suite' ), '');
	echo '<p class="prosper_descb">' . __( "Adding a click CNAME will make all the click URLs point to that domain.", 'prosperent-suite' ) . '</p><br>';
}
/*echo $prosperAdmin->checkbox( 'URL_Masking', __( 'Affiliate URL Masking', 'prosperent-suite' ), false, '',  'Masks the  affiliate urls, they will now match your website\'s URL. Test before you fully commit to this. It may cause redirection issue with some active plugins.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Image_Masking', __( 'Image URL Masking', 'prosperent-suite' ), false, '',  'Masks the image urls, they will now match your website\'s URL. Test before you fully commit to this. It may cause images to load slowly.</span></a>' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';
*/

if ($genOptions['PSAct'] || $genOptions['PICIAct'])
{
	echo '<strong style="float:left;margin:6px 0 5px 20px;font-size:14px;">SID Tracking</strong><br>';
	echo $prosperAdmin->multiCheckbox( 'prosperSid',  array( 'blogname' => 'Blog Name', 'interface' => 'Interface', 'query' => 'Query/Topic', 'page' => 'Page', 'pageNumber' => 'Page Number', 'widgetTitle' => 'Widget Title', 'widgetName' => 'Widget Type', 'authorId' => 'Author ID', 'authorName' => 'Author Name', 'postId' => 'Post ID'  ));
	echo '<p>';
	echo $prosperAdmin->textinput( 'prosperSidText', __( 'Other', 'prosperent-suite' ), '', "Additional info for SID tracking, can be a SESSION, SERVER, or COOKIE variable (eg. \$_SERVER['HTTP_HOST'] or \$_COOKIE['MYCOOKIE']).", 'prosper_textinputsmallindent');
	echo '<p class="prosper_descb">' . __( "Each piece will be added to one another with an underscore (_) separator.<br> SID Tracking is used to help you track where clicks/commissions are coming from. It is added to the affiliate URL. <br><br>Helps you figure out what your money making pages are to better convert users.<br><br><strong>Blog Name:</strong> Your Blog\'s Name<br><strong>Interface:</strong> Interface that the click/commission came from (api, pa, pl, pi, al).<br><strong>Query/Topic:</strong> Query or Topic (pa) that the click originated from<br><strong>Page:</strong> What page the click came from<br><strong>Page Number:</strong> If click came from Shop, which page the user was on<br><strong>Widget Title:</strong> The title of the Widget.<br><strong>Widget Type:</strong> The type of widget.<br><strong>Author ID:</strong> Author's ID of the page the click came from.<br><strong>Author Name:</strong> Author's Name of the page the click came from. <br><strong>Post ID:</strong> Page/Post ID", 'prosperent-suite' ) . '</p>';
}

if ($genOptions['PSAct'])
{
	echo '<table><tr><td><img src="' . PROSPER_IMG . '/adminImg/ProsperShop Settings.png"/></td><td><h1 style="margin-left:8px;display:inline-block;font-size:34px;">Advanced ProsperShop Settings</h1></td></tr></table><div style="clear:both"></div>';
	echo '<p class="prosper_settingDesc" style="border:none;">' . __( '', 'prosperent-suite' ) . '</p>';								

	echo $prosperAdmin->textinput( 'relThresh', __( '<strong style="font-size:14px">Relevancy Threshold</strong>', 'prosperent-suite' ));
	echo '<p class="prosper_desc">' . __( "Increase or decrease the relevancy of each query. Enter a decimal value between 0 and 1. Defaults to 0.7.", 'prosperent-suite' ) . '</p><br>';

	echo $prosperAdmin->checkbox( 'noSearchBar', __( '<strong style="font-size:14px">Hide the Shop\'s Search Bar</strong>', 'prosperent-suite' ), true);
	echo '<p class="prosper_desc">' . __( "Hides the primary search bar, use only if you have another way for people to search the shop.", 'prosperent-suite' ) . '</p>';

	echo $prosperAdmin->checkbox( 'noFollowFacets', __( '<strong style="font-size:14px">Add a noFollow to the filters</strong>', 'prosperent-suite' ), true);
	echo '<p class="prosper_desc">' . __( "Adds a NoFollow to Filter Links for Crawlers. This may help reduce the amount of pages that get crawled.", 'prosperent-suite' ) . '</p><br>';

	echo $prosperAdmin->select( 'Title_Structure', __( '<strong style="font-size:14px">Shop\'s Title Structure</strong>', 'prosperent-suite' ), array( 0 => __( 'WordPress Default', 'prosperent-suite' ), 1 => __( 'Page Title | Query', 'prosperent-suite' ), 2 => __( 'Query | Page Title', 'prosperent-suite' ), 3 => __( 'Query', 'prosperent-suite' ), 4 => __( 'Page Title', 'prosperent-suite' ) ));
	echo '<p class="prosper_desc">' . __( "Choose a title structure for product results and product pages.", 'prosperent-suite' ) . '</p><br>';

	echo $prosperAdmin->textinput( 'Title_Sep', __( '<strong style="font-size:14px">Title seperator</strong>', 'prosperent-suite' ) );
	echo '<p class="prosper_desc">' . __( "Enter a seperator to use for the title structure above.", 'prosperent-suite' ) . '</p><br>';

	echo $prosperAdmin->checkbox( 'Manual_Base', __( '<strong style="font-size:14px">Change Base URL Manually</strong>', 'prosperent-suite' ), true, '',  'Check this box if you\'d like to change your site\'s Base URL manually. Helpful if it set incorrectly by the Plugin, or you have a special use case.' );
	echo '<p class="prosper_desc">' . __( "Base URL for the ProsperShop. Gets <strong>set automatically</strong> by the plugin, but there are some instances that you may need to change it manually, for example if you are using the shop as a static front page.", 'prosperent-suite' ) . '</p>';

	if ($options['Manual_Base'])
	{
		echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
		echo $prosperAdmin->textinput( 'Base_URL', __( '<strong style="font-size:14px">Base Url</strong>', 'prosperent-suite' ), '', 'If you have a different URL from - your-blog.com/products - that you want the search query to go to.' );
		echo '<p class="prosper_desc">' . __( "<strong>Deactivate and Reactivate the plugin for the new routes to take effect after saving.</strong>", 'prosperent-suite' ) . '</p><br>';
	}
	else
	{
		echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';
	}

	echo $prosperAdmin->textinput( 'Twitter_Site', __( '<strong style="font-size:14px">Twitter Site Handle</strong>', 'prosperent-suite' ) );
	echo '<p class="prosper_desc">' . __( "Your site's Twitter Handle.", 'prosperent-suite' ) . '</p><br>';

	echo $prosperAdmin->textinput( 'Twitter_Creator', __( '<strong style="font-size:14px">Twitter Site Creator Handle</strong>', 'prosperent-suite' ) );
	echo '<p class="prosper_desc">' . __( "Your Twitter Handle.", 'prosperent-suite' ) . '</p><br>';
	
	echo $prosperAdmin->textinput( 'OG_Image', __( '<strong style="font-size:14px">Facebook Image Width</strong>', 'prosperent-suite' ), '', 'Changes the size of the image when someone shares a shop link on Facebook', 'prosper_textinputsmall');
	echo '<p class="prosper_descb">' . __( "Insert a width for the Facebook Image when a product page is linked to. The height will be the same as your width.<br>Minimum is <strong>200</strong>, Maximum is <strong>500</strong>", 'prosperent-suite' ) . '</p>';
}

if ($genOptions['PSAct'] || $genOptions['PICIAct'])
{
    if ($themeOpts = get_option('prosper_themes'))
    {
        $options['Set_Theme'] = $themeOpts['Set_Theme'];
        update_option('prosper_advanced', $options);
        delete_option('prosper_themes');
    }
    echo '<h2><span id="prosperThemes">Theme Options</span></h2>';        
    
    if (!file_exists(PROSPER_THEME))
    {
        echo '<div class="update-nag" style="padding:6px 0;margin:0;margin-bottom:20px;">';
        echo _e( '<span style="font-size:14px; padding-left:10px;">The plugin was <strong>unable</strong> to create the <strong>prosperent-themes</strong> directory inside <strong>wp-content</strong>.</span><br><br>', 'my-text-domain' );
        echo _e( '<span style="font-size:14px; padding-left:10px;">Please create a <strong>prosperent-themes</strong> directory inside <strong>wp-content</strong>.</span><br>', 'my-text-domain' );
        echo '</div>';
    }
    
    echo '<p class="prosper_desc" style="font-size:15px;">' . __( '<span style="font-size:14px;font-weight:bold;">Themes allow you to change the look of either the ProsperShop or ProsperInsert.</span><br><br>You can change the layout and styling in your own theme and it will last even when the plugin is updated.<br><br>To make your own theme, follow these simple instructions.<br><span style="font-size:13px;margin-left:2em;">First, make sure the <strong>prosperent-themes</strong> directory exists inside wp-content, if not create it.</span><br><span style="font-size:13px;margin-left:2em;">Next, create your own directory inside prosperent-themes and name it anything you\'d like.</span><br><span style="font-size:13px;margin-left:2em;">Now make any changes to the file of your choice below depending on what you are changing.</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; css file</strong> - change any of the styling within the plugin easily</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; product.php</strong> - this file controls the layout of the store</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; productPage.php</strong> - this file controls each product page view</span><br><span style="font-size:13px;margin-left:3.5em;"><strong>&bull; insertProd.php</strong> - this file controls the layout of the ProsperInsert</span></span><br><span style="font-size:13px;margin-left:2em;">Now select your theme from below.</span><br><br>An ExampleTheme should be loaded for you inside prosperent-themes. This is to give you a starting point and show you how easy it is to create your own.', 'prosperent-suite' ) . '</p>';
    
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
            wp_redirect( admin_url( 'admin.php?page=prosper_advanced' ) );
        }
    }
    
    $mainThemesDir = scandir(PROSPER_VIEW . '/prospersearch/themes');
    unset($mainThemesDir[0], $mainThemesDir[1], $mainThemesDir[3], $mainThemesDir[4]);
    $themesDir = array_merge($mainThemesDir, $themesDir);
    
    $themes = array();
    foreach ($themesDir as $theme)
    {
        $themes[$theme] = ucwords($theme);
    }
    
    echo $prosperAdmin->select( 'Set_Theme', __( '<strong style="font-size:14px;white-space: nowrap;">Set Theme</strong>', 'prosperent-suite' ),  $themes, '', 'Select Default if you want to use the default theme.');
    echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
    
}

$prosperAdmin->adminFooter();
