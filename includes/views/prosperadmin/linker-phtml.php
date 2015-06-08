<script type="text/javascript">
jQuery(function(){jQuery(document).ready(function(){jQuery("#prosper-conf").data("serialize",jQuery("#prosper-conf").serialize())});jQuery(window).bind("beforeunload",function(a){if(jQuery("#prosper-conf").serialize()!=jQuery("#prosper-conf").data("serialize"))return"You have unsaved settings."})});function deleteMyParent(a){var b=document.getElementById("linkCarrier");window.setTimeout(function(){b.removeChild(a)},50);return!1};
<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$options = get_option('prosper_autoLinker');

$prosperAdmin->adminHeader( __( 'AutoLinker Settings', 'prosperent-suite' ), true, 'prosperent_linker_options', 'prosper_autoLinker' );

echo '<p class="prosper_settingDesc" style="font-size:15px;">' . __( 'The AutoLinker is useful to link phrase the Shop or to the merchant\'s page.<br><br><span style="font-size:18px;font-weight:bold;">List more specific matches first.</span><br> For example, if you want to link both "shoes" and "Nike shoes", put "Nike shoes" first. Otherwise, "shoes" will prevent "Nike shoes" from being found.<br><br>Go to <a href="http://wordpress.prosperentdemo.com/auto-linker/">WordPress Prosperent Demo: Auto-linker</a> for more information and to see how it looks.', 'prosperent-suite' ) . '</p>';
							
echo $prosperAdmin->hidden( 'LinkAmount' );

echo '<p class="prosper_desc">' . __( '<span style="font-weight:bold; font-size:1.5em;">More Links = More Money</span>', 'prosperent-suite' ) . '</p>';
if ($options['LinkAmount'])
{
	echo '<div id="linkCarrier" style="margin-left:20px;">';
	for ($i = 0; $i < $options['LinkAmount']; $i++)
	{
		echo '<span id="ALFields' . $i . '">';
		echo $prosperAdmin->textinputinline( 'Match', __( '<strong style="font-size:14px">Match</strong>', 'prosperent-suite' ), $i, 'Word to be matched.' ); 
		echo $prosperAdmin->textinputinline( 'Query', __( '<strong style="font-size:14px">Query</strong>', 'prosperent-suite' ), $i, 'Word to be used as the query, if empty, it will use the matched word as the query.' );
		echo $prosperAdmin->textinputinline( 'PerPage', __( '<strong style="font-size:14px;">Links Per Page</strong>', 'prosperent-suite' ), $i, '', 'Amount of times to link matched word. Matches the first appearances of the word. If no limit is given, it will match 5.', 'prosper_textinputinlinesmall' );
		//echo $prosperAdmin->checkboxinline( 'LTM', __( 'Link to merchant?', 'prosperent-suite' ), false, $i );
		echo $prosperAdmin->checkboxinline( 'Case', __( '<strong style="font-size:14px">Case Sensitive</strong>', 'prosperent-suite' ), false, $i );
		echo '<a style="margin-left:10px; vertical-align:baseline;" onClick="deleteMyParent(this.parentNode);" class="button-secondary" href="' . admin_url( 'admin.php?page=prosper_autoLinker&delete=' . $i . '&nonce='. wp_create_nonce( 'prosper_delete_setting' )) . '">' . __( 'Delete', 'prosperent-suite' ) . '</a>';
		echo '</span>';
		echo '<br class="clear" />';
	}
	
	echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';
	echo '</div>';
}


echo '<a style="margin-left:24px; vertical-align:baseline;" class="button-secondary" href="' . admin_url( 'admin.php?page=prosper_autoLinker&add&nonce='. wp_create_nonce( 'prosper_add_setting' )) . '">' . __( 'Add New', 'prosperent-suite' ) . '</a>';
echo '<p class="prosper_desc">' . __( '', 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'Auto_Link_Comments', __( '<strong style="font-size:16px">Match Inside Comments</strong>', 'prosperent-suite' ), true );
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p>';


							

$prosperAdmin->adminFooter();