=== Prosperent Suite ===
Contributors: Prosperent Brandon
Tags: Prosperent, products, search, money, SEO, affiliate, links, ad, ads,
Requires at least: 3.0
Tested up to: 3.5
Stable tag: NA at this time
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

All of the Prosperent's Tools in one easy to use plugin to help monetize your blog.

== Description ==

= Deactivate any other Prosperent Plugins before use =

To view a demo of the Prosperent Suite, visit [Prosperent Demo](http://wordpress.prosperentdemo.com/)

= Prosperent Product Search =

This plugin will add a product search box to your wordpress site, which viewers can use to search for products. When they click a product through your site they will be redirected to the Merchant's site.
If they make a purchase you will earn a commission from the sale.

Prosperent Product Search uses Prosperent's API, which is a very advanced API that offers you access to 3000 merchants and 50 million products from all of the top
online retailers. Stores like Zappos, 6pm, Best Buy, Overstock, REI, Advance Auto Parts, Kohl's, Gap, Banana Republic, Cabelas, and thousands more. If it is sold online,
we probably have it in our system.

= Prosperent Auto-Linker =

Adds Auto-Linker buttons to HTML and Visual editor in WordPress for pages and posts. When text is highlighted and the button is pressed it will place [linker] short code tags around the highlighted phrase.
Also puts Auto-Linker settings under the settings menu that includes a text area allowing you to link more commonly used phrases.

This is an example of what to enter into the text-area in the setting for Auto-Linker

* nike => nike shoes
* soccer cleats

Anytime you publish a wordpress post with the word nike in it, it will be linked to a search result page for nike shoes. If you publish a post with soccer cleats in it, the plugin links you to a soccer cleats product search page.

In the Visual Editor, the button is the one with the chain link and Prosperent gears on it. In the HTML editor the button is named 'auto-linker'.

When using the Visual Editor, a dialog box will pop up when the button is clicked, allowing you to alter the query, brand/merchant filter and decide whether to go to merchant,
skipping the products page. A preview is displayed at the bottom of the dialog box based on what you have entered.

Example of auto-linker:

* [linker q="ipad 4 case"]ipad 4[/linker]

Inside the Auto-Linker dialog box you'll see options as follows:

* Query - the query to be used when someone clicks the link
* Brand - filters by brand
* Merchant - filters by merchant
* Go to Merchant - the link will go directly to the merchant's page, skipping the product's page

*If no matches are found and there are filters used, this tool will try again without the filters, and lastly in the rare case that there are no results it will not display a link.*

= Prosperent Auto-Comparer =

Adds Auto-Comparer buttons to HTML and Visual editor in WordPress for pages and posts.
This tool will place products or coupons on your page where you want them to appear. Simply put the cursor inside the page or post editor and click the Auto-Comparer button, if you are using the Visual Editor, the button is the one with the boxes and the Prosperent gears on it, in the HTML editor the button is named 'auto-compare'.

When using the Visual Editor, a dialog box will pop up when the button is clicked, allowing you to enter a query as well as other options listed below.

Example of Auto-Comparer:

* [compare q="puma shoes" m="Holabird Sports"][/compare]

Some queries will result in displaying a comparison between products if there are similar products in the catalog, if not if will display a poroduct or products based on the limit you use matching the query and/or filters.

If you select the option to use coupons, the Auto-Comparer will display coupon(s) based of the query and merchant filter.

Inside the Auto-Linker dialog you'll see options as follows:

* Query - the query to be used for the product or coupon
* Brand - filters by brand
* Merchant - filters by merchant
* Limit - Non-comparison and Coupon limit, defaults to 1 if nothing is entered
* Comparison Limit - Limit used for products with comparisons, defaults to 3
* Use Coupons - use coupons instead of products

*If no matches are found and there are filters used, this tool will try again without the filters, and lastly in the rare case that there are no results it will not display a product.*

= Prosperent Performance Ads =

Adds Performance Ads on a WordPress Page. The ads automatically scale in size and use your pages styling so they will look great on any page.

Prosperent Performance ads have a content analyzer that will produce an ad based on content in each thread. This will give you targeted ads, which in turn will lead to more clicks and conversions.

If Performance Ads doesn't find results based on the content, it will use the fallback, either your page's/post's tags, fallbacks that you set inside the Admin settings, or trends (top selling products)
At the moment, the tags fallback will only use your first tag on the page/post, we will be altering it to take the full list.

*We have an ever growing community, which is always willing to answer questions and lend a helping hand, and our team here at Prosperent is also available on the forum.*

== Installation ==

[Video Tutorial of Installation](http://youtu.be/pTEmLn_UGXg)

1.	Head over to [Prosperent](http://prosperent.com) and click Join, its *Free* to do so. Create your account and sign in.
    1. Once signed in, you will create an **Api Key** and find your **User Id** which you'll need to input in the settings.
    * Click the API tab in the menu, then click API Keys on the submenu and click Add New API Key. This will get you the API key you'll need so commissions can be tracked back to you. Name it whatever you'd like and you'll see that its created a key for you.
    * Next look for your User Id which can be found near your name in the upper right had corner.
2.	Upload the **prosperent-suite** folder to the **/wp-content/plugins/** directory.
3.	Activate the plugin through the *Plugins* menu in WordPress.
4.	Go to the **Prosperent Settings** under Settings and edit those that you'd like.
5.  Go to *Appearance* and then *Widgets* in your admin menu.
    * There are widgets available for the Search Bar for the Product Search and also two that allow you to place Performance Ads in your sidebar and/or footer.
        * If you have Accessibility Mode activated it will show 'Add' next to the widget, click 'Add' instead of trying to drag it.\
6.  When making a page/post there are two new buttons at your disposal, the Auto-Linker and Auto-Comparer.
    * Auto-Linker allows you to link words that when clicked will go to the product page or the merchant's site, depending on which options you choose to utilize. There is also a text-area in the settings to match more commonly used words.
    * Auto-Comparer allows you to place products or coupons within content on your pages/posts. Some products will use the comparison feature which will list similar products from other merchants so your viewer can find the best price.
7.  When editing or creating a page or post there is also a button for the auto-linker, this will allow you to select text that you did not specify in the text and query option.

*Congratulations*, you now have access to Prosperent tools on your WordPress blog, available with many options to customize attributes of each.
Log in to **Prosperent.com** every so often to check your stats.

= ShortCodes Available =

* [prosper_store][/prosper_store]- for the search results
* [prosper_search][/prosper_search]- for a search box

= Additional Notes for Installation =

* This plugin automatically creates a *new page* called product. Go into that page and change the title to whatever you would like to be visible.
* You can change the placeholder text in the *search bars* by using the **Search Bar Placeholder Text** setting under the Product Search section of the settings.
* The search bar is shortcoded as **[prosper_search][/prosper_search]**, you are able to post that whereever you'd like.
* Also, now that the results are shortcoded, you can add **[prosper_store][/prosper_store]** to any page.
* Reminder, if you use a different page from */products* to display search results change the **Base_URL** Setting.
* You do not necessarily need to add the search bar to a page, as the Product page has a search bar included with the search results. But it is recommended to increase the use.
* Reminder, if you use a page other than '/products' to display search results change the 'Base_URL' under the Product Search section of the settings.
* If your `product` page has a parent, make sure you assign that in the `Prosperent Settings` for `Parent Directory` under the Product Search section of the settings.

= Advanced Installation Options =

* To add the performance ad after the first post on each page
    * Go into your themes directory and open `index.php`. Add `<?php include_once(ABSPATH . 'wp-admin/includes/plugin.php'); if(!isset($show_ads) && is_plugin_active('prosper-suite/Prosper_Suite.php')){ performance_ads(); $show_ads = 1; } ?>` after `<?php while ( have_posts() ) : the_post(); ?>` and before the `<?php endwhile; ?>`
* To add the performance ad to each post's page, before the comment section
    * Go into your themes directory and open `content-single.php`. Add `<?php include_once(ABSPATH . 'wp-admin/includes/plugin.php'); if (is_plugin_active('prosper-suite/Prosper_Suite.php')) { performance_ads(); } ?>` after `<?php endif; ?>` and before `</footer><!-- .entry-meta -->`
* If you want to add the search box in the header of your page, add `<?php include_once(ABSPATH . 'wp-admin/includes/plugin.php'); if (is_plugin_active('prosper-suite/Prosper_Suite.php')) { prospere_header(); } ?>` in your themes `header.php` file where you'd like it to be located.

== Frequently Asked Questions ==

1. **What is Prosperent?**
    * Prosperent is a company that is serious about getting you the tools that simplify your life as an affiliate marketer. We manage relationships with merchants, clean datafeeds, and provide a variety of publisher tools to get products on your site quickly and easily.
2. **How many merchants does Prosperent work with?**
    * Currently over 2,000 and growing.
3. **How many products does Prosperent have?**
    * We currently index and search against almost 50 million products.
4. **Where can publishers go to get help?**
    * Our Community Forums are a fantastic resource. Our entire team is active on a daily basis, and we are always here to lend a helping hand no matter what the question may be.
5. **How do I get paid?**
    * Prosperent pays publishers net30 which means we pay you 30 days after commission event takes place. This gives merchants time to see if a product is returned, or otherwise needs to be delayed for whatever reason.
6. **How can we track our earnings?**
    * We have a comprehensive reporting system in place that allows you to see which pages are generating earnings, which city/state/country the sales are coming from, and which individual products and retailers are providing those sales.
7. **What is the revenue split?**
    * We take a 30% commission and pay you the other 70%. If you are a larger publisher this split changes to 80/20.
8. **What are the commissions paid and terms?**
    * The commission rates vary from merchant to merchant, but we are always negotiating the highest rates in the industry. We pay out net30 like most networks. The only exception is when a merchant that we work with extends a commission based on their return policy. Our reporting interface reflects this and allows you to see the status of each commission. It's the same as what you would experience with any of the other affiliate networks like commission junction.

== Changelog ==

= 1.2.3 =
* Performance Ads will use any tags you may have on a post/page for a fallback for the Sidebar and Footer Ad units, if there are no tags it will use the fallback you set in the admin settings

= 1.2.2 =
* Open Links in new window or tab changed to general settings, shop links now open in new window if you check that setting, does not apply to Ads though

= 1.2.1 =
* Quick fix for those who were having troubles with this plugin and Jetpack, pagination will be turned off if Jetpack is active

= 1.2 =
* Added Auto-Comparer tool
* Short codes will now go where you place them on the page instead of defaulting to the top
* Fallbacks for Auto-Linker and Auto-Comparer

= 1.1.1 =
* Uses native colors primarily, should allow it to work with more themese
* Fixed the transparency on the Visit Store Image

= 1.1 =
* Removed ProsperLinks
* AutoLinker has undergone a huge update
* Major performance increases across all tools

= 1.0 =
* First Release

== Upgrade Notice ==

Deactivate any other Prosperent Plugins before use.

== Notes ==

If you have any questions or suggestions, please feel free to ask me here or on the [Prosperent Community](http://community.prosperent.com/forum.php), or email me Prosperent Brandon at brandon@prosperent.com.
