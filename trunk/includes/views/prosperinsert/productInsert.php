<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
?>
<html>
    <head>
        <title>Product/Coupon Insert</title>
        <script data-cfasync="false"type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
        <script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
        <script data-cfasync="false" type="text/javascript">
            var AutoCompare = {
                local_ed : 'ed',
                init : function(ed) {
                    AutoCompare.local_ed = ed;
                    tinyMCEPopup.resizeToInnerSize();
                },

                insert : function insertAutoCompareSection(ed) {
                    var query = jQuery('#query').val();                    
                    var merchant = jQuery('#merchant').val();
                    var limit = jQuery('#limit').val();
                    var country = jQuery('#country').val();
					var view = jQuery('#view').val();
                    var goToMerc = jQuery('#goToMerc').is(':checked');
					var coup = jQuery('#coup').is(':checked');			
					var brand = jQuery('#brand').val();					
					var prodId = jQuery('#prodid').val();
                    var output = '[compare';

                    // Apply
                    if (query)
                    {
                        output += ' q="'+query+'"';
                    }
					if (goToMerc)
                    {
                        output += ' gtm="'+goToMerc+'"';
                    }
                    if (coup)
                    {
                        output += ' c="'+coup+'"';
                    }
                    if (brand && !coup)
                    {
                        output += ' b="'+brand+'"';
                    }
                    if (merchant)
                    {
                        output += ' m="'+merchant+'"';
                    }
                    if (limit)
                    {
                        output += ' l="'+limit+'"';
                    }
                    if (country)
                    {
                        output += ' ct="'+country+'"';
                    }
					if (view)
                    {
                        output += ' v="'+view+'"';
                    }
					if (prodId)
                    {
                        output += ' id="'+prodId+'"';
                    }
					
                    output += ']' + AutoCompare.local_ed.selection.getContent() + '[/compare]';

                    tinyMCEPopup.execCommand('mceReplaceContent', false, output);

                    // Return
                    tinyMCEPopup.close();
                }
            };
            tinyMCEPopup.onInit.add(AutoCompare.init, AutoCompare);

            document.write('<base href="'+tinymce.baseURL+'" />');

            var t;
            function showValues()
            {
				var coup = jQuery('#coup').is(':checked');		
				jQuery('#brand').prop('disabled', coup);
				
                clearTimeout(t);

                var str = '';

                str = jQuery("form").serialize();

                xmlhttp=new XMLHttpRequest();

                xmlhttp.onreadystatechange=function()
                {
                    jQuery('div.preview').html(xmlhttp.responseText).show();
                }

				var loc = window.location.pathname;
				var dir = loc.substring(0, loc.lastIndexOf('prosperinsert/'));
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

                if (AutoCompare.local_ed.selection.getContent())
                {
                    document.getElementById("query").value = AutoCompare.local_ed.selection.getContent();
                    showValues();
                }
            }
			
			function getIdofItem(el)
			{
				document.getElementById("prodid").value = el.id;
				showValues(el.id);
			}	
        </script>

        <style>
            #autoCompare {
            }
            a {
                text-decoration: none;
                font-weight: bold;
            }
            #auto_compare_submit {
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
        <div id="autoCompare">
            <div align="center"> 
                <form action="/" method="get" accept-charset="utf-8">
                    <p>Query: <input tabindex="1" type="text" name="q" id="query" style="width:125px" onKeyUp="showValues();"/><a href="#" class="tooltip"><span>The query that is  used for the search</span></a></p>
					<p>Merchant: <input tabindex="2" type="text" id="merchant" name="merchant" style="width:125px" onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name.</span></a></p>
                    <p>Brand: <input tabindex="3" type="text" id="brand" name="brand" style="width:125px" onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.</span></a></p>                    
					<p>Country: <select tabindex="4" id="country" name="country" onChange="showValues();"><option value="US" selected="selected">US</option><option value="UK">UK</option><option value="CA">Canada</option></select><a href="#" class="tooltip"><span>Choose a country to choose the catalog to pull products from.</span></a></p>
                    <p>Limit: <input tabindex="5" type="text" id="limit" style="width:50px" onKeyUp="showValues();"/><a href="#" class="tooltip"><span>This limit will be used for coupons and non-comparison products, defaults to 1</span></a></p>                    
                    <p>Grid View<input tabindex="6" style="padding-left:4px;" type="radio" name="view" value="grid" id="view" checked/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;List View<input tabindex="7" type="radio" name="view" value="list" id="view"/><a href="#" class="tooltip"><span>Checking this will use coupons instead of products</span></a></p>
					<p>Go Directly to Merchant: <input tabindex="8" type="checkbox" id="goToMerc" checked="checked"/><a href="#" class="tooltip"><span>Checking this will link to the merchant's page, skipping the product page for all links.</span></a></p>
					<p>Use Coupons: <input tabindex="9" type="checkbox" name="coup" id="coup" onChange="showValues();"/><a href="#" class="tooltip"><span>Checking this will use coupons instead of products</span></a></p>
                    <p>ID: <input type="text" name="prodid" id="prodid" style="width:150px" readonly="readonly"/><a href="#" class="tooltip"><span>This is set by clicking the product/coupon below that you would like to users to go to.</span></a></p>
					<input tabindex="10" type="submit" value="Submit" class="button-primary" id="auto_compare_submit" onClick="javascript:AutoCompare.insert(AutoCompare.local_ed);"/>
                </form>
            </div>
            <div style="float:left;font-size:12px;display:block;">Product Review:</div><p></br>
			<span style="font-size:10px;"><strong>Note</strong>: Click the Product/Coupon that you would like to be displayed when a user clicks the link.</span>
            <div class="preview" style="display:none"></div>
        </div>
    </body>
</html>

