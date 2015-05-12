<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<html>
    <head>
        <title>Auto-Linker</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=3.4.4'; ?>">
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/utils/mctabs.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=3.3.3'; ?>"></script>
		<script type="text/javascript">		
			var t;function showValues(){var b=getNewCurrent();clearTimeout(t);var c="",c=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div.preview").html(xmlhttp.responseText).show()};var d=window.location.pathname,b=d.substring(0,d.lastIndexOf("prosperlinker/"))+"preview.php?type="+b+"&";xmlhttp.open("GET",b+c,!0);t=setTimeout(function(){try{xmlhttp.send(),c=""}catch(a){}},500);c||clearTimeout(t)}
			function showAddedValues(){var b=getNewCurrent(),c="",c=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div.added").html(xmlhttp.responseText).show()};var d=window.location.pathname,b=d.substring(0,d.lastIndexOf("prosperlinker/"))+"added.php?type="+b+"&";xmlhttp.open("GET",b+c,!0);xmlhttp.send()}
			function setFocus(){document.getElementById("prodquery").focus();shortCode.local_ed.selection.getContent()&&(document.getElementById("prodquery").value=shortCode.local_ed.selection.getContent(),document.getElementById("coupquery").value=shortCode.local_ed.selection.getContent(),showValues())}
			function getIdofItem(b,c){var d=getNewCurrent(),a;a=!0==c?b.id.replace("small",""):b.id;console.log(a);var e=jQuery("#"+a).find("img.newImage").attr("src"),f=jQuery("#"+a).find("img.newImage").attr("alt");jQuery("#stickyHeader").find("#"+a).attr("src");0<=document.getElementById(d+"id").value.indexOf(a+",")?(jQuery("#"+a).removeClass("highlight"),a=document.getElementById(d+"id").value.replace(a+",",""),e=document.getElementById("images").value.replace(e+",",""),f=document.getElementById("keywords").value.replace(f+
			",",""),document.getElementById(d+"id").value=a,document.getElementById("images").value=e,document.getElementById("keywords").value=f):(document.getElementById(d+"id").value+=a+",",document.getElementById("images").value+=e+",",document.getElementById("keywords").value+=f+",",jQuery("#"+a).addClass("highlight"));/*showAddedValues()*/}
			function sticky_relocate(){var b=jQuery(window).scrollTop(),c=jQuery("#sticky-anchor").offset().top;b>c?jQuery("#stickyHeader").addClass("sticky"):jQuery("#stickyHeader").removeClass("sticky")}jQuery(function(){jQuery(window).scroll(sticky_relocate);sticky_relocate()});
		</script>
    </head>
    <base target="_self" />
    <body id="linker" role="application" aria-labelledby="app_label" onload="setFocus();showValues();">
		<form action="/" method="get" id="prosperSCForm">
            <div style="width:34%;float:left;position:fixed;display:block;">
			    <input type="hidden" id="prosperSC" value="linker"/>
    			<div class="tabs">
    				<ul>
    					<li id="products_tab" aria-controls="products_panel" class="current"><span><a href="javascript:;" onclick="mcTabs.displayTab('products_tab','products_panel');setFocus();showValues();" onmousedown="return false;">Products</a></span></li>
    					<li id="coupons_tab" aria-controls="coupons_panel"><span><a href="javascript:;" onclick="mcTabs.displayTab('coupons_tab','coupons_panel');setFocus();showValues();" onmousedown="return false;">Coupons</a></span></li>
    					<li id="local_tab" aria-controls="local_panel"><span><a href="javascript:;" onclick="javascript:mcTabs.displayTab('local_tab','local_panel');setFocus();showValues();" onmousedown="return false;">Local</a></span></li>    					
    				</ul>
    			</div>
    			<div class="panel_wrapper">
    				<div id="products_panel" class="panel current">
    					<fieldset style="font-size:13px;">
    						<legend>Product Linker</legend>
    						<input type="hidden" name="prodfetch" id="prodfetch" value="fetchProducts"/>
    						<p><label>Query:</label><input class="prosperTextSC" tabindex="1" type="text" name="prodq" id="prodquery" onKeyUp="showValues();" value="shoes" placeholder="shoes"/><a href="#" class="tooltip"><span>The query that is  used for the search</span></a></p>
    						<p><label>Merchant:</label><input class="prosperTextSC" tabindex="2" type="text" id="prodmerchant" name="prodm"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name.</span></a></p>
    						<p><label>Brand:</label><input class="prosperTextSC" tabindex="3" type="text" id="prodbrand" name="prodb"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.</span></a></p>
    						<p><label>Country:</label><select tabindex="4" id="country" name="country" onChange="showValues();"><option value="US" selected="selected">US</option><option value="UK">UK</option><option value="CA">Canada</option></select><a href="#" class="tooltip"><span>Choose a country to choose the catalog to pull products from.</span></a></p>
    						<p><label>Price Range:</label><input class="prosper_textinputsmall" style="width:40px;" tabindex="4" type="text" id="pricerangea" name="pricerangea" onKeyUp="showValues();"/>&nbsp;&nbsp;&nbsp;<input class="prosper_textinputsmall" style="width:40px;" tabindex="4" type="text" id="pricerangeb" name="pricerangeb" onKeyUp="showValues();"/><a href="#" class="tooltip"><span>Enter a price range.</span></a></p>														
    						<p><label class="longLabel">Sale Items Only:</label><input tabindex="6" type="checkbox" id="onSale" name="onSale" onClick="showValues();"/><a href="#" class="tooltip"><span>Checking this will only use On Sale Items</span></a></p>                    
    						<table style="font-size:13px;">
    							<tr>
    								<td><label><strong>Go To:</strong></label></td><td>Merchant Page</td><td><input tabindex="2" type="radio" name="prodgoTo" id="prodgoTo" value="merchant" checked="checked"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the merchant's page.</span></a></td>
    							</tr>
    							<tr>	
    								<td><label>&nbsp;</label></td><td>Product Page</td><td><input type="radio" id="prodgoTo" name="prodgoTo" value="prodPage"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the product page of the most relevant product.</span></a></td>
    							</tr>
    							<tr>					
    								<td><label>&nbsp;</label></td><td>Product Results</td><td><input type="radio" id="prodgoTo" name="prodgoTo" value="prodResults" /></td><td><a href="#" class="tooltip"><span>Checking this will link to the product results with your query as the search term.</span></a></td>
    							</tr>
    						</table>
    						<input type="hidden" id="prodid" name="prodid"/>
    					</fieldset>					
    				</div>
    
    				<div id="coupons_panel" class="panel">
    					<fieldset style="font-size:13px;">
    						<legend>Coupon Linker</legend>	
    						<input type="hidden" name="coupfetch" id="coupfetch" value="fetchCoupons"/>						
    						<p><label>Query:</label><input class="prosperTextSC" tabindex="1" type="text" name="coupq" id="coupquery" value="shoes" onKeyUp="showValues();"/><a href="#" class="tooltip"><span>The query that is  used for the search</span></a></p>
    						<p><label>Merchant:</label><input class="prosperTextSC" tabindex="2" type="text" id="coupmerchant" name="coupm" onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name.</span></a></p>																											
    						<table style="font-size:13px;">
    							<tr>
    								<td><label>Go To:</label></td><td>Merchant Page</td><td><input tabindex="2" type="radio" id="coupgoTo" name="coupgoTo" value="merchant" checked="checked"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the merchant's page.</span></a></td>
    							</tr>
    							<tr>	
    								<td><label>&nbsp;</label></td><td>Product Page</td><td><input type="radio" id="coupgoTo" name="coupgoTo" value="prodPage"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the product page of the most relevant product.</span></a></td>
    							</tr>
    							<tr>					
    								<td><label>&nbsp;</label></td><td>Product Results</td><td><input type="radio" id="coupgoTo" name="coupgoTo" value="prodResults" /></td><td><a href="#" class="tooltip"><span>Checking this will link to the product results with your query as the search term.</span></a></td>
    							</tr>
    						</table>
    						<input type="hidden" id="coupid" name="coupid"/>
    					</fieldset>
    				</div>
    
    				<div id="local_panel" class="panel">		
    					<fieldset style="font-size:13px;">
    						<legend>Local Deals Linker</legend>					
    						<input type="hidden" name="localfetch" id="localfetch" value="fetchLocal"/>								
    						<p><label>State:</label><input class="prosperTextSC" tabindex="3" type="text" id="state" name="state" value="California" onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.</span></a></p>                    
    						<p><label>City:</label><input class="prosperTextSC" tabindex="3" type="text" id="city" name="city"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.</span></a></p>                    
    						<p><label>ZipCode:</label><input class="prosperTextSC" tabindex="3" type="text" id="zipcode" name="zip"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.</span></a></p>                    
    						<p><label>Query:</label><input class="prosperTextSC" tabindex="1" type="text" name="localq" id="localquery"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>The query that is  used for the search</span></a></p>									
    						<p><label>Merchant:</label><input class="prosperTextSC" tabindex="2" type="text" id="localmerchant" name="localm"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name.</span></a></p>								
    						<table style="font-size:13px;">
    							<tr>
    								<td><label><strong>Go To:</strong></label></td><td>Merchant Page</td><td><input tabindex="2" type="radio" id="localgoTo" name="localgoTo" value="merchant" checked="checked"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the merchant's page.</span></a></td>
    							</tr>
    							<tr>	
    								<td><label>&nbsp;</label></td><td>Product Page</td><td><input type="radio" id="localgoTo" name="localgoTo" value="prodPage"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the product page of the most relevant product.</span></a></td>
    							</tr>
    							<tr>					
    								<td><label>&nbsp;</label></td><td>Product Results</td><td><input type="radio" id="localgoTo" name="localgoTo" value="prodResults" /></td><td><a href="#" class="tooltip"><span>Checking this will link to the product results with your query as the search term.</span></a></td>
    							</tr>
    						</table>						
    						<input type="hidden" id="localid" name="localid"/>							
    					</fieldset>
    				</div>
    			</div>    			
    		</div>

    		<div style="overflow:hidden">
    			<div id="resultsGoHere" class="mceActionPanel" style="width:65%;float:right;display:block;height:305px;overflow:auto">
    				<input type="hidden" id="images" name="images"/>
    				<input type="hidden" id="keywords" name="keywords"/>
    				<div id="sticky-anchor"></div>
    				<div class="added" style="display:none" aria-required="true"></div>
    				<div class="preview" aria-required="true" style="overflow:auto"></div>				        
    			</div>
    			<div style="display:block;position:absolute;bottom:10px;right:20px;overflow:hidden;position:fixed">
    			    <input tabindex="11" type="submit" value="Submit Results" class="button-primary" id="prosperMCE_submit" onClick="javascript:shortCode.insert(shortCode.local_ed);" style="postion:fixed;"/>
    			</div>
			</div>
		</form>
    </body>
</html>

