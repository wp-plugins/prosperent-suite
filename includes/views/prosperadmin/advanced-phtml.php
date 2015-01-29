<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$options = get_option('prosper_advanced');

$prosperAdmin->adminHeader( __( 'Advanced Settings', 'prosperent-suite' ), true, 'prosperent_advanced_options', 'prosper_advanced' );

echo '<p class="prosper_settingDesc" style="font-size:15px;">' . __( 'These settings are <strong>not required</strong>. ', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Delete Options on Uninstall', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Option_Delete', __( 'Delete Options on Plugin Uninstall', 'prosperent-suite' ) );
echo '<p class="prosper_descb">' . __( "<strong>Checking this will delete options on Uninstall. On reinstallation, some options will be added automatically.</strong>", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'URL Masking', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'URL_Masking', __( 'Affiliate URL Masking', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span>Masks the  affiliate urls, they will now match your website\'s URL. Test before you fully commit to this. It may cause redirection issue with some active plugins.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Image_Masking', __( 'Image URL Masking', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span>Masks the image urls, they will now match your website\'s URL. Test before you fully commit to this. It may cause images to load slowly.</span></a>' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'SID Tracking', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->multiCheckbox( 'prosperSid',  array( 'blogname' => 'Blog Name', 'interface' => 'Interface', 'query' => 'Query/Topic', 'page' => 'Page', 'pageNumber' => 'Page Number', 'widgetTitle' => 'Widget Title', 'widgetName' => 'Widget Name' ), 'Select what you\'d like to be included in the SID ', '', '<a href="#" class="prosper_tooltip"><span>Choose a SID Tracking for your blog.<br><strong>Blog Name:</strong> Your Blog\'s Name<br><strong>Interface:</strong> Interface that the click/commission came from (api, pa, pl, pi, al).<br><strong>Query/Topic:</strong> Query or Topic (pa) that the click originated from<br><strong>Page:</strong> What page the click came from<br><strong>Page Number:</strong> If click came from Shop, which page the user was on<br><br>Defaults to <strong>blogname_interface</strong>.</span></a>' );
echo '<p>';
echo $prosperAdmin->textinput( 'prosperSidText', __( 'Other', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Additional info for SID tracking, can be a SESSION, SERVER, or COOKIE variable <br><strong>(eg. $_SERVER[\'HTTP_HOST\'] or $_SERVER[\"HTTP_HOST\"])</strong>.</span></a>', 'prosper_textinputsmallindent');
echo '<p class="prosper_descb">' . __( "Each piece will be added to one another with an underscore (_) separator.<br> SID Tracking is used to help you track where clicks/commissions are coming from. It is added to the affiliate URL. <br><br>Helps you figure out what your money making pages are to better convert users.", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'ProsperShop', 'prosperent-suite' ) . '</h2>';
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

echo '<h2 class="prosper_h2">' . __( 'Twitter Cards Info', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->textinput( 'Twitter_Site', __( 'Twitter Site', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>If your site has a twitter handle, enter that here.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Twitter_Creator', __( 'Twitter Creator', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>If you want to make your twitter handle available, as the site creator.</span></a>' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

$prosperAdmin->adminFooter();
