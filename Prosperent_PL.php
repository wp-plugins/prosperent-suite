<?php
if (!class_exists('ProsperLinks'))
{
	require_once('Prosperent.php');
	require_once('prosperent_suite.php');

	class ProsperLinks extends Prosperent_Suite
	{		
		public function __construct()
		{
			add_action('wp_enqueue_scripts', array($this, 'prosperLinks_css'));
			add_action('wp_head', array($this, 'prosperLinks'));
		}
		
		public function prosperLinks_css() {
			global $wp_styles;

			// ProsperLinks CSS
			wp_register_style( 'prosperLinks_css', plugins_url('/css/prosperLinks.css', __FILE__) );
			wp_enqueue_style( 'prosperLinks_css' );
		}
		
		public function prosperLinks()
		{
			$options = $this->options();
			
			?>
			<script type="text/javascript">
				<!--
				 prosperent_pl_uid = <?php echo json_encode($options['UID']); ?>;
				 prosperent_pl_sid = <?php echo json_encode($options['SID']); ?>;
				 prosperent_pl_hoverBox = <?php echo json_encode($options['HoverBox']); ?>;
				 prosperent_pl_underline = <?php echo json_encode($options['Underline']); ?>;
				 prosperent_pl_limit = <?php echo json_encode($options['linkLimit']); ?>;
				 prosperent_pl_enableLinkAffiliation = <?php echo json_encode($options['linkAffiliation']); ?>;
				//-->
			</script>
			<script type="text/javascript" src="http://prosperent.com/js/plink.min.js"></script>
			<?php
		}
	}

	new ProsperLinks();
}
