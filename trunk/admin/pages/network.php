<?php
global $prosper_admin;
$prosper_admin->admin_header( __( 'MultiSite', 'prosperent-suite' ), true, 'prosperent_multisite_options', 'prosper_multisite' );

echo '<h2>' . __( 'MultiSite User Settings', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->select( 'MultiSite_User', __( 'Who do you want to have access to the Prosperent Settings?', 'prosperent-suite' ), array( 'admin' => __( 'Site Admins (default)', 'prosperent-suite' ), 'superadmin' => __( 'Super Admins Only', 'prosperent-suite' ) ) );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>'; 

do_action( 'prosper_multisite' );
$prosper_admin->admin_footer();
