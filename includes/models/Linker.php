<?php
require_once(PROSPER_MODEL . '/Base.php');
/**
 * Search Model
 *
 * @package Model
 */
class Model_Linker extends Model_Base
{
	protected $_shortcode = 'linker';
	
	protected $_options;

	public function __construct()
	{
		$this->_options = $this->getOptions();
		$this->registerFilters();
	}
	
	public function registerFilters()
	{
		add_filter('the_content', array($this, 'autoLinker'), 2);			
		add_filter('the_excerpt', array($this, 'autoLinker'), 2);
		add_filter('widget_text', array($this, 'autoLinker'), 2);

		// Note that the priority must be set high enough to avoid links inserted by the plugin from
		// getting omitted as a result of any link stripping that may be performed.
		if ($this->_options['Auto_Link_Comments'])
		{
			add_filter('get_comment_text', array($this, 'autoLinker'), 11);
			add_filter('get_comment_excerpt', array($this, 'autoLinker'), 11);
		}
	}	
	
	public function qTagsLinker()
	{
		$id 	 = 'autoLinker';
		$display = 'Auto-Linker';
		$arg1 	 = '[linker q="QUERY" gtm="true" b="BRAND" m="MERCHANT" ct="US"]';
		$arg2 	 = '[/linker]';		
	
		$this->qTagsProsper($id, $display, $arg1, $arg2);
	}
	
	public function linkerShortcode($atts, $content = null)
	{
		$options   = $this->_options;
		$target    = $options['Target'] ? '_blank' : '_self';
		$base      = $options['Base_URL'] ? $options['Base_URL'] : 'products';
		$homeUrl   = home_url('/');	
		$storeUrl  = $homeUrl . $base;	
			
		$pieces = $this->shortCodeExtract($atts, $this->_shortcode);

		$brands    = $pieces['b'] ? str_replace(',', '|', $pieces['b']) : '';
		$merchants = $pieces['m'] ? str_replace(',', '|', $pieces['m']) : '';

		if (!$options['shortCodesAccessed'])
		{
			$mainOpts = get_option('prosperSuite');
			$mainOpts['shortCodesAccessed'] = 1;
			update_option('prosperSuite', $mainOpts);
		}	
		
		// Remove links within links
		$content = $content ? (preg_match('/<a/i', $content) ? strip_tags($content) : $content) : $query;
		
		$query = trim(strip_tags($pieces['q']));
		if ((!$brands || !$merchants) && !$query)
		{
			$query = $content;
		}
		
		if ($pieces['gtm'] === 'merchant' || !$options['PSAct'] || $pieces['gtm'] === 'true' || $pieces['gtm'] === 'prodPage' || $pieces['ft'] == 'fetchMerchant')
		{			
			if ($pieces['ft'] == 'fetchProducts')
			{		
				$expiration = PROSPER_CACHE_PRODS;
				$type = '';
				$page = $curlType = 'product';
				
				if ($pieces['ct'] === 'UK')
				{
					$fetch = 'fetchUkProducts';
				}
				elseif ($pieces['ct'] === 'CA')
				{
					$fetch = 'fetchCaProducts';
				}
				else 
				{
					$fetch = 'fetchProducts';
				}	
				
				$settings = array(
					'curlCall'		  => 'single-' . $curlType,
					'interface'		  => 'linker',
					'enableFullData'  => 'FALSE',
					'limit'           => 1,
					'query'           => $query,
					'filterMerchant'  => $merchants,
					'filterBrand'	  => $brands,
					'filterProductId' => $pieces['id'] ? str_replace(',', '|', $pieces['id']) : '',
					'filterPriceSale' => $pieces['sale'] ? ($pieces['pr'] ? $pieces['pr'] : '0.01,') : '',
					'filterPrice' 	  => ($pieces['sale'] ? '' : ($pieces['pr'] ? $pieces['pr'] : '')),
				);
			}
			elseif ($pieces['ft'] == 'fetchMerchant')
			{			
				$expiration = PROSPER_CACHE_PRODS;
				$fetch = 'fetchMerchant';
				$type = '';
				$page = 'product';
				$curlType = 'merchant';
				
				$settings = array(
					'curlCall'		   => 'single-' . $curlType,
					'interface'		   => 'linker',
					'enableFullData'   => 'FALSE',		
					'limit' 		   => 1,						
					'filterMerchant'   => $merchants,
					'filterMerchantId' => $pieces['id'] ? str_replace(',', '|', $pieces['id']) : '',
				);				
			}	
			else
			{
				$expiration = PROSPER_CACHE_COUPS;
				$fetch = $pieces['ft'];
				
				if ($fetch === 'fetchCoupons')
				{
					$type = '/type/coup/';
					$page = $curlType = 'coupon';
				
					$settings = array(
						'curlCall'		  => 'single-' . $curlType,
						'interface'		 => 'linker',
						'enableFullData' => 'FALSE',
						'limit'          => 1,
						'query'          => $query,
						'filterMerchant' => $merchants,
						'filterCouponId' => str_replace(',', '|', $pieces['id'])
					);				
				}
				elseif ($fetch === 'fetchLocal')
				{
					$expiration = PROSPER_CACHE_COUPS;
					$type = '/type/local/';
					$page = $curlType = 'local';
				
					require_once(PROSPER_MODEL . '/Search.php');
					$searchModel = new Model_Search();
					if (strlen($pieces['state']) > 2)
					{
						$state = $searchModel->states[strtolower($pieces['state'])];
					}
					else
					{
						$state = $pieces['state'];
					}

					$settings = array(
						'curlCall'		  => 'single-' . $curlType,
						'interface'		  => 'linker',
						'limit'           => 1,
						'enableFullData'  => 'FALSE',
						'filterState'	  => $state ? $state : '',
						'filterCity'	  => $pieces['city'] ? str_replace(',', '|', $pieces['city']) : '',
						'filterZipCode'	  => $pieces['z'] ? str_replace(',', '|', $pieces['z']) : '',
						'query'           => trim(strip_tags($pieces['q'] ? $pieces['q'] : $content)),
						'filterMerchant'  => $merchants,
						'filterLocalId'   => str_replace(',', '|', $pieces['id'])				
					);
				}
			}
			
			if (count($settings) < 4)
			{
				return $content;
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
						return $content;
					}
				
					$url = $this->apiCall($settings, $fetch);
					$allData = $this->singleCurlCall($url, $expiration, $settings);
					
					if ($allData['data'])
					{
						break;
					}	 
				}
			}			

			if ($pieces['ft'] == 'fetchMerchant')
			{
				if ($allData['data'][0]['deepLinking'] == 1)
				{	
					if ($options['prosperSid'] && !$sid)
					{
						foreach ($options['prosperSid'] as $sidPiece)
						{
							if ('blogname' === $sidPiece)
							{
								$sidArray[] = get_bloginfo('name');
							}
							elseif ('interface' === $sidPiece)
							{
								$sidArray[] = $settings['interface'] ? $settings['interface'] : 'api';
							}
							elseif ('query' === $sidPiece)
							{
								$sidArray[] = $settings['query'];
							}
							elseif ('page' === $sidPiece)
							{
								$sidArray[] = get_the_title();
							}
						}
					}
					if ($options['prosperSidText'] && !$sid)
					{
						if (preg_match('/(^\$_(SERVER|SESSION|COOKIE))\[(\'|")(.+?)(\'|")\]/', $options['prosperSidText'], $regs))
						{
							if ($regs[1] == '$_SERVER')
							{
								$sidArray[] = $_SERVER[$regs[4]];
							}
							elseif ($regs[1] == '$_SESSION')
							{
								$sidArray[] = $_SESSION[$regs[4]];
							}
							elseif ($regs[1] == '$_COOKIE')
							{
								$sidArray[] = $_COOKIE[$regs[4]];
							}					
						}
						elseif (!preg_match('/\$/', $options['prosperSidText']))
						{
							$sidArray[] = $options['prosperSidText'];
						}
					}
					
					if ($sidArray)
					{
						$sidArray = array_filter($sidArray);
						$sid = implode('_', $sidArray);
					}
				
					if ($allData['data'][0]['domain'] == 'sportsauthority.com')
					{
						$allData['data'][0]['domain'] = $allData['data'][0]['domain'] . '%2Fhome%2Findex.jsp';
					}
				
					$affUrl = 'http://prosperent.com/api/linkaffiliator/redirect?apiKey=' . $options['Api_Key'] . '&sid=' . $sid . '&url=' . rawurlencode('http://' . $allData['data'][0]['domain']);
					$rel = 'nofollow,nolink';
				}
				else
				{
					return $content;
				}
			}	
			elseif ($pieces['gtm'] === 'merchant' || !$options['PSAct'] || $pieces['gtm'] === 'true')
			{			
				$affUrl = $allData['data'][0]['affiliate_url'];
				$rel = 'nofollow,nolink';
				$checkClass =  'shopCheck';
			}
			else if ($pieces['gtm'] === 'prodPage')
			{				
				$affUrl = $homeUrl . $page . '/' . rawurlencode(str_replace('/', ',SL,', $allData['data'][0]['keyword'])) . '/cid/' . $allData['data'][0]['catalogId'];
				$rel = 'nolink';
			}

			return '<a class="shopCheck" href="' . $affUrl . '" TARGET=' . $target . '" class="prosperent-kw" class="' . $checkClass . '" rel="' . $rel . '">' . $content . '</a>';
		}

		$fB = '';
		if ($brands)
		{			
			foreach ($brands as $brand)
			{
				if (!preg_match('/^!/', $brand))
				{
					$fB = '/brand/' . $brand;
					break;
				}
			}
		}
		
		$fM = '';
		if ($merchants)
		{
			foreach ($merchants as $merchant)
			{
				if (!preg_match('/^!/', $merchant))
				{
					$fM = $merchant;
				}
			}
		}

		$query = isset($pieces['q']) ? '/query/' . rawurlencode($pieces['q']) : '';

		if ($fB || $fM || $query)
		{
			return '<a href="' . $storeUrl . $query . $fB . $fM . $type . '" TARGET="' . $target . '" class="prosperent-kw">' . $content . '</a>';
		}
		else 
		{
			return $content;
		}
	}
	
	/**
	 * Perform auto-linker
	 *
	 * @param string $text
	 * @return string
	 */
	public function autoLinker($text)
	{	
		$options 		  = $this->_options;
		$random 		  = FALSE;
		$base   		  = $options['Base_URL'] ? $options['Base_URL'] . '/query/' : 'products/query/';
		$target 		  = $options['Target'] ? '_blank' : '_self';
		$productSearchUrl = home_url('/') . $base;	

		if ($options['Country'] == 'US')
		{
			$fetch = 'fetchProducts';
		}
		elseif ($options['Country'] == 'CA')
		{
			$fetch = 'fetchCaProducts';
		}
		else 
		{
			$fetch = 'fetchUkProducts';
		}			
		
		$text = ' ' . $text . ' ';
		if (!empty($options['Match'][0]))
		{
			$val = array();
			foreach ($options['Match'] as $i => $match)
			{			
				if (!empty($match))
				{
					$val[$match] =  $options['Query'][$i] ? $options['Query'][$i] : $match;
				}
			}
			
			$i = 0;				
			foreach ($val as $oldText => $newText)
			{ 				
				$limit = $options['PerPage'][$i] ? $options['PerPage'][$i] : 5;
				$case  = isset($options['Case'][$i]) ? '' : 'i';
				
				if (!preg_match('/' . $oldText . '/' . $case, $text))
				{
					continue;
					
				}

				$query = rawurlencode(trim($newText));	
				//$qText = 'q="' . $oldText . '"';
				preg_match('/q=\".+?\"/', $text, $qText);

				$settings = array(
					'enableFullData'  => FALSE,
					'limit'           => 1,
					'query'           => $newText,
					'groupBy'		  => 'productId',
					'interface'		  => 'linker',
					'curlCall'		  => 'single-product'
				);

				
				$url = $this->apiCall($settings, $fetch);
				$allData = $this->singleCurlCall($url, PROSPER_CACHE_PRODS, $settings);
				
				$text = str_ireplace($qText[0], $base = base64_encode($qText[0]), $text);
				
				if ($random)
				{							
					preg_match_all('/\b' . $oldText . '\b/' . $case, $text, $matches, PREG_PATTERN_ORDER);

					$matches = $matches[0];

					if($case == 'i')
					{
						$oldText = strtolower($oldText);
						$text = preg_replace('/\b' . $oldText . '\b/i', $oldText, $text);						
					}

					$newText = explode($oldText, $text);
					
					if ($limit < count($matches))
					{
						$rand_keys = array_rand($matches, $limit);
						
						if ($limit > 1)
						{
							foreach($rand_keys as $key)
							{
								if (!$options['PSAct'] || $options['LTM'][$i] == 1)
								{
									$matches[$key] = '<a href="' . $allData['data'][0]['affiliate_url'] . '" target="' . $target . '" class="prosperent-kw">' . $matches[$key] . '</a>';
								}							
								else
								{
									$matches[$key] = '<a href="' . $productSearchUrl . $query . '" target="' . $target . '" class="prosperent-kw">' . $matches[$key] . '</a>';								
								}						
							}	
						}	
						else
						{
							if (!$options['PSAct'] || $options['LTM'][$i] == 1)
							{
								$matches[$rand_keys] = '<a href="' . $allData['data'][0]['affiliate_url'] . '" target="' . $target . '" class="prosperent-kw">' . $matches[$rand_keys] . '</a>';
							}							
							else
							{
								$matches[$rand_keys] = '<a href="' . $productSearchUrl . $query . '" target="' . $target . '" class="prosperent-kw">' . $matches[$rand_keys] . '</a>';								
							}	
						}
					}
					else
					{
						foreach($matches as $p => $match)
						{
							if (!$options['PSAct'] || $options['LTM'][$i] == 1)
							{
								$matches[$p] = '<a href="' . $allData['data'][0]['affiliate_url'] . '" target="' . $target . '" class="prosperent-kw">' . $match . '</a>';
							}							
							else
							{
								$matches[$p] = '<a href="' . $productSearchUrl . $query . '" target="' . $target . '" class="prosperent-kw">' . $match . '</a>';
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
					if (!isset($options['PSAct']) || isset($options['LTM'][$i]) == 1)
					{				
						$text = preg_replace('/\b' . $oldText . '\b/' . $case, '<a href="' . $allData['data'][0]['affiliate_url'] . '" target="' . $target . '" class="prosperent-kw shopCheck">$0</a>', $text, $limit);
					}
					else
					{
						$text = preg_replace('/\b' . $oldText . '\b/' . $case, '<a href="' . $productSearchUrl . $query . '" target="' . $target . '" class="prosperent-kw">$0</a>', $text, $limit);
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

}