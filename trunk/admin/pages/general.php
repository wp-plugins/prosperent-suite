<?php

global $prosper_admin;
$prosper_admin->admin_header( __( 'General Settings', 'prosperent-suite' ), true, 'prosperent_options', 'prosperSuite' );

$options = get_site_option( 'prosper_general' );

echo '<h2 class="prosper_h2">' . __( 'Your Settings (Required)', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->textinputsmall( 'UID', __( '<strong>Prosperent User-Id</strong>', 'prosperent-suite' ), '');
echo '<p class="prosper_desc">' . __( '1.) Sign Up (It\'s free) at <a href="http://prosperent.com/join" target="_blank">Prosperent</a><br>2.) Once you are logged in, look in the upper Right Hand corner for the 6 digit number that comes after your user-name. That is your UserId.<br><strong>Example</strong>: Welcome, Brandon (123456).  Your UserId would be <strong>123456</strong> in this case.', 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Api_Key', __( '<strong>Prosperent API Key</strong>', 'prosperent-suite' ), '');
echo '<p class="prosper_descb">' . __( '1.) Go to the <a href="http://prosperent.com/affiliate/api" target="_blank">Prosperent API</a> page <br>2.) Click the API Keys tab<br>3.) Click either "Add New API Key" or copy an existing API Key<br>4.) Paste the API Key into this box', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Links', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Target', __( 'Open Links in New Window or Tab', 'prosperent-suite' ));
echo '<p class="prosper_desc">' . __( "<strong>Checked</strong> : opens link in a new window or tab <br><strong>Unchecked</strong> : opens link in the same window<br><strong>Will Not Change the Functionality of Performance Ads</strong>", 'prosperent-suite' ) . '</p>';

$prosper_admin->admin_footer();