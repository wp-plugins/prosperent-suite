<?php
error_reporting(0);  
$params = array_filter($_GET); 
if ($params['images'])
{
	$images = array_map('trim', explode(',',  rtrim($params['images'], ",")));
	$id = array_map('trim', explode(',',  rtrim($params[$params['type'] . 'id'], ",")));
	$keywords = array_map('trim', explode(',',  rtrim($params['keywords'], ",")));

	echo '<div id="stickyHeader">';
	echo '<h3>Added Items - ' . count($images) . '</h3>'; 
	echo '<span style="font-size:10px;">Click to remove an item from the list.</span><br>';
	foreach ($images as $i => $image)
	{
		echo '<img id="small' . $id[$i] . '" style="width:50px;height:50px;padding:0 1px;" class="smallImage" src="' . $image . '"  alt="' . $keywords[$i] . '" title="' . $keywords[$i] . '" onclick="getIdofItem(this, true)"/>';
	}
	echo '</div>';
}