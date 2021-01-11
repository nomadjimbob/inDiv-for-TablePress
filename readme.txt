=== inDiv for TablePress ===
Contributors: nomadjimbob
Tags: tablepress
Requires at least: 5.3
Tested up to: 5.6
Stable tag: 5.3
Requires PHP: 5.6.20
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Custom Extension for TablePress to automatically wrap the table in a DIV element.


== Description ==

Custom Extension for TablePress to automatically wrap the table in a DIV element. Add indiv=true to your tables to enclose your TablePress tables in a DIV with the class "indiv_tablepress"


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/indiv-for-tablepress` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Ensure that you have the TablePress plugin installed and active.
4. Add the shortcode in_div=true to your TablePress shortcode.


== Frequently Asked Questions ==

= What does this plugin do? =

It wraps the TablePress element in <div class="indiv_tablepress"></div> to allow easier styling for your theme/site.

= How do I apply inDiv to TablePress =

Simply add the shortcut in_div=true to a TablePress shortcode. You will end up with a basic TablePress shortcode like [table id=... indiv=true]

= Can I apply this to all TablePress tables =

Yes! In the TablePress > inDiv Options page, you can enable the option to automatically apply to all TablePress tables instead of per shortcode attribute.


== Changelog ==

= 1.0.3 =
* Fixed error when saving options

= 1.0.2 =
* Users can now enable the plugin to apply to all tables instead of per shortcode attribute
* Rewritten backend to be modular
* Now requires WordPress 5.3+ and PHP 5.6.20+

= 1.0.1 =
* Initial Release.
