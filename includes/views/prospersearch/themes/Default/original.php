<script type="text/javascript">function toggle_visibility(e){var l=document.getElementById(e);l&&"none"==l.style.display&&(l.style.display="block")}function toggle_hidden(e){var l=document.getElementById(e);l&&"block"==l.style.display&&(l.style.display="none")}</script>
<div class="clear"></div>
<div id="prosperShopMain">
	<?php if (!$options['noSearchBar']): ?>
		<div class="prosper_searchform" style="padding:0 15px 0 0">
			<form id="prosperSearchForm" class="searchform" method="POST" action="" rel="nolink">
				<input id="s" class="<?php echo ($type == 'celebrity' ? 'prosper_celeb_field prosper_field' : 'prosper_field'); ?>" value="<?php echo ($query ? $query : ''); ?>" type="text" name="<?php echo $searchPost ? $searchPost : 'q'; ?>" placeholder="<?php echo isset($options['Search_Bar_Text']) ? $options['Search_Bar_Text'] : ($searchTitle ? 'Search ' . $searchTitle : 'Search Products'); ?>">
				<?php if ($typeSelector): ?>
					<select style="display:inline;overflow:hidden;margin:0" name="type">
						<?php 				
						foreach ($typeSelector as $i => $ends)
						{
							?>
							<option <?php echo ($params['type'] == $i ? 'selected="selected"' : ''); ?> value="<?php echo $i; ?>">In: <strong><?php echo $ends; ?></strong></option>
							<?php 
						}
						?>
					</select>
				<?php endif; ?>
				<input class="prosper_submit" type="submit" name="submit" value="Search">
			</form>
		</div>
		<div class="clear"></div>
	<?php 
	endif;
	if ($filterArray)
	{
		?>
		<div id="filterSidebar" style="width:19%;overflow:hidden;padding-left:2px;float:left;margin-top:6px; border:1px solid #ddd; background:#f2f0ee;">
			<div>	
				<?php 
				if ($celebrityInfo)
				{
					echo '<div style="width:100%; margin:0; padding:4px 2px;border-bottom:2px solid #ddd;">';
					echo $celebrityInfo['celebrity'] ? '<strong style="text-align:center;">' . $celebrityInfo['celebrity'] . '</strong>' : '';
					echo '<div><img style="width:160px;" src="' . ($options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $celebrityInfo['image_url'])) : $celebrityInfo['image_url']) . '" alt="' . $celebrityInfo['celebrity'] . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
					echo $celebrityInfo['age'] ? '<strong>Age:</strong> ' . $celebrityInfo['age'] : '';
					echo $celebrityInfo['height'] ? '<br><strong>Height:</strong> ' . $celebrityInfo['height'] : '';
					echo $celebrityInfo['dateBirth'] ? '<br><strong>DOB:</strong> ' . date('M j, Y', strtotime($celebrityInfo['dateBirth'])) : '<br><br>';	
					echo '</div>';
				}			
				
				if (!empty($pickedFacets)): ?>
				<div style="width:100%; margin:0; padding:4px 2px;border-bottom:2px solid #ddd;">
					<div class="activeFilters">
						<span><?php echo implode('</span></div><div class="activeFilters"><span>', $pickedFacets);?></span>
					</div>
				</div>
				<?php endif; ?>
			
				<?php			
				$mainCount = count($mainFilters);

				foreach ($mainFilters as $i => $partials)
				{				
					if ($params['zip'])
					{
						$params['zipCode'] = $params['zip'];
					}
					
					$name = $i;
					if ($i === 'city')
						$name = 'citie';
					if ($i === 'category')
						$name = 'categorie';	
					if ($i === 'zipCode')
						$name = 'Zip Code';
					?>
					<div style="display:block;">				
					
						<div id="more<?php echo ucfirst($i); ?>s" onclick="toggle_visibility('<?php echo $i; ?>List'); toggle_hidden('more<?php echo ucfirst($i); ?>s'); toggle_visibility('hide<?php echo ucfirst($i); ?>s'); return false;" style="display:none;cursor:pointer; font-size:14px; font-weight:bold;"><?php echo ucfirst($name); ?>s <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_down_small.png'; ?>" alt=""/></div>
						<div id="hide<?php echo ucfirst($i); ?>s" onclick="toggle_hidden('<?php echo $i; ?>List'); toggle_hidden('hide<?php echo ucfirst($i); ?>s'); toggle_visibility('more<?php echo ucfirst($i); ?>s'); return false;" style="display:block;cursor:pointer; font-size:14px;font-weight:bold;""><?php echo ucfirst($name); ?>s <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_up_small.png'; ?>" alt=""/></div>

						<?php		

						if ((empty($mainFilters[$i]) && !$params[$i]) && $i != 'celebrity')
						{
							echo '<div class="no' .  $i . '">No ' .  $i . 's Found</div>';
						}
						else
						{					
							echo '<span id="' . $i . 'List" style="padding: 0 0 8px 12px;display:block;">' . implode('</br>', $partials);
							if ($secondaryFilters[$i])
							{
								?>
								<div id="moreExtra<?php echo ucfirst($i); ?>s" onclick="toggle_visibility('<?php echo $i; ?>ExtraList'); toggle_hidden('moreExtra<?php echo ucfirst($i); ?>s'); toggle_visibility('hideExtra<?php echo ucfirst($i); ?>s'); return false;" style="display:block;cursor:pointer; font-size:13px;font-weight:bold;"">More <?php echo ucfirst($name); ?>s <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_down_small.png'; ?>" alt=""/></div>
								<div id="hideExtra<?php echo ucfirst($i); ?>s" onclick="toggle_hidden('<?php echo $i; ?>ExtraList'); toggle_hidden('hideExtra<?php echo ucfirst($i); ?>s'); toggle_visibility('moreExtra<?php echo ucfirst($i); ?>s'); return false;" style="display:none;cursor:pointer; font-size:13px;font-weight:bold;"">More <?php echo ucfirst($name); ?>s <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_up_small.png'; ?>" alt=""/></div>
								<?php
								echo '<span id="' . $i . 'ExtraList" style="display:none;">' . implode('</br>', $secondaryFilters[$i]) . '</span>';
							}				
							
							echo '</span>';
						}				
						?>
					</div>
					<?php
					$z++;
				}		
				?>
				<div class="clear"></div>
				<?php if ($options['Enable_Sliders']): ?>
					<div id="morePriceRange" onclick="toggle_visibility('priceRangeSlider');  toggle_hidden('morePriceRange'); toggle_visibility('hidePriceRange'); return false;" style="display:none;"><?php echo $dollarSlider; ?> <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_down_small.png'; ?>" alt=""/></div>
					<div id="hidePriceRange" onclick="toggle_hidden('priceRangeSlider'); toggle_hidden('hidePriceRange'); toggle_visibility('morePriceRange'); return false;" style="display:block;"><?php echo $dollarSlider; ?> <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_up_small.png'; ?>" alt=""/></div>		
					<div id="priceRangeSlider" style="display:block;">
						</br>
						<div id="sliderRange"></div>
						<form name="priceRange" method="POST" action="">
							<input type="text" class="min" id="rangeMin" name="priceSliderMin" value="<?php echo ($priceSlider[0] ? '$' . $priceSlider[0] : '$0'); ?>">
							<input type="text" class="max" id="rangeMax" name="priceSliderMax" value="<?php echo ($priceSlider[1] ? '$' . $priceSlider[1] : '$500'); ?>"> 
							<input type="submit" value="Submit" style="display: none;" >
						</form>
					</div>
					<div class="clear"></div>
					<div id="morePercentRange" onclick="toggle_visibility('percentRangeSlider'); toggle_hidden('morePercentRange'); toggle_visibility('hidePercentRange'); return false;" style="display:none;">Percent Off <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_down_small.png'; ?>" alt=""/></div>
					<div id="hidePercentRange" onclick="toggle_hidden('percentRangeSlider'); toggle_hidden('hidePercentRange'); toggle_visibility('morePercentRange'); return false;" style="display:block;">Percent Off <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_up_small.png'; ?>" alt=""/></div>
					<div id="percentRangeSlider" style="display:block;">
						</br>
						<div id="sliderPercent"></div>
						<form name="percentOffRange" method="POST" action="">
							<input type="text" class="min" id="percentMin" name="percentSliderMin" value="<?php echo ($percentSlider[0] ? $percentSlider[0] . '%' : '0%'); ?>"/>
							<input type="text" class="max" id="percentMax" name="percentSliderMax" value="<?php echo ($percentSlider[1] ? $percentSlider[1] . '%' : '100%'); ?>"/>
							<?php if ($type == 'product') : ?>
							<div class="clear"></div>
							<input type="checkbox" style="display:inline-block;" name="onSale" <?php echo ($params['pR'] ? 'checked' : ''); ?> onChange="percentOffRange.submit();">
							<label style="display:inline-block;" for="onSale">On Sale Only</label>
							<?php endif; ?>
							<input type="submit" value="Submit" style="display: none;" >
						</form>
					</div>
				<?php endif; ?>
			</div>	
		</div>

		<?php
	}

	if ($noResults)
	{
		echo '<div class="noResults">No Results</div>';

		if (($params['brand'] || $params['merchant']) && $query)
		{
			echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace(array('/merchant/' . $params['merchant'], '/brand/' . $params['brand']), '', $url) . '>clear the filter(s)</a>.</div>';
		}
		else
		{
			echo '<div class="noResults-secondary">' . ($type == 'celebrity' ? 'Please search for a celebrity' : 'Please try your search again.') . '</div>';
		}
		echo '<div class="noResults-padding"></div>';
	}
	?>

	<div class="totalFound"><?php echo (($totalFound > 0 && isset($title)) ? (number_format($totalFound) . ' results for ' . preg_replace('/\(.+\)/i', '', $title)) : ($newTrendsTitle ? $newTrendsTitle : 'Browse these <strong>' . $trend . '</strong>')) . ($demolishUrl && !$trend ? '<strong><a class="xDemolish" href=' . $demolishUrl . '> &#215;</a></strong>' : ''); ?></div>

	<div class="prosper_priceSorter">
		<form class="sorterofprice" name="priceSorter" method="POST" action="" >
			<label for="PriceSort">Sort By: </label>
			<select name="sort" onChange="priceSorter.submit();">
				<?php 						
				foreach ($sortArray as $i => $sort)
				{
					?>
					<option <?php echo (rawurldecode($params['sort']) == $sort ? 'selected="selected"' : ''); ?> value="<?php echo $sort; ?>"><?php echo $i; ?></option>
					<?php 
				}
				?>
			</select>
			<?php echo (($params['sort'] != 'rel' && '' != $params['sort']) ? '<strong><a class="xDemolish" href=' . str_replace(array('/page/' . $params['page'], '/sort/' . $params['sort']), '', $url) . '> &#215;</a></strong>' : ''); ?>
		</form>
	</div>

	<div id="views">
		<a href="<?php echo str_replace(array('/view/' . $params['view']), '', $url) . '/view/list'; ?>"><span class="listIcon"></span></a>
		<a href="<?php echo str_replace(array('/view/' . $params['view']), '', $url) . '/view/grid'; ?>"><span class="gridIcon"></span></a>
	</div>

	<?php
	//$pagedResults = $this->searchModel->prosperPages($results, $params['page']);

	if (!$params['view'] || $params['view'] === 'list') 
	{ 
		?>
		<div id="productList" style="<?php echo ($filterArray ? 'width:80%' : 'width:100%'); ?>;float:right;">
			<?php
			if (!empty($results))
			{
				// Loop to return Products and corresponding information
				foreach ($results as $i => $record)
				{			
					if (is_ssl())
					{
						$record['image_url'] = str_replace('http', 'https', $record['image_url']);
					}
				
					$record['affiliate_url'] = $options['URL_Masking'] ? $homeUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) : $record['affiliate_url'];
					$cid = $type === 'coupon' ? $record['couponId'] : ($type === 'local' ? $record['localId'] : $record['catalogId']);				
					?>
					<div class="productBlock">
						<div class="productImage">
							<a href=<?php echo ($options['imageMercLink'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?>  rel="nolink"><span <?php echo ($type === 'coupon' ? 'class="loadCoup"' : 'class="load"'); ?>><img src="<?php echo $options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('https://img1.prosperent.com/images/', 'http://img1.prosperent.com/images/', '/'), array('', '', ',SL,'), $record['image_url'])) : $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
						</div>
						<div class="productContent">
							<?php
							if ($record['promo'])
							{					
								echo '<div class="promo"><span>' . $record['promo'] . '</span></div>' . (($record['expiration_date'] || $record['expirationDate']) ? '&nbsp;&nbsp;&mdash;&nbsp;&nbsp;' : '');
							}
							
							if($record['expiration_date'] || $record['expirationDate'])
							{
								$expirationDate = $record['expirationDate'] ? $record['expirationDate'] : $$record['expiration_date'];
								$expires = strtotime($expirationDate);
								$today = strtotime(date("Y-m-d"));
								$interval = ($expires - $today) / (60*60*24);

								if ($interval <= 20 && $interval > 0)
								{
									echo '<div class="couponExpire"><span>' . $interval . ' Day' . ($interval > 1 ? 's' : '') . ' Left!</span></div>';
								}
								elseif ($interval <= 0)
								{
									echo '<div class="couponExpire"><span>Ends Today!</span></div>';
								}
								else
								{
									echo '<div class="couponExpire"><span>Expires Soon!</span></div>';
								}
							}	
							?>
							<div class="productTitle"><a href=<?php echo ($options['titleMercLink'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink"><span><?php echo preg_replace('/\(.+\)/i', '', $record['keyword']); ?></span></a></div>
							<?php
							if ($type != 'coupon' && $type != 'local')
							{ 
								?>
								<div class="productDescription">
									<?php
									if (strlen($record['description']) > 200)
									{
										echo substr($record['description'], 0, 200) . '...';
									}
									else
									{
										echo $record['description'];
									}
									?>
								</div>
								<?php
							}	
							
							if ($record['coupon_code'])
							{
								echo '<div class="couponCode">Coupon Code: <span class="code_cc">' . $record['coupon_code'] . '</span></div>';
							}	
				
							?>				
							<div class="productBrandMerchant">
								<?php
								if($record['brand'])
								{
									echo '<span class="brandIn"><u>Brand</u>: ' . (!$params['brand'] ? '<a href="' . str_replace('/page/' . $params['page'], '', $url) . '/brand/' . rawurlencode($record['brand']) . '" rel="nolink"><cite>' . $record['brand'] . '</cite></a>' : $record['brand']) . '</span>';
								}
								if ($record['state'] || $record['city'] || $record['zipCode'] && !$params['zipCode'])
								{
									$city  = ((!$params['city'] || $noResults) ? '<a href="' . str_replace(array('/page/' . $params['page'], '/city/' . $filterCity), array('', ''), $url) . '/city/' . rawurlencode($record['city']) . '" rel="nolink"><cite>' . ucwords($record['city']) . '</cite></a>' : $record['city']) . ($record['state'] ? ', ' : '');
									$state = (!$params['state'] || $noResults) ? '<a href="' . str_replace(array('/page/' . $params['page'], '/state/' . $filterState), array('', ''), $url) . '/state/' . rawurlencode($record['state']) . '" rel="nolink"><cite>' . ($record['city'] ? strtoupper($record['state']) : ucwords($backStates[$record['state']])) . '</cite></a> ' : ($record['city'] ? strtoupper($record['state']) : ucwords($record['state'])) . '&nbsp;';
									$zip   = (!$params['zip'] || $noResults) ? ' <a href="' . str_replace(array('/page/' . $params['page'], '/zip/' . $filterZip), array('', ''), $url) . '/zip/' . rawurlencode($record['zipCode']) . '" rel="nolink"><cite>' . $record['zipCode'] . '</cite></a>' : $record['zipCode'];
									echo '<span class="brandIn" style="display:inline-block;"><u>Location</u>: ' . $city . $state . $zip . '</span>';
								}
								if($record['merchant'])
								{
									echo '<span class="merchantIn"><u>Merchant</u>: ' . (!$params['merchant'] ? '<a href="' . str_replace('/page/' . $params['page'], '', $url) . '/merchant/' . rawurlencode($record['merchant']) . '" rel="nolink"><cite>' . $record['merchant'] . '</cite></a>' : $record['merchant']) . '</span>';
								}				
								?>
							</div>
						</div>
						<div class="productEnd">
							<?php
							if ($record['price_sale'] || $record['price'] || $record['priceSale'])
							{
								$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
								
								if(empty($priceSale) || $record['price'] <= $priceSale)
								{
									//we don't do anything
									?>
									<div class="productPriceNoSale"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . number_format($record['price'], 2); ?></span></div>
									<?php
								}
								//otherwise strike-through Price and list the Price_Sale
								else
								{
									?>
									<div class="productPrice"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . number_format($record['price'], 2)?></span></div>
									<div class="productPriceSale"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . number_format($priceSale, 2)?></span></div>
									<?php
								}
							}
							?>
							<div class="prosperVisit">					
								<form class="shopCheck" action="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" method="POST" rel="nofollow,nolink">
									<input type="submit" value="Visit Store"/>
								</form>
							</div>	
						</div>
					</div>
					<?php
				}
			}
	} 
	elseif ($params['view'] === 'grid')
	{
		$gridImage = ($options['Grid_Img_Size'] ? preg_replace('/px|em|%/i', '', $options['Grid_Img_Size']) : 200) . 'px';
		if ($trend)
		{
			$gridImage = ($options['Same_Img_Size'] ? preg_replace('/px|em|%/i', '', $options['Same_Img_Size']) : 200) . 'px';
		}
		$classLoad = ($type === 'coupon' || $gridImage < 120) ? 'class="loadCoup"' : 'class="load"';
		echo '<div id="simProd" style="' . ($filterArray ? 'width:80%' : 'width:100%') . ';float:right;border-top: 2px solid #ddd;">';
		echo '<ul>';	
		if (!empty($results))
		{
			foreach ($results as $record)
			{
				if (is_ssl())
				{
					$record['image_url'] = str_replace('http', 'https', $record['image_url']);
				}
				
				$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
				$price 	   = $priceSale ? '<div class="prodPriceSale">' . ($currency == 'GBP' ? '&pound;' : '$') . number_format($priceSale, 2) . '</div>' : '<div class="prodPrice">' . ($currency == 'GBP' ? '&pound;' : '$') . number_format($record['price'], 2) . '</div>';
				$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
				$cid 	   = $type === 'coupon' ? $record['couponId'] : ($type === 'local' ? $record['localId'] : $record['catalogId']);
				?>
					<li <?php echo 'style="width:' . $gridImage . '!important;"'; ?>>
						<div class="listBlock">
							<div class="prodImage">
								<a href=<?php echo ($options['imageMercLink'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink"><span <?php echo $classLoad . ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px;margin:0 15px"'); ?>><img <?php echo ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px;"'); ?> src="<?php echo $options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('https://img1.prosperent.com/images/', 'http://img1.prosperent.com/images/', '/'), array('', '', ',SL,'), $record['image_url'])) : $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
							</div>
								<?php
								if ($record['promo'])
								{					
									echo '<div class="promo"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink">' . $record['promo'] . '!</a></span></div>';
								}
								elseif($record['expiration_date'] || $record['expirationDate'])
								{
									$expirationDate = $record['expirationDate'] ? $record['expirationDate'] : $record['expiration_date'];			
									$expires = strtotime($expirationDate);
									$today = strtotime(date("Y-m-d"));
									$interval = ($expires - $today) / (60*60*24);

									if ($interval <= 20 && $interval > 0)
									{
										echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink">' . $interval . ' Day' . ($interval > 1 ? 's' : '') . ' Left!</a></span></div>';
									}
									elseif ($interval <= 0)
									{
										echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink">Ends Today!</a></span></div>';
									}
									else
									{
										echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid . '" rel="nolink">Expires Soon!</a></span></div>';
									}
								}
								elseif ($type == 'coupon' || $type == 'local')
								{
									echo '<div class="promo">&nbsp;</div>';
								}
								?>
							<div class="prodContent">
								<div class="prodTitle">
									<a href=<?php echo ($options['titleMercLink'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink">
										<?php echo $keyword; ?>
									</a>
								</div>     
								<?php if ($price && $type != 'coupon' && $type != 'local'){ echo $price; } ?>												
							</div>
							
							<div class="prosperVisit">					
								<form class="shopCheck" action="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" method="POST" rel="nofollow,nolink">
									<input type="submit" value="Visit Store"/>
								</form>
							</div>	
						</div>			
					</li>
				<?php
			}
		}
		echo '</ul>';
	}

	$this->searchModel->prosperPagination($totalAvailable, $params['page']);
	?>
	</div>
</div>
<div class="clear"></div>