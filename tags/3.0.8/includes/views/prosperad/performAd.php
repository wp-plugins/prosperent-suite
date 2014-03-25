<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url)
?>
<html>
    <head>
        <title>Content Performance Ad</title>
        <script data-cfasync="false"type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
        <script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
        <script data-cfasync="false" type="text/javascript">
            var PerformAd = {
                local_ed : 'ed',
                init : function(ed) {
                    PerformAd.local_ed = ed;
                    tinyMCEPopup.resizeToInnerSize();
                },

                insert : function insertPerformAdSection(ed) {
                    var height = jQuery('#height').val();
                    var width = jQuery('#width').val();
                    var topic = jQuery('#topic').val();
                    var useTags = jQuery('#useTags').is(':checked');
                    var useTitle = jQuery('#useTitle').is(':checked');
					var output = '[perform_ad';

                    // Apply
                    if (height)
                    {
                        output += ' h="'+height+'"';
                    }
                    if (width && width != 'auto')
                    {
                        output += ' w="'+width+'"';
                    }
					else 
					{
						output += ' w="auto"';
					}	
                    if (topic)
                    {
                        output += ' q="'+topic+'"';
                    }
                    if (useTags)
                    {
                        output += ' utg="'+useTags+'"';
                    }
                    if (useTitle)
                    {
                        output += ' utt="'+useTitle+'"';
                    }
					
                    output += '][/perform_ad]';

                    tinyMCEPopup.execCommand('mceInsertContent', false, output);

                    // Return
                    tinyMCEPopup.close();
                }
            };
            tinyMCEPopup.onInit.add(PerformAd.init, PerformAd);

            document.write('<base href="'+tinymce.baseURL+'" />');

            function setFocus()
            {
                document.getElementById("height").focus();
            }
			
		
	    </script>

        <style type="text/css">
            #performAd {
            }
            a {
                text-decoration: none;
                font-weight: bold;
            }
            #performAd_submit {
                font-size:12px;
                font-family:sans-serif;
                display:inline-block;
                background-color:#21759b;
                background-image:-webkit-gradient(linear,left top,left bottom,from(#2a95c5),to(#21759b));
                background-image:-webkit-linear-gradient(top,#2a95c5,#21759b);
                background-image:-moz-linear-gradient(top,#2a95c5,#21759b);
                background-image:-ms-linear-gradient(top,#2a95c5,#21759b);
                background-image:-o-linear-gradient(top,#2a95c5,#21759b);
                background-image:linear-gradient(to bottom,#2a95c5,#21759b);
                border-color:#21759b;border-bottom-color:#1e6a8d;
                -webkit-box-shadow:inset 0 1px 0 rgba(120,200,230,0.5);
                box-shadow:inset 0 1px 0 rgba(120,200,230,0.5);
                color:#fff;text-decoration:none;
                text-shadow:0 1px 0 rgba(0,0,0,0.1);
                line-height:23px;
                height:24px;
            }
            a.tooltip {
				background:url('../../img/help.png') no-repeat scroll 50% center transparent;
                padding-left:24px;
				width:16px;
				height:16px;
            }
            .tooltip img {
                margin-bottom:-4px;
            }
            a:hover {                              
				
            }
            a.tooltip span {
                display:none;
                padding:2px 3px;
                margin-left:8px;
            }
            a.tooltip:hover span{
                display:block;
                position:absolute;
                right:10px;
                background:#ffffff;
                border:1px solid #cccccc;
                color:#6c6c6c;
                width:195px;
                font-size:11px;
            }
        </style>
    </head>
    <base target="_self" />
    <body onload="setFocus();">
        <div id="performAd">
            <div align="center">
                <form action="/" method="get" accept-charset="utf-8">
                    <p>Height: <input tabindex="1" type="text" name="height" id="height" style="width:125px"/><a href="#" class="tooltip"><span>The height of your in content ad unit.</span></a></p>
                    <p>Width: <input tabindex="2" type="text" id="width" name="width" style="width:125px"/><a href="#" class="tooltip"><span>The width of your in content ad unit. Entering 'auto' or leaving this field blank will make it auto-adjust to your content's width.</span></a></p>
                    <p>Topic: <input tabindex="3" type="text" id="topic" name="topic" style="width:125px"/><a href="#" class="tooltip"><span>Comma seperated list. Max 3 (including title and tags if used). Topics to use for your page's ad.</span></a></p>
					<p>Use Tags as Topic: <input tabindex="4" type="checkbox" id="useTags" checked="checked"/><a href="#" class="tooltip"><span>Adds your page/post tags to the topic list. You can remove common tags under the Performance Ad settings.</span></a></p>
					<p>Use Title as Topic: <input tabindex="5" type="checkbox" id="useTitle" checked="checked"/><a href="#" class="tooltip"><span>Adds your page/post title to the topic list. You can remove common tags under the Performance Ad settings</span></a></p>
					<input tabindex="6" type="submit" value="Submit" class="button-primary" id="performAd_submit" onClick="javascript:PerformAd.insert(PerformAd.local_ed);" style="display:block;"/>					
                </form>
            </div>
        </div>
    </body>
</html>

