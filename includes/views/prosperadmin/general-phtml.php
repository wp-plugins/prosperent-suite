<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'General Settings', 'prosperent-suite' ), true, 'prosperent_options', 'prosperSuite' );

echo '<h2 class="prosper_h2">' . __( 'Your Settings (Required)', 'prosperent-suite' ) . '</h2>';
echo __( '<ol><li><a href="http://prosperent.com/join" target="_blank">Sign Up (It\'s free)</a>, if you haven\'t already.</li><li>Go to the <a href="http://prosperent.com/affiliate/install/" target="_blank">Prosperent Install</a> screen.</li><li>Ensure that your Campaign is set to WordPress.</li><li>Copy the API Key from step 3, and paste it into the box below.</li><li>Save your Settings!</li></ol>', 'prosperent-suite' );

echo $prosperAdmin->textinput( 'Api_Key', __( '<strong>Prosperent API Key</strong>', 'prosperent-suite' ), '');
echo '<p class="prosper_descb">' . __( '', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Links', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Target', __( 'Open Links in New Window or Tab', 'prosperent-suite' ));
echo '<p class="prosper_descb">' . __( "<strong>Checked</strong> : opens link in a new window or tab <br><strong>Unchecked</strong> : opens link in the same window<br><strong>Will Not Change the Functionality of Performance Ads</strong>", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Caching', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Enable_Caching', __( 'Turn on Caching', 'prosperent-suite' ));
echo '<p class="prosper_desc">' . __( "You will need to create the <strong>prosperent_cache</strong> directory inside your <strong>wp-content</strong> directory and set the permissions to 0777.<br><strong>Note:</strong> By enabling this, you <strong>MUST</strong> have the <strong>prosperent_cache</strong> directory writable inside your <strong>wp-content</strong> directory. <br>If you have this enabled without the directory, <strong>Everything Will Still Work</strong>, just caching will not be used. You will see an <strong>Error</strong> at the top of your page if this is the case.", 'prosperent-suite' ) . '</p>';

$prosperAdmin->adminFooter();