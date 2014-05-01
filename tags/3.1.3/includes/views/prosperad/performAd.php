<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<html>
    <head>
        <title>ProsperAd</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=3.1.3'; ?>">
        <script data-cfasync="false"type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
        <script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=3.1.3'; ?>"></script>
		<script type="text/javascript">function setFocus(){document.getElementById("height").focus()};</script>
    </head>
    <base target="_self" />
    <body onload="setFocus();">
        <form action="/" method="get" id="prosperSCForm">
			<fieldset style="font-size:14px;">
				<legend>Performance Ad</legend>	
				<input type="hidden" name="prosperSC" id="prosperSC" value="performAd"/>
				<p><label>Height:</label><input class="prosperShortTextSC" tabindex="1" type="text" id="height" /><a href="#" class="tooltip"><span>The height of your in content ad unit.</span></a></p>
				<p><label>Width:</label><input class="prosperShortTextSC" tabindex="2" type="text" id="width" /><a href="#" class="tooltip"><span>The width of your in content ad unit. Entering 'auto' or leaving this field blank will make it auto-adjust to your content's width.</span></a></p>				
				<p><label><u><b>Topics</b></u></label></p><br>
				<p><label>Use Tags:</label><input tabindex="4" type="checkbox" id="useTags" checked="checked"/><a href="#" class="tooltip"><span>Adds your page/post tags to the topic list. You can remove common tags under the ProsperAd settings.</span></a></p>
				<p><label>Use Title:</label><input tabindex="5" type="checkbox" id="useTitle" checked="checked"/><a href="#" class="tooltip"><span>Adds your page/post title to the topic list. You can remove common tags under the ProsperAd settings</span></a></p>
				<p><label>Additional:</label><input class="prosperTextSC" tabindex="3" type="text" id="topic"/><a href="#" class="tooltip"><span>Comma seperated list. Max 3 (including title and tags if used). Topics to use for your page's ad.</span></a></p>
				<input tabindex="6" type="submit" value="Submit" class="button-primary" id="prosperMCE_submit" onClick="javascript:shortCode.insert(shortCode.local_ed);"/>					
			</fieldset>
		</form>
    </body>
</html>

