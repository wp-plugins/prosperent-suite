<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$options = get_option('prosper_advanced');
$genOptions = get_option('prosperSuite');

$prosperAdmin->adminHeader( __( 'Advanced Settings', 'prosperent-suite' ), true, 'prosperent_advanced_options', 'prosper_advanced' );

echo '<p class="prosper_settingDesc" style="font-size:15px;">' . __( 'These settings are <strong>not required</strong>. ', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Delete Options on Uninstall', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Option_Delete', __( 'Delete Options on Plugin Uninstall', 'prosperent-suite' ) );
echo '<p class="prosper_descb">' . __( "<strong>Checking this will delete options on Uninstall. On reinstallation, some options will be added automatically.</strong>", 'prosperent-suite' ) . '</p>';

if ($genOptions['PSAct'] || $genOptions['PICIAct'] || $genOptions['ALAct'])
{
	echo '<h2 class="prosper_h2">' . __( 'Memcache', 'prosperent-suite' ) . '</h2>';
	echo $prosperAdmin->textinput( 'MemcacheIP', __( 'Memcache IP', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Enter your Memcache IP if it differs from the default of 127.0.0.1</span></a>');
	echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

	echo $prosperAdmin->textinput( 'MemcachePort', __( 'Memcache Port', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Enter your Memcache Port if it differs from the default of 11211</span></a>');
	echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

	echo '<h2 class="prosper_h2">' . __( 'CNAME Masking', 'prosperent-suite' ) . '</h2>';
	echo '<p class="prosper_settingDescb" style="font-size:15px;">' . __( 'CNAMES will need to be added to your Server\'s DNS Settings first. This <a href="http://community.prosperent.com/showthread.php?2442-Image-url-s-and-CNAME-masking">post</a> shoes how to set up a CNAME.<br><br>DO NOT forget the http:// or https://', 'prosperent-suite' ) . '</p>';
	echo $prosperAdmin->textinput( 'ImageCname', __( 'Image CNAME', 'prosperent-suite' ), '');
	echo '<p class="prosper_desc">' . __( "Adding an Image CNAME will make all the images point to that domain.", 'prosperent-suite' ) . '</p>';

	echo $prosperAdmin->textinput( 'ClickCname', __( 'Click CNAME', 'prosperent-suite' ), '');
	echo '<p class="prosper_descb">' . __( "Adding a click CNAME will make all the click URLs point to that domain.", 'prosperent-suite' ) . '</p>';
}
/*echo $prosperAdmin->checkbox( 'URL_Masking', __( 'Affiliate URL Masking', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span>Masks the  affiliate urls, they will now match your website\'s URL. Test before you fully commit to this. It may cause redirection issue with some active plugins.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Image_Masking', __( 'Image URL Masking', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span>Masks the image urls, they will now match your website\'s URL. Test before you fully commit to this. It may cause images to load slowly.</span></a>' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';
*/

if ($genOptions['PSAct'] || $genOptions['PICIAct'] || $genOptions['ALAct'] || $genOptions['PAAct'])
{
	echo '<h2 class="prosper_h2">' . __( 'SID Tracking', 'prosperent-suite' ) . '</h2>';
	echo $prosperAdmin->multiCheckbox( 'prosperSid',  array( 'blogname' => 'Blog Name', 'interface' => 'Interface<a href="#" class="prosper_tooltip"><span>Interface that the click came from.<br><br>API: the shop<br>PA: ProsperAds<br>PL: ProsperLinks<br>PI: ProsperInsert/ContentInsert<br>AL: Auto-Linker</span></a>', 'query' => 'Query/Topic', 'page' => 'Page', 'pageNumber' => 'Page Number', 'widgetTitle' => 'Widget Title', 'widgetName' => 'Widget Type', 'authorId' => 'Author ID', 'authorName' => 'Author Name'  ), 'Select what you\'d like to be included in the SID ', '', '<a href="#" class="prosper_tooltip"><span>Choose a SID Tracking for your blog.</span></a>' );
	echo '<p>';
	echo $prosperAdmin->textinput( 'prosperSidText', __( 'Other', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Additional info for SID tracking, can be a SESSION, SERVER, or COOKIE variable <br><strong>(eg. $_SERVER[\'HTTP_HOST\'] or $_SERVER[\"HTTP_HOST\"])</strong>.</span></a>', 'prosper_textinputsmallindent');
	echo '<p class="prosper_descb">' . __( "Each piece will be added to one another with an underscore (_) separator.<br> SID Tracking is used to help you track where clicks/commissions are coming from. It is added to the affiliate URL. <br><br>Helps you figure out what your money making pages are to better convert users.<br><br><strong>Blog Name:</strong> Your Blog\'s Name<br><strong>Interface:</strong> Interface that the click/commission came from (api, pa, pl, pi, al).<br><strong>Query/Topic:</strong> Query or Topic (pa) that the click originated from<br><strong>Page:</strong> What page the click came from<br><strong>Page Number:</strong> If click came from Shop, which page the user was on<br><strong>Widget Title:</strong> The title of the Widget.<br><strong>Widget Type:</strong> The type of widget.<br><strong>Author ID:</strong> Author's ID of the page the click came from.<br><strong>Author Name:</strong> Author's Name of the page the click came from. ", 'prosperent-suite' ) . '</p>';
}

if ($genOptions['PSAct'])
{
	echo '<table><tr><td><img src="' . PROSPER_IMG . '/adminImg/ProsperShop Settings.png"/></td><td><h1 style="margin-left:8px;display:inline-block;font-size:34px;">Advanced ProsperShop Settings</h1></td></tr></table><div style="clear:both"></div>';
	echo '<p class="prosper_settingDesc" style="border:none;">' . __( '', 'prosperent-suite' ) . '</p>';								

	echo $prosperAdmin->textinput( 'relThresh', __( 'Relevancy Threshold', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Any decimal value between 0 and 1.<br>Lower values will return more results, but not as relevant, while higher values will return less results, but more relevant.</span></a>' );
	echo '<p class="prosper_desc">' . __( "Controls the relevancy for queries passed to the ProsperShop.", 'prosperent-suite' ) . '</p>';

	echo $prosperAdmin->checkbox( 'noSearchBar', __( 'Turn off the main search bar on the product page', 'prosperent-suite' ), false);
	echo '<p class="prosper_desc">' . __( "Only use this if you have a means of passing searches to the results. Otherwise no one will be able to search on your site.", 'prosperent-suite' ) . '</p>';

	echo $prosperAdmin->checkbox( 'titleMercLink', __( 'Link Product Titles to Merchant', 'prosperent-suite' ), false);
	echo '<p class="prosper_desc">' . __( "Bypasses the Product Page.", 'prosperent-suite' ) . '</p>';

	echo $prosperAdmin->checkbox( 'imageMercLink', __( 'Link Product Images to Merchant', 'prosperent-suite' ), false);
	echo '<p class="prosper_desc">' . __( "Bypasses the Product Page.", 'prosperent-suite' ) . '</p>';

	echo $prosperAdmin->checkbox( 'noFollowFacets', __( 'Add a noFollow to the facet links.', 'prosperent-suite' ), false);
	echo '<p class="prosper_descb">' . __( "May help reduce the amount of pages crawled.", 'prosperent-suite' ) . '</p>';

	echo '<h2 class="prosper_h2">' . __( 'Title Structure', 'prosperent-suite' ) . '</h2>';
	echo $prosperAdmin->select( 'Title_Structure', __( 'Page Title Structure', 'prosperent-suite' ), array( 0 => __( 'WordPress Default', 'prosperent-suite' ), 1 => __( 'Page Title | Query', 'prosperent-suite' ), 2 => __( 'Query | Page Title', 'prosperent-suite' ), 3 => __( 'Query', 'prosperent-suite' ), 4 => __( 'Page Title', 'prosperent-suite' ) ), '', '<a href="#" class="prosper_tooltip"><span>You can choose which seperator to use for option 2 and 3 in the next option. <br>These titles will only change the title on the Store Page.</span></a>' );
	echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

	echo $prosperAdmin->textinput( 'Title_Sep', __( 'Enter a title seperator', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Optional</span></a>' );
	echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

	echo '<h2 class="prosper_h2">' . __( 'Base URL', 'prosperent-suite' ) . '</h2>';
	echo $prosperAdmin->checkbox( 'Manual_Base', __( 'Change Base URL Manually', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span>Check this box if you\'d like to change your site\'s Base URL manually. Helpful if it set incorrectly by the Plugin, or you have a special use case.</span></a>' );
	echo '<p class="prosper_desc">' . __( "This is the Base URL for the ProsperShop. It gets <strong>set automatically</strong> by the plugin, but there are some circumstances that you may need to change it for instance if you are using the shop as a static front page.", 'prosperent-suite' ) . '</p>';

	if ($options['Manual_Base'])
	{
		echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
		echo $prosperAdmin->textinput( 'Base_URL', __( 'Base Url', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>If you have a different URL from "<strong>your-blog.com/products</strong>" that you want the search query to go to. </span></a>' );
		echo '<p class="prosper_descb">' . __( "<strong>Deactivate and Reactivate the plugin for the new routes to take effect after saving.</strong>", 'prosperent-suite' ) . '</p>';
	}
	else
	{
		echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';
	}

	echo '<h2 class="prosper_h2">' . __( 'Open Graph Info', 'prosperent-suite' ) . '</h2>';
	echo $prosperAdmin->textinput( 'Twitter_Site', __( 'Twitter Site', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>If your site has a twitter handle, enter that here.</span></a>' );
	echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

	echo $prosperAdmin->textinput( 'Twitter_Creator', __( 'Twitter Creator', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>If you want to make your twitter handle available, as the site creator.</span></a>' );
	echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
	
	echo $prosperAdmin->textinput( 'OG_Image', __( 'Facebook Image Width', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Changes the size of the image when someone shares the product page.</span></a>', 'prosper_textinputsmall');
	echo '<p class="prosper_desc">' . __( "Minimum is <strong>200</strong>, Maximum is <strong>500</strong><br>Height of image will be the same as the width.", 'prosperent-suite' ) . '</p>';
}

$prosperAdmin->adminFooter();
