<?php
global $prosper_admin;
$prosper_admin->admin_header( __( 'ProsperLinks Settings', 'prosperent-suite' ), true, 'prosperent_prosper_links_options', 'prosper_prosperLinks' );

echo '<p class="prosper_settingDesc">' . __( 'ProsperLinks detects URLs for the merchants we work with, and on click turns those links into affiliate URLs so you can earn money from them. <br><br>The link is not changed until after the click, so they do not appear as an affiliate link even when viewing your page\'s source.', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Turn on ProsperLinks...', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Enable_PL', __( 'Yes!', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

do_action( 'prosper_prosperLinks' );
$prosper_admin->admin_footer();
