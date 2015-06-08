<script type="text/javascript">
jQuery(function(){jQuery(document).ready(function(){var a=Math.floor(jQuery("#prosperShopMain")[0].getBoundingClientRect().width)-jQuery("#prosperFilterSidebar").width()-8,c=Math.ceil(a/250),b=Math.floor((a-8*c)/c);jQuery("#simProd.prosperResults").css("width",a);jQuery("#simProd.prosperResults li").css("width",b);jQuery("#simProd.prosperResults li .prosperLoad").css({height:b,width:b});jQuery("#simProd.prosperResults li").each(function(a){jQuery(this).attr("data-lirow",c+"-"+a)});(result=window.location.href.match(/cid\/(.{32})/))&&
550<document.body.clientWidth&&prosperProdDetails("",result[1])})});var resizeTimer;
jQuery(window).resize(function(){clearTimeout(resizeTimer);resizeTimer=setTimeout(function(){var a=jQuery("#prosperShopMain").width()-jQuery("#prosperFilterSidebar").width()-8,c=Math.ceil(a/250),b=Math.floor((a-8*c)/c);jQuery("#simProd.prosperResults").css("width",a);jQuery("#simProd.prosperResults li").css("width",b);jQuery("#simProd.prosperResults li .prosperLoad").css({height:b,width:b});jQuery("#simProd.prosperResults li").each(function(a){jQuery(this).attr("data-lirow",c+"-"+a)})},200)});
function toggle_visibility(a){a=document.getElementById(a);jQuery("#"+a.id).css("display","block"==jQuery("#"+a.id).css("display")?"none":"block");a.id+" fa fa-caret-down"==jQuery("i."+a.id).attr("class")?(jQuery("i."+a.id).removeClass("fa-caret-down"),jQuery("i."+a.id).addClass("fa-caret-up")):(jQuery("i."+a.id).removeClass("fa-caret-up"),jQuery("i."+a.id).addClass("fa-caret-down"))}
function prosperProdDetails(a,c){var b=jQuery("#"+a.id).attr("data-simresults");bodyWidth=jQuery("#prosperShopMain").width();resultWidth=bodyWidth-jQuery("#prosperFilterSidebar").width()-8;prodCount=Math.ceil(resultWidth/250);documentWidth=document.body.clientWidth;if(550>documentWidth)return b=jQuery("#"+a.id).attr("data-prosperKeyword"),window.location.href=window.location.protocol+"//"+window.location.hostname+"/product/"+b+"/cid/"+a.id,!1;if(b)var e=a.id.substr(3);else{var e=c?c:a.id,d=jQuery("#"+
e).attr("data-lirow"),b=d.match(/-(.*)/),d=d.match(/(.*)-/),b=parseInt(Math.floor(b[1]/d[1])*d[1])+prodCount,d=jQuery("*[data-lirow]").length;jQuery(".prosperDetails").remove();jQuery(".prosperpointer").remove();jQuery("#simProd.prosperResults li:nth-child("+(d>b?b:d)+")").after('<li class="prosperDetails" style="overflow:hidden;"></li>')}b=window.location.href.replace(/\/$/,"");window.history.pushState("Test","",b.replace(/\/cid\/.{32}/,"")+"/cid/"+e);jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",
data:{api_key:_prosperShop.api,filterCatalogId:e,imageSize:"500x500",limit:1,enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){jQuery("#prodbrand").empty();jQuery.each(a.data,function(a,b){if(b.description){var c=b.description;if(250<c.length)var d=c.substr(0,250),c=c.substr(249,c.length-250),c=d+'<span class="moreellipses">... </span><span class="morecontent"><span>'+c+'</span><a href="javascript:void(0);" class="morelink" onClick="moreDesc(this);">More</a></span>'}var f=
document.getElementById(e),d=jQuery(f).offset().left,g=jQuery("#simProd.prosperResults").offset().left,f=jQuery(f).outerWidth(!0),d=Math.floor(d-g+f/2-16)+"px";jQuery(".prosperDetails").html('<div><div class="prosperpointer" style="left:'+d+';height:14px;margin-top:10px;margin-bottom:-1px;position:relative;width:20px;z-index-1;"><img src="'+_prosperShop.img+'/arrow.png"/></div><div class="prosperDetsContain"><div class="prosperDets"><div class="prosperDetContent"><div class="pDetailsImage"><a href="'+
b.affiliate_url+'" target="_blank" rel="nofollow,nolink"><img src="'+b.image_url+'" alt="'+b.keyword+'" title="'+b.keyword+'" /></a></div><div class="pDetailsAll"><div class="pDetailsKeyword"><a href="'+b.affiliate_url+'" target="_blank" rel="nofollow,nolink">'+b.keyword+'</a></div><div class="pDetailsDesc">'+c+'</div><table class="productResults"></table></div></div></div><div class="simTitle">Similar Products</div><div class="prosperSimResults" style="width:100%!important;max-width:100%!important;"><ul></ul></div></div></div>');
jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:_prosperShop.api,filterProductId:b.productId,groupBy:"merchant",limit:10,enableFullData:0,imageSize:"125x125"},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){jQuery.each(a.data,function(a,b){jQuery(".productResults").append('<tr><td><img style="width:80px;height:40px;" src="http://images.prosperentcdn.com/images/logo/merchant/original/120x60/'+b.merchantId+".jpg?prosp=&m="+b.merchant+
'"/></td><td style="vertical-align:middle;"><strong>$'+(b.price_sale?b.price_sale:b.price)+'</strong></td><td style="vertical-align:middle;"><div class="shopCheck prosperVisit"><a itemprop="offerURL" href="'+b.affiliate_url+'" target="_blank" rel="nofollow,nolink"><input type="submit" type="submit" class="prosperVisitSubmit" value="Visit Store"/></a></div></td></tr>')})},error:function(){}});jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:_prosperShop.api,query:b.keyword,
limit:6,enableFullData:0,imageSize:"125x125"},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){var b=Math.floor(jQuery(".prosperSimResults")[0].getBoundingClientRect().width),c=Math.ceil(b/125),d=Math.floor((b-8*c)/c);jQuery.each(a.data,function(a,b){jQuery(".prosperSimResults ul").append('<li data-simresults="1" id="sim'+b.catalogId+'" class="'+b.productId+'" onClick="prosperProdDetails(this);" style="width:'+d+'px;"><div class="prodImage"><img src="'+b.image_url+
'"/></div><div class="prodContent"><div class="prodTitle">'+(b.brand?b.brand:"&nbsp;")+"</div>$"+b.price+"</div></li>")})},error:function(){}})})},error:function(){}})}function moreDesc(a){jQuery(a).hasClass("less")?(jQuery(a).removeClass("less"),jQuery(a).html("More")):(jQuery(a).addClass("less"),jQuery(a).html("Less"));jQuery(a).parent().prev().toggle();jQuery(a).prev().toggle();return a};
</script>
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
    				        <i class="fa fa-search"></i>
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

			    <?php if ($lowRange != $highRange): ?>	
			    					
					<ul class="prosperFilterContainer">    						
                       <li class="prosperParent" onclick="toggle_visibility('prospPrice'); return false;">
    					    <a href="javascript:void(0);">                                        
                                <span class="prosperArrow"><i class="prospPrice fa fa-caret-down"></i></span>
                                <span class="prosperFilterName">Price Range</span>
                            </a>
                        </li>
                        <li id="prospPrice" class="prosperFilterList prospPrice" style="text-align:center">
    						<form name="priceRange" method="POST" action="">
    							<div style="padding-bottom:4px;"><span style="color:#747474;padding-right:2px;">$</span><input type="text" class="min" id="prospRangeMin" name="priceSliderMin" value="<?php echo number_format(($priceSlider[0] ? $priceSlider[0] : $lowRange)); ?>">
    							<span style="color:#747474;padding-right:4px;">&nbsp;to&nbsp;&nbsp;&nbsp;</span></div>
    							<div><span style="color:#747474;padding-right:2px;">$</span><input type="text" class="max" id="prospRangeMax" name="priceSliderMax" value="<?php echo number_format(($priceSlider[1] ? $priceSlider[1] : $highRange) ); ?>"> 
    							<input type="submit" value="Go" class="submit" style="display:inline;"></div>
    						</form>
                        </li>
					</ul>
					
				<?php endif; ?>
					<ul class="prosperFilterContainer">
                       <li class="prosperParent" onclick="toggle_visibility('prospPercent'); return false;">
    					    <a href="javascript:void(0);">                     
                                <span class="prosperArrow"><i class="prospPercent fa fa-caret-down"></i></span>
                                <span class="prosperFilterName">Percent Off</span>
                            </a>
                        </li>
                        <li id="prospPercent" class="prosperFilterList prospPercent" style="width:100%">
        					<form name="percentOffRange" method="POST" action="">
        						<input type="text" class="min" id="prospPercentMin" name="percentSliderMin" value="<?php echo ($percentSlider[0] ? $percentSlider[0] : '0'); ?>"/><span style="color:#747474;padding-left:2px;">%</span>
        						<span style="color:#747474;">to</span>
        						<input type="text" class="max" id="prospPercentMax" name="percentSliderMax" value="<?php echo ($percentSlider[1] ? $percentSlider[1] : '100'); ?>"/><span style="color:#747474;padding-left:2px;">%</span>
        						<input type="submit" value="Go" class="submit" style="display:inline;">
        						<?php /*<li style="display:block; margin-top: 10px;">
            						<i class="fa fa-times"></i>
            						<label style="display:inline-block;color:#646464;" for="onSale">On Sale Only</label><input type="hidden" name="onSale">
        						</li> */?>
        					</form>
    					</li>
					</ul>
		</div>		
		<?php
	}

	if (!$noResults): ?>
    	<div id="prosperPriceSorter">
    		<span class="sortLabel">Sort By: </span>			
    		<?php
    		
    		$sortCount = count($sortArray);
    		$c = 0;
    		foreach ($sortArray as $i => $sort)
    		{		  
    			?>
    			&nbsp;&nbsp;<a <?php echo (preg_match('/' . $sortedParam . '/i', $sort) ? 'class="activeSort"' : ''); ?> href="<?php echo ($sortUrl ? $sortUrl : $currentUrl) . '/sort/' . $sort; ?>"><?php echo $i; ?></a>&nbsp;&nbsp;
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
							<a href=<?php echo $options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'; ?>  rel="nolink"><span class="load"><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
						</div>
						<div class="productContent">							
							<div class="productTitle"><a href=<?php echo ($options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?> rel="nolink"><span><?php echo preg_replace('/\(.+\)/i', '', $record['keyword']); ?></span></a></div>
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
						<a onClick="return false;" href=<?php echo $options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'; ?> rel="nolink"><span class="prosperLoad"><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
					</div>								
					<div class="prodContent">
						<div class="prodTitle">
						    <a onClick="return false;" href=<?php echo $options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'; ?> rel="nolink" style="text-decoration:none;color:#646464"><?php echo $record['brand']; ?></a>
							<?php /*<a href=<?php echo ($options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"javascript:void(0);" onClick="prosperProdDetails();"'); ?> rel="nolink">
								<?php echo $keyword; ?>
							</a> */?>
						</div>   
						<div class="<?php echo ($priceSale ? 'prodPriceSale' : 'prodPrice'); ?>">  
						    <?php echo $price . '<span style="color:#666;font-size:12px;font-weight:normal;"> from</span><div style="display:inline;color:#666;font-size:14px;font-weight:normal;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;"> ' . $record['merchant'] . '</div>' ?>
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