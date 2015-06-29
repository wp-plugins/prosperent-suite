<script type="text/javascript">
function deleteMyParent(a){var b=document.getElementById("linkCarrier");window.setTimeout(function(){b.removeChild(a)},50);return!1};
</script>
<?php 
wp_register_script( 'autoSuggest', PROSPER_JS . '/autosuggest.js', array('jquery', 'jquery-ui-autocomplete'), '3.1.7');
wp_enqueue_script( 'autoSuggest' );

require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$options = get_option('prosper_productSearch');
$optionsGen = get_option('prosperSuite');

if ($optionsAL = get_option('prosper_autoLinker'))
{ 
    foreach ($optionsAL as $i => $alOpt)
    {
        $options[$i] = $alOpt;
    }

    update_option('prosper_productSearch', $options);
    delete_option('prosper_autoLinker');
}

if ($optionsGen['gotoMerchantBypass'])
{
    $options['gotoMerchantBypass'] = $optionsGen['gotoMerchantBypass'];
    $optionsGen['gotoMerchantBypass'] = '';
    update_option('prosper_productSearch', $options);
    update_option('prosperSuite', $optionsGen);
}

$prosperAdmin->adminHeader( __( 'ProsperShop Settings', 'prosperent-suite' ), true, 'prosperent_products_options', 'prosper_productSearch' );
echo '<h1 style="font-size:23px;max-width:876px;font-weight:300;padding:0 15px 4px 0;margin-top:15px;line-height:29px;">Create a shop that gives your visitors access to products you can curate.</h1>';
echo '<p class="prosper_settingDesc" style="font-size:14px;">' . __( 'The store was automatically created for you.<br><br>Next step is to play around with the following settings to change the look of your store.<br><br><b style="font-size:14px;">*If you make the shop your static front page, follow this <a href="http://wordpress.prosperentdemo.com/prodstore/#prosperShopFaq">guide</a></b>.', 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->textinput( 'Starting_Query', __( '<strong style="font-size:14px">Starting Query</strong>', 'prosperent-suite' ), '', 'This will be used if no search query was provided. If not set and no query, the shop shows the No Results page which show Popular Products.' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->checkbox( 'Enable_Facets', __( '<strong style="font-size:14px">Show the merchants/brands</strong>', 'prosperent-suite' ), true );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->textinput( 'searchTitle', __( '<strong style="font-size:14px">Title Above Search Bar</strong>', 'prosperent-suite' ), '', 'Text to show above the search bar.');
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->checkbox( 'hideShopPageTitle', __( '<strong style="font-size:14px">Hide the Shop Page Title</strong>', 'prosperent-suite' ), true);
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->textinput( 'Pagination_Limit', __( '<strong style="font-size:14px">Per Page Limit</strong>', 'prosperent-suite' ), '', 'Number of products shown on each page.', 'prosper_textinputsmall' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->radio( 'Product_View', array( 'grid' => __( 'Grid', 'prosperent-suite' ), 'list' => __( 'List', 'prosperent-suite' )), __( '<strong style="font-size:14px">Shop View</strong>', 'prosperent-suite' ), '', 'The style to use for the shop.');
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->textinput( 'VisitStoreButton', __( '<strong style="font-size:14px">Visit Store Button Text</strong>', 'prosperent-suite' ), '', 'Number of products shown on each page.' );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->checkbox( 'gotoMerchantBypass', __( '<strong style="font-size:14px">Links Go To Merchant</strong>', 'prosperent-suite' ), true, '', 'Bypasses the Product Page.');
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo '<div style="margin:0 0 5px 20px;">';
echo '<label style="font-size:14px;font-weight:bold;display:block;">Use These Categories</label>';
echo '<input style="margin-top:4px;width:444px;" id="categories" type="text" class="ProsperCategories" placeholder="Enter Category"/>';
echo '<a style="margin-left:2px; vertical-align:baseline;" class="button-secondary" href="javascript:void(0);" onClick="addNewCategory();">Add</a>';

echo '<div id="prosperTotalResults" style="font-weight:bold;"></div>';

echo '<div style="margin:6px 0 5px 20px;width:100%;display:inline-block;"><ul style="margin:0;list-style:none;display:inline-block;width:100%" id="ProsperCategoryFilters">';
if ($options['ProsperCategories'])
{
    $options['ProsperCategories'] = rtrim($options['ProsperCategories'], '|');
    $categories = explode('|', $options['ProsperCategories']);
    foreach ($categories as $category)
    {
        echo '<li data-savedvalue="' . $category . '" class="ProsperCategories" data-filtype="ProsperCategories" style="float:left;margin:0;padding:6px;" onClick="removeFilter(this);"><span><a data-filtype="ProsperCategories" style="text-decoration:none;" href="javascript:void(0);">' . str_replace('_', ' ', $category) . ' <i class="fa fa-times"></i></a></span></li>';
    }  
}
echo '</ul></div>';

echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
echo '<label style="font-size:14px;font-weight:bold;display:block;">Hide Products from these Merchants</label>';
echo '<input style="margin-left:5px;margin-top:4px;" type="text" id="NegativeMerchant" placeholder="Enter Merchant Name" onKeyUp="addingNewMerchantFilter(this);"/>';
echo '<div style="margin:6px 0 5px 20px;width:100%;display:inline-block;"><ul style="margin:0;list-style:none;display:inline-block;width:100%" id="NegativeMerchantFilters"></ul></div>';
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<label style="font-size:14px;font-weight:bold;display:block;">Only Show Products from these Merchants</label>';
echo '<input style="margin-left:5px;margin-top:4px;" type="text" id="PositiveMerchant" placeholder="Enter Merchant Name" onKeyUp="addingNewMerchantFilter(this);"/>';

echo '<p class="prosper_desc" style="padding-left:6px;margin-bottom:0;">' . __( "<strong style='font-size:12px;color:red;'>When you enter merchant names here, these will be the ONLY merchants that will show up!</strong>", 'prosperent-suite' ) . '</p>';
echo '<p class="prosper_desc" style="padding-left:6px;margin-bottom:0;">' . __( "<strong style='font-size:12px;color:red;'>This could result in you missing out on new merchants and extra commissions.</strong>", 'prosperent-suite' ) . '</p>';
echo '<div style="margin:6px 0 5px 20px;width:100%;display:inline-block;"><ul style="margin:0;list-style:none;display:inline-block;width:100%" id="PositiveMerchantFilters"></ul></div>';

echo $prosperAdmin->hidden( 'ProsperCategories');
echo $prosperAdmin->hidden( 'PositiveMerchant');
echo $prosperAdmin->hidden( 'NegativeMerchant');
echo $prosperAdmin->hidden( 'Positive_Merchant');
echo $prosperAdmin->hidden( 'Negative_Merchant');
echo $prosperAdmin->hidden( 'refreshFilters');

if ($options['recentSearches'])
{
	echo '<p><label style="font-size:14px;font-weight:bold;">Recent Searches</label></p>';
	echo $prosperAdmin->hidden( 'numRecentSearch');
	echo '<div id="linkCarrier">';
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



echo $prosperAdmin->hidden( 'LinkAmount' );

echo '<p style="margin-top:18px;"><label style="font-size:14px;font-weight:bold;">Link phrases to your ProsperShop</label></p>';

if ($options['LinkAmount'])
{
    echo '<div id="linkCarrier" style="margin-left:20px;">';
    for ($i = 0; $i < $options['LinkAmount']; $i++)
    {
        echo '<span id="ALFields' . $i . '">';
        echo $prosperAdmin->textinputinline( 'Match', __( '<strong style="font-size:14px">Match</strong>', 'prosperent-suite' ), $i, '', 'Word to be matched.' );
        echo $prosperAdmin->textinputinline( 'Query', __( '<strong style="font-size:14px">Query</strong>', 'prosperent-suite' ), $i, '', 'Word to be used as the query, if empty, it will use the matched word as the query.' );
        echo $prosperAdmin->textinputinline( 'PerPage', __( '<strong style="font-size:14px;">Links Per Page</strong>', 'prosperent-suite' ), $i, '', 'Amount of times to link matched word. Matches the first appearances of the word. If no limit is given, it will match 5.', 'prosper_textinputinlinesmall' );
        echo $prosperAdmin->checkboxinline( 'Case', __( '<strong style="font-size:14px">Case Sensitive</strong>', 'prosperent-suite' ), true, $i );
		echo '<a style="margin-left:10px; vertical-align:baseline;" class="button-secondary" onClick="deleteMyParent(this.parentNode);"  href="' . admin_url( 'admin.php?page=prosper_productSearch&delete=' . $i . '&nonce='. wp_create_nonce( 'prosper_delete_setting' )) . '">' . __( 'Delete', 'prosperent-suite' ) . '</a>';
		echo '</span>';
        echo '<br class="clear" />';
    }

echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
	echo '</div>';
}


echo '<a style="margin-left:24px; vertical-align:baseline;" class="button-secondary" href="' . admin_url( 'admin.php?page=prosper_productSearch&add&nonce='. wp_create_nonce( 'prosper_add_setting' )) . '">' . __( 'Add New', 'prosperent-suite' ) . '</a>';
echo '<p class="prosper_desc">' . __( '', 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Auto_Link_Comments', __( '<strong style="font-size:14px">Match Inside Comments</strong>', 'prosperent-suite' ), true );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
echo '</div>';
$prosperAdmin->adminFooter();
