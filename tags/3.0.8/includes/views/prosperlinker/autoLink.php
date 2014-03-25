<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url)
?>
<html>
    <head>
        <title>Auto-Linker</title>
        <script data-cfasync="false"type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
        <script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
        <script data-cfasync="false" type="text/javascript">
            var AutoLinker = {
                local_ed : 'ed',
                init : function(ed) {
                    AutoLinker.local_ed = ed;
                    tinyMCEPopup.resizeToInnerSize();
                },

                insert : function insertAutoLinkerSection(ed) {
                    var query = jQuery('#query').val();
                    var brand = jQuery('#brand').val();
                    var merchant = jQuery('#merchant').val();
                    var country = jQuery('#country').val();
                    var goTo = jQuery('#goTo:checked').val();
					var prodId = jQuery('#prodid').val();
                    var output = '[linker';

                    // Apply
                    if (query)
                    {
                        output += ' q="'+query+'"';
                    }
                    if (goTo)
                    {
                        output += ' gtm="'+goTo+'"';
                    }
                    if (brand)
                    {
                        output += ' b="'+brand+'"';
                    }
                    if (merchant)
                    {
                        output += ' m="'+merchant+'"';
                    }
                    if (country)
                    {
                        output += ' ct="'+country+'"';
                    }
                    if (prodId)
                    {
                        output += ' id="'+prodId+'"';
                    }
					
                    output += ']' + AutoLinker.local_ed.selection.getContent() + '[/linker]';

                    tinyMCEPopup.execCommand('mceReplaceContent', false, output);

                    // Return
                    tinyMCEPopup.close();
                }
            };
            tinyMCEPopup.onInit.add(AutoLinker.init, AutoLinker);

            document.write('<base href="'+tinymce.baseURL+'" />');

            var t;
            function showValues()
            {
                clearTimeout(t);

                var str = '';

                str = jQuery("form").serialize();

                xmlhttp=new XMLHttpRequest();

                xmlhttp.onreadystatechange=function()
                {
                    jQuery('div.preview').html(xmlhttp.responseText).show();
                }
				
               	var loc = window.location.pathname;
				var dir = loc.substring(0, loc.lastIndexOf('prosperlinker/'));
				var previewUrl = dir + "preview.php?";
                xmlhttp.open("GET",previewUrl + str,true);

                t = setTimeout(function(){
                    try
                    {
                        xmlhttp.send();
                        str = '';
                    }
                    catch(e){}
                }, 500);

                if (!str) clearTimeout(t);
            }

            function setFocus()
            {
                document.getElementById("query").focus();

                if (AutoLinker.local_ed.selection.getContent())
                {
                    document.getElementById("query").value = AutoLinker.local_ed.selection.getContent();
                    showValues();
                }
            }
			
			function getIdofItem(el)
			{
				document.getElementById("prodid").value = el.id;
				showValues(el.id);
			}			
	    </script>

        <style type="text/css">
            #autoLinker {
            }
            a {
                text-decoration: none;
                font-weight: bold;
            }
            #auto_link_submit {
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
        <div id="autoLinker">
            <div align="center">
                <form action="/" method="get" accept-charset="utf-8">
                    <p>Query: <input tabindex="1" type="text" name="q" id="query" style="width:125px" onKeyUp="showValues();"/><a href="#" class="tooltip"><span>You can change the query here, otherwise it will use the content you highlighted, this will not change the content on the page.</span></a></p>
                    <p>Brand: <input tabindex="2" type="text" id="brand" name="brand" style="width:125px" onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Brands</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.(ie. !Nike)</span></a></p>
                    <p>Merchant: <input tabindex="3" type="text" id="merchant" name="merchant" style="width:125px" onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name. (ie. !Zappos.com)</span></a></p>
					<p>Country: <select tabindex="6" id="country" name="country" onChange="showValues();"><option value="US" selected="selected">US</option><option value="UK">UK</option><option value="CA">Canada</option></select><a href="#" class="tooltip"><span>Choose a country to choose the catalog to pull products from.</span></a></p>
					<table style="font-size:13px;">
						<tr>
							<td><strong>Go To:</strong></td>
						</tr>
						<tr>
							<td><input tabindex="2" type="radio" id="goTo" name="goTo" value="merchant" checked="checked"/></td><td>Merchant Page<a href="#" class="tooltip"><span>Checking this will link to the merchant's page.</span></a></td>
						<tr>	
							<td><input type="radio" id="goTo" name="goTo" value="prodPage"/></td><td>Product Page<a href="#" class="tooltip"><span>Checking this will link to the product page of the most relevant product.</span></a></td>
						</tr>
						<tr>					
							<td><input type="radio" id="goTo" name="goTo" value="prodResults" /></td><td>Product Results<a href="#" class="tooltip"><span>Checking this will link to the product results with your query as the search term.</span></a></td>
						</tr>
					</table>					
                    <p>ID: <input tabindex="7" type="text" name="prodid" id="prodid" style="width:150px" readonly="readonly"/><a href="#" class="tooltip"><span>This is set by clicking the product/coupon that you would like to users to go to when clicked.</span></a></p>
					<input tabindex="8" type="submit" value="Submit" class="button-primary" id="auto_link_submit" onClick="javascript:AutoLinker.insert(AutoLinker.local_ed);" style="display:block;"/>					
                </form>
            </div>
			<br>
            <div style="float:left;font-size:12px;display:block;">Product Review:</div><p></br>
			<span style="font-size:10px;"><strong>Note</strong>: Click the Product/Coupon that you would like to be displayed when a user clicks the link. If after you have set an item, it is removed from our catalog, we will use your query and any filters.</span>
            <div class="preview" style="display:none"></div>
        </div>
    </body>
</html>

