<?php

global $prosper_admin;
$prosper_admin->admin_header( __( 'Performance Ad Settings', 'prosperent-suite' ), true, 'prosperent_perform_options', 'prosper_performAds' );

echo '<p class="settingDesc">' . __( 'The Performance Ads are content based ads. They are intelligent ads that will analyze the page\'s content and display an ad that is relevant. 
									  If the algorithm is unable to determine what your page is about it will default to the fallback, which is trends, the top selling products in 
									  our catalog. <br><br>You are also able to input a fallback query for each ad if you find it necessary. <br><br>To read more about Performance Ads or see them 
									  in action, head to <a href="http://wordpress.prosperentdemo.com/performance-ads/">WordPress Prosperent Demo: Performance Ads</a>', 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Turn on Performance Ads...', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Enable_PA', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Widget Sizes', 'prosperent-suite' ) . '</h2>';
echo '<h3>' . __( 'Sidebar Widget', 'prosperent-suite' ) . '</h3>';
echo $prosper_admin->textinput( 'SWH', __( 'Height', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Minimum = 54</span></a>' );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'SWW', __( 'Width', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Using <strong>auto</strong> will scale the ad to fit the sidebar.<br>Minimum = 77</span></a>' );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h3>' . __( 'Footer Widget', 'prosperent-suite' ) . '</h3>';
echo '<p class="desc">' . __( "<strong>Note:</strong> Not all themes have footers.", 'prosperent-suite' ) . '</p>';
echo $prosper_admin->textinput( 'FWH', __( 'Height', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Minimum = 54</span></a>' );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'FWW', __( 'Width', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Using <strong>auto</strong> will scale the ad to fit the footer.<br>Minimum = 77</span></a>' );
echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Do you want to us a Fallback?', 'prosperent-suite' ) . '</h2>';
echo '<p class="desc">' . __( "<strong>What is a fallback?</strong><br>
							   -- A fallback is a query that is either a generic term that summarizes your site or a specific product. This is only used if our content analyzer doesn't 
							   produce a good product-to-topic match from your site.", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'sidebar_fallBack', __( 'Sidebar Fallback', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'footer_fallBack', __( 'Footer Fallback', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'content_fallBack', __( 'Content Fallback', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

do_action( 'prosper_performAds' );
$prosper_admin->admin_footer();
