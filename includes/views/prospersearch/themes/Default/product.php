<script type="text/javascript">
function toggle_visible(e) {
    var l = document.getElementById(e);
    l && "none" == l.style.display && (l.style.display = "block")
}

function toggle_hidden(e) {
    var l = document.getElementById(e);
    l && "block" == l.style.display && (l.style.display = "none")
}</script>
<div class="clear"></div>
<div id="prosperShopMain">
	<?php if (!$options['noSearchBar']): ?>
		<div class="prosper_searchform" style="padding:0 15px 0 0">
			<form id="prosperSearchForm" class="searchform" method="POST" action="" rel="nolink">
				<input id="s" class="prosper_field" value="<?php echo ($query ? $query : ''); ?>" type="text" name="<?php echo $searchPost ? $searchPost : 'q'; ?>" placeholder="<?php echo isset($options['Search_Bar_Text']) ? $options['Search_Bar_Text'] : ($searchTitle ? 'Search ' . $searchTitle : 'Search Products'); ?>">
				<input class="prosper_submit" type="submit" name="submit" value="Search">
			</form>
		</div>
		<div class="clear"></div>
	<?php 
	endif;
	if ($filterArray)
	{
		?>
		<div id="filterSidebar">
			<div class="totalFound">Filter: <span><?php echo ($totalFound > 0 ? (number_format($totalFound) . ' results') : ($newTrendsTitle ? $newTrendsTitle : 'Browse these <strong>' . $trend . '</strong>'));// . ($demolishUrl && !$trend ? '<strong><a class="xDemolish" href=' . $demolishUrl . '> &#215;</a></strong>' : ''); ?></span></div>
			<?php if (!empty($pickedFacets)): ?>
				<div class="activeFilters">
					<span><?php echo implode('</span></div><div class="activeFilters"><span>', $pickedFacets);?></span>
				</div>
			<?php endif; 				
				$mainCount = count($mainFilters);
				foreach ($mainFilters as $i => $partials)
				{								
					$name = $i;
					if ($i === 'category')
						$name = 'categorie';	
					
						if ((empty($mainFilters[$i]) && !$params[$i]))
						{
						    continue;
						}		
						?>
						
                        <ul class="filterContainer active">
                            <li class="parent active">
                                <a href="javascript:void(0);" onclick="toggle_visible('<?php echo $i; ?>');toggle_hidden('<?php echo $i; ?>'); return false;" style="display:block;cursor:pointer; font-size:14px; font-weight:bold;"><img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_down_small.png'; ?>" alt=""/><?php echo ucfirst($name); ?>s</a>
                            </li>
                            <ul id="<?php echo $i; ?>" class="filterList <?php echo $i; ?>" style="display:block;">
        						<?php												
        							echo implode('', $partials);																
        						?>
    					   </ul>
    				    </ul>
					<?php
					$z++;
				}		
				?>
				<div class="clear"></div>
				<?php if ($options['Enable_Sliders']): ?>
					<div id="morePriceRange" onclick="toggle_visibility('priceRangeSlider');  toggle_hidden('morePriceRange'); toggle_visibility('hidePriceRange'); return false;" style="display:none;"><?php echo $dollarSlider; ?> <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_down_small.png'; ?>" alt=""/></div>
					<div id="hidePriceRange" onclick="toggle_hidden('priceRangeSlider'); toggle_hidden('hidePriceRange'); toggle_visibility('morePriceRange'); return false;" style="display:block;"><?php echo $dollarSlider; ?> <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_up_small.png'; ?>" alt=""/></div>		
					<div id="priceRangeSlider" style="display:block;">
						<br>
						<div id="sliderRange"></div>
						<form name="priceRange" method="POST" action="">
						    <input type="hidden" id="minRangeValue" value="<?php echo $lowRange; ?>">
						    <input type="hidden" id="maxRangeValue" value="<?php echo $highRange; ?>">
							<input type="text" class="min" id="rangeMin" name="priceSliderMin" value="<?php echo ($priceSlider[0] ? '$' . $priceSlider[0] : '$' . $lowRange); ?>">
							<input type="text" class="max" id="rangeMax" name="priceSliderMax" value="<?php echo ($priceSlider[1] ? '$' . $priceSlider[1] : '$' . $highRange); ?>"> 
							<input type="submit" value="Submit" style="display: none;" >
						</form>
					</div>
					<div class="clear"></div>
					<div id="morePercentRange" onclick="toggle_visibility('percentRangeSlider'); toggle_hidden('morePercentRange'); toggle_visibility('hidePercentRange'); return false;" style="display:none;">Percent Off <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_down_small.png'; ?>" alt=""/></div>
					<div id="hidePercentRange" onclick="toggle_hidden('percentRangeSlider'); toggle_hidden('hidePercentRange'); toggle_visibility('morePercentRange'); return false;" style="display:block;">Percent Off <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_up_small.png'; ?>" alt=""/></div>
					<div id="percentRangeSlider" style="display:block;">
						<br>
						<div id="sliderPercent"></div>
						<form name="percentOffRange" method="POST" action="">
							<input type="text" class="min" id="percentMin" name="percentSliderMin" value="<?php echo ($percentSlider[0] ? $percentSlider[0] . '%' : '0%'); ?>"/>
							<input type="text" class="max" id="percentMax" name="percentSliderMax" value="<?php echo ($percentSlider[1] ? $percentSlider[1] . '%' : '100%'); ?>"/>
							<div class="clear"></div>
							<input type="checkbox" style="display:inline-block;" name="onSale" <?php echo ($params['pR'] ? 'checked' : ''); ?> onChange="percentOffRange.submit();">
							<label style="display:inline-block;" for="onSale">On Sale Only</label>
							<input type="submit" value="Submit" style="display: none;" >
						</form>
					</div>
				<?php endif; ?>

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
			echo '<div class="noResults-secondary">Please try your search again.</div>';
		}
		echo '<div class="noResults-padding"></div>';
	}
	?>	

	<div id="prosperPriceSorter">
		<span class="sortLabel">Sort By: </span>
                			
			
		<?php 	
		$sortCount = count($sortArray);
		$c = 0;
		foreach ($sortArray as $i => $sort)
		{			        
			?>
			&nbsp;&nbsp;<a <?php echo (rawurldecode($params['sort']) == $sort ? 'class="activeSort"' : ''); ?> href="<?php echo $currentUrl . '/sort/' . $sort; ?>"><?php echo $i; ?></a>&nbsp;&nbsp;
			<?php
			if ($sortCount > ($c + 1) )
			{
                echo '|';
			}	
			
			$c++;		 
		}
		?>
		<?php echo (($params['sort'] != 'rel' && '' != $params['sort']) ? '<strong><a class="xDemolish" href=' . str_replace(array('/page/' . $params['page'], '/sort/' . $params['sort']), '', $url) . '> &#215;</a></strong>' : ''); ?>
	</div>

	<?php
	if (!$params['view'] || $params['view'] === 'list') 
	{ 
		?>
		<div id="productList" style="<?php echo ($filterArray ? 'width:80%' : 'width:100%'); ?>;float:right;">
			<?php
			if (!empty($results))
			{
				$resultsCount = count($results);
				// Loop to return Products and corresponding information
				foreach ($results as $i => $record)
				{			
					if (is_ssl())
					{
						$record['image_url'] = str_replace('http', 'https', $record['image_url']);
					}
					
					$cid = $record['catalogId'];				
					?>
					<div class="productBlock" <?php echo ($i == ($resultsCount - 1) ? 'style="border-bottom:none;"' : ''); ?>>
						<div class="productImage">
							<a href=<?php echo ($options['imageMercLink'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?>  rel="nolink"><span class="load"><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
						</div>
						<div class="productContent">							
							<div class="productTitle"><a href=<?php echo ($options['titleMercLink'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink"><span><?php echo preg_replace('/\(.+\)/i', '', $record['keyword']); ?></span></a></div>
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
							<div class="productBrandMerchant">
								<?php
								if($record['brand'])
								{
									echo '<span class="brandIn"><u>Brand</u>: ' . (!(preg_match('/' . $record['brand'] . '/i', rawurldecode($params['brand']))) ? '<a href="' . str_replace('/page/' . $params['page'], '', $url) . '/brand/' . rawurlencode($record['brand']) . '" rel="nolink"><cite>' . $record['brand'] . '</cite></a>' : $record['brand']) . '</span>';
								}								
								if($record['merchant'])
								{
									echo '<span class="merchantIn"><u>Merchant</u>: ' . (!(preg_match('/\b' . $record['merchant'] . '\b/i', rawurldecode($params['merchant']))) ? '<a href="' . str_replace('/page/' . $params['page'], '', $url) . '/merchant/' . rawurlencode($record['merchant']) . '" rel="nolink"><cite>' . $record['merchant'] . '</cite></a>' : $record['merchant']) . '</span>';
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
									<div class="productPriceNoSale"><span><?php echo '$' . number_format($record['price'], 2); ?></span></div>
									<?php
								}
								//otherwise strike-through Price and list the Price_Sale
								else
								{
									?>
									<div class="productPrice"><span><?php echo '$' . number_format($record['price'], 2)?></span></div>
									<div class="productPriceSale"><span><?php echo '$' . number_format($priceSale, 2)?></span></div>
									<?php
								}
							}
							?>
							<div class="shopCheck prosperVisit">		
								<a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $visitButton; ?>"/></a>				
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
		$classLoad = $gridImage < 120 ? 'class="loadCoup"' : 'class="load"';
		echo '<div id="simProd" style="' . ($filterArray ? 'width:80%' : 'width:100%') . ';float:right;border-top: 1px solid #ddd;">';
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
				$price 	   = $priceSale ? '$' . number_format($priceSale, 2) . '' : '$' . number_format($record['price'], 2);
				$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
				$cid 	   = $record['catalogId'];
				?>
				<li <?php echo 'style="width:' . $gridImage . '!important;border:1px solid #000"'; ?>>
						<div class="listBlock">
							<div class="prodImage">
								<a href=<?php echo ($options['imageMercLink'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink"><span <?php echo $classLoad . ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px;margin:0 40px"'); ?>><img <?php echo ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px;"'); ?> src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
							</div>								
							<div class="prodContent">
								<div class="prodTitle">
									<a href=<?php echo ($options['titleMercLink'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink">
										<?php echo $keyword; ?>
									</a>
								</div>   
								<div class="<?php echo ($priceSale ? 'prodPriceSale' : 'prodPrice'); ?>">  
								    <?php echo $price . '<span style="color:#666;font-size:12px;font-weight:normal;"> from</span><span style="color:#666;font-size:14px;font-weight:normal;"> ' . $record['merchant'] . '</span>' ?>
								</div>												
							</div>								
							<div class="shopCheck prosperVisit">		
								<a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $visitButton; ?>"/></a>				
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