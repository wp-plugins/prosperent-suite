<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>ProsperInsert</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=4.1'; ?>">		
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/utils/mctabs.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=4.3232227'; ?>"></script>

		<script type="text/javascript">
		var screenHeight=725>jQuery(window).height()?600:750,t;function getNewCurrent(){var b;jQuery("#products_tab").hasClass("current")?b="prod":b="merchant";return b}
		function showValues(){var b=getNewCurrent();clearTimeout(t);var d="",d=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div."+b+"preview").html(xmlhttp.responseText).show()};var a=window.location.pathname,a=a.substring(0,a.lastIndexOf("prosperinsert/"))+"preview.php?type="+b+"&";xmlhttp.open("GET",a+d,!0);t=setTimeout(function(){try{xmlhttp.send(),d="",getFilters()}catch(b){}},500);d||clearTimeout(t)}
		function closeMainPreview(){jQuery("#truePreview").css("visibility","hidden"==jQuery("#truePreview").css("visibility")?"visible":"hidden");jQuery("#truePreview").css("z-index","10"==jQuery("#truePreview").css("z-index")?"-10":"10");jQuery("#mainFormDiv").css("z-index","1"==jQuery("#mainFormDiv").css("z-index")?"-10":"1")}
		function editPreExist(b){var d=getNewCurrent(),a="prod"==d?"http://api.prosperent.com/api/search":"http://api.prosperent.com/api/merchant";if(!b.match(/~/g)){var c=!0;b=b.replace(/,/g,"~");document.getElementById(d+"id").value=b}b=b.replace(/~$/,"").split("~");var e=b.length,f=0;jQuery.each(b,function(b,g){"prod"==d&&c?jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:parent.prosperSuiteVars.apiKey,filterProductId:g,limit:1,imageSize:"125x125"},contentType:"application/json; charset=utf-8",
		dataType:"jsonp",success:function(b){b.data||(document.getElementById("prodid").value=document.getElementById("prodid").value.replace(g+"~",""),document.getElementById(d+"notFound").value=parseInt(document.getElementById(d+"notFound").value)+1);jQuery.each(b.data,function(b,a){document.getElementById("prodid").value=document.getElementById("prodid").value.replace(g,a.keyword.replace(/ /g,"_"));document.getElementById(d+"images").value+=a.image_url+"~"});f+=1;f==e&&showAddedValues(!1)}}):jQuery.ajax({type:"POST",
		url:a,data:{api_key:parent.prosperSuiteVars.apiKey,query:g,limit:1,imageSize:"125x125"},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(b){b.data||(document.getElementById("prodid").value=document.getElementById("prodid").value.replace(g+"~",""),document.getElementById(d+"notFound").value=parseInt(document.getElementById(d+"notFound").value)+1);jQuery.each(b.data,function(b,a){document.getElementById(d+"images").value+=a.image_url+"~"});f+=1;f==e&&showAddedValues(!1)}})})}
		function setFocus(){"prod"==getNewCurrent()?document.getElementById("prodquery").focus():document.getElementById("merchantmerchant").focus();top.tinymce.activeEditor.windowManager.getParams()||shortCode.local_ed.selection.getContent()&&!shortCode.local_ed.selection.getContent().match(/(<([^>]+)>)/ig)&&(document.getElementById("prodquery").value=shortCode.local_ed.selection.getContent()?shortCode.local_ed.selection.getContent():"shoes",document.getElementById("merchantmerchant").value=shortCode.local_ed.selection.getContent()?
		shortCode.local_ed.selection.getContent():"Backcountry",showValues())}
		function openPreview(){jQuery("#truePreview").css("visibility","hidden"==jQuery("#truePreview").css("visibility")?"visible":"hidden");jQuery("#truePreview").css("z-index","10"==jQuery("#truePreview").css("z-index")?"-10":"10");jQuery("#mainFormDiv").css("z-index","1"==jQuery("#mainFormDiv").css("z-index")?"-10":"1");var b=getNewCurrent(),d="",d=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div#truePreview").html(xmlhttp.responseText).show()};var a=
		window.location.pathname,b=a.substring(0,a.lastIndexOf("prosperinsert/"))+"truePreview.php?type="+b+"&";xmlhttp.open("GET",b+d,!0);try{xmlhttp.send(),d=""}catch(c){}}
		function getIdofItem(b,d){var a=getNewCurrent(),c=1==d?b.id.replace("small",""):b.id,e=jQuery(document.getElementById("small"+c)).attr("src")?jQuery(document.getElementById("small"+c)).attr("src"):jQuery(document.getElementById(c)).find("img.newImage").attr("src");if("prod"!=a||d)d||(0<=document.getElementById(a+"id").value.indexOf(c)?(f=new RegExp(c.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&")+".+?~","g"),window.merchantqueryString=window.merchantqueryString.replace(f,"")):(window.merchantqueryString+=
		c,document.getElementById("merchantmerchant").value&&(window.merchantqueryString+="filterMerchant_"+document.getElementById("merchantmerchant").value+"_"),document.getElementById("merchantcategory").value&&(window.merchantqueryString+="filterCategory_"+document.getElementById("merchantcategory").value),window.prodqueryString+="~"));else if(0<=document.getElementById(a+"id").value.indexOf(c)){var f=new RegExp(c.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&")+".+?~","g");window.prodqueryString=
		window.prodqueryString.replace(f,"")}else{window.prodqueryString+=c;document.getElementById("prodquery").value&&(prodqueryString+="query_"+document.getElementById("prodquery").value+"_");document.getElementById("prodd").value&&(prodqueryString+="filterMerchantId_"+document.getElementById("prodd").value.replace(",","|")+"_");document.getElementById("prodb").value&&(prodqueryString+="filterBrand_"+document.getElementById("prodb").value.replace(",","|")+"_");if(document.getElementById("pricerangea").value||
		document.getElementById("pricerangeb").value)prodqueryString+="filterPrice_"+(document.getElementById("pricerangea").value+","+document.getElementById("pricerangeb").value)+"_";if(document.getElementById("percentrangea").value||document.getElementById("percentrangeb").value)prodqueryString+="filterPercentOff_"+(document.getElementById("percentrangea").value+","+document.getElementById("percentrangeb").value)+"_";!document.getElementById("onSale").checked||document.getElementById("percentrangea").value||
		document.getElementById("percentrangeb").value||(prodqueryString+="filterPriceSale_0.01,_");window.prodqueryString+="~"}"pc"==jQuery("#prodview:checked").val()?0<=document.getElementById(a+"id").value.indexOf(c)?(e=document.getElementById(a+"images").value.replace(e,""),jQuery(document.getElementById(c)).removeClass("highlight"),c=document.getElementById(a+"id").value.replace(c,""),document.getElementById(a+"id").value=c,document.getElementById(a+"images").value=e):(document.getElementById(a+"id").value=
		c,document.getElementById(a+"images").value=e,jQuery("#productList li").removeClass("highlight"),jQuery(document.getElementById(c)).addClass("highlight")):0<=document.getElementById(a+"id").value.indexOf(c+"~")?(e=document.getElementById(a+"images").value.replace(e+"~",""),jQuery(document.getElementById(c)).removeClass("highlight"),c=document.getElementById(a+"id").value.replace(c+"~",""),document.getElementById(a+"id").value=c,document.getElementById(a+"images").value=e):(document.getElementById(a+
		"id").value+=c+"~",jQuery(document.getElementById(c)).addClass("highlight"),document.getElementById(a+"images").value+=e+"~");showAddedValues(!0)}
		function getFilters(){var b=jQuery("#prodd").val()?jQuery("#prodd").val().replace(",","|"):"",d=jQuery("#prodb").val()?jQuery("#prodb").val().replace(",","|"):"",a=(jQuery("#pricerangea").val()?jQuery("#pricerangea").val()+",":"0.01,")+(jQuery("#pricerangeb").val()?jQuery("#pricerangeb").val():""),c=jQuery("#onSale:checked").val()?"1,":(jQuery("#percentrangea").val()?jQuery("#percentrangea").val()+",":"")+(jQuery("#percentrangeb").val()?jQuery("#percentrangeb").val():"");jQuery.ajax({type:"POST",
		url:"http://api.prosperent.com/api/search",data:{api_key:parent.prosperSuiteVars.apiKey,query:jQuery("#prodquery").val(),filterBrand:d,filterPrice:a,filterPercentOff:c,limit:1,enableFacets:"merchantId|merchant",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){jQuery("#prodmerchant").empty();jQuery.each(a.facets.merchantId,function(c,d){b.match(d.value)?jQuery("#prodmerchant").append('<li id="d'+d.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+
		a.facets.merchant[c].value+"</span></a></li>"):jQuery("#prodmerchant").append('<li id="d'+d.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.facets.merchant[c].value+"</span></a></li>")})},error:function(){alert("Failed to load data.")}});jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:parent.prosperSuiteVars.apiKey,query:jQuery("#prodquery").val(),filterMerchantId:b,filterBrand:d,filterPrice:a,filterPercentOff:c,limit:1,enableFacets:"brand",
		enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(b){jQuery("#prodbrand").empty();jQuery.each(b.facets.brand,function(b,a){d.match(a.value)?jQuery("#prodbrand").append('<li id="b'+a.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.value+"</span></a></li>"):jQuery("#prodbrand").append('<li id="b'+a.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.value+
		"</span></a></li>")})},error:function(){alert("Failed to load data.")}})}function getIdValue(b,d){var a=b.id,c=a.slice(0,1),e=a.slice(1);0<=document.getElementById("prod"+c).value.indexOf(e+",")?(jQuery("#"+a).removeClass("activeFilter"),a=document.getElementById("prod"+c).value.replace(e+",",""),document.getElementById("prod"+c).value=a):(document.getElementById("prod"+c).value+=e+",",jQuery("#"+a).addClass("activeFilter"));showValues()}
		function showAddedValues(b){var d=getNewCurrent(),a="",a=jQuery("form").serialize();jQuery("#prodresultsGoHere").css("height",document.getElementById("prodimages").value?600==screenHeight?"256px":"409px":600==screenHeight?"312px":"462px");jQuery("#merchantresultsGoHere").css("height",document.getElementById("merchantimages").value?600==screenHeight?"360px":"409px":600==screenHeight?"383px":"462px");xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div."+d+"added").html(xmlhttp.responseText).show()};
		var c=window.location.pathname,c=c.substring(0,c.lastIndexOf("prosperinsert/"))+"added.php?type="+d+"&";xmlhttp.open("GET",c+a,b);xmlhttp.send()}function sticky_relocate(){var b=jQuery(window).scrollTop(),d=jQuery("#sticky-anchor").offset().top;b>d?jQuery("#stickyHeader").addClass("sticky"):jQuery("#stickyHeader").removeClass("sticky")}
		jQuery(function(){window.prodqueryString="";window.merchantqueryString="";document.getElementById("apiKey").value=parent.prosperSuiteVars.apiKey;var b=top.tinymce.activeEditor.windowManager.getParams();if(b&&jQuery("<i "+b+">").attr("ft")){document.getElementById("edit").value=!0;var d="fetchMerchant"==jQuery("<i "+b+">").attr("ft")?"merchant":"prod",a=jQuery("<i "+b+">").attr("id"),c=jQuery("<i "+b+">").attr("mid"),e=jQuery("<i "+b+">").attr("b"),f=jQuery("<i "+b+">").attr("sale"),k=jQuery("<i "+
		b+">").attr("pr"),g=jQuery("<i "+b+">").attr("po"),l=jQuery("<i "+b+">").attr("q"),m=jQuery("<i "+b+">").attr("gtm"),h=jQuery("<i "+b+">").attr("v"),n=jQuery("<i "+b+">").attr("vst"),p=jQuery("<i "+b+">").attr("cat");limit=jQuery("<i "+b+">").attr("l");jQuery("<i "+b+">").attr("imgt");"prod"!=d&&(jQuery("#products_tab").removeClass("current"),jQuery("#merchant_tab").addClass("current"),jQuery("#products_panel").removeClass("current"),jQuery("#merchant_panel").addClass("current"));"undefined"!=typeof c&&
		null!==c&&(document.getElementById(d+"d").value=c);"undefined"!=typeof e&&null!==e&&(document.getElementById(d+"b").value=e);"undefined"!=typeof limit&&null!==limit&&(document.getElementById(d+"limit").value=limit);"undefined"!=typeof p&&null!==p&&(document.getElementById("merchantcategory").value=p);"undefined"!=typeof n&&null!==n&&(document.getElementById("prodvisit").value=n);"undefined"!=typeof k&&null!==k&&(a=k.split(","),document.getElementById("pricerangea").value=a[0],document.getElementById("pricerangeb").value=
		a[1]);"undefined"!=typeof f&&null!==f&&jQuery("input[name=onSale]").attr("checked",!0);"undefined"!=typeof h&&null!==h&&jQuery("input[name=prodview][value="+h+"]").attr("checked",!0);"pc"==h&&openImageType();"undefined"!=typeof g&&null!==g&&(f=g.split(","),document.getElementById("percentrangea").value=f[0],document.getElementById("percentrangeb").value=f[1]);"undefined"!=typeof m&&null!==m&&jQuery("input[name="+d+"goTo][value="+m+"]").attr("checked",!0);"undefined"!=typeof a&&null!==a&&(document.getElementById("prodid").value=
		a.replace(/ /g,"_"),editPreExist(a));"undefined"!=typeof l&&null!==l&&(document.getElementById("prodquery").value=l)}jQuery("#prodresultsGoHere").css("height",600==screenHeight?"312px":"462px");jQuery("#merchantresultsGoHere").css("height",600==screenHeight?"410px":"480px");jQuery("#truePreview").css("height",600==screenHeight?"588px":"738px");jQuery(window).keydown(function(a){if(13==a.keyCode)return a.preventDefault(),!1});jQuery(window).scroll(sticky_relocate);sticky_relocate()});
		function openImageType(){jQuery("#prodimages").val("");jQuery("#prodid").val("");showAddedValues();showValues();"pc"==jQuery("#prodview:checked").val()?(jQuery(document.getElementById("prosperInsertLimit")).hide(),document.getElementById("prosperInsertInstructions").innerHTML="Choose 1 product to get price comparison from multiple merchants.",jQuery("#prodImageType").css("visibility","visible")):(document.getElementById("prosperInsertInstructions").innerHTML="Choose products to include in your widget, or select nothing for default search results.",
		jQuery(document.getElementById("prosperInsertLimit")).show(),jQuery("#prodImageType").css("visibility","hidden"))};
			</script>
    </head>
    <base target="_self" />
    <body id="inserter" role="application" aria-labelledby="app_label" onload="setFocus();showAddedValues();showValues();">			
		<div id="mainFormDiv" style="display:block;position:relative;z-index:1;width:100%;">
		    <form action="/" method="get" id="prosperSCForm">
			    <input type="hidden" id="apiKey" name="apiKey"/>
			    <input type="hidden" id="edit" name="edit"/>
			    <input type="hidden" id="prosperSC" value="prosperInsert"/>
				<div class="tabs">
					<ul>
						<li id="products_tab" aria-controls="products_panel" class="current"><span><a href="javascript:;" onClick="mcTabs.displayTab('products_tab','products_panel');setFocus();showAddedValues();showValues();" onmousedown="return false;">Products</a></span></li>						
						<li id="merchant_tab" aria-controls="merchant_panel"><span><a href="javascript:;" onClick="mcTabs.displayTab('merchant_tab','merchant_panel');setFocus();showAddedValues();showValues();" onmousedown="return false;">Merchants</a></span></li>
					</ul>
				</div>

				<div class="panel_wrapper" style="padding: 5px 10px;">
					<div id="products_panel" class="panel current">
						<input type="hidden" id="prodid" name="prodid"/>
						<input type="hidden" id="prodnotFound" name="prodnotFound" value="0"/>
						<input type="hidden" id="prodd" name="prodd"/>
						<input type="hidden" id="prodb" name="prodb"/>						
						<input type="hidden" name="prodfetch" id="prodfetch" value="fetchProducts"/>						
						<label><strong>Search Products:</strong></label><input class="prosperMainTextSC" tabindex="1" type="text" name="prodq" id="prodquery" onKeyUp="showValues();" value="shoes"/>						
						<table>
    						<tr>
        						<td style="vertical-align:top;width:205px;"><div style="display:block;"><label class="secondaryLabels" style="width:70px;">Merchant:</label><ul class="prosperSelect" id="prodmerchant"></ul></div></td>
                                <td style="vertical-align:top;width:205px;"><div style="display:block;"><label class="secondaryLabels" style="width:50px;">Brand:</label><ul class="prosperSelect" id="prodbrand"></ul></div></td>						
                                <td style="width:290px;vertical-align:middle;">
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:100px;">Price Range:</label><span style="color:#747474;padding-right:2px;">$</span><input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangea" name="pricerangea" onKeyUp="showValues();getFilters();" style="margin-top:2px;"/><span style="color:#747474;padding-right:">&nbsp;to&nbsp;</span><span style="color:#747474;padding-right:2px;">$</span><input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangeb" name="pricerangeb" onKeyUp="showValues();getFilters();" style="margin-top:2px;"/></span>														
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:100px;">Percent Off:</label><input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangea" name="percentrangea" onKeyUp="showValues();getFilters();" style="margin-top:4px;"/><span style="color:#747474;padding-left:2px;">%</span><span style="color:#747474;">&nbsp;to&nbsp;</span><input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangeb" name="percentrangeb" onKeyUp="showValues();getFilters();" style="margin-top:4px;"/><span style="color:#747474;padding-left:2px;">%</span></span>
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:100px;">On Sale Only:</label><input tabindex="6" type="checkbox" id="onSale" name="onSale" onClick="showValues();getFilters();" style="margin-top:6px;"/></span>
                                    <div style="display:block;"><label class="secondaryLabels" style="width:100px;">Go To:</label><span style="display:inline-block;margin-top:6px"><input tabindex="9" class="viewRadioSC" type="radio" value="merchant" name="prodgoTo" id="prodgoTo" checked="checked"/>Merchant<input tabindex="10" type="radio" value="prodPage" name="prodgoTo" id="prodgoTo"/>Product Page</span></div>
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:100px;">Button Text:</label><input class="prosperTextSC" style="font-size:14px;width:170px!important;margin-top:4px;" placeholder="Visit Store" tabindex="8" type="text" id="prodvisit" name="prodvisit"/></span>                                               
                                </td>    
                                <td style="vertical-align:middle;">
                                    <span style="display:inline-block;" id="prosperInsertLimit"><label class="secondaryLabels" style="width:50px;">Limit:</label><input class="prosperTextSC" style="font-size:14px;width:75px!important;margin-top:4px;" tabindex="8" type="text" id="prodlimit" name="prodlimit"/></span>
                                    <div>
                                        <span><label style="width:45px;" class="secondaryLabels">View:</label> 
                                        <input tabindex="9" style="margin-top:6px;" class="viewRadioSC" type="radio" value="grid" name="prodview" id="prodview" checked="checked" onClick="showValues();openImageType()"/>Grid</span> 
                                        <div><input style="margin-left:50px;" tabindex="10" type="radio" value="list" name="prodview" id="prodview" onClick="showValues();openImageType()"/>List/Detail</div>
                                        <div><input style="margin-left:50px;" tabindex="10" type="radio" value="pc" name="prodview" id="prodview" onClick="showValues();openImageType();"/>Price Comparison</div>
                                    </div>
                                    <select tabindex="2" id="prodImageType" name="prodImageType" style="margin-left:55px;font-size:14px;visibility:hidden;">
            						    <option value="original" selected="selected">Original Logo</option>
            						    <option value="white">White Logo</option>
            						    <option value="black">Black Logo</option>
        						    </select>
        						    <div style="float:right;z-index:1001;">
                            			<input tabindex="11" type="submit" value="Preview" class="button-primary" id="prosperMCE_preview" onClick="openPreview(this);return false;" style="visibility:visible;"/>
    				                    <input tabindex="11" type="submit" value="Submit Results" class="button-primary" id="prosperMCE_submit" onClick="javascript:shortCode.insert(shortCode.local_ed);"/>
                                    </div>
                                </td>                          
                            </tr>                            
						</table>
						<div id="prosperAddedprod">      			
                            <input type="hidden" id="prodimages" name="prodimages"/>
            			    <div id="sticky-anchor"></div>
    				        <div class="prodadded" aria-required="true"></div> 
        			    </div>	
						<div id="prodresultsGoHere" class="mceActionPanel" style="overflow:auto;height:517px;max-height:517px;border:1px solid #919B9C;">  				
            				<div class="prodpreview" aria-required="true" style="overflow:auto;"></div>               				 						        
            			</div>  
            							
					</div>
					
					<div id="merchant_panel" class="panel">				
						<input type="hidden" id="merchantid" name="merchantid"/>		
						<input type="hidden" name="merchantfetch" id="merchantfetch" value="fetchMerchant"/>															
						<div style="margin-bottom:4px;"><label><strong>Search By Merchant:</strong></label><input class="prosperMainTextSC" value="Backcountry" tabindex="5" type="text" id="merchantmerchant" name="merchantm"  onKeyUp="showValues();"/></div>
						<div style="margin-bottom:4px;"><label><strong>Search By Category:</strong></label><input class="prosperMainTextSC" tabindex="5" type="text" id="merchantcategory" name="merchantcat"  onKeyUp="showValues();"/></div>														
						<div style="margin-bottom:12px;font-size:16px;">
						    <label class="secondaryLabels" style="width:182px;font-size:16px;">Image Type:</label>
						    <select tabindex="2" id="merchantImageType" name="merchantImageType" onChange="showValues();" style="font-size:16px;">
    						    <option value="original" selected="selected">Original Logo</option>
    						    <option value="white">White Logo</option>
    						    <option value="black">Black Logo</option>
						    </select>
						    <span style="display:inline-block;padding-left:25px"><label class="secondaryLabels" style="width:140px;font-size:16px;">Go To Merchant:</label><input style="margin-top:7px" tabindex="2" type="checkbox" name="merchantgoTo" id="merchantgoTo" value="merchant" checked="checked"/></span>
						    <span style="display:inline-block;padding-left:25px"><label class="secondaryLabels" style="width:60px;font-size:16px;">Limit:</label><input class="prosperTextSC" style="width:75px!important;margin-top:7px" tabindex="2" type="text" name="merchantlimit" id="merchantlimit"/></span>
                            <div style="display:inline-block;float:right;margin-top:4px;z-index:1001;">
                    			<input tabindex="11" type="submit" value="Preview" class="button-primary" id="prosperMCE_preview" onClick="openPreview(this);return false;" style="visibility:visible;"/>
			                    <input tabindex="11" type="submit" value="Submit Results" class="button-primary" id="prosperMCE_submit" onClick="javascript:shortCode.insert(shortCode.local_ed);"/>
                            </div>
						</div>
						<div id="prosperAddedmerchant" style="display:block;">      			
                            <input type="hidden" id="merchantimages" name="merchantimages"/>
            			    <div id="sticky-anchor"></div>
    				        <div class="merchantadded" style="display:block" aria-required="true"></div> 
        			    </div> 
						<div id="merchantresultsGoHere" class="mceActionPanel" style="overflow:auto;display:block;height:480px;max-height:480px;border:1px solid #919B9C;background-color:#fff">			
            				<div class="merchantpreview" aria-required="true" style="overflow:auto;"></div>    	
            			</div>             			 								
					</div>
				</div>			
		    </form>
        </div>  
        <div style="display:block;bottom:4px;overflow:hidden;position:fixed;">			    		    
            <div id="truePreview" style="visibility:hidden;z-index:-1000;position:relative;overflow:auto;background:#fff;height:708px;width:932px;"></div>
        </div>	
    </body>
</html>