<?php
/*
Plugin Name: Prosperent Suite (Contains Performance Ads, Product Search, Auto-Linker and Auto-Comparer)
Description: Contains all of the Prosperent tools in one plugin to easily monetize your blog.
Version: 2.0.3
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

if (!defined('PROSPER_URL'))
    define('PROSPER_URL', plugin_dir_url(__FILE__));
if (!defined('PROSPER_PATH'))
    define('PROSPER_PATH', plugin_dir_path(__FILE__));
if (!defined('PROSPER_BASENAME'))
    define('PROSPER_BASENAME', plugin_basename(__FILE__));

if (!class_exists('Prosperent_Suite'))
{
    require PROSPER_PATH . 'admin/admin.php';

    class Prosperent_Suite extends Prosperent_Admin
    {
        /**
         * Constructor
         *
         * @return void
         */
        public function __construct()
        {
            add_action( 'init', array($this, 'prosper_query_tag'), 1 );
            add_action('init', array($this, 'do_output_buffer'));

            $options = $this->get_option();

            register_activation_hook(__FILE__, array($this, 'prosper_activate'));
            register_deactivation_hook( __FILE__, array($this, 'prosperent_store_remove'));

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
                add_shortcode('prosper_product', array($this, 'product_shortcode'));
                add_action('prospere_header', array($this, 'Prospere_Search'));
                add_action('wp_title', array($this, 'prosper_title'), 10, 3);

                require_once('TP_Widget.php');
                require_once('PS_Widget.php');
            }
            else
            {
                add_action('admin_init', array($this, 'prosperent_store_remove'));
            }
        }

        /**
         * Retrieve an array of all the options the plugin uses. It can't use only one due to limitations of the options API.
         *
         * @return array of options.
         */
        public function get_prosper_options_array()
        {
            $optarr = array( 'prosperSuite', 'prosper_productSearch', 'prosper_performAds', 'prosper_autoComparer', 'prosper_autoLinker', 'prosper_prosperLinks', 'prosper_advanced' );

            return apply_filters( 'prosper_options', $optarr );
        }

        /**
         * Retrieve all the options
         *
         * @return array of options
         */
        public function get_option()
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

            $this->prosper_rewrite();

            $this->prosper_flush_rules();
        }

        /**
         * Flush the rewrite rules.
         */
        public function prosper_flush_rules()
        {
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
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
            $page     = (!$options['Base_URL'] ?  'products' : ($options['Base_URL'] == 'null' ? '' : $options['Base_URL']));
            $pageName = (!$options['Base_URL'] ?  'pagename=products' : ($options['Base_URL'] == 'null' ? '' : 'pagename=' . $options['Base_URL']));

            add_rewrite_rule('local/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
            add_rewrite_rule('travel/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
            add_rewrite_rule('coupon/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
            add_rewrite_rule('product/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
            add_rewrite_rule('celebrity/([^/]*)/cid/([^/]*)/?', 'index.php?' . $pageName . '&keyword=$matches[1]&cid&cid=$matches[2]', 'top');
            add_rewrite_rule('store/go/([^/]*)/?', 'index.php?' . $pageName . '&go&storeUrl=$matches[1]', 'top');
            add_rewrite_rule('img/([^/]*)/?', 'index.php?' . $pageName . '&prosperImg=$matches[1]', 'top');
            add_rewrite_rule($page . '/(.*)', 'index.php?' . $pageName . '&queryParams=$matches[1]', 'top');
        }

        public function base_url()
        {
            global $wp_rewrite;

            add_rewrite_rule($options['Base_URL'] . '/(.*)', 'index.php?pagename=products&queryParams=$matches[1]', 'top');
            $wp_rewrite->flush_rules();
        }

        public function prosper_default()
        {
            $old_options = get_option('prosper_prosperent_suite');

            if (!is_array(get_option('prosperSuite')))
            {
                if (is_array($old_options))
                {
                    $opt = array(
                        'UID'     => $old_options['UID'],
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
                        'Enable_PA'        => $old_options['Enable_PA'],
                        'SWH' 		 	   => $old_options['SWH'],
                        'SWW'   		   => $old_options['SWW'],
                        'FWH'      		   => $old_options['FWH'],
                        'FWW' 		 	   => $old_options['FWW'],
                        'content_fallBack' => $old_options['content_fallBack'],
                        'sidebar_fallBack' => $old_options['sidebar_fallBack'],
                        'footer_fallBack'  => $old_options['footer_fallBack']
                    );
                }
                else
                {
                    $opt = array(
                        'Enable_PA'        => 1,
                        'SWH' 	 		   => 150,
                        'SWW'  	 		   => 'auto',
                        'FWH'    		   => 150,
                        'FWW' 			   => 'auto',
                        'content_fallBack' => '',
                        'sidebar_fallBack' => '',
                        'footer_fallBack'  => ''
                    );
                }
                update_option( 'prosper_performAds', $opt );
            }

            if (!is_array(get_option('prosper_autoComparer')))
            {
                if (is_array($old_options))
                {
                    $opt = array(
                        'Enable_AC' => $old_options['Enable_AC']
                    );
                }
                else
                {
                    $opt = array(
                        'Enable_AC' => 1
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
                        'Auto_Link' 		 => $old_options['Auto_Link'],
                        'Auto_Link_Comments' => $old_options['Auto_Link_Comments'],
                        'Case_Sensitive' 	 => $old_options['Case_Sensitive']
                    );
                }
                else
                {
                    $opt = array(
                        'Enable_AL' 		 => 1,
                        'Auto_Link'			 => 'shoes => Nike shoes',
                        'Auto_Link_Comments' => 0,
                        'Case_Sensitive' 	 => 0
                    );
                }
                update_option( 'prosper_autoLinker', $opt );
            }

            if (!is_array(get_option('prosper_advanced')))
            {

                    $opt = array(
                        'Title_Structure' => 0,
                        'Title_Sep'		  => '',
                        'Base_URL' 		  => '',
                        'Additional_CSS'  => '',
                        'Logo_Image' 	  => 0,
                        'Logo_imageSmall' => 0
                    );

                update_option( 'prosper_advanced', $opt );
            }
        }

        /**
         * Override the plugin framework's register_filters() to actually hook actions and filters.
         *
         * @return void
         */
        public function register_filters()
        {
            $options = $this->get_option();
            if ($options['Enable_AL'])
            {
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

                        $new_text = '<a href="' . (!$base_url ? '/products/query/' : '/' . $base_url . '/query/') . urlencode($query) . '" target="' . $target . '" class="prosperent-kw">' . $old_text . '</a>';
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

            wp_register_style( 'prospere_product_style', plugins_url('/css/productPage.css', __FILE__) );
            wp_enqueue_style( 'prospere_product_style' );

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
            $options = $this->get_option();

            ob_start();
            include(plugin_dir_path(__FILE__) . 'search_short.php');
            $search = ob_get_clean();
            return $search;
        }

        public function prosper_title($sep, $seplocation, $title)
        {
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

            $options = $this->get_option();

            $sep = ' ' . (!$options['Title_Sep'] ? trim($sep) : ' ' . trim($options['Title_Sep'])) . ' ';
            $page = !$options['Base_URL'] ? 'products' : $options['Base_URL'];
            $query = ucwords(urldecode($sendParams['query'] ? $sendParams['query'] : $options['Starting_Query']));

            if (get_query_var('cid'))
            {
                $query = preg_replace('/\(.+\)/i', '', urldecode(get_query_var('keyword')));
                $title = ' ' . $query . ' ';
            }
            elseif (is_page($page))
            {
                switch ( $options['Title_Structure'] )
                {
                    case 0:
                        $title = $title;
                        break;
                    case 1:
                        $title =  $title . (!$query ? $sep :  $seperator . $query . $sep);
                        break;
                    case 2:
                        $title =  (!$query ? '' : $query . $seperator) . $title;
                        break;
                    case 3:
                        $title =  (!$query ? $title : $query . $sep);
                        break;
                    case 4:
                        $title =  $title;
                        break;
                }
            }

            return $title;
        }


        public function Prospere_Search()
        {
            $options = $this->get_option();
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
            $options = $this->get_option();

            extract(shortcode_atts(array(
                'q'  => isset($q) ? $q : '',
                'c'  => isset($c) ? $c : '',
                'b'  => isset($b) ? $b : '',
                'm'  => isset($m) ? $m : '',
                'l'  => isset($l) ? intval($l) : 1,
                'cl' => isset($cl) ? intval($cl) : 3
            ), $atts));

            $query = $q ? $q : $content;

            // Remove links within links
            $query = strip_tags($query);
            $content = strip_tags($content);

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
                        'api_key'      => $options['Api_Key'],
                        'query'        => $query,
                        'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
                        'limit'        => $l,
                        'sortPrice'	   => ''
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
                        'api_key'      => $options['Api_Key'],
                        'query'        => $query,
                        'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
                        'limit'        => $l,
                        'sortPrice'	   => ''
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
            $options = $this->get_option();
            $target   = $options['Target'] ? '_blank' : '_self';

            extract(shortcode_atts(array(
                'q'   => isset($q) ? $q : '',
                'gtm' => isset($gtm) ? $gtm : '',
                'b'   => isset($b) ? $b : '',
                'm'   => isset($m) ? $m : ''
            ), $atts));

            $query = $q ? $q : $content;

            // Remove links within links
            $query = strip_tags($query);
            $content = strip_tags($content);

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
                    return '<a href="' . $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $results[0]['affiliate_url'])) . '" TARGET=' . $target . '" class="prosperent-kw">' . $content . '</a>';
                }
                else
                {
                    require_once('Prosperent_Api.php');
                    $prosperentApi = new Prosperent_Api(array(
                        'api_key'      => $options['Api_Key'],
                        'query'        => $query,
                        'visitor_ip'   => $_SERVER['REMOTE_ADDR'],
                        'limit'        => 1
                    ));

                    $prosperentApi -> fetch();
                    $results = $prosperentApi -> getAllData();

                    if ($results)
                    {
                        return '<a href="' . $productPage . '/store/go/' . urlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $results[0]['affiliate_url'])) . '" TARGET=' . $target . '" class="prosperent-kw">' . $content . '</a>';
                    }
                    else
                    {
                        return;
                    }
                }
            }

            $fB = empty($b) ? '' : '/brand/' . urlencode($b);
            $fM = empty($m) ? '' : '/merchant/' . urlencode($m);

            return '<a href="' . (!$options['Base_URL'] ? '/products' : '/' . $options['Base_URL']) . '/query/' . urlencode($query) . $fB . $fM . '" TARGET="' . $target . '" class="prosperent-kw">' . $content . '</a>';
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
            $options = $this->get_option();

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
