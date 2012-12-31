<?php
if (!class_exists('Performance_Ads'))
	{
	require_once('prosperent_suite.php');
	require_once('PA_Sidebar.php');
	require_once('PA_Footer.php');

	class Performance_Ads extends Prosperent_Suite
	{
		public function __construct()
		{
			add_action('performance_ads', array($this, 'Prosper_Perform_Ads'));
			add_action('wp_enqueue_scripts', array($this, 'prosperAds_css'));	
		}
		
		public function prosperAds_css()
		{
			global $wp_styles;

			// Performance Ad CSS for results and search
			wp_register_style('prosper_perform_css', plugins_url('/css/performance_ads.css', __FILE__));
			wp_enqueue_style('prosper_perform_css');
		}


		
		public function Prosper_Perform_Ads()
		{	
			$options = $this->options();

			?>
			<li class="firstpost_advert_container">
				<div class="firstpost_advert">
					<script type="text/javascript"><!--
						prosperent_pa_uid = <?php echo json_encode($options['UID']); ?>;
						prosperent_pa_height = 90;
						prosperent_pa_fallback_query = <?php echo json_encode($options['content_fallBack']); ?>;
						//-->
					</script>
					<script type="text/javascript" src="http://prosperent.com/js/ad.js"></script>
				</div>
			</li>
			<?php
		}
	}

	new Performance_Ads();

	function performance_ads() 
	{
		do_action('performance_ads');
	}
}