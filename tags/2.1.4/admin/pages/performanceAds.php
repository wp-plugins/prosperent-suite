<?php

global $prosper_admin;
$prosper_admin->admin_header( __( 'Performance Ad Settings', 'prosperent-suite' ), true, 'prosperent_perform_options', 'prosper_performAds' );

echo '<p class="prosper_settingDesc">' . __( 'The Performance Ads are content based ads. We build out a list of phrases that relates to your content. We then split test those phrases on a page by page basis to determine the best performing phrase. So over time you end up showing the most relevant phrase on each page that displays our ads.
											  <br><br>
											  If you provide a topic for the ad, we will use that to display the best fitting products according to the given topic.  
											  
											  <br><br>
											  You can change the widget sizes and topic inside the widget settings when you move it to your sidebar or footer. Each performance ad unit will accept up to 5 comma seperated topics, 
											  including any tags or the page title if you choose to use those.
											  
											  <br><br>
											  If you use tags as a topic, and want to make sure some more common tags aren\'t being used as a topic you can remove those below by putting the tag into the <strong>Common Tags</strong>
											  box.
											  
											  <br><br>
											  Also, there is a button in the page/post editior that will place shortcode for Performance Ads. This allows you to easily place Performance Ads in your content.
											  
											  <br><br>To read more about Performance Ads or see them in action, head to 
											  <a href="http://wordpress.prosperentdemo.com/performance-ads/">WordPress Prosperent Demo: Performance Ads</a>', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Turn on Performance Ads...', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Enable_PA', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Remove Common Words from Topics', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->textinput( 'Remove_Tags', __( 'Common Words', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span><strong>Seperate by Commas. </strong>This removes common tags from being used as topics if you select "Use Tags as Topics" in the widget.</span></a>' );
echo '<p class="prosper_desc">' . __( "Common words are either tags or titles. These can be words that you use across multiple pages or very common words, such as 'shopping' or 'products'. Words that might not come back with a product that you want. <strong><br>Case is not important.</strong>", 'prosperent-suite' ) . '</p>';
/*
echo '<h2 class="prosper_h2">' . __( 'Widget Sizes', 'prosperent-suite' ) . '</h2>';
echo '<h3>' . __( 'Sidebar Widget', 'prosperent-suite' ) . '</h3>';
echo $prosper_admin->textinput( 'SWH', __( 'Height', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Minimum = 54</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'SWW', __( 'Width', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Using <strong>auto</strong> will scale the ad to fit the sidebar.<br>Minimum = 77</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h3>' . __( 'Footer Widget', 'prosperent-suite' ) . '</h3>';
echo '<p class="prosper_desc">' . __( "<strong>Note:</strong> Not all themes have footers.", 'prosperent-suite' ) . '</p>';
echo $prosper_admin->textinput( 'FWH', __( 'Height', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Minimum = 54</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'FWW', __( 'Width', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Using <strong>auto</strong> will scale the ad to fit the footer.<br>Minimum = 77</span></a>' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Do you want to us a Fallback?', 'prosperent-suite' ) . '</h2>';
echo '<p class="prosper_desc">' . __( "<strong>What is a fallback?</strong><br>
							   -- A fallback is a query that is either a generic term that summarizes your site or a specific product. This is only used if our content analyzer doesn't 
							   produce a good product-to-topic match from your site.", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'sidebar_fallBack', __( 'Sidebar Fallback', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'footer_fallBack', __( 'Footer Fallback', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'content_fallBack', __( 'Content Fallback', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
*/


do_action( 'prosper_performAds' );
$prosper_admin->admin_footer();
