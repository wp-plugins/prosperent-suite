<?php
/*
Plugin Name: Prosperent Suite (Contains Performance Ads, Product Search, Auto-Linker and Auto-Comparer)
Description: Contains all of the Prosperent tools in one plugin to easily monetize your blog.
Version: 1.2
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
            $options = $this->options();
            register_deactivation_hook(__FILE__, array(__CLASS__, 'prosperent_store_remove'));

            if ($options['Enable_PA'])
            {
                add_action('performance_ads', array($this, 'Prosper_Perform_Ads'));
                add_action('wp_enqueue_scripts', array($this, 'prosperAds_css'));
                require_once('PA_Sidebar.php');
                require_once('PA_Footer.php');
            }
            if ($options['Enable_AL'])
            {
                if(is_admin())
                {
                    add_action('admin_print_footer_scripts', array($this, 'qTagsLinker'));
                    add_action('admin_init', array($this, 'autoLinker_custom_add'));
                }
                else
                {
                    add_shortcode('linker', array($this, 'linker_shortcode'));
                }
            }
			if ($options['Enable_AC'])
            {
                if(is_admin())
                {
					add_action('admin_print_footer_scripts', array($this, 'qTagsCompare'));
                    add_action('admin_init', array($this, 'autoCompare_custom_add'));
                }
                else
                {
					add_shortcode('compare', array($this, 'autoCompare_shortcode'));
                }
            }
            if ($options['Enable_PPS'])
            {
                add_action('wp_enqueue_scripts', array($this, 'prospere_stylesheets'));
                add_shortcode('prosper_store', array($this, 'store_shortcode'));
                add_shortcode('prosper_search', array($this, 'search_shortcode'));
                add_action('prospere_header', array($this, 'Prospere_Search'));
                add_action('wp_title', array($this, 'prosper_title'), 10, 3);
                register_activation_hook(__FILE__, array(__CLASS__, 'prosperent_store_install'));
                require_once('PS_Widget.php');
            }
        }

        public function prosperent_suite()
        {
            // Be a singleton
            if (!is_null(self::$instance))
                return;

            parent::__construct('1.0', 'prosperent-suite', 'prosper', __FILE__, array());

            self::$instance = $this;
        }

        public function options()
        {
            $options = $this->get_options();
            return $options;
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
                'blank1' => array('input' => 'custom', 'label' => __('<strong style="font-size:14px; text-decoration:underline;"><p>User Settings</p></strong>', $this->textdomain),
                ),
                'UID'	=> array('input' => 'text',
                    'label' => __('Your Prosperent User-ID', $this->textdomain)
                ),
				'Api_Key' => array('input' => 'text',
                    'label' => __('Your API Key.', $this->textdomain)
                ),
                'blank' => array('input' => 'custom', 'label' => __('<strong style="font-size:14px; text-decoration:underline;"><p>Enable or Disable Options</p></strong>', $this->textdomain)
                ),
                'Enable_PPS' => array('input' => 'checkbox', 'default' => true,
                    'label' => __('Enable Product Search', $this->textdomain)
                ),
                'Enable_PA' => array('input' => 'checkbox', 'default' => true,
                    'label' => __('Enable Performance-Ads', $this->textdomain)
                ),
				'Enable_AC' => array('input' => 'checkbox',  'default' => true,
                    'label' => __('Enable Auto-Comparer', $this->textdomain)
                ),
                'Enable_AL' => array('input' => 'checkbox',  'default' => true,
                    'label' => __('Enable Auto-Linker', $this->textdomain),
                    'help'  => 'Some features will only work if Product Search is enabled.'
                ),
                'blank2' => array('input' => 'custom', 'label' => __('<strong style="font-size:14px; text-decoration:underline;"><p>Product Search Settings</p></strong>', $this->textdomain)
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
                    'options' => array('' => 'Relevancy', 'desc' => 'Price: High to Low', 'asc' => 'Price: Low to High'),
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
                'Celebrity_Endpoint' => array('input' => 'checkbox', 'default' => true,
                    'label' => __('Dsiplay the Link to the Celebrity Endpoint', $this->textdomain)
                ),
                'Page_Title' => array('input' => 'checkbox',
                    'label' => __('Use a page title you decide.', $this->textdomain),
                    'help'  => 'Will use the title how you set it up in the next setting.'
                ),
                'Title_Param' => array('input' => 'text',
                    'label' => __('Enter the title as you would like it.', $this->textdomain),
                    'help'  => 'Seperate each part of the title with commas<br>
                                Use the letter Q to represent the Query<br>
                                Use PT to represent Page Title<br>
                                Or enter your own, otherwise the page title will be of the WordPress configuration you use'
                ),
                'Title_Sep' => array('input' => 'text',
                    'label' => __('Enter a seperator for the Product page title.', $this->textdomain)
                ),
                'Base_URL' => array('input' => 'text',
                    'label' => __('Base Url', $this->textdomain),
                    'help'  => 'If you have a different URL from "<b>your-blog.com/product</b>" that you want the search query to go to.'
                ),
                'Additional_CSS' => array('input' => 'text',
                    'label' => __('Additional CSS for the shortcode search bar.', $this->textdomain)
                ),
                /*'Parent_Directory' => array('input' => 'text',
                    'label' => __('Sub-Directory', $this->textdomain),
                    'help'  => 'If your WP install has a sub-directory.'
                ),*/
                'Logo_Image' => array('input' => 'checkbox',
                    'label' => __('Logo Image', $this->textdomain),
                    'help'  => '<b>Only for search bar in header.</b> Display the original sized Prosperent Logo. Size is 167px x 50px.'
                ),
                'Logo_imageSmall' => array('input' => 'checkbox',
                    'label' => __('Logo Image- Small', $this->textdomain),
                    'help'  => '<b>Only for search bar in header.</b> Display the smaller Prosperent Logo. Size is 100px x 30px.'
                ),
                'blank4' => array('input' => 'custom', 'label' => __('<strong style="font-size:14px; text-decoration:underline;"><p>Performance Ad Settings</p></strong>', $this->textdomain)
                ),
                'content_fallBack' => array('input' => 'text',
                    'label' => __('<strong>Optional.</strong> Fallback query for the content ad unit(s).', $this->textdomain),
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
                    'label' => __('<strong>Optional.</strong> Fallback query for the sidebar ad unit(s).', $this->textdomain)
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
                    'label' => __('<strong>Optional.</strong> Fallback query for the footer ad unit(s).', $this->textdomain)
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
                    'label'  => __('Enable auto-link in comments', $this->textdomain)
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
				echo "<blockquote><code>Prosperent Auto-Comparer</code></blockquote>";
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
            $options = $this->options();
            if ($options['Enable_AL'])
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
        }

        public function prosperent_store_install()
        {
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
                $_p['post_content'] = "[prosper_store][/prosper_store]";
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
            $options = $this->options();

            if ($options['Enable_PPS'])
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

                        $new_text = '<a href="' . (!$base_url ? '/products/?q=' : '/' . $base_url . '/?q=') . urlencode($query) . '" target="' . $target . '">' . $old_text . '</a>';
                        $text = preg_replace("|(?!<.*?)\b$old_text\b(?![^<>]*?>)|$preg_flags", $new_text, $text);
                    }
                    // Remove links within links
                    $text = preg_replace( "#(<a [^>]+>)(.*)<a [^>]+>([^<]*)</a>([^>]*)</a>#iU", "$1$2$3$4</a>" , $text );
                }
            }
            return trim($text);
        }

        public function prospere_stylesheets()
        {
            // Product Search CSS for results and search
            wp_register_style( 'prospere_main_style', plugins_url('/css/productSearch.css', __FILE__) );
            wp_enqueue_style( 'prospere_main_style' );

            // Product Search CSS for IE7, a few changes to align objects
            wp_enqueue_style('prospere_IE_7', plugins_url('/css/productSearch-IE7.css', __FILE__));
            wp_enqueue_style( 'prospere_IE_7' );
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
			
			ob_start();
            include(plugin_dir_path(__FILE__) . 'search_short.php');
            $search = ob_get_clean();
            return $search;
        }

        public function prosper_title($post_title, $sep, $seplocation)
        {
            $options = $this->options();

            if ($options['Page_Title'])
            {
                if ($options['Title_Param'])
                {
                    $parts = explode(',', $options['Title_Param']);

                    foreach ($parts as $part)
                    {
                        if ($part == 'Q')
                        {
                            $post_title = ucwords(!$_GET['q'] ? (!$options['Starting_Query'] ? '' : $options['Starting_Query']) : $_GET['q']) . (!$options['Title_Sep'] ? ' | ' : $options['Title_Sep']) . $post_title;
                        }
                        else if($part == 'PT')
                        {
                            $post_title = $post_title . $post_title;
                        }
                        else
                        {
                            $post_title = $post_title . $part;
                        }
                    }
                }
            }
            else
            {
                $page = !$options['Base_URL'] ? 'products' : $options['Base_URL'];

                if (is_page($page) && !isset($_GET['q']) && $options['Starting_Query'])
                {
                    $post_title = ucwords($options['Starting_Query'])  . ' | ';
                }
                else if (is_page($page) && isset($_GET['q']))
                {
                    $post_title = ucwords($_GET['q'])  . ' | ';
                }
                else
                {
                    $post_title = $post_title;
                }
            }

            return $post_title;
        }

        public function Prospere_Search()
        {
            $options = $this->options();
            ?>
            <form id="search" method="GET" action="<?php echo !$options['Base_URL'] ? '/products' : '/' . $options['Base_URL']; ?>">
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
                        else if ($options['Logo_imageSmall'])
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

		public function autoCompare_shortcode($atts, $content = null)
        {
            $options = $this->options();
            $target   = $options['Target'] ? '_blank' : '_self';			

            extract(shortcode_atts(array(
                "q"  => !isset($q) ? '' : $q,
                "c"  => !isset($c) ? '' : $c,
                "b"  => !isset($b) ? '' : $b,
                "m"  => !isset($m) ? '' : $m,
				"l"  => !isset($l) ? 1 : intval($l),
				"cl" => !isset($cl) ? 3 : intval($cl)
            ), $atts));

            $query = !$q ? $content : $q;

            // Remove links within links
            $query = strip_tags($query);
            $content = strip_tags($content);

            $fB = !isset($b) ? '' : '&brand=' . urlencode($b);
            $fM = !isset($m) ? '' : '&merchant=' . urlencode($m);

			if (!$c)
			{
				require_once('Prosperent_Api.php');
				$prosperentApi = new Prosperent_Api(array(
					'api_key'        => $options['Api_Key'],
					'query'          => $query,
					'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
					'limit'          => $l,
					'enableFacets'   => TRUE,
					'sortPrice'		 => '',
					'filterMerchant' => $m,
					'filterBrand'	 => $b
				));
				
				$prosperentApi -> fetch();
				$results = $prosperentApi -> getAllData();				

				if ($results)
				{
					ob_start();
					include(plugin_dir_path(__FILE__) . 'compare_short.php');
					$compare = ob_get_clean();
					return $compare;
				}
				else
				{ 
					require_once('Prosperent_Api.php');
					$prosperentApi = new Prosperent_Api(array(
						'api_key'        => $options['Api_Key'],
						'query'          => $query,
						'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
						'limit'          => $l,
						'enableFacets'   => TRUE,
						'sortPrice'		 => '',
					));

					$prosperentApi -> fetch();
					
					
					$results = $prosperentApi -> getAllData();
					
					if ($results)
					{
						ob_start();
						include(plugin_dir_path(__FILE__) . 'compare_short.php');
						$compare = ob_get_clean();
						return $compare;
					}
					else
					{
						return;
					}
				}
			}
			else
			{
				require_once('Prosperent_Api.php');
				$prosperentApi = new Prosperent_Api(array(
					'api_key'        => $options['Api_Key'],
					'query'          => $query,
					'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
					'limit'          => $l,
					'enableFacets'   => TRUE,
					'sortPrice'		 => '',
					'filterMerchant' => $m
				));
			
				$prosperentApi -> fetchCoupons();
				$results = $prosperentApi -> getAllData();
				
				if ($results)
				{
					ob_start();
					include(plugin_dir_path(__FILE__) . 'compare_coup.php');
					$compare = ob_get_clean();
					return $compare;
				}
				else
				{
					require_once('Prosperent_Api.php');
					$prosperentApi = new Prosperent_Api(array(
						'api_key'        => $options['Api_Key'],
						'query'          => $query,
						'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
						'limit'          => $l,
						'enableFacets'   => TRUE,
						'sortPrice'		 => '',
					));

					$prosperentApi -> fetchCoupons();				
					$results = $prosperentApi -> getAllData();
					
					if ($results)
					{
						ob_start();
						include(plugin_dir_path(__FILE__) . 'compare_coup.php');
						$compare = ob_get_clean();
						return $compare;
					}
					else
					{
						return;
					}
				}
			}
        }
		
        public function linker_shortcode($atts, $content = null)
        {
            $options = $this->options();
            $target   = $options['Target'] ? '_blank' : '_self';

            extract(shortcode_atts(array(
                "q"   => !isset($q) ? '' : $q,
                "gtm" => !isset($gtm) ? '' : $gtm,
                "b"   => !isset($b) ? '' : $b,
                "m"   => !isset($m) ? '' : $m
            ), $atts));

            $query = !$q ? $content : $q;

            // Remove links within links
            $query = strip_tags($query);
            $content = strip_tags($content);

            $fB = empty($b) ? '' : '&brand=' . urlencode($b);
            $fM = empty($m) ? '' : '&merchant=' . urlencode($m);

            if ($gtm || !$options['Enable_PPS'])
            {
                require_once('Prosperent_Api.php');
                $prosperentApi = new Prosperent_Api(array(
                    'api_key'        => $options['Api_Key'],
                    'query'          => $query,
                    'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
                    'limit'          => 1,
                    'enableFacets'   => TRUE,
                    'filterBrand'    => $b,
                    'filterMerchant' => $m
                ));

                $prosperentApi -> fetch();
                $results = $prosperentApi -> getAllData();
                if ($results)
                {
                    return '<a href="' . $results[0]['affiliate_url'] . '" TARGET=_blank">' . $content . '</a>';
                }
                else
                {
					require_once('Prosperent_Api.php');
					$prosperentApi = new Prosperent_Api(array(
						'api_key'        => $options['Api_Key'],
						'query'          => $query,
						'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
						'limit'          => 1,
						'enableFacets'   => TRUE
					));

					$prosperentApi -> fetch();
					$results = $prosperentApi -> getAllData();
					
					if ($results)
					{
						return '<a href="' . $results[0]['affiliate_url'] . '" TARGET=_blank">' . $content . '</a>';
					}
					else
					{
						return;
					}
                }
            }

            return '<a href="' . (!$options['Base_URL'] ? '/products' : '/' . $options['Base_URL']) . '/?q=' . urlencode($query) . $fB . $fM . '" TARGET="' . $target . '">' . $content . '</a>';
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

		public function autoCompare_custom_add()
        {
            // Add only in Rich Editor mode
            if (get_user_option('rich_editing') == 'true')
            {
				add_filter('mce_external_plugins', array($this, 'autoCompare_tiny_register'));
                add_filter('mce_buttons', array($this, 'autoCompare_tiny_add'));
            }
        }
		
        public function qTagsLinker()
        {
            ?>
            <script type="text/javascript">
                QTags.addButton('auto-linker', 'auto-linker', '[linker]', '[/linker]', 0);
            </script>
            <?php
        }

		public function qTagsCompare()
        {
            ?>
            <script type="text/javascript">
                QTags.addButton('auto-compare', 'auto-compare', '[compare]', '[/compare]', 0);
            </script>
            <?php
        }
		
		public function autoCompare_tiny_add($buttons)
        {
            array_push($buttons, "|", "compare");
            return $buttons;
        }

        public function autoCompare_tiny_register($plugin_array)
        {
            $plugin_array["compare"] = plugin_dir_url(__FILE__) . 'js/compare.min.js';
            return $plugin_array;
        }
		
        public function autoLinker_tiny_add($buttons)
        {
            array_push($buttons, "|", "linker");
            return $buttons;
        }

        public function autoLinker_tiny_register($plugin_array)
        {
            $plugin_array["linker"] = plugin_dir_url(__FILE__) . 'js/linker.min.js';
            return $plugin_array;
        }

        public function prosperAds_css()
        {
            // Performance Ad CSS for results and search
            wp_register_style('prosper_perform_css', plugins_url('/css/performance_ads.css', __FILE__));
            wp_enqueue_style('prosper_perform_css');
        }

        public function Prosper_Perform_Ads()
        {
            $options = $this->options();

            ?>
            <script type="text/javascript">
                <!--
                prosperent_pa_uid = <?php echo json_encode($options['UID']); ?>;
                prosperent_pa_height = 90;
                prosperent_pa_fallback_query = <?php echo json_encode($options['content_fallBack']); ?>;
                //-->
            </script>
            <script type="text/javascript" src="http://prosperent.com/js/ad.js"></script>
            <?php
        }

    }

    new Prosperent_Suite();

    function prospere_header()
    {
        do_action('prospere_header');
    }

    function performance_ads()
    {
        do_action('performance_ads');
    }
}

