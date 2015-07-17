<script>
jQuery(function(){
	document.getElementById('prosper_insertView-pc').checked?jQuery("#prosperpc").css("display","block"):jQuery("#prodImageType").css("display","none");
	jQuery("#prosper_insertView-grid, #prosper_insertView-pc, #prosper_insertView-list").change(function () {
    	document.getElementById('prosper_insertView-pc').checked?jQuery("#prosperpc").css("display","block"):jQuery("#prodImageType").css("display","none");
    	document.getElementById('prosper_insertView-grid').checked || document.getElementById('prosper_insertView-list').checked ?jQuery("#prosperpc").css("display","none"):jQuery("#prodImageType").css("display","block");
        });
});
</script>
<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'ProsperInsert Settings', 'prosperent-suite' ), true, 'prosperent_compare_options', 'prosper_autoComparer' );

echo '<h1 style="font-size:23px;max-width:876px;font-weight:300;padding:0 15px 4px 0;margin-top:15px;line-height:29px;">Insert Products into All Posts and/or Pages.</h1>';
echo '<p class="prosper_settingDesc" style="font-size:14px;">' . __( 'Uses the PagePost titles to create a ProsperInsert above or below the content for all posts/pages.<br>You can also choose words to exclude from page titles.<br><br>You can edit products on a page/post by using gear icon in the visual editor and clicking "Edit ProsperInsert Products".', 'prosperent-suite' ) . '</p>';						

echo $prosperAdmin->checkbox( 'prosper_inserter_posts', __( '<strong style="font-size:14px;">Add ProsperInsert to All Posts</strong>', 'prosperent-suite' ), true );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->checkbox( 'prosper_inserter_pages', __( '<strong style="font-size:14px;">Add ProsperInsert to All Pages</strong>', 'prosperent-suite' ), true );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->radio( 'prosper_inserter', array('top'=> 'Above', 'bottom'=> 'Below'), __( '<strong style="font-size:14px;">Insert Above or Below content</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->radio( 'prosper_insertView', array('grid'=> 'Grid', 'list'=> 'List/Detail', 'pc' => 'Price Comparison'), __( '<strong style="font-size:14px;">Automated ProsperInsert View</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo '<div id="prosperpc" style="display:none;">';
echo $prosperAdmin->radio( 'prosper_imageType', array('original'=> 'Original Logo', 'black'=> 'Black Logo', 'white' => 'White Logo'), __( '<strong style="font-size:14px;">Price Comparison Image</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';
echo '</div>';

echo $prosperAdmin->checkbox( 'Link_to_Merc', __( '<strong style="font-size:14px;">Link to Merchant</strong>', 'prosperent-suite' ), true);
echo '<p class="prosper_desc" style="font-weight:600;">' . __( "Will Link to the Shop if not checked.", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'PI_Limit', __( '<strong style="font-size:14px;">Number of Products to Insert</strong>', 'prosperent-suite' ), '', '', 'prosper_textinputsmall' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

/*echo $prosperAdmin->checkbox( 'contentAnalyzer', __( 'Use Content Analyzer for Query', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span><strong>Use our Content Anaylzer to come up with a query for the Product Inserts. <br>Helpful if the titles are not adding products.</strong></span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';*/

echo $prosperAdmin->textinput( 'prosper_inserter_negTitles', __( '<strong style="font-size:14px;">Words to Ignore from Titles</strong>', 'prosperent-suite' ), '', 'Seperate by commas.' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

$prosperAdmin->adminFooter();