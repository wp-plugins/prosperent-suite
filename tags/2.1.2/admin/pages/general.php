<?php

global $prosper_admin;
$prosper_admin->admin_header( __( 'General Settings', 'prosperent-suite' ), true, 'prosperent_options', 'prosperSuite' );

$options = get_site_option( 'prosper_general' );

echo '<h2 class="prosper_h2">' . __( 'Your Settings (Required)', 'prosperent-suite' ) . '</h2>';
echo __( '<ol><li><a href="http://prosperent.com/join" target="_blank">Sign Up (It\'s free)</a>, if you haven\'t already.</li><li>Go to the <a href="http://prosperent.com/affiliate/install/" target="_blank">Prosperent Install</a> screen.</li><li>Ensure that your Campaign is set to WordPress.</li><li>Copy the API Key from step 3, and paste it into the box below.</li><li>Save your Settings!</li></ol>', 'prosperent-suite' );

echo $prosper_admin->textinput( 'Api_Key', __( '<strong>Prosperent API Key</strong>', 'prosperent-suite' ), '');
echo '<p class="prosper_descb">' . __( '', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Links', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Target', __( 'Open Links in New Window or Tab', 'prosperent-suite' ));
echo '<p class="prosper_desc">' . __( "<strong>Checked</strong> : opens link in a new window or tab <br><strong>Unchecked</strong> : opens link in the same window<br><strong>Will Not Change the Functionality of Performance Ads</strong>", 'prosperent-suite' ) . '</p>';

$prosper_admin->admin_footer();