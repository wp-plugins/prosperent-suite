jQuery(document).ready(function(){var a=Math.floor(document.getElementById("prosperShopMain").offsetWidth-(document.getElementById("prosperFilterSidebar")?document.getElementById("prosperFilterSidebar").offsetWidth:0)-8),c=Math.ceil(a/250),b=Math.floor((a-8*c)/c);jQuery("#simProd.prosperResults").width(a);jQuery("#simProd.prosperResults li").width(b);jQuery("#simProd.prosperResults li img").css({height:b,width:b});jQuery("#simProd.prosperResults li .prosperLoad").css({height:b,width:b});jQuery("#simProd.prosperResults li").each(function(a){jQuery("#simProd.prosperResults li:nth-child("+
a+")")[0]&&jQuery("#simProd.prosperResults li:nth-child("+a+") .prosperExtra").width(Math.floor(b-Math.ceil(jQuery("#simProd.prosperResults li:nth-child("+a+") .prosperPrice").width())-6))});jQuery("#simProd.prosperResults li").each(function(a){jQuery(this).attr("data-lirow",c+"-"+a)});(result=window.location.href.match(/cid\/(.{32})/))&&550<document.body.clientWidth&&prosperProdDetails("",result[1])});var resizeTimer;
jQuery(window).resize(function(){clearTimeout(resizeTimer);resizeTimer=setTimeout(function(){var a=Math.floor(document.getElementById("prosperShopMain").offsetWidth-document.getElementById("prosperFilterSidebar").offsetWidth-8),c=Math.ceil(a/250),b=Math.floor((a-8*c)/c);jQuery("#simProd.prosperResults").width(a);jQuery("#simProd.prosperResults li").width(b);jQuery("#simProd.prosperResults li .prosperLoad").css({height:b,width:b});jQuery("#simProd.prosperResults li").each(function(a){jQuery("#simProd.prosperResults li:nth-child("+
a+")")[0]&&jQuery("#simProd.prosperResults li:nth-child("+a+") .prosperExtra").width(Math.floor(b-Math.ceil(jQuery("#simProd.prosperResults li:nth-child("+a+") .prosperPrice").width())-6))});jQuery("#simProd.prosperResults li").each(function(a){jQuery(this).attr("data-lirow",c+"-"+a)})},200)});
function toggle_visibility(a){a=document.getElementById(a);jQuery("#"+a.id).css("display","block"==jQuery("#"+a.id).css("display")?"none":"block");a.id+" fa fa-caret-down"==jQuery("i."+a.id).attr("class")?(jQuery("i."+a.id).removeClass("fa-caret-down"),jQuery("i."+a.id).addClass("fa-caret-up")):(jQuery("i."+a.id).removeClass("fa-caret-up"),jQuery("i."+a.id).addClass("fa-caret-down"))}
function prosperProdDetails(a,c){var b=jQuery("#"+a.id).attr("data-simresults");bodyWidth=jQuery("#prosperShopMain").width();resultWidth=bodyWidth-jQuery("#prosperFilterSidebar").width()-8;prodCount=Math.ceil(resultWidth/250);documentWidth=document.body.clientWidth;if(550>documentWidth)return b=jQuery("#"+a.id).attr("data-prosperKeyword"),window.location.href=window.location.protocol+"//"+window.location.hostname+"/product/"+b+"/cid/"+a.id,!1;if(b)var e=a.id.substr(3);else{window.parentId=c?c:a.id;
var e=parentId,d=jQuery("#"+e).attr("data-lirow");if(!jQuery("#"+e).attr("data-lirow"))return closeProsperDetails(),!1;b=d.match(/-(.*)/);d=d.match(/(.*)-/);b=parseInt(Math.floor(b[1]/d[1])*d[1])+prodCount;d=jQuery("*[data-lirow]").length;parent=e;jQuery(".prosperDetails").remove();jQuery(".prosperpointer").remove();jQuery("#simProd.prosperResults li:nth-child("+(d>b?b:d)+")").after('<li class="prosperDetails" style="overflow:hidden;"></li>');jQuery("#"+e).get(0).scrollIntoView({block:"start",behavior:"smooth"})}b=
window.location.href.replace(/\/$/,"");window.history.pushState("Test","",b.replace(/\/cid\/.{32}/,"")+"/cid/"+e+"/");jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:_prosperShop.api,filterCatalogId:e,imageSize:"500x500",limit:1,imageMaskDomain:_prosperShop.imk,clickMaskDomain:_prosperShop.cmk,enableFullData:0},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){jQuery("#prodbrand").empty();jQuery.each(a.data,function(a,b){if(b.description){var c=
b.description;if(250<c.length)var d=c.substr(0,250),c=c.substr(249,c.length-250),c=d+'<span class="moreellipses">... </span><span class="morecontent"><span>'+c+'</span><a href="javascript:void(0);" class="morelink" onClick="moreDesc(this);">More</a></span>'}var e=document.getElementById(parentId),d=jQuery(e).offset().left,f=jQuery("#simProd.prosperResults").offset().left,e=jQuery(e).outerWidth(!0),d=Math.floor(d-f+e/2-16)+"px";jQuery(".prosperDetails").html('<div><div class="prosperpointer" style="left:'+
d+';height:14px;margin-top:10px;margin-bottom:-1px;position:relative;width:20px;z-index-1;"><img src="'+_prosperShop.img+'/arrow.png"/></div><div class="prosperDetsContain"><div style="position:relative;padding:4px 4px 0 0;"><a href="javascript:void(0);" onClick="closeProsperDetails();"><i style="color:red;display:inline-block;float:right;font-size:16px;" class="fa fa-times"></i></a></div><div class="prosperDets"><div class="prosperDetContent"><div class="pDetailsImage"><a href="'+b.affiliate_url+
'" target="_blank" rel="nofollow,nolink"><img src="'+b.image_url+'" alt="'+b.keyword+'" title="'+b.keyword+'" /></a></div><div class="pDetailsAll"><div class="pDetailsKeyword"><a href="'+b.affiliate_url+'" target="_blank" rel="nofollow,nolink">'+b.keyword+'</a></div><div class="pDetailsDesc">'+c+'</div><table class="productResults"></table></div></div></div><div class="simTitle">Similar Products</div><div class="prosperSimResults" style="width:100%!important;max-width:100%!important;"><ul></ul></div></div></div>');
jQuery.ajax({type:"POST",url:"http://api.prosperent.com/api/search",data:{api_key:_prosperShop.api,filterProductId:b.productId,groupBy:"merchant",limit:10,enableFullData:0,imageMaskDomain:_prosperShop.imk,clickMaskDomain:_prosperShop.cmk,imageSize:"125x125"},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){jQuery.each(a.data,function(a,b){jQuery(".productResults").append('<tr><td><img style="width:80px;height:40px;" src="http://images.prosperentcdn.com/images/logo/merchant/original/120x60/'+
b.merchantId+".jpg?prosp=&m="+b.merchant+'"/></td><td style="vertical-align:middle;"><strong>$'+(b.price_sale?b.price_sale:b.price)+'</strong></td><td style="vertical-align:middle;"><div class="shopCheck prosperVisit"><a itemprop="offerURL" href="'+b.affiliate_url+'" target="_blank" rel="nofollow,nolink"><input type="submit" type="submit" class="prosperVisitSubmit" value="'+(_prosperShop.vbt?_prosperShop.vbt:"Visit Store")+'"/></a></div></td></tr>')})},error:function(){}});jQuery.ajax({type:"POST",
url:"http://api.prosperent.com/api/search",data:{api_key:_prosperShop.api,query:b.keyword,limit:6,enableFullData:0,imageMaskDomain:_prosperShop.imk,clickMaskDomain:_prosperShop.cmk,imageSize:"125x125"},contentType:"application/json; charset=utf-8",dataType:"jsonp",success:function(a){var b=Math.floor(jQuery(".prosperSimResults")[0].getBoundingClientRect().width),c=Math.ceil(b/125),d=Math.floor((b-8*c)/c);jQuery.each(a.data,function(a,b){jQuery(".prosperSimResults ul").append('<li data-simresults="1" id="sim'+
b.catalogId+'" class="'+b.productId+'" onClick="prosperProdDetails(this);" style="width:'+d+'px;cursor:pointer;"><div class="prodImage"><img src="'+b.image_url+'"/></div><div class="prodContent"><div class="prodTitle">'+(b.brand?b.brand:"&nbsp;")+"</div>$"+b.price+"</div></li>")})},error:function(){}})})},error:function(){}})}
function closeProsperDetails(){jQuery(".prosperDetails").remove();var a=window.location.href.replace(/\/$/,"");window.history.pushState("RemovingCID","",a.replace(/\/cid\/.{32}/,""))}function moreDesc(a){jQuery(a).hasClass("less")?(jQuery(a).removeClass("less"),jQuery(a).html("More")):(jQuery(a).addClass("less"),jQuery(a).html("Less"));jQuery(a).parent().prev().toggle();jQuery(a).prev().toggle();return a};