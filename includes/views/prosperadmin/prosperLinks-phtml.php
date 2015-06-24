<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'ProsperLinks Settings', 'prosperent-suite' ), true, 'prosperent_prosper_links_options', 'prosper_prosperLinks' );

echo '<p class="prosper_settingDesc" style="font-size:15px;">' . __( 'ProsperLinks will turn ordinary links in your pages and posts into links that will make you money.<br><br>
  <span style="font-weight:bold;">Link Optimizer :</span>
  <span style="font-size:14px;">Sometimes merchants aren\'t running programs that pay you commissions for links. This setting redirects your visitors to a new merchant that carries the same product so that a commission can still be earned.</span>

<br>', 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'PL_LinkOpt', __( '<strong style="font-size:16px;">Activate Link Optimizer</strong>', 'prosperent-suite' ), true);
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

$prosperAdmin->adminFooter();
