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
global $prosper_admin;
$options = $prosper_admin->get_option('prosper_autoLinker');
$prosper_admin->admin_header( __( 'Auto-Linker Settings', 'prosperent-suite' ), true, 'prosperent_linker_options', 'prosper_autoLinker' );

echo '<p class="prosper_settingDesc">' . __( 'The AutoLinker is a useful tool for to link words, brands, or products to either the search page or straight to the merchant. <br><br>The Auto-Linker has two different ways to link.. <br><br>Go to <a href="http://wordpress.prosperentdemo.com/auto-comparer/">WordPress Prosperent Demo: Auto-linker</a> for more information and to see how it looks.', 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Turn on the Auto-Linker...', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Enable_AL', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Matching Settings', 'prosperent-suite' ) . '</h2>';								
echo $prosper_admin->checkbox( 'Auto_Link_Comments', __( 'Do you want to match text inside comments?', 'prosperent-suite' ) );
echo '<p class="prosper_descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Text to Highlight and the Query to Match to that Text', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->hidden( 'LinkAmount' );

if ($options['LinkAmount'])
{
	echo '<div id="linkCarrier" style="margin-left:20px;">';
	for ($i = 0; $i < $options['LinkAmount']; $i++)
	{
		echo '<span id="ALFields' . $i . '">';
		echo $prosper_admin->textinputinline( 'Match', __( 'Match', 'prosperent-suite' ), $i ); 
		echo $prosper_admin->textinputinline( 'Query', __( 'Query', 'prosperent-suite' ), $i );
		echo $prosper_admin->textinputinline( 'PerPage', __( 'Links per page?', 'prosperent-suite' ), $i );
		echo $prosper_admin->checkboxinline( 'LTM', __( 'Link to merchant?', 'prosperent-suite' ), false, $i );
		echo $prosper_admin->checkboxinline( 'Case', __( 'Case Sensitive?', 'prosperent-suite' ), false, $i );
		echo '<a style="margin-left:10px;" onClick="deleteMyParent(this.parentNode);" class="button-secondary" href="' . admin_url( 'admin.php?page=prosper_autoLinker&delete=' . $i . '&nonce='. wp_create_nonce( 'prosper_delete_setting' )) . '">' . __( 'Delete', 'prosperent-suite' ) . '</a>';
		echo '</span>';
		echo '<br class="clear" />';
	}
	
	echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
	echo '</div>';
}
		/*	?>
			<div class="submit"><input type="submit" onClick="location.href = '<?php echo admin_url( 'admin.php?page=prosper_autoLinker&add&nonce='. wp_create_nonce( 'prosper_add_setting' )); ?>';" class="button-primary" name="submit" value="<?php _e( "Add New", 'prosperent-suite' ); ?>"/></div>
			<?php */
echo '<a style="margin-left:24px; margin-top:4px;" class="button-secondary" href="' . admin_url( 'admin.php?page=prosper_autoLinker&add&nonce='. wp_create_nonce( 'prosper_add_setting' )) . '">' . __( 'Add New', 'prosperent-suite' ) . '</a><span style="font-weight:bold; padding-left:8px;font-size:1.1em;">More Links = More Money</span>';

echo '<p class="prosper_desc">' . __( '<br>Define text and the query in the field above. Follow this format:<br>
									<strong>Match</strong>: Word to be matched in your content.</br>
									<strong>Query</strong>: Word to be used as the query, if none is entered, it will use the matched word as the query.</br>
									<strong>Links per Page</strong>: Amount of times to link matched word. Matches the first appearances of the word. If no limit is given, it will match 5 instances of the word.</br>
									<strong>Link to Merchant</strong>: Skips the Product Search and goes stright to the best converting merchant.</br></br>								
								<strong>Example</strong>: If you match the word shoes to Nike shoes, with 2 links per page and checked link to merchant then you would see two instances of the word shoes linked to a query of Nike Shoes, that goes stright to the best converting merchant.<br><br>
								
								List more specific matches first. For example, if you want to link both "shoes" and "Nike shoes", put "Nike shoes" first. Otherwise, "shoes" will match first, preventing "Nike shoes" from being found.', 'prosperent-suite' ) . '</p>';
							
do_action( 'prosper_autoLinker' );
$prosper_admin->admin_footer();