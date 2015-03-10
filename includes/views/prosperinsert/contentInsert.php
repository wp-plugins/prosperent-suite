<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$result = preg_replace('/wp-content.*/i', '', $url);
$mainURL = preg_replace('/views.+/', '' , $url);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Change ContentInserter</title>
		<link rel="stylesheet" href="<?php echo $mainURL . 'css/prosperMCE.css?v=3.34.3'; ?>">		
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/jquery/jquery.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $result . 'wp-includes/js/tinymce/tiny_mce_popup.js'; ?>"></script>
		<script data-cfasync="false" type="text/javascript" src="<?php echo $mainURL . 'js/prosperMCE.js?v=3.4.3'; ?>"></script>
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
			<input type="hidden" id="prosperSC" value="prosperNewQuery"/>
			<fieldset style="font-size:14px;">
				<p>Change the query for the ContentInsert or disable it by checking 'Don't Show'.</p>
				<input type="hidden" name="prodfetch" id="prodfetch" value="fetchProducts"/>
				<p><label>Query:</label><input class="prosperTextSC" tabindex="1" type="text" name="prodq" id="query"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span>The query that is  used for the search</span></a></p>
				<p><label>Merchant:</label><input class="prosperTextSC" tabindex="2" type="text" id="merchant" name="prodm"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Merchants</strong> If you want to exclude a merchant, put an <strong>!</strong> before the merchant name.</span></a></p>
				<p><label>Brand:</label><input class="prosperTextSC" tabindex="3" type="text" id="brand" name="prodb"  onKeyUp="showValues();"/><a href="#" class="tooltip"><span><strong>Comma Seperate multiple Brands</strong> If you want to exclude a brand, put an <strong>!</strong> before the brand name.</span></a></p>
				<p><label>Price Range:</label><input class="prosper_textinputsmall" style="width:40px;" tabindex="4" type="text" id="pricerangea" name="pricerangea" onKeyUp="showValues();"/>&nbsp;&nbsp;&nbsp;<input class="prosper_textinputsmall" style="width:40px;" tabindex="4" type="text" id="pricerangeb" name="pricerangeb" onKeyUp="showValues();"/><a href="#" class="tooltip"><span>Enter a price range.</span></a></p>														
				<p><label class="longLabel">Sale Items Only:</label><input tabindex="6" type="checkbox" id="onSale" name="onSale" onClick="showValues();"/><a href="#" class="tooltip"><span>Checking this will only use On Sale Items</span></a></p>                    
				<p><label>Limit:</label><input class="prosperTextSC" tabindex="7" type="text" id="limit" style="width:50px"/><a href="#" class="tooltip"><span>This amount of products to display.</span></a></p>                    
				<p><label>Don't Show:</label><input tabindex="8" type="checkbox" id="noShow" name="noShow"/><a href="#" class="tooltip"><span>Checking this will disable the ContentInserter on this page/post.</span></a></p>                    
				<input type="hidden" id="prodid" name="prodid"/>
			</fieldset>					

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

