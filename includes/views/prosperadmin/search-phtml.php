<script type="text/javascript">function deleteMyParent(a){var b=document.getElementById("linkCarrier");window.setTimeout(function(){b.removeChild(a)},50);return!1};</script>
<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'ProsperShop Settings', 'prosperent-suite' ), true, 'prosperent_products_options', 'prosper_productSearch' );

echo '<p class="prosper_settingDesc"  style="font-size:14px;">' . __( 'ProsperShop is a store that gives visitors to your site access to products from over 4500 merchants.<br><br>The store was automatically created for you.<br><br>Next step is to play around with the following settings to change the look of your store. <br><br>Go to <a href="http://wordpress.prosperentdemo.com/prodstore/">WordPress Prosperent Demo: The Shop</a> for more information and to see how it runs.<br><br><b style="font-size:16px;">*If you make the shop your static front page, follow this <a href="http://wordpress.prosperentdemo.com/prodstore/#prosperShopFaq">guide</a></b>.', 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->hidden( 'Enable_PPS');

echo '<h2 class="prosper_h2">' . __( 'Allow My Visitors to Search for... ', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Product_Endpoint', __( 'Products', 'prosperent-suite' ) );
echo $prosperAdmin->select( 'Country', __( 'From ', 'prosperent-suite' ), array( 'US' => __( 'US', 'prosperent-suite' ), 'UK' => __( 'UK', 'prosperent-suite' ), 'CA' => __( 'Canada', 'prosperent-suite' ) ), '', '', 'prosper_selectCountry' );
echo $prosperAdmin->textinput( 'prodLabel', __( 'Products Label', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Changes the headline above the store when active. Default is "Products".</span></a>', 'prosper_textinputsmallindent');
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Coupon_Endpoint', __( 'Coupons', 'prosperent-suite' ) );
echo $prosperAdmin->textinput( 'coupLabel', __( 'Coupons Label', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Changes the headline above the store when active. Default is "Coupons".</span></a>', 'prosper_textinputsmallindent');
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Celebrity_Endpoint', __( 'Celebrity Products', 'prosperent-suite' ) );
echo $prosperAdmin->textinput( 'celeLabel', __( 'Celebrity Products Label', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Changes the headline above the store when active. Default is "Celebrity Products".</span></a>', 'prosper_textinputsmallindent');
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Local_Endpoint', __( 'Local Deals', 'prosperent-suite' ) );
echo $prosperAdmin->checkbox( 'Geo_Locate', __( 'Turn on Geo-Location', 'prosperent-suite' ), false, '', '<a href="#" class="prosper_tooltip"><span>Gets User Location Data from their IP address to use with Local Deals</span></a>', 'prosper_geocheckbox'  );
echo $prosperAdmin->textinput( 'localLabel', __( 'Local Deals Label', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Changes the headline above the store when active. Default is "Local Deals".</span></a>', 'prosper_textinputsmallindent');
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Set Results Limit...', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->textinput( 'Pagination_Limit', __( 'Results per page', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Amount of products shown per page.</span></a>', 'prosper_textinputsmall' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Which view do you want for the results pages...', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->radio( 'Product_View', array( 'grid' => __( 'Grid', 'prosperent-suite' ), 'list' => __( 'List', 'prosperent-suite' )), 'Product/Celebrity Results' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->radio( 'Coupon_View', array( 'grid' => __( 'Grid', 'prosperent-suite' ), 'list' => __( 'List', 'prosperent-suite' )), 'Coupon/Local Results' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Image Sizes', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->textinput( 'Grid_Img_Size', __( 'Enter <strong>Grid</strong> image width', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Only changes the size for <strong>grid</strong> product images. (does not change local/coupon images)<br><br>The image will be a square, so entering the width will be the same as the height. </span></a>', 'prosper_textinputsmall');
echo '<p class="prosper_desc">' . __( "Minimum is <strong>70</strong>", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Same_Img_Size', __( 'Enter Same/Similar Products image width', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Will change the image size on the product pages as well for the same/similar product sections.<br><br><br>The image will be a square, so entering the width will be the same as the height.</span></a>', 'prosper_textinputsmall');
echo '<p class="prosper_descb">' . __( "Minimum is <strong>70</strong>", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Do you want to allow filtering of the results?', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Enable_Facets', __( '<strong>Yes!</strong> Show the merchants and brands.', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Enable_Sliders', __( 'Enable Price Range and Percent Off Sliders', 'prosperent-suite' ) );
echo '<p class="prosper_desc">' . __( "May conflict with some themes.", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Merchant_Facets', __( 'Number of facets to show for each category.', 'prosperent-suite' ), '', '', 'prosper_textinputsmall'  );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Negative_Merchant', __( 'Want to hide some merchants from results?', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span><strong>Seperate by commas.</strong> <br>Negative Merchant Filter.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Negative_Brand', __( 'Want to hide some brands from results?', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span><strong>Seperate by commas.</strong> <br>Negative Brand Filter.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Positive_Merchant', __( 'Want to only show certain merchants in results?', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span><strong>Seperate by commas.</strong> <br>Positive Merchant Filter.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Positive_Brand', __( 'Want to only show certain brands in results?', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span><strong>Seperate by commas.</strong> <br>Positive Brand Filter.</span></a>' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Default Queries', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->textinput( 'No_Results_Categories', __( 'Filter by Categories on<br>Products No Results Page', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>If there are no results for a query, entering categories here will filter the trending products by those categories, helpful for niche sites. Will only work for <strong>Product</strong> searches.</br>May result is less Trend Products.</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Starting_Query', __( 'Products Query', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>If no query has been given, this will be used. If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Coupon_Query', __( 'Coupons Query', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Default query for coupons. If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Celebrity_Query', __( 'Celebrity- Celeb Name', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Default query for celebrity. If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Local_Query', __( 'Local- City, State or State', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>Default query for local, either "city, state" or "state". If no starting query is set, the store shows the <b>No Results</b> page which includes Top Products from Trends data</span></a>' );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Product Page Limits...', 'prosperent-suite' ) . '</h2>';
echo '<p class="prosper_desc">' . __( "Entering 0 or leaving blank will turn each section off.", 'prosperent-suite' ) . '</p>';
echo $prosperAdmin->textinput( 'Same_Limit', __( 'Same Brand Products', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>This limit will set the desired amount for the "Other Products from Brand" on the product pages.</span></a>', 'prosper_textinputsmall' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Same_Limit_Merchant', __( 'Same Merchant Products', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>This limit will set the desired amount for the "Other Products from Merchant" on the product pages.</span></a>', 'prosper_textinputsmall' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Similar_Limit', __( 'Similar Products', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>This limit will set the desired amount for the "Similar Products" on the product pages.</span></a>', 'prosper_textinputsmall' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

$options = get_option('prosper_productSearch');

echo $prosperAdmin->textinput( 'MCoupon_Limit', __( 'Coupons from Merchant', 'prosperent-suite' ), '', '<a href="#" class="prosper_tooltip"><span>This limit will set the desired amount for the "Merchant Coupons" on the product pages.</span></a>', 'prosper_textinputsmall' );
echo '<p class="' . ($options['numRecentSearch'] ? 'prosper_descb' : 'prosper_desc') . '">' . __( "", 'prosperent-suite' ) . '</p>';

if ($options['numRecentSearch'])
{
	echo '<p class="prosper_descb">' . __( ".", 'prosperent-suite' ) . '</p>';

	echo '<h2 class="prosper_h2">' . __( 'Recent Searches', 'prosperent-suite' ) . '</h2>';
	echo $prosperAdmin->hidden( 'numRecentSearch');
	echo '<div id="linkCarrier" style="margin-left:20px;">';
	for ($i = 0; $i < count($options['recentSearches']); $i++)
	{
		echo '<span id="ALFields' . $i . '">';
		echo $prosperAdmin->textinputnewinline( 'recentSearches', $i ); 
		echo '<a style="margin:3px 0 0 10px; vertical-align:baseline;" onClick="deleteMyParent(this.parentNode);" class="button-secondary" href="' . admin_url( 'admin.php?page=prosper_productSearch&deleteRecent=' . $i . '&nonce='. wp_create_nonce( 'prosper_delete_recent' )) . '">' . __( 'Delete', 'prosperent-suite' ) . '</a>';
		echo '</span>';
		echo '<br class="clear" />';
	}
	
	echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
	echo '</div>';
}
else
{
	echo '<p class="prosper_desc">' . __( ".", 'prosperent-suite' ) . '</p>';
}

$prosperAdmin->adminFooter();
