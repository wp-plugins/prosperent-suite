<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'MultiSite Settings', 'prosperent-suite' ), true, 'prosperent_multisite_options', 'prosper_multisite' );

echo $prosperAdmin->select( 'MultiSite_User', __( '< strong style="font-size:14px;">MultiSite User Access</strong>', 'prosperent-suite' ), array( 'admin' => __( 'Site Admins (default)', 'prosperent-suite' ), 'superadmin' => __( 'Super Admins Only', 'prosperent-suite' ) ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>'; 


$prosperAdmin->adminFooter();
