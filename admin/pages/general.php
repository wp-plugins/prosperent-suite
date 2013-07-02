<?php

global $prosper_admin;
$prosper_admin->admin_header( __( 'General Settings', 'prosperent-suite' ), true, 'prosperent_options', 'prosperSuite' );

$options = get_site_option( 'prosper_general' );

echo '<h2>' . __( 'Your Settings', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->textinput( 'UID', __( 'Prosperent User-Id', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Found next to your screen name once you have logged in to Prosperent. In the upper right hand corner, in parentheses.</span></a>' );

echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Api_Key', __( 'Prosperent API Key', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Once you log in to the backend, click the API tab. Click the API Keys sub-tab, and then either "Add New API Key" or copy an existing one.</span></a>' );
echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Links', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Target', __( 'Open Links in New Window or Tab', 'prosperent-suite' ));
echo '<p class="desc">' . __( "<strong>Checked</strong> : opens link in a new window or tab <br><strong>Unchecked</strong> : opens link in the same window<br><strong>Will Not Change the Functionality of Performance Ads</strong>", 'prosperent-suite' ) . '</p>';

$prosper_admin->admin_footer();