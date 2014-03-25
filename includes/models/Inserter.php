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
		$display = 'Product Insert';
		$arg1 	 = '[compare q="QUERY" b="BRAND" m="MERCHANT" l="LIMIT" ct="US" gtm="GO TO MERCHANT?" c="USE COUPONS?" v="GRID OR LIST"]';
		$arg2 	 = '[/compare]';		
	
		$this->qTagsProsper($id, $display, $arg1, $arg2);
	}
	
	public function contentInserter($text)
	{		
		$newTitle = get_the_title();

		if (preg_match('/\[prosperNewQuery="(.+)"\]/i', $text, $regs))
		{
			$newTitle = $regs[1];
			$text = preg_replace('/\[prosperNewQuery="(.+)"\]/i', '', $text);
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
		
		$insert = '<p>[compare q="' . $newTitle . '" l="' . ($this->_options['PI_Limit'] ? $this->_options['PI_Limit'] : 1) . '" v="' . ($this->_options['prosper_insertView'] ? $this->_options['prosper_insertView'] : 'list') . '" gtm="' . ($this->_options['Link_to_Merc'] ? 1 : 0) . '"][/compare]</p>';
		
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
			if( is_singular() && is_main_query() ) 
			{
				$text = $content;
			}
			
			if(is_single()) 
			{
				$text = $content;	
			}
		}
		elseif($this->_options['prosper_inserter_posts'])
		{
			if(is_single()) 
			{
				$text = $content;
			}				
		}
		elseif($this->_options['prosper_inserter_pages'])
		{
			if( is_singular() && is_main_query() ) 
			{
				$text = $content;
			}
		}		

		return trim($text);
	}
	
	public function inserterShortcode($atts, $content = null)
	{
		$target  = $this->_options['Target'] ? '_blank' : '_self';
		$base 	 = $this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products';
		$homeUrl = home_url();
		$type 	 = 'product';

		$pieces = $this->shortCodeExtract($atts, $this->_shortcode);
		
		// Remove links within links
		$content = strip_tags($content);

		if (!$pieces['c'] || $pieces['c'] === 'false')
		{
			if ($pieces['ct'] === 'UK')
			{
				$fetch = 'fetchUkProducts';
				$currency = 'GBP';
			}
			elseif ($pieces['ct'] === 'CA')
			{
				$fetch = 'fetchCaProducts';
				$currency = 'CAD';
			}
			else 
			{
				$fetch = 'fetchProducts';
				$currency = 'USD';
			}	

			$settings = array(
				'imageSize'		  => $pieces['v'] === 'grid' ? '250x250' : '125x125',
				'limit'           => ($pieces['cl'] && $pieces['cl'] > $pieces['l']) ? $pieces['cl'] : ($pieces['l'] || $pieces['l'] > 1) ? $pieces['l'] : 1,
				'query'           => trim(strip_tags($pieces['q'] ? $pieces['q'] : $content)),
				'filterMerchant'  => $pieces['m'] ? explode(',', trim($pieces['m'])) : '',
				'filterBrand'	  => $pieces['b'] ? explode(',', trim($pieces['b'])) : '',				
				'filterProductId' => !$pieces['m'] && $pieces['id'] ? $pieces['id'] : '',
				'filterCatalogId' => $pieces['m'] && $pieces['id'] ? $pieces['id'] : ''
			);

			$settings = array_filter($settings);

			if (count($settings) < 3)
			{
				return;
			}
			
			$allData = $this->apiCall($settings, $fetch);

			if (!$allData['results'])
			{
				$count = count($settings);
				for ($i = 0; $i <= $count; $i++)
				{
					array_pop($settings);

					if(count($settings) < 3)
					{
						return;
					}
				
					$allData = $this->apiCall($settings, $fetch);
					
					if ($allData['results'])
					{
						break;
					}	 
				}
			}
			
			$prodSubmit = home_url('/') . $base;
		}
		else
		{		
			$settings = array(
				'imageSize'		 => '120x60',
				'limit'          => ($pieces['cl'] && $pieces['cl'] > $pieces['l']) ? $pieces['cl'] : ($pieces['l'] || $pieces['l'] > 1) ? $pieces['l'] : 1,
				'query'          => trim(strip_tags($pieces['q'] ? $pieces['q'] : $content)),
				'filterMerchant' => $pieces['m'] ? explode(',', trim($pieces['m'])) : '',		
				'filterCouponId' => $pieces['id'] ? $pieces['id'] : '',
			);

			$settings = array_filter($settings);
	
			$imageLoader = 'small';
			$type = 'coupon';
			
			if (count($settings) < 3)
			{
				return;
			}
		
			$allData = $this->apiCall($settings, 'fetchCoupons');

			if (!$allData['results'])
			{
				$count = count($settings);
				for ($i = 0; $i <= $count; $i++)
				{
					array_pop($settings);

					if(count($settings) < 3)
					{
						return;
					}
				
					$allData = $this->apiCall($settings, $fetch);
					
					if ($allData['results'])
					{
						break;
					}	 
				}
			}	
		}
		
		// CHECK INTO THIS AFTER STORE IS COMPLETE
		if (!$this->_options['Enable_PPS'])
		{
			if ($storeUrl = get_query_var('storeUrl'))
			{    
				$storeUrl = rawurldecode($storeUrl);
				$storeUrl = str_replace(',SL,', '/', $storeUrl);
				header('Location:http://prosperent.com/' . $storeUrl);
				exit;
			}
		}
		
		$results = $allData['results'];
		
		ob_start();
		require(PROSPER_VIEW . '/prosperinsert/insertProd.php');
		$insert = ob_get_clean();
		return $insert;
	}
}