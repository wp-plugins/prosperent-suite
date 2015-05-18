<?php

// TODO DELETE
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'ProsperAd Settings', 'prosperent-suite' ), true, 'prosperent_perform_options', 'prosper_performAds' );

echo '<p class="prosper_settingDesc" style="font-size:14px;">' . __( 'ProsperAds are content based ads. We build out a list of phrases that relates to your content. We then split test those phrases on a page by page basis to determine the best performing phrase. So over time you end up showing the most relevant phrase on each page that displays our ads.									  
											  <br><br>
											  To use ProsperAds you can either:<br><br>&bull; Add a ProsperAd to any page/post while editing it using the shortcode within the <strong>Gear dropdown</strong> or...<br>&bull; Add ProsperAds as a <strong>widget</strong>.
											  
											  <br><br>To read more about ProsperAds and see them in action, head to 
											  <a href="http://wordpress.prosperentdemo.com/performance-ads/">WordPress Prosperent Demo: ProsperAds</a>', 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->hidden( 'Enable_PA');

echo '<h2 class="prosper_h2">' . __( 'Remove Common Words from Topics', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->textinput( 'Remove_Tags', __( 'Common Words', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span><strong>Seperate by Commas. </strong>This removes common tags from being used as topics if you select "Use Tags as Topics" in the widget.</span></a>' );
echo '<p class="prosper_desc">' . __( "Common words are either tags or titles. These can be words that you use across multiple pages or very common words, such as 'shopping' or 'products'. Words that might not come back with a product that you want. <strong><br>Case is not important.</strong>", 'prosperent-suite' ) . '</p>';


$prosperAdmin->adminFooter();
