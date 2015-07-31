<?php
require_once(PROSPER_MODEL . '/Base.php');
/**
 * Search Model
 *
 * @package Model
 */
class Model_Inserter extends Model_Base
{
	protected $_shortcode = 'compare';
	
	public $_options;

	public function __construct()
	{
		$this->_options = $this->getOptions();		
	}
	
	public function qTagsInsert()
	{
		$id 	 = 'productInsert';
		$display = 'ProsperInsert';
		$arg1 	 = '[prosperInsert q="QUERY" b="BRAND" m="MERCHANT" l="LIMIT" v="GRID, LIST OR PRICE COMP"]';
		$arg2 	 = '[/prosperInsert]';		
	
		$this->qTagsProsper($id, $display, $arg1, $arg2);
	}
	
	public function newQueries($atts, $content = null)
	{		
		return;
	}
	
	public function contentInserter($text)
	{		
	    if (!$this->_options['PICIAct'])
	    {
	        return $text;
	    }
	    
	    if (($this->_options['prosper_inserter_posts'] && is_singular('post')) || ($this->_options['prosper_inserter_pages'] && (is_page() || (is_plugin_active('woocommerce/woocommerce.php') ? is_product() : ''))))
	    {
	        if (preg_match('/\[prosperNewQuery (.+)\]/i', $text, $regs) || preg_match('/\[contentInsert (.+)\]\[\/contentInsert\]/i', $text, $regs))
    		{
    			if (preg_match('/noShow="on"/', $regs[1]))
    			{
    			    return trim($text);
    			}
    			
    			$insert = '<p>[prosperInsert ' . $regs[1] . '][/prosperInsert]</p>';    			
    		}
	        else
	        {
        		$newTitle = get_the_title();
                
        		if ($this->_options['prosper_inserter_negTitles'])
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
        				explode(',', $this->_options['prosper_inserter_negTitles'])
        			);
        
        			$newTitle = preg_replace($exclude, '', $newTitle);
        		}
        
        		if (!$newTitle)
        		{
        			return trim($text);
        		}
        		
        		if ($this->_options['contentAnalyzer'])
        		{
        			$settings = array(
        				'url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        			);
        
        			$url = $this->apiCall($settings, 'fetchAnalyzer');
        			$allData = $this->singleCurlCall($url, 86400, $settings);
        
        			foreach ($allData['data'] as $newKeyword)
        			{
        				$newKeywords[] = $newKeyword['phrase']; 
        			}		
        		}
        		
        		$insert = '<p>[prosperInsert imgt="' . ($this->_options['prosper_imageType'] ? $this->_options['prosper_imageType'] : 'original') . '" q="' . $newTitle . '" l="' . ($this->_options['PI_Limit'] ? $this->_options['PI_Limit'] : 1) . '" v="' . ($this->_options['prosper_insertView'] ? $this->_options['prosper_insertView'] : 'list') . '" gtm="' . ($this->_options['Link_to_Merc'] ? 1 : 0) . '"][/prosperInsert]</p>';
	        }
        		
    		if ('top' == $this->_options['prosper_inserter'])
    		{
    			$content = $insert . $text;
    		}
    		else
    		{
    			$content = $text . $insert;
    		}
    			
    		$text = $content;
	    }
    		
		return trim($text);
	}
	
	public function inserterShortcode($atts, $content = null)
	{
		if (!$this->_options['PICIAct'])
		{
			return $content;
		}

		wp_register_script('productInsert', PROSPER_JS . '/productInsert.js', array('jquery', 'json2'), $this->_version);
		wp_enqueue_script( 'productInsert');
		
		$target  = $this->_options['Target'] ? '_blank' : '_self';
		$base 	 = $this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products';
		$homeUrl = home_url();
		$page    = get_page_by_path($base);

		$pieces = $this->shortCodeExtract($atts, $this->_shortcode);
		$pieces = array_filter($pieces);

		$fetch = $pieces['ft'];

		if (!$this->_options['shortCodesAccessed'])
		{
			$mainOpts = get_option('prosperSuite');
			$mainOpts['shortCodesAccessed'] = 1;
			update_option('prosperSuite', $mainOpts);
		}	
		
		// Remove links within links
		$content = strip_tags($content);

		if ($pieces['id'] && strpos($pieces['id'], '~'))
		{
		    $id = explode('~', rtrim($pieces['id'], '~'));
		    
		}
		elseif ($pieces['id'])
		{
		    $filterType = ($pieces['ft'] == 'fetchProducts' ? 'Product' : 'Merchant') . 'Id';
            $id = explode(',', rtrim($pieces['id'], ','));
		}

		$limit = 1;		
		if ($pieces['cl'] && $pieces['cl'] > $pieces['l'])
		{
			$limit = $pieces['cl'];
		}
		elseif ($pieces['l'] > 1)
		{		
			$limit = $pieces['l'];
		}
		elseif ($id)
		{
			$limit = count($id);
		}
		
		if ($fetch === 'fetchProducts')
		{
			$expiration = PROSPER_CACHE_PRODS;
			$recordId 	= 'catalogId';
			$type = 'product';			
			$currency = 'USD';	
			
			if ($pieces['v'] == 'pc')
			{
			    $idFilter = array();
			    if (strlen($pieces['id']) == 32 && strpos($pieces['id'], ' '))
			    {
			        $idFilter = array('filter' . $filterType => $pieces['id']);
			    }
			    elseif ($pieces['id'])
			    {
			        $idFilter = array('query' => rtrim(str_replace('_', ' ', $pieces['id']), '~'));
			    } 
			    $type = 'productPC';
			     
			    $settings = array(
			        'curlCall'		     => 'single-' . $type,
			        'query'              => (!$pieces['id'] ? trim(strip_tags($pieces['q'] ? $pieces['q'] : '')) : ''),
			        'imageSize'		     => '250x250',
			        'groupBy'            => 'merchant',
			        'limit'              => 5
			    );
			    $curlUrls[0] = $this->apiCall(array_merge($settings, $idFilter), $fetch);
			}
			elseif (count($id))
            {
			    foreach ($id as $i => $apart)
			    {
			        if ($filterType == 'ProductId')
			        {
    			        $settings[$i] = array(
    			            'curlCall'		  => 'single-' . $type,
    			            'interface'		  => 'insert',
    			            'imageSize'		  => '250x250',
    			            'limit'           => 1,
    			            'filterProductId' => $apart    			           
    			        );
			        }
			        else 
			        {
			            $filterType = 'Keyword';
			            
			            $settings[$i] = array(
			                'curlCall'	=> 'single-' . $type,
			                'interface'	=> 'insert',
			                'imageSize'	=> '250x250',
			                'limit'     => 1,
			                'query'     => $apart
			            );
			        }
			        
			        $curlUrls[$i] = $this->apiCall($settings[$i], $fetch);
			    }			    
			    $settings = array(
			        'curlCall'		  => 'single-' . $type,
			        'interface'		  => 'insert',
			        'imageSize'		  => '250x250',
			        'limit'           => $limit,
			        'query'           => $pieces['q'] ? trim(strip_tags($pieces['q'])) : '',
			        'filterMerchant'  => $pieces['m'] ? str_replace(',', '|', $pieces['m']) : '',
			        'filterBrand'	  => $pieces['b'] ? str_replace(',', '|', $pieces['b']) : '',
			        'filterPriceSale' => $pieces['sale'] ? ($pieces['pr'] ? $pieces['pr'] : '0.01,') : '',
			        'filterPrice' 	  => $pieces['sale'] ? '' : ($pieces['pr'] ? $pieces['pr'] : ''),
			        'filter' . $filterType => implode('|', $id)
			    );
			    
            }
            else 
            {
                $settings = array(
                    'curlCall'		  => 'single-' . $type,
                    'interface'		  => 'insert',
                    'imageSize'		  => '250x250',
                    'limit'           => $limit,
                    'query'           => $pieces['q'] ? trim(strip_tags($pieces['q'])) : '',
                    'filterMerchant'  => $pieces['m'] ? str_replace(',', '|', $pieces['m']) : '',
                    'filterBrand'	  => $pieces['b'] ? str_replace(',', '|', $pieces['b']) : '',
                    'filterPriceSale' => $pieces['sale'] ? ($pieces['pr'] ? $pieces['pr'] : '0.01,') : '',
                    'filterPrice' 	  => $pieces['sale'] ? '' : ($pieces['pr'] ? $pieces['pr'] : '')
                );
                                
                $curlUrls[0] = $this->apiCall($settings, $fetch);
            }			
		}
		elseif ($fetch === 'fetchMerchant')
		{
			$expiration  = PROSPER_CACHE_PRODS;
			$recordId 	 = 'merchantId';
			$type 		 = 'merchant';
			$pieces['v'] = 'grid';

			$settings = array(
				'curlCall'		   => 'single-' . $type,
				'imageSize'		   => '120x60',
				'interface'		   => 'insert',
				'limit'            => $limit,			    
				'filterMerchant'   => (!$id ? str_replace(',', '|', $pieces['m']) : ''),		
				'filterMerchantId' => $id,
			    'filterCategory'   => !$id && $pieces['cat'] ? '*' . $pieces['cat'] . '*' : '',
				'imageType'		   => $pieces['imgt'] ? $pieces['imgt'] : 'original'            		
			);
			
			$curlUrls[0] = $this->apiCall($settings, $fetch);
		}		

		if (count($settings) < 5)
		{
			return;
		}		

		$allData = $this->multiCurlCall($curlUrls, PROSPER_CACHE_PRODS, $settings);

		$everything = array();
		if ($pieces['v'] == 'pc' || (count($allData) == 1))
		{
		    $everything = $allData[0];		    
		}
		else 
		{		
    		foreach($allData as $i => $record)
    		{
    		    if ($record['data'])
    		    { 
    		        $everything['data'][$i] = $record['data'][0];
    		    }
    		    elseif ($pieces['fb'])
    		    {
    		        $fallback = explode('~', $pieces['fb']);
    		        $fallbackSettings = str_replace($id[$i], '', $fallback[$i]);
    
    		        $params = explode('_', $fallbackSettings);
    		        
    		        $sendParams = array();
    		        foreach ($params as $k => $p)
    		        {
    		            //if the number is even, grab the next index value
    		            if (!($k & 1) && $p)
    		            {
    		                $sendParams[$p] = $params[$k + 1];
    		            }
    		        }
    		        
    		        $url = $this->apiCall($newSettings = array_merge(array(
        		            'curlCall'	=> 'single-' . $type,
                            'interface'	=> 'insert',
                            'imageSize'	=> '250x250',
                            'limit'     => 1), 
    		            $sendParams), $fetch);
    
    		        $allData = $this->singleCurlCall($url, $expiration, $newSettings);
    
    		        if (!$allData['data'])
    		        {
        		        $count = count($settings);
        		        for ($i = 0; $i <= $count; $i++)
        		        {
            		        array_pop($settings);
            		        
            		            if(count($settings) < 5)
            		            {
            		            return;
            		        }
            		        	
            		        $url = $this->apiCall($settings, $fetch);
            		        $allData = $this->singleCurlCall($url, $expiration, $settings);
            		        
            		        if ($allData['data'])
            		        {
            		            break;
            		        }
        		        }        		        
    		        }
    		        $everything['data'][$i] = $allData['data'][0];
    		    }
    		    else 
    		    {
    		        $count = count($settings);
    		        for ($i = 0; $i <= $count; $i++)
    		        {
        		        array_pop($settings);
        		        
        		        if(count($settings) < 5)
        		        {
        		            return;
        		        }
        		        	
        		        $url = $this->apiCall($settings, $fetch);
        		        $allData = $this->singleCurlCall($url, $expiration, $settings);
        		        
        		        if ($allData['data'])
        		        {
        		            break;
        		        }
    		        }
    		        $everything['data'][$i] = $allData['data'][0];
    		    }
    		}		
		}
		$results = $everything['data'];

		$prodSubmit = home_url('/') . $base;	

		$insertProd = PROSPER_VIEW . '/prosperinsert/insertProd.php';
		
		// Inserter PHTML file
		if ($this->_options['Set_Theme'] != 'Default')
		{
			$dir = PROSPER_THEME . '/' . $this->_options['Set_Theme'];
			if($newTheme = glob($dir . "/*.php"))
			{			
				foreach ($newTheme as $theme)
				{
					if (preg_match('/insertProd.php/i', $theme))
					{
						$insertProd = $theme;
					}			
				}
			}
		}
		
		ob_start();
		require($insertProd);
		$insert = ob_get_clean();
		return $insert;
	}
}