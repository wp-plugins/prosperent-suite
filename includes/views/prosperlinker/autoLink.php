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
			function setFocus(){document.getElementById("prodquery").focus();shortCode.local_ed.selection.getContent()&&(document.getElementById("prodquery").value=shortCode.local_ed.selection.getContent(),showValues())}
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
						<li id="products_tab" aria-controls="products_panel" class="current"><span><a href="javascript:;" onClick="mcTabs.displayTab('products_tab','products_panel');setFocus();showValues();" onmousedown="return false;">Products</a></span></li>						
						<li id="merchant_tab" aria-controls="merchant_panel"><span><a href="javascript:;" onClick="mcTabs.displayTab('merchant_tab','merchant_panel');setFocus();showValues();" onmousedown="return false;">Merchants</a></span></li>
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
 					<div id="merchant_panel" class="panel">		
						<fieldset style="font-size:14px;">
							<legend>Merchant Linker</legend>					
							<input type="hidden" name="merchantfetch" id="merchantfetch" value="fetchMerchant"/>
							<p>Only Merchants that allow DeepLinking.</p>																
							<p><label>Merchant:</label><input class="prosperTextSC" value="Backcountry" tabindex="5" type="text" id="merchantmerchant" name="merchantm"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>Enter the merchant name.</span></a></p>														
							<p><label>Category:</label><input class="prosperTextSC" tabindex="2" type="text" id="merchantcategory" name="merchantcategory"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple categories</strong></span></a>
							<p><span style="font-size:11px;color:#695F5F;padding-left:55px;">*Uses categories from <a href="http://prosperent.com/merchants/all">All Merchants</a></span></p></p>
							<table style="font-size:13px;">
    							<tr>
    								<td><label><strong>Go To:</strong></label></td><td>Merchant Page</td><td><input tabindex="2" type="radio" name="merchantgoTo" id="merchantgoTo" value="merchant" checked="checked"/></td><td><a href="#" class="tooltip"><span>Checking this will link to the merchant's page.</span></a></td>
    								
    							</tr>
    							<tr>					
    								<td><label>&nbsp;</label></td><td>Product Results</td><td><input type="radio" id="merchantgoTo" name="merchantgoTo" value="prodResults" /></td><td><a href="#" class="tooltip"><span>Checking this will link to the product results with your query as the search term.</span></a></td>
    							</tr>
    						</table>						
							<input type="hidden" id="merchantid" name="merchantid"/>							
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

