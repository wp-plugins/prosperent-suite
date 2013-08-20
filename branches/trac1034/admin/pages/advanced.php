<?php
global $prosper_admin;
$prosper_admin->admin_header( __( 'Advanced', 'prosperent-suite' ), true, 'prosperent_advanced_options', 'prosper_advanced' );

echo '<p class="settingDesc" style="font-size:16px;">' . __( 'These are the more <strong>advanced</strong> settings. <br><br>They are not necessary to get everything running correctly. ', 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Title Structure', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->select( 'Title_Structure', __( 'Page Title Structure', 'prosperent-uite' ), array( 0 => __( 'WordPress Default', 'prosperent-suite' ), 1 => __( 'Page Title | Query', 'prosperent-suite' ), 2 => __( 'Query | Page Title', 'prosperent-suite' ), 3 => __( 'Query', 'prosperent-suite' ), 4 => __( 'Page Title', 'prosperent-suite' ) ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>You can choose which seperator to use for option 2 and 3 in the next option. <br>These titles will only change the title on the Store Page.</span></a>' );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>'; 

echo $prosper_admin->textinput( 'Title_Sep', __( 'Enter a title seperator', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Optional</span></a>' );
echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Changing the Base URL for the product search', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->textinput( 'Base_URL', __( 'Base Url', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>If you have a different URL from "<strong>your-blog.com/products</strong>" that you want the search query to go to. </span></a>' );
echo '<p class="descb">' . __( "<strong>Deactivate and Reactivate the plugin for the new routes to take effect after saving.</strong>", 'prosperent-suite' ) . '</p>';
	
echo '<h2>' . __( 'Additional CSS for the Shortcode Search Bar', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->textinput( 'Additional_CSS', __( 'Additional CSS', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Additional CSS for the shortcode search bar.</span></a>' );
echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';
			
echo '<h2>' . __( 'Logo Images for the Search Bar in the header', 'prosperent-suite' ) . '</h2>';								
echo $prosper_admin->checkbox( 'Logo_Image', __( 'Logo Image', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "<strong>Only for search bar in header.</strong> Display the original sized Prosperent Logo. Size is 167px x 50px.", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->checkbox( 'Logo_imageSmall', __( 'Logo Image- Small', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "<strong>Only for search bar in header.</strong> Display the smaller Prosperent Logo. Size is 100px x 30px.", 'prosperent-suite' ) . '</p>';

do_action( 'prosper_advanced' );
$prosper_admin->admin_footer();