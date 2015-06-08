<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'ContentInsert Settings', 'prosperent-suite' ), true, 'prosperent_compare_options', 'prosper_autoComparer' );

echo '<p class="prosper_settingDesc" style="font-size:14px;">' . __( 'Insert Products into All Posts and/or Pages.<br>Uses the PagePost titles to create a ProsperInsert above or below the content for all posts/pages.<br>You can also choose words to exclude from page titles.<br><br>You can edit products on a page/post by using gear icon in the visual editor and clicking "Edit ContentInsert Products"<br><br>Go to <a href="http://wordpress.prosperentdemo.com/prodinsert/">WordPress Prosperent Demo: ProsperInsert</a> for more information and to see how it looks and works.', 'prosperent-suite' ) . '</p>';

								

echo $prosperAdmin->checkbox( 'prosper_inserter_posts', __( '<strong style="font-size:14px;">Add ContentInsert to All Posts</strong>', 'prosperent-suite' ), true );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->checkbox( 'prosper_inserter_pages', __( '<strong style="font-size:14px;">Add ContentInsert to All Pages</strong>', 'prosperent-suite' ), true );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->radio( 'prosper_inserter', array('top'=> 'Above', 'bottom'=> 'Below'), __( '<strong style="font-size:14px;">Insert Above or Below content</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->radio( 'prosper_insertView', array('grid'=> 'Grid', 'list'=> 'List/Detail', 'pc' => 'Price Comparison'), __( '<strong style="font-size:14px;">View of ContentInserts</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';
	
echo $prosperAdmin->textinput( 'PI_Limit', __( '<strong style="font-size:14px;">Number of Products to Insert</strong>', 'prosperent-suite' ), '', '', 'prosper_textinputsmall' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

/*echo $prosperAdmin->checkbox( 'contentAnalyzer', __( 'Use Content Analyzer for Query', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span><strong>Use our Content Anaylzer to come up with a query for the Product Inserts. <br>Helpful if the titles are not adding products.</strong></span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';*/

echo $prosperAdmin->textinput( 'prosper_inserter_negTitles', __( '<strong style="font-size:14px;">Words to exclude from Titles</strong>', 'prosperent-suite' ), '', 'Seperate by commas.' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

$prosperAdmin->adminFooter();