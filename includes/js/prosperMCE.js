var shortCode={local_ed:"ed",init:function(b){shortCode.local_ed=b;tinyMCEPopup.resizeToInnerSize()},insert:function(b){b=getNewCurrent()?getNewCurrent():"";var d=jQuery("#"+b+"fetch").val(),e=jQuery("#"+b+"query").val(),f=jQuery("#"+b+"merchant").val(),g=jQuery("#"+b+"limit").val(),h=jQuery("#"+b+"view:checked").val(),k=jQuery("#"+b+"goTo:checked").val(),l=jQuery("#"+b+"brand").val(),m=jQuery("#"+b+"visit").val(),n=jQuery("#"+b+"id").val(),p=jQuery("#topic").val(),q=jQuery("#useTags").is(":checked"),
r=jQuery("#useTitle").is(":checked"),t=jQuery("#onSale").is(":checked"),u=jQuery("#gridimgsz").val(),v=jQuery("#prosperSC").val(),w=jQuery("#css").val(),x=jQuery("#searchFor:checked").val(),y=jQuery("#sBarText").val(),z=jQuery("#sButtonText").val(),A=jQuery("#pricerangea").val(),B=jQuery("#pricerangeb").val(),C=jQuery("#noShow:checked").val(),D=jQuery("#imageType").val(),c=jQuery("#"+c+"category").val(),a="["+v;e&&(a+=' q="'+e+'"');k&&(a+=' gtm="'+k+'"');l&&(a+=' b="'+l+'"');f&&(a+=' m="'+f+'"');
g&&(a+=' l="'+g+'"');h&&(a+=' v="'+h+'"');n&&(a+=' id="'+n+'"');d&&(a+=' ft="'+d+'"');w&&(a+=' css="'+w+'"');p&&(a+=' q="'+p+'"');q&&(a+=' utg="'+q+'"');r&&(a+=' utt="'+r+'"');t&&(a+=' sale="'+t+'"');x&&(a+=' sf="'+x+'"');y&&(a+=' sbar="'+y+'"');z&&(a+=' sbu="'+z+'"');m&&(a+=' vst="'+m+'"');C&&(a+=' noShow="'+C+'"');D&&(a+=' imgt="'+D+'"');c&&(a+=' cat="'+c+'"');(A||B)&&(a+=' pr="'+A+","+B+'"');u&&"prod"==b&&(a+=' gimgsz="'+u+'"');a+="]"+shortCode.local_ed.selection.getContent()+"[/"+v+"]";tinyMCEPopup.execCommand("mceReplaceContent",
!1,a);tinyMCEPopup.close()}};tinyMCEPopup.onInit.add(shortCode.init,shortCode);document.write('<base href="'+tinymce.baseURL+'" />');function getNewCurrent(){var b;jQuery("#products_tab").hasClass("current")?b="prod":b="merchant";return b};

/*

var shortCode = {
local_ed: "ed",
init: function(b) {
    shortCode.local_ed = b;
    tinyMCEPopup.resizeToInnerSize()
},
insert: function(b) {
    b = getNewCurrent() ? getNewCurrent() : "";
    var d = jQuery("#" + b + "fetch").val(),
        e = jQuery("#" + b + "query").val(),
        f = jQuery("#" + b + "merchant").val(),
        h = jQuery("#" + b + "view:checked").val(),
        l = jQuery("#" + b + "brand").val(),
        n = jQuery("#" + b + "id").val(),
        t = jQuery("#onSale").is(":checked"),
        v = jQuery("#prosperSC").val(),
        w = jQuery("#css").val(),
        y = jQuery("#sBarText").val(),
        z = jQuery("#sButtonText").val(),
         = jQuery("#pricerangea").val(),
        B = jQuery("#pricerangeb").val(),
        A = jQuery("#pricerangea").val(),
        B = jQuery("#pricerangeb").val(),
        C = jQuery("#noShow:checked").val(),
        D = jQuery("#imageType").val(),
        c = jQuery("#" + c + "category").val(),
        a = "[" + v;
    e && (a += ' q="' + e + '"');
    l && (a += ' b="' + l + '"');
    f && (a += ' m="' + f + '"');
    h && (a += ' v="' + h + '"');
    n && (a += ' id="' + n + '"');
    d && (a += ' ft="' + d + '"');
    w && (a += ' css="' + w + '"');
    t && (a += ' sale="' + t + '"');
    y && (a += ' sbar="' + y + '"');
    z && (a += ' sbu="' + z + '"');
    C && (a += ' noShow="' + C + '"');
    D && (a += ' imgt="' + D + '"');
    c && (a += ' cat="' + c + '"');
    (A || B) && (a += ' pr="' + A + "," + B + '"');
    a += "]" + shortCode.local_ed.selection.getContent() + "[/" + v + "]";
    tinyMCEPopup.execCommand("mceReplaceContent", !1, a);
    tinyMCEPopup.close()
}
};
tinyMCEPopup.onInit.add(shortCode.init, shortCode);
document.write('<base href="' + tinymce.baseURL + '" />');

function getNewCurrent() {
var b;
jQuery("#products_tab").hasClass("current") ? b = "prod" : b = "merchant";
return b
}; 

*/