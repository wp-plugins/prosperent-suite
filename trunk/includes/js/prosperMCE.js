var shortCode={local_ed:"ed",init:function(b){shortCode.local_ed=b;tinyMCEPopup.resizeToInnerSize()},insert:function(b){b=getNewCurrent()?getNewCurrent():"";var d=jQuery("#"+b+"fetch").val(),e=jQuery("#"+b+"query").val(),f=jQuery("#state").val(),g=jQuery("#city").val(),h=jQuery("#zipcode").val(),k=jQuery("#"+b+"merchant").val(),l=jQuery("#"+b+"limit").val(),m=jQuery("#country").val(),n=jQuery("#"+b+"view:checked").val(),p=jQuery("#"+b+"goTo:checked").val(),q=jQuery("#"+b+"brand").val(),r=jQuery("#"+
b+"visit").val(),t=jQuery("#prodcelebname").val(),u=jQuery("#"+b+"id").val(),v=jQuery("#height").val(),c=jQuery("#width").val(),w=jQuery("#topic").val(),x=jQuery("#useTags").is(":checked"),y=jQuery("#useTitle").is(":checked"),z=jQuery("#onSale").is(":checked"),A=jQuery("#gridimgsz").val(),B=jQuery("#prosperSC").val(),C=jQuery("#widthStyle:checked").val(),D=jQuery("#css").val(),E=jQuery("#searchFor:checked").val(),F=jQuery("#sBarText").val(),G=jQuery("#sButtonText").val(),H=jQuery("#pricerangea").val(),
I=jQuery("#pricerangeb").val(),a="["+B;e&&(a+=' q="'+e+'"');f&&(a+=' state="'+f+'"');g&&(a+=' city="'+g+'"');h&&(a+=' z="'+h+'"');p&&(a+=' gtm="'+p+'"');q&&(a+=' b="'+q+'"');k&&(a+=' m="'+k+'"');l&&(a+=' l="'+l+'"');m&&(a+=' ct="'+m+'"');n&&(a+=' v="'+n+'"');u&&(a+=' id="'+u+'"');d&&(a+=' ft="'+d+'"');v&&(a+=' h="'+v+'"');a=c&&"auto"!=c?a+(' w="'+c+'"'):a+' w="auto"';C&&(a+=' ws="'+C+'"');D&&(a+=' css="'+D+'"');w&&(a+=' q="'+w+'"');x&&(a+=' utg="'+x+'"');y&&(a+=' utt="'+y+'"');z&&(a+=' sale="'+z+
'"');E&&(a+=' sf="'+E+'"');F&&(a+=' sbar="'+F+'"');G&&(a+=' sbu="'+G+'"');r&&(a+=' vst="'+r+'"');t&&(a+=' celeb="'+t+'"');(H||I)&&(a+=' pr="'+H+","+I+'"');A&&"prod"==b&&(a+=' gimgsz="'+A+'"');a+="]"+shortCode.local_ed.selection.getContent()+"[/"+B+"]";tinyMCEPopup.execCommand("mceReplaceContent",!1,a);tinyMCEPopup.close()}};tinyMCEPopup.onInit.add(shortCode.init,shortCode);document.write('<base href="'+tinymce.baseURL+'" />');
function getNewCurrent(){var b;jQuery("#products_tab").hasClass("current")?b="prod":jQuery("#coupons_tab").hasClass("current")?b="coup":jQuery("#merchant_tab").hasClass("current")?b="merchant":jQuery("#local_tab").hasClass("current")&&(b="local");return b}
function getNewCurrent(){var b;jQuery("#products_tab").hasClass("current")?b="prod":jQuery("#coupons_tab").hasClass("current")?b="coup":jQuery("#merchant_tab").hasClass("current")?b="merchant":jQuery("#local_tab").hasClass("current")&&(b="local");return b};