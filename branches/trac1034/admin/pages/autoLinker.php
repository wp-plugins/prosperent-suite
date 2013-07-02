<?php
global $prosper_admin;
$prosper_admin->admin_header( __( 'Auto-Linker Settings', 'prosperent-suite' ), true, 'prosperent_linker_options', 'prosper_autoLinker' );

echo '<p class="settingDesc">' . __( 'The AutoLinker is a useful tool for to link words, brands, or products to either the search page or straight to the merchant. <br><br>The Auto-Linker has two different ways to link.. <br><br>Go to <a href="http://wordpress.prosperentdemo.com/auto-comparer/">WordPress Prosperent Demo: Auto-linker</a> for more information and to see how it looks.', 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Turn on the Auto-Linker...', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->checkbox( 'Enable_AC', __( '<strong>Yes!</strong>', 'prosperent-suite' ) );
echo '<p class="descb">' . __( "", 'prosperent-suite' ) . '</p>';

echo '<h2>' . __( 'Text to Highlight and the Query to Match to that Text', 'prosperent-suite' ) . '</h2>';
echo $prosper_admin->textarea( 'Auto_Link', __( 'Text and Query', 'prosperent-suite' ) );
echo '<p class="descb">' . __( 'Define text and the query in the field above. Follow this format:<br>
                                <strong>"text" => "query (Optional)" </strong><br>
                                For example: <br>
								<strong>shoes => Nike shoes</strong><br>
								This would a link the word shoes to the Product Search as the query Nike shoes.
								<br>

								The query parameter is optional so you can leave it as just the text as follows:<br>
								<strong>"text" </strong><br>
								For example: <br>
								<strong>shoes</strong><br>
								This would a link the word shoes to the Product Search as the query shoes.
								<br>
								List more specific matches first. For example, if you want to link both "shoes" and "Nike shoes", put "Nike shoes" first. Otherwise, "shoes" will match first, preventing "Nike shoes" from being found.', 'prosperent-suite' ) . '</p>';
							
echo '<h2>' . __( 'Auto-Linker Matching Settings', 'prosperent-suite' ) . '</h2>';								
echo $prosper_admin->checkbox( 'Auto_Link_Comments', __( 'Do you want Auto-Linker to match text inside comments?', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosper_admin->checkbox( 'Case_Sensitive', __( 'Do you want Auto-Linker to be case sensitive?', 'prosperent-suite' ) );
echo '<p class="desc">' . __( "", 'prosperent-suite' ) . '</p>';

do_action( 'prosper_autoLinker' );
$prosper_admin->admin_footer();