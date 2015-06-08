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
	    
		$newTitle = get_the_title();

		if (preg_match('/\[prosperNewQuery (.+)\]/i', $text, $regs))
		{
			preg_match_all('/([^=]*?)=?"([^"]*)" ?/i', $regs[1], $results, PREG_PATTERN_ORDER);
			$allParams = array_combine($results[1], $results[2]);
	
			if ($allParams['noShow'])
			{
				return trim($text);
			}
		}

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
		
		$insert = '<p>[compare q="' . ($allParams['q'] ? $allParams['q'] : $newTitle) . '" b="' . $allParams['b'] . '" m="' . $allParams['m'] . '" l="' . ($this->_options['PI_Limit'] ? $this->_options['PI_Limit'] : 1) . '" v="' . ($this->_options['prosper_insertView'] ? $this->_options['prosper_insertView'] : 'list') . '" gtm="' . ($this->_options['Link_to_Merc'] ? 1 : 0) . '"][/compare]</p>';

		if ('top' == $this->_options['prosper_inserter'])
		{
			$content = $insert . $text;
		}
		else
		{
			$content = $text . $insert;
		}
		
		if ($this->_options['prosper_inserter_pages'] && $this->_options['prosper_inserter_posts'])
		{
			if( is_page() ) 
			{
				$text = $content;
			}
			
			if(is_singular('post')) 
			{
				$text = $content;	
			}
		}
		elseif($this->_options['prosper_inserter_posts'] && is_singular('post'))
		{
			$text = $content;			
		}
		elseif($this->_options['prosper_inserter_pages'] && is_page())
		{
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
	    
		$target  = $this->_options['Target'] ? '_blank' : '_self';
		$base 	 = $this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products';
		$homeUrl = home_url();
		$type 	 = 'product';
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

		$id = $pieces['id'] ? str_replace(',', '|', $pieces['id']) : '';
		
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

			$settings = array(
				'curlCall'		  => 'single-' . $type,
				'interface'		  => 'insert',
				'imageSize'		  => $pieces['v'] === 'grid' && $pieces['gimgsz'] > 125 ? '250x250' : '125x125',
				'limit'           => $limit,
				'query'           => trim(strip_tags($pieces['q'] ? $pieces['q'] : $content)),
				//'filterKeyword'   => $pieces['k'],
				'filterMerchant'  => $pieces['m'] ? str_replace(',', '|', $pieces['m']) : '',
				'filterBrand'	  => $pieces['b'] ? str_replace(',', '|', $pieces['b']) : '',			
				'filterProductId' => $id,
				'filterPriceSale' => $pieces['sale'] ? ($pieces['pr'] ? $pieces['pr'] : '0.01,') : '',
				'filterPrice' 	  => ($pieces['sale'] ? '' : ($pieces['pr'] ? $pieces['pr'] : ''))				
			);
			
			if ($pieces['v'] == 'pc')
			{
			    $productIds = array_map('trim', explode(',', $params['prodid']));
			     
			    $settings = array(
			        'query'           => trim($params['prodq'] ? $params['prodq'] : 'shoes'),
			        'filterProductId' => $id,
			        'imageSize'		  => '250x250',
			        'groupBy'         => 'merchant',
			        'limit'           => 5,
			    );
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
				'filterMerchant'   => str_replace(',', '|', $pieces['m']),		
				'filterMerchantId' => $id,
			    'filterCategory'   => $pieces['cat'] ? '*' . $pieces['cat'] . '*' : '',
				'imageType'		   => $pieces['imgt'] ? $pieces['imgt'] : 'original'            		
			);
		}		
		
		if (count($settings) < 5)
		{
			return;
		}

		$url = $this->apiCall($settings, $fetch);
		$allData = $this->singleCurlCall($url, $expiration, $settings);

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
		
		$prodSubmit = home_url('/') . $base;	
		
		$results = $allData['data'];

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