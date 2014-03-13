<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'ProsperLinks Settings', 'prosperent-suite' ), true, 'prosperent_prosper_links_options', 'prosper_prosperLinks' );

echo '<p class="prosper_settingDesc">' . __( 'ProsperLinks  has two parts.<br><br><strong>The Link Affiliator</strong>- detects URLs for the merchants we work with, and on click turns those links into affiliate URLs so you can earn money from them. The link is not changed until after the click, so they do not appear as an affiliate link even when viewing your page\'s source.<br><br><strong>Link Optimizer</strong>- takes a merchant url and tries to find a higher converting/ higher paying merchant to send the click to. This currently works for Amazon url\'s, and we hope to expand to other merchants as time passes<br>', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Turn on Link Affiliator...', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'PL_LinkAff', __( 'Yes!', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>Detects URLs for the merchants we work with, and on click turns those links into affiliate URLs.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Turn on Link Optimizer...', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'PL_LinkOpt', __( 'Yes!', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>Takes a merchant URL and tries to find a higher converting/ higher paying merchant to send the click to.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';


$prosperAdmin->adminFooter();
