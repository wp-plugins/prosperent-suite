<?php if ($options['hideShopPageTitle']): ?>
<style>
    .page .entry-title { display: none; }
    .page .article-title { display: none; }
</style>
<?php endif; ?>
<div class="clear"></div>
<div id="prosperShopMain">
	<?php if (!$options['noSearchBar']): ?>
		<div class="prosper_searchform">
		    <?php if($options['searchTitle']): ?>
                <div class="searchSpan" style="color:#747474;"><?php echo $options['searchTitle']; ?></div>
            <?php endif; ?>	         
			<form id="prosperSearchForm" class="searchform" method="POST" action="" rel="nolink">
                <div class="prosperSearchInputs">	   
    				<input id="s" class="prosper_field" value="<?php echo ($query ? $query : ''); ?>" type="text" name="<?php echo $searchPost ? $searchPost : 'q'; ?>" placeholder="<?php echo isset($options['Search_Bar_Text']) ? $options['Search_Bar_Text'] : ($searchTitle ? 'Search ' . $searchTitle : 'Search Products'); ?>">
    				<span class="prosperSearchRight">				    
    				    <button class="prosper_submit submit" id="searchsubmit" type="submit" name="submit">
    				        <i style="font-size:16px;vertical-align:middle;" class="fa fa-search"></i>
    				    </button>
    				</span>
			    </div>	
			</form>
			<?php 
			if ($noResults){ echo '<div class="prosperNoResults">Please try your search again.</div>'; }
            if ($related){echo '<div class="prosperNoResults">Showing Related Products to your Original Search.</div><div class="clear"></div>'; }
			?> 			
		</div>
	<?php 
	endif;
	
	if ($filterArray)
	{
		?>
		<div id="prosperFilterSidebar">
			<div class="prosperTotalFound">Filter: <span><?php echo number_format($totalFound) . ' results';/* . ($demolishUrl && !$trend ? '<strong><a class="xDemolish" href=' . $demolishUrl . '> &#215;</a></strong>' : '');*/ ?></span></div>
			<?php if (!empty($pickedFacets)): ?>
				<div class="prosperActiveFilters">
					<?php echo implode('', $pickedFacets);?>
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
						
                        <ul class="prosperFilterContainer">
                            <li class="prosperParent" onclick="toggle_visibility('prosp<?php echo ucwords($i); ?>'); return false;">
                                <a href="javascript:void(0);">                                    
                                    <span class="prosperArrow"><i class="prosp<?php echo ucwords($i); ?> fa fa-caret-down"></i></span>
                                    <span class="prosperFilterName"><?php echo ucfirst($name); ?>s</span>
                                </a>
                            </li>
                            <ul id="prosp<?php echo ucwords($i); ?>" class="prosperFilterList prosp<?php echo ucwords($i); ?>">
        						<?php												
        							echo implode('', $partials);																
        						?>
    					   </ul>
    				    </ul>
					<?php
					$z++;
				}		
				?>
                <form name="priceRange" method="POST" action="" style="border-bottom:1px solid #ccc">
                    <ul class="prosperFilterContainer" style="border:none">  
			    <?php if ($lowRange != $highRange): ?>		    					
                       <li class="prosperParent" onclick="toggle_visibility('prospPrice'); return false;">
    					    <a href="javascript:void(0);">                                        
                                <span class="prosperArrow"><i class="prospPrice fa fa-caret-down"></i></span>
                                <span class="prosperFilterName">Price Range</span>
                            </a>
                        </li>
                        <li id="prospPrice" class="prosperFilterList prospPrice">
    							<div style="padding-bottom:4px;"><span style="color:#747474;padding-right:2px;">$</span><input style="width:45px!important;" type="text" class="min" id="prospRangeMin" name="priceSliderMin" value="<?php echo number_format(($priceSlider[0] ? $priceSlider[0] : $lowRange)); ?>">
    							<span style="color:#747474;padding-right:4px;">&nbsp;to&nbsp;</span>
    							<span style="color:#747474;padding-right:2px;">$</span><input style="width:60px!important;" type="text" class="max" id="prospRangeMax" name="priceSliderMax" value="<?php echo number_format(($priceSlider[1] ? $priceSlider[1] : $highRange) ); ?>"> 
                        </li>
					
				<?php endif; ?>
                       <li class="prosperParent" onclick="toggle_visibility('prospPercent'); return false;">
    					    <a href="javascript:void(0);">                     
                                <span class="prosperArrow"><i class="prospPercent fa fa-caret-down"></i></span>
                                <span class="prosperFilterName">Discount</span>
                            </a>
                        </li>
                        <li id="prospPercent" class="prosperFilterList prospPercent" style="width:100%; margin-bottom:6px!important;">
    						<input type="text" class="min" id="prospPercentMin" style="width:32%" name="percentSliderMin" value="<?php echo ($percentSlider[0] ? $percentSlider[0] : '0'); ?>"/><span style="color:#747474;padding-left:2px;">%</span>
    						<span style="color:#747474;">to</span>
    						<input type="text" class="max" id="prospPercentMax" style="width:32%!important;" name="percentSliderMax" value="<?php echo ($percentSlider[1] ? $percentSlider[1] : '100'); ?>"/><span style="color:#747474;padding-left:2px;">%</span>
        				</li>
						<li style="min-height:1.6em;padding-bottom:6px;margin-bottom:6px;"><input style="float:right;" type="submit" id="s" value="Go" class="submit"/></li> 
				    </ul>

                </form>
		</div>		
		<?php
	}
?>
<div style="float:right;">
	<div id="views"> 
	    <a href="<?php echo str_replace(array('/view/'.$params['view']),'',$url).'/view/grid';?>"><span class="gridIcon"></span></a> 
	    <a href="<?php echo str_replace(array('/view/'.$params['view']),'',$url).'/view/list';?>"><span class="listIcon"></span></a>
    </div>
	<?php 
	if (!$noResults): ?>
    	<div id="prosperPriceSorter">
    		<span class="sortLabel">Sort By: </span>			
    		<?php
    		
    		$sortCount = count($sortArray);
    		$c = 0;
    		foreach ($sortArray as $i => $sort)
    		{		  
    			?>
    			&nbsp;&nbsp;<a <?php echo (preg_match('/' . $sortedParam . '/i', $sort) ? 'class="activeSort"' : ''); ?> href="<?php echo ($sortUrl ? $sortUrl : $url) . '/sort/' . $sort; ?>"><?php echo $i; ?></a>&nbsp;&nbsp;
    			<?php
    			if ($sortCount > ($c + 1) )
    			{
                    echo '|';
    			}	
    			
    			$c++;		 
    		}
    		?>		
    	</div>
	<?php endif; ?>
	</div>
	<div id="simProd" class="prosperResults" style="<?php echo (!$filterArray ? 'width:100%!important;max-width:100%!important;' : 'margin-left:8px;'); ?>">
<?php 
	if (!$params['view'] || $params['view'] === 'list') 
	{ 
		?>
		<div id="productList" style="width:100%;float:right;display:inline-block;border:none;">
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
					<div data-prosperKeyword="<?php echo $keyword; ?>" id="<?php echo $cid; ?>" class="<?php echo $record['productId']; ?> productBlock" <?php echo ($i == ($resultsCount - 1) ? 'style="border-bottom:none;"' : ''); ?>>
						<div class="productImage">
							<a href=<?php echo $options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '" rel="nolink,nofollow"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '" rel="nolink"'; ?>><span class="load"><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
						</div>
						<div class="productContent">							
							<div class="productTitle"><a href=<?php echo $options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '" rel="nolink,nofollow"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '" rel="nolink"'; ?>><span><?php echo preg_replace('/\(.+\)/i', '', $record['keyword']); ?></span></a></div>
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
		?></div><?php 
	} 
	elseif ($params['view'] === 'grid')
	{
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
			    <li id="<?php echo $cid; ?>" class="<?php echo $record['productId']; ?>" onClick="return prosperProdDetails(this);" data-prosperKeyword="<?php echo $keyword; ?>">
					<div class="prodImage">
						<a onClick="return false;" href=<?php echo $options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '" rel="nolink,nofollow"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '" rel="nolink"'; ?>><span class="prosperLoad"><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
					</div>								
					<div class="prodContent">
						<div class="prodTitle">
						    <a onClick="return false;" href=<?php echo $options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '" rel="nolink,nofollow"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '" rel="nolink"'; ?> style="text-decoration:none;color:#646464"><?php echo ($record['brand'] ? $record['brand'] : '&nbsp;'); ?></a>
							<div style="position:absolute;left:-9999em;height:1px;line-height:1px;"><?php echo $record['description']; ?> </div>
						</div>   
						<div class="prodPrice">  
						    <span class="prosperPrice"><?php echo $price; ?></span><span class="prosperExtra" style="display:inline-block;color:#666;font-size:14px;font-weight:normal;text-overflow:ellipsis;white-space:nowrap;-webkit-hyphens:auto;-moz-hyphens:auto;hyphens:auto;word-wrap:break-word;overflow:hidden;vertical-align:text-bottom;"><span style="color:#666;font-size:12px;font-weight:normal;">&nbsp;from </span><?php echo $record['merchant']; ?></span> 
						</div>												
					</div>								
					<div class="shopCheck prosperVisit">		
						<a href="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input id="submit" class="submit" type="submit" value="<?php echo $visitButton; ?>"/></a>				
					</div>							
				</li>
				<?php
			}
		}
		?>
            
                  
            
		
        </ul>
        <?php 
	}
?>
		
	<?php
	$this->searchModel->prosperPagination($totalAvailable, $params['page']);
	?>
	</div>
</div>
<div class="clear"></div>