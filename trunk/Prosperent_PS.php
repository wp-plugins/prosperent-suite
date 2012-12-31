<?php
if (!class_exists('Prosper_Store'))
{
	require_once('PS_Widget.php');
	require_once('Prosperent.php');
	
	class Prosper_Store extends Prosperent_Suite
	{
		public function __construct()
		{
			add_action('wp_enqueue_scripts', array($this, 'prospere_stylesheets'));
			add_shortcode('prosper_store', array($this, 'store_shortcode'));
			add_shortcode('prosper_search', array($this, 'search_shortcode'));
			add_action('prospere_header', array($this, 'Prospere_Search'));
		}
				
		public function prospere_stylesheets()
		{
			global $wp_styles;

			// Product Search CSS for results and search
			wp_register_style( 'prospere_main_style', plugins_url('/css/productSearch.css', __FILE__) );
			wp_enqueue_style( 'prospere_main_style' );

			// Product Search CSS for IE7, a few changes to align objects
			wp_enqueue_style('prospere_IE_7', plugins_url('/css/productSearch-IE7.css', __FILE__));
			$wp_styles->add_data('prospere_IE_7', 'conditional', 'IE 7');
		}

		public function store_shortcode()
		{
			ob_start();
			include(plugin_dir_path(__FILE__) . 'products.php');
			$store = ob_get_clean();
			return $store;
		}
		
		public function search_shortcode()
		{		
			$options = $this->options();
			?>
			<div style="width:200px;<?php echo $options['Additional_CSS']; ?>">
				<form id="searchform" method="GET" action="<?php echo !$options['Base_URL'] ?  (!$options['Parent_Directory'] ? '/products' : '/' . $options['Parent_Directory'] . '/products') : '/' . $options['Base_URL']; ?>">
					<input class="field" type="text" name="q" id="s" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>">
					<input class="submit" type="submit" id="searchsubmit" value="Search">
				</form>
			</div>
			<?php
		}

		public function Prospere_Search()
		{
			$options = $this->options();
			?>
			<form id="search" method="GET" action="<?php echo !$options['Base_URL'] ?  (!$options['Parent_Directory'] ? '/products' : '/' . $options['Parent_Directory'] . '/products') : '/' . $options['Base_URL']; ?>">
				<table>
					<tr>
						<?php
						// if $logo_image is set to TRUE, this statement will output the Prosperent logo before the input box
						if ($options['Logo_Image'])
						{
							?>
							<td class="image"><a href="http://prosperent.com" title="Prosperent Search"> <img src="<?php echo plugins_url('/img/logo_small.png', __FILE__); ?>" /> </a></td>
							<style type=text/css>
								#search-input {
									margin-bottom:5px;
								}
							</style>
							<?php
						}
						if ($options['Logo_imageSmall'])
						{
							?>
							<td class="image"><a href="http://prosperent.com" title="Prosperent"> <img src="<?php echo plugins_url('/img/logo_smaller.png', __FILE__); ?>"/> </a></td>
							<style type=text/css>
								#branding img {
									margin-bottom:6px;
								}
							</style>
							<?php
						}
						?>
						<td>
							<table id="search-input" cellspacing="0">
								<tr>
									<td class="srchBoxCont" nowrap>
										<input class="srch_box" type="text" name="q" placeholder="<?php echo !$options['Search_Bar_Text'] ? 'Search Products' : $options['Search_Bar_Text']; ?>">
									</td>
									<td nowrap style="vertical-align:middle;">
										<input class="submit" type="submit" id="searchsubmit" value="Search">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</form>
			<?php
		}
	}

	new Prosper_Store();

	function prospere_header()
	{
		do_action('prospere_header');
	}

	add_action('wp_title', 'prosper_title', 10, 3);

	function prosper_title($post_title, $sep, $seplocation) 
	{
		$options = Prosper_Store::options();
	
		if (is_singular() && !$_GET['q'] && $options['Starting_Query'])
		{
			$post_title = ucwords($options['Starting_Query'])  . ' | ';
		}
		else if (is_singular() && $_GET['q'])
		{
			$post_title = ucwords($_GET['q'])  . ' | ';
		}
		else
		{
			$post_title = $post_title;
		}

		return $post_title;
	}
	
}