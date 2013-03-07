<div id="couponList">
    <?php
    $target = $options['Target'] ? '_blank' : '_self';

    // Loop to return coupons and corresponding information
    foreach ($results as $record)
    {
        ?>
        <div class="couponBlock">
            <div class="couponImage">
                <?php
                echo '<a href="' . $record['affiliate_url'] . '" target="' . $target . '"><img src="' . $record['image_url'] . '" style="background: none repeat scroll 0 0 transparent; border: medium none;"></a>';
                ?>
            </div>
            <div class="couponContent">
                <div class="couponTitle">
                    <?php
                    echo '<a href="' . $record['affiliate_url'] . '" target="' . $target . '" class="prosperent-kw">' . $record['keyword'] . '</a>';
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
                <form style="margin:0;" action="<?php echo $record['affiliate_url'] . '" target="' . $target; ?>">
                    <input type="submit" value="Visit Store">
                </form>
            </div>
        </div>
        <?php
    }
    ?>
</div>
