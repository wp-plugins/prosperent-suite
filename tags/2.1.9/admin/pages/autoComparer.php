<?php
global $prosper_admin;
$prosper_admin->admin_header( __( 'Product Insert Settings', 'prosperent-suite' ), true, 'prosperent_compare_options', 'prosper_autoComparer' );

echo '<p class="prosper_settingDesc">' . __( 'The Product Insert is a great tool for anyone looking to promote a product, and makes it very easy to do so. <br><br>Go to <a href="http://wordpress.prosperentdemo.com/auto-comparer/">WordPress Prosperent Demo: Product Insert</a> for more information and to see how it looks and works.', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Turn on Product Insert...', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Enable_AC', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Insert Products into All Posts/Pages', 'prosperent-suite' ) . '</h2>';
echo '<p class="prosper_settingDesc" style="border:none;">' . __( 'This uses the Page/Post titles to create a Product Insert either above or below the content for all posts/pages.<br>You can also choose words to exclude from page titles.<br>For example, if you use review in the titles you can exclude it from the Product Insert query by inserting that below.', 'prosperent-suite' ) . '</p>';								

echo $prosper_admin->checkbox( 'prosper_inserter_posts', __( 'Add Product Insert to <strong>All</strong> Posts?', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->checkbox( 'prosper_inserter_pages', __( 'Add Product Insert to <strong>All</strong> Pages?', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->radio( 'prosper_inserter', array('top'=> 'Above', 'bottom'=> 'Below'), __( 'Insert <strong>Above</strong> or <strong>Below</strong> content?', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinputsmall( 'PI_Limit', __( 'Number of Products to Insert', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Product Insert of Page/Post Limit</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'prosper_inserter_negTitles', __( 'Words to exclude from Titles', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span><strong>Seperate by commas.</strong></span></a>' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Link to Merchant', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Link_to_Merc', __( '<strong>Yes</strong>', 'prosperent-suite' ), '', '',  '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>This will only change what the Title and Image link to. The Visit Store button will always link to the merchant.</span></a>' );
echo '<p class="prosper_desc">' . __( "Note: The Product Insert will always link to the Merchant if the Product Store is disabled.", 'prosperent-suite' ) . '</p>';

do_action( 'prosper_autoComparer' );
$prosper_admin->admin_footer();