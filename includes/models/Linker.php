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

		if (!$options['PLAct'])
		{
		    return $content;
		}

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
		if (!$brands && !$merchants && !$query)
		{
			$query = $content;
		}


		if ($pieces['ft'] == 'fetchProducts')
		{
			$expiration = PROSPER_CACHE_PRODS;
			$type = '';
			$page = $curlType = 'product';

			$fetch = 'fetchProducts';

			$settings = array(
				'curlCall'		  => 'single-' . $curlType,
				'interface'		  => 'linker',
				'enableFullData'  => 'FALSE',
				'limit'           => 1,
				'query'           => (!$pieces['id'] ? $query : ''),
				'filterMerchant'  => (!$pieces['id'] ? $merchants : ''),
				'filterBrand'	  => (!$pieces['id'] ? $brands : ''),
				'filterProductId' => $pieces['id'] ? str_replace(',', '|', $pieces['id']) : '',
				'filterPriceSale' => !$pieces['id'] && $pieces['sale'] ? ($pieces['pr'] ? $pieces['pr'] : '0.01,') : '',
				'filterPrice' 	  => ($pieces['id'] || $pieces['sale'] ? '' : ($pieces['pr'] ? $pieces['pr'] : '')),

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
			    'filterCategory'   => !$pieces['id'] && $pieces['cat'] ? '*' . $pieces['cat'] . '*' : ''
			);
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
				if (!$sid)
				{
				    $sid = $this->getSid($settings);
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
		elseif ($pieces['gtm'] == 'merchant' || !$options['PSAct'])
		{
			$affUrl = $allData['data'][0]['affiliate_url']. '&interface=wp&subinterface=phraselinker';
			$rel = 'nofollow,nolink';
			$checkClass =  'shopCheck';
		}
		else
		{
			$affUrl = $homeUrl . $page . '/' . str_replace('/', ',SL,', $allData['data'][0]['keyword']) . '/cid/' . $allData['data'][0]['catalogId'];
			$rel = 'nolink';
		}

		return '<a class="shopCheck" style="text-decoration:none;" href="' . $affUrl . '" TARGET=' . $target . '" class="prosperent-kw" class="' . $checkClass . '" rel="' . $rel . '">' . $content . '</a>';


		/*$fB = '';
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
		    $merchants = explode('|', $merchants);
			foreach ($merchants as $merchant)
			{
				if (!preg_match('/^!/', $merchant))
				{
					$fM = '/merchant/' . $merchant;
					break;
				}
			}
		}

		$query = ($pieces['q'] && $pieces['ft'] == 'fetchProducts') ? '/query/' . rawurlencode($pieces['q']) : ($pieces['q'] && $pieces['ft'] == 'fetchMerchant') ? '/merchant/' . rawurlencode($pieces['q'])  : '';

		if ($fB || $fM || $query)
		{
			return '<a style="text-decoration:none;" href="' . $storeUrl . $query . $fB . $fM . $type . '" TARGET="' . $target . '" class="prosperent-kw">' . $content . '</a>';
		}
		else
		{
			return $content;
		}*/
	}

	/**
	 * Perform auto-linker
	 *
	 * @param string $text
	 * @return string
	 */
	public function autoLinker($text)
	{
	    $options = $this->_options;

	    if (!isset($options['PSAct']))
	    {
	        return $text;
	    }

		$text = ' ' . $text . ' ';
		if (!empty($options['Match'][0]))
		{
		    $random 		  = FALSE;
		    $basePage         = ($this->_options['Base_URL'] ? $this->_options['Base_URL'] : 'products');
		    $base   		  = $basePage . '/query/';
		    $target 		  = '_self';
		    $productSearchUrl = home_url('/') . $base;
            $fetch            = 'fetchProducts';

		    $page = get_page_by_path($basePage);

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
				$limit = $options['PerPage'][$i] ? $options['PerPage'][$i] : 3;
				$case  = isset($options['Case'][$i]) ? '' : 'i';

				if (!preg_match('/' . $oldText . '/' . $case, $text))
				{
					continue;
				}

				$query = rawurlencode(trim($newText));
				//$qText = 'q="' . $oldText . '"';
				preg_match('/q=\".+?\"/', $text, $qText);

				$text = str_ireplace($qText[0], $base = base64_encode($qText[0]), $text);

				/*
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
					if (!isset($options['PSAct']) || isset($options['LTM'][$i]) == 1 || $page->post_status != 'publish')
					{
					    $settings = array(
					        'enableFullData'  => 'FALSE',
					        'limit'           => 1,
					        'query'           => $newText,
					        'groupBy'		  => 'productId',
					        'interface'		  => 'linker',
					        'curlCall'		  => 'single-product'
					    );

					    $url = $this->apiCall($settings, $fetch);
					    $allData = $this->singleCurlCall($url, PROSPER_CACHE_PRODS, $settings);

					    if ($allData['data'])
					    {
					        $text = preg_replace('/\b\s(' . $oldText . ')\s\b/' . $case, ' <a href="' . $allData['data'][0]['affiliate_url'] . '" target="' . $target . '" class="prosperent-kw shopCheck">$1</a> ', $text, $limit);
					    }
					}
					else
					{*/

						$text = preg_replace('/\b(' . $oldText . ')\b/' . $case, '<a href="' . $productSearchUrl . $query . '" target="_self" class="prosperent-kw">$1</a>', $text, $limit);
					//}
				//}

				$text = str_ireplace($base, $qText[0], $text);

				$i++;
			}

			// Remove links within links
			$text = preg_replace( "#(<a [^>]+>)(.*)<a [^>]+>([^<]*)</a>([^>]*)</a>#iU", "$1$2$3$4</a>" , $text );
		}

		return trim($text);
	}

}