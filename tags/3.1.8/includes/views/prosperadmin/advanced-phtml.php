<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$options = get_option('prosper_advanced');

$prosperAdmin->adminHeader( __( 'Advanced', 'prosperent-suite' ), true, 'prosperent_advanced_options', 'prosper_advanced' );

echo '<p class="prosper_settingDesc" style="font-size:16px;">' . __( 'These are the more <strong>advanced</strong> settings. <br><br>They are not necessary to get everything running correctly. ', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Delete Options on Uninstall', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Option_Delete', __( 'Delete Options on Plugin Uninstall', 'prosperent-suite' ) );
echo '<p class="prosper_descb">' . __( "<strong>Checking this will delete options on Uninstall. On reinstallation, some options will be added automatically.</strong>", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'URL Masking', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'URL_Masking', __( ' Affiliate URL Masking', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span>Masks the  affiliate urls, they will now match your website\'s URL. Test before you fully commit to this. It may cause redirection issue with some active plugins.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Image_Masking', __( 'Image URL Masking', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span>Masks the image urls, they will now match your website\'s URL. Test before you fully commit to this. It may cause images to load slowly.</span></a>' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Title Structure', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->select( 'Title_Structure', __( 'Page Title Structure', 'prosperent-suite' ), array( 0 => __( 'WordPress Default', 'prosperent-suite' ), 1 => __( 'Page Title | Query', 'prosperent-suite' ), 2 => __( 'Query | Page Title', 'prosperent-suite' ), 3 => __( 'Query', 'prosperent-suite' ), 4 => __( 'Page Title', 'prosperent-suite' ) ), '', '<a href="#" class="prosper_tooltip"><span>You can choose which seperator to use for option 2 and 3 in the next option. <br>These titles will only change the title on the Store Page.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Title_Sep', __( 'Enter a title seperator', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Optional</span></a>' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Changing the Base URL (Gets set Automatically by the Plugin now)', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Manual_Base', __( 'Change Base URL Manually', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span>Check this box if you\'d like to change your site\'s Base URL manually. Helpful if it set incorrectly by the Plugin, or you have a special use case.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

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
