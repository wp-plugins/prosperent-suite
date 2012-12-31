=== Prosperent Suite ===
Contributors: Prosperent Brandon
Tags: Prosperent, products, search, money, SEO, affiliate, links, ad, ads,
Requires at least: 3.0
Tested up to: 3.5
Stable tag: NA at this time
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Includes all Prosperent Plugins in one easy to use plugin. Contains: Product Search, Performance Ads, ProsperLinks, and Auto-Linker.

== Description ==

[Video Tutorial of Installation](http://youtu.be/pTEmLn_UGXg)

= Deactivate your other Prosperent Plugins, and use this plugin in their place. =

= Prosperent Product Search =

This plugin will help make/increase earnings from your blog. It will add a product search box to your wordpress site, which viewers can use to search for products.
When they click the product through your site they will be redirected to the Merchant's site. If they make a purchase you will earn a commission from the sale.

*Why Prosperent Product Search?*

Prosperent Product Search uses Prosperent's API, which is a very advanced API that offers you access to 3000 merchants and 50 million products from all of the top
online retailers. Stores like Zappos, 6pm, Best Buy, Overstock, REI, Advance Auto Parts, Kohl's, Gap, Banana Republic, Cabelas, and thousands more. If it is sold online,
we probably have it in our system.

= Prosperent ProsperLinks =

Adds ProsperLinks on a WordPress Page. This plugin places product links on the page that are relevant to the content.

*Why Prosperent ProsperLinks?*

Prosperent's ProsperLinks advanced algorithm analyzes your content in realtime and delivers laser targeted, on topic product references.

= Prosperent Auto-Linker =

Adds Auto-Linker buttons to HTML and Visual editor in WordPress for pages and posts. When text is highlighted an the button is pressed it will place [linker] short tags around the highlighted phrase connecting it to the Prosperent Product Search. Also puts Auto-Linker settings under the settings menu. Allowing you to link more commonly used phrases.

On the HTML editor the button is named 'auto-linker'. In the Visual Editor, the button is the one with the gears on it.

When using the Visual Editor, a dialog box will pop up when the button is clicked, allowing you to alter the query used with the highlighed phrase. once you hit submit if you changed the query it should look something like [linker q="ipad 4 case"]ipad 4[/linker]. The q variable will change the query used when someone clicks on Ipad 4 within the blog.

*Why Prosperent Auto-Linker?*

This plugin does a couple things. You can map words that appear in posts to product searches. For example, if you configure the plugin like so:

nike => nike shoes
soccer cleats

Anytime you publish a wordpress post with the word nike in it, it will be linked to a search result page for nike shoes. If you publish a post with soccer cleats in it, the plugin links you to a soccer cleats product search page.

The second addition is a tool that helps you quickly link words to product search pages when writing a new post. There is a Prosperent gear button. You simply highlight a word or phrase in your post, click the gear and it gets linked to a product search page.

= Prosperent Performance Ads =

Adds Performance Ads on a WordPress Page. The ads automatically scale in size and use your pages styling so they will look great on any page.

*Why Prosperent Performance Ads?*

Prosperent Performance ads have a content analyzer that will produce an ad based on content in each thread. This will give you targeted ads, which in turn will lead to more clicks and conversions.

The image below show the Performance Ads on a forum. The specific thread was related to Integra, and the items that Performance Ads pulled back were related to Integra.

* We have an ever growing community, which is always willing to answer questions and lend a helping hand, and our team here at Prosperent is also available on the forum. *

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
        * If you have Accessibility Mode activated it will show 'Add' next to the widget, click 'Add' instead of trying to drag it.
6.  There are Shortcodes available as well for the search results and the search bar for the Product Search portion. More can be found on those below in *Additional Notes for Installation*.
7.  When editing or creating a page or post there is also a button for the auto-linker, this will allow you to select text that you did not specify in the text and query option.

*Congratulations*, you now have access to Prosperent tools on your WordPress blog, available with many options to customize attributes of each.
Log in to **Prosperent.com** every so often to check your stats.

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
    * Go into your themes directory and open `index.php`. Add `<?php if(!$show_ads){ performance_ads(); $show_ads = 1; } ?>` after `<?php while ( have_posts() ) : the_post(); ?>` and before the `<?php endwhile; ?>`
* To add the performance ad to each post's page, before the comment section
    * Go into your themes directory and open `content-single.php`. Add `<?php performance_ads(); ?>` after `<?php endif; ?>` and before `</footer><!-- .entry-meta -->`
* If you want to add the search box in the header of your page, add `<?php prospere_header(); ?>` in your themes `header.php` file where you'd like it to be located.


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
8. **What are the comissions paid and terms?**
    * The commission rates vary from merchant to merchant, but we are always negotiating the highest rates in the industry. We pay out net30 like most networks. The only exception is when a merchant that we work with extends a commission based on their return policy. Our reporting interface reflects this and allows you to see the status of each commission. It's the same as what you would experience with any of the other affiliate networks like commission junction.

== Changelog ==

= 1.0 =
* First Release

== Notes ==

If you have any questions or suggestions, please feel free to ask me here or on the [Prosperent Community](http://community.prosperent.com/forum.php), or email me Prosperent Brandon at brandon@prosperent.com.
