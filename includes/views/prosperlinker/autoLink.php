<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<html>
    <head>
        <title>Link Phrase/Image to a Product</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=1'; ?>">
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/utils/mctabs.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=3.3.3'; ?>"></script>
		<script type="text/javascript">		
		jQuery(function(){var b=950>jQuery(window).height()?675:750;jQuery("#prodresultsGoHere").css("height",675==b?"400px":"480px");jQuery("#merchantresultsGoHere").css("height",675==b?"400px":"480px");jQuery("#truePreview").css("height",675==b?"635px":"708px")});var t;
		function showValues(){var b=getNewCurrent();clearTimeout(t);var c="",c=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div."+b+"preview").html(xmlhttp.responseText).show()};var a=window.location.pathname,a=a.substring(0,a.lastIndexOf("prosperlinker/"))+"preview.php?type="+b+"&";xmlhttp.open("GET",a+c,!0);t=setTimeout(function(){try{xmlhttp.send(),c=""}catch(a){}},500);c||clearTimeout(t)}
		function setFocus(){"prod"==getNewCurrent()?document.getElementById("prodquery").focus():document.getElementById("merchantmerchant").focus();shortCode.local_ed.selection.getContent()&&!shortCode.local_ed.selection.getContent().match(/(<([^>]+)>)/ig)&&(document.getElementById("prodquery").value=shortCode.local_ed.selection.getContent()?shortCode.local_ed.selection.getContent():"shoes",document.getElementById("merchantmerchant").value=shortCode.local_ed.selection.getContent()?shortCode.local_ed.selection.getContent():
		"Backcountry",showValues())}function getIdofItem(b,c){var a=getNewCurrent(),f=b.id;0<=document.getElementById(a+"id").value.indexOf(f)?(jQuery("#"+f).removeClass("highlight"),f=document.getElementById(a+"id").value.replace(f,""),document.getElementById(a+"id").value=f):(document.getElementById(a+"id").value=f,jQuery("#productList li").removeClass("highlight"),jQuery("li#"+f).addClass("highlight"))}
		function getFilters(){var b=jQuery("#prodd").val()?jQuery("#prodd").val().replace(",","|"):"",c=jQuery("#prodb").val()?jQuery("#prodb").val().replace(",","|"):"";pRange=(jQuery("#pricerangea").val()?jQuery("#pricerangea").val()+",":"0.01,")+(jQuery("#pricerangeb").val()?jQuery("#pricerangeb").val():"");perRange=jQuery("#onSale:checked").val()?"1,":(jQuery("#percentrangea").val()?jQuery("#percentrangea").val()+",":"")+(jQuery("#percentrangeb").val()?jQuery("#percentrangeb").val():"");jQuery.ajax({type:"POST",
		url:"http://api.prosperent.com/api/search",data:{api_key:"fc91d36b383ca0231ee59c5048eabedc",query:jQuery("#prodquery").val(),filterMerchantId:b,filterBrand:c,filterPrice:pRange,filterPercentOff:perRange,limit:1,enableFacets:"merchantId|merchant|brand",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){jQuery("#prodbrand").empty();jQuery("#prodmerchant").empty();jQuery.each(a.facets.merchantId,function(f,c){b.match(c.value)?jQuery("#prodmerchant").append('<li id="d'+
		c.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.facets.merchant[f].value+"</span></a></li>"):jQuery("#prodmerchant").append('<li id="d'+c.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.facets.merchant[f].value+"</span></a></li>")});jQuery.each(a.facets.brand,function(a,b){c.match(b.value)?jQuery("#prodbrand").append('<li id="b'+b.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+
		b.value+"</span></a></li>"):jQuery("#prodbrand").append('<li id="b'+b.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+b.value+"</span></a></li>")})},error:function(){alert("Failed to load data.")}})}
		function getIdValue(b,c){var a=b.id;d=a.slice(0,1);e=a.slice(1);0<=document.getElementById("prod"+d).value.indexOf(e+",")?(jQuery("#"+a).removeClass("activeFilter"),a=document.getElementById("prod"+d).value.replace(e+",",""),document.getElementById("prod"+d).value=a):(document.getElementById("prod"+d).value+=e+",",jQuery("#"+a).addClass("activeFilter"));showValues()};
		</script>
    </head>
    <base target="_self" />
    <body id="linker" role="application" aria-labelledby="app_label" onload="setFocus();showValues();getFilters();">
		<form action="/" method="get" id="prosperSCForm">
            <div id="mainFormDiv" style="display:block;position:relative;z-index:1;width:100%;">
                <input type="hidden" id="prosperSC" value="linker"/>
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
						<label><strong>Search Products:</strong></label><input class="prosperMainTextSC" tabindex="1" type="text" name="prodq" id="prodquery" onKeyUp="showValues();getFilters();" value="shoes" placeholder="shoes"/>						
						<table>
    						<tr>
        						<td><div style="display:block;padding-right:10px;"><label class="secondaryLabels" style="width:70px;">Merchant:</label><ul style="max-height:125px;" class="prosperSelect" id="prodmerchant"></ul></div></td>
                                <td><div style="display:block;padding-right:10px;"><label class="secondaryLabels" style="width:50px;">Brand:</label><ul style="max-height:125px;" class="prosperSelect" id="prodbrand"></ul></div></td>						
                                <td style="width:345px;vertical-align:middle;">
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">Price Range:</label>$<input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangea" name="pricerangea" onKeyUp="showValues();"/>&nbsp;&nbsp;to&nbsp;&nbsp;$<input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangeb" name="pricerangeb" onKeyUp="showValues();"/></span>														
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">Percent Off Range:</label><input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangea" name="percentrangea" onKeyUp="showValues();"/>%&nbsp;to&nbsp;&nbsp;&nbsp;<input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangeb" name="percentrangeb" onKeyUp="showValues();"/>%</span>
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">On Sale Only:</label><input tabindex="6" type="checkbox" id="onSale" name="onSale" onClick="showValues();"/></span>
                                </td>
                            </tr>
						</table>
						<div id="prodresultsGoHere" class="mceActionPanel" style="overflow:auto;display:block;height:500px!important;border:1px solid #919B9C;background-color:gray;">  				
            				<div class="prodpreview" aria-required="true" style="overflow:auto;"></div>    						        
            			</div>           			
					</div>
					<div id="merchant_panel" class="panel">				
						<input type="hidden" id="merchantid" name="merchantid"/>	
						<input type="hidden" name="merchantfetch" id="merchantfetch" value="fetchMerchant"/>															
						<div style="margin-bottom:4px;"><label><strong>Search By Merchant:</strong></label><input class="prosperMainTextSC" value="Backcountry" tabindex="5" type="text" id="merchantmerchant" name="merchantm"  onKeyUp="showValues();"/></div>
						<div style="margin-bottom:4px;"><label><strong>Search By Category:</strong></label><input class="prosperMainTextSC" tabindex="5" type="text" id="merchantcategory" name="merchantcat"  onKeyUp="showValues();"/></div>													
						<div id="merchantresultsGoHere" class="mceActionPanel" style="overflow:auto;display:block;height:500px!important;border:1px solid #919B9C;background-color:gray">			
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

