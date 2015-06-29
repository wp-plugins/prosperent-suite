<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<html>
    <head>
        <title>Link Phrase/Image to a Product</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=4.1'; ?>">
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/utils/mctabs.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=4.43322224.1'; ?>"></script>
		<script type="text/javascript">		
		jQuery(function(){var a=top.tinymce.activeEditor.windowManager.getParams();if(a){var c="fetchProducts"==jQuery("<i "+a+">").attr("ft")?"prod":"merchant",b=jQuery("<i "+a+">").attr("id"),f=jQuery("<i "+a+">").attr("mid"),g=jQuery("<i "+a+">").attr("b"),h=jQuery("<i "+a+">").attr("sale"),k=jQuery("<i "+a+">").attr("pr"),l=jQuery("<i "+a+">").attr("po"),m=jQuery("<i "+a+">").attr("q"),a=jQuery("<i "+a+">").attr("gtm");"prod"!=c&&(jQuery("#products_tab").removeClass("current"),jQuery("#merchant_tab").addClass("current"),
				jQuery("#products_panel").removeClass("current"),jQuery("#merchant_panel").addClass("current"));"undefined"!=typeof f&&null!==f&&(document.getElementById(c+"d").value=f);"undefined"!=typeof m&&null!==m&&(document.getElementById(c+"query").value=m);"undefined"!=typeof g&&null!==g&&(document.getElementById(c+"b").value=g);"undefined"!=typeof k&&null!==k&&(b=k.split(","),document.getElementById("pricerangea").value=b[0],document.getElementById("pricerangeb").value=b[1]);"undefined"!=typeof h&&null!==
				h&&jQuery("input[name=onSale][value="+h+"]").attr("checked",!0);"undefined"!=typeof l&&null!==l&&(h=l.split(","),document.getElementById("percentrangea").value=h[0],document.getElementById("percentrangeb").value=h[1]);"undefined"!=typeof a&&null!==a&&jQuery("input[name="+c+"goTo][value="+a+"]").attr("checked",!0)}c=950>jQuery(window).height()?600:750;jQuery("#prodresultsGoHere").css("height",600==c?"345px":"500px");jQuery("#merchantresultsGoHere").css("height",600==c?"345px":"500px");jQuery(window).keydown(function(b){if(13==
				b.keyCode)return b.preventDefault(),!1})});var t;function showValues(){var a=getNewCurrent();clearTimeout(t);var c="",c=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div."+a+"preview").html(xmlhttp.responseText).show()};var b=window.location.pathname,b=b.substring(0,b.lastIndexOf("prosperlinker/"))+"preview.php?type="+a+"&";xmlhttp.open("GET",b+c,!0);t=setTimeout(function(){try{xmlhttp.send(),c=""}catch(b){}},500);c||clearTimeout(t)}
				function setFocus(){"prod"==getNewCurrent()?document.getElementById("prodquery").focus():document.getElementById("merchantquery").focus();top.tinymce.activeEditor.windowManager.getParams()||shortCode.local_ed.selection.getContent()&&!shortCode.local_ed.selection.getContent().match(/(<([^>]+)>)/ig)&&(document.getElementById("prodquery").value=shortCode.local_ed.selection.getContent()?shortCode.local_ed.selection.getContent():"shoes",document.getElementById("merchantquery").value=shortCode.local_ed.selection.getContent()?
				shortCode.local_ed.selection.getContent():"Backcountry",showValues())}function getIdofItem(a,c){var b=getNewCurrent(),f=a.id;0<=document.getElementById(b+"id").value.indexOf(f)?(jQuery("#"+f).removeClass("highlight"),f=document.getElementById(b+"id").value.replace(f,""),document.getElementById(b+"id").value=f):(document.getElementById(b+"id").value=f,jQuery("#productList li").removeClass("highlight"),jQuery("li#"+f).addClass("highlight"))}
				function getFilters(){var a=jQuery("#prodd").val()?jQuery("#prodd").val().replace(",","|"):"",c=jQuery("#prodb").val()?jQuery("#prodb").val().replace(",","|"):"";pRange=(jQuery("#pricerangea").val()?jQuery("#pricerangea").val()+",":"0.01,")+(jQuery("#pricerangeb").val()?jQuery("#pricerangeb").val():"");perRange=jQuery("#onSale:checked").val()?"1,":(jQuery("#percentrangea").val()?jQuery("#percentrangea").val()+",":"")+(jQuery("#percentrangeb").val()?jQuery("#percentrangeb").val():"");jQuery.ajax({type:"POST",
				url:"http://api.prosperent.com/api/search",data:{api_key:"fc91d36b383ca0231ee59c5048eabedc",query:jQuery("#prodquery").val(),filterBrand:c,filterPrice:pRange,filterPercentOff:perRange,limit:1,enableFacets:"merchantId|merchant",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(b){jQuery("#prodmerchant").empty();jQuery.each(b.facets.merchantId,function(c,g){a.match(g.value)?jQuery("#prodmerchant").append('<li id="d'+g.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+
				b.facets.merchant[c].value+"</span></a></li>"):jQuery("#prodmerchant").append('<li id="d'+g.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+b.facets.merchant[c].value+"</span></a></li>")})},error:function(){alert("Failed to load data.")}});jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:"fc91d36b383ca0231ee59c5048eabedc",query:jQuery("#prodquery").val(),filterMerchantId:a,filterBrand:c,filterPrice:pRange,filterPercentOff:perRange,
				limit:1,enableFacets:"brand",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(b){jQuery("#prodbrand").empty();jQuery.each(b.facets.brand,function(b,a){"undefined"!=typeof a.value&&null!==a.value&&0<a.value.length&&(c.match(a.value)?jQuery("#prodbrand").append('<li id="b'+a.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.value+"</span></a></li>"):jQuery("#prodbrand").append('<li id="b'+a.value+
				'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.value+"</span></a></li>"))})},error:function(){alert("Failed to load data.")}})}
				function getIdValue(a,c){var b=a.id;d=b.slice(0,1);e=b.slice(1);0<=document.getElementById("prod"+d).value.indexOf(e+",")?(jQuery("#"+b).removeClass("activeFilter"),b=document.getElementById("prod"+d).value.replace(e+",",""),document.getElementById("prod"+d).value=b):(document.getElementById("prod"+d).value+=e+",",jQuery("#"+b).addClass("activeFilter"));showValues()};
		</script>
    </head>
    <base target="_self" />
    <body id="linker" role="application" aria-labelledby="app_label" onload="setFocus();showValues();getFilters();">
		<form action="/" method="get" id="prosperSCForm">
            <div id="mainFormDiv" style="display:block;position:relative;z-index:1;width:100%;">
                <input type="hidden" id="prosperSC" name="prosperSC" value="linker"/>
			    <div class="tabs">
					<ul>
						<li id="products_tab" aria-controls="products_panel" class="current"><span><a href="javascript:;" onClick="mcTabs.displayTab('products_tab','products_panel');setFocus();showValues();getFilters();" onmousedown="return false;">Products</a></span></li>						
						<li id="merchant_tab" aria-controls="merchant_panel"><span><a href="javascript:;" onClick="mcTabs.displayTab('merchant_tab','merchant_panel');setFocus();showValues();" onmousedown="return false;">Merchants</a></span></li>
					</ul>
				</div>	
				
				<div class="panel_wrapper" style="padding: 5px 10px;">
					<div id="products_panel" class="panel current">
						<input type="hidden" id="prodid" name="prodid"/>
						<input type="hidden" id="prodd" name="prodd"/>
						<input type="hidden" id="prodb" name="prodb"/>
						<input type="hidden" name="prodfetch" id="prodfetch" value="fetchProducts"/>						
						<label><strong>Search Products:</strong></label><input class="prosperMainTextSC" tabindex="1" type="text" name="prodq" id="prodquery" onKeyUp="showValues();" onBlur="getFilters();" value="shoes" placeholder="shoes"/>						
						<table>
    						<tr>
        						<td><div style="display:block;padding-right:10px;"><label class="secondaryLabels" style="width:70px;">Merchant:</label><ul style="max-height:125px;" class="prosperSelect" id="prodmerchant"></ul></div></td>
                                <td><div style="display:block;padding-right:10px;"><label class="secondaryLabels" style="width:50px;">Brand:</label><ul style="max-height:125px;" class="prosperSelect" id="prodbrand"></ul></div></td>						
                                <td style="width:345px;vertical-align:middle;">
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">Price Range:</label>$<input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangea" name="pricerangea" onKeyUp="showValues();"/>&nbsp;&nbsp;to&nbsp;&nbsp;$<input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangeb" name="pricerangeb" onKeyUp="showValues();"/></span>														
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">Percent Off Range:</label><input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangea" name="percentrangea" onKeyUp="showValues();"/>%&nbsp;to&nbsp;&nbsp;&nbsp;<input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangeb" name="percentrangeb" onKeyUp="showValues();"/>%</span>
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">On Sale Only:</label><input tabindex="6" type="checkbox" id="onSale" name="onSale" onClick="showValues();"/></span>
                                    <div style="display:block;"><label class="secondaryLabels" style="width:140px;">Go To:</label><span style="display:inline-block;margin-top:6px;font-size:14px"><input tabindex="9" class="viewRadioSC" type="radio" value="merchant" name="prodgoTo" id="prodgoTo" checked="checked"/>Merchant<input tabindex="10" type="radio" value="prodPage" name="prodgoTo" id="prodgoTo"/>Product Page</span></div>
                                </td>
                            </tr>
						</table>
						<div id="prodresultsGoHere" class="mceActionPanel" style="overflow:auto;display:block;border:1px solid #919B9C;background-color:gray;">  				
            				<div class="prodpreview" aria-required="true" style="overflow:auto;"></div>    						        
            			</div>           			
					</div>
					<div id="merchant_panel" class="panel">				
						<input type="hidden" id="merchantid" name="merchantid"/>	
						<input type="hidden" name="merchantfetch" id="merchantfetch" value="fetchMerchant"/>															
						<div style="margin-bottom:4px;"><label><strong>Search By Merchant:</strong></label><input class="prosperMainTextSC" value="Backcountry" tabindex="5" type="text" id="merchantquery" name="merchantm"  onKeyUp="showValues();"/></div>
						<div style="margin-bottom:4px;"><label><strong>Search By Category:</strong></label><input class="prosperMainTextSC" tabindex="5" type="text" id="merchantcategory" name="merchantcat"  onKeyUp="showValues();"/></div>
						<div style="display:block;margin-bottom:4px;"><label class="secondaryLabels" style="width:140px;">Go To:</label><span style="display:inline-block;margin-top:6px;font-size:14px"><input tabindex="9" class="viewRadioSC" type="radio" value="merchant" name="merchantgoTo" id="merchantgoTo" checked="checked"/>Merchant<input tabindex="10" type="radio" value="prodResults" name="merchantgoTo" id="merchantgoTo"/>Product Results</span></div>																			
						<div id="merchantresultsGoHere" class="mceActionPanel" style="overflow:auto;display:block;height:430px!important;border:1px solid #919B9C;background-color:gray;width:100%">			
            				<div class="merchantpreview" aria-required="true" style="overflow:auto;"></div>    						        
            			</div>   								
					</div>
				</div>
    		</div>

			<div style="display:block;bottom:4px;overflow:hidden;position:fixed;">			    		    
			    <div id="truePreview" style="visibility:hidden;z-index:-1000;position:relative;overflow:auto;background:gray;height:565px;width:932px;margin-bottom:4px;"></div>
    			<div style="float:right;">        			
    				<input tabindex="11" type="submit" value="Submit Results" class="button-primary" id="prosperMCE_submit" onClick="javascript:shortCode.insert(shortCode.local_ed);"/>
                </div>
		  	</div>		
		</form>
    </body>
</html>

