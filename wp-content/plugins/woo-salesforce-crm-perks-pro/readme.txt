=== WooCommerce Salesforce Integration ===
Contributors: crmperks, sbazzi, asif876
Tags: salesforce, woocommerce salesforce integration, WooCommerce Salesforce, salesforce add-on, woocommerce integration with salesforce
Requires at least: 4.7
Tested up to: 5.7
Stable tag: 1.5.4
Version: 1.5.4
WC requires at least: 3.0
WC tested up to: 5.1
Requires PHP: 5.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WooCommerce Salesforce Plugin allows you to quickly integrate WooCommerce Orders with Salesforce CRM.

== Description ==

Easily create leads, contacts or any object in Salesforce when an order is placed via WooCommerce. Learn more at [crmperks.com](https://www.crmperks.com/plugins/woocommerce-plugins/woocommerce-salesforce-plugin/?utm_source=wordpress&utm_medium=directory&utm_campaign=readme)


== WooCommerce Salesforce Integration Setup ==

* Go to WooCommerce -> Settings -> Salesforce tab then add new account.
* Go to WooCommerce -> Salesforce Feeds tab then create new feed.
* Map required Salesforce fields to WooCommerce Order fields.
* Send your test entry to Salesforce CRM.
* Go to WooCommerce -> Salesforce Logs and verify, if entry was sent to Salesforce.

**Connect Salesforce Account**

You can connect Salesforce Account by Oauth 2.0 or Salesforce Organization ID if API is not enabled. Also you can connect multiple Saleforce accounts.

**Fields Mapping**

Simply Select Salesforce CRM Object then map WooCommerce Order/product/user fields to Salesforce Object(Account, Lead, Contact, Case, custom Object etc) fields.

**Export Event**

Choose event, when WooCommerce Order data should be sent to Salesforce. For example , send WooCommerce Order to Salesforce when Order Status changes to "processing".

**Primary Key**

Instead of creating new Object in salesforce, you can update old object by setting Primary Key field.

**Error Reporting**

If there is an error while sending data to Salesforce, an email containing the error details will be sent to the specified email address.

**CRM Logs**

Plugin saves detailed log of each entry whether sent or not sent to to Salesforce and easily resend an entry to Salesforce.

**Filter Orders**

By default all orders are sent to Salesforce, but you can apply filters & setup rules to limit the orders sent to Salesforce. For example sending Orders from specific city to Salesforce.

**Send Data As Notes**

You can send one to many WooCommerce Order fields data as an object Note in salesforce.

**Assign Objects**

An Object created/updated by one feed can be assigned to the Object created/updated by other feed.


[youtube https://www.youtube.com/watch?v=-9OTisEwItI]



<blockquote><strong>Premium Version.</strong>

Following features are available in Premium version only.<a href="https://www.crmperks.com/plugins/woocommerce-plugins/woocommerce-salesforce-plugin/?utm_source=wordpress&amp;utm_medium=directory&amp;utm_campaign=Salesforce_readme">WooCommerce Salesforce Integration</a>

<ul>
 	<li>Add WooCommerce Order Items to Salesforce.</li>
 	<li>Salesforce Custom fields.</li>
 	<li>Salesforce Phone Number fields.</li>
 	<li>Products Synchronization (from Woocommerce to Salesforce and from salesforce to Woocommerce).</li>
 	<li>Wordpress Users Synchronization (from Woocommerce to Salesforce).</li>
 	<li>Send WooCommerce Orders in bulk to Salesforce.</li>
 	<li>Add a lead/Contact to campaign in Salesforce CRM.</li>
 	<li>Assign owner to any object(Contact, lead , account etc) in Salesforce.</li>
 	<li>Assign object created/updated/found by one feed to other feed. For example assigning a contact to a custom Salesforce object.</li>
 	<li>Track Google Analytics Parameters and Geolocation of a WooCommerce customer.</li>
 	<li>Lookup lead's email and phone number using popular email and phone lookup services.</li>
</ul>
</blockquote>

== Premium Addons ==

We have 20+ premium addons and new ones being added regularly, it's likely we have everything you'll ever need.[View All Add-ons](https://www.crmperks.com/add-ons/?utm_medium=referral&amp;utm_source=wordpress&amp;utm_campaign=WC+salesforce+Readme&amp;utm_content=WC)

== Want to send data to other crm ==
We have Premium Extensions for 20+ CRMs.[View All CRM Extensions](https://www.crmperks.com/plugin-category/woocommerce-plugins/?utm_source=wordpress&amp;utm_medium=directory&amp;utm_campaign=salesforce_readme)



== Screenshots ==

1. Connect Salesforce Account.
2. Connect Multiple Salesforce Accounts.
3. Map Salesforce Fields to WooCommerce fields.
4. Create New Entry in Salesforce or Update Old Entry searched By Primary key.
5. Filter Orders.
6. Orders Sent to Salesforce.

== Frequently Asked Questions ==

= Where can I get support? =

Our team provides free support at <a href="https://www.crmperks.com/contact-us/">https://www.crmperks.com/contact-us/</a>.

= WooCommerce Integration with Salesforce =

* First Connect your Salesforce account to WooCommerce.
* Go to Salesforce feeds and Create a feed, select Object then map salesforce fields to WooCommerce fields.
* All New WooCommerce Orders will be automatically sent to your Salesforce account.
* You can Open any Order then click "Send to Salesforce" button.

= Woocommerce salesforce =

Woocommerce is a free wordpress plugin, When someone places Order via Woocommerce, you can send this data to Salesforce with this free WooCommerce Salesforce plugin.

= Woocommerce Salesforce connector =

* You can easily connect Salesforce to Woocommerce with free WooCommerce Salesforce plugin.
* Simply Connect your Salesforce account first.
* Create Salesforce feed then map fields.
* All new Orders will be automatically sent to Salesforce. 


== Changelog ==

= 1.5.5 =
* added "refund reason" field.

= 1.5.4 =
* fixed php8 issues.

= 1.5.3 =
* fixed update_profile hook.

= 1.5.2 =
* added campaign ID field.

= 1.5.1 =
* fixed "disable_rules" error.

= 1.5.0 =
* added "shpping as line item" feature
* fixed shipping fields
* fixed variation sku field.

= 1.4.9 =
* fixed "test connection" button
* fixed "custom filter" feature for products feed

= 1.4.8 =
* added product_cats and order notes fields.
* added user_caps field.

= 1.4.7 =
* fixed timezone issue in date_created field.
* fixed optin filters feature.

= 1.4.6 =
* fixed "hidden connection notice" issue

= 1.4.5 =
* fixed "double saving same feed" issue.
* fixed file_get_contents function for files.
* fixed "trigger feed events".
* added separate note title feature.

= 1.4.4 =
* fixed "self::$order is undefined".

= 1.4.3 =
* fixed "No fields in feed" error.

= 1.4.2 =
* added name and email as Primary key.
* fixed entities issue in multi-picklist value.

= 1.4.1 =
* fixed get objects list.

= 1.4 =
* added products and user Sync Support.
* added Saleforce Sandbox support.

= 1.0 =
*	Initial release.



