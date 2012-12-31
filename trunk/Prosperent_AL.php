<?php
if (!class_exists('Prosper_autoLinker'))
{
	require_once('prosperent_suite.php');

	class Prosper_autoLinker extends Prosperent_Suite
	{
		public function __construct()
		{
			if(is_admin())
			{
				add_action('admin_print_footer_scripts', array($this, 'qTagsButton'));
				add_action('admin_init', array($this, 'autoLinker_custom_add'));

			}
			else
			{
				add_shortcode('linker', array($this, 'linker_shortcode'));
			}
		}

		public function linker_shortcode($atts, $content = null)
		{
			$options = $this->options();
			$target   = $options['Target'] ? '_blank' : '_self';
			$sub_dir  = $options['Parent_Directory'];
			$base_url = $options['Base_URL'];

			extract(shortcode_atts(array(
				"to" => $sub_dir . !$base_url ? '/product?q=' : $base_url . '?q=',
				"q"  => $q
			), $atts));

			$query = !$q ? $content : $q;

			// Remove links within links
			$content = strip_tags($content);
			$query = strip_tags($query);

			return '<a href="' . $to . urlencode($query) . '" TARGET="' . $target . '">' . $content . '</a>';
		}


		
		public function autoLinker_custom_add()
		{
			// Add only in Rich Editor mode
			if (get_user_option('rich_editing') == 'true')
			{
				add_filter('mce_external_plugins', array($this, 'autoLinker_tiny_register'));
				add_filter('mce_buttons', array($this, 'autoLinker_tiny_add'));
			}
		}

		public function qTagsButton()
		{
			?>
			<script type="text/javascript">
				QTags.addButton('auto-linker', 'auto-linker', '[linker]', '[/linker]', 0);
			</script>
			<?php
		}

		public function autoLinker_tiny_add($buttons)
		{
			array_push($buttons, "|", "linker");
			return $buttons;
		}

		public function autoLinker_tiny_register($plugin_array)
		{
			$plugin_array["linker"] = plugin_dir_url(__FILE__) . 'js/button.js';
			return $plugin_array;
		}
	}

	new Prosper_autoLinker();
}
