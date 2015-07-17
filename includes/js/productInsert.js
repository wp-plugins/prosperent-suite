jQuery(document).ready(function() {
    jQuery("#simProd.prosperInsert ul").each(function(a,e) {	
    	var a = Math.floor(document.getElementById("simProd").offsetWidth),
    		c = jQuery(e)[0].childElementCount < 6 ? jQuery(e)[0].childElementCount : 4,
            b = Math.floor((a - 8 * c) / c),
            d = b > 250 ? 250 : b,
            h = jQuery(e)[0].children;

        jQuery.each(h, function(p,q) {
        	var extraClass = jQuery(q).find(".prosperExtra")[0],
        		prosperPriceClass = jQuery(q).find(".prosperPrice")[0],
        		prosperLoad = jQuery(q).find(".prosperLoad")[0];
        	
            jQuery(q).width(d);
            
            jQuery(prosperLoad).css({
                height: d,
                width: d
            });
            
            jQuery(extraClass).width(Math.floor(d - Math.ceil(jQuery(prosperPriceClass).width()) - 6))
        });
    });
});
var resizeTimer;
jQuery(window).resize(function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        jQuery("#simProd.prosperInsert ul").each(function(a,e) {	
        	var a = Math.floor(document.getElementById("simProd").offsetWidth),
    			c = jQuery(e)[0].childElementCount < 6 ? jQuery(e)[0].childElementCount : 4,
                b = Math.floor((a - 8 * c) / c),
                d = b > 250 ? 250 : b,
                h = jQuery(e)[0].children;

            jQuery.each(h, function(p,q) {
            	var extraClass = jQuery(q).find(".prosperExtra")[0],
            		prosperPriceClass = jQuery(q).find(".prosperPrice")[0],
            		prosperLoad = jQuery(q).find(".prosperLoad")[0];
            	
                jQuery(q).width(d);
                
                jQuery(prosperLoad).css({
                    height: d,
                    width: d
                });
                
                jQuery(extraClass).width(Math.floor(d - Math.ceil(jQuery(prosperPriceClass).width()) - 6))
            });
        });
    }, 200)
});