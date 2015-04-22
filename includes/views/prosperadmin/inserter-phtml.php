<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'ProsperInsert Settings', 'prosperent-suite' ), true, 'prosperent_compare_options', 'prosper_autoComparer' );

echo '<p class="prosper_settingDesc" style="font-size:14px;">' . __( 'ProsperInsert is a great tool for anyone looking to promote a product, and it makes it very easy to do so. To use the ProsperInsert you can either:<br><br>&bull; Use the Content Inserter below to automatically add products to any page or post or...<br>&bull; Add a ProsperInsert to any page/post while editing it using the shortcode within the <strong>Gear dropdown</strong><br>&bull; Add ProsperInsert as a <strong>widget</strong>.<br><br>Go to <a href="http://wordpress.prosperentdemo.com/prodinsert/">WordPress Prosperent Demo: ProsperInsert</a> for more information and to see how it looks and works.', 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->hidden( 'Enable_AC');

echo '<table><tr><td><img src="' . PROSPER_IMG . '/adminImg/Content Inserter.png"/></td><td><h1 style="margin-left:8px;display:inline-block;font-size:34px;">ContentInsert</h1></td></tr></table><div style="clear:both"></div>';
echo '<p class="prosper_settingDesc" style="border:none;">' . __( 'Insert Products into All Posts/Pages.<br>This uses the Page/Post titles to create a ProsperInsert above or below the content for all posts/pages.<br>You can also choose words to exclude from page titles.<br>For example, if you use review in the titles you can exclude it from the ProsperInsert query by inserting that below.', 'prosperent-suite' ) . '</p>';								

echo $prosperAdmin->checkbox( 'prosper_inserter_posts', __( 'Add ProsperInsert to <strong>All</strong> Posts?', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'prosper_inserter_pages', __( 'Add ProsperInsert to <strong>All</strong> Pages?', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->radio( 'prosper_inserter', array('top'=> 'Above', 'bottom'=> 'Below'), __( 'Insert <strong>Above</strong> or <strong>Below</strong> content?', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->radio( 'prosper_insertView', array('grid'=> 'Grid', 'list'=> 'List'), __( 'Which view do you want to use?', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

$options = get_option('prosper_autoComparer');

if($options['prosper_insertView'] == 'grid' || !isset($options['prosper_insertView']))
{
	echo $prosperAdmin->textinput( 'prosper_insertGridImage', __( 'Enter <strong>Grid</strong> Image Width', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Only changes the size for <strong>grid</strong> content inserter product images.</span></a>', 'prosper_textinputsmall');
	echo '<p class="prosper_desc">' . __( "Defaults to 200.", 'prosperent-suite' ) . '</p>';
}
	
echo $prosperAdmin->textinput( 'PI_Limit', __( 'Number of Products to Insert', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>ProsperInsert of Page/Post Limit</span></a>', 'prosper_textinputsmall' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

/*echo $prosperAdmin->checkbox( 'contentAnalyzer', __( 'Use Content Analyzer for Query', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span><strong>Use our Content Anaylzer to come up with a query for the Product Inserts. <br>Helpful if the titles are not adding products.</strong></span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';*/

echo $prosperAdmin->textinput( 'prosper_inserter_negTitles', __( 'Words to exclude from Titles', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span><strong>Seperate by commas.</strong></span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Link_to_Merc', __( 'Link to Merchant', 'prosperent-suite' ), false, '',  '<a href="#" class="prosper_tooltip"><span>This will only change what the Title and Image link to. The Visit Store button will always link to the merchant.</span></a>' );
echo '<p class="prosper_desc">' . __( "Note: The ProsperInsert will always link to the Merchant if the Product Store is disabled.", 'prosperent-suite' ) . '</p>';

$prosperAdmin->adminFooter();