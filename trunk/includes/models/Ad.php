<?php
require_once(PROSPER_MODEL . '/Base.php');
/**
 * Ad Model
 *
 * @package Model
 */
class Model_Ad extends Model_Base
{
	protected $_shortcode = 'perform_ad';
	
	protected $_options;

	public function qTagsAd()
	{	
		$id 	 = 'performanceAd';
		$display = 'ProsperAd';
		$arg1 	 = '[perform_ad q="TOPIC" h="90" w="auto" utt="USE TAGS?" utt="USE TITLE?"]';
		$arg2 	 = '[/perform_ad]';		
	
		$this->qTagsProsper($id, $display, $arg1, $arg2);
	}
	
   /**
	* Performs shortcode extraction for ProsperAds
	*
	* @param array  $atts    Attributes from the shortcode
	* @param string $content Content on the page/post
	*/	
	public function performAdShortCode($atts, $content = null)
	{
		$this->_options = $this->getOptions();
		extract($this->shortCodeExtract($atts, $this->_shortcode));
		
		$fallback = array();
		if ($utg)
		{
			$fallback = $this->getTags();
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

		if ($this->_options['Remove_Tags'])
		{
			$fallback = $this->removeTags($fallback);		
		}

		$fallback = implode(",", $fallback);

		$height = $h ? ($h == 'auto' ? '100%' : preg_replace('/px|em|%/i', '', $h) . 'px') : 90 . 'px';
		$width = $w ? ($w == 'auto' ? '100%' : preg_replace('/px|em|%/i', '', $w) . 'px') : '100%';

		$sidArray = array();
		if ($this->_options['prosperSid'])
		{
			foreach ($this->_options['prosperSid'] as $sidPiece)
			{
				switch ($sidPiece)
				{
					case 'blogname':
						$sidArray[] = get_bloginfo('name');
						break;
					case 'interface':
						$sidArray[] = $settings['interface'] ? $settings['interface'] : 'api';
						break;
					case 'query':
						$sidArray[] = $settings['query'];
						break;
					case 'page':
						$sidArray[] = get_the_title();
						break;						
				}
			}
		}
		if ($this->_options['prosperSidText'])
		{
			if (preg_match('/(^\$_(SERVER|SESSION|COOKIE))\[(\'|")(.+?)(\'|")\]/', $this->_options['prosperSidText'], $regs))
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
			elseif (!preg_match('/\$/', $this->_options['prosperSidText']))
			{
				$sidArray[] = $this->_options['prosperSidText'];
			}
		}
		
		if (!empty($sidArray))
		{
			$sidArray = array_filter($sidArray);
			$sid = implode('_', $sidArray);
		}	
		
		return '<div style="clear:both;"></div><p><div class="prosperent-pa" style="position:relative;height:' . $height . '; width:' . $width . ';" ' . ($sid ? 'pa_sid="' . $sid . '"' : '') . ($fallback ? 'pa_topics="' . $fallback . '"' : '') . '>' . (wp_script_is('loginCheck') ? '<div class="shopCheck" style="cursor:pointer;position:absolute;top:0;left:0;height:' . $height . '; width:' . $width . ';z-index:10;"></div>' : '') . '</div></p><div style="clear:both;"></div>';
	}
	
	public function getTags()
	{
		$posttags = get_the_tags();
		if ($posttags) 
		{
			foreach($posttags as $tag) 
			{
				$fallback[] = strtolower($tag->name); 
			}
		}	
		
		return $fallback;
	}
	
	public function removeTags($fallback)
	{
		$removeTags = explode(',', $this->_options['Remove_Tags']);			
		$fbacks = array_flip($fallback);

		foreach ($removeTags as $remove)
		{ 
			$remove = trim($remove);
			if(isset($fbacks[$remove]))
			{
				unset($fbacks[$remove]);
			}
		}	
		
		return array_flip($fbacks);			
	}
}