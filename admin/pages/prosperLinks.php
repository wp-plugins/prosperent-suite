<?php
global $prosper_admin;
$prosper_admin->admin_header( __( 'ProsperLinks Settings', 'prosperent-suite' ), true, 'prosperent_prosper_links_options', 'prosper_prosperLinks' );

echo '<p class="prosper_settingDesc">' . __( 'The Auto-Comparer is a great tool for anyone looking to promote a product, and it makes it very easy to do so. <br>Go to <a href="http://wordpress.prosperentdemo.com/auto-comparer/">WordPress Prosperent Demo: Auto-Comparer</a> for more information and to see how it looks.', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Turn on ProsperLinks...', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Enable_PL', __( 'Yes!', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

do_action( 'prosper_prosperLinks' );
$prosper_admin->admin_footer();
