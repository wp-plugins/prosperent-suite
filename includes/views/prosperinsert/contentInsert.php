

<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>ContentInsert</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=214'; ?>">		
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=3.4.3'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/utils/mctabs.js'; ?>"></script>

		<script type="text/javascript">
		function editInsert () {
			console.log(shortCode.local_ed.selection.getContent());

		}
		
		var t;
		function showValues() {			
		    var b = getNewCurrent();
		    clearTimeout(t);
		    var c = "",
		        c = jQuery("form").serialize();
		    xmlhttp = new XMLHttpRequest;
		    xmlhttp.onreadystatechange = function() {
		        jQuery("div." + b + "preview").html(xmlhttp.responseText).show()
		    };
		    var d = window.location.pathname,
		        e = d.substring(0, d.lastIndexOf("prosperinsert/")) + "preview.php?type=" + b + "&";
		    xmlhttp.open("GET", e + c, !0);
		    t = setTimeout(function() {
		        try {
		            xmlhttp.send(), c = "";
		        } catch (a) {}
		    }, 500);
		    c || clearTimeout(t)
		}

		function setFocus() {
			var d = getNewCurrent();			
			(d == 'prod') ? document.getElementById("prodquery").focus() : document.getElementById("merchantmerchant").focus();
		    shortCode.local_ed.selection.getContent() && !shortCode.local_ed.selection.getContent().match(/(<([^>]+)>)/ig) && (document.getElementById("prodquery").value = (shortCode.local_ed.selection.getContent() ? shortCode.local_ed.selection.getContent() : 'shoes'), document.getElementById("merchantmerchant").value = (shortCode.local_ed.selection.getContent() ? shortCode.local_ed.selection.getContent() : 'Backcountry'), showValues())
		}

		function openPreview() 
		{
		    jQuery("#truePreview").css('visibility', jQuery("#truePreview").css('visibility') == 'hidden' ? 'visible' : 'hidden');
		    jQuery("#truePreview").css('z-index', jQuery("#truePreview").css('z-index') == '1000' ? '-1000' : '1000');
		    jQuery("#mainFormDiv").css('z-index', jQuery("#mainFormDiv").css('z-index') == '1' ? '-1000' : '1');
		    jQuery("#prosperMCE_preview").prop('value', jQuery("#prosperMCE_preview").prop('value') == 'Preview' ? 'Close Preview' : 'Preview');

		    var b = getNewCurrent(),
    		    c = "",
    		    c = jQuery("form").serialize();
    	    xmlhttp = new XMLHttpRequest;
    	    xmlhttp.onreadystatechange = function() {
    	        jQuery("div#truePreview").html(xmlhttp.responseText).show()
    	    };
    	    var d = window.location.pathname,
    	        b = d.substring(0, d.lastIndexOf("prosperinsert/")) + "truePreview.php?type=" + b + "&";
    	    xmlhttp.open("GET", b + c, !0);
    	    
	        try {
	            xmlhttp.send(), c = ""
	        } catch (a) {};;
		}

		function getIdofItem(b, c) 
		{
		    var d = getNewCurrent(),		        
		        a = (!0 == c ? b.id.replace("small", "") : b.id),
		        e = (jQuery("#small" + a).attr("src") ? jQuery("#small" + a).attr("src") :  jQuery("#" + a).find("img.newImage").attr("src")),
		        v = jQuery("#prodview:checked").val();

		    if (v == 'pc')
		    {
		    	0 <= document.getElementById(d + "id").value.indexOf(a) ? (jQuery("#" + a).removeClass("highlight"), a = document.getElementById(d + "id").value.replace(a, ""), document.getElementById(d + "id").value = a) : (document.getElementById(d + "id").value = a, jQuery('#productList li').removeClass('highlight'),jQuery("li#" + a).addClass("highlight"));
		    }
		    else
		    {		    
		    	  0 <= document.getElementById(d + "id").value.indexOf(a + ",") ? (e = document.getElementById(d + "images").value.replace(e + ",", ""), jQuery("#" + a).removeClass("highlight"), a = document.getElementById(d + "id").value.replace(a + ",", ""), document.getElementById(d + "id").value = a, document.getElementById(d + "images").value = e) : (document.getElementById(d + "id").value += a + ",", jQuery("#" + a).addClass("highlight"), document.getElementById(d + "images").value += e + ",");
		    	  showAddedValues();

		    	  jQuery("#" + d + "resultsGoHere").css('height', document.getElementById(d + "images").value ? '380px' : '480px');	
		    }	    
		}

		function getFilters () {		
			var merchants = (jQuery("#prodd").val() ? jQuery("#prodd").val().replace(',','|') : ''),
			    brands    = (jQuery("#prodb").val() ? jQuery("#prodb").val().replace(',','|') : '')
                pRange    = (jQuery('#pricerangea').val() ? jQuery('#pricerangea').val() + ',' : '0.01,') + (jQuery('#pricerangeb').val() ? jQuery('#pricerangeb').val() : '')
                perRange  = (jQuery("#onSale:checked").val() ? '1,' : (jQuery('#percentrangea').val() ? jQuery('#percentrangea').val() + ',' : '') + (jQuery('#percentrangeb').val() ? jQuery('#percentrangeb').val() : ''));
                
    	    jQuery.ajax({
	            type: "POST",
	            url: "http://api.prosperent.com/api/search",
	            data: {
	                api_key: "fc91d36b383ca0231ee59c5048eabedc",
	                query: jQuery("#prodquery").val(),
	                filterMerchantId:merchants,
	                filterBrand:brands,
	                filterPrice:pRange,
	                filterPercentOff:perRange,
	                limit: 1,
	                enableFacets: "merchantId|merchant|brand",
	                enableFullData: 0
	            },
	            contentType: "application/json; charset=utf-8",
	            dataType: "jsonp",
	            success: function(a) {
	            	jQuery("#prodbrand").empty();
	    			jQuery("#prodmerchant").empty();	    			
	                jQuery.each(a.facets.merchantId, function(c, b) {		                
		                if (merchants.match(b.value))
		                {
		                	  jQuery("#prodmerchant").append('<li id="d'+b.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.facets.merchant[c].value+'</span></a></li>');
		                }
		                else
		                {
		                	jQuery("#prodmerchant").append('<li id="d'+b.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+a.facets.merchant[c].value+'</span></a></li>');
		                }                
	                }),
	                jQuery.each(a.facets.brand, function(c, b) {	
	                	if (brands.match(b.value))
		                {	                			                
	                	    jQuery("#prodbrand").append('<li id="b'+b.value+'" class="activeFilter" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+b.value+'</span></a></li>');
		                }
		                else
		                {
		                	jQuery("#prodbrand").append('<li id="b'+b.value+'" onClick="getIdValue(this);getFilters();"><a href="javascript:void(0);"><span>'+b.value+'</span></a></li>');
		                } 
	                })
	            },
	            error: function() {
	                alert("Failed to load data.")
	            }
    	    });		    
		}

		function getIdValue(b, c) 
		{
			var a = b.id,       
			    d = a.slice(0,1),
			    e = a.slice(1);	    
			0 <= document.getElementById("prod" + d).value.indexOf(e + ",") ? (jQuery("#" + a).removeClass("activeFilter"), a = document.getElementById("prod" + d).value.replace(e + ",", ""), document.getElementById("prod" + d).value = a) : (document.getElementById("prod" + d).value += e + ",", jQuery("#" + a).addClass("activeFilter"));
			showValues();						 
		}
		
		function showAddedValues() {
			
		    var b = getNewCurrent(),
		        c = "",
		        c = jQuery("form").serialize();
		    xmlhttp = new XMLHttpRequest;
		    xmlhttp.onreadystatechange = function() {
		        jQuery("div."+b+"added").html(xmlhttp.responseText).show()
		    };
		    var d = window.location.pathname,
		        e = d.substring(0, d.lastIndexOf("prosperinsert/")) + "added.php?type=" + b + "&";
		    xmlhttp.open("GET", e + c, !0);
		    xmlhttp.send()
		}
		
		function sticky_relocate() {
		    var b = jQuery(window).scrollTop(),
		        c = jQuery("#sticky-anchor").offset().top;
		    b > c ? jQuery("#stickyHeader").addClass("sticky") : jQuery("#stickyHeader").removeClass("sticky")
		}
		
		jQuery(function() {
		    jQuery(window).scroll(sticky_relocate);
		    sticky_relocate();

			var height = ((jQuery(window).height() < 950) ? 675 : 750);
		    jQuery("#prodresultsGoHere").css('height', (height == 675 ? '400px' : '480px'));
		    jQuery("#merchantresultsGoHere").css('height', (height == 675 ? '400px' : '480px'));
		    jQuery("#truePreview").css('height', (height == 675 ? '635px' : '708px'));
		});

		function openImageType() {
			var view = jQuery("#prodview:checked").val();
			if (view == 'pc')
			{
				jQuery("#prosperAddedprod").css('display', 'none');				
				jQuery("#prodImageType").css('visibility', 'visible');
			}
			else
			{
				jQuery("#prosperAddedprod").css('display', 'block');
				jQuery("#prodImageType").css('visibility', 'hidden');
			}
		}	
		</script>
    </head>
    <base target="_self" />
    <body id="inserter" role="application" aria-labelledby="app_label" onload="setFocus();showValues();editInsert();getFilters()">			
		<form action="/" method="get" id="prosperSCForm">
			<div id="mainFormDiv" style="display:block;position:relative;z-index:1;width:100%;">
			    <input type="hidden" id="prosperSC" value="contentInsert"/>
				<div class="tabs">
					<ul>
						<li id="products_tab" aria-controls="products_panel" class="current"><span><a href="javascript:;" onClick="mcTabs.displayTab('products_tab','products_panel');setFocus();showValues();" onmousedown="return false;">Products</a></span></li>						
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
        						<td style="vertical-align:top;width:215px;"><div style="display:block;padding-right:10px;"><label class="secondaryLabels" style="width:70px;">Merchant:</label><ul class="prosperSelect" id="prodmerchant"></ul></div></td>
                                <td style="vertical-align:top;width:215px;"><div style="display:block;padding-right:10px;"><label class="secondaryLabels" style="width:50px;">Brand:</label><ul class="prosperSelect" id="prodbrand"></ul></div></td>						
                                <td style="width:330px;vertical-align:middle;">
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">Price Range:</label><span style="color:#747474;padding-right:2px;">$</span><input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangea" name="pricerangea" onKeyUp="showValues();getFilters();"/><span style="color:#747474;padding-right:">&nbsp;to&nbsp;</span><span style="color:#747474;padding-right:2px;">$</span><input class="prosperShortTextSC" tabindex="4" type="text" id="pricerangeb" name="pricerangeb" onKeyUp="showValues();getFilters();"/></span>														
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">Percent Off Range:</label><input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangea" name="percentrangea" onKeyUp="showValues();getFilters();"/><span style="color:#747474;padding-left:2px;">%</span><span style="color:#747474;padding-right:">&nbsp;to&nbsp;</span><input class="prosperShortTextSC" tabindex="4" type="text" id="percentrangeb" name="percentrangeb" onKeyUp="showValues();getFilters();"/><span style="color:#747474;padding-left:2px;">%</span></span>
                                    <span style="display:inline-block;"><label class="secondaryLabels" style="width:140px;">On Sale Only:</label><input tabindex="6" type="checkbox" id="onSale" name="onSale" onClick="showValues();getFilters();"/></span>
                                               
                                </td>    
                                <td style="vertical-align:middle;">
                                    <span style="display:inline-block;width:130px;"><label class="secondaryLabels" style="">View:</label></span> 
                                    <span style="display:inline-block;width:130px;"><input tabindex="9" class="viewRadioSC" type="radio" value="grid" name="prodview" id="prodview" checked="checked" onClick="showValues();openImageType()"/>Grid</span> 
                                    <span style="display:inline-block;width:130px;"><input tabindex="10" type="radio" value="list" name="prodview" id="prodview" onClick="showValues();openImageType()"/>List/Detail</span>
                                    <span style="display:inline-block;width:130px;"><input tabindex="10" type="radio" value="pc" name="prodview" id="prodview" onClick="showValues();openImageType();"/>Price Comparison</span>
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
					
					<div id="merchant_panel" class="panel">				
						<input type="hidden" id="merchantid" name="merchantid"/>		
						<input type="hidden" name="merchantfetch" id="merchantfetch" value="fetchMerchant"/>															
						<div style="margin-bottom:4px;"><label><strong>Search By Merchant:</strong></label><input class="prosperMainTextSC" value="Backcountry" tabindex="5" type="text" id="merchantmerchant" name="merchantm"  onKeyUp="showValues();"/></div>
						<div style="margin-bottom:4px;"><label><strong>Search By Category:</strong></label><input class="prosperMainTextSC" tabindex="5" type="text" id="merchantcategory" name="merchantcat"  onKeyUp="showValues();"/></div>														
						<div style="margin-bottom:12px;font-size:16px;">
						    <label class="secondaryLabels" style="width:182px;font-size:16px;">Image Type:</label>
						    <select tabindex="2" id="imageType" name="imageType" onChange="showValues();" style="font-size:16px;">
    						    <option value="original" selected="selected">Original Logo</option>
    						    <option value="white">White Logo</option>
    						    <option value="black">Black Logo</option>
						    </select>
						</div>
						<div id="merchantresultsGoHere" class="mceActionPanel" style="overflow:auto;display:block;height:480px!important;border:1px solid #919B9C;background-color:gray">			
            				<div class="merchantpreview" aria-required="true" style="overflow:auto;"></div>    	
            			</div> 
            			<div id="prosperAddedmerchant" style="display:block; margin-top:4px;">      			
                            <input type="hidden" id="merchantimages" name="merchantimages"/>
            			    <div id="sticky-anchor"></div>
    				        <div class="merchantadded" style="display:block" aria-required="true"></div> 
        			    </div>  								
					</div>
				</div>
			

    		</div>
    		 	
			<div style="display:block;bottom:4px;overflow:hidden;position:fixed;">			    		    
			    <div id="truePreview" style="visibility:hidden;z-index:-1000;position:relative;overflow:auto;background:gray;height:708px;width:932px;margin-bottom:4px;"></div>
    			<div style="float:right;">
        			<input tabindex="11" type="submit" value="Preview" class="button-primary" id="prosperMCE_preview" onClick="openPreview(this);return false;" style="visibility:visible;"/>
    				<input tabindex="11" type="submit" value="Submit Results" class="button-primary" id="prosperMCE_submit" onClick="javascript:shortCode.insert(shortCode.local_ed);"/>
                </div>
		  	</div>				
		</form>
    </body>
</html>

