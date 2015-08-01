<?php
//error_reporting(0);
$params = array_filter($_GET);

$type = $params['type'];
$view = $params[$type . 'view'];
$images = explode('~',  rtrim($params[$type . 'images'], "~"));
$keywords = explode('~',  rtrim($params['keywords'], "~"));
$id = explode('~', rtrim($params[$type . 'id'], '~'));

if ($params[$type.'images'])
{
    $width = $type == 'prod' ? '80px': '100px';
    
	echo '<div style="font-weight:bold;">Selected ' . ($type == 'prod' ? 'Product' : 'Merchant') . '(s) - ' . count($images) . ($params[$type . 'notFound'] > 0 ? '<span style="padding-left:12px;color:red">(' . $params[$params['type'] . 'notFound'] . ' Product(s) Not Found)</span>' : '') . '</div>';
	echo '<div id="prosperAdded' . $params['type'] . '" style="display:block;overflow:auto;border:1px solid #919B9C;background-color:#fff;padding:2px 0;">';
    	echo '<div id="stickyHeader">';
        	foreach ($images as $i => $image)
        	{
        	    echo '<img id="small' . $id[$i] . '" style="width:' . $width . ';padding:0 2px;" class="smallImage" src="' . $image . '"  alt="' . $keywords[$i] . '" title="' . $keywords[$i] . '" onclick="getIdofItem(this, true)"/>';
        	}
    	echo '</div>';
    echo '</div>';
}
else 
{
    echo '<div style="font-weight:bold;">Selected ' . ($type == 'prod' ? 'Product' : 'Merchant') . '(s)</span></div>
          <div id="prosperAdded' . $params['type'] . '" style="display:block;overflow:auto;border:1px solid #919B9C;background-color:#fff;padding:2px 0;">
              <div id="stickyHeader" style="margin:5px;font-size:16px">
                  <span id="prosperInsertInstructions">' . ($view == 'pc' ? 'Choose 1 product to get price comparison from multiple merchants.' : 'Choose ' . ($type == 'prod' ? 'products' : 'merchants') .' to include in your widget, or select nothing for default search results.') . '</span>
              </div>
          </div>';
}