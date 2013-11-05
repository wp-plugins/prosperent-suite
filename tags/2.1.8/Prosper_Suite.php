<?php
/*
Plugin Name: Prosperent Suite
Description: Contains all of the Prosperent tools in one plugin to easily monetize your blog.
Version: 2.1.8
Author: Prosperent Brandon
License: GPLv3

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

if (!defined( 'WP_CONTENT_DIR'))
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if (!defined('PROSPER_URL'))
    define('PROSPER_URL', plugin_dir_url(__FILE__));
if (!defined('PROSPER_PATH'))
    define('PROSPER_PATH', plugin_dir_path(__FILE__));
if (!defined('PROSPER_BASENAME'))
    define('PROSPER_BASENAME', plugin_basename(__FILE__));
if (!defined('PROSPER_CACHE'))
	define('PROSPER_CACHE', WP_CONTENT_DIR . '/prosperent_cache');

if (!class_exists('Prosperent_Suite'))
{
    require PROSPER_PATH . 'admin/admin.php';
	require_once(PROSPER_PATH . 'Prosperent_Api.php');

    class Prosperent_Suite extends Prosperent_Admin
    {
		private $version;
		
        /**
         * Constructor
         *
         * @return void
         */
        public function __construct()
        {						
            add_action('init', array($this, 'do_output_buffer'));
            add_action('init', array($this, 'prosper_query_tag'), 1);

            $options = $this->get_option();
			
			if ($options['Enable_Caching'] && (substr(decoct( fileperms(PROSPER_CACHE) ), 1) != '0777') || !file_exists(PROSPER_CACHE))
			{
				add_action( 'admin_notices', array($this, 'prosperNoticeWrite' ));
			}
			
			if ( ! function_exists( 'get_plugins' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			
			$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
			$plugin_file = basename( ( __FILE__ ) );
			$this->version = $plugin_folder[$plugin_file]['Version'];
			
            register_activation_hook(__FILE__, array($this, 'prosper_activate'));
            register_deactivation_hook( __FILE__, array($this, 'prosper_deactivate'));

			add_action('admin_init', array($this, 'prosper_activate_redirect'));
			
            $rules = get_option('rewrite_rules');
            if (!$rules['store/go/([^/]*)/?'])
            {
                add_action( 'init', array($this, 'prosper_reroutes' ));
            }

			if ($options['Api_Key'] && preg_match('/\w{32,32}/i', $options['Api_Key']))
			{ 
				add_action('wp_head', array($this, 'prosper_headerScript'));
				add_action('wp_enqueue_scripts', array($this, 'prospere_stylesheets'));
				
				if (isset($options['Enable_PA']))
				{
					if(is_admin())
					{
						add_action('admin_print_footer_scripts', array($this, 'qTagsPerformAd'));
						add_action('admin_init', array($this, 'performAd_custom_add'));
					}
					else
					{
						add_shortcode('perform_ad', array($this, 'performAd_shortCode'));
					}
					require_once('PA_Widget.php');
				}
				if (isset($options['Enable_AC']))
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
					
					if ($options['prosper_inserter_posts'] || $options['prosper_inserter_pages'])
					{
						add_filter('the_content', array($this, 'content_inserter'), 2);
					}
				}
				if (isset($options['Enable_AL']))
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

					$this->register_filters();
				}
				if (isset($options['Enable_PPS']))
				{					
					add_shortcode('prosper_store', array($this, 'store_shortcode'));
					add_shortcode('prosper_search', array($this, 'search_shortcode'));
					add_action('wp_head', array($this, 'ogMeta'));
					add_filter('wp_title', array($this, 'prosper_title'), 20, 3);

					require_once('TP_Widget.php');
					require_once('PS_Widget.php');
				}
				else
				{
					add_action('admin_init', array($this, 'prosperent_store_remove'));
				}
			}
			else
			{
				add_action( 'admin_notices', array($this, 'prosperBadSettings' ));
			}	
        }
		
		public function prosperNoticeWrite() 
		{
			echo '<div class="error" style="padding:6px 0;">';
			echo _e( '<span style="font-size:14px; padding-left:10px;">Please create the <strong>prosperent_cache</strong> directory inside your <strong>wp_content</strong> directory and make it writable (0777). If you need assistance, <a href="http://codex.wordpress.org/Changing_File_Permissions">Changing File Permissions</a></span><span style="font-size:12px;"></span>', 'my-text-domain' );
			echo '</div>';	
		}
		
		public function prosperBadSettings()
		{			
			$url = admin_url( 'admin.php?page=prosper_general' );
			echo '<div class="error" style="padding:6px 0;">';
			echo _e( '<span style="font-size:14px; padding-left:10px;">Your User Id or API Key is either incorrect or missing. </span><br>
			<span style="font-size:14px; padding-left:10px;">Please enter your <strong>Prosperent API Key</strong>. Go to the Prosperent Suite <a href="' . $url . '">General Settings</a> and follow the directions to get your API Key.</span>', 'my-text-domain' );
			echo '</div>';		
		}
		
		public function prosper_headerScript()
        {
			$options = $this->get_option();		
						
            echo '<script type="text/javascript">var _prosperent={"campaign_id":"' . $options['Api_Key'] . '", "pl_active":1, "pa_active":' . ($options['Enable_PA'] ? 1 : 0) . ', "pl_phraselinker_active":0, "pl_linkoptimizer_active":' . ($options['PL_LinkOpt'] ? 1 : 0) . ', "pl_linkaffiliator_active":' . ($options['PL_LinkAff'] ? 1 : 0) . ', "platform":"wordpress"};</script><script async type="text/javascript" src="http://prosperent.com/js/prosperent.js"></script>';
        }
		
        /**
         * Retrieve an array of all the options the plugin uses. It can't use only one due to limitations of the options API.
         *
         * @return array of options.
         */
        public function get_prosper_options_array()
        {
            $optarr = array('prosperSuite', 'prosper_productSearch', 'prosper_performAds', 'prosper_autoComparer', 'prosper_autoLinker', 'prosper_prosperLinks', 'prosper_advanced');
            return apply_filters( 'prosper_options', $optarr );
        }

        public function ogMeta()
        {
            $options = $this->get_option();

            if(!preg_match('/^@/', $options['Twitter_Site']))
            {
                $options['Twitter_Site'] = '@' . $options['Twitter_Site'];
            }
            if(!preg_match('/^@/', $options['Twitter_Creator']))
            {
                $options['Twitter_Creator'] = '@' . $options['Twitter_Creator'];
            }

            /*
            /  Prosperent API Query
            */
            $settings = array(
                'api_key'         => $options['Api_Key'],
                'limit'           => 1,
                'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
                'enableFacets'    => $options['Enable_Facets'],
                'filterCatalogId' => get_query_var('cid')
            );

			if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
			{	
				$settings = array_merge($settings, array(
					'cacheBackend'  => 'FILE',
					'cacheOptions'  => array(
						'cache_dir' => PROSPER_CACHE
					)
				));	
			}
			
			$prosperentApi = new Prosperent_Api($settings);
			
            switch ($options['Country'])
            {
                case 'UK':
                    $prosperentApi -> fetchUkProducts();
                    $currency = 'GBP';
                    break;
                case 'CA':
                    $prosperentApi -> fetchCaProducts();
                    $currency = 'CAD';
                    break;
                default:
                    $prosperentApi -> fetchProducts();
                    $currency = 'USD';
                    break;
            }
            $record = $prosperentApi -> getAllData();

            $page = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products';
            if (is_page($page) && get_query_var('cid'))
            {
                // Open Graph: FaceBook
                echo '<meta property="og:url" content="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/" />';
                echo '<meta property="og:site_name" content="' . get_bloginfo('name') . '" />';
                echo '<meta property="og:type" content="website" />';
                echo '<meta property="og:image" content="' . $record[0]['image_url'] . '" />';
                echo '<meta property="og:description" content="' . $record[0]['description'] . '" />';
                echo '<meta property="og:title" content="' . strip_tags($record[0]['keyword'] . ' - ' .  get_the_title($post) . ' - ' . get_bloginfo('name')) . '" />';

                // Twitter Cards
                echo '<meta name="twitter:card" content="product">';
                echo '<meta name="twitter:site" content="' . $options['Twitter_Site'] . '" />';
                echo '<meta name="twitter:creator" content="' . $options['Twitter_Creator'] . '"/>';
                echo '<meta name="twitter:image" content="' . $record[0]['image_url'] . '" />';
                echo '<meta name="twitter:data1" content="' . ((!$record[0]['price_sale'] || $record[0]['price'] <= $record[0]['price_sale']) ? $record[0]['price'] : $record[0]['price_sale']) . '">';
                echo '<meta name="twitter:label1" content="Price">';
                echo '<meta name="twitter:data2" content="' . $record[0]['brand'] . '">';
                echo '<meta name="twitter:label2" content="Brand">';
                echo '<meta name="twitter:description" content="' . $record[0]['description'] . '" />';
                echo '<meta name="twitter:title" content="' . strip_tags($record[0]['keyword'] . ' - ' .  get_the_title($post) . ' - ' . get_bloginfo('name')) . '" />';
            }
        }

        public function register_filters()
        {
            $options = $this->get_option();

            add_filter('the_content', array($this, 'auto_linker'), 2);			
            add_filter('the_excerpt', array($this, 'auto_linker'), 2);
            add_filter('widget_text', array($this, 'auto_linker'), 2);

            // Note that the priority must be set high enough to avoid links inserted by the plugin from
            // getting omitted as a result of any link stripping that may be performed.
            if ($options['Auto_Link_Comments'])
            {
                add_filter('get_comment_text', array($this, 'auto_linker'), 11);
                add_filter('get_comment_excerpt', array($this, 'auto_linker'), 11);
            }
        }

		public function content_inserter($text)
		{		
			$options = $this->get_option();
			$text = ' ' . $text . ' ';
			
			if ($options['prosper_inserter_negTitles'])
			{
				if(function_exists('prosper_negatives') === false)
				{
					function prosper_negatives($negative)
					{
						return '/\b' . trim($negative) . '\b/i';
					}
				}	

				$exclude = array_map(
					"prosper_negatives",
					explode(',', $options['prosper_inserter_negTitles'])
				);

				$newTitle = preg_replace($exclude, '', get_the_title());
			}
			else
			{
				$newTitle = get_the_title();
			}
			
			if (!$newTitle)
			{
				return trim($text);
			}
			
			$insert = '<p>[compare q="' . $newTitle . '" l="' . ($options['PI_Limit'] ? $options['PI_Limit'] : 1) . '"][/compare]</p>';
			
			if ('top' == $options['prosper_inserter'])
			{
				$content = $insert . $text;
			}
			else
			{
				$content = $text . $insert;
			}
			
			if ($options['prosper_inserter_pages'] && $options['prosper_inserter_posts'])
			{
				if( is_singular() && is_main_query() ) 
				{
					$text = $content;
				}
				
				if(is_single()) 
				{
					$text = $content;	
				}
			}
			elseif($options['prosper_inserter_posts'])
			{
				if(is_single()) 
				{
					$text = $content;
				}				
			}
			elseif($options['prosper_inserter_pages'])
			{
				if( is_singular() && is_main_query() ) 
				{
					$text = $content;
				}
			}		
			
			return trim($text);
		}
		
        public function prosper_reroutes()
        {
            $this->prosper_rewrite();
            $this->prosper_flush_rules();
        }

        /**
         * Retrieve all the options
         *
         * @return array of options
         */
        public function get_option($option = null)
        {
            static $options;

            if (!isset($options))
            {
                $options = array();
                foreach ($this->get_prosper_options_array() as $opt)
                {
                    $options = array_merge($options, (array) get_option($opt));
                }
            }

            return $options;
        }

        public function prosper_activate()
        {
            $this->prosper_default();
            $this->prosperent_store_install();
            $this->prosper_reroutes();
			$this->prosperOptionActivateAdd();
        }

        public function prosper_deactivate()
        {
            $this->prosperent_store_remove();
            $this->prosper_flush_rules();
        }

		public function prosperOptionActivateAdd() 
		{
			add_option('prosperActivationRedirect', true);
		}

		public function prosper_activate_redirect() 
		{
			if (get_option('prosperActivationRedirect', false)) 
			{
				delete_option('prosperActivationRedirect');
				if(!isset($_GET['activate-multi']))
				{
					wp_redirect( admin_url( 'admin.php?page=prosper_general' ) );
				}
			}
		}
		
        /**
         * Flush the rewrite rules.
         */
        public function prosper_flush_rules()
        {
            flush_rewrite_rules();
        }

        public function do_output_buffer()
        {
            ob_start();
        }

        public function prosper_query_tag()
        {
            $GLOBALS['wp']->add_query_var( 'keyword' );
            $GLOBALS['wp']->add_query_var( 'cid' );
            $GLOBALS['wp']->add_query_var( 'storeUrl' );
            $GLOBALS['wp']->add_query_var( 'queryParams' );
            $GLOBALS['wp']->add_query_var( 'prosperImg' );
        }

        public function prosper_rewrite()
        {
            $options  = $this->get_option();

            $page     = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL'] . '/') : 'products/';
            $pageName = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : 'pagename=' . $options['Base_URL']) : 'pagename=products';

            add_rewrite_rule('local/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
            add_rewrite_rule('travel/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
            add_rewrite_rule('coupon/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
            add_rewrite_rule('product/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
            add_rewrite_rule('celebrity/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
            add_rewrite_rule('store/go/([^/]*)/?', 'index.php?' . $pageName . '&store&go&storeUrl=$matches[1]', 'top');
            add_rewrite_rule('img/([^/]*)/?', 'index.php?' . $pageName . '&prosperImg=$matches[1]', 'top');
            add_rewrite_rule($page . '(.*)', 'index.php?' . $pageName . '&queryParams=$matches[1]', 'top');
        }

        public function prosper_default()
        {
            $old_options = get_option('prosper_prosperent_suite');

            if (!is_array(get_option('prosperSuite')))
            {
                if (is_array($old_options))
                {
                    $opt = array(
                        'Api_Key' => $old_options['Api_Key'],
                        'Target'  => $old_options['Target']
                    );
                }
                else
                {
                    $opt = array(
                        'Target' => 1
                    );
                }

                update_option('prosperSuite', $opt);
            }

            if (!is_array(get_option('prosper_productSearch' )))
            {
                if (is_array($old_options))
                {
                    $opt = array(
                        'Enable_PPS'       	 => $old_options['Enable_PPS'],
                        'Product_Endpoint' 	 => 1,
                        'Country_Code'  	 => 'US',
                        'Coupon_Endpoint'    => 1,
                        'Celebrity_Endpoint' => 0,
                        'Local_Endpoint'     => 1,
                        'Geo_Locate' 		 => 1,
                        'Travel_Endpoint'    => 0,
                        'Api_Limit' 		 => $old_options['Api_Limit'],
                        'Pagination_Limit'   => $old_options['Pagination_Limit'],
                        'Same_Limit'		 => 8,
                        'Enable_Facets'      => $old_options['Enable_Facets'],
                        'Default_Sort' 		 => $old_options['Default_Sort'],
                        'Search_Bar_Text'  	 => $old_options['Search_Bar_Text'],
                        'Merchant_Facets'    => $old_options['Merchant_Facets'],
                        'Brand_Facets' 		 => $old_options['Brand_Facets'],
                        'Negative_Brand'  	 => $old_options['Negative_Brand'],
                        'Negative_Merchant'  => $old_options['Negative_Merchant'],
                        'Positive_Merchant'  => '',
                        'Positive_Brand' 	 => '',
                        'Starting_Query' 	 => $old_options['Starting_Query'],
                        'Coupon_Query'       => '',
                        'Celebrity_Query' 	 => '',
                        'Local_Query' 		 => '',
                        'Travel_Query' 		 => ''
                    );
                }
                else
                {
                    $opt = array(
                        'Enable_PPS'       	 => 1,
                        'Product_Endpoint' 	 => 1,
                        'Country_Code'  	 => 'US',
                        'Coupon_Endpoint'    => 1,
                        'Celebrity_Endpoint' => 0,
                        'Local_Endpoint'     => 1,
                        'Geo_Locate' 		 => 1,
                        'Travel_Endpoint'    => 0,
                        'Api_Limit' 		 => 50,
                        'Pagination_Limit'   => 10,
                        'Same_Limit'		 => 8,
                        'Enable_Facets'      => 1,
                        'Default_Sort' 		 => '',
                        'Search_Bar_Text'  	 => '',
                        'Merchant_Facets'    => 10,
                        'Brand_Facets' 		 => 10,
                        'Negative_Brand'  	 => '',
                        'Negative_Merchant'  => '',
                        'Starting_Query' 	 => 'shoes',
                        'Positive_Merchant'  => '',
                        'Positive_Brand' 	 => '',
                        'Coupon_Query'       => '',
                        'Celebrity_Query' 	 => '',
                        'Local_Query' 		 => '',
                        'Travel_Query' 		 => ''
                    );
                }
                update_option( 'prosper_productSearch', $opt );
            }

            if (!is_array(get_option('prosper_performAds')))
            {
                if (is_array($old_options))
                {
                    $opt = array(
                        'Enable_PA'   => $old_options['Enable_PA'],
						'Remove_Tags' => ''
                    );
                }
                else
                {
                    $opt = array(
                        'Enable_PA'   => 1,
						'Remove_Tags' => ''
                    );
                }
                update_option( 'prosper_performAds', $opt );
            }

            if (!is_array(get_option('prosper_autoComparer')))
            {
                if (is_array($old_options))
                {
                    $opt = array(
                        'Enable_AC'    => $old_options['Enable_AC'],
						'Link_to_Merc' => 1,
						'PI_Limit'	   => 1
                    );
                }
                else
                {
                    $opt = array(
                        'Enable_AC'    => 1,
						'Link_to_Merc' => 1,
						'PI_Limit'	   => 1
                    );
                }
                update_option( 'prosper_autoComparer', $opt );
            }

            if (!is_array(get_option('prosper_autoLinker')))
            {
                if (is_array($old_options))
                {
                    $opt = array(
                        'Enable_AL' 		 => $old_options['Enable_AL'],
                        'Auto_Link_Comments' => $old_options['Auto_Link_Comments']
                    );
                }
                else
                {
                    $opt = array(
                        'Enable_AL' 		 => 1,
                        'Auto_Link_Comments' => 0
                    );
                }
                update_option( 'prosper_autoLinker', $opt );
            }

			if (!is_array(get_option('prosper_prosperLinks')))
            {
				$opt = array(
					'PL_LinkOpt' => 1,
					'PL_LinkAff' => 1
				);
                
                update_option( 'prosper_prosperLinks', $opt );
            }
			
            if (!is_array(get_option('prosper_advanced')))
            {
                if (is_array($old_options))
                {
                    $opt = array(
                        'Title_Structure' => $old_options['Title_Structure'],
                        'Title_Sep'		  => $old_options['Title_Sep'],
                        'Twitter_Site'	  => '',
                        'Twitter_Creator' => '',
                        'Additional_CSS'  => $old_options['Additional_CSS'],
                        'Image_Masking'	  => 0,
						'Base_URL'		  => 'products'
                    );
                }
                else
                {
                    $opt = array(
                        'Title_Structure' => 0,
                        'Title_Sep'		  => '',
                        'Twitter_Site'	  => '',
                        'Twitter_Creator' => '',
                        'Additional_CSS'  => '',
                        'Image_Masking'	  => 0,
						'Base_URL'		  => 'products'
                    );
                }
                update_option( 'prosper_advanced', $opt );
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
                $proserStore = array(
                    'post_title'     => $the_page_title,
                    'post_content'   => '[prosper_store][/prosper_store]',
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                    'comment_status' => 'closed',
                    'ping_status'    => 'closed'
                );

                // Insert the post into the database
                $the_page_id = wp_insert_post($proserStore);
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
            $options = $this->get_option();
			
			$random 			= FALSE;
			$base_url   		= $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '/query/' : $options['Base_URL'] . '/query/') : 'products/query/';
			$target 			= $options['Target'] ? '_blank' : '_self';
			$prosper_aff_url    = 'http://prosperent.com/store/product/' . $options['UID'] . '-427-0/?k=';
			$store_go_url       = site_url() . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $prosper_aff_url)) . ',SL,';
			$product_search_url = site_url('/') . $base_url;	
						
			$text = ' ' . $text . ' ';
			if ($options['Match'])
			{
				foreach ($options['Match'] as $i => $match)
				{			
					if (!empty($match))
					{
						$val[$match] =  $options['Query'][$i] ? $options['Query'][$i] : $match;
					}
				}
				
				$i = 0;				
				foreach ($val as $old_text => $new_text)
				{ 				
					$limit = $options['PerPage'][$i] ? $options['PerPage'][$i] : 5;
					$case  = isset($options['Case'][$i]) ? '' : 'i';
					$query = rawurlencode(trim($new_text));	
					//$qText = 'q="' . $old_text . '"';
					preg_match('/q=\".+?\"/', $text, $qText);
					
					/*
					*  Prosperent API Query
					*/
					$settings = array(
						'api_key'         => $options['Api_Key'],
						'limit'           => 1,
						'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
						'query'			  => $new_text,
						'groupBy'		  => 'productId',
						'enableFullData'  => 0
					);

					if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
					{	
						$settings = array_merge($settings, array(
							'cacheBackend'   => 'FILE',
							'cacheOptions'   => array(
								'cache_dir'  => PROSPER_CACHE
							)
						));	
					}

					$prosperentApi = new Prosperent_Api($settings);
					
					$prosperentApi -> fetchProducts();
					$result = $prosperentApi -> getAllData();
					$affUrl = $result[0]['affiliate_url'];

					$text = str_ireplace($qText[0], $base = base64_encode($qText[0]), $text);
					
					if ($random)
					{							
						preg_match_all('/\b' . $old_text . '\b/' . $case, $text, $matches, PREG_PATTERN_ORDER);

						$matches = $matches[0];

						if($case == 'i')
						{
							$old_text = strtolower($old_text);
							$text = preg_replace('/\b' . $old_text . '\b/i', $old_text, $text);						
						}

						$newText = explode($old_text, $text);
						
						if ($limit < count($matches))
						{
							$rand_keys = array_rand($matches, $limit);
							
							if ($limit > 1)
							{
								foreach($rand_keys as $key)
								{
									if (!$options['Enable_PPS'] || $options['LTM'][$i] == 1)
									{
										$matches[$key] = '<a href="' . $affUrl . '" target="' . $target . '" class="prosperent-kw">' . $matches[$key] . '</a>';
									}							
									else
									{
										$matches[$key] = '<a href="' . $product_search_url . $query . '" target="' . $target . '" class="prosperent-kw">' . $matches[$key] . '</a>';								
									}						
								}	
							}	
							else
							{
								if (!$options['Enable_PPS'] || $options['LTM'][$i] == 1)
								{
									$matches[$rand_keys] = '<a href="' . $affUrl . '" target="' . $target . '" class="prosperent-kw">' . $matches[$rand_keys] . '</a>';
								}							
								else
								{
									$matches[$rand_keys] = '<a href="' . $product_search_url . $query . '" target="' . $target . '" class="prosperent-kw">' . $matches[$rand_keys] . '</a>';								
								}	
							}
						}
						else
						{
							foreach($matches as $p => $match)
							{
								if (!$options['Enable_PPS'] || $options['LTM'][$i] == 1)
								{
									$matches[$p] = '<a href="' . $affUrl . '" target="' . $target . '" class="prosperent-kw">' . $match . '</a>';
								}							
								else
								{
									$matches[$p] = '<a href="' . $product_search_url . $query . '" target="' . $target . '" class="prosperent-kw">' . $match . '</a>';
								}						
							}	
						}

						$content = array();
						foreach ($newText as $x => $new)
						{
							$content[] = $new . $matches[$x];						
						}			

						$text = implode('', $content);
					}
					else
					{
						if (!isset($options['Enable_PPS']) || isset($options['LTM'][$i]) == 1)
						{					
							$text = preg_replace('/\b' . $old_text . '\b/' . $case, '<a href="' . $affUrl . '" target="' . $target . '" class="prosperent-kw">$0</a>', $text, $limit);
						}
						else
						{
							$text = preg_replace('/\b' . $old_text . '\b/' . $case, '<a href="' . $product_search_url . $query . '" target="' . $target . '" class="prosperent-kw">$0</a>', $text, $limit);
						}
					}
					
					$text = str_ireplace($base, $qText[0], $text);
					
					$i++;
				}		
				
				// Remove links within links
				$text = preg_replace( "#(<a [^>]+>)(.*)<a [^>]+>([^<]*)</a>([^>]*)</a>#iU", "$1$2$3$4</a>" , $text );
			}

            return trim($text);
        }
		
        public function prospere_stylesheets()
        {
            // Product Search CSS for results and search
            wp_register_style( 'prospere_main_style', PROSPER_URL . '/css/products.css', array(), $this->version );
            wp_enqueue_style( 'prospere_main_style' );
        }

        public function store_shortcode()
        {						            
			$this->storeChecker();
			$options = $this->get_option();
		
            ob_start();
            include(PROSPER_PATH . 'products.php');
            $store = ob_get_clean();
            return $store;
        }
		
		public function storeChecker()
		{
			global $prosper_admin;
			$options = $prosper_admin->get_option('prosper_advanced');
			if (!$options['Manual_Base'])
			{
				if (empty($options['Base_URL']) || $options['Base_URL'] != get_post()->post_name)
				{
					if (!is_front_page())
					{
						$options['Base_URL'] = get_post()->post_name;					
					}
					elseif (is_front_page() && get_post()->post_name != 'products')
					{
						$options['Base_URL'] = 'null';
					}
					
					update_option('prosper_advanced', $options);

					$page     = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL'] . '/') : 'products/';
					$pageName = $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : 'pagename=' . $options['Base_URL']) : 'pagename=products';

					
					add_rewrite_rule('local/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
					add_rewrite_rule('travel/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
					add_rewrite_rule('coupon/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
					add_rewrite_rule('product/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
					add_rewrite_rule('celebrity/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
					add_rewrite_rule('store/go/([^/]*)/?', 'index.php?' . $pageName . '&store&go&storeUrl=$matches[1]', 'top');
					add_rewrite_rule('img/([^/]*)/?', 'index.php?' . $pageName . '&prosperImg=$matches[1]', 'top');
					add_rewrite_rule($page . '(.*)', 'index.php?' . $pageName . '&queryParams=$matches[1]', 'top');
					
					flush_rewrite_rules();
				}
			}
		}

        public function search_shortcode()
        {
            $options = $this->get_option();

            ob_start();
            include(PROSPER_PATH . 'search_short.php');
            $search = ob_get_clean();
            return $search;
        }

        public function prosper_title($title, $sep, $seplocation)
        { 
            if ( is_feed() )
            {
                return $title;
            }

            $params = array_reverse(explode('/', get_query_var('queryParams')));

            $sendParams = array();
            if (!empty($params))
            {
                $params = array_reverse($params);
                foreach ($params as $k => $p)
                {
                    //if the number is even, grab the next index value
                    if (!($k & 1))
                    {
                        $sendParams[$p] = $params[$k + 1];
                    }
                }
            }
			
            $options  	= $this->get_option();
            $sep      	= ' ' . (!$options['Title_Sep'] ? !$sep ? '|' : trim($sep) : trim($options['Title_Sep'])) . ' ';
            $page     	= $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']) : 'products';
            $page_num 	= $sendParams['page'] ? ' Page ' . $sendParams['page'] : '';
            $pagename 	= get_the_title();
            $blogname 	= get_bloginfo();
            $brand 	  	= ucwords(rawurldecode($sendParams['brand']));
            $merchant 	= ucwords(rawurldecode($sendParams['merchant']));
            $type 	  	= $sendParams['type'];
			$query 	  	= ($sendParams['query'] ? $sendParams['query'] : (($options['Starting_Query'] && !$brand && !$merchant) ? $options['Starting_Query'] : ''));
            $query 	  	= ucwords(rawurldecode(str_replace('+', ' ', $query)));
            $state 	  	= ucwords(rawurldecode($sendParams['state']));
            $city 	  	= ucwords(rawurldecode($sendParams['city']));
            $zip 		= ucwords(rawurldecode($sendParams['zip']));
            $celeb 		= ucwords(rawurldecode($sendParams['celeb']));
            $celebQuery = ucwords(rawurldecode($sendParams['celebQuery']));
			
            if ('coup' == $type)
            {
                $query = ($sendParams['query'] ? $sendParams['query'] : ($options['Coupon_Query'] ? $options['Coupon_Query'] : ''));
            }

            if (get_query_var('cid'))
            { 
                $query = preg_replace('/\(.+\)/i', '', rawurldecode(get_query_var('keyword')));
                $query = str_replace(',SL,', '/', $query);
                $title = $query . $sep . $title;
            }
            elseif (is_page($page) && !get_query_var('cid'))
            {
                if ('local' == $type)
                {
                    switch ( $options['Title_Structure'] )
                    {
                        case '0':
                            $title =  $title;
                            break;
                        case '1':
                            $title =  $title . $page_num . (($zip || $city || $state) ? $sep : '') . ($zip ? $zip : '') . ($zip && $city ? ' ' : '') . ($city ? $city : '') . (($zip && $state || $state && $city) ? ' ' : '') . ($state ? $state : '');
                            break;
                        case '2':
                            $title = ($zip ? $zip : '') . ($zip && $city ? ' ' : '') . ($city ? $city : '') . (($zip && $state && !$city) ? ' ' : (($state && $city) ? ', ' : '')) . ($state ? $state : '') . (($zip || $city || $state) ? $sep : '') . $pagename . $page_num . $sep . $blogname;
                            break;
                        case '3':
                            $title =  !$zip ? $title : ($zip ? $zip : '') . ($city ?  ' &raquo; ' . $city : '') . ($state ? ' &raquo; ' . $state : '') . $page_num;
                            break;
                        case '4':
                            $title =  $title;
                            break;
                    }
                }
                elseif('cele' == $type)
                {
                    switch ( $options['Title_Structure'] )
                    {
                        case '0':
                            $title =  $title;
                            break;
                        case '1':
                            $title =  $title . $page_num . (($celebQuery || $celeb) ? $sep : '') . ($celebQuery ? $celebQuery : '') . ($celebQuery && $celeb ? ' &raquo; ' : '') . ($celeb ? $celeb : '');
                            break;
                        case '2':
                            $title = ($celebQuery ? $celebQuery : '') . ($celebQuery && $celeb ? ' &raquo; ' : '') . ($celeb ? $celeb : '') . (($celebQuery || $celeb ) ? $sep : '') . $pagename . $page_num . $sep . $blogname;
                            break;
                        case '3':
                            $title =  !$celebQuery ? $title : ($celebQuery ? $celebQuery : '') . ($celeb ?  ' &raquo; ' . $celeb : '') . ($merchant ? ' &raquo; ' . $merchant : '') . $page_num;
                            break;
                        case '4':
                            $title =  $title;
                            break;
                    }
                }
                else
                {
                    switch ( $options['Title_Structure'] )
                    {
                        case '0':
                            $title =  $title;
                            break;
                        case '1':
                            $title =  $title . $page_num . (($query || $brand || $merchant) ? $sep : '') . ($query ? $query : '') . ($query && $brand ? ' &raquo; ' : '') . ($brand ? $brand : '') . (($query && $merchant || $merchant && $brand) ? ' &raquo; ' : '') . ($merchant ? $merchant : '');
                            break;
                        case '2':
                            $title = ($query ? $query : '') . ($query && $brand ? ' &raquo; ' : '') . ($brand ? $brand : '') . (($query && $merchant || $merchant && $brand) ? ' &raquo; ' : '') . ($merchant ? $merchant : '') . (($query || $brand || $merchant) ? $sep : '') . $pagename . $page_num . $sep . $blogname;
                            break;
                        case '3':
                            $title =  !$query ? $title : ($query ? $query : '') . ($brand ?  ' &raquo; ' . $brand : '') . ($merchant ? ' &raquo; ' . $merchant : '') . $page_num;
                            break;
                        case '4':
                            $title =  $title;
                            break;
                    }
                }
            }

            return $title;
        }

        public function autoCompare_shortcode($atts, $content = null)
        {
            $options = $this->get_option();

            extract(shortcode_atts(array(
                'q'  => isset($q) ? $q : '',
                'c'  => isset($c) ? $c : '',
                'b'  => isset($b) ? $b : '',
                'm'  => isset($m) ? $m : '',
                'l'  => isset($l) ? intval($l) : 1,
                'cl' => isset($cl) ? intval($cl) : '',
                'ct' => isset($ct) ? $ct : 'US',
				'id' => isset($id) ? $id : ''
            ), $atts));

            $query = $q ? $q : $content;
			
            // Remove links within links
            $query = strip_tags($query);
            $content = strip_tags($content);

			$b = explode(',', trim($b));
			$m = explode(',', trim($m));

            if (!$c)
            {
				if ($m && $id)
				{
					$catalogId = $id;
					$productId = '';
				}
				else
				{
					$productId = $id;
					$catalogId = '';
				}
				
                $settings = array(
                    'api_key'         => $options['Api_Key'],
                    'query'           => trim($query),
                    'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
                    'limit'           => $l,
                    'enableFacets'    => TRUE,
                    'sortPrice'		  => '',
                    'filterMerchant'  => $m,
                    'filterBrand'	  => $b,
					'filterCatalogId' => $catalogId,
					'filterProductId' => $productId
                );
				
				if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
				{	
					$settings = array_merge($settings, array(
						'cacheBackend'   => 'FILE',
						'cacheOptions'   => array(
							'cache_dir'  => PROSPER_CACHE
						)
					));	
				}
				
				$prosperentApi = new Prosperent_Api($settings);

                switch ($ct)
                {
                    case 'UK':
                        $prosperentApi -> fetchUkProducts();
                        $currency = 'GBP';
                        break;
                    case 'CA':
                        $prosperentApi -> fetchCaProducts();
                        $currency = 'CAD';
                        break;
                    default:
                        $prosperentApi -> fetchProducts();
                        $currency = 'USD';
                        break;
                }
				
                $results = $prosperentApi -> getAllData();

                if ($results)
                {
                    ob_start();
                    include(PROSPER_PATH . 'compare_short.php');
                    $compare = ob_get_clean();
                    return $compare;
                }
				else
				{
					$settings = array(
						'api_key'        => $options['Api_Key'],
						'query'          => trim($query),
						'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
						'limit'          => $l,
						'enableFacets'   => TRUE,
						'sortPrice'		 => '',
						'filterMerchant' => $m,
						'filterBrand'	 => $b
					);
					
					if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
					{	
						$settings = array_merge($settings, array(
							'cacheBackend'   => 'FILE',
							'cacheOptions'   => array(
								'cache_dir'  => PROSPER_CACHE
							)
						));	
					}
					
					$prosperentApi = new Prosperent_Api($settings);

					switch ($ct)
					{
						case 'UK':
							$prosperentApi -> fetchUkProducts();
							$currency = 'GBP';
							break;
						case 'CA':
							$prosperentApi -> fetchCaProducts();
							$currency = 'CAD';
							break;
						default:
							$prosperentApi -> fetchProducts();
							$currency = 'USD';
							break;
					}
					
					$results = $prosperentApi -> getAllData();

					if ($results)
					{
						ob_start();
						include(PROSPER_PATH . 'compare_short.php');
						$compare = ob_get_clean();
						return $compare;
					}
					else
					{
						$settings = array(
							'api_key'      => $options['Api_Key'],
							'query'        => $query,
							'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
							'limit'        => $l,
							'sortPrice'	   => ''
						);

						if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
						{	
							$settings = array_merge($settings, array(
								'cacheBackend'   => 'FILE',
								'cacheOptions'   => array(
									'cache_dir'  => PROSPER_CACHE
								)
							));	
						}
						
						$prosperentApi = new Prosperent_Api($settings);
						
						switch ($ct)
						{
							case 'UK':
								$prosperentApi -> fetchUkProducts();
								$currency = 'GBP';
								break;
							case 'CA':
								$prosperentApi -> fetchCaProducts();
								$currency = 'CAD';
								break;
							default:
								$prosperentApi -> fetchProducts();
								$currency = 'USD';
								break;
						}
						
						$results = $prosperentApi -> getAllData();

						if ($results)
						{
							ob_start();
							include(PROSPER_PATH . 'compare_short.php');
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
            else
            {
				if ($id)
				{
					$couponId = $id;
				}
			
			    $settings = array(
                    'api_key'        => $options['Api_Key'],
                    'query'          => $query,
                    'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
                    'limit'          => $l,
                    'enableFacets'   => TRUE,
                    'sortPrice'		 => '',
                    'filterMerchant' => $m,
					'filterCouponId' => $id
					
                );
				
				if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
				{	
					$settings = array_merge($settings, array(
						'cacheBackend'   => 'FILE',
						'cacheOptions'   => array(
							'cache_dir'  => PROSPER_CACHE
						)
					));	
				}
				
				$prosperentApi = new Prosperent_Api($settings);

                $prosperentApi -> fetchCoupons();
                $results = $prosperentApi -> getAllData();

                if ($results)
                {
                    ob_start();
                    include(PROSPER_PATH . 'compare_coup.php');
                    $compare = ob_get_clean();
                    return $compare;
                }
                else
                {
					$settings = array(
						'api_key'        => $options['Api_Key'],
						'query'          => $query,
						'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
						'limit'          => $l,
						'enableFacets'   => TRUE,
						'sortPrice'		 => '',
						'filterMerchant' => $m
					);
					
					if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
					{	
						$settings = array_merge($settings, array(
							'cacheBackend'   => 'FILE',
							'cacheOptions'   => array(
								'cache_dir'  => PROSPER_CACHE
							)
						));	
					}
					
					$prosperentApi = new Prosperent_Api($settings);

					$prosperentApi -> fetchCoupons();
					$results = $prosperentApi -> getAllData();

					if ($results)
					{
						ob_start();
						include(PROSPER_PATH . 'compare_coup.php');
						$compare = ob_get_clean();
						return $compare;
					}
					else
					{
						$settings = array(
							'api_key'      => $options['Api_Key'],
							'query'        => $query,
							'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
							'limit'        => $l,
							'sortPrice'	   => ''
						);
						
						if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
						{	
							$settings = array_merge($settings, array(
								'cacheBackend'   => 'FILE',
								'cacheOptions'   => array(
									'cache_dir'  => PROSPER_CACHE
								)
							));	
						}					
						
						$prosperentApi = new Prosperent_Api($settings);

						$prosperentApi -> fetchCoupons();
						$results = $prosperentApi -> getAllData();

						if ($results)
						{
							ob_start();
							include(PROSPER_PATH . 'compare_coup.php');
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
        }

        public function linker_shortcode($atts, $content = null)
        {
            $options 			= $this->get_option();
            $target  		    = $options['Target'] ? '_blank' : '_self';
			$base_url   		= $options['Base_URL'] ? ($options['Base_URL'] == 'null' ? '/query/' : $options['Base_URL'] . '/query/') : 'products/query/';
			$product_search_url = site_url('/') . $base_url;	
			
            extract(shortcode_atts(array(
                'q'   => isset($q) ? $q : '',
                'gtm' => isset($gtm) ? 1 : 0,
                'b'   => isset($b) ? $b : '',
                'm'   => isset($m) ? $m : '',
                'ct'  => isset($ct) ? $ct : 'US',
				'id'  => isset($id) ? $id : ''
            ), $atts));

            $query = $q ? $q : $content;
			
			if ($m)
			{
				$catalogId = $id;
				$productId = '';
			}
			else
			{
				$productId = $id;
				$catalogId = '';
			}

			$b = empty($b) ? array() : array_map('trim', explode(',', $b));
			$m = empty($m) ? array() : array_map('trim', explode(',', $m));
			
            // Remove links within links
            $query = strip_tags($query);
			$content = $content ? (preg_match('/<img/i', $content) ? $content : strip_tags($content)) : $query;

            if ($gtm || !$options['Enable_PPS'])
            {
                $settings = array(
                    'api_key'         => $options['Api_Key'],
                    'query'           => $query,
                    'visitor_ip'      => $_SERVER['REMOTE_ADDR'],
                    'limit'           => 1,
                    'enableFacets'    => TRUE,
                    'filterBrand'     => $b,
                    'filterMerchant'  => $m,
					'filterCatalogId' => $catalogId,
					'filterProductId' => $productId,
					'enableFullData' => 0
                );
								
				if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
				{	
					$settings = array_merge($settings, array(
						'cacheBackend'   => 'FILE',
						'cacheOptions'   => array(
							'cache_dir'  => PROSPER_CACHE
						)
					));	
				}
				
				$prosperentApi = new Prosperent_Api($settings);

                switch ($ct)
                {
                    case 'UK':
                        $prosperentApi -> fetchUkProducts();
                        $currency = 'GBP';
                        break;
                    case 'CA':
                        $prosperentApi -> fetchCaProducts();
                        $currency = 'CAD';
                        break;
                    default:
                        $prosperentApi -> fetchProducts();
                        $currency = 'USD';
                        break;
                }
                $results = $prosperentApi -> getAllData();

                if ($results)
                {
                    return '<a href="' . $productPage . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $results[0]['affiliate_url'])) . '" TARGET=' . $target . '" class="prosperent-kw">' . $content . '</a>';
                }
                else
                {
                    $settings = array(
                        'api_key'        => $options['Api_Key'],
                        'query'          => $query,
                        'visitor_ip'     => $_SERVER['REMOTE_ADDR'],
                        'limit'          => 1,							
						'enableFacets'   => TRUE,
						'filterBrand'    => $b,
						'filterMerchant' => $m,
						'enableFullData' => 0
                    );
					
					if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
					{	
						$settings = array_merge($settings, array(
							'cacheBackend'   => 'FILE',
							'cacheOptions'   => array(
								'cache_dir'  => PROSPER_CACHE
							)
						));	
					}
					
					$prosperentApi = new Prosperent_Api($settings);

                    switch ($ct)
                    {
                        case 'UK':
                            $prosperentApi -> fetchUkProducts();
                            $currency = 'GBP';
                            break;
                        case 'CA':
                            $prosperentApi -> fetchCaProducts();
                            $currency = 'CAD';
                            break;
                        default:
                            $prosperentApi -> fetchProducts();
                            $currency = 'USD';
                            break;
                    }
                    $results = $prosperentApi -> getAllData();

                    if ($results)
                    {
                        return '<a href="' . $productPage . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $results[0]['affiliate_url'])) . '" TARGET=' . $target . '" class="prosperent-kw">' . $content . '</a>';
                    }
                    else
                    {
						$settings = array(
							'api_key'        => $options['Api_Key'],
							'query'          => $query,
							'visitor_ip'   	 => $_SERVER['REMOTE_ADDR'],
							'limit'          => 1,
							'enableFullData' => 0
						);
						
						if (file_exists(PROSPER_CACHE) && substr(decoct( fileperms(PROSPER_CACHE) ), 1) == '0777')
						{	
							$settings = array_merge($settings, array(
								'cacheBackend'   => 'FILE',
								'cacheOptions'   => array(
									'cache_dir'  => PROSPER_CACHE
								)
							));	
						}
						
						$prosperentApi = new Prosperent_Api($settings);
						
						if ($results)
						{
							return '<a href="' . $productPage . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $results[0]['affiliate_url'])) . '" TARGET=' . $target . '" class="prosperent-kw">' . $content . '</a>';
						}
						else
						{
							return '<a href="' . $product_search_url . rawurlencode($query) . '" TARGET="' . $target . '" class="prosperent-kw">' . $content . '</a>';
						}
                    }
                }
            }

			$brands = array();
			foreach ($b as $brand)
			{
				if (!preg_match('/^!/', $brand))
				{
					$brands[] = $brand;
				}
			}

			$merchants = array();
			foreach ($m as $merchant)
			{
				if (!preg_match('/^!/', $merchant))
				{
					$merchants[] = $merchant;
				}
			}

            $fB = empty($brands) ? '' : '/brand/' . rawurlencode($brands[0]);
            $fM = empty($merchants) ? '' : '/merchant/' . rawurlencode($merchants[0]);
			

            return '<a href="' . $product_search_url . rawurlencode($query) . $fB . $fM . '" TARGET="' . $target . '" class="prosperent-kw">' . $content . '</a>';
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
                QTags.addButton('auto-linker', 'auto-linker', '[linker q="QUERY" gtm="true" b="BRAND" m="MERCHANT" ct="US"]', '[/linker]', 0);
            </script>
            <?php
        }

        public function qTagsCompare()
        {		
            ?>
            <script type="text/javascript">
                QTags.addButton('auto-compare', 'auto-compare', '[compare q="QUERY" b="BRAND" m="MERCHANT" l="LIMIT" cl="COMPARISON LIMIT" ct="US"]', '[/compare]', 0);
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
            $plugin_array["compare"] = PROSPER_URL . 'js/compare.min.js';
            return $plugin_array;
        }

        public function autoLinker_tiny_add($buttons)
        {
            array_push($buttons, "|", "linker");
            return $buttons;
        }

        public function autoLinker_tiny_register($plugin_array)
        {
            $plugin_array["linker"] = PROSPER_URL . 'js/linker.min.js';
            return $plugin_array;
        }

        public function performAd_shortCode($atts, $content = null)
        {
            extract(shortcode_atts(array(
                'q'   => isset($q) ? $q : '',
				'utt' => isset($utt) ? $utt : 0,
				'utg' => isset($utg) ? $utg : 0,
				'h'   => isset($h) ? $h : 90,
				'w'   => isset($w) ? $w : 'auto'
            ), $atts));
			
            $options = $this->get_option();

			$fallback = array();
			if ($utg)
			{
				$posttags = get_the_tags();
				if ($posttags) 
				{
					foreach($posttags as $tag) 
					{
						$fallback[] = strtolower($tag->name); 
					}
				}				
			}

			if($q)
			{
				$newFallback = explode(',', $q);
				foreach ($newFallback as $fall)
				{
					$fall = strtolower(trim($fall));
					$fallback[] = $fall;
				}
			}

			if($utt)
			{
				$fallback[] = strtolower(get_the_title());
			}
				
			if ($options['Remove_Tags'])
			{
				$removeTags = explode(',', $options['Remove_Tags']);			
				$fbacks = array_flip($fallback);

				foreach ($removeTags as $remove)
				{ 
					$remove = trim($remove);
					if(isset($fbacks[$remove]))
					{
						unset($fbacks[$remove]);
					}
				}	
				$fallback = array_flip($fbacks);		
			}
			
			$fallback = implode(",", $fallback);
			
			$height = $h ? ($h == 'auto' ? '100%' : preg_replace('/px|em|%/i', '', $h) . 'px') : 90 . 'px';
			$width = $w ? ($w == 'auto' ? '100%' : preg_replace('/px|em|%/i', '', $w) . 'px') : '100%';
			            
            return '<p><div class="prosperent-pa" style="height:' . $height . '; width:' . $width . ';" pa_topics="' . $fallback . '"></div></p>';
            
        }
				
		public function performAd_custom_add()
        {
            // Add only in Rich Editor mode
            if (get_user_option('rich_editing') == 'true')
            {
                add_filter('mce_external_plugins', array($this, 'performAd_tiny_register'));
                add_filter('mce_buttons', array($this, 'performAd_tiny_add'));
            }
        }
		
		public function qTagsPerformAd()
        {
            ?>
            <script type="text/javascript">
                QTags.addButton('performance ad', 'performance_ad', '[perform_ad top="TOPIC" h="HEIGHT" w="WIDTH" ut="USE TAGS"]', '[/perform_ad]', 0);
            </script>
            <?php
        }

        public function performAd_tiny_add($buttons)
        {
            array_push($buttons, "|", "performAd");
            return $buttons;
        }

        public function performAd_tiny_register($plugin_array)
        {
            $plugin_array["performAd"] = PROSPER_URL . 'js/performAd.min.js';
            return $plugin_array;
        }		
    }

	global $prosper_suite;
	$prosper_suite = new Prosperent_Suite();
}
