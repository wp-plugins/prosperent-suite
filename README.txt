=== Prosperent Suite ===
Contributors: Prosperent Brandon
Tags: Prosperent, products, search, money, SEO, affiliate, links, ad, ads, product search, store, affiliate links, shortcode, Prosperent.com, monetize, make money, affiliate marketing, wordpress seo, seo wordpress, search engine optimization, advertising, earn money, easy, revenue, tool, comparison
Requires at least: 3.0
Tested up to: 3.5.2
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Prosperent's Tools in one plugin help monetize your blog with ease.

== Description ==

= Deactivate any other Prosperent Plugins before use =
= To ensure the new reroutes are working correctly, it's best to deactivate and reactivate the plugin. =

To view a demo of the Prosperent Suite, visit [Prosperent Demo](http://wordpress.prosperentdemo.com/)

This plugin contains the following Prosperent tools:

* **Product Search-** Create a store within your blog.
* **Performance Ads-** Display ads easily inside widgets, these ads are use an advanced content analyzer to display relevant ads.
* **Auto-Linker-** Easily link products to the store or the merchant.
* **Auto-Comparer-** Display a product comparison within your post or page to easily show visitors product information.

This suite will give you everything you need to easily start making money with your blog.

**Look at the screenshots to get a brief overview of the tools and what you can do with them.**

= Why choose Prosperent? =

Well these reasons should help you with that question:

* **We are here for you-** We are always around to answer questions, office guidance and make sure you have everything you need to make a living online.
* **Traffic optimization-** Our algorithm's are always making sure every click you send us goes to the HIGHEST PAYING and HIGHEST CONVERTING merchant at that moment.
* **Size-** Because of our high volume, we work with the best Merchants and negotiate the highest commissions out there. Yes, we take a percentage of each commission (30% by default) instead of charging you a monthly fee, but because of our size, we more than make up for that with higher commission rates.
* **Automatic access to top merchants-** Once you sign up with us (for Free), you have access to all 3000+ merchants that we work with.
* **Clean data-** We clean the data feeds and resize images while serving it all from our global Content Delivery Network which means the images on your site load lightning fast at all times
* **Big data-** We have mountains of conversion data. We know which merchants are converting, the brands that are hot, and the products that sell at various times throughout the year. We crunch all of this data and give you access to it all.
* **Limitless and fast!-** We handle over 1.6 billion requests per month with no signs of slowing down. Fire off a request to our api, search tens of millions of products, and get back results within a few milliseconds.
* **Join for free-** All of our competitors charge you a monthly fee to access their data and tools. We don't! Plain and simple, if you don't make money, we don't make money.

== Screenshots ==

1.	As you can see in the image, we can add a phrase, but we can also map that phrase to a more specific product. Say for example we want to link the word shoe to a specific Nike running shoe every time it appears on our blog.
    Simply type: shoes => Nike Running Shoes - Now, every instance of the word "shoes" on your blog is hyperlinked to a "Nike Running Shoes" product search results page.
2.	Highlight the product in your post and hit our Auto Link button in the editor. We pop up a screen so you can narrow down the product to an exact brand and merchant, determine if you want clicks on the link to go to the merchant directly, or to a search result page, and hit submit.
3.  This is what it will look like to your visitor, an ordinary link in your posts.
4.  When you click the link, you are either sent straight to the merchants site, or to your built in search page (which is also part of our plugin suite).
5.  While you are writing a new blog post (or editing an old one) you will now see a Prosperent Auto Compare icon in the editor tool bar. Click that and you can now type a product name, even restrict the results to a specific merchant/brand, and set the limit for the number of products to show or compare. You could also check the coupon box and enter a more generic term to display coupons instead (we have a few...well nearly 40,000 of those too . ) At the bottom of the box you see a preview of the product that will show up, then hit the submit button.
6.  This is how it will look in your blog post or page.
7.  This image shows a few of the features of the Prosperent Suite.--  A widget for the search bar, where a visitor can input a product search and be directed to the store. -- There's a sidebar ad unit, it analyzes the content of your blog to create a very targeted ad. -- A sidebar widget that displays the top sold products. -- And lastly, a content ad unit. It auto-scales to fit the content area. --
8.  This shows the store that is created by the plugin. It uses your blog's styling so it fits nicely within the content.

== Installation ==

= To ensure the new reroutes are working correctly, it's best to deactivate and reactivate the plugin. =

1.	Head over to [Prosperent](http://prosperent.com) and click Join, its *Free* to do so. Create your account and sign in.
    * Once signed in, you will create an **Api Key** and find your **User Id** which you'll need to input in the settings.
    * Click the API tab in the menu, then click API Keys on the submenu and click Add New API Key. This will get you the API key you'll need so commissions can be tracked back to you. Name it whatever you'd like and you'll see that its created a key for you.
    * Next look for your User Id which can be found near your name in the upper right had corner.
2.	Upload the **prosperent-suite** folder to the **/wp-content/plugins/** directory.
3.	Activate the plugin through the *Plugins* menu in WordPress.
4.	Go to the **Prosperent Settings** under Settings and edit those that you'd like.
5.  Go to *Appearance* and then *Widgets* in your admin menu.
    * There are widgets available for the Search Bar for the Product Search, Top Product widget and also two that allow you to place Performance Ads in your sidebar and/or footer.
    * *If you have Accessibility Mode activated it will show 'Add' next to the widget, click 'Add' instead of trying to drag it.*
6.  When making a page/post there are two new buttons at your disposal, the Auto-Linker and Auto-Comparer.
    * Auto-Linker allows you to link words to the product page or the merchant's site, depending on which options you choose to utilize. There is also a text-area in the settings to match more commonly used words.
    * Auto-Comparer allows you to place products or coupons within content on your pages/posts. Some products will use the comparison feature which will list similar products from other merchants so your viewer can find the best price.

*Congratulations*, you now have access to Prosperent tools on your WordPress blog, available with many options to customize attributes of each.
Log in to **Prosperent.com** every so often to check your stats.

= Available ShortCodes =

* `[prosper_store][/prosper_store]`- for the search results
* `[prosper_search][/prosper_search]`- for a search box

= Additional Notes for Installation =

* This plugin automatically creates a *new page* called product. Go into that page and change the title to whatever you would like to be visible.
* You can change the placeholder text in the *search bars* by using the **Search Bar Placeholder Text** setting under the Product Search section of the settings.
* Also, now that the results are shortcoded, you can add `[prosper_store][/prosper_store]` to any page.
* Reminder, if you use a different page from `/products` to display search results change the **Base_URL** Setting under the Product Search section.

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

= 2.0.4 =
* More Bug Fixes, Adjusted Page titles, de-cluttered No Results page, adjusted open graph rules (Facebook), fixed Auto-Linker, adjusted a few options

= 2.0.4 =
* Bug Fixes, fixed page titles, added Open Graph rules

= 2.0.2, 2.0.3 =
* trying to push new css rules for productPage and admin

= 2.0.1 =
* created header redirect for those who had pages indexed with the prior url structure

= 2.0 =
* clean, SEO friendly URLs
* expanded shop
* added new local deals endpoint
* updated Prosperent admin settings look, added tabs to better seperate settings
* added rich snippets
* trends added to No Results page
* displays price comparisons if they exist on product page
* added new settings, can turn on and off any endpoint, positive filters, and others

= 1.2.9 =
* adjusted the last revision, only worked if you were using PHP Version 5.3+

= 1.2.8 =
* bug fix for Auto-Comparer and Auto-Linker when no query is given

= 1.2.7 =
* optimizations

= 1.2.5, 1.2.6 =
* Bug Fixes

= 1.2.4 =
* Updated product search and auto-comparer to look more native to your page, uses more of the same styling as your blog
* Optimization, a lot of performance increases
* Changed some options, be sure to look at them
* Updated the readme, screenshots are now included to give a brief overview of the tools
* Added another widget, this one displays the top products that are selling through Prosperent

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
* Uses native colors primarily, should allow it to work with more themes
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
