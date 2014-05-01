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
		$maskedUrl = home_url('/') . 'store/go/';
		$storeUrl  = $homeUrl . $base;	
			
		$pieces = $this->shortCodeExtract($atts, $this->_shortcode);

		// Remove links within links
		$content = $content ? (preg_match('/<img/i', $content) ? $content : strip_tags($content)) : $query;

		if ($pieces['gtm'] === 'merchant' || !$options['Enable_PPS'] || $pieces['gtm'] === 'true' || $pieces['gtm'] === 'prodPage')
		{
			if ($pieces['ft'] == 'fetchProducts')
			{		
				$type = '';
				$page = 'product';
				
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
					'limit'           => 1,
					'query'           => trim(strip_tags($pieces['q'] ? $pieces['q'] : $content)),
					'filterMerchant'  => $pieces['m'] ? array_map('trim', explode(',',  $pieces['m'])) : '',
					'filterBrand'	  => $pieces['b'] ? array_map('trim', explode(',',  $pieces['b'])) : '',
					'filterProductId' => $pieces['id'] ? array_map('trim', explode(',',  rtrim($pieces['id'], ","))) : '',		
				);
			}
			else
			{
				$fetch = $pieces['ft'];
				
				if ($fetch === 'fetchCoupons')
				{
					$type = '/type/coup/';
					$page = 'coupon';
				
					$settings = array(
						'limit'           => 1,
						'query'           => trim(strip_tags($pieces['q'] ? $pieces['q'] : $content)),
						'filterMerchant'  => $pieces['m'] ? array_map('trim', explode(',',  $pieces['m'])) : '',
						'filterCouponId' => $pieces['id'] ? array_map('trim', explode(',',  rtrim($pieces['id'], ","))) : '',		
					);				
				}
				elseif ($fetch === 'fetchLocal')
				{
					$type = '/type/local/';
					$page = 'local';
				
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
						'limit'           => 1,
						'filterState'	  => $state ? $state : '',
						'filterCity'	  => $pieces['city'] ? $pieces['city'] : '',
						'filterZipCode'	  => $pieces['z'] ? $pieces['z'] : '',
						'query'           => trim(strip_tags($pieces['q'] ? $pieces['q'] : $content)),
						'filterMerchant'  => $pieces['m'] ? array_map('trim', explode(',',  $pieces['m'])) : '',
						'filterLocalId'   => $pieces['id'] ? array_map('trim', explode(',',  rtrim($pieces['id'], ","))) : '',		
					);
				}
			}

			$settings = array_filter($settings);			
			$settings = array_merge(array('enableFullData' => 0), $settings);

			if (count($settings) < 3)
			{
				return $content;
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
						return $content;
					}
				
					$allData = $this->apiCall($settings, $fetch);
					
					if ($allData['results'])
					{
						break;
					}	 
				}
			}			

			if ($pieces['gtm'] === 'merchant' || !$options['Enable_PPS'] || $pieces['gtm'] === 'true')
			{
				$affUrl = $options['URL_Masking'] ? $maskedUrl . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $allData['results'][0]['affiliate_url'])) : $allData['results'][0]['affiliate_url'];
				$rel = 'nofollow,nolink';
			}
			else if ($pieces['gtm'] === 'prodPage')
			{				
				$affUrl = $homeUrl . $page . '/' . rawurlencode(str_replace('/', ',SL,', $allData['results'][0]['keyword'])) . '/cid/' . $allData['results'][0]['catalogId'];
				$rel = 'nolink';
			}
			
			return '<a href="' . $affUrl . '" TARGET=' . $target . '" class="prosperent-kw" rel="' . $rel . '">' . $content . '</a>';
		}

		$brands = array();
		if ($pieces['b'])
		{
			foreach ($pieces['b'] as $brand)
			{
				if (!preg_match('/^!/', $brand))
				{
					$brands[] = $brand;
				}
			}
		}
		
		$merchants = array();
		if ($pieces['m'])
		{
			foreach ($pieces['m'] as $merchant)
			{
				if (!preg_match('/^!/', $merchant))
				{
					$merchants[] = $merchant;
				}
			}
		}

		$fB    = empty($brands) ? '' : '/brand/' . rawurlencode($brands[0]);
		$fM    = empty($merchants) ? '' : '/merchant/' . rawurlencode($merchants[0]);
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
		//$prosperAffUrl    = 'http://prosperent.com/store/product/' . $options['UID'] . '-427-0/?k=';
		//$storeGoUrl       = home_url() . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $prosperAffUrl)) . ',SL,';
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
					'enableFullData'  => 0,
					'limit'           => 1,
					'query'           => $newText,
					'groupBy'		  => 'productId'		
				);

				$settings = array_filter($settings);
				
				$allData = $this->apiCall($settings, $fetch);

				$affUrl = $options['URL_Masking'] ? $homeUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $allData['results'][0]['affiliate_url'])) : $allData['results'][0]['affiliate_url'];
				
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
								if (!$options['Enable_PPS'] || $options['LTM'][$i] == 1)
								{
									$matches[$key] = '<a href="' . $affUrl . '" target="' . $target . '" class="prosperent-kw">' . $matches[$key] . '</a>';
								}							
								else
								{
									$matches[$key] = '<a href="' . $productSearchUrl . $query . '" target="' . $target . '" class="prosperent-kw">' . $matches[$key] . '</a>';								
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
								$matches[$rand_keys] = '<a href="' . $productSearchUrl . $query . '" target="' . $target . '" class="prosperent-kw">' . $matches[$rand_keys] . '</a>';								
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
					if (!isset($options['Enable_PPS']) || isset($options['LTM'][$i]) == 1)
					{				
						$text = preg_replace('/\b' . $oldText . '\b/' . $case, '<a href="' . $affUrl . '" target="' . $target . '" class="prosperent-kw">$0</a>', $text, $limit);
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