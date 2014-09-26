<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'MultiSite', 'prosperent-suite' ), true, 'prosperent_multisite_options', 'prosper_multisite' );

echo '<h2 class="prosper_h2">' . __( 'MultiSite User Settings', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->select( 'MultiSite_User', __( 'Who do you want to have access to the Prosperent Settings?', 'prosperent-suite' ), array( 'admin' => __( 'Site Admins (default)', 'prosperent-suite' ), 'superadmin' => __( 'Super Admins Only', 'prosperent-suite' ) ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>'; 


$prosperAdmin->adminFooter();
