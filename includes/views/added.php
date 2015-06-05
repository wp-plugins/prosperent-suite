<?php
error_reporting(0);  
$params = array_filter($_GET); 

if ($params['prodimages'] && $params['type'] == 'prod')
{
	$images = array_map('trim', explode(',',  rtrim($params[$params['type'] . 'images'], ",")));
	$id = array_map('trim', explode(',',  rtrim($params[$params['type'] . 'id'], ",")));
	$keywords = array_map('trim', explode(',',  rtrim($params['keywords'], ",")));
	echo '<div style="font-weight:bold;">Selected Products - ' . count($images) . '</div>';
	echo '<div id="prosperAddedprod" style="display:block;overflow:auto;height:80px;border:1px solid #919B9C;background-color:gray;padding:2px 0;">';
    	echo '<div id="stickyHeader">';
        	foreach ($images as $i => $image)
        	{
        		echo '<img id="small' . $id[$i] . '" style="width:80px;height:80px;padding:0 2px;" class="smallImage" src="' . $image . '"  alt="' . $keywords[$i] . '" title="' . $keywords[$i] . '" onclick="getIdofItem(this, true)"/>';
        	}
    	echo '</div>';
    echo '</div>';
}

if ($params['merchantimages'] && $params['type'] == 'merchant')
{
    $images = array_map('trim', explode(',',  rtrim($params[$params['type'] . 'images'], ",")));
    $id = array_map('trim', explode(',',  rtrim($params[$params['type'] . 'id'], ",")));
    $keywords = array_map('trim', explode(',',  rtrim($params['keywords'], ",")));
    echo '<div style="font-weight:bold;">Selected Merchants - ' . count($images) . '</div>';
    echo '<div id="prosperAddedmerchant" style="display:block;overflow:auto;height:60px;border:1px solid #919B9C;background-color:gray;padding:2px 0;">';
    echo '<div id="stickyHeader">';
    foreach ($images as $i => $image)
    {
        echo '<img id="small' . $id[$i] . '" style="width:120px;height:60px;padding:0 2px;" class="smallImage" src="' . $image . '"  alt="' . $keywords[$i] . '" title="' . $keywords[$i] . '" onclick="getIdofItem(this, true)"/>';
    }
    echo '</div>';
    echo '</div>';
}