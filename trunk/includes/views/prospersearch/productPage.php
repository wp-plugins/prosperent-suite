<div class="prosper_searchform" >
    <form class="searchform" method="POST" action="" rel="nolink">
        <input class="prosper_field" type="text" name="<?php echo $searchPost ? $searchPost : 'q'; ?>" id="s" placeholder="<?php echo isset($options['Search_Bar_Text']) ? $options['Search_Bar_Text'] : 'Search Products'; ?>">
        <input id="submit" class="prosper_submit" type="submit" value="Search">
    </form>
</div>
<div class="backTo" style="display:inline-block;padding-top:4px; color:#00AFF0;font-weight:bold;"><a href="<?php echo $returnUrl; ?>" rel="nolink">&#8592;&nbsp;Return to Search Results</a></div>

<div id="product" itemscope itemtype="http://data-vocabulary.org/Product">
    <div class="productTitle"><a href="<?php echo ($options['URL_Masking'] ? $homeUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $mainRecord[0]['affiliate_url'])) : $mainRecord[0]['affiliate_url']); ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><span itemprop="name"><?php echo preg_replace('/\(.+\)/i', '', $mainRecord[0]['keyword']); ?></span></a></div>
    <div class="productBlock">
		<div class="productImage" style="text-align:center;">
            <a itemprop="offerURL" href="<?php echo ($options['URL_Masking'] ? $homeUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $mainRecord[0]['affiliate_url'])) : $mainRecord[0]['affiliate_url']); ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><img itemprop="image" src="<?php echo ($options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $mainRecord[0]['image_url'])) : $mainRecord[0]['image_url']); ?>" alt="<?php echo $mainRecord[0]['keyword']; ?>" title="<?php echo $mainRecord[0]['keyword']; ?>"/></a>
        	<br>
			<?php
			if (count($results) <= 1 )
			{
				?>
				<div class="prosperVisit">		
					<a itemprop="offerURL" href="<?php echo $options['URL_Masking'] ? $homeUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $mainRecord[0]['affiliate_url'])) : $mainRecord[0]['affiliate_url']; ?>" target="<?php echo $target; ?>" rel="nofollow,nolink"><input type="submit" id="submit" value="Visit Store"/></a>				
				</div>	
			<?php
			}
			?>
		</div>
        <div class="productContent">
            <div class="productDescription" itemprop="description">
				<?php
				if ($mainRecord[0]['expiration_date'] || $mainRecord[0]['expirationDate'])
				{
					$expirationDate = $mainRecord[0]['expirationDate'] ? $mainRecord[0]['expirationDate'] : $mainRecord[0]['expiration_date'];
					$expires = strtotime($expirationDate);
					$today = strtotime(date("Y-m-d"));
					$interval = ($expires - $today) / (60*60*24);
					
					echo '<meta itemprop="priceValidUntil" content="' . $mainRecord[0][0]['expiration_date'] . '"/>';
					if ($interval <= 60 && $interval > 0)
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
                if (strlen($mainRecord[0]['description']) > 200)
                {
                    echo trim(substr($mainRecord[0]['description'], 0, 200));					
					?>
					<span id="moreDesc" style="display:inline-block;">... <a style="cursor:pointer;" onclick="showFullDesc('fullDesc'); hideMoreDesc('moreDesc');">more</a></span><span id="fullDesc" style="display:none;-moz-hyphens:manual;font-style:normal;"><?php echo trim(substr($mainRecord[0]['description'], 200)); ?></span>
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
				$cityUrl = ($mainRecord[0]['city'] ? '/city/' . rawurlencode($mainRecord[0]['city']) : '');
				
				if ($mainRecord[0]['city'] || $mainRecord[0]['state'] || $mainRecord[0]['zip'])
				{
					echo '<div class="prodBrand"><u>Location</u>: <strong><a href="' . $matchingUrl . $cityUrl . '" rel="nolink"><span itemprop="category">' . $mainRecord[0]['city'] . '</span></a>' . ($mainRecord[0]['state'] ? ', ' . '<a href="' . $matchingUrl . '/state/' . rawurlencode($mainRecord[0]['state']) . '"><span itemprop="category">' . $mainRecord[0]['state'] . '</span></a>' : '') . ($mainRecord[0]['zip'] ? ' ' . '<a href="' . $matchingUrl . '/zip/' . rawurlencode($mainRecord[0]['zip']) . '"><span itemprop="category">' . $mainRecord[0]['zip'] . '</span></a>' : '') . '</strong></div>';
				}
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
				if($mainRecord[0]['coupon_code'])
				{
					echo '<div class="prodBrand"><u>Coupon Code</u>: <strong style="font-size:16px;">' . $mainRecord[0]['coupon_code'] . '</strong></div>';
				}	

				if (count($results) > 1)
				{
					?>
						<table class="productResults" itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">
							<thead>
								<tr>
									<th><strong>Store</strong></th>
									<th><strong>Price</strong></th>
									<th></th>
								</tr>
							</thead>
							<?php                
							foreach ($results as $product)
							{
								$product['affiliate_url'] = $options['URL_Masking'] ? $homeUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $product['affiliate_url'])) : $product['affiliate_url'];
								$priceSale = $product['priceSale'] ? $product['priceSale'] : $product['price_sale'];
								
								echo '<tr itemscope itemtype="http://data-vocabulary.org/Product">';
								echo '<td itemprop="seller">' . $product['merchant'] . '</td>';
								echo '<td itemprop="price">' . ($currency == 'GBP' ? '&pound;' : '$') . ($priceSale ? $priceSale : $product['price']) . '</td>';
								echo '<meta itemprop="priceCurrency" content="' . $currency . '"/>';
								echo '<td><div class="prosperVisit"><a itemprop="offerURL" href="' . $product['affiliate_url'] . '" target="' . $target . '" rel="nofollow,nolink"><input type="submit" id="submit" type="submit" class="prosperVisitSubmit" value="Visit Store"/></a></div></td>';
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
						echo '<br><div class="prodBrand" style="font-size:24px;"><strong>' . ($currency == 'GBP' ? '&pound;' : '$') . $mainRecord[0]['price'] . '</strong></div>';
					}
					else
					{
						echo '<br><div class="prodBrand" style="font-size:24px;padding-top:8px;"><strong>' . ($currency == 'GBP' ? '&pound;' : '$') . $priceSale . '</strong></div>';
						echo '<div class="prodBrand" style="color:#ED3E30;font-size:18px;">A savings of <strong>' . ($currency == 'GBP' ? '&pound;' : '$') . ($mainRecord[0]['dollarsOff'] ? $mainRecord[0]['dollarsOff'] : number_format($mainRecord[0]['price'] - $priceSale, 2, '.', '')) . '!</strong></div>';
					}
				}
				?>
            </div>
        </div>
    </div>
</div>

<?php
$gridImage = ($options['Grid_Img_Size'] ? preg_replace('/px|em|%/i', '', $options['Grid_Img_Size']) : 200) . 'px';
$classLoad = ($type === 'coupon' || $gridImage < 120) ? 'class="loadCoup"' : 'class="load"';
if (count($similar) > 1)
{
    echo '<div class="clear"></div>';
    echo '<div class="simTitle">Similar ' . (($type === 'coupon' || $type === 'local') ? 'Deals' : 'Products') . ($type === 'local' ? ' for' . ucwords($fullState) : '') . '</div>';
    echo '<div id="simProd">';
    echo '<ul>';
    foreach ($similar as $prod)
    {
		$priceSale = $prod['priceSale'] ? $prod['priceSale'] : $prod['price_sale'];
        $price 	   = $priceSale ? $priceSale : $prod['price'];
		$keyword   = preg_replace('/\(.+\)/i', '', $prod['keyword']);
		$cid 	   = $type === 'coupon' ? $prod['couponId'] : ($type === 'local' ? $prod['localId'] : $prod['catalogId']);
        ?>
            <li <?php echo ($type === 'coupon' ? 'class="coupBlock"' : 'style="width:' . $gridImage . '!important;"'); ?>>
				<div class="listBlock">
					<div class="prodImage">
						<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $cid; ?>" rel="nolink"><span <?php echo $classLoad . ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px"'); ?>><img <?php echo ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px"'); ?> src="<?php echo ($options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $prod['image_url'])) : $prod['image_url']); ?>" alt="<?php echo $prod['keyword']; ?>" title="<?php echo $prod['keyword']; ?>" /></span></a>
					</div>
					<?php
					if ($prod['promo'])
					{					
						echo '<div class="promo"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $cid . '" rel="nolink">' . $prod['promo'] . '!</a></span></div>';
					}
					elseif($prod['expiration_date'] || $prod['expirationDate'])
					{
						$expirationDate = $prod['expirationDate'] ? $prod['expirationDate'] : $prod['expiration_date'];
						$expires = strtotime($expirationDate);
						$today = strtotime(date("Y-m-d"));
						$interval = ($expires - $today) / (60*60*24);

						if ($interval <= 20 && $interval > 0)
						{
							echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $cid . '" rel="nolink">' . $interval . ' Day' . ($interval > 1 ? 's' : '') . ' Left!</a></span></div>';
						}
						elseif ($interval <= 0)
						{
							echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $cid . '" rel="nolink">Ends Today!</a></span></div>';
						}
						else
						{
							echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $cid . '" rel="nolink">Expires Soon!</a></span></div>';
						}
					}
					?>
					<div class="prodContent">
						<div class="prodTitle">
							<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $prod['keyword'])) . '/cid/' . $cid; ?>" rel="nolink">
								<?php echo $keyword; ?>
							</a>
						</div>
						<?php if ($price && $type != 'coupon' && $type != 'local'): ?>
						<div class="prodPrice"><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $price; ?></div>
						<?php endif; ?>
					</div>
				</div>
				<div class="prosperVisit">					
					<form action="<?php echo $prod['affiliate_url']; ?>" target="<?php echo $target; ?>" method="POST" rel="nofollow,nolink">
						<input type="submit" id="submit" value="Visit Store"/>
					</form>
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
    echo '<div class="simTitle">Other Products from ' . $mainRecord[0]['brand'] . '</div>';
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
						<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $cid; ?>" rel="nolink"><span <?php echo $classLoad . 'style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"'; ?>><img <?php echo 'style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"'; ?> src="<?php echo ($options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $brandProd['image_url'])) : $brandProd['image_url']); ?>" alt="<?php echo $brandProd['keyword']; ?>" title="<?php echo $brandProd['keyword']; ?>"/></span></a>
					</div>
					<div class="prodContent">
						<div class="prodTitle">
							<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $brandProd['keyword'])) . '/cid/' . $cid; ?>" rel="nolink">
								<?php echo $keyword; ?>
							</a>
						</div>
						<div class="prodPrice"><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $price; ?></div>                   
					</div>			
				</div>
				<div class="prosperVisit">					
					<form action="<?php echo $brandProd['affiliate_url']; ?>" target="<?php echo $target; ?>" method="POST" rel="nofollow,nolink">
						<input type="submit" id="submit" value="Visit Store"/>
					</form>
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
    echo '<div class="simTitle">Other ' . ($type === 'coupon' || $type === 'local' ? 'Deals' : 'Products') . ' from ' . $mainRecord[0]['merchant'] . ($type === 'local' ? ' for ' . ucwords($fullState) : '') . '</div>';
    echo '<div id="simProd">';
    echo '<ul>';
    foreach ($sameMerchant as $merchantProd)
    {
		$priceSale = $merchantProd['priceSale'] ? $merchantProd['priceSale'] : $merchantProd['price_sale'];
        $price 	   = $priceSale ? $priceSale : $merchantProd['price'];
		$keyword   = preg_replace('/\(.+\)/i', '', $merchantProd['keyword']);
		$cid 	   = $type === 'coupon' ? $merchantProd['couponId'] : ($type === 'local' ? $merchantProd['localId'] : $merchantProd['catalogId']);
        ?>
            <li <?php echo ($type === 'coupon' ? 'class="coupBlock"' : 'style="width:' . $gridImage . '!important;"'); ?>>
				<div class="listBlock">
					<div class="prodImage">
						<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $merchantProd['keyword'])) . '/cid/' . $cid; ?>" rel="nolink"><span <?php echo $classLoad . ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px"'); ?>><img <?php echo ($type != 'coupon' ? ('style="width:' . $gridImage . '!important; height:' . $gridImage . '!important;"') : 'style="height:60px;width:120px""'); ?> src="<?php echo ($options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $merchantProd['image_url'])) : $merchantProd['image_url']); ?>" alt="<?php echo $merchantProd['keyword']; ?>" title="<?php echo $merchantProd['keyword']; ?>"/></span></a>
					</div>
					<?php
					if ($record['promo'])
					{					
						echo '<div class="promo"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $merchantProd['keyword'])) . '/cid/' . $cid . '" rel="nolink">' . $merchantProd['promo'] . '!</a></span></div>';
					}
					elseif($merchantProd['expiration_date'] || $merchantProd['expirationDate'])
					{
						$expirationDate = $merchantProd['expirationDate'] ? $merchantProd['expirationDate'] : $merchantProd['expiration_date'];
						$expires = strtotime($expirationDate);
						$today = strtotime(date("Y-m-d"));
						$interval = ($expires - $today) / (60*60*24);

						if ($interval <= 20 && $interval > 0)
						{
							echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $merchantProd['keyword'])) . '/cid/' . $cid . '" rel="nolink">' . $interval . ' Day' . ($interval > 1 ? 's' : '') . ' Left!</a></span></div>';
						}
						elseif ($interval <= 0)
						{
							echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $merchantProd['keyword'])) . '/cid/' . $cid . '" rel="nolink">Ends Today!</a></span></div>';
						}
						else
						{
							echo '<div class="couponExpire"><span><a href="' . $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $merchantProd['keyword'])) . '/cid/' . $cid . '" rel="nolink">Expires Soon!</a></span></div>';
						}
					}
					?>
					<div class="prodContent">
						<div class="prodTitle">
							<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $merchantProd['keyword'])) . '/cid/' . $cid; ?>" rel="nolink">
								<?php echo $keyword; ?>
							</a>
						</div>       
						<?php if ($price && $type != 'coupon' && $type != 'local'): ?>
						<div class="prodPrice"><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $price; ?></div>
						<?php endif; ?>					
					</div>			
				</div>
				
				<div class="prosperVisit">					
					<form action="<?php echo $merchantProd['affiliate_url']; ?>" target="<?php echo $target; ?>" method="POST" rel="nofollow,nolink">
						<input type="submit" id="submit" value="Visit Store"/>
					</form>
				</div>	
            </li>
        <?php
    }
    echo '</ul>';
    echo '</div>';
}