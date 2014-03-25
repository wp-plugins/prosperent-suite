<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url)
?>
<html>
    <head>
        <title>Product Search Bar</title>
        <script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
        <script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
        <script data-cfasync="false" type="text/javascript">
            var ProsperSearch = {
                local_ed : 'ed',
                init : function(ed) {
                    ProsperSearch.local_ed = ed;
                    tinyMCEPopup.resizeToInnerSize();
                },

                insert : function insertAutoCompareSection(ed) {
                    var width = jQuery('#width').val();
					var widthStyle = jQuery('#widthStyle:checked').val();
                    var css = jQuery('#css').val();
                    var output = '[prosper_search';

                    // Apply
                    if (width)
                    {
                        output += ' w="'+width+'"';
                    }
					if (widthStyle)
                    {
                        output += ' ws="'+widthStyle+'"';
                    }
                    if (css)
                    {
                        output += ' css="'+css+'"';
                    }
					
                    output += '][/prosper_search]';

                    tinyMCEPopup.execCommand('mceReplaceContent', false, output);

                    // Return
                    tinyMCEPopup.close();
                }
            };
            tinyMCEPopup.onInit.add(ProsperSearch.init, ProsperSearch);

            document.write('<base href="'+tinymce.baseURL+'" />');

            function setFocus()
            {
                document.getElementById("width").focus();
            }
        </script>

        <style>
            #ProsperSearch {
            }
            a {
                text-decoration: none;
                font-weight: bold;
            }
            #prosper_search_submit {
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
        <div id="prosperSearch">
            <div align="center">
                <form action="/" method="get" accept-charset="utf-8">
                    <p>Width: <input tabindex="1" type="text" name="w" id="width" style="width:125px"/><a href="#" class="tooltip"><span>The width of the search bar. Select a button below to choose the ending for the width, (%, px, or em).</span></a></p>
					<p>
					<div style="text-align:center;">
						<input tabindex="2" type="radio" id="widthStyle" name="widthStyle" value="%" /> <strong>%</strong>
						<input style="margin-left:8px;" type="radio" id="widthStyle" name="widthStyle" value="px" checked="checked"/> <strong>px</strong>
						<input style="margin-left:8px;" type="radio" id="widthStyle" name="widthStyle" value="em" /> <strong>em</strong>
					</div>					
					</p>
                    <p>CSS: <input tabindex="3" type="text" id="css" name="css" style="width:125px""/><a href="#" class="tooltip"><span><strong>Use CSS standards</strong></span></a></p>                    
					<input tabindex="4" type="submit" value="Submit" class="button-primary" id="prosper_search_submit" onClick="javascript:ProsperSearch.insert(ProsperSearch.local_ed);"/>
                </form>
            </div>
        </div>
    </body>
</html>

