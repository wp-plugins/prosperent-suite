<?php
   require_once('Prosperent.php');

class prodSearch extends Prosperent_WP
{
		public static $instance;
		
		/**
         * Initializes the plugin's configuration and localizable text variables.
         *
         * @return void
         */
        public function load_config()
        {
            $this->name      = __('Product Search', $this->textdomain);
            $this->menu_name = __('Product Search', $this->textdomain);

            $this->config = array(
                'blank1' => array('input' => 'custom', 'label' => __('<strong style="font-size:14px; text-decoration:underline;"><p>General Settings</p></strong>', $this->textdomain),
                ),
                'UID'	=> array('input' => 'text',
                    'label' => __('Your Prosperent User-ID', $this->textdomain)
                ),
                'Api_Key' => array('input' => 'text',
                    'label' => __('Your API Key.', $this->textdomain)
                ),
                'Target' => array('input' => 'checkbox', 'default' => true,
                    'label'  => __('Open Links in New Window or Tab', $this->textdomain),
                    'help'   => '<b>Checked</b> = <b>_blank</b>: opens link in a new window or tab<p><b>Unchecked</b> = <b>_self</b>: opens link in the same window<p><b>Will Not Change the Functionality of the Ads</b>',
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
            parent::options_page_description(__('Product Search', $this->textdomain));

            echo '<p>' . __('All the Prosperent Plugins Bundled as one.', $this->textdomain);
            echo '<p>' . __('Each add-on has its own Enable option. So you choose which to run on your blog.', $this->textdomain);
            echo '<p>' . __('The Prosperent Tools in this plugin bundle are:', $this->textdomain);
                echo "<blockquote><code>Prosperent Product Search</code></blockquote>";
                echo "<blockquote><code>Prosperent Auto-Linker</code></blockquote>";
                echo "<blockquote><code>Prosperent Auto-Comparer</code></blockquote>";
                echo "<blockquote><code>Prosperent Performance-Ads</code></blockquote>";
            echo '<p>' . __('If you have any questions, feel free to ask it at the <a href="http://community.prosperent.com/forumdisplay.php?33-Prosperent-Plugins">Prosperent forums</a>, or email me at <a href="mailto:brandon@prosperent.com">brandon@prosperent.com</a>', $this->textdomain);
        }

}

new prodSearch();