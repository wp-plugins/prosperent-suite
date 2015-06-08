<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'ProsperLinks Settings', 'prosperent-suite' ), true, 'prosperent_prosper_links_options', 'prosper_prosperLinks' );

echo '<p class="prosper_settingDesc" style="font-size:15px;">' . __( 'This tool is automated, either turn it on or off below.<br><br>
  <span style="font-weight:bold;">Link Affiliator :</span>
  <span style="font-size:14px;">Detects links for the merchants we work with, and on click turns them into affiliate links so you can earn money from them. (The link is not changed until after the click.) </span><br><br>
  <span style="font-weight:bold;">Link Optimizer :</span>
  <span style="font-size:14px;">Takes a merchant url and tries to find a higher converting and higher paying merchant to send the click to. This currently works for Amazon URL\'s.</span>

<br>', 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'PL_LinkAff', __( '<strong style="font-size:16px;">Turn on Link Affiliator</strong>', 'prosperent-suite' ), true);
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->checkbox( 'PL_LinkOpt', __( '<strong style="font-size:16px;">Turn on Link Optimizer</strong>', 'prosperent-suite' ), true);
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

$prosperAdmin->adminFooter();
