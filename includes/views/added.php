<?php
//error_reporting(0);
$params = array_filter($_GET);

$images = explode('~',  rtrim($params[$params['type'] . 'images'], "~"));
$keywords = explode('~',  rtrim($params['keywords'], "~"));
$id = explode('~', rtrim($params[$params['type'] . 'id'], '~'));

if ($params[$params['type'].'images'])
{
    $width = $params['type'] == 'prod' ? '80px': '100px';
    
	echo '<div style="font-weight:bold;">Selected ' . ($params['type'] == 'prod' ? 'Products' : 'Merchants') . ' - ' . count($images) . ($params[$params['type'] . 'notFound'] > 0 ? '<span style="padding-left:12px;color:red">(' . $params[$params['type'] . 'notFound'] .($params['type'] == 'prod' ? ' Product(s)' : ' Merchant(s)') .  ' Not Found)</span>' : '') . '</div>';
	echo '<div id="prosperAdded' . $params['type'] . '" style="display:block;overflow:auto;border:1px solid #919B9C;background-color:gray;padding:2px 0;">';
    	echo '<div id="stickyHeader">';
        	foreach ($images as $i => $image)
        	{
        	    echo '<img id="small' . $id[$i] . '" style="width:' . $width . ';padding:0 2px;" class="smallImage" src="' . $image . '"  alt="' . $keywords[$i] . '" title="' . $keywords[$i] . '" onclick="getIdofItem(this, true)"/>';
        	}
    	echo '</div>';
    echo '</div>';
}