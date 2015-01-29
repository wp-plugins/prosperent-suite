<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>ProsperInsert</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=3.3.3'; ?>">		
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=3.3.3'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/utils/mctabs.js'; ?>"></script>

		<script type="text/javascript">
			var t;function showValues(){var b=getNewCurrent();clearTimeout(t);var c="",c=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div.preview").html(xmlhttp.responseText).show()};var d=window.location.pathname,b=d.substring(0,d.lastIndexOf("prosperinsert/"))+"preview.php?type="+b+"&";xmlhttp.open("GET",b+c,!0);t=setTimeout(function(){try{xmlhttp.send(),c=""}catch(a){}},500);c||clearTimeout(t)}
			function showAddedValues(){var b=getNewCurrent(),c="",c=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div.added").html(xmlhttp.responseText).show()};var d=window.location.pathname,b=d.substring(0,d.lastIndexOf("prosperinsert/"))+"added.php?type="+b+"&";xmlhttp.open("GET",b+c,!0);xmlhttp.send()}
			function setFocus(){document.getElementById("prodquery").focus();shortCode.local_ed.selection.getContent()&&(document.getElementById("prodquery").value=shortCode.local_ed.selection.getContent(),document.getElementById("coupquery").value=shortCode.local_ed.selection.getContent(),showValues())}
			function getIdofItem(b,c){var d=getNewCurrent(),a;a=!0==c?b.id.replace("small",""):b.id;var e=jQuery("#"+a).find("img.newImage").attr("src"),f=jQuery("#"+a).find("img.newImage").attr("alt");jQuery("#stickyHeader").find("#"+a).attr("src");0<=document.getElementById(d+"id").value.indexOf(a+",")?(jQuery("#"+a).removeClass("highlight"),a=document.getElementById(d+"id").value.replace(a+",",""),e=document.getElementById("images").value.replace(e+",",""),f=document.getElementById("keywords").value.replace(f+
			",",""),document.getElementById(d+"id").value=a,document.getElementById("images").value=e,document.getElementById("keywords").value=f):(document.getElementById(d+"id").value+=a+",",document.getElementById("images").value+=e+",",document.getElementById("keywords").value+=f+",",jQuery("#"+a).addClass("highlight"));/*showAddedValues()*/}
			function sticky_relocate(){var b=jQuery(window).scrollTop(),c=jQuery("#sticky-anchor").offset().top;b>c?jQuery("#stickyHeader").addClass("sticky"):jQuery("#stickyHeader").removeClass("sticky")}jQuery(function(){jQuery(window).scroll(sticky_relocate);sticky_relocate()});
		</script>
    </head>
    <base target="_self" />
    <body id="inserter" style="display: none" role="application" aria-labelledby="app_label" onload="setFocus();">
		<form action="/" method="get" id="prosperSCForm">
			<input type="hidden" id="prosperSC" value="compare"/>
			<div class="tabs">
				<ul>
					<li id="products_tab" aria-controls="products_panel" class="current"><span><a href="javascript:mcTabs.displayTab('products_tab','products_panel');" onmousedown="return false;">Products</a></span></li>
					<li id="coupons_tab" aria-controls="coupons_panel"><span><a href="javascript:mcTabs.displayTab('coupons_tab','coupons_panel');" onmousedown="return false;">Coupons</a></span></li>
					<li id="local_tab" aria-controls="local_panel"><span><a href="javascript:mcTabs.displayTab('local_tab','local_panel');" onmousedown="return false;">Local Deals</a></span></li>
					<li id="merchant_tab" aria-controls="merchant_panel"><span><a href="javascript:mcTabs.displayTab('merchant_tab','merchant_panel');" onmousedown="return false;">Merchants</a></span></li>
				</ul>
			</div>

			<div class="panel_wrapper">
				<div id="products_panel" class="panel current">
					<fieldset style="font-size:14px;">
						<legend>Product Insert</legend>
						<input type="hidden" name="prodfetch" id="prodfetch" value="fetchProducts"/>
						<p><label>Query:</label><input class="prosperTextSC" tabindex="1" type="text" name="prodq" id="prodquery"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>The query that is  used for the search</span></a></p>
						<p><label>Merchant:</label><input class="prosperTextSC" tabindex="2" type="text" id="prodmerchant" name="prodm"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name.</span></a></p>
						<p><label>Brand:</label><input class="prosperTextSC" tabindex="3" type="text" id="prodbrand" name="prodb"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Brands</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.</span></a></p>
						<p><label>Celebrity Name:</label><input class="prosperTextSC" tabindex="4" type="text" id="prodcelebname" name="prodcelebname"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>Celebrity Name to get products of a celebrity.</span></a></p>														
						<p><label>Country:</label><select tabindex="5" id="country" name="country" onChange="showValues();"><option value="US" selected="selected">US</option><option value="UK">UK</option><option value="CA">Canada</option></select><a href="#" class="tooltip"><span>Choose a country to choose the catalog to pull products from.</span></a></p>
						<p><label>Price Range:</label><input class="prosper_textinputsmall" style="width:40px;" tabindex="4" type="text" id="pricerangea" name="pricerangea" onKeyUp="showValues();"/>&nbsp;&nbsp;&nbsp;<input class="prosper_textinputsmall" style="width:40px;" tabindex="4" type="text" id="pricerangeb" name="pricerangeb" onKeyUp="showValues();"/><a href="#" class="tooltip"><span>Enter a price range.</span></a></p>														
						<p><label class="longLabel">Sale Items Only:</label><input tabindex="6" type="checkbox" id="onSale" name="onSale" onClick="showValues();"/><a href="#" class="tooltip"><span>Checking this will only use On Sale Items</span></a></p>                    
						<p><label>Limit:</label><input class="prosperTextSC" tabindex="7" type="text" id="prodlimit" style="width:50px"/><a href="#" class="tooltip"><span>This amount of products to display.</span></a></p>                    
						<p><label>Button Text:</label><input class="prosperTextSC" tabindex="8" type="text" id="prodvisit" name="prodvisit" /><a href="#" class="tooltip"><span>Change the Visit Store button text to anything you'd like. <strong>Defaults to Visit Store</strong></span></a></p>
						<p><label>View:</label>Grid <input tabindex="9" class="viewRadioSC" type="radio" value="grid" name="prodview" id="prodview" checked="checked"/>&nbsp;&nbsp;&nbsp;List <input tabindex="10" type="radio" value="list" name="prodview" id="prodview"/><a href="#" class="tooltip"><span>Choose the view you would like to display products in.</span></a></p>									
						<p><label>Grid Img Size:</label><input tabindex="11" class="prosperTextSC" type="text" id="gridimgsz"/><a href="#" class="tooltip"><span>Image size of grid view products.</span></a></p>                    
						<p><label class="longLabel">Go To Merchant:</label><input  tabindex="12" type="checkbox" id="prodgoTo" checked="checked"/><a href="#" class="tooltip"><span>Checking this will link to the merchant's page, skipping the product page for all links.</span></a></p>									
						<input type="hidden" id="prodid" name="prodid"/>
					</fieldset>					
				</div>

				<div id="coupons_panel" class="panel">
					<fieldset style="font-size:14px;">
						<legend>Coupon Insert</legend>	
						<input type="hidden" name="coupfetch" id="coupfetch" value="fetchCoupons"/>						
						<p><label>Query:</label><input class="prosperTextSC" tabindex="1" type="text" name="coupq" id="coupquery"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>The query that is  used for the search</span></a></p>
						<p><label>Merchant:</label><input class="prosperTextSC" tabindex="2" type="text" id="coupmerchant" name="coupm" onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name.</span></a></p>																											
						<p><label>Limit:</label><input class="prosperTextSC" tabindex="3" type="text" id="couplimit" style="width:50px"/><a href="#" class="tooltip"><span>This amount of products to display.</span></a></p>                    
						<p><label>Button Text:</label><input class="prosperTextSC" tabindex="4" type="text" id="coupvisit" name="coupvisit"/><a href="#" class="tooltip"><span>Change the Visit Store button text to anything you'd like. <strong>Defaults to Visit Store</strong></span></a></p>
						<p><label>View:</label>Grid <input tabindex="5" class="viewRadioSC" type="radio" value="grid" name="coupview" id="coupview" checked="checked"/>&nbsp;&nbsp;&nbsp;List <input tabindex="6" type="radio" value="list" name="coupview" id="coupview"/><a href="#" class="tooltip"><span>Choose the view you would like to display products in.</span></a></p>									
						<p><label style="width:125px;float:left;">Go to Merchant:</label><input tabindex="7" type="checkbox" id="coupgoTo" checked="checked"/><a href="#" class="tooltip"><span>Checking this will link to the merchant's page, skipping the product page for all links.</span></a></p>									
						<input type="hidden" id="coupid" name="coupid"/>
					</fieldset>
				</div>

				<div id="local_panel" class="panel">		
					<fieldset style="font-size:14px;">
						<legend>Local Deals Insert</legend>					
						<input type="hidden" name="localfetch" id="localfetch" value="fetchLocal"/>								
						<p><label>State:</label><input class="prosperTextSC" tabindex="1" type="text" id="state" name="state"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>Filter results by state.</span></a></p>                    
						<p><label>City:</label><input class="prosperTextSC" tabindex="2" type="text" id="city" name="city"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>Filter results by city.</span></a></p>                    
						<p><label>ZipCode:</label><input class="prosperTextSC" tabindex="3" type="text" id="zipcode" name="zip"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>Filter results by zip code.</span></a></p>                    
						<p><label>Query:</label><input class="prosperTextSC" tabindex="4" type="text" name="localq" id="localquery"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>The query that is  used for the search</span></a></p>									
						<p><label>Merchant:</label><input class="prosperTextSC" tabindex="5" type="text" id="localmerchant" name="localm"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name.</span></a></p>								
						<p><label>Limit:</label><input class="prosperTextSC" tabindex="6" type="text" id="locallimit" style="width:50px"/><a href="#" class="tooltip"><span>This amount of products to display.</span></a></p>                    
						<p><label>Button Text:</label><input class="prosperTextSC" tabindex="7" type="text" id="localvisit" name="localvisit"/><a href="#" class="tooltip"><span>Change the Visit Store button text to anything you'd like. <strong>Defaults to Visit Store</strong></span></a></p>
						<p><label>View:</label>Grid <input tabindex="8" class="viewRadioSC" type="radio" value="grid" name="localview" id="localview" checked="checked"/>&nbsp;&nbsp;&nbsp;List <input tabindex="9" type="radio" value="list" name="localview" id="localview"/><a href="#" class="tooltip"><span>Choose the view you would like to display products in.</span></a></p>									
						<p><label style="width:125px;float:left;">Go to Merchant:</label><input tabindex="10" type="checkbox" id="localgoTo" checked="checked"/><a href="#" class="tooltip"><span>Checking this will link to the merchant's page, skipping the product page for all links.</span></a></p>						
						<input type="hidden" id="localid" name="localid"/>							
					</fieldset>
				</div>
				
				<div id="merchant_panel" class="panel">		
					<fieldset style="font-size:14px;">
						<legend>Merchant Insert</legend>					
						<input type="hidden" name="merchantfetch" id="merchantfetch" value="fetchMerchant"/>																
						<p><label>Merchant:</label><input class="prosperTextSC" tabindex="5" type="text" id="merchantmerchant" name="merchantm"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>Enter the merchant name.</span></a></p>														
						<p><label style="width:125px;float:left;">Go to Merchant:</label><input tabindex="9" type="checkbox" id="merchantgoTo" checked="checked"/><a href="#" class="tooltip"><span>Checking this will link to the merchant's page, skipping the product page for all links.</span></a></p>						
						<input type="hidden" id="merchantid" name="merchantid"/>							
					</fieldset>
				</div>
			</div>

			<div class="mceActionPanel">	
				<input type="hidden" id="images" name="images"/>
				<input type="hidden" id="keywords" name="keywords"/>
				<input tabindex="11" type="submit" value="Submit" class="button-primary" id="prosperMCE_submit" onClick="javascript:shortCode.insert(shortCode.local_ed);"/><br><br>
				<h2>Product Review:</h2>
				<span style="font-size:10px;"><strong>Note</strong>: Click the item(s) that you would like to be displayed.</span><br><br>
				<div id="sticky-anchor"></div>
				<div class="added" style="display:none" aria-required="true"></div><br>
				<div class="preview" style="display:none" aria-required="true"></div>
			</div>
		</form>
    </body>
</html>

