jQuery(function() {
    var c;
    if (jQuery("#rangeMin").val())
    {
	    jQuery("#sliderRange").slider({
	        range: !0,
	        min: parseInt(jQuery("#minRangeValue").val()),
	        max: parseInt(jQuery("#maxRangeValue").val()),
	        step: 10,
	        values: [parseInt(jQuery("#rangeMin").val().replace(/^\$/g, "")), parseInt(jQuery("#rangeMax").val().replace(/^\$/g, ""))],
	
	        slide: function(d, b) {
	            clearTimeout(c);
	            var a = 0 == jQuery(b.handle).data("uiSliderHandleIndex") ? "#rangeMin" : "#rangeMax";
	            jQuery("#rangeMin").val("$" + b.values[0]);
	            jQuery("#rangeMax").val("$" + b.values[1]);
	            jQuery(a).html("$" + b.value).position({
	                my: "center top",
	                at: "center bottom",
	                of: b.handle,
	                offset: "0, 10"
	            })
	        },
	        stop: function(d, b) {
	            var a = window.location.href;
	            c = setTimeout(function() {
	                a.match(/\/dR\/\d+(,|%2C)\d+\//g) && (a = a.replace(/\/dR\/\d+(,|%2C)\d+/g, ""));
	                a = a.replace(/\/?$/g, "");
	                a = a + "/dR/" + [b.values[0], b.values[1]] + "/";
	                window.location = a
	            }, 1350)
	        }
	    });
	    jQuery("#rangeMin").html(jQuery("#sliderRange").slider("values", 0)).position({
	        my: "center top",
	        at: "center bottom",
	        of: jQuery("#sliderRange span:eq(0)"),
	        offset: "0, 10"
	    });
	    jQuery("#rangeMax").html(jQuery("#sliderRange").slider("values", 1)).position({
	        my: "center top",
	        at: "center bottom",
	        of: jQuery("#sliderRange span:eq(1)"),
	        offset: "0, 10"
	    });
    }
    if (jQuery("#percentMin").val())
    {
	    jQuery("#sliderPercent").slider({
	        range: !0,
	        min: 0,
	        max: 100,
	        step: 5,
	        values: [parseInt(jQuery("#percentMin").val().replace(/^\$/g, "")), parseInt(jQuery("#percentMax").val().replace(/^\$/g, ""))],
	        slide: function(d, b) {
	            clearTimeout(c);
	            var a = 0 == jQuery(b.handle).data("uiSliderHandleIndex") ? "#percentMin" : "#percentMax";
	            jQuery("#percentMin").val(b.values[0] + "%");
	            jQuery("#percentMax").val(b.values[1] + "%");
	            jQuery(a).html(b.value + "%").position({
	                my: "center top",
	                at: "center bottom",
	                of: b.handle,
	                offset: "0, 10"
	            })
	        },
	        stop: function(d, b) {
	            var a = window.location.href;
	            c = setTimeout(function() {
	                a.match(/\/pR\/\d+(,|%2C)\d+\//g) && (a = a.replace(/\/pR\/\d+(,|%2C)\d+/g, ""));
	                a = a.replace(/\/?$/g, "");
	                a = a + "/pR/" + [b.values[0], b.values[1]] + "/";
	                window.location = a
	            }, 1350)
	        }
	    });
	    jQuery("#percentMin").html(jQuery("#sliderPercent").slider("values", 0) + "%").position({
	        my: "center top",
	        at: "center bottom",
	        of: jQuery("#sliderPercent span:eq(0)"),
	        offset: "0, 10"
	    });
	    jQuery("#percentMax").html(jQuery("#sliderPercent").slider("values",
	        1) + "%").position({
	        my: "center top",
	        at: "center bottom",
	        of: jQuery("#sliderPercent span:eq(1)"),
	        offset: "0, 10"
	    });
    }
    jQuery("a.ui-slider-handle").attr("rel", "nolink")
});