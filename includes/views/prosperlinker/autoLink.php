<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<html>
    <head>
        <title>Auto-Linker</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=3.1.2'; ?>">
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/utils/mctabs.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=3.1.2'; ?>"></script>
		<script type="text/javascript">
			var t;function showValues(){var b=getNewCurrent();clearTimeout(t);var c="",c=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div.preview").html(xmlhttp.responseText).show()};var d=window.location.pathname,b=d.substring(0,d.lastIndexOf("prosperlinker/"))+"preview.php?type="+b+"&";xmlhttp.open("GET",b+c,!0);t=setTimeout(function(){try{xmlhttp.send(),c=""}catch(a){}},500);c||clearTimeout(t)}
			function showAddedValues(){var b=getNewCurrent(),c="",c=jQuery("form").serialize();xmlhttp=new XMLHttpRequest;xmlhttp.onreadystatechange=function(){jQuery("div.added").html(xmlhttp.responseText).show()};var d=window.location.pathname,b=d.substring(0,d.lastIndexOf("prosperlinker/"))+"added.php?type="+b+"&";xmlhttp.open("GET",b+c,!0);xmlhttp.send()}
			function setFocus(){document.getElementById("prodquery").focus();shortCode.local_ed.selection.getContent()&&(document.getElementById("prodquery").value=shortCode.local_ed.selection.getContent(),document.getElementById("coupquery").value=shortCode.local_ed.selection.getContent(),showValues())}
			function getIdofItem(b,c){var d=getNewCurrent(),a;a=!0==c?b.id.replace("small",""):b.id;console.log(a);var e=jQuery("#"+a).find("img.newImage").attr("src"),f=jQuery("#"+a).find("img.newImage").attr("alt");jQuery("#stickyHeader").find("#"+a).attr("src");0<=document.getElementById(d+"id").value.indexOf(a+",")?(jQuery("#"+a).removeClass("highlight"),a=document.getElementById(d+"id").value.replace(a+",",""),e=document.getElementById("images").value.replace(e+",",""),f=document.getElementById("keywords").value.replace(f+
			",",""),document.getElementById(d+"id").value=a,document.getElementById("images").value=e,document.getElementById("keywords").value=f):(document.getElementById(d+"id").value+=a+",",document.getElementById("images").value+=e+",",document.getElementById("keywords").value+=f+",",jQuery("#"+a).addClass("highlight"));showAddedValues()}
			function sticky_relocate(){var b=jQuery(window).scrollTop(),c=jQuery("#sticky-anchor").offset().top;b>c?jQuery("#stickyHeader").addClass("sticky"):jQuery("#stickyHeader").removeClass("sticky")}jQuery(function(){jQuery(window).scroll(sticky_relocate);sticky_relocate()});
		</script>
    </head>
    <base target="_self" />
    <body id="linker" style="display: none" role="application" aria-labelledby="app_label" onload="setFocus();">
		<form action="/" method="get" id="prosperSCForm">
			<input type="hidden" id="prosperSC" value="linker"/>
			<div class="tabs">
				<ul>
					<li id="products_tab" aria-controls="products_panel" class="current"><span><a href="javascript:mcTabs.displayTab('products_tab','products_panel');" onmousedown="return false;">Products</a></span></li>
					<li id="coupons_tab" aria-controls="coupons_panel"><span><a href="javascript:;" onclick="mcTabs.displayTab('coupons_tab','coupons_panel');" onmousedown="return false;">Coupons</a></span></li>
					<li id="local_tab" aria-controls="local_panel"><span><a href="javascript:;" onclick="javascript:mcTabs.displayTab('local_tab','local_panel');" onmousedown="return false;">Local Deals</a></span></li>
				</ul>
			</div>
			<div class="panel_wrapper">
				<div id="products_panel" class="panel current">
					<fieldset style="font-size:14px;">
						<legend>Product Linker</legend>
						<input type="hidden" name="prodfetch" id="prodfetch" value="fetchProducts"/>
						<p><label>Query:</label><input class="prosperTextSC" tabindex="1" type="text" name="prodq" id="prodquery"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>The query that is  used for the search</span></a></p>
						<p><label>Merchant:</label><input class="prosperTextSC" tabindex="2" type="text" id="prodmerchant" name="prodm"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name.</span></a></p>
						<p><label>Brand:</label><input class="prosperTextSC" tabindex="3" type="text" id="prodbrand" name="prodb"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.</span></a></p>
						<p><label>Country:</label><select tabindex="4" id="country" name="country" onChange="showValues();"><option value="US" selected="selected">US</option><option value="UK">UK</option><option value="CA">Canada</option></select><a href="#" class="tooltip"><span>Choose a country to choose the catalog to pull products from.</span></a></p>
						<table style="font-size:13px;">
							<tr>
								<td><label><strong>Go To:</strong></label></td><td>Merchant Page</td><td><input tabindex="2" type="radio" name="prodgoTo" id="prodGoTo" value="merchant" checked="checked"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the merchant's page.</span></a></td>
							<tr>	
								<td><label>&nbsp;</label></td><td>Product Page</td><td><input type="radio" id="prodGoTo" name="prodgoTo" value="prodPage"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the product page of the most relevant product.</span></a></td>
							</tr>
							<tr>					
								<td><label>&nbsp;</label></td><td>Product Results</td><td><input type="radio" id="prodGoTo" name="prodgoTo" value="prodResults" /></td><td><a href="#" class="tooltip"><span>Checking this will link to the product results with your query as the search term.</span></a></td>
							</tr>
						</table>
						<input type="hidden" id="prodid" name="prodid"/>
					</fieldset>					
				</div>

				<div id="coupons_panel" class="panel">
					<fieldset style="font-size:14px;">
						<legend>Coupon Linker</legend>	
						<input type="hidden" name="coupfetch" id="coupfetch" value="fetchCoupons"/>						
						<p><label>Query:</label><input class="prosperTextSC" tabindex="1" type="text" name="coupq" id="coupquery"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>The query that is  used for the search</span></a></p>
						<p><label>Merchant:</label><input class="prosperTextSC" tabindex="2" type="text" id="coupmerchant" name="coupm" onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name.</span></a></p>																											
						<table style="font-size:13px;">
							<tr>
								<td><label>Go To:</label></td><td>Merchant Page</td><td><input tabindex="2" type="radio" id="coupGoTo" name="coupgoTo" value="merchant" checked="checked"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the merchant's page.</span></a></td>
							<tr>	
								<td><label>&nbsp;</label></td><td>Product Page</td><td><input type="radio" id="coupGoTo" name="coupgoTo" value="prodPage"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the product page of the most relevant product.</span></a></td>
							</tr>
							<tr>					
								<td><label>&nbsp;</label></td><td>Product Results</td><td><input type="radio" id="coupGoTo" name="coupgoTo" value="prodResults" /></td><td><a href="#" class="tooltip"><span>Checking this will link to the product results with your query as the search term.</span></a></td>
							</tr>
						</table>
						<input type="hidden" id="coupid" name="coupid"/>
					</fieldset>
				</div>

				<div id="local_panel" class="panel">		
					<fieldset style="font-size:14px;">
						<legend>Local Deals Linker</legend>					
						<input type="hidden" name="localfetch" id="localfetch" value="fetchLocal"/>								
						<p><label>State:</label><input class="prosperTextSC" tabindex="3" type="text" id="state" name="state"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.</span></a></p>                    
						<p><label>City:</label><input class="prosperTextSC" tabindex="3" type="text" id="city" name="city"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.</span></a></p>                    
						<p><label>ZipCode:</label><input class="prosperTextSC" tabindex="3" type="text" id="zipcode" name="zip"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.</span></a></p>                    
						<p><label>Query:</label><input class="prosperTextSC" tabindex="1" type="text" name="localq" id="localquery"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>The query that is  used for the search</span></a></p>									
						<p><label>Merchant:</label><input class="prosperTextSC" tabindex="2" type="text" id="localmerchant" name="localm"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name.</span></a></p>								
						<table style="font-size:13px;">
							<tr>
								<td><label><strong>Go To:</strong></label></td><td>Merchant Page</td><td><input tabindex="2" type="radio" id="localGoTo" name="localgoTo" value="merchant" checked="checked"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the merchant's page.</span></a></td>
							<tr>	
								<td><label>&nbsp;</label></td><td>Product Page</td><td><input type="radio" id="localGoTo" name="prosperGoTo" value="prodPage"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the product page of the most relevant product.</span></a></td>
							</tr>
							<tr>					
								<td><label>&nbsp;</label></td><td>Product Results</td><td><input type="radio" id="localGoTo" name="localgoTo" value="prodResults" /></td><td><a href="#" class="tooltip"><span>Checking this will link to the product results with your query as the search term.</span></a></td>
							</tr>
						</table>						
						<input type="hidden" id="localid" name="localid"/>							
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

