<?php
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$options = get_option('prosperSuite');

$prosperAdmin->adminHeader( __( 'General Settings', 'prosperent-suite' ), true, 'prosperent_options', 'prosperSuite' );

echo '<p class="prosper_settingDesc" style="font-size:14px;">' . __( 'Go to <a href="http://wordpress.prosperentdemo.com">WordPress Prosperent Demo</a> for more information and tutorials.', 'prosperent-suite' ) . '</p>';

if (!$options['Api_Key'])
{
    echo __( '<ol><li><a href="http://prosperent.com/join?utm_source=' . urlencode(home_url()) . '&utm_medium=direct&utm_campaign=wp-suite-signup" target="_blank">Sign Up (It\'s free)</a>, if you haven\'t already.</li><li>Go to the <a href="http://prosperent.com/account/wordpress" target="_blank">Prosperent WordPress Install</a> screen.</li><li>Either Create a New Installation or use the Key from a previous setup.</li><li>Copy the Key, and paste it into the box below.</li><li>Save your Settings!</li></ol>', 'prosperent-suite' );
}
echo $prosperAdmin->textinput( 'Api_Key', __( '<strong style="font-size:14px;">Prosperent Key</strong>', 'prosperent-suite' ), '');
echo '<p class="prosper_desc">' . __( '', 'prosperent-suite' ) . '</p><br>';

if ($options['PSAct'] || $options['PICIAct'] || $options['ALAct'])
{
    echo $prosperAdmin->checkbox( 'Target', __( '<strong style="font-size:14px">Open Links in New Window</strong>', 'prosperent-suite' ), true, '', 'Will Not Change ProsperLinks');
    echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

    echo $prosperAdmin->checkbox( 'gotoMerchantBypass', __( '<strong style="font-size:14px">Links Go To Merchant</strong>', 'prosperent-suite' ), true, '', 'Bypasses the Product Page.');
    echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';
    
    echo $prosperAdmin->checkbox( 'Enable_Caching', __( '<strong style="font-size:14px">Caching</strong', 'prosperent-suite' ), true);
    if ($options['Enable_Caching'] &&  extension_loaded('memcache'))
    {
        echo '<a style="margin:10px 0 6px 35px; vertical-align:baseline;" class="button-secondary" href="' . admin_url( 'admin.php?page=prosper_general&clearCache&nonce='. wp_create_nonce( 'prosper_clear_cache' )) . '">' . __( 'Clear Memcache', 'prosperent-suite' ) . '</a>';
    }
    echo '<p class="prosper_desc">' . __( '', 'prosperent-suite' ) . '</p><br>';
}
else
{
    echo $prosperAdmin->hidden( 'Enable_Caching' );
}

echo $prosperAdmin->checkbox( 'autoMinorUpdates', __( '<strong style="font-size:14px">Automatic Minor Updates</strong>', 'prosperent-suite' ), true);
echo '<p class="prosper_desc">' . __( "", 'prosperent-suite' ) . '</p><br>';

echo $prosperAdmin->checkbox( 'anonymousData', __( 'Send Usage Data Back to Us', 'prosperent-suite' ), true);
echo '<p class="prosper_descb">' . __( "This will help us better serve you by knowing which features are used the most and helping with support when needed.", 'prosperent-suite' ) . '</p>';

echo '<h2 class="prosper_h2">' . __( 'Enable/Disable...', 'prosperent-suite' ) . '</h2>';
echo $prosperAdmin->checkbox( 'PSAct', __( '<span style="font-size:18px;line-height:1.2em;">ProsperShop</span>', 'prosperent-suite' ), true );
echo '<p class="prosper_desc" style="padding-bottom:2px;">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'PICIAct', __( '<span style="font-size:18px;line-height:1.2em;">ProsperInsert</span>', 'prosperent-suite' ), true );
echo '<p class="prosper_desc" style="padding-bottom:2px;">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'ALAct', __( '<span style="font-size:18px;line-height:1.2em;">AutoLinker</span>', 'prosperent-suite' ), true );
echo '<p class="prosper_desc" style="padding-bottom:2px;">' . __( "", 'prosperent-suite' ) . '</p>';

echo $prosperAdmin->checkbox( 'PLAct', __( '<span style="font-size:18px;line-height:1.2em;">ProsperLinks</span>', 'prosperent-suite' ), true );
echo '<p class="prosper_desc" style="padding-bottom:2px;">' . __( "", 'prosperent-suite' ) . '</p>';


echo $prosperAdmin->hidden( 'ProsperFirstTimeOperator' );
echo $prosperAdmin->hidden( 'prosperNewVersion' );
echo $prosperAdmin->hidden( 'shortCodesAccessed' );
echo $prosperAdmin->hidden( 'prosperNoOptions' );

$prosperAdmin->adminFooter();