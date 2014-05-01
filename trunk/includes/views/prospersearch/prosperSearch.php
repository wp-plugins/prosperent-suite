<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<html>
    <head>
        <title>ProsperShop Search Bar</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=3.1.3'; ?>">
        <script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
        <script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=3.1.3'; ?>"></script>
		<script type="text/javascript">function setFocus(){document.getElementById("width").focus()};</script>
    </head>
    <base target="_self" />
    <body onload="setFocus();">
		<form action="/" method="get" id="prosperSCForm">
			<input type="hidden" id="prosperSC" value="prosper_search"/>
			<fieldset style="font-size:14px;">
				<legend>Search Bar</legend>	
				<p>
					<label class="shortLabel">Width:</label><input class="prosperShortTextSC" tabindex="1" type="text" name="w" id="width" />
					<span style="font-size:12px;">
						<input tabindex="2" type="radio" id="widthStyle" name="widthStyle" value="%" /><strong>%</strong>
						<input style="margin-left:4px;" type="radio" id="widthStyle" name="widthStyle" value="px" checked="checked"/><strong>px</strong>
						<input style="margin-left:4px;" type="radio" id="widthStyle" name="widthStyle" value="em" /><strong>em</strong>
					</span>
					<a href="#" class="tooltip"><span>The width of the search bar. Select a format to use for the width (%, px, or em).</span></a>
				</p>
				<p><label class="shortLabel">CSS:</label><input class="prosperTextSC" tabindex="3" type="text" id="css" name="css" "/><a href="#" class="tooltip"><span><strong>Use CSS standards</strong></span></a></p>                    
				<input tabindex="4" type="submit" value="Submit" class="button-primary" id="prosperMCE_submit" onClick="javascript:shortCode.insert(shortCode.local_ed);"/>
			</fieldset>
		</form>
    </body>
</html>

