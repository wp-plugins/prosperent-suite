<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'Product Search Settings', 'prosperent-suite' ), true, 'prosperent_products_options', 'prosper_productSearch' );

echo '<p class="prosper_settingDesc">' . __( 'The Product Search is the center of the Shop. This will allow you to run a store on your WordPress blog. Play around with the following settings to change the look of your store. <br><br>Go to <a href="http://wordpress.prosperentdemo.com/products/">WordPress Prosperent Demo: The Shop</a> for more information and to see how it runs.', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Turn on The Store...', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Enable_PPS', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Allow My Visitors to Search for... ', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Product_Endpoint', __( 'Products', 'prosperent-suite' ) );
echo $prosperAdmin->select( 'Country', __( 'From ', 'prosperent-suite' ), array( 'US' => __( 'US', 'prosperent-suite' ), 'UK' => __( 'UK', 'prosperent-suite' ), 'CA' => __( 'Canada', 'prosperent-suite' ) ), '', '', 'prosper_selectCountry' );
echo $prosperAdmin->textinput( 'prodLabel', __( 'Products Label', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>Changes the headline above the store when active. Default is "Products".</span></a>', 'prosper_textinputsmallindent');
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Coupon_Endpoint', __( 'Coupons', 'prosperent-suite' ) );
echo $prosperAdmin->textinput( 'coupLabel', __( 'Coupons Label', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>Changes the headline above the store when active. Default is "Coupons".</span></a>', 'prosper_textinputsmallindent');
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Celebrity_Endpoint', __( 'Celebrity Products', 'prosperent-suite' ) );
echo $prosperAdmin->textinput( 'celeLabel', __( 'Celebrity Products Label', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>Changes the headline above the store when active. Default is "Coupons".</span></a>', 'prosper_textinputsmallindent');
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Local_Endpoint', __( 'Local Deals', 'prosperent-suite' ) );
echo $prosperAdmin->checkbox( 'Geo_Locate', __( 'Turn on Geo-Location', 'prosperent-suite' ), false, '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>Gets User Location Data from their IP address to use with Local Deals</span></a>', 'prosper_geocheckbox'  );
echo $prosperAdmin->textinput( 'localLabel', __( 'Local Deals Label', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>Changes the headline above the store when active. Default is "Local Deals".</span></a>', 'prosper_textinputsmallindent');
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Which view do you want for the products page...', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->radio( 'Product_View', array( 'grid' => __( 'Grid', 'prosperent-suite' ), 'list' => __( 'List', 'prosperent-suite' )), '' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Set Limits...', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->textinput( 'Api_Limit', __( 'Number of results', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>API Limit (Max = 1000)</span></a>', 'prosper_textinputsmall' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Pagination_Limit', __( 'Results per page', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>Amount of products shown per page.</span></a>', 'prosper_textinputsmall' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Same_Limit', __( 'Limit for Same Brand/Merchant Products', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>This limit will set the desired amount for the "Other Products from Brand/Merchant" on the individual product pages.</span></a>', 'prosper_textinputsmall' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Similar_Limit', __( 'Limit for Similar Products', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>This limit will set the desired amount for the "Similar Products" on the individual product pages.</span></a>', 'prosper_textinputsmall' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Do you want to allow filtering of the results?', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Enable_Facets', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Merchant_Facets', __( 'Number of facets to show for each category.', 'prosperent-suite' ), '', '', 'prosper_textinputsmall'  );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Negative_Merchant', __( 'Want to hide some merchants from results?', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span><strong>Seperate by commas.</strong> <br>Negative Merchant Filter.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Negative_Brand', __( 'Want to hide some brands from results?', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span><strong>Seperate by commas.</strong> <br>Negative Brand Filter.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Positive_Merchant', __( 'Want to only show certain merchants in results?', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span><strong>Seperate by commas.</strong> <br>Positive Merchant Filter.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Positive_Brand', __( 'Want to only show certain brands in results?', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span><strong>Seperate by commas.</strong> <br>Positive Brand Filter.</span></a>' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Default Queries', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->textinput( 'Starting_Query', __( 'Products Query', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>If no query has been given, this will be used. If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="prosper_desc">' . __( ".", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Coupon_Query', __( 'Coupons Query', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>Default query for coupons. If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="prosper_desc">' . __( ".", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Celebrity_Query', __( 'Celebrity- Celeb Name', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>Default query for celebrity. If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="prosper_desc">' . __( ".", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Local_Query', __( 'Local- City, State or State', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><img border="0" src="' . PROSPER_IMG . '/help.png"><span>Default query for local, either "city, state" or "state". If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="prosper_desc">' . __( ".", 'prosperent-suite' ) . '</p>';

$prosperAdmin->adminFooter();
