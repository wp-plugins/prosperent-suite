<?php
/*
Plugin Name: Prosperent Suite (Contains Performance Ads, Product Search, ProsperLinks, and Auto-Linker)
Description: Contains all Prosperent tools in one plugin, each one can be disabled if you don't want to use one.
Version: 1.0
Author: Prosperent Brandon
License: GPL2
*/

/*
    Copyright 2012  Prosperent Brandon  (email : brandon@prosperent.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists('Prosperent_Suite'))
{
	require_once('Prosperent.php');

	class Prosperent_Suite extends Prosperent_WP
	{
		public static $instance;

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->prosperent_suite();
			register_activation_hook(__FILE__, array($this, 'prosperent_store_install'));
			register_deactivation_hook(__FILE__, array($this, 'prosperent_store_remove'));
					
			$options = $this->get_options();

			if ($options['Enable_PA'])
			{
				require_once('Prosperent_PA.php');
			}
			if ($options['Enable_PL'])
			{
				require_once('Prosperent_PL.php' );
			}
			if ($options['Enable_AL'] && $options['Enable_PPS'])
			{
				require_once('Prosperent_AL.php' );
			}
			if ($options['Enable_PPS'])
			{
				require_once('Prosperent_PS.php' );
				$this->prosperent_store_install();
			}
			else 
			{
				$this->prosperent_store_remove();
			}
		}
		
		public function prosperent_suite()
		{
			// Be a singleton
			if (!is_null(self::$instance))
				return;

			parent::__construct('1.0', 'prosperent-suite', 'prosper', __FILE__, array());
			register_activation_hook(__FILE__, array(__CLASS__, 'activation'));
			self::$instance = $this;
		}

		public function options()
		{
			global $wpdb;
			$wpdb->hide_errors();
			$myrows = $wpdb->get_row("SELECT *
						FROM $wpdb->options
						WHERE option_name = 'prosper_prosperent_suite'", ARRAY_A);

			$options = unserialize($myrows['option_value']);
			return $options;
		}
		
		/**
		 * Handles activation tasks, such as registering the uninstall hook.
		 *
		 * @return void
		 */
		public function activation()
		{
			register_uninstall_hook(__FILE__, array(__CLASS__, 'uninstall'));
		}

		/**
		 * Initializes the plugin's configuration and localizable text variables.
		 *
		 * @return void
		 */
		public function load_config()
		{
			$this->name      = __('Prosperent Settings', $this->textdomain);
			$this->menu_name = __('Prosperent Settings', $this->textdomain);

			$this->config = array(
				'blank1' => array('input' => 'custom', 'label' => __('<strong style="font-size:14px; text-decoration:underline;"><p>Prosperent Settings</p></strong>', $this->textdomain),
				),	
				'UID'	=> array('input' => 'text',
					'label' => __('Enter your Prosperent User-ID', $this->textdomain)
				),
				'blank' => array('input' => 'custom', 'label' => __('<strong style="font-size:14px; text-decoration:underline;"><p>Enable or Disable Options</p></strong>', $this->textdomain)
				),	
				'Enable_PPS' => array('input' => 'checkbox', 'default' => true,
					'label' => __('Enable Product Search', $this->textdomain)
				),
				'Enable_PL' => array('input' => 'checkbox', 'default' => true,
					'label' => __('Enable ProsperLinks', $this->textdomain)
				),
				'Enable_PA' => array('input' => 'checkbox', 'default' => true,
					'label' => __('Enable Performance-Ads', $this->textdomain)
				),
				'Enable_AL' => array('input' => 'checkbox',  'default' => true,
					'label' => __('Enable Auto-Linker', $this->textdomain),
					'help'  => 'Will only work if Prosperent Product Store is enabled also.'
				),
				'blank2' => array('input' => 'custom', 'label' => __('<strong style="font-size:14px; text-decoration:underline;"><p>Product Search Settings</p></strong>', $this->textdomain)
				),	
				'Api_Key' => array('input' => 'text',
					'label' => __('Enter your API Key.', $this->textdomain)
				),
				'Enable_Facets' => array('input' => 'checkbox', 'default' => true,
					'label' => __('Enable facets', $this->textdomain)
				),
				'Api_Limit' => array('input' => 'text', 'default' => 50,
					'label' => __('Number of results (Max = 100)', $this->textdomain)
				),
				'Pagination_Limit' => array('input' => 'text', 'default' => 10,
					'label' => __('Results to display on each page', $this->textdomain)
				),
				'Default_Sort' => array('input' => 'select', 'datatype' => 'hash',
					'options' => array('relevance desc' => 'Relevancy', 'price desc' => 'Price: High to Low', 'price asc' => 'Price: Low to High'),
					'label' => __('Default sort method', $this->textdomain)
				),
				'Search_Bar_Text' => array('input' => 'text',
					'label' => __('Search Bar Placeholder Text', $this->textdomain),
					'help'  => 'Change what the search bar says as a placeholder.'
				),
				'Merchant_Facets' => array('input' => 'text', 'default' => 10,
					'label' => __('Number of merchants to display in primary facet list', $this->textdomain)
				),
				'Brand_Facets' => array('input' => 'text', 'default' => 10,
					'label' => __('Number of brands to display in primary facet list', $this->textdomain)
				),
				'Negative_Brand' => array('input' => 'text',
					'label' => __('Negative Brand Filter', $this->textdomain),
					'help'  => 'Brands to discard from results.'
				),
				'Negative_Merchant' => array('input' => 'text',
					'label' => __('Negative Merchant Filter', $this->textdomain),
					'help'  => 'Merchants to discard from results.'
				),
				'Starting_Query' => array('input' => 'text',
					'label' => __('Starting Query', $this->textdomain),
					'help'  => 'When first visited, this query will be used if one has not been given. If no starting query is set, it shows the no results page.'
				),
				'Base_URL' => array('input' => 'text',
					'label' => __('Base Url', $this->textdomain),
					'help'  => 'If you have a different URL from "<b>your-blog.com/product</b>" that you want the search query to go to.'
				),
				'Additional_CSS' => array('input' => 'text',
					'label' => __('Additional CSS for the shortcode search bar.', $this->textdomain)
				),
				'Parent_Directory' => array('input' => 'text',
					'label' => __('Parent Directory', $this->textdomain),
					'help'  => 'If your WP install has a sub-directory.'
				),
				'Logo_Image' => array('input' => 'checkbox',
					'label' => __('Logo Image', $this->textdomain),
					'help'  => '<b>Only for search bar in header.</b> Display the original sized Prosperent Logo. Size is 167px x 50px.'
				),
				'Logo_imageSmall' => array('input' => 'checkbox',
					'label' => __('Logo Image- Small', $this->textdomain),
					'help'  => '<b>Only for search bar in header.</b> Display the smaller Prosperent Logo. Size is 100px x 30px.'
				),				
				'blank3' => array('input' => 'custom', 'label' => __('<strong style="font-size:14px; text-decoration:underline;"><p>ProsperLink Settings</p></strong>', $this->textdomain)
				),	
				'SID' => array('input' => 'text',
					'label' => __('<strong>Optional.</strong> SID', $this->textdomain),
					'help'  => 'Used for commission tracking.'
				),
				'HoverBox' => array('input' => 'checkbox', 'default' => true,
					'label' => __('Enable or Disable the HoverBox', $this->textdomain)
				),
				'Underline' => array('input' => 'select', 'default' => 1, 'datatype' => 'hash',
					'options' => array(1=> 'Single Underline', 2 => 'Double Underline', 0 => 'None'),
					'label' => __('Underline Links', $this->textdomain)
				),
				'linkLimit' => array('input' => 'text', 'default' => 5,
					'label' => __('Maximum number of links to be displayed on a page', $this->textdomain),
					'help'  => 'Max is 10'
				),
				'linkAffiliation' => array('input' => 'checkbox', 'default' => true,
					'label' => __('Links to external merchants are converted into affiliate links.', $this->textdomain),
				),			
				'blank4' => array('input' => 'custom', 'label' => __('<strong style="font-size:14px; text-decoration:underline;"><p>Performance Ad Settings</p></strong>', $this->textdomain)
				),	
				'content_fallBack' => array('input' => 'text',
					'label' => __('<strong>Optional.</strong> Sets a fallback query for the content ad unit(s).', $this->textdomain),
					'help'  => 'If no relevant ad can be generated by analyzing your page content or user behavior. This can be a generic term that encompases the general idea of your site, or it can be a specific product.'
				),	
				'SWH' => array('input' => 'text', 'default' => 150,
					'label' => __('Sidebar Widget Height', $this->textdomain),
					'help'  => 'Minimum: 54'
				),	
				'SWW' => array('input' => 'text', 'default' => 180,
					'label' => __('Sidebar Widget Width', $this->textdomain),
					'help'  => 'Minimum: 77'
				),	
				'sidebar_fallBack' => array('input' => 'text',
					'label' => __('<strong>Optional.</strong> Sets a fallback query for the sidebar ad unit(s).', $this->textdomain)
				),	
				'FWH' => array('input' => 'text', 'default' => 150,
					'label' => __('Footer Widget Height', $this->textdomain),
					'help'  => 'Minimum: 54'
				),
				'FWW' => array('input' => 'text', 'default' => 180,
					'label' => __('Footer Widget Width', $this->textdomain),
					'help'  => 'Minimum: 77'
				),				
				'footer_fallBack' => array('input' => 'text',
					'label' => __('<strong>Optional.</strong> Footer Ad Fallback Query', $this->textdomain)
				),	
				'blank5' => array('input' => 'custom', 'label' => __('<strong style="font-size:14px; text-decoration:underline;"><p>Auto-Linker Settings</p></strong>', $this->textdomain),),	
				'Auto_Link' => array('input' => 'inline_textarea', 'datatype' => 'hash',
					'allow_html' => true, 'no_wrap' => true, 'input_attributes' => 'rows="15" cols="40"',
					'label' 	 => __('Text and Query', $this->textdomain),
					'help' 	 	 => 'Define text and the query in the field above. Follow this format:<br>
									<strong>"text" => "query (Optional)" </strong><p>
									For example: <br>
									<strong>shoes => Nike shoes</strong><br>
									This would create a link on the word shoes and send the query to the Product Search as Nike shoes.
									<p>
									
									The query parameter is optional so you can leave it as just the text to match as follows:<br>
									<strong>"text" </strong><p>
									For example: <br>
									<strong>shoes</strong><br>
									This would create a link on the word shoes and send the query to the Product Search as shoes.
									<p>
									List the more specific matches early. For example, if you want to link both "shoes" and "Nike shoes", put "Nike shoes" first. Otherwise, "shoes" will match first, preventing "Nike shoes" from being found.'
				),
				'Auto_Link_Comments' => array( 'input' => 'checkbox', 'default' => false,
					'label' 	 => __('Enable auto-link in comments', $this->textdomain)
				),
				'Case_Sensitive' => array('input' => 'checkbox', 'default' => false,
					'label'  => __('Case sensitive matching', $this->textdomain)
				),
				'Target' => array('input' => 'checkbox', 'default' => true,
					'label'  => __('Open Links in New Window or Tab', $this->textdomain),
					'help'   => '<b>Checked</b> = <b>_blank</b>: opens link in a new window or tab<p><b>Unchecked</b> = <b>_self</b>: opens link in the same window',
				)
			);
		}

		/**
		 * Outputs the text above the text area
		 *
		 * @return void
		 */
		public function options_page_description()
		{
			parent::options_page_description(__('Prosperent Settings', $this->textdomain));

			echo '<p>' . __('All the Prosperent Plugins Bundled as one.', $this->textdomain);
			echo '<p>' . __('Each add-on has its own Enable option. So you choose which to run on your blog.', $this->textdomain);
			echo '<p>' . __('The Prosperent Tools in this plugin bundle are:', $this->textdomain);
				echo "<blockquote><code>Prosperent Product Search</code></blockquote>";
				echo "<blockquote><code>Prosperent Auto-Linker</code></blockquote>";
				echo "<blockquote><code>Prosperent ProsperLinks</code></blockquote>";
				echo "<blockquote><code>Prosperent Performance-Ads</code></blockquote>";
			echo '<p>' . __('If you have any questions, feel free to ask it at the <a href="http://community.prosperent.com/forumdisplay.php?33-Prosperent-Plugins">Prosperent forums</a>, or email me at <a href="mailto:brandon@prosperent.com">brandon@prosperent.com</a>', $this->textdomain);	
		}

		/**
		 * Override the plugin framework's register_filters() to actually hook actions and filters.
		 *
		 * @return void
		 */
		public function register_filters()
		{
			$filters = apply_filters('c2c_linkify_text_filters', array('the_content', 'the_excerpt', 'widget_text'));
			foreach ((array) $filters as $filter)
				add_filter( $filter, array($this, 'auto_linker'), 2);

			// Note that the priority must be set high enough to avoid links inserted by the plugin from
			// getting omitted as a result of any link stripping that may be performed.
			if ($options['Auto_Link_Comments'])
			{
				add_filter('get_comment_text', array($this, 'auto_linker'), 11);
				add_filter('get_comment_excerpt', array($this, 'auto_linker'), 11);
			}
		}

		public function prosperent_store_install()
		{
			global $wpdb;

			$the_page_title = 'Products';
			$the_page_name = 'Prosperent Search';

			// the menu entry...
			delete_option("prosperent_store_page_title");
			add_option("prosperent_store_page_title", $the_page_title, '', 'yes');
			// the slug...
			delete_option("prosperent_store_page_name");
			add_option("prosperent_store_page_name", $the_page_name, '', 'yes');
			// the id...
			delete_option("prosperent_store_page_id");
			add_option("prosperent_store_page_id", '0', '', 'yes');

			$the_page = get_page_by_title($the_page_title);

			if (!$the_page)
			{
				// Create post object
				$_p = array();
				$_p['post_title'] = $the_page_title;
				$_p['post_content'] = "[prosper_store] [/prosper_store]";
				$_p['post_status'] = 'publish';
				$_p['post_type'] = 'page';
				$_p['comment_status'] = 'closed';
				$_p['ping_status'] = 'closed';
				$_p['post_category'] = array(0); // the default 'Uncatrgorised'

				// Insert the post into the database
				$the_page_id = wp_insert_post($_p);

			}
			else
			{
				// the plugin may have been previously active and the page may just be trashed...
				$the_page_id = $the_page->ID;

				//make sure the page is not trashed...
				$the_page->post_status = 'publish';
				$the_page_id = wp_update_post($the_page);

			}

			delete_option('prosperent_store_page_id');
			add_option('prosperent_store_page_id', $the_page_id);

		}

		public function prosperent_store_remove()
		{
			global $wpdb;

			$the_page_title = get_option("prosperent_store_page_title");
			$the_page_name = get_option("prosperent_store_page_name");

			// the id of our page...
			$the_page_id = get_option('prosperent_store_page_id');
			if($the_page_id)
			{
				wp_delete_post($the_page_id); // this will trash, not delete
			}

			delete_option("prosperent_store_page_title");
			delete_option("prosperent_store_page_name");
			delete_option("prosperent_store_page_id");
		}
		
		/**
		 * Perform auto-linker
		 *
		 * @param string $text
		 * @return string
		 */
		public function auto_linker($text)
		{
			$options = $this->get_options();

			if ($options['Enable_AL'])
			{
				$preg_flags = $options['Case_Sensitive'] ? 's' : 'si';
				$target 	= $options['Target'] ? '_blank' : '_self';
				$base_url   = $options['Base_URL'];

				$text = ' ' . $text . ' ';
				if ($options['Auto_Link'])
				{
					foreach ($options['Auto_Link'] as $old_text => $new_text)
					{
						$query = urlencode(trim(empty($new_text) ? $old_text : $new_text));

						$new_text = '<a href="' . $options['Sub_Dir'] . '/product?q=' . $query . '" target="' . $target . '">' . $old_text . '</a>';
						$text = preg_replace("|(?!<.*?)\b$old_text\b(?![^<>]*?>)|$preg_flags", $new_text, $text);
					}
					// Remove links within links
					$text = preg_replace( "#(<a [^>]+>)(.*)<a [^>]+>([^<]*)</a>([^>]*)</a>#iU", "$1$2$3$4</a>" , $text );
				}
			}
			return trim($text);
		}
	}

	new Prosperent_Suite();
}

