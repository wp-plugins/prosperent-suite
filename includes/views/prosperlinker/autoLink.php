<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<html>
    <head>
        <title>Link Phrase/Image to a Product</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=4.1'; ?>">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/utils/mctabs.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=4.43322224.1'; ?>"></script>
		<script type="text/javascript">		
		var screenHeight=725>jQuery(window).height()?600:750;
		jQuery(function(){window.prodqueryString="";document.getElementById("apiKey").value=parent.prosperSuiteVars.apiKey;var b=top.tinymce.activeEditor.windowManager.getParams();if(b){document.getElementById("edit").value=!0;var c="fetchProducts"==jQuery("<i "+b+">").attr("ft")?"prod":"merchant",a=jQuery("<i "+b+">").attr("id"),f=jQuery("<i "+b+">").attr("mid"),h=jQuery("<i "+b+">").attr("b"),g=jQuery("<i "+b+">").attr("sale"),k=jQuery("<i "+b+">").attr("pr"),l=jQuery("<i "+b+">").attr("po"),m=jQuery("<i "+
		b+">").attr("q"),n=jQuery("<i "+b+">").attr("ahl"),b=jQuery("<i "+b+">").attr("gtm");"prod"!=c&&(jQuery("#products_tab").removeClass("current"),jQuery("#merchant_tab").addClass("current"),jQuery("#products_panel").removeClass("current"),jQuery("#merchant_panel").addClass("current"));"undefined"!=typeof f&&null!==f&&(document.getElementById(c+"d").value=f);"undefined"!=typeof m&&null!==m&&(document.getElementById(c+"query").value=m);"undefined"!=typeof h&&null!==h&&(document.getElementById(c+"b").value=
		h);"undefined"!=typeof k&&null!==k&&(a=k.split(","),document.getElementById("pricerangea").value=a[0],document.getElementById("pricerangeb").value=a[1]);"undefined"!=typeof g&&null!==g&&jQuery("input[name=onSale][value="+g+"]").attr("checked",!0);"undefined"!=typeof l&&null!==l&&(g=l.split(","),document.getElementById("percentrangea").value=g[0],document.getElementById("percentrangeb").value=g[1]);"undefined"!=typeof b&&null!==b&&jQuery("input[name="+c+"goTo][value="+b+"]").attr("checked",!0);"undefined"!=
		typeof n&&null!==n&&(document.getElementById("prosperHeldURL").value=n);"undefined"!=typeof a&&null!==a&&editPreExist(a)}jQuery("#prodresultsGoHere").css("height",600==screenHeight?"318px":"468px");jQuery("#merchantresultsGoHere").css("height",600==screenHeight?"392px":"542px");jQuery(window).keydown(function(a){if(13==a.keyCode)return a.preventDefault(),!1});jQuery(window).scroll(sticky_relocate);sticky_relocate()});
		function showAddedValues(b){var c=getNewCurrent(),a="",a=jQuery("form").serialize();jQuery("#prodresultsGoHere").css("height",document.getElementById("prodimages").value?600==screenHeight?"270px":"420px":600==screenHeight?"318px":"468px");jQuery("#merchantresultsGoHere").css("height",document.getElementById("merchantimages").value?600==screenHeight?"374px":"524px":600==screenHeight?"392px":"542px");xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div."+c+"added").html(xmlhttp.responseText).show()};
		var f=window.location.pathname,f=f.substring(0,f.lastIndexOf("prosperlinker/"))+"added.php?type="+c+"&";xmlhttp.open("GET",f+a,b);xmlhttp.send()}function sticky_relocate(){var b=jQuery(window).scrollTop(),c=jQuery("#sticky-anchor").offset().top;b>c?jQuery("#stickyHeader").addClass("sticky"):jQuery("#stickyHeader").removeClass("sticky")}function editPreExist(b){clearTimeout(c);var c=window.setTimeout(function(){getIdofItem(jQuery(document.getElementById(b))[0])},1750)}var t;
		function showValues(){var b=getNewCurrent();clearTimeout(t);var c="",c=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div."+b+"preview").html(xmlhttp.responseText).show()};var a=window.location.pathname,a=a.substring(0,a.lastIndexOf("prosperlinker/"))+"preview.php?type="+b+"&";xmlhttp.open("GET",a+c,!0);t=setTimeout(function(){try{xmlhttp.send(),c="",getFilters()}catch(a){}},500);c||clearTimeout(t)}
		function setFocus(){"prod"==getNewCurrent()?document.getElementById("prodquery").focus():document.getElementById("merchantmerchant").focus();top.tinymce.activeEditor.windowManager.getParams()||shortCode.local_ed.selection.getContent()&&!shortCode.local_ed.selection.getContent().match(/(<([^>]+)>)/ig)&&(document.getElementById("prodquery").value=shortCode.local_ed.selection.getContent()?shortCode.local_ed.selection.getContent():"shoes",document.getElementById("merchantmerchant").value=shortCode.local_ed.selection.getContent()?
		shortCode.local_ed.selection.getContent():"Backcountry",showValues())}
		function getIdofItem(b,c){var a=getNewCurrent(),f=1==c?b.id.replace("small",""):b.id,h=jQuery(document.getElementById("small"+f)).attr("src")?jQuery(document.getElementById("small"+f)).attr("src"):jQuery(document.getElementById(f)).find("img.newImage").attr("src");if("prod"!=a||c)c||(0<=document.getElementById(a+"id").value.indexOf(f)?(g=new RegExp(f.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&")+".+?~","g"),window.merchantqueryString=window.merchantqueryString.replace(g,"")):(window.merchantqueryString+=
		f,document.getElementById("merchantmerchant").value&&(window.merchantqueryString+="filterMerchant_"+document.getElementById("merchantmerchant").value+"_"),document.getElementById("merchantcategory").value&&(window.merchantqueryString+="filterCategory_"+document.getElementById("merchantcategory").value),window.prodqueryString+="~"));else if(0<=document.getElementById(a+"id").value.indexOf(f)){var g=new RegExp(f.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&")+".+?~","g");window.prodqueryString=
		window.prodqueryString.replace(g,"")}else{window.prodqueryString+=f;document.getElementById("prodquery").value&&(prodqueryString+="query_"+document.getElementById("prodquery").value+"_");document.getElementById("prodd").value&&(prodqueryString+="filterMerchantId_"+document.getElementById("prodd").value.replace(",","|")+"_");document.getElementById("prodb").value&&(prodqueryString+="filterBrand_"+document.getElementById("prodb").value.replace(",","|")+"_");if(document.getElementById("pricerangea").value||
		document.getElementById("pricerangeb").value)prodqueryString+="filterPrice_"+(document.getElementById("pricerangea").value+","+document.getElementById("pricerangeb").value)+"_";if(document.getElementById("percentrangea").value||document.getElementById("percentrangeb").value)prodqueryString+="filterPercentOff_"+(document.getElementById("percentrangea").value+","+document.getElementById("percentrangeb").value)+"_";!document.getElementById("onSale").checked||document.getElementById("percentrangea").value||
		document.getElementById("percentrangeb").value||(prodqueryString+="filterPriceSale_0.01,_");window.prodqueryString+="~"}0<=document.getElementById(a+"id").value.indexOf(f)?(h=document.getElementById(a+"images").value.replace(h,""),jQuery(document.getElementById(f)).removeClass("highlight"),f=document.getElementById(a+"id").value.replace(f,""),document.getElementById(a+"id").value=f,document.getElementById(a+"images").value=h):(document.getElementById(a+"id").value=f,document.getElementById(a+"images").value=
		h,jQuery("#productList li").removeClass("highlight"),jQuery(document.getElementById(f)).addClass("highlight"));showAddedValues(!0)}
		function getFilters(){var b=jQuery("#prodd").val()?jQuery("#prodd").val().replace(",","|"):"",c=jQuery("#prodb").val()?jQuery("#prodb").val().replace(",","|"):"";pRange=(jQuery("#pricerangea").val()?jQuery("#pricerangea").val()+",":"0.01,")+(jQuery("#pricerangeb").val()?jQuery("#pricerangeb").val():"");perRange=jQuery("#onSale:checked").val()?"1,":(jQuery("#percentrangea").val()?jQuery("#percentrangea").val()+",":"")+(jQuery("#percentrangeb").val()?jQuery("#percentrangeb").val():"");jQuery.ajax({type:"POST",
		url:"http://api.prosperent.com/api/search",data:{api_key:parent.prosperSuiteVars.apiKey,query:jQuery("#prodquery").val(),filterBrand:c,filterPrice:pRange,filterPercentOff:perRange,limit:1,enableFacets:"merchantId|merchant",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){jQuery("#prodmerchant").empty();jQuery.each(a.facets.merchantId,function(f,c){b.match(c.value)?jQuery("#prodmerchant").append('<li id="d'+c.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+
		a.facets.merchant[f].value+"</span></a></li>"):jQuery("#prodmerchant").append('<li id="d'+c.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.facets.merchant[f].value+"</span></a></li>")})},error:function(){alert("Failed to load data.")}});jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:parent.prosperSuiteVars.apiKey,query:jQuery("#prodquery").val(),filterMerchantId:b,filterBrand:c,filterPrice:pRange,filterPercentOff:perRange,
		limit:1,enableFacets:"brand",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){jQuery("#prodbrand").empty();jQuery.each(a.facets.brand,function(a,b){"undefined"!=typeof b.value&&null!==b.value&&0<b.value.length&&(c.match(b.value)?jQuery("#prodbrand").append('<li id="b'+b.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+b.value+"</span></a></li>"):jQuery("#prodbrand").append('<li id="b'+b.value+
		'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+b.value+"</span></a></li>"))})},error:function(){alert("Failed to load data.")}})}
		function getIdValue(b,c){var a=b.id;d=a.slice(0,1);e=a.slice(1);0<=document.getElementById("prod"+d).value.indexOf(e+",")?(jQuery("#"+a).removeClass("activeFilter"),a=document.getElementById("prod"+d).value.replace(e+",",""),document.getElementById("prod"+d).value=a):(document.getElementById("prod"+d).value+=e+",",jQuery("#"+a).addClass("activeFilter"));showValues()};
		</script>
    </head>
    <base target="_self" />
    <body id="linker" role="application" aria-labelledby="app_label" onload="setFocus();showAddedValues();showValues();">
        <div id="mainFormDiv" style="display:block;position:relative;z-index:1;width:100%;">
            <form action="/" method="get" id="prosperSCForm">
            
                <input type="hidden" id="apiKey" name="apiKey"/>
				<input type="hidden" id="edit" name="edit"/>
                <input type="hidden" id="prosperSC" name="prosperSC" value="linker"/>
			    <div class="tabs">
					<ul>
						<li id="products_tab" aria-controls="products_panel" class="current"><span><a href="javascript:;" onClick="mcTabs.displayTab('products_tab','products_panel');setFocus();showAddedValues();showValues();" onmousedown="return false;">Products</a></span></li>						
						<li id="merchant_tab" aria-controls="merchant_panel"><span><a href="javascript:;" onClick="mcTabs.displayTab('merchant_tab','merchant_panel');setFocus();showAddedValues();showValues();" onmousedown="return false;">Merchants</a></span></li>
					</ul>
				</div>	
				
				<div class="panel_wrapper" style="padding: 5px 10px;">
					<div id="products_panel" class="panel current">
						<input type="hidden" id="prodid" name="prodid"/>
						<input type="hidden" id="prodd" name="prodd"/>
						<input type="hidden" id="prodb" name="prodb"/>
						<input type="hidden" name="prodfetch" id="prodfetch" value="fetchProducts"/>						
						<label><strong>Search Products:</strong></label><input class="prosperMainTextSC" tabindex="1" type="text" name="prodq" id="prodquery" onKeyUp="showValues();" value="shoes" placeholder="shoes"/>						
						<table>
    						<tr>
        						<td><div style="display:block;padding-right:10px;"><label class="secondaryLabels" style="width:70px;">Merchant:</label><ul style="max-height:125px;" class="prosperSelect" id="prodmerchant"></ul></div></td>
                                <td><div style="display:block;padding-right:10px;"><label class="secondaryLabels" style="width:50px;">Brand:</label><ul style="max-height:125px;" class="prosperSelect" id="prodbrand"></ul></div></td>						
                                <td style="width:345px;vertical-align:middle;">
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">Price Range:</label>$<input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangea" name="pricerangea" onKeyUp="showValues();"/>&nbsp;&nbsp;to&nbsp;&nbsp;$<input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangeb" name="pricerangeb" onKeyUp="showValues();"/></span>														
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">Percent Off Range:</label><input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangea" name="percentrangea" onKeyUp="showValues();"/>%&nbsp;to&nbsp;&nbsp;&nbsp;<input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangeb" name="percentrangeb" onKeyUp="showValues();"/>%</span>
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">On Sale Only:</label><input tabindex="6" type="checkbox" id="onSale" name="onSale" onClick="showValues();"/></span>
                                    <div style="display:block;"><label class="secondaryLabels" style="width:140px;">Go To:</label><span style="display:inline-block;margin-top:6px;font-size:14px"><input tabindex="9" class="viewRadioSC" type="radio" value="merchant" name="prodgoTo" id="prodgoTo" checked="checked"/>Merchant<input tabindex="10" type="radio" value="prodPage" name="prodgoTo" id="prodgoTo"/>Product Page</span></div>
                                    <span style="float:right;margin-top:5px;">        			
                        				<input tabindex="11" type="submit" value="Submit Results" class="button-primary" id="prosperMCE_submit" onClick="javascript:shortCode.insert(shortCode.local_ed);"/>
                                    </span>
                                </td>                                
                            </tr>
						</table>
						<div id="prosperAddedprod">      			
                            <input type="hidden" id="prodimages" name="prodimages"/>
            			    <div id="sticky-anchor"></div>
    				        <div class="prodadded" aria-required="true"></div> 
        			    </div>	
						<div id="prodresultsGoHere" class="mceActionPanel" style="overflow:auto;display:block;border:1px solid #919B9C;background-color:#fff;">  				
            				<div class="prodpreview" aria-required="true" style="overflow:auto;"></div>    						        
            			</div>           			
					</div>
					<div id="merchant_panel" class="panel">				
						<input type="hidden" id="merchantid" name="merchantid"/>	
						<input type="hidden" name="merchantfetch" id="merchantfetch" value="fetchMerchant"/>															
						<div style="margin-bottom:4px;"><label><strong>Search By Merchant:</strong></label><input class="prosperMainTextSC" value="Backcountry" tabindex="5" type="text" id="merchantmerchant" name="merchantm"  onKeyUp="showValues();"/></div>
						<div style="margin-bottom:4px;"><label><strong>Search By Category:</strong></label><input class="prosperMainTextSC" tabindex="5" type="text" id="merchantcategory" name="merchantcat"  onKeyUp="showValues();"/></div>
						<div style="display:block;margin-bottom:4px;">
						    <label class="secondaryLabels" style="width:182px;">Go To:</label><span style="display:inline-block;margin-top:6px;font-size:14px"><input tabindex="9" class="viewRadioSC" type="radio" value="merchant" name="merchantgoTo" id="merchantgoTo" checked="checked"/>Merchant<input tabindex="10" type="radio" value="prodResults" name="merchantgoTo" id="merchantgoTo"/>Product Results</span>
						    <span style="float:right;"><input tabindex="11" type="submit" value="Submit Results" class="button-primary" id="prosperMCE_submit" onClick="javascript:shortCode.insert(shortCode.local_ed);"/></span>
                        </div>	
                        <div id="prosperAddedmerchant" style="display:block;">      			
                            <input type="hidden" id="merchantimages" name="merchantimages"/>
            			    <div id="sticky-anchor"></div>
    				        <div class="merchantadded" style="display:block" aria-required="true"></div> 
        			    </div> 																		
						<div id="merchantresultsGoHere" class="mceActionPanel" style="overflow:auto;display:block;height:430px!important;border:1px solid #919B9C;background-color:#fff;width:100%">			
            				<div class="merchantpreview" aria-required="true" style="overflow:auto;"></div>    						        
            			</div>   								
					</div>
				</div>
		    </form>
		</div>	
    </body>
</html>

