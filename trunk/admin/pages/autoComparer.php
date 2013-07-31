<?php
global $prosper_admin;
$prosper_admin->admin_header( __( 'Auto-Comparer Settings', 'prosperent-suite' ), true, 'prosperent_compare_options', 'prosper_autoComparer' );

echo '<p class="prosper_settingDesc">' . __( 'The Auto-Comparer is a great tool for anyone looking to promote a product, and it makes it very easy to do so. <br><br>Go to <a href="http://wordpress.prosperentdemo.com/auto-comparer/">WordPress Prosperent Demo: Auto-Comparer</a> for more information and to see how it looks.', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Turn on the Auto-Comparer...', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Enable_AC', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

do_action( 'prosper_autoComparer' );
$prosper_admin->admin_footer();