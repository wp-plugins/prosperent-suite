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
		$display = 'Auto Linker';
		$arg1 	 = '[linker q="QUERY" gtm="true" b="BRAND" m="MERCHANT" ct="US"]';
		$arg2 	 = '[/linker]';		
	
		$this->qTagsProsper($id, $display, $arg1, $arg2);
	}
	
	public function linkerShortcode($atts, $content = null)
	{
		$target  		    = $this->_options['Target'] ? '_blank' : '_self';
		$base_url   		= $this->_options['Base_URL'] ? $this->_options['Base_URL'] . '/query/' : 'products/query/';
		$product_search_url = home_url('/') . $base_url;	
		
		extract($this->shortCodeExtract($atts, $this->_shortcode));

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

		if ($gtm || !$this->_options['Enable_PPS'])
		{
			$settings = array(
				'api_key'         => $this->_options['Api_Key'],
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
					'api_key'        => $this->_options['Api_Key'],
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
						'api_key'        => $this->_options['Api_Key'],
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
	
	/**
	 * Perform auto-linker
	 *
	 * @param string $text
	 * @return string
	 */
	public function autoLinker($text)
	{		
		$random 			= FALSE;
		$base_url   		= $this->_options['Base_URL'] ? $this->_options['Base_URL'] . '/query/' : 'products/query/';
		$target 			= $this->_options['Target'] ? '_blank' : '_self';
		$prosper_aff_url    = 'http://prosperent.com/store/product/' . $this->_options['UID'] . '-427-0/?k=';
		$store_go_url       = home_url() . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $prosper_aff_url)) . ',SL,';
		$product_search_url = home_url('/') . $base_url;	
					
		$text = ' ' . $text . ' ';
		if (!empty($this->_options['Match'][0]))
		{
			foreach ($this->_options['Match'] as $i => $match)
			{			
				if (!empty($match))
				{
					$val[$match] =  $this->_options['Query'][$i] ? $this->_options['Query'][$i] : $match;
				}
			}
			
			$i = 0;				
			foreach ($val as $old_text => $new_text)
			{ 				
				$limit = $this->_options['PerPage'][$i] ? $this->_options['PerPage'][$i] : 5;
				$case  = isset($this->_options['Case'][$i]) ? '' : 'i';
				$query = rawurlencode(trim($new_text));	
				//$qText = 'q="' . $old_text . '"';
				preg_match('/q=\".+?\"/', $text, $qText);
				
				/*
				*  Prosperent API Query
				*/
				$settings = array(
					'api_key'         => $this->_options['Api_Key'],
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
								if (!$this->_options['Enable_PPS'] || $this->_options['LTM'][$i] == 1)
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
							if (!$this->_options['Enable_PPS'] || $this->_options['LTM'][$i] == 1)
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
							if (!$this->_options['Enable_PPS'] || $this->_options['LTM'][$i] == 1)
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
					if (!isset($this->_options['Enable_PPS']) || isset($this->_options['LTM'][$i]) == 1)
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

}