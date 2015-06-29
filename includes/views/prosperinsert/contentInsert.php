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
		var screenHeight=725>jQuery(window).height()?600:750,t;function getNewCurrent(){var b;jQuery("#products_tab").hasClass("current")?b="prod":b="merchant";return b}
		function showValues(){var b=getNewCurrent();clearTimeout(t);var d="",d=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div."+b+"preview").html(xmlhttp.responseText).show()};var a=window.location.pathname,a=a.substring(0,a.lastIndexOf("prosperinsert/"))+"preview.php?type="+b+"&";xmlhttp.open("GET",a+d,!0);t=setTimeout(function(){try{xmlhttp.send(),d=""}catch(a){}},500);d||clearTimeout(t)}
		function setFocus(){"prod"==getNewCurrent()?document.getElementById("prodquery").focus():document.getElementById("merchantmerchant").focus();top.tinymce.activeEditor.windowManager.getParams()||shortCode.local_ed.selection.getContent()&&!shortCode.local_ed.selection.getContent().match(/(<([^>]+)>)/ig)&&(document.getElementById("prodquery").value=shortCode.local_ed.selection.getContent()?shortCode.local_ed.selection.getContent():"shoes",showValues())}
		function openPreview(){jQuery("#truePreview").css("visibility","hidden"==jQuery("#truePreview").css("visibility")?"visible":"hidden");jQuery("#truePreview").css("z-index","1000"==jQuery("#truePreview").css("z-index")?"-1000":"1000");jQuery("#mainFormDiv").css("z-index","1"==jQuery("#mainFormDiv").css("z-index")?"-1000":"1");jQuery("#prosperMCE_preview").prop("value","Preview"==jQuery("#prosperMCE_preview").prop("value")?"Close Preview":"Preview");var b=getNewCurrent(),d="",d=jQuery("form").serialize();
		xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div#truePreview").html(xmlhttp.responseText).show()};var a=window.location.pathname,b=a.substring(0,a.lastIndexOf("prosperinsert/"))+"truePreview.php?type="+b+"&";xmlhttp.open("GET",b+d,!0);try{xmlhttp.send(),d=""}catch(c){}}
		function getIdofItem(b,d){var a=getNewCurrent(),c=1==d?b.id.replace("small",""):b.id,e=jQuery("#small"+c).attr("src")?jQuery("#small"+c).attr("src"):jQuery("#"+c).find("img.newImage").attr("src");"pc"==jQuery("#prodview:checked").val()?0<=document.getElementById(a+"id").value.indexOf(c)?(jQuery("#"+c).removeClass("highlight"),c=document.getElementById(a+"id").value.replace(c,""),document.getElementById(a+"id").value=c):(document.getElementById(a+"id").value=c,jQuery("#productList li").removeClass("highlight"),
		jQuery("li#"+c).addClass("highlight")):(0<=document.getElementById(a+"id").value.indexOf(c+",")?(e=document.getElementById(a+"images").value.replace(e+",",""),jQuery("#"+c).removeClass("highlight"),c=document.getElementById(a+"id").value.replace(c+",",""),document.getElementById(a+"id").value=c,document.getElementById(a+"images").value=e):(document.getElementById(a+"id").value+=c+",",jQuery("#"+c).addClass("highlight"),document.getElementById(a+"images").value+=e+","),showAddedValues(),jQuery("#"+
		a+"resultsGoHere").css("height",document.getElementById(a+"images").value?600==screenHeight?"233px":"380px":600==screenHeight?"337px":"480px"))}
		function getFilters(){var b=jQuery("#prodd").val()?jQuery("#prodd").val().replace(",","|"):"",d=jQuery("#prodb").val()?jQuery("#prodb").val().replace(",","|"):"";pRange=(jQuery("#pricerangea").val()?jQuery("#pricerangea").val()+",":"0.01,")+(jQuery("#pricerangeb").val()?jQuery("#pricerangeb").val():"");perRange=jQuery("#onSale:checked").val()?"1,":(jQuery("#percentrangea").val()?jQuery("#percentrangea").val()+",":"")+(jQuery("#percentrangeb").val()?jQuery("#percentrangeb").val():"");jQuery.ajax({type:"POST",
		url:"http://api.prosperent.com/api/search",data:{api_key:"fc91d36b383ca0231ee59c5048eabedc",query:jQuery("#prodquery").val(),filterBrand:d,filterPrice:pRange,filterPercentOff:perRange,limit:1,enableFacets:"merchantId|merchant",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){jQuery("#prodmerchant").empty();jQuery.each(a.facets.merchantId,function(c,d){b.match(d.value)?jQuery("#prodmerchant").append('<li id="d'+d.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+
		a.facets.merchant[c].value+"</span></a></li>"):jQuery("#prodmerchant").append('<li id="d'+d.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.facets.merchant[c].value+"</span></a></li>")})},error:function(){alert("Failed to load data.")}});jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:"fc91d36b383ca0231ee59c5048eabedc",query:jQuery("#prodquery").val(),filterMerchantId:b,filterBrand:d,filterPrice:pRange,filterPercentOff:perRange,
		limit:1,enableFacets:"brand",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){jQuery("#prodbrand").empty();jQuery.each(a.facets.brand,function(a,b){d.match(b.value)?jQuery("#prodbrand").append('<li id="b'+b.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+b.value+"</span></a></li>"):jQuery("#prodbrand").append('<li id="b'+b.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+
		b.value+"</span></a></li>")})},error:function(){alert("Failed to load data.")}})}function getIdValue(b,d){var a=b.id,c=a.slice(0,1),e=a.slice(1);0<=document.getElementById("prod"+c).value.indexOf(e+",")?(jQuery("#"+a).removeClass("activeFilter"),a=document.getElementById("prod"+c).value.replace(e+",",""),document.getElementById("prod"+c).value=a):(document.getElementById("prod"+c).value+=e+",",jQuery("#"+a).addClass("activeFilter"));showValues()}
		function showAddedValues(){var b=getNewCurrent(),d="",d=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div."+b+"added").html(xmlhttp.responseText).show()};var a=window.location.pathname,a=a.substring(0,a.lastIndexOf("prosperinsert/"))+"added.php?type="+b+"&";xmlhttp.open("GET",a+d,!0);xmlhttp.send()}
		function sticky_relocate(){var b=jQuery(window).scrollTop(),d=jQuery("#sticky-anchor").offset().top;b>d?jQuery("#stickyHeader").addClass("sticky"):jQuery("#stickyHeader").removeClass("sticky")}
		jQuery(function(){jQuery(window).scroll(sticky_relocate);sticky_relocate();var b=top.tinymce.activeEditor.windowManager.getParams();if(b){var d=jQuery("<i "+b+">").attr("id"),a=jQuery("<i "+b+">").attr("mid"),c=jQuery("<i "+b+">").attr("b"),e=jQuery("<i "+b+">").attr("sale"),g=jQuery("<i "+b+">").attr("pr"),h=jQuery("<i "+b+">").attr("po"),k=jQuery("<i "+b+">").attr("q"),l=jQuery("<i "+b+">").attr("gtm"),p=jQuery("<i "+b+">").attr("noShow"),f=jQuery("<i "+b+">").attr("v"),m=jQuery("<i "+b+">").attr("vst"),
		n=jQuery("<i "+b+">").attr("cat");jQuery("<i "+b+">").attr("imgt");"undefined"!=typeof a&&null!==a&&(document.getElementById("prodd").value=a);"undefined"!=typeof k&&null!==k&&(document.getElementById("prodquery").value=k);"undefined"!=typeof c&&null!==c&&(document.getElementById("prodb").value=c);"undefined"!=typeof n&&null!==n&&(document.getElementById("merchantcategory").value=n);"undefined"!=typeof m&&null!==m&&(document.getElementById("prodvisit").value=m);"undefined"!=typeof g&&null!==g&&(d=
		g.split(","),document.getElementById("pricerangea").value=d[0],document.getElementById("pricerangeb").value=d[1]);"undefined"!=typeof e&&null!==e&&jQuery("input[name=onSale]").attr("checked",!0);"undefined"!=typeof f&&null!==f&&jQuery("input[name=prodview][value="+f+"]").attr("checked",!0);"pc"==f&&openImageType();"undefined"!=typeof h&&null!==h&&(e=h.split(","),document.getElementById("percentrangea").value=e[0],document.getElementById("percentrangeb").value=e[1]);"undefined"!=typeof l&&null!==l&&
		jQuery("input[name=prodgoTo][value="+l+"]").attr("checked",!0);"undefined"!=typeof p&&null!==p&&jQuery("input[name=noShow]").attr("checked",!0)}jQuery(window).keydown(function(a){if(13==a.keyCode)return a.preventDefault(),!1});jQuery("#prodresultsGoHere").css("height",600==screenHeight?"337px":"480px");jQuery("#truePreview").css("height",600==screenHeight?"558px":"708px")});
		function openImageType(){"pc"==jQuery("#prodview:checked").val()?(jQuery("#prosperAddedprod").css("display","none"),jQuery("#prodImageType").css("visibility","visible")):(jQuery("#prosperAddedprod").css("display","block"),jQuery("#prodImageType").css("visibility","hidden"))};
		</script>
    </head>
    <base target="_self" />
    <body id="inserter" role="application" aria-labelledby="app_label" onload="setFocus();showValues();getFilters()">			
		<form action="/" method="get" id="prosperSCForm">
			<div id="mainFormDiv" style="display:block;position:relative;z-index:1;width:100%;">
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

