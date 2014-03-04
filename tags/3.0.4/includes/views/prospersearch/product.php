<?php echo $typeSelector; ?>

<div class="prosper_searchform">
    <form class="searchform" method="POST" action="">
        <input class="prosper_field" type="text" name="<?php echo $searchPost ? $searchPost : 'q'; ?>" id="s" placeholder="<?php echo isset($options['Search_Bar_Text']) ? $options['Search_Bar_Text'] : ($searchTitle ? 'Search ' . $searchTitle : 'Search Products'); ?>">
        <input id="submit" class="prosper_submit" type="submit" value="Search">
    </form>
</div>
<?php
if ($filterArray)
{
	?>
	<table id="facets">
		<tr>
			<?php
			$mainCount = count($mainFilters);
			$z = 0;
 			foreach ($mainFilters as $i => $partials)
			{
				$facetDir = $z === 0 ? 'left' : 'right';
				?>
				<td class="<?php echo $facetDir; ?>" style="width:<?php  echo (100 / $mainCount) - 2; ?>%; float:<?php echo $facetDir; ?>">
				<?php
				echo (empty($params[$i]) ? '<div class="browse' .  ucfirst($i) . '">' . ucfirst($i) . ': </div>' : '<div class="filtered' . ucfirst($i) . '">Filtered by: </div>');
				if ((empty($facets[$i]) && !$params[$i]) && $i != 'celebrity')
				{
					if ($i === 'city')
						$i = 'citie';
					echo '<div class="no' .  $i . '">No ' .  $i . 's Found</div>';
				}
				else if (!$params[$i])
				{
					echo implode('&nbsp;&nbsp;<strong>|</strong>  ', $partials);
					
					if (!empty($secondaryFilters[$i]))
					{
						if ($i === 'brand' || $i === 'city')
							$x = 'merchant';
						if ($i === 'merchant' || $i === 'zip')
							$x = 'brand';
						
						?>
						</br>
						<a onclick="toggle_visibility('<?php echo $i; ?>List'); toggle_hidden('<?php echo $x; ?>List'); toggle_hidden('more<?php echo ucfirst($i); ?>s'); toggle_visibility('hide<?php echo ucfirst($i); ?>s'); toggle_hidden('hide<?php echo ucfirst($x); ?>s'); toggle_visibility('more<?php echo ucfirst($x); ?>s'); return false;" style="cursor:pointer; font-size:12px;"><span id="more<?php echo ucfirst($i); ?>s" style="display:block;">More <?php echo ucfirst($i); ?>s <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_down_small.png'; ?>" alt=""/></span></a>
						<a onclick="toggle_hidden('<?php echo $i; ?>List'); toggle_hidden('hide<?php echo ucfirst($i); ?>s'); toggle_visibility('more<?php echo ucfirst($i); ?>s'); return false;" style="cursor:pointer; font-size:12px;"><span id="hide<?php echo ucfirst($i); ?>s" style="display:none;">Hide <?php echo ucfirst($i); ?>s <img class="facetArrows" src="<?php echo PROSPER_IMG . '/arrow_up_small.png'; ?>" alt=""/></span></a>
						<?php	
					}
				}					
				else
				{
					echo '<div style="min-height:35px;">';
					echo rawurldecode($params[$i]);
					echo '</br><a href=' . str_replace(array('/page/' . $params['page'], '/' . $i . '/' . $params[$i]), '', $url) . '>clear filter</a>';
					if ($i === 'brand')
					{
						echo '<div style="margin-top:-50px;padding-left:150px;"><img src="' . ($options['Image_Masking'] ? $homeUrl  . '/img/' . rawurlencode(str_replace('/', ',SL,',  ('brandlogos/120x60/' . $params[$i] . '.png'))) : 'http://img1.prosperent.com/images/brandlogos/120x60/' . $params[$i] . '.png') . '" alt="' . $params[$i] . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
					}
					elseif ($i === 'celebrity')
					{
						echo '<div style="margin-top:-50px;padding-left:150px;"><img src="' . ($options['Image_Masking'] ? $homeUrl  . '/img/' . rawurlencode(str_replace('/', ',SL,',  ('celebrity/100x100/' . $params[$i] . '.png'))) : 'http://img1.prosperent.com/images/celebrity/100x100/' . $params[$i] . '.png') . '" alt="' . $params[$i] . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
					}
					else
					{
						echo '<div style="margin-top:-50px;padding-left:150px;"><img src="' . ($options['Image_Masking'] ? $homeUrl  . '/img/' . rawurlencode(str_replace('/', ',SL,',  ('logos/120x60/' . $params[$i] . '.png'))) : 'http://img1.prosperent.com/images/logos/120x60/' . $params[$i] . '.png') . '" alt="' . $params[$i] . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></div>';
					}
					
					echo '</div>';
				}
				?>
				</td>
				<?php
				$z++;
			}
			?>
			
		</tr>
		<tr>
			<?php
			if($secondaryFilters)
			{
				foreach ($secondaryFilters as $i => $secondaryPartials)
				{
					if (!empty($secondaryFilters[$i])) 
					{ 
						?>
						<td id="<?php echo $i; ?>List" style="display:none; font-size:12px; width:100%;">
							<?php echo implode('&nbsp;&nbsp;<strong>|</strong> &nbsp; ', $secondaryPartials); ?>
						</td>
						<?php 
					}	
				}
			}			
			?>
		</tr>	
	</table>
	<?php
}
else
{
	echo '<div class="table-seperator"></div>';
}

if ($noResults && $params['type'] != 'cele')
{
	echo '<div class="noResults">No Results</div>';

	if ($params['brand'] || $params['merchant'])
	{
		echo '<div class="noResults-secondary">Please try your search again or <a style="text-decoration:none;" href=' . str_replace(array('/merchant/' . $params['merchant'], '/brand/' . $params['brand']), '', $url) . '>clear the filter(s)</a>.</div>';
	}
	else
	{
		echo '<div class="noResults-secondary">Please try your search again.</div>';
	}
	echo '<div class="noResults-padding"></div>';
}
elseif (!$params['celebrity']&& $params['type'] == 'cele')
{
	echo '<div class="noResults-secondary"><strong>Please select a Celebrity above.</strong></div>';
}
?>
<div class="totalFound" style="margin-top:none;"><?php echo (($totalFound > 0 && isset($title)) ? number_format($totalFound) . ' results for <strong>' . preg_replace('/\(.+\)/i', '', $title) : ($newTrendsTitle ? $newTrendsTitle : 'Browse these <strong>' . $trend . '</strong>')); ?></b></div>

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
		<?php echo ($params['sort'] != 'rel' && '' != $params['sort']) ? '<a style="font-size:11px;margin-top:-5px;" href=' . str_replace(array('/page/' . $params['page'], '/sort/' . $params['sort']), '', $url) . '> [x]</a>' : ''; ?>
	</form>
</div>

<div id="views">
	<a href="<?php echo str_replace(array('/view/' . $params['view']), '', $url) . '/view/list'; ?>"><span class="listIcon"></span></a>
	<a href="<?php echo str_replace(array('/view/' . $params['view']), '', $url) . '/view/grid'; ?>"><span class="gridIcon"></span></a>
</div>
<div class="clear"></div>
<?php
$pagedResults = $this->searchModel->prosperPages($results, $params['page']);

if (!$params['view'] || $params['view'] === 'list') 
{ 
	?>
	<div id="productList">
		<?php
		// Loop to return Products and corresponding information
		foreach ($pagedResults as $i => $record)
		{			
			$record['affiliate_url'] = $options['URL_Masking'] ? $homeUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) : $record['affiliate_url'];
			$cid = $type === 'coupon' ? $record['couponId'] : ($type === 'local' ? $record['localId'] : $record['catalogId']);
			?>
			<div class="productBlock">
				<div class="productImage">
					<a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid; ?>"><span <?php echo ($params['type'] === 'coup' ? 'class="loadCoup"' : 'class="load"'); ?>><img src="<?php echo $options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record['image_url'])) : $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
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
							echo '<div class="couponExpire"><span>Expires in ' . $interval . ' days!</span></div>';
						}
						elseif ($interval <= 0)
						{
							echo '<div class="couponExpire"><span>Expires Today!</span></div>';
						}
						else
						{
							echo '<div class="couponExpire"><span>Expires Soon!</span></div>';
						}
					}	
					?>
					<div class="productTitle"><a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid; ?>"><span><?php echo preg_replace('/\(.+\)/i', '', $record['keyword']); ?></span></a></div>
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
					
					if ('Online' === $record['city'] && 'Online' === $record['zipCode'] && !$params['city'])
					{
						echo '<span class="brandIn"><a href="' . str_replace(array('/page/' . $page, '/zip/' . $filterZip), array('', ''), $url) . '/zip/' . rawurlencode($record['zipCode']) . '"><cite>Online Offer</cite></a></span>';
					}
		
					?>				
					<div class="productBrandMerchant">
						<?php
						if($record['brand'])
						{
							echo '<span class="brandIn"><u>Brand</u>: ' . (!$params['brand'] ? '<a href="' . str_replace('/page/' . $params['page'], '', $url) . '/brand/' . rawurlencode($record['brand']) . '"><cite>' . $record['brand'] . '</cite></a>' : $record['brand']) . '</span>';
						}
						if ($record['state'] || ($record['city'] && 'Online' != $record['city']) || ($record['zipCode'] && 'Online' != $record['zipCode'] && !$params['zipCode']))
						{
							$city  = (!$params['city'] ? '<a href="' . str_replace(array('/page/' . $params['page'], '/city/' . $filterCity), array('', ''), $url) . '/city/' . rawurlencode($record['city']) . '"><cite>' . ucwords($record['city']) . '</cite></a>' : $record['city']) . ($record['state'] ? ', ' : '');
							$state = !$params['state'] ? '<a href="' . str_replace(array('/page/' . $params['page'], '/state/' . $filterState), array('', ''), $url) . '/state/' . rawurlencode($record['state']) . '"><cite>' . ucwords($backStates[$record['state']]) . '</cite></a>' : $record['state'];
							$zip   = !$params['zip'] ? ' <a href="' . str_replace(array('/page/' . $params['page'], '/zip/' . $filterZip), array('', ''), $url) . '/zip/' . rawurlencode($record['zipCode']) . '"><cite>' . $record['zipCode'] . '</cite></a>' : $record['zipCode'];
							echo '<span class="brandIn" style="display:inline-block;"><u>Location</u>: ' . $city . $state . $zip . '</span>';
						}
						if($record['merchant'])
						{
							echo '<span class="merchantIn"><u>Merchant</u>: ' . (!$params['merchant'] ? '<a href="' . str_replace('/page/' . $params['page'], '', $url) . '/merchant/' . rawurlencode($record['merchant']) . '"><cite>' . $record['merchant'] . '</cite></a>' : $record['merchant']) . '</span>';
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
							<div class="productPriceNoSale"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $record['price']; ?></span></div>
							<?php
						}
						//otherwise strike-through Price and list the Price_Sale
						else
						{
							?>
							<div class="productPrice"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $record['price']?></span></div>
							<div class="productPriceSale"><span><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $priceSale?></span></div>
							<?php
						}
					}
					?>
					<div class="prosperVisit">
					
						<form action="<?php echo $record['affiliate_url']; ?>" target="<?php echo $target; ?>" method="POST" rel="nofollow">
							<input type="submit" id="submit" value="Visit Store"/>
						</form>
					</div>	
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php 
} 
elseif ($params['view'] === 'grid')
{
	echo '<div id="simProd" style="border-top: 2px solid #ddd;">';
    echo '<ul>';
    foreach ($pagedResults as $record)
    {
		$priceSale = $record['priceSale'] ? $record['priceSale'] : $record['price_sale'];
        $price 	   = $priceSale ? $priceSale : $record['price'];
		$keyword   = preg_replace('/\(.+\)/i', '', $record['keyword']);
		$cid 	   = $type === 'coupon' ? $record['couponId'] : ($type === 'local' ? $record['localId'] : $record['catalogId']);
        ?>
            <li <?php echo ($params['type'] === 'coup' ? 'class="coupBlock"' : ''); ?>>
            <div class="listBlock">
                <div class="prodImage">
                    <a href="<?php echo $homeUrl . '/' . $type . '/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid; ?>"><span <?php echo ($params['type'] === 'coup' ? 'class="loadCoup"' : 'class="load"'); ?>><img <?php echo ($params['type'] != 'coup' && $params['type'] != 'local' ? 'class="gridImg"' : ''); ?> src="<?php echo $options['Image_Masking'] ? $homeUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), $record['image_url'])) : $record['image_url']; ?>"  title="<?php echo $record['keyword']; ?>" alt="<?php echo $record['keyword']; ?>"/></span></a>
                </div>
                <div class="prodContent">
                    <div class="prodTitle">
                        <a href="<?php echo $homeUrl . '/product/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $cid; ?>" >
                            <?php
                            if (strlen($keyword) > 40)
                            {
                                echo substr($keyword, 0, 40) . '...';
                            }
                            else
                            {
                                echo $keyword;
                            }
                            ?>
                        </a>
                    </div>                    
                </div>
				<?php if ($price && $params['type'] != 'coup'): ?>
				<div class="prodPrice"><?php echo ($currency == 'GBP' ? '&pound;' : '$') . $price; ?></div>
				<?php endif; ?>
			</div>
			
            </li>
        <?php
    }
    echo '</ul>';
    echo '</div>';
}

$this->searchModel->prosperPagination($results, $params['page']);
echo '</br>';