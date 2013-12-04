<div id="couponList">
    <?php
    $target = $options['Target'] ? '_blank' : '_self';
    $startUrl = home_url();
	
    // Loop to return coupons and corresponding information
    foreach ($results as $i => $record)
    {
		$goToUrl = ($options['Enable_PPS'] && !$options['Link_to_Merc'] && $options['URL_Masking'] ? '"' . $startUrl . '/coupon/' . rawurlencode(str_replace('/', ',SL,', $record['keyword'])) . '/cid/' . $record['couponId'] . '" rel="nofollow"' : ($options['Enable_PPS'] && $options['Link_to_Merc'] && $options['URL_Masking'] ? '"' . $startUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) . '" rel="nofollow"' : '"' . $record['affiliate_url'] . '" rel="nofollow"'));
		$formGoToUrl = $options['Enable_PPS'] && $options['URL_Masking'] ? $startUrl . '/store/go/' . rawurlencode(str_replace(array('http://prosperent.com/', '/'), array('', ',SL,'), $record['affiliate_url'])) : $record['affiliate_url'];
        $record['image_url'] = $options['Image_Masking'] ? $startUrl  . '/img/'. rawurlencode(str_replace(array('http://img1.prosperent.com/images/', '/'), array('', ',SL,'), preg_replace('/\/250x250\//', '/125x125/', $record['image_url']))) : preg_replace('/\/250x250\//', '/125x125/', $record['image_url']);
        ?>
        <div class="<?php echo $i > 0 ? 'couponBlock' : 'couponBlock0'; ?>">
            <div class="couponImage">
                <?php
                echo '<a href=' . $goToUrl . '><img src="' . $record['image_url'] . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"/></a>';
                ?>
            </div>
            <div class="couponContent">
                <div class="couponTitle">
                    <?php
                    echo '<a href=' . $goToUrl . ' target="' . $target . '">' . $record['keyword'] . '</a>';
                    ?>
                </div>
                <?php
                if(!empty($record['expiration_date']))
                {
                    $expires = strtotime($record['expiration_date']);
                    $today = strtotime(date("Y-m-d"));
                    $interval = abs($expires - $today) / (60*60*24);

                    if ($interval <= 7 && $interval > 0)
                    {
                        echo '<div class="couponExpire"><span>Expires in ' . $interval . ' days!</span></div>';
                    }
                    else
                    {
                        echo '<div class="couponExpire"><span>Expires Soon!</span></div>';
                    }
                }
                ?>
                <div class="couponDescription">
                    <?php
                    echo $record['description'];
                    ?>
                </div>
                <?php
                if ($record['coupon_code'])
                {
                    echo '<div class="couponCode">Coupon Code: <span class="code_cc">' . $record['coupon_code'] . '</span></div>';
                }
                ?>
            </div>
			<div class="couponVisit">
				<form style="margin:0; text-align:center;" method="POST" action="<?php echo $formGoToUrl . '" target="' . $target; ?>" rel="nofollow">
					<input type="submit" value="Visit Store"/>
				</form>
			</div>
        </div>
        <?php
    }
    ?>
</div>
