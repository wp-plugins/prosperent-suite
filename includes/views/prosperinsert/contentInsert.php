<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Edit Automated ProsperInsert</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=4.1'; ?>">		
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=4.1'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/utils/mctabs.js'; ?>"></script>

		<script type="text/javascript">
		/*function editInsert () {
			console.log(shortCode.local_ed.selection.getContent());
		}*/
		var screenHeight=725>jQuery(window).height()?600:750,t;function getNewCurrent(){var a;jQuery("#products_tab").hasClass("current")?a="prod":a="merchant";return a}
		function showValues(){var a=getNewCurrent();clearTimeout(t);var c="",c=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div."+a+"preview").html(xmlhttp.responseText).show()};var b=window.location.pathname,b=b.substring(0,b.lastIndexOf("prosperinsert/"))+"preview.php?type="+a+"&";xmlhttp.open("GET",b+c,!0);t=setTimeout(function(){try{xmlhttp.send(),c=""}catch(b){}},500);c||clearTimeout(t)}
		function setFocus(){"prod"==getNewCurrent()?document.getElementById("prodquery").focus():document.getElementById("merchantmerchant").focus();top.tinymce.activeEditor.windowManager.getParams()||shortCode.local_ed.selection.getContent()&&!shortCode.local_ed.selection.getContent().match(/(<([^>]+)>)/ig)&&(document.getElementById("prodquery").value=shortCode.local_ed.selection.getContent()?shortCode.local_ed.selection.getContent():"shoes",showValues())}
		function openPreview(){jQuery("#truePreview").css("visibility","hidden"==jQuery("#truePreview").css("visibility")?"visible":"hidden");jQuery("#truePreview").css("z-index","1000"==jQuery("#truePreview").css("z-index")?"-1000":"1000");jQuery("#mainFormDiv").css("z-index","1"==jQuery("#mainFormDiv").css("z-index")?"-1000":"1");jQuery("#prosperMCE_preview").prop("value","Preview"==jQuery("#prosperMCE_preview").prop("value")?"Close Preview":"Preview");var a=getNewCurrent(),c="",c=jQuery("form").serialize();
		xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div#truePreview").html(xmlhttp.responseText).show()};var b=window.location.pathname,a=b.substring(0,b.lastIndexOf("prosperinsert/"))+"truePreview.php?type="+a+"&";xmlhttp.open("GET",a+c,!0);try{xmlhttp.send(),c=""}catch(d){}}
		function getIdofItem(a,c){var b=getNewCurrent(),d=1==c?a.id.replace("small",""):a.id,e=jQuery("#small"+d).attr("src")?jQuery("#small"+d).attr("src"):jQuery("#"+d).find("img.newImage").attr("src");"pc"==jQuery("#prodview:checked").val()?0<=document.getElementById(b+"id").value.indexOf(d)?(jQuery("#"+d).removeClass("highlight"),d=document.getElementById(b+"id").value.replace(d,""),document.getElementById(b+"id").value=d):(document.getElementById(b+"id").value=d,jQuery("#productList li").removeClass("highlight"),
		jQuery("li#"+d).addClass("highlight")):(0<=document.getElementById(b+"id").value.indexOf(d+",")?(e=document.getElementById(b+"images").value.replace(e+",",""),jQuery("#"+d).removeClass("highlight"),d=document.getElementById(b+"id").value.replace(d+",",""),document.getElementById(b+"id").value=d,document.getElementById(b+"images").value=e):(document.getElementById(b+"id").value+=d+",",jQuery("#"+d).addClass("highlight"),document.getElementById(b+"images").value+=e+","),showAddedValues(),jQuery("#"+
		b+"resultsGoHere").css("height",document.getElementById(b+"images").value?600==screenHeight?"233px":"380px":600==screenHeight?"337px":"480px"))}
		function getFilters(){var a=jQuery("#prodd").val()?jQuery("#prodd").val().replace(",","|"):"",c=jQuery("#prodb").val()?jQuery("#prodb").val().replace(",","|"):"";pRange=(jQuery("#pricerangea").val()?jQuery("#pricerangea").val()+",":"0.01,")+(jQuery("#pricerangeb").val()?jQuery("#pricerangeb").val():"");perRange=jQuery("#onSale:checked").val()?"1,":(jQuery("#percentrangea").val()?jQuery("#percentrangea").val()+",":"")+(jQuery("#percentrangeb").val()?jQuery("#percentrangeb").val():"");jQuery.ajax({type:"POST",
		url:"http://api.prosperent.com/api/search",data:{api_key:"fc91d36b383ca0231ee59c5048eabedc",query:jQuery("#prodquery").val(),filterBrand:c,filterPrice:pRange,filterPercentOff:perRange,limit:1,enableFacets:"merchantId|merchant",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(b){jQuery("#prodmerchant").empty();jQuery.each(b.facets.merchantId,function(d,e){a.match(e.value)?jQuery("#prodmerchant").append('<li id="d'+e.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+
		b.facets.merchant[d].value+"</span></a></li>"):jQuery("#prodmerchant").append('<li id="d'+e.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+b.facets.merchant[d].value+"</span></a></li>")})},error:function(){alert("Failed to load data.")}});jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:"fc91d36b383ca0231ee59c5048eabedc",query:jQuery("#prodquery").val(),filterMerchantId:a,filterBrand:c,filterPrice:pRange,filterPercentOff:perRange,
		limit:1,enableFacets:"brand",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(b){jQuery("#prodbrand").empty();jQuery.each(b.facets.brand,function(b,a){c.match(a.value)?jQuery("#prodbrand").append('<li id="b'+a.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.value+"</span></a></li>"):jQuery("#prodbrand").append('<li id="b'+a.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+
		a.value+"</span></a></li>")})},error:function(){alert("Failed to load data.")}})}function getIdValue(a,c){var b=a.id,d=b.slice(0,1),e=b.slice(1);0<=document.getElementById("prod"+d).value.indexOf(e+",")?(jQuery("#"+b).removeClass("activeFilter"),b=document.getElementById("prod"+d).value.replace(e+",",""),document.getElementById("prod"+d).value=b):(document.getElementById("prod"+d).value+=e+",",jQuery("#"+b).addClass("activeFilter"));showValues()}
		function showAddedValues(){var a=getNewCurrent(),c="",c=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div."+a+"added").html(xmlhttp.responseText).show()};var b=window.location.pathname,b=b.substring(0,b.lastIndexOf("prosperinsert/"))+"added.php?type="+a+"&";xmlhttp.open("GET",b+c,!0);xmlhttp.send()}
		function sticky_relocate(){var a=jQuery(window).scrollTop(),c=jQuery("#sticky-anchor").offset().top;a>c?jQuery("#stickyHeader").addClass("sticky"):jQuery("#stickyHeader").removeClass("sticky")}
		jQuery(function(){var a=top.tinymce.activeEditor.windowManager.getParams();if(a){document.getElementById("edit").value=!0;var c=jQuery("<i "+a+">").attr("id"),b=jQuery("<i "+a+">").attr("mid"),d=jQuery("<i "+a+">").attr("b"),e=jQuery("<i "+a+">").attr("sale"),h=jQuery("<i "+a+">").attr("pr"),f=jQuery("<i "+a+">").attr("po"),k=jQuery("<i "+a+">").attr("q"),l=jQuery("<i "+a+">").attr("gtm"),p=jQuery("<i "+a+">").attr("noShow"),g=jQuery("<i "+a+">").attr("v"),m=jQuery("<i "+a+">").attr("vst"),n=jQuery("<i "+
		a+">").attr("cat");jQuery("<i "+a+">").attr("imgt");"undefined"!=typeof b&&null!==b&&(document.getElementById("prodd").value=b);"undefined"!=typeof d&&null!==d&&(document.getElementById("prodb").value=d);"undefined"!=typeof n&&null!==n&&(document.getElementById("merchantcategory").value=n);"undefined"!=typeof m&&null!==m&&(document.getElementById("prodvisit").value=m);"undefined"!=typeof h&&null!==h&&(c=h.split(","),document.getElementById("pricerangea").value=c[0],document.getElementById("pricerangeb").value=
		c[1]);"undefined"!=typeof e&&null!==e&&jQuery("input[name=onSale]").attr("checked",!0);"undefined"!=typeof g&&null!==g&&jQuery("input[name=prodview][value="+g+"]").attr("checked",!0);"pc"==g&&openImageType();"undefined"!=typeof f&&null!==f&&(e=f.split(","),document.getElementById("percentrangea").value=e[0],document.getElementById("percentrangeb").value=e[1]);"undefined"!=typeof l&&null!==l&&jQuery("input[name=prodgoTo][value="+l+"]").attr("checked",!0);"undefined"!=typeof p&&null!==p&&jQuery("input[name=noShow]").attr("checked",
		!0);"undefined"!=typeof c&&null!==c?(document.getElementById("prodid").value=c,editPreExist(c)):"undefined"!=typeof k&&null!==k&&(document.getElementById("prodquery").value=k)}jQuery(window).keydown(function(b){if(13==b.keyCode)return b.preventDefault(),!1});jQuery("#prodresultsGoHere").css("height",600==screenHeight?"337px":"480px");jQuery("#truePreview").css("height",600==screenHeight?"558px":"708px");jQuery(window).scroll(sticky_relocate);sticky_relocate()});var newtimeOut;
		function editPreExist(a){clearTimeout(c);var c=window.setTimeout(function(){var b=a.replace(/,$/,"").split(",");jQuery.each(b,function(b,a){if("undefined"!=typeof a&&null!==a&&0<a.length&&"undefined"!=a){var c=getNewCurrent(),f=jQuery("#"+a).find("img.newImage").attr("src");jQuery("#"+a).addClass("highlight");document.getElementById(c+"images").value+=f+",";showAddedValues();jQuery("#"+c+"resultsGoHere").css("height",document.getElementById(c+"images").value?600==screenHeight?"233px":"380px":600==
		screenHeight?"337px":"480px")}})},1750)}function openImageType(){"pc"==jQuery("#prodview:checked").val()?(jQuery("#prosperAddedprod").css("display","none"),jQuery("#prodImageType").css("visibility","visible")):(jQuery("#prosperAddedprod").css("display","block"),jQuery("#prodImageType").css("visibility","hidden"))};
			</script>
    </head>
    <base target="_self" />
    <body id="inserter" role="application" aria-labelledby="app_label" onload="setFocus();showValues();getFilters()">			
		<form action="/" method="get" id="prosperSCForm">
			<div id="mainFormDiv" style="display:block;position:relative;z-index:1;width:100%;">
				<input type="hidden" id="edit" name="edit"/>			    
			    <input type="hidden" id="prosperSC" value="contentInsert"/>
				<div class="tabs">
					<ul>
						<li id="products_tab" aria-controls="products_panel" class="current"><span><a href="javascript:;" onClick="mcTabs.displayTab('products_tab','products_panel');setFocus();showValues();" onmousedown="return false;">Products</a></span></li>						
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
        						<td style="vertical-align:top;width:215px;"><div style="display:block;padding-right:10px;"><label class="secondaryLabels" style="width:70px;">Merchant:</label><ul class="prosperSelect" id="prodmerchant"></ul></div></td>
                                <td style="vertical-align:top;width:215px;"><div style="display:block;padding-right:10px;"><label class="secondaryLabels" style="width:50px;">Brand:</label><ul class="prosperSelect" id="prodbrand"></ul></div></td>						
                                <td style="width:350px;vertical-align:middle;">
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">Price Range:</label><span style="color:#747474;padding-right:2px;">$</span><input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangea" name="pricerangea" onKeyUp="showValues();getFilters();"/><span style="color:#747474;padding-right:">&nbsp;to&nbsp;</span><span style="color:#747474;padding-right:2px;">$</span><input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangeb" name="pricerangeb" onKeyUp="showValues();getFilters();"/></span>														
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">Percent Off Range:</label><input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangea" name="percentrangea" onKeyUp="showValues();getFilters();" style="margin-top:4px;"/><span style="color:#747474;padding-left:2px;">%</span><span style="color:#747474;">&nbsp;to&nbsp;</span><input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangeb" name="percentrangeb" onKeyUp="showValues();getFilters();"/><span style="color:#747474;padding-left:2px;">%</span></span>
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">On Sale Only:</label><input tabindex="6" type="checkbox" id="onSale" name="onSale" onClick="showValues();getFilters();" style="margin-top:6px;"/></span>
                                    <div style="display:block;"><label class="secondaryLabels" style="width:140px;">Go To:</label><span style="display:inline-block;margin-top:6px"><input tabindex="9" class="viewRadioSC" type="radio" value="merchant" name="prodgoTo" id="prodgoTo" checked="checked"/>Merchant<input tabindex="10" type="radio" value="prodPage" name="prodgoTo" id="prodgoTo"/>ProdPage</span></div>
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">Button Text:</label><input class="prosperTextSC" style="width:170px!important;margin-top:4px;" tabindex="8" type="text" id="prodvisit" name="prodvisit" /></span>       
                                </td>    
                                <td style="vertical-align:middle;">
                                    <span style="display:inline-block;width:120px;"><label class="secondaryLabels" style="">View:</label></span> 
                                    <span style="display:inline-block;width:120px;"><input tabindex="9" class="viewRadioSC" type="radio" value="grid" name="prodview" id="prodview" checked="checked" onClick="showValues();openImageType()"/>Grid</span> 
                                    <span style="display:inline-block;width:120px;"><input tabindex="10" type="radio" value="list" name="prodview" id="prodview" onClick="showValues();openImageType()"/>List/Detail</span>
                                    <span style="display:inline-block;width:120px;"><input tabindex="10" type="radio" value="pc" name="prodview" id="prodview" onClick="showValues();openImageType();"/>Price Comp</span>
                                    <select tabindex="2" id="prodImageType" name="prodImageType" style="font-size:14px;visibility:hidden;">
            						    <option value="original" selected="selected">Original Logo</option>
            						    <option value="white">White Logo</option>
            						    <option value="black">Black Logo</option>
        						    </select>
                                </td>                          
                            </tr>
						</table>
						<div id="prodresultsGoHere" class="mceActionPanel" style="overflow:auto;display:block;height:480px;max-height:480px;border:1px solid #919B9C;background-color:gray;">  				
            				<div class="prodpreview" aria-required="true" style="overflow:auto;"></div>               				 						        
            			</div>  
            			<div id="prosperAddedprod" style="display:block; margin-top:4px;">      			
                            <input type="hidden" id="prodimages" name="prodimages"/>
            			    <div id="sticky-anchor"></div>
    				        <div class="prodadded" style="display:block" aria-required="true"></div> 
        			    </div>					
					</div>
				</div>
    		</div>
    		 	
			<div style="display:block;bottom:4px;overflow:hidden;position:fixed;">		
			    
			    <div id="truePreview" style="visibility:hidden;z-index:-1000;position:relative;overflow:auto;background:gray;height:708px;width:932px;margin-bottom:4px;"></div>
    			<div style="display:inline"><label style="color:red;font-weight:bold;width:auto">Disable on this page/post:</label><input style="margin-top:8px;" tabindex="8" type="checkbox" id="noShow" name="noShow"/></div>                  	    		    
    			<div style="float:right;">
        			<input tabindex="11" type="submit" value="Preview" class="button-primary" id="prosperMCE_preview" onClick="openPreview(this);return false;" style="visibility:visible;"/>
    				<input tabindex="11" type="submit" value="Submit Results" class="button-primary" id="prosperMCE_submit" onClick="javascript:shortCode.insert(shortCode.local_ed);"/>
                </div>
		  	</div>				
		</form>
    </body>
</html>

