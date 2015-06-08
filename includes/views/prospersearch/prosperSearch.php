<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<html>
	<head>
		<title>ProsperShop Search Bar</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=3.3.3'; ?>">
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=3.3.3'; ?>"></script>
		<script type="text/javascript">function setFocus(){document.getElementById("width").focus()};</script>
	</head>
	<base target="_self" />
	<body onload="setFocus()">
		<form action="/" method="get" id="prosperSCForm">
			<input type="hidden" id="prosperSC" value="prosper_search"/>
				<p><label class="secondaryLabels" style="width:140px;">Placeholder Text:</label><input class="prosperMainTextSC" style="width:70%" tabindex="3" type="text" id="sBarText" name="sBarText" "/></p>
				<p><label class="secondaryLabels" style="width:140px;">Button Text:</label><input class="prosperMainTextSC" style="width:70%" tabindex="3" type="text" id="sButtonText" name="sButtonText" "/></p>
				<p>
					<label class="secondaryLabels" style="width:140px;">Width:</label><input style="font-size:14px;width:75px;" tabindex="1" type="text" name="w" id="width" />
					<span style="font-size:16px">
						<input tabindex="2" type="radio" id="widthStyle" name="widthStyle" value="%" /><strong>%</strong>
						<input style="margin-left:4px" type="radio" id="widthStyle" name="widthStyle" value="px" checked="checked"/><strong>px</strong>
						<input style="margin-left:4px" type="radio" id="widthStyle" name="widthStyle" value="em" /><strong>em</strong>
					</span>					
				</p>
				<p><label class="secondaryLabels" style="width:140px;">CSS Rules:</label><input class="prosperMainTextSC" style="font-size:14px;width:70%" tabindex="3" type="text" id="css" name="css" /></p>
				<input tabindex="4" type="submit" value="Submit" class="button-primary" id="prosperMCE_submit" style="float:right;" onClick="javascript:shortCode.insert(shortCode.local_ed)"/>
		</form>
	</body>
</html>