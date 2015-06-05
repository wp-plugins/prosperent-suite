jQuery(function() {
	jQuery(document).ready(function()
	{
		jQuery("#prosper-conf").data("serialize", jQuery("#prosper-conf").serialize());
		
	    if (jQuery( "#hidden_PositiveMerchant" ).val())
	    {
	    	jQuery.ajax({
	            url: "http://api.prosperent.com/api/merchant",
	            dataType: "jsonp",
	            data: {
	                filterMerchantId: (jQuery( "#hidden_PositiveMerchant" ).val()).replace(/,/g, '|'),
	                sortBy: "merchant",
	                limit: 500
	            },
	            success: function(a) {
	                a.data && jQuery.each(a.data, function(a, b) {
	                	jQuery("#PositiveMerchantFilters").append('<li id="' + b.merchantId + '" class="PositiveMerchant" data-filtype="PositiveMerchant" style="float:left;margin:0;padding:6px;" onClick="removeFilter(this);"><span><a style="text-decoration:none;" id="' + b.merchantId + '" href="javascript:void(0);">' + b.merchant + ' <i class="fa fa-times"></i></a></span></li>');
	                });
                   
	            }
	        });	
	    }
	    
	    if (jQuery( "#hidden_NegativeMerchant" ).val())
	    {
	    	jQuery.ajax({
	            url: "http://api.prosperent.com/api/merchant",
	            dataType: "jsonp",
	            data: {
	                filterMerchantId: (jQuery( "#hidden_NegativeMerchant" ).val()).replace(/,/g, '|'),
	                sortBy: "merchant",
	                limit: 500
	            },
	            success: function(a) {
	                a.data && jQuery.each(a.data, function(a, b) {
	                	jQuery("#NegativeMerchantFilters").append('<li id="' + b.merchantId + '" class="NegativeMerchant" data-filtype="NegativeMerchant" style="float:left;margin:0;padding:6px;" onClick="removeFilter(this);"><span><a style="text-decoration:none;" id="' + b.merchantId + '" href="javascript:void(0);">' + b.merchant + ' <i class="fa fa-times"></i></a></span></li>');
	                });                   
	            }
	        });	
	    }
	    
	});
	
	if (jQuery( "#hidden_Positive_Merchant" ).val())
	{
		newFilters('Positive_Merchant');
	}
	if (jQuery( "#hidden_Negative_Merchant" ).val())
	{
		newFilters('Negative_Merchant');
	}	
	
    jQuery("#categories.ProsperCategories").autocomplete({
        minLength: 3,
        source: function(a, b) {
            jQuery.ajax({
                url: "http://api.prosperent.com/api/search",
                dataType: "jsonp",
                data: {
                	api_key:'fc91d36b383ca0231ee59c5048eabedc',
                    query:a.term + "*",
                    enableFacets:'category',
                    limit:1,
                    imageSize:'125x125'
                },
                success: function(a) {
                    var c = [];
                    a.facets && jQuery.each(a.facets.category, function(a, b) {
                        c.push(b.value);
                    });
                   	b(c);                    
                }
            })
        },
        select: function(a, b) {        	
        	jQuery( "#hidden_ProsperCategories" ).val( function( index, val ) {
        	    return val + b.item.value + ',';
        	});
        	jQuery("#categories.ProsperCategories").val('');  
        	jQuery("#ProsperCategoryFilters").append('<li id="' + (b.item.value).replace(/ > /g, '') + '" class="ProsperCategories" data-filtype="ProsperCategories" style="float:left;margin:0;padding:6px;" onClick="removeFilter(this);"><span><a style="text-decoration:none;" id="' + b.item.value + '" href="javascript:void(0);">' + b.item.value + ' <i class="fa fa-times"></i></a></span></li>');
        	return false;
        }
    });	 
    
    jQuery(window).bind("beforeunload", function(e){
        if(jQuery("#prosper-conf").serialize()!=jQuery("#prosper-conf").data("serialize"))return 'You have unsaved settings.';
        else e=null; 
    });
});

function newFilters(filterName)
{
	jQuery.ajax({
	    url: "http://api.prosperent.com/api/merchant",
	    data: {
	    	filterMerchant: (jQuery( "#hidden_" + filterName ).val()).replace(/,|, /g, '|'),
	    	limit:500
	    },
	    contentType: "application/json; charset=utf-8",                    
	    dataType: "jsonp",
	    success: function(g) {   	
	        jQuery.each(g.data, function(h, i) {                            	    
	        	jQuery( "#hidden_" + filterName.replace(/_/g, '')).val( function( index, val ) {
	        		return val + i.merchantId + ',';
	        	});
	        	jQuery("#hidden_" + filterName).val('');
	        })            
	    },
	    error: function() {
	    	return;
	    }
	});	
}

function addingNewMerchantFilter(filterName)
{	
    jQuery("#" +filterName.id).autocomplete({
        minLength: 3,
        source: function(a, b) {
            jQuery.ajax({
                url: "http://api.prosperent.com/api/merchant",
                dataType: "jsonp",
                data: {
                    filterMerchant: (jQuery.isNumeric(a.term) && a.term.length > 5) ? '' : "*" + a.term + "*",
                    filterMerchantId: (jQuery.isNumeric(a.term) && a.term.length > 5) ? a.term : '',
                    sortBy: "merchant",
                    limit: 10
                },
                success: function(a) {
                    var c = [];
                    a.data && jQuery.each(a.data, function(a, b) {
                        c.push({
                        	'label':b.merchant, 
                        	'value': b.merchantId
                        	});
                    });
                   	b(c);                    
                }
            })
        },
        select: function(a, b) {      
        	if (!(jQuery( "#hidden_" +filterName.id ).val()).match(b.item.value))
        	{
	        	jQuery( "#hidden_" +filterName.id ).val( function( index, val ) {
	        	    return val + b.item.value + ',';
	        	});
        	}
        	jQuery("#" +filterName.id ).val('');        	
        	
        	jQuery("#" + filterName.id + "Filters").append('<li id="' + b.item.value + '" class="' + filterName.id + '" data-filtype="' + filterName.id + '" style="float:left;margin:0;padding:6px;" onClick="removeFilter(this);"><span><a style="text-decoration:none;" id="' + b.item.value + '" href="javascript:void(0);">' + b.item.label + ' <i class="fa fa-times"></i></a></span></li>');
        	
        	return false;
        }
    });	
}

function removeFilter(filterName)
{	
	var remove = new RegExp(filterName.id + ",", "g"),
		className = filterName.className;
	var values = (jQuery('#hidden_' + className).val()).replace(remove, '');
	
	jQuery('#hidden_' + className).val(values);
	jQuery("#" + filterName.id + "." + className).empty();
	
}