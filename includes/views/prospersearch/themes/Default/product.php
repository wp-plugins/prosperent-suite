<script type="text/javascript">
jQuery(function(){jQuery(document).ready(function(){
	var bodyWidth   = Math.floor(jQuery('#prosperShopMain')[0].getBoundingClientRect().width),
	    resultWidth = bodyWidth - (jQuery( "#prosperFilterSidebar" ).width()) - 8,
	    prodCount   = Math.ceil(resultWidth / 250),
	    liEach      = Math.floor((resultWidth - (prodCount * 8)) / prodCount);

	jQuery( "#simProd.prosperResults" ).css( "width", resultWidth);	
	jQuery( "#simProd.prosperResults li" ).css( "width", liEach);
	jQuery("#simProd.prosperResults li .prosperLoad").css({
        height:liEach,
        width:liEach
    });

    jQuery("#simProd.prosperResults li").each(function(index) {
        jQuery(this).attr("data-lirow", prodCount + '-' + index);    
    });

    if (result = (window.location.href).match(/cid\/(.{32})/))
    {
        prosperProdDetails('', result[1]);
    }    
})
});

var resizeTimer;
jQuery(window).resize(function() {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(function() {
		var bodyWidth   = jQuery('#prosperShopMain').width(),
	    resultWidth = bodyWidth - (jQuery( "#prosperFilterSidebar" ).width()) - 8,
	    prodCount   = Math.ceil(resultWidth / 250),
	    liEach      = Math.floor((resultWidth - (prodCount * 8)) / prodCount);
		
		jQuery( "#simProd.prosperResults" ).css( "width", resultWidth);	
		jQuery( "#simProd.prosperResults li" ).css( "width", liEach);
		jQuery("#simProd.prosperResults li .prosperLoad").css({
	        height:liEach,
	        width:liEach
	    });
	    
	    jQuery("#simProd.prosperResults li").each(function(index) {
	        jQuery(this).attr("data-lirow", prodCount + '-' + index);    
	    });
	     
  }, 200);
});

function toggle_visibility(e) {
	var l = document.getElementById(e);
	jQuery("#" + l.id).css('display', jQuery("#" + l.id).css('display') == 'block' ? 'none' : 'block');
	l.id + ' fa fa-caret-down' == jQuery("i." + l.id).attr('class') ? (jQuery("i." + l.id).removeClass("fa-caret-down"), jQuery("i." + l.id).addClass("fa-caret-up")) : (jQuery("i." + l.id).removeClass("fa-caret-up"), jQuery("i." + l.id).addClass("fa-caret-down"));
}

function prosperProdDetails(d, r)
{		
    var liSim = jQuery('#' + d.id ).attr("data-simresults");
    if (liSim)
    {
    	var catalogId = (d.id).substr(3),
    	    productId = d.className;
    }
    else
    {
    	var catalogId = r ? r : d.id,
    	    productId = d.className,    	        
            liData    = jQuery('#' + catalogId).attr("data-lirow"), 
	        liRow     = liData.match(/-(.*)/),
	        liNumber  = liData.match(/(.*)-/),
            liAfter   = parseInt((Math.floor(liRow[1]/liNumber[1]) * liNumber[1])) + 3
            liCount   = jQuery("#simProd.prosperResults ul li").length;




    	jQuery(".prosperDetails").remove();
    	jQuery(".prosperpointer").remove();  
    	jQuery("#simProd.prosperResults li:nth-child(" + (liCount > liAfter ? liAfter : liCount) + ")").after('<li class="prosperDetails" style="margin-top:0;width:100%!important;border:1px solid #bebebe;box-shadow:0 2px 1px rgba(0,0,0,.1),0 0 1px rgba(0,0,0,0.1);"></li>');
    }
    

    var url = (window.location.href).replace(/\/$/, '');
    window.history.pushState('Test', '', url.replace(/\/cid\/.{32}/, '') + '/cid/' + catalogId);
	
	jQuery.ajax({
        type: "POST",
        url: "http://api.prosperent.com/api/search",
        data: {
            api_key: _prosperShop.api,
            filterCatalogId: catalogId,
            imageSize:'500x500',
            limit: 1,
            enableFullData: 0
        },
        contentType: "application/json; charset=utf-8",
        dataType: "jsonp",
        success: function(a) {
        	jQuery("#prodbrand").empty();  
            var showChars = 250,
                ellipses  = '...',
                moreText  = 'More';
            			
            jQuery.each(a.data, function(c, b) {	   

                var description = b.description;
                if (description.length > showChars)
                {
                    var content     = description.substr(0, showChars),
                        fullCont    = description.substr(showChars-1, description.length - showChars),
                        description = content + '<span class="moreellipses">' + ellipses + ' </span><span class="morecontent"><span>' + fullCont + '</span><a href="javascript:void(0);" class="morelink" onClick="moreDesc(this);">' + moreText + '</a></span>';
                }
                
                jQuery(".prosperDetails").html('<div class="prosperDets"><div class="prosperpointer" style="height:10px;maring-top:10px;margin-bottom:-1px;position:relative;width:20px;z-index-1;"><img src="'+_prosperShop.img+'/arrow.png"/></div><div class="prosperDetContent"><div class="pDetailsImage"><a href="'+b.affiliate_url+'" target="_blank" rel="nofollow,nolink"><img src="'+b.image_url+'" alt="'+b.keyword+'" title="'+b.keyword+'" /></a></div><div class="pDetailsAll"><div class="pDetailsKeyword"><a href="'+b.affiliate_url+'" target="_blank" rel="nofollow,nolink">'+b.keyword+'</a></div><div class="pDetailsDesc">'+description+'</div><table class="productResults"></table></div></div></div><div class="simTitle">Similar Products</div><div class="prosperSimResults" style="width:100%!important;max-width:100%!important;"><ul></ul></div>');

                jQuery.ajax({
                    type: "POST",
                    url: "http://api.prosperent.com/api/search",
                    data: {
                        api_key: _prosperShop.api,
                        filterProductId:b.productId,
                        groupBy:'merchant',
                        limit: 10,
                        enableFullData: 0,
                        imageSize:'125x125'
                    },
                    contentType: "application/json; charset=utf-8",                    
                    dataType: "jsonp",
                    success: function(d) {   	               		
                        jQuery.each(d.data, function(e, f) {	                              
                            jQuery(".productResults").append('<tr><td><img style="width:80px;height:40px;" src="http://images.prosperentcdn.com/images/logo/merchant/original/120x60/' + f.merchantId + '.jpg?prosp=&m=' + f.merchant + '"/></td><td style="vertical-align:middle;"><strong>$'+(f.price_sale ? f.price_sale : f.price)+'</strong></td><td style="vertical-align:middle;"><div class="shopCheck prosperVisit"><a itemprop="offerURL" href="' + f.affiliate_url + '" target="_blank" rel="nofollow,nolink"><input type="submit" type="submit" class="prosperVisitSubmit" value="Visit Store"/></a></div></td></tr>');           
                        })            
                    },
                    error: function() {
                        return;
                    }
                });	

                jQuery.ajax({
                    type: "POST",
                    url: "http://api.prosperent.com/api/search",
                    data: {
                        api_key: _prosperShop.api,
                        query: b.keyword,
                        limit: 6,
                        enableFullData: 0,
                        imageSize:'125x125'
                    },
                    contentType: "application/json; charset=utf-8",                    
                    dataType: "jsonp",
                    success: function(g) {   			
                        jQuery.each(g.data, function(h, i) {                            	    
                        	var bodyWidth = Math.floor(jQuery('#simProd')[0].getBoundingClientRect().width),
                    	    prodCount     = Math.ceil(bodyWidth / 125),
                    	    liEach        = Math.floor((bodyWidth - (prodCount * 8)) / prodCount);
                        	
                            jQuery(".prosperSimResults ul").append('<li data-simresults="1" id="sim'+ i.catalogId +'" class="'+ i.productId +'" onClick="prosperProdDetails(this);" style="width:' + liEach + 'px;"><div class="prodImage"><img src="'+i.image_url+'"/></div><div class="prodContent"><div class="prodTitle">'+(i.brand ? i.brand : '&nbsp;')+ '</div>$' +i.price+'</div></li>');           
                        })            
                    },
                    error: function() {
                    	return;
                    }
                });	         
            }) 

            
        },
        error: function() {
        	return;
        }
    });	

    var offset = jQuery(d).offset();
    var width = jQuery(d).width();
    var height = jQuery(d).height();
    var centerX = (offset ? offset.left + width / 2 : '');
    var centerY = (offset ? offset.top + height : '');
    console.log(centerY);
    jQuery( ".prosperpointer" ).css( "left", centerX + 'px');

}

function moreDesc(e) {
    if(jQuery(e).hasClass("less")) {
    	jQuery(e).removeClass("less");
    	jQuery(e).html("More");
    } else {
    	jQuery(e).addClass("less");
    	jQuery(e).html("Less");
    }
    jQuery(e).parent().prev().toggle();
    jQuery(e).prev().toggle();
    return e;
}


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
			<?php if ($noResults){ echo '<div class="prosperNoResults">Please try your search again.</div>'; } ?> 			
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
                        <li id="prospPrice" class="prosperFilterList prospPrice">
    						<form name="priceRange" method="POST" action="">
    							<span style="color:#747474;padding-right:2px;">$</span><input type="text" class="min" id="prospRangeMin" name="priceSliderMin" value="<?php echo number_format(($priceSlider[0] ? $priceSlider[0] : $lowRange)); ?>">
    							<span style="color:#747474;padding-right:">&nbsp;to&nbsp;</span>
    							<span style="color:#747474;padding-right:2px;">$</span><input type="text" class="max" id="prospRangeMax" name="priceSliderMax" value="<?php echo number_format(($priceSlider[1] ? $priceSlider[1] : $highRange) ); ?>"> 
    							<input type="submit" value="Go" class="submit" style="display:inline;">
    						</form>
                        </li>
					</ul>
					
				<?php endif; ?>
					<ul class="prosperFilterContainer">
                       <li class="prosperParent" onclick="toggle_visibility('prospPrice'); return false;">
    					    <a href="javascript:void(0);">                     
                                <span class="prosperArrow"><i class="prospPercent fa fa-caret-down"></i></span>
                                <span class="prosperFilterName">Percent Off</span>
                            </a>
                        </li>
                        <li id="prospPercent" class="prosperFilterList prospPercent" style="">
        					<form name="percentOffRange" method="POST" action="">
        						<input type="text" class="min" id="prospPercentMin" name="percentSliderMin" value="<?php echo ($percentSlider[0] ? $percentSlider[0] : '0'); ?>"/><span style="color:#747474;padding-left:2px;">%</span>
        						<span style="color:#747474;padding-right:">&nbsp;to&nbsp;</span>
        						<input type="text" class="max" id="prospPercentMax" name="percentSliderMax" value="<?php echo ($percentSlider[1] ? $percentSlider[1] : '100'); ?>"/><span style="color:#747474;padding-left:2px;">%</span>
        						<input type="submit" value="Go" class="submit" style="display:inline;">
        						<li style="display:block; margin-top: 10px;">
            						<i class="fa fa-times"></i>
            						<label style="display:inline-block;color:#646464;" for="onSale">On Sale Only</label>
        						</li>
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
					<div id="<?php echo $cid; ?>" class="<?php echo $record['productId']; ?> productBlock" <?php echo ($i == ($resultsCount - 1) ? 'style="border-bottom:none;"' : ''); ?>>
						<div class="productImage">
							<a href=<?php echo ($options['gotoMerchantBypass'] ? '"' . $record['affiliate_url'] . '" target="' . $target .  '"' :  '"' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid .  '"'); ?>  rel="nolink"><span class="load"><img src="<?php echo $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
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
			    <li id="<?php echo $cid; ?>" class="<?php echo $record['productId']; ?>" onClick="return prosperProdDetails(this);">
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