<script type="text/javascript">
function createShortCode(){event.preventDefault();var c=jQuery("#prodfetch").val(),d=jQuery("#prodquery").val(),b=jQuery("#prodd").val(),e=jQuery("#prodlimit").val(),f=jQuery("#prodview:checked").val(),h=jQuery("#prodgoTo:checked").val(),k=jQuery("#prodb").val(),l=jQuery("#prodvisit").val(),m=jQuery("#prodid").val(),n=jQuery("#topic").val(),p=jQuery("#useTags").is(":checked"),q=jQuery("#useTitle").is(":checked"),r=jQuery("#onSale").is(":checked"),u=jQuery("#gridimgsz").val(),v=jQuery("#prosperSC").val(),
w=jQuery("#css").val(),x=jQuery("#searchFor:checked").val(),y=jQuery("#sBarText").val(),z=jQuery("#sButtonText").val(),A=jQuery("#pricerangea").val(),B=jQuery("#pricerangeb").val(),C=jQuery("#noShow:checked").val(),D=jQuery("#prodImageType").val(),E=jQuery("#sBarWidth").val(),F=jQuery("#widthStyle:checked").val(),G=jQuery("#percentrangea").val(),H=jQuery("#percentrangeb").val(),I=jQuery("#prodcategory").val(),g=jQuery("#prosperHeldURL").val(),J=window.prodqueryString,a="["+v;d&&(a+=' q="'+d+'"');
h&&(a+=' gtm="'+h+'"');k&&(a+=' b="'+k+'"');b&&(a+=' mid="'+b+'"');e&&(a+=' l="'+e+'"');f&&(a+=' v="'+f+'"');m&&(a+=' id="'+m.replace(/notfound~/g,"")+'"');c&&(a+=' ft="'+c+'"');w&&(a+=' css="'+w+'"');n&&(a+=' q="'+n+'"');p&&(a+=' utg="'+p+'"');q&&(a+=' utt="'+q+'"');r&&(a+=' sale="'+r+'"');x&&(a+=' sf="'+x+'"');y&&(a+=' sbar="'+y+'"');z&&(a+=' sbu="'+z+'"');l&&(a+=' vst="'+l+'"');C&&(a+=' noShow="'+C+'"');D&&(a+=' imgt="'+D+'"');I&&(a+=' cat="'+I+'"');J&&(a+=' fb="'+J+'"');E&&(a+=' w="'+E+'"');F&&
(a+=' ws="'+F+'"');g&&"http://"!=g&&(a+=' ahl="'+g+'"');(A||B)&&(a+=' pr="'+A+","+B+'"');(G||H)&&(a+=' po="'+G+","+H+'"');u&&(a+=' gimgsz="'+u+'"');a+="][/"+v+"]";a.replace(/"/g,"&quot;");jQuery(document.getElementById("shortCodeVal")).val(a);jQuery(document.getElementById("shortCodeBox")).show();jQuery(".wrap").get(0).scrollIntoView(!0)}var t;function getNewCurrent(){return"prod"}
function showValues(c){clearTimeout(t);var d="",d=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div.prodpreview").html(xmlhttp.responseText).show()};c=window.location.pathname;c=c.substring(0,c.lastIndexOf("wp-admin/"))+"wp-content/plugins/prosperent-suite/includes/views/preview.php?type=prod&";xmlhttp.open("GET",c+d,!0);t=setTimeout(function(){try{xmlhttp.send(),d="",getFilters()}catch(b){}},500);d||clearTimeout(t)}
function setFocus(){document.getElementById("prodquery").focus();showValues()}
function openPreview(){jQuery("#truePreview").css("visibility","hidden"==jQuery("#truePreview").css("visibility")?"visible":"hidden");jQuery("#truePreview").css("z-index","10"==jQuery("#truePreview").css("z-index")?"-10":"10");jQuery("#mainFormDiv").css("z-index","1"==jQuery("#mainFormDiv").css("z-index")?"-10":"1");var c="prod",d="",d=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div#truePreview").html(xmlhttp.responseText).show()};var b=window.location.pathname,
c=b.substring(0,b.lastIndexOf("wp-admin/"))+"wp-content/plugins/prosperent-suite/includes/views/truePreview.php?type="+c+"&";xmlhttp.open("GET",c+d,!0);try{xmlhttp.send(),d=""}catch(e){}}
function closeMainPreview(){jQuery("#truePreview").css("visibility","hidden"==jQuery("#truePreview").css("visibility")?"visible":"hidden");jQuery("#truePreview").css("z-index","10"==jQuery("#truePreview").css("z-index")?"-10":"10");jQuery("#mainFormDiv").css("z-index","1"==jQuery("#mainFormDiv").css("z-index")?"-10":"1")}
function getIdofItem(c,d){var b=1==d?c.id.replace("small",""):c.id,e=jQuery(document.getElementById("small"+b)).attr("src")?jQuery(document.getElementById("small"+b)).attr("src"):jQuery(document.getElementById(b)).find("img.newImage").attr("src");if(d)d||(0<=document.getElementById("prodid").value.indexOf(b)?(f=new RegExp(b.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&")+".+?~","g"),window.merchantqueryString=window.merchantqueryString.replace(f,"")):(window.merchantqueryString+=b,document.getElementById("merchantmerchant").value&&
(window.merchantqueryString+="filterMerchant_"+document.getElementById("merchantmerchant").value+"_"),document.getElementById("merchantcategory").value&&(window.merchantqueryString+="filterCategory_"+document.getElementById("merchantcategory").value),window.prodqueryString+="~"));else if(0<=document.getElementById("prodid").value.indexOf(b)){var f=new RegExp(b.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&")+".+?~","g");window.prodqueryString=window.prodqueryString.replace(f,"")}else{window.prodqueryString+=
b;document.getElementById("prodquery").value&&(prodqueryString+="query_"+document.getElementById("prodquery").value+"_");document.getElementById("prodd").value&&(prodqueryString+="filterMerchantId_"+document.getElementById("prodd").value.replace(",","|")+"_");document.getElementById("prodb").value&&(prodqueryString+="filterBrand_"+document.getElementById("prodb").value.replace(",","|")+"_");if(document.getElementById("pricerangea").value||document.getElementById("pricerangeb").value)prodqueryString+=
"filterPrice_"+(document.getElementById("pricerangea").value+","+document.getElementById("pricerangeb").value)+"_";if(document.getElementById("percentrangea").value||document.getElementById("percentrangeb").value)prodqueryString+="filterPercentOff_"+(document.getElementById("percentrangea").value+","+document.getElementById("percentrangeb").value)+"_";!document.getElementById("onSale").checked||document.getElementById("percentrangea").value||document.getElementById("percentrangeb").value||(prodqueryString+=
"filterPriceSale_0.01,_");window.prodqueryString+="~"}"pc"==jQuery("#prodview:checked").val()?0<=document.getElementById("prodid").value.indexOf(b)?(e=document.getElementById("prodimages").value.replace(e,""),jQuery(document.getElementById(b)).removeClass("highlight"),b=document.getElementById("prodid").value.replace(b,""),document.getElementById("prodid").value=b,document.getElementById("prodimages").value=e):(document.getElementById("prodid").value=b,document.getElementById("prodimages").value=
e,jQuery("#productList li").removeClass("highlight"),jQuery(document.getElementById(b)).addClass("highlight")):0<=document.getElementById("prodid").value.indexOf(b+"~")?(e=document.getElementById("prodimages").value.replace(e+"~",""),jQuery(document.getElementById(b)).removeClass("highlight"),b=document.getElementById("prodid").value.replace(b+"~",""),document.getElementById("prodid").value=b,document.getElementById("prodimages").value=e):(document.getElementById("prodid").value+=b+"~",jQuery(document.getElementById(b)).addClass("highlight"),
document.getElementById("prodimages").value+=e+"~");showAddedValues(!0)}
function getFilters(){var c=jQuery("#prodd").val()?jQuery("#prodd").val().replace(",","|"):"",d=jQuery("#prodb").val()?jQuery("#prodb").val().replace(",","|"):"",b=(jQuery("#pricerangea").val()?jQuery("#pricerangea").val()+",":"0.01,")+(jQuery("#pricerangeb").val()?jQuery("#pricerangeb").val():""),e=jQuery("#onSale:checked").val()?"1,":(jQuery("#percentrangea").val()?jQuery("#percentrangea").val()+",":"")+(jQuery("#percentrangeb").val()?jQuery("#percentrangeb").val():"");jQuery.ajax({type:"POST",
url:"http://api.prosperent.com/api/search",data:{api_key:prosperSuiteVars.apiKey,query:jQuery("#prodquery").val(),filterBrand:d,filterPrice:b,filterPercentOff:e,limit:1,enableFacets:"merchantId|merchant",enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(b){jQuery("#prodmerchant").empty();jQuery.each(b.facets.merchantId,function(d,e){c.match(e.value)?jQuery("#prodmerchant").append('<li id="d'+e.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+
b.facets.merchant[d].value+"</span></a></li>"):jQuery("#prodmerchant").append('<li id="d'+e.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+b.facets.merchant[d].value+"</span></a></li>")})},error:function(){alert("Failed to load data.")}});jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:prosperSuiteVars.apiKey,query:jQuery("#prodquery").val(),filterMerchantId:c,filterBrand:d,filterPrice:b,filterPercentOff:e,limit:1,enableFacets:"brand",
enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(b){jQuery("#prodbrand").empty();jQuery.each(b.facets.brand,function(b,c){d.match(c.value)?jQuery("#prodbrand").append('<li id="b'+c.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+c.value+"</span></a></li>"):jQuery("#prodbrand").append('<li id="b'+c.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+c.value+
"</span></a></li>")})},error:function(){alert("Failed to load data.")}})}function getIdValue(c,d){var b=c.id,e=b.slice(0,1),f=b.slice(1);0<=document.getElementById("prod"+e).value.indexOf(f+",")?(jQuery("#"+b).removeClass("activeFilter"),b=document.getElementById("prod"+e).value.replace(f+",",""),document.getElementById("prod"+e).value=b):(document.getElementById("prod"+e).value+=f+",",jQuery("#"+b).addClass("activeFilter"));showValues()}
function showAddedValues(c){var d="",d=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div.prodadded").html(xmlhttp.responseText).show()};var b=window.location.pathname,b=b.substring(0,b.lastIndexOf("wp-admin/"))+"wp-content/plugins/prosperent-suite/includes/views/added.php?type=prod&";xmlhttp.open("GET",b+d,c);xmlhttp.send()}
function sticky_relocate(){var c=jQuery(window).scrollTop(),d=jQuery("#sticky-anchor").offset().top;c>d?jQuery("#stickyHeader").addClass("sticky"):jQuery("#stickyHeader").removeClass("sticky")}jQuery(function(){window.prodqueryString="";window.merchantqueryString="";document.getElementById("apiKey").value=prosperSuiteVars.apiKey;jQuery(window).keydown(function(c){if(13==c.keyCode)return c.preventDefault(),!1});jQuery(window).scroll(sticky_relocate);sticky_relocate()});
function openImageType(){jQuery("#prodimages").val("");jQuery("#prodid").val("");showAddedValues();showValues();"pc"==jQuery("#prodview:checked").val()?(jQuery(document.getElementById("prosperInsertLimit")).hide(),jQuery("#prodImageType").css("visibility","visible")):(jQuery(document.getElementById("prosperInsertLimit")).show(),jQuery("#prodImageType").css("visibility","hidden"))};
</script>
<?php 
require_once(PROSPER_MODEL . '/Admin.php');
$prosperAdmin = new Model_Admin();

$prosperAdmin->adminHeader( __( 'Search Products', 'prosperent-suite' ), false);
echo '<div style="font-weight:bold;font-size:16px;">Search for products, choose a view format, and paste the short code into a post or page.<div style="padding-top:6px;">You’ll make money on any products clicked and purchased!</div></div>';

?>
<body id="inserter" role="application" aria-labelledby="app_label" onload="setFocus();showAddedValues();showValues();">	
    <div id="shortCodeBox" style="display:none;font-size:18px;font-weight:bold;">Copy This into a Page or Post:<br><br>
        <input id="shortCodeVal" readonly type="text" onClick="this.select();" style="width:100%;padding:8px 6px;background-color:white;border:1px solid #000;font-weight:normal;">
    </div><br>
    <div id="mainFormDiv" style="display:block;position:relative;z-index:1;width:100%;">
    <form action="/" method="get" id="prosperSCForm">    	
	    <input type="hidden" id="apiKey" name="apiKey"/>
	    <input type="hidden" id="createPI" name="createPI" value="true"/>
	    <input type="hidden" id="prosperSC" value="prosperInsert"/>
		<input type="hidden" id="prodid" name="prodid"/>
		<input type="hidden" id="prodd" name="prodd"/>
		<input type="hidden" id="prodb" name="prodb"/>			
		<input type="hidden" name="prodfetch" id="prodfetch" value="fetchProducts"/>						
		<label><strong>Search Products:</strong></label><input class="prosperMainTextSC" tabindex="1" type="text" name="prodq" id="prodquery" onKeyUp="showValues();" placeholder="Please enter a search term"/>						
		<table>
			<tr>
				<td style="vertical-align:top;width:200px;"><div style="display:block;"><label class="secondaryLabels" style="width:70px;">Merchant:</label><ul class="prosperSelect" id="prodmerchant" style="background-color:white;"></ul></div></td>
                <td style="vertical-align:top;width:200px;"><div style="display:block;"><label class="secondaryLabels" style="width:50px;">Brand:</label><ul class="prosperSelect" id="prodbrand" style="background-color:white;"></ul></div></td>						
                <td style="width:290px;vertical-align:middle;">
                    <span><label class="secondaryLabels" style="width:105px;">Price Range:</label><span style="color:#747474;padding-right:2px;">$</span><input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangea" name="pricerangea" onKeyUp="showValues();getFilters();" style="margin-top:2px;"/><span style="color:#747474;padding-right:">&nbsp;to&nbsp;</span><span style="color:#747474;padding-right:2px;">$</span><input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangeb" name="pricerangeb" onKeyUp="showValues();getFilters();" style="margin-top:2px;"/></span>														
                    <span><label class="secondaryLabels" style="width:105px;">Percent Off:</label><input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangea" name="percentrangea" onKeyUp="showValues();getFilters();" style="margin-top:4px;"/><span style="color:#747474;padding-left:2px;">%</span><span style="color:#747474;">&nbsp;to&nbsp;</span><input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangeb" name="percentrangeb" onKeyUp="showValues();getFilters();" style="margin-top:4px;"/><span style="color:#747474;padding-left:2px;">%</span></span>
                    <span><label class="secondaryLabels" style="width:105px;">On Sale Only:</label><input tabindex="6" type="checkbox" id="onSale" name="onSale" onClick="showValues();getFilters();" style="margin-top:6px;"/></span>
                    <div><label class="secondaryLabels" style="width:105px;">Go To:</label><span style="display:inline-block;margin-top:6px;"><input tabindex="9" class="viewRadioSC" type="radio" value="merchant" name="prodgoTo" id="prodgoTo" checked="checked"/>Merchant<input style="margin-left:4px;" tabindex="10" type="radio" value="prodPage" name="prodgoTo" id="prodgoTo"/>Product Page</span></div>
                    <div><label class="secondaryLabels" style="width:105px;">Button Text:</label><input class="prosperTextSC" style="height:auto;font-size:14px;width:170px;margin-top:4px;" placeholder="Visit Store" tabindex="8" type="text" id="prodvisit" name="prodvisit"/></div>                                                          
                </td>    
                <td style="vertical-align:middle;">                                                                                   
                    <span style="display:block;" id="prosperInsertLimit"><label class="secondaryLabels" style="width:50px;">Limit:</label><input class="prosperTextSC" style="height:auto;font-size:14px;width:75px;margin-top:4px;" tabindex="8" type="text" id="prodlimit" name="prodlimit"/></span>
                    <div>
                        <span><label style="width:50px;" class="secondaryLabels">View:</label> 
                        <input tabindex="9" style="margin-top:6px;" class="viewRadioSC" type="radio" value="grid" name="prodview" id="prodview" checked="checked" onClick="openImageType();"/>Grid</span> 
                        <div><input style="margin-left:50px;" tabindex="10" type="radio" value="list" name="prodview" id="prodview" onClick="openImageType();"/>List/Detail</div>
                        <div><input style="margin-left:50px;" tabindex="10" type="radio" value="pc" name="prodview" id="prodview" onClick="openImageType();"/>Price Comparison</div>
                    </div>
                    <select tabindex="2" id="prodImageType" name="prodImageType" style="font-size:14px;visibility:hidden;width:120px;margin-left:50px!important;">
					    <option value="original" selected="selected">Original Logo</option>
					    <option value="white">White Logo</option>
					    <option value="black">Black Logo</option>
				    </select>
				    <div style="float:right;z-index:1001;">
            			<input tabindex="11" type="submit" value="Preview" class="button-primary" id="prosperMCE_preview" onClick="openPreview(this);return false;"/>
            			<input tabindex="11" type="submit" value="Create Short Code" class="button-primary" id="prosperMCE_submit" onClick="createShortCode();return false;"/>
                    </div>
                </td>                          
            </tr>                            
		</table>
		<div id="prosperAddedprod" style="margin-bottom:5px;">      			
            <input type="hidden" id="prodimages" name="prodimages"/>
		    <div id="sticky-anchor"></div>
	        <div class="prodadded" aria-required="true"></div> 
	    </div>	
		<div id="prodresultsGoHere" class="mceActionPanel" style="overflow:auto;height:500px;max-height:500px;border:1px solid #919B9C;background-color:#fff;">  				
			<div class="prodpreview" aria-required="true" style="overflow:auto;"></div>             				 						        
		</div> 
    </form>
</div>
    <div id="truePreview" style="position:absolute;top:145px;left:-1px;height:800px;width:955px;visibility:hidden;z-index:-10;overflow:auto;background-color:#fff;border:1px solid #000;"></div>
</body>