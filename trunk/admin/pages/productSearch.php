<?php
global $prosper_admin;
$prosper_admin->admin_header( __( 'Product Search Settings', 'prosperent-suite' ), true, 'prosperent_products_options', 'prosper_productSearch' );

echo '<p class="settingDesc">' . __( 'The Product Search is the center of the Shop. This will allow you to run a store on your WordPress blog. Play around with the following settings to alter the look of your own personal store. <br><br>Go to <a href="http://wordpress.prosperentdemo.com/products/">WordPress Prosperent Demo: The Shop</a> for more information and to see how it runs.', 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Turn on The Store...', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Enable_PPS', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Allow My Visitors to Search for... ', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Product_Endpoint', __( 'Products', 'prosperent-suite' ) );
echo $prosper_admin->selectCountry( 'Country', __( 'From ', 'prosperent-suite' ), array( 'US' => __( 'US', 'prosperent-suite' ), 'UK' => __( 'UK', 'prosperent-suite' ), 'CA' => __( 'Canada', 'prosperent-suite' ) ) );

echo $prosper_admin->checkbox( 'Coupon_Endpoint', __( 'Coupons', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->checkbox( 'Celebrity_Endpoint', __( 'Celebrity Products', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->checkbox( 'Local_Endpoint', __( 'Local Deals', 'prosperent-suite' ) );
echo $prosper_admin->geoCheckbox( 'Geo_Locate', __( 'Turn on Geo-Location', 'prosperent-suite' ), '', '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Gets User Location Data from their IP address to use with Local Deals</span></a>'  );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

//echo $prosper_admin->checkbox( 'Travel_Endpoint', __( 'Travel Deals', 'prosperent-suite' ) );
//echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Set Limits...', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->textinput( 'Api_Limit', __( 'Number of results', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>API Limit (Max = 1000)</span></a>' );
echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Pagination_Limit', __( 'Results per page', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Pagination Limit</span></a>' );
echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Do you want to allow filtering of the results?', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Enable_Facets', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Merchant_Facets', __( 'Amount of Merchants in filter bar', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Brand_Facets', __( 'Amount of Brands in filter bar', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Negative_Merchant', __( 'Want to hide some merchants from results?', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span><strong>Seperate by commas.</strong> <br>Negative Merchant Filter.</span></a>' );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Negative_Brand', __( 'Want to hide some brands from results?', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span><strong>Seperate by commas.</strong> <br>Negative Brand Filter.</span></a>' );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Positive_Merchant', __( 'Want to only show certain merchants in results?', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span><strong>Seperate by commas.</strong> <br>Positive Merchant Filter.</span></a>' );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Positive_Brand', __( 'Want to only show certain brands in results?', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span><strong>Seperate by commas.</strong> <br>Positive Brand Filter.</span></a>' );
echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'How do you want your results ordered?', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->select( 'Default_Sort', __( 'Sort Results By', 'prosperent-suite' ), array( 'rel' => __( 'Relevancy', 'prosperent-suite' ), 'desc' => __( 'Price: High to Low', 'prosperent-suite' ), 'asc' => __( 'Price: Low to High', 'prosperent-suite' ) ) );
echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Default Queries', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->textinput( 'Starting_Query', __( 'Products Query', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>If no query has been given, this will be used. If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="descb">' . __( ".", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Coupon_Query', __( 'Coupons Query', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Default query for coupons. If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="descb">' . __( ".", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Celebrity_Query', __( 'Celebrity Query (Celebrity Name)', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Default query for celebrity. If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="descb">' . __( ".", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->textinput( 'Local_Query', __( 'Local Query (City, State or State)', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Default query for local, either "city, state" or "state". If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="descb">' . __( ".", 'prosperent-suite' ) . '</p>';

//echo $prosper_admin->textinput( 'Travel_Query', __( 'Travel Query (Destination)', 'prosperent-suite' ), '', '<a href="#" class="tooltip"><img border="0" src="' . PROSPER_URL . '/img/help.png"><span>Default query for travel. If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
//echo '<p class="descb">' . __( ".", 'prosperent-suite' ) . '</p>';



$prosper_admin->admin_footer();
