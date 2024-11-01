=== WooCommerce - Prevent Purchase ===
Contributors: sumobi
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EFUPMPEZPGW7L
Tags: woocommerce, sumobi, purchase, disable, prevent
Requires at least: 3.3
Tested up to: 4.1.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Prevents a product from being purchased

== Description ==

This plugin requires [WooCommerce](https://wordpress.org/plugins/woocommerce/ "WooCommerce"). It allows the site owner to prevent a product from being purchased by enabling a checkbox. It also allows you to override the message on a per-product basis. This will be useful for when you want to let customers know a product is only available in-store but still have the product listed on your website.

How does it work?

This is a very simple plugin that hooks into WooCommerce's woocommerce_is_purchasable filter. When a product is deemed unpurchasable the "add to cart" button on the main product listing is replaced with a "read more" button. When the customer views the single product page they are shown a message that the product is not available for purchase and the add to cart buttons are removed.

**Stay up to date**

*Become a fan on Facebook* 
[http://www.facebook.com/sumobicom](http://www.facebook.com/sumobicom "Facebook")

*Follow me on Twitter* 
[http://twitter.com/sumobi_](http://twitter.com/sumobi_ "Twitter")

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin

OR you can just install it with WordPress by going to Plugins >> Add New >> and type this plugin's name

Go to a product's edit/publish screen and enable the "Prevent Purchase" checkbox. Optionally you can enter in a custom message which is shown on the single product page.

= 1.0 =
* Initial release