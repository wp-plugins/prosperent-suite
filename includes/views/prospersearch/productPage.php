<?php if ($options['hideShopPageTitle']): ?>
<style>
    .page .entry-title { display: none; }
    .page .article-title { display: none; }
</style>
<?php endif; ?>

<script type="text/javascript">
/*<![CDATA[*/function d(g){var f=document.getElementById(g);"none"==f.style.display&&(f.style.display="")}function b(g){var f=document.getElementById(g);"inline-block"==f.style.display&&(f.style.display="none")}/*]]>*/


jQuery(function(){jQuery(document).ready(function(){
	var bodyWidth   = Math.floor(jQuery('#simProd.prosperSimResults')[0].getBoundingClientRect().width),
	    prodCount   = Math.ceil(bodyWidth / 185),
	    liEach      = Math.floor((bodyWidth - (prodCount * 8)) / prodCount);
	jQuery( "#simProd.prosperSimResults li" ).css({
        height:'225px',
        width:liEach
    });
	jQuery("#simProd.prosperSimResults li .prosperLoad").css({
        height:liEach,
        width:liEach
    }); 
})
});

var resizeTimer;
jQuery(window).resize(function() {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(function() {
		var bodyWidth   = Math.floor(jQuery('#simProd.prosperSimResults')[0].getBoundingClientRect().width),
	        prodWidth   = Math.ceil(bodyWidth / 185),
	        liEach      = (bodyWidth - (prodWidth * 8)) / prodWidth;
		
		jQuery( "#simProd.prosperSimResults li" ).css({
	        height:'225px',
	        width:liEach
	    });
		jQuery("#simProd.prosperSimResults li .prosperLoad").css({
	        height:liEach,
	        width:liEach
	    });
  }, 200);
}); 
 
 </script>
<div class="prosper_searchform" style="margin-bottom:0;">
    <form id="prosperSearchForm"  class="searchform" method="POST" action="" rel="nolink">
        <div class="prosperSearchInputs">	
            <input class="prosper_field" type="text" name="<?php echo $searchPost ? $searchPost : 'q'; ?>" id="s" placeholder="<?php echo isset($options['Search_Bar_Text']) ? $options['Search_Bar_Text'] : 'Search Products'; ?>">
        	<span class="prosperSearchRight">				    
			    <button class="prosper_submit submit" id="searchsubmit" type="submit" name="submit">
			        <i class="fa fa-search"></i>
			    </button>
			</span>
        </div>
    </form>
</div>
<div class="backTo" style="display:inline-block;padding-top:4px; color:#00AFF0;font-weight:bold;"><a href="<?php echo $returnUrl; ?>" rel="nolink">&#8592;&nbsp;Return to Search Results</a></div>
<div id="product" itemscope itemtype="http://data-vocabulary.org/Product"> 
    <div class="productBlock">
		<div class="shopCheck productImage" style="text-align:center;">
            <a itemprop="offerURL" href="<?php echo $mainRecord[0]['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><img itemprop="image" src="<?php echo $mainRecord[0]['image_url']; ?>" alt="<?php echo $mainRecord[0]['keyword']; ?>" title="<?php echo $mainRecord[0]['keyword']; ?>"/></a>
		</div>
        <div class="productContent">
            <div class="shopCheck productTitle"><a href="<?php echo $mainRecord[0]['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><span itemprop="name"><?php echo preg_replace('/\(.+\)/i', '', $mainRecord[0]['keyword']); ?></span></a></div>
            <div class="productDescription" itemprop="description">
				<?php 
                if (strlen($mainRecord[0]['description']) > 200)
                {
                    echo trim(substr($mainRecord[0]['description'], 0, 200));					
					?>
					<span id="moreDesc" style="display:inline-block;">... <a style="cursor:pointer;" onclick="d('fullDesc'); b('moreDesc');">more</a></span><span id="fullDesc" style="display:none;-moz-hyphens:manual;font-style:normal;"><?php echo trim(substr($mainRecord[0]['description'], 200)); ?></span>
					<?php
                }
                else
                {
                    echo $mainRecord[0]['description'];
                }
                ?>
            </div>
            <div class="productBrandMerchant">
				<table class="productResults" itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">
					<?php                
					foreach ($results as $product)
					{								
						$priceSale = $product['priceSale'] ? $product['priceSale'] : $product['price_sale'];
						
						echo '<tr itemscope itemtype="http://data-vocabulary.org/Product">';
						echo '<td itemprop="seller"><img style="width:80px;height:40px;" src="http://images.prosperentcdn.com/images/logo/merchant/original/120x60/' . $product['merchantId'] . '.jpg?prosp=&m=' . $product['merchant'] . '"/></td>';
						echo '<td itemprop="price" style="vertical-align:middle;">' . ($priceSale ? '$' . number_format($priceSale, 2) :  '$' . number_format($product['price'], 2, '.', ',')) . '</td>';
						echo '<meta itemprop="priceCurrency" content=USD"/>';
						echo '<td style="vertical-align:middle;"><div class="shopCheck prosperVisit"><a itemprop="offerURL" href="' . $product['affiliate_url'] . '" target="' . $target . '" rel="nofollow,nolink"><input type="submit" type="submit" class="prosperVisitSubmit" value="' . $visitButton . '"/></a></div></td>';
						echo '</tr>';
					}
					?>
				</table>				
            </div>
        </div>
    </div>
</div>

<?php
if (count($similar) > 1)
{
    echo '<div class="simTitle">Similar Products</div>';
    echo '<div id="simProd" class="prosperSimResults" style="width:100%!important;max-width:100%!important;overflow:hidden;max-height:225px;">';
    echo '<ul>';
    foreach ($similar as $prod)
    {
		$priceSale = $prod['priceSale'] ? $prod['priceSale'] : $prod['price_sale'];
        $price 	   = $priceSale ? $priceSale : $prod['price'];
		$keyword   = preg_replace('/\(.+\)/i', '', $prod['keyword']);
		$cid 	   = $prod['catalogId'];
        ?>
            <li>
				<div class="prodImage">
					<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $cid; ?>" rel="nolink"><span class="prosperLoad"><img src="<?php echo $prod['image_url']; ?>" alt="<?php echo $prod['keyword']; ?>" title="<?php echo $prod['keyword']; ?>" /></span></a>
				</div>
				<div class="prodContent">
					<div class="prodTitle">
                        <a href="<?php echo  $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $cid; ?>"><?php echo $prod['brand']; ?></a>
					</div>
					$<?php echo $price; ?>
				</div>
            </li>
        <?php
    }
    echo '</ul>';
    echo '</div>';
}