var shortCode={local_ed:"ed",init:function(b){shortCode.local_ed=b;tinyMCEPopup.resizeToInnerSize()},insert:function(b){b=getNewCurrent()?getNewCurrent():"";var d=jQuery("#"+b+"fetch").val(),e=jQuery("#"+b+"query").val(),f=jQuery("#state").val(),g=jQuery("#city").val(),h=jQuery("#zipcode").val(),k=jQuery("#"+b+"merchant").val(),l=jQuery("#"+b+"limit").val(),m=jQuery("#country").val(),n=jQuery("#"+b+"view:checked").val(),p=jQuery("#"+b+"goTo:checked").val(),q=jQuery("#"+b+"brand").val(),r=jQuery("#"+b+"id").val(),
s=jQuery("#height").val(),c=jQuery("#width").val(),t=jQuery("#topic").val(),u=jQuery("#useTags").is(":checked"),v=jQuery("#useTitle").is(":checked"),w=jQuery("#onSale").is(":checked"),x=jQuery("#gridimgsz").val(),y=jQuery("#prosperSC").val(),z=jQuery("#widthStyle:checked").val(),A=jQuery("#css").val(),a="["+y;e&&(a+=' q="'+e+'"');f&&(a+=' state="'+f+'"');g&&(a+=' city="'+g+'"');h&&(a+=' z="'+h+'"');p&&(a+=' gtm="'+p+'"');q&&(a+=' b="'+q+'"');k&&(a+=' m="'+k+'"');l&&(a+=' l="'+l+'"');m&&(a+=' ct="'+
m+'"');n&&(a+=' v="'+n+'"');r&&(a+=' id="'+r+'"');d&&(a+=' ft="'+d+'"');s&&(a+=' h="'+s+'"');a=c&&"auto"!=c?a+(' w="'+c+'"'):a+' w="auto"';z&&(a+=' ws="'+z+'"');A&&(a+=' css="'+A+'"');t&&(a+=' q="'+t+'"');u&&(a+=' utg="'+u+'"');v&&(a+=' utt="'+v+'"');w&&(a+=' sale="'+w+'"');x&&"prod"==b&&(a+=' gimgsz="'+x+'"');a+="]"+shortCode.local_ed.selection.getContent()+"[/"+y+"]";tinyMCEPopup.execCommand("mceReplaceContent",!1,a);tinyMCEPopup.close()}};tinyMCEPopup.onInit.add(shortCode.init,shortCode);
document.write('<base href="'+tinymce.baseURL+'" />');function getNewCurrent(){var b;jQuery("#products_tab").hasClass("current")?b="prod":jQuery("#coupons_tab").hasClass("current")?b="coup":jQuery("#merchant_tab").hasClass("current")?b="merchant":jQuery("#local_tab").hasClass("current")&&(b="local");return b};
