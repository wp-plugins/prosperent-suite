<script type="text/javascript">/*<![CDATA[*/function d(g){var f=document.getElementById(g);"none"==f.style.display&&(f.style.display="")}function b(g){var f=document.getElementById(g);"inline-block"==f.style.display&&(f.style.display="none")}/*]]>*/</script>
<div class="prosper_searchform" >
    <form class="searchform" method="POST" action="" rel="nolink">
        <input class="prosper_field" type="text" name="<?php echo $searchPost ? $searchPost : 'q'; ?>" id="s" placeholder="<?php echo isset($options['Search_Bar_Text']) ? $options['Search_Bar_Text'] : 'Search Products'; ?>">
        <input id="submit" class="prosper_submit" type="submit" value="Search">
    </form>
</div>
<div class="backTo" style="display:inline-block;padding-top:4px; color:#00AFF0;font-weight:bold;"><a href="<?php echo $returnUrl; ?>" rel="nolink">&#8592;&nbsp;Return to Search Results</a></div>
<div id="product" itemscope itemtype="http://data-vocabulary.org/Product">
    <div class="shopCheck productTitle"><a href="<?php echo $mainRecord[0]['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><span itemprop="name"><?php echo preg_replace('/\(.+\)/i', '', $mainRecord[0]['keyword']); ?></span></a></div>
    <div class="productBlock">
		<div class="shopCheck productImage" style="text-align:center;">
            <a itemprop="offerURL" href="<?php echo $mainRecord[0]['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><img itemprop="image" src="<?php echo $mainRecord[0]['image_url']; ?>" alt="<?php echo $mainRecord[0]['keyword']; ?>" title="<?php echo $mainRecord[0]['keyword']; ?>"/></a>
        	<br>
			<?php
			if (count($results) <= 1 )
			{
				?>
				<div class="shopCheck prosperVisit">		
					<a itemprop="offerURL" href="<?php echo $mainRecord[0]['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $visitButton; ?>"/></a>				
				</div>	
				<?php
			}
			?>
		</div>
        <div class="productContent">
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
                <?php 
                if($mainRecord[0]['category'])
                {	
					$mainRecord[0]['category'] = preg_replace('/\/$/', '', $mainRecord[0]['category']);
					$mainRecord[0]['category'] = preg_replace('/([a-z0-9])(?=[A-Z])/', '$1-$2', $mainRecord[0]['category']);
                    $categoryList = preg_split('/(:|>|<|;|\.|\/|,)/i', $mainRecord[0]['category']);
					$catCount = count($categoryList);					
					
                    echo '<div><u>Category</u>: ';
                    foreach ($categoryList as $i => $category)
                    {
                        $category = trim($category);
                        if (preg_match('/' . $query . '/i', $category))
                        {
                            echo '<a href="' . $matchingUrl . '/query/' . rawurlencode($category) . $cityUrl . '" rel="nolink"><span itemprop="category">' . $category . '</span></a>';
                        }
                        else
                        {
                            echo '<a href="' . $matchingUrl . '/query/' . rawurlencode($category) . '+' . rawurlencode($query) . $cityUrl .  '" rel="nolink"><span itemprop="category">' . $category . '</span></a>';
                        }

                        if ($i < ($catCount - 1))
                        {
                            echo ' > ';
                        }
                    }
                    echo '</div>';
                }
                if($mainRecord[0]['upc'])
                {
                    echo '<div itemprop="identifier" content="upc:' . $mainRecord[0]['upc'] . '" class="prodBrand"><u>UPC</u>: ' . $mainRecord[0]['upc'] . '</div>';
                }
				if($mainRecord[0]['merchant'])
				{
					echo '<div class="prodBrand"><u>Merchant</u>: <a href="' . $matchingUrl . '/merchant/' . rawurlencode($mainRecord[0]['merchant']) . $cityUrl . '" rel="nolink"><span itemprop="seller">' . $mainRecord[0]['merchant'] . '</span></a></div>';
				}
                if($mainRecord[0]['brand'])
                {
                    echo '<div class="prodBrand"><u>Brand</u>: <a href="' . $matchingUrl . '/brand/' . rawurlencode($mainRecord[0]['brand']) . '" rel="nolink"><span itemprop="brand">' . $mainRecord[0]['brand'] . '</span></a></div>';
                }	

				if (count($results) > 1)
				{
					?>
						<table class="productResults" itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">
							<thead>
								<tr>
									<th><strong>Merchant</strong></th>
									<th><strong>Price</strong></th>
									<th></th>
								</tr>
							</thead>
							<?php                
							foreach ($results as $product)
							{								
								$priceSale = $product['priceSale'] ? $product['priceSale'] : $product['price_sale'];
								
								echo '<tr itemscope itemtype="http://data-vocabulary.org/Product">';
								echo '<td itemprop="seller"><a href="' . $matchingUrl . '/merchant/' . rawurlencode($product['merchant']) . '" rel="nolink"><span>' . $product['merchant'] . '</span></a></td>';
								echo '<td itemprop="price">' . ($priceSale ? '<span style="color:#bb0628">$' . number_format($priceSale, 2) . '</span>' :  '$' . number_format($product['price'], 2, '.', ',')) . '</td>';
								echo '<meta itemprop="priceCurrency" content="' . $currency . '"/>';
								echo '<td><div class="shopCheck prosperVisit"><a itemprop="offerURL" href="' . $product['affiliate_url'] . '" target="' . $target . '" rel="nofollow,nolink"><input type="submit" type="submit" class="prosperVisitSubmit" value="' . $visitButton . '"/></a></div></td>';
								echo '</tr>';
							}
							?>
						</table>
					<?php 
				}
				elseif ($mainRecord[0]['priceSale'] || $mainRecord[0]['price_sale'] || $mainRecord[0]['price'])
				{
					$priceSale = $mainRecord[0]['priceSale'] ? $mainRecord[0]['priceSale'] : $mainRecord[0]['price_sale'];
					if(empty($priceSale) || $mainRecord[0]['price'] <= $priceSale)
					{
						echo '<br><div class="prodBrand" style="font-size:24px;"><strong>$' . number_format($mainRecord[0]['price'], 2) . '</strong></div>';
					}
					else
					{
						echo '<br><div class="prodBrand" style="font-size:24px;padding-top:8px;"><strong>$' . number_format($priceSale, 2) . '</strong></div>';
						echo '<div class="prodBrand" style="color:#ED3E30;font-size:18px;">A savings of <strong>$' . ($mainRecord[0]['dollarsOff'] ? $mainRecord[0]['dollarsOff'] : number_format($mainRecord[0]['price'] - $priceSale, 2)) . '!</strong></div>';
					}
				}
				?>
            </div>
        </div>
    </div>
</div>

<?php
$gridImage = ($options['Same_Img_Size'] ? preg_replace('/px|em|%/i', '', $options['Same_Img_Size']) : 200) . 'px';
$classLoad = $gridImage < 120 ? 'class="loadCoup"' : 'class="load"';

if (count($similar) > 1)
{
    echo '<div class="clear"></div>';
    echo '<div class="simTitle">Similar Products</div>';
    echo '<div id="simProd">';
    echo '<ul>';
    foreach ($similar as $prod)
    {
		$priceSale = $prod['priceSale'] ? $prod['priceSale'] : $prod['price_sale'];
        $price 	   = $priceSale ? $priceSale : $prod['price'];
		$keyword   = preg_replace('/\(.+\)/i', '', $prod['keyword']);
		$cid 	   = $prod['catalogId'];
        ?>
            <li style="width:<?php echo $gridImage; ?> !important;">
				<div class="listBlock">
					<div class="prodImage">
						<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $cid; ?>" rel="nolink"><span <?php echo $classLoad . 'style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"'; ?>><img <?php echo 'style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"'; ?> src="<?php echo $prod['image_url']; ?>" alt="<?php echo $prod['keyword']; ?>" title="<?php echo $prod['keyword']; ?>" /></span></a>
					</div>
					<div class="prodContent">
						<div class="prodTitle">
							<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $cid; ?>" rel="nolink">
								<?php echo $keyword; ?>
							</a>
						</div>
						<?php if ($price): ?>
						<div class="prodPrice"><?php echo '$' . number_format($price, 2); ?></div>
						<?php endif; ?>
					</div>
				</div>
				<div class="shopCheck prosperVisit">		
					<a href="<?php echo $prod['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $visitButton; ?>"/></a>				
				</div>
            </li>
        <?php
    }
    echo '</ul>';
    echo '</div>';
}

if (count($sameBrand) > 1)
{
	echo '<div class="clear"></div>';
    echo '<div class="simTitle">Other Products from <a href="' . $matchingUrl . '/brand/' . rawurlencode($mainRecord[0]['brand']) . '" rel="nolink"><span>' . $mainRecord[0]['brand'] . '</span></a></div>';
    echo '<div id="simProd">';
    echo '<ul>';
    foreach ($sameBrand as $brandProd)
    {	
		$priceSale = $brandProd['priceSale'] ? $brandProd['priceSale'] : $brandProd['price_sale'];
        $price 	   = $priceSale ? $priceSale : $brandProd['price'];
		$keyword   = preg_replace('/\(.+\)/i', '', $brandProd['keyword']);
		$cid 	   = $brandProd['catalogId'];
        ?>
            <li style="width:<?php echo $gridImage; ?>!important;">
				<div class="listBlock">
					<div class="prodImage">
						<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $cid; ?>" rel="nolink"><span <?php echo $classLoad . 'style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"'; ?>><img <?php echo 'style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"'; ?> src="<?php echo $brandProd['image_url']; ?>" alt="<?php echo $brandProd['keyword']; ?>" title="<?php echo $brandProd['keyword']; ?>"/></span></a>
					</div>
					<div class="prodContent">
						<div class="prodTitle">
							<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $cid; ?>" rel="nolink">
								<?php echo $keyword; ?>
							</a>
						</div>
						<div class="prodPrice"><?php echo '$' . number_format($price, 2); ?></div>                   
					</div>			
				</div>
				<div class="shopCheck prosperVisit">		
					<a href="<?php echo $brandProd['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $visitButton; ?>"/></a>				
				</div>				
            </li>
        <?php
    }
    echo '</ul>';
    echo '</div>';
}

if (count($sameMerchant) > 1)
{
	echo '<div class="clear"></div>';
    echo '<div class="simTitle">Other ' . ($type === 'coupon' || $type === 'local' ? 'Deals' : 'Products') . ' from <a href="' . $matchingUrl . '/merchant/' . rawurlencode($mainRecord[0]['merchant']) . '" rel="nolink"><span>' . $mainRecord[0]['merchant'] . '</span></a>' . ($type === 'local' ? ' for <a href="' . $matchingUrl . '/state/' . rawurlencode($mainRecord[0]['state']) . '" rel="nolink"><span>' . ucwords($fullState) . '</span></a>' : '') . '</div>';
    echo '<div id="simProd">';
    echo '<ul>';
    foreach ($sameMerchant as $merchantProd)
    {
		$priceSale = $merchantProd['priceSale'] ? $merchantProd['priceSale'] : $merchantProd['price_sale'];
        $price 	   = $priceSale ? $priceSale : $merchantProd['price'];
		$keyword   = preg_replace('/\(.+\)/i', '', $merchantProd['keyword']);
		$cid 	   = $type === 'coupon' ? $merchantProd['couponId'] : ($type === 'local' ? $merchantProd['localId'] : $merchantProd['catalogId']);
        ?>
            <li style="width:<?php echo $gridImage; ?>!important;">
				<div class="listBlock">
					<div class="prodImage">
						<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $merchantProd['keyword'])) . '/cid/' . $cid; ?>" rel="nolink"><span <?php echo $classLoad . 'style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"'; ?>><img <?php echo 'style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"'; ?> src="<?php echo $merchantProd['image_url']; ?>" alt="<?php echo $merchantProd['keyword']; ?>" title="<?php echo $merchantProd['keyword']; ?>"/></span></a>
					</div>					
					<div class="prodContent">
						<div class="prodTitle">
							<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $merchantProd['keyword'])) . '/cid/' . $cid; ?>" rel="nolink">
								<?php echo $keyword; ?>
							</a>
						</div>       
						<?php if ($price): ?>
						<div class="prodPrice"><?php echo '$' . number_format($price, 2); ?></div>
						<?php endif; ?>					
					</div>			
				</div>			
				
				<div class="shopCheck prosperVisit">		
					<a href="<?php echo $merchantProd['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" value="<?php echo $visitButton; ?>"/></a>				
				</div>	
            </li>
        <?php
    }    
	
	echo '</ul>';
    echo '</div>';
}