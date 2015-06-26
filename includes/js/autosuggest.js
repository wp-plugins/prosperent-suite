jQuery(function() {
    jQuery(document).ready(function() {
        jQuery("#prosper-conf").data("serialize", jQuery("#prosper-conf").serialize());
        jQuery("#hidden_PositiveMerchant").val() && jQuery.ajax({
            url: "http://api.prosperent.com/api/merchant",
            dataType: "jsonp",
            data: {
                filterMerchantId: jQuery("#hidden_PositiveMerchant").val(),
                sortBy: "merchant",
                limit: 500
            },
            success: function(a) {
                a.data && jQuery.each(a.data, function(a, b) {
                    jQuery("#PositiveMerchantFilters").append('<li data-savedvalue="' + b.merchantId + '" class="PositiveMerchant" data-filtype="PositiveMerchant" style="float:left;margin:0;padding:6px;" onClick="removeFilter(this);"><span><a style="text-decoration:none;" id="' +
                        b.merchantId + '" href="javascript:void(0);">' + b.merchant + ' <i class="fa fa-times"></i></a></span></li>')
                })
            }
        });
        jQuery("#hidden_NegativeMerchant").val() && jQuery.ajax({
            url: "http://api.prosperent.com/api/merchant",
            dataType: "jsonp",
            data: {
                filterMerchantId: jQuery("#hidden_NegativeMerchant").val(),
                sortBy: "merchant",
                limit: 500
            },
            success: function(a) {
                a.data && jQuery.each(a.data, function(a, b) {
                    jQuery("#NegativeMerchantFilters").append('<li data-savedvalue="' + b.merchantId + '" class="NegativeMerchant" data-filtype="NegativeMerchant" style="float:left;margin:0;padding:6px;" onClick="removeFilter(this);"><span><a style="text-decoration:none;" id="' +
                        b.merchantId + '" href="javascript:void(0);">' + b.merchant + ' <i class="fa fa-times"></i></a></span></li>')
                })
            }
        })
    });
    jQuery("#hidden_Positive_Merchant").val() && newFilters("Positive_Merchant");
    jQuery("#hidden_Negative_Merchant").val() && newFilters("Negative_Merchant");
    jQuery("#categories.ProsperCategories").autocomplete({
        minLength: 3,
        source: function(a, c) {
            jQuery.ajax({
                url: "http://api.prosperent.com/api/search",
                dataType: "jsonp",
                data: {
                    api_key: "fc91d36b383ca0231ee59c5048eabedc",
                    filterCategory: "*" + a.term + "*",
                    enableFacets: "category",
                    limit: 1,
                    imageSize: "125x125"
                },
                success: function(a) {
                    var d = [];
                    
                    a.facets && jQuery.each(a.facets.category, function(a, b) {
                        d.push(b.value)
                    });
                    d = d.sort();
                    c(d)
                }
            })
        },
        select: function(a, c) {
            jQuery("#categories.ProsperCategories").val(c.item.value);
            return !1
        }
    });
    jQuery(window).bind("beforeunload", function(a) {
        if (jQuery("#prosper-conf").serialize() != jQuery("#prosper-conf").data("serialize")) return "You have unsaved settings."
    });
    jQuery("form#prosper-conf").submit(function() {
        jQuery(window).unbind("beforeunload")
    });
    
    if (jQuery("#hidden_ProsperCategories").val())
    {
    	var category = jQuery("#hidden_ProsperCategories").val();

    	jQuery.ajax({
            url: "http://api.prosperent.com/api/search",
            dataType: "jsonp",
            data: {
                api_key: "fc91d36b383ca0231ee59c5048eabedc",
                filterCategory: category.replace(/_/g, ' '),
                limit: 1,
                imageSize: "75x75"
            },
            success: function(a) {
            	jQuery("#prosperTotalResults").append('Total Results In Shop With These Category Filters: <span>' + a.totalRecordsFound.toLocaleString() + '</span>');                
            }
        });
    }
});

function addNewCategory()
{
	var newCategory = jQuery("#categories.ProsperCategories").val();
	var hiddenCategory = jQuery("#hidden_ProsperCategories").val();
	
	jQuery("#hidden_ProsperCategories").val(function(a, d) {
        return hiddenCategory + newCategory.replace(/ /g, "_") + "|"
    });
	jQuery.ajax({
        url: "http://api.prosperent.com/api/search",
        dataType: "jsonp",
        data: {
            api_key: "fc91d36b383ca0231ee59c5048eabedc",
            filterCategory: newCategory + '*',
            limit: 1,
            imageSize: "75x75"
        },
        success: function(a) {        	
        	jQuery("#ProsperCategoryFilters").append('<li data-savedvalue="' + newCategory.replace(/ /g, "_") + '" class="ProsperCategories" data-filType="ProsperCategories" style="float:left;margin:0;padding:6px;" onClick="removeFilter(this);"><span><a style="text-decoration:none;" id="' +
        		newCategory.replace(/ /g, "_") + '" href="javascript:void(0);">' + newCategory + ' (' + a.totalRecordsFound.toLocaleString() + ') <i class="fa fa-times"></i></a></span></li>');
        }
    });
	
	jQuery("#categories.ProsperCategories").val("");
}

function newFilters(a) {
    jQuery.ajax({
        url: "http://api.prosperent.com/api/merchant",
        data: {
            filterMerchant: jQuery("#hidden_" + a).val(),
            limit: 500
        },
        contentType: "application/json; charset=utf-8",
        dataType: "jsonp",
        success: function(c) {
            jQuery.each(c.data, function(b, c) {
                jQuery("#hidden_" + a.replace(/_/g, "")).val(function(a, b) {
                    return b + c.merchantId + "|"
                });
                jQuery("#hidden_" + a).val("")
            })
        },
        error: function() {}
    })
}

function addingNewMerchantFilter(a) {
    jQuery("#" + a.id).autocomplete({
        minLength: 3,
        source: function(a, b) {
            jQuery.ajax({
                url: "http://api.prosperent.com/api/merchant",
                dataType: "jsonp",
                data: {
                    filterMerchant: jQuery.isNumeric(a.term) && 5 < a.term.length ? "" : "*" + a.term + "*",
                    filterMerchantId: jQuery.isNumeric(a.term) && 5 < a.term.length ? a.term : "",
                    sortBy: "merchant",
                    limit: 10
                },
                success: function(a) {
                    var c = [];
                    a.data && jQuery.each(a.data, function(a, b) {
                        c.push({
                            label: b.merchant,
                            value: b.merchantId
                        })
                    });
                    b(c)
                }
            })
        },
        select: function(c, b) {
            jQuery("#hidden_" +
                a.id).val().match(b.item.value) || jQuery("#hidden_" + a.id).val(function(a, c) {
                return c + b.item.value + "|"
            });
            jQuery("#" + a.id).val("");
            jQuery("#" + a.id + "Filters").append('<li data-savedvalue="' + b.item.value + '" class="' + a.id + '" data-filtype="' + a.id + '" style="float:left;margin:0;padding:6px;" onClick="removeFilter(this);"><span><a style="text-decoration:none;" id="' + b.item.value + '" href="javascript:void(0);">' + b.item.label + ' <i class="fa fa-times"></i></a></span></li>');
            return !1
        }
    })
}

function removeFilter(a) {
    var c = new RegExp(jQuery(a).attr("data-savedvalue") + "\\|", "g"),    
        b = a.className,
        c = jQuery("#hidden_" + b).val().replace(c, "");
    jQuery("#hidden_" + b).val(c);
    jQuery("." + b + "[data-savedvalue='" + jQuery(a).attr("data-savedvalue") + "']").empty()
};