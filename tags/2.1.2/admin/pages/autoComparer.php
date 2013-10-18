<?php
global $prosper_admin;
$prosper_admin->admin_header( __( 'Product Insert Settings', 'prosperent-suite' ), true, 'prosperent_compare_options', 'prosper_autoComparer' );

echo '<p class="prosper_settingDesc">' . __( 'The Product Insert is a great tool for anyone looking to promote a product, and makes it very easy to do so. <br><br>Go to <a href="http://wordpress.prosperentdemo.com/auto-comparer/">WordPress Prosperent Demo: Product Insert</a> for more information and to see how it looks and works.', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Turn on Product Insert...', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Enable_AC', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

do_action( 'prosper_autoComparer' );
$prosper_admin->admin_footer();