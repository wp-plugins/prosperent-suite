<script type="text/javascript">
function deleteMyParent(el)
{
	var linkCarrier = document.getElementById("linkCarrier");
    window.setTimeout(function() {
        linkCarrier.removeChild(el);
    }, 50);

    return false;
}
</script>

<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$options = get_option('prosper_autoLinker');

$prosperAdmin->adminHeader( __( 'Auto-Linker Settings', 'prosperent-suite' ), true, 'prosperent_linker_options', 'prosper_autoLinker' );

echo '<p class="prosper_settingDesc">' . __( 'The AutoLinker is a useful tool for to link words, brands, or products to either the search page or straight to the merchant. <br><br>The Auto-Linker has two different ways to link.. <br><br>Go to <a href="http://wordpress.prosperentdemo.com/auto-comparer/">WordPress Prosperent Demo: Auto-linker</a> for more information and to see how it looks.', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Turn on the Auto-Linker...', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'Enable_AL', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Matching Settings', 'prosperent-suite' ) . '</h2>';								
echo $prosperAdmin->checkbox( 'Auto_Link_Comments', __( 'Do you want to match text inside comments?', 'prosperent-suite' ) );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Text to Highlight and the Query to Match to that Text', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->hidden( 'LinkAmount' );

if ($options['LinkAmount'])
{
	echo '<div id="linkCarrier" style="margin-left:20px;">';
	for ($i = 0; $i < $options['LinkAmount']; $i++)
	{
		echo '<span id="ALFields' . $i . '">';
		echo $prosperAdmin->textinputinline( 'Match', __( 'Match', 'prosperent-suite' ), $i ); 
		echo $prosperAdmin->textinputinline( 'Query', __( 'Query', 'prosperent-suite' ), $i );
		echo $prosperAdmin->textinputinline( 'PerPage', __( 'Links per page?', 'prosperent-suite' ), $i );
		echo $prosperAdmin->checkboxinline( 'LTM', __( 'Link to merchant?', 'prosperent-suite' ), false, $i );
		echo $prosperAdmin->checkboxinline( 'Case', __( 'Case Sensitive?', 'prosperent-suite' ), false, $i );
		echo '<a style="margin-left:10px; vertical-align:baseline;" onClick="deleteMyParent(this.parentNode);" class="button-secondary" href="' . admin_url( 'admin.php?page=prosper_autoLinker&delete=' . $i . '&nonce='. wp_create_nonce( 'prosper_delete_setting' )) . '">' . __( 'Delete', 'prosperent-suite' ) . '</a>';
		echo '</span>';
		echo '<br class="clear" />';
	}
	
	echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
	echo '</div>';
}

echo '<a style="margin-left:24px; vertical-align:baseline;" class="button-secondary" href="' . admin_url( 'admin.php?page=prosper_autoLinker&add&nonce='. wp_create_nonce( 'prosper_add_setting' )) . '">' . __( 'Add New', 'prosperent-suite' ) . '</a>';
echo '<p class="prosper_desc">' . __( '<br><strong>** Clicking the Add New button will delete any links that are un saved. Make sure to Save Settings before adding new links.</strong>', 'prosperent-suite' ) . '</p>';
echo '<p class="prosper_desc">' . __( '<span style="font-weight:bold; font-size:1.5em;">More Links = More Money</span>
									<br>Define text and the query in the field above. Follow this format:<br>
									<strong>Match</strong>: Word to be matched in your content.</br>
									<strong>Query</strong>: Word to be used as the query, if none is entered, it will use the matched word as the query.</br>
									<strong>Links per Page</strong>: Amount of times to link matched word. Matches the first appearances of the word. If no limit is given, it will match 5 instances of the word.</br>
									<strong>Link to Merchant</strong>: Skips the Product Search and goes stright to the best converting merchant.</br></br>								
								<strong>Example</strong>: If you match the word shoes to Nike shoes, with 2 links per page and checked link to merchant then you would see two instances of the word shoes linked to a query of Nike Shoes, that goes stright to the best converting merchant.<br><br>
								
								List more specific matches first. For example, if you want to link both "shoes" and "Nike shoes", put "Nike shoes" first. Otherwise, "shoes" will match first, preventing "Nike shoes" from being found.', 'prosperent-suite' ) . '</p>';
							

$prosperAdmin->adminFooter();