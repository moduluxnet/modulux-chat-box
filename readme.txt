=== Modulux Chat Box ===
Contributors: modulux
Tags: chat, whatsapp, faq, support, woocommerce
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight floating Q&A chat box that helps visitors find answers first, before contacting you via WhatsApp or a custom link.

== Description ==

Modulux Chat Box is designed to reduce unnecessary direct messages by guiding visitors to predefined answers before they contact you.

Instead of immediately opening WhatsApp, visitors see a searchable list of frequently asked questions. Only if they confirm that their question is not listed can they proceed to contact you.

This approach helps:
- Reduce repetitive questions
- Save time for store owners and support teams
- Improve user experience without blocking communication

The plugin is fully self-contained, multilingual-friendly, and follows WordPress.org coding standards.

== Why Modulux Chat Box? ==

Many chat plugins rely on:
- External SaaS services
- Heavy icon libraries
- Tracking scripts
- Bloated frontend frameworks

Modulux Chat Box does **none** of these.

✔ No external APIs  
✔ No tracking or analytics  
✔ No third-party icon libraries  
✔ No SaaS dependencies  

Everything runs locally inside WordPress.

== Features ==

* Control visibility by post type
* Optionally limit display to specific pages
* Floating launcher button with full styling control
* Custom post type for Questions & Answers
* Searchable Q&A list
* Confirmation checkbox before enabling contact
* WhatsApp or custom contact URL
* WooCommerce product-aware message templates
* Working hours / offline message support
* Custom open triggers via CSS selector
* Optional overlay background when open
* Multilingual ready (Polylang / WPML)
* Accessible and keyboard-friendly UI

== Lightweight by Design ==

The plugin loads only what it needs:
- Small vanilla JavaScript file (no frameworks)
- Minimal CSS scoped to the plugin
- No frontend requests to external servers

HTML is rendered server-side and enhanced with JavaScript only where needed.

== Multilingual Support ==

* Q&As are stored as a custom post type and can be translated using Polylang or WPML
* All interface texts are translatable
* WPML configuration file included for option strings

== Installation ==

1. Upload the plugin folder to /wp-content/plugins/modulux-chat-box/
2. Activate the plugin through the "Plugins" menu
3. Go to Settings > Modulux Chat Box
4. Add your questions under "Chat Q&As"

== Frequently Asked Questions ==

= Can I control where the chat box appears? =
Yes. You can limit the chat box to specific post types and/or specific pages.  
If no restrictions are set, the chat box appears everywhere by default.

= Does this plugin track visitors? =
No. Modulux Chat Box does not track users, store personal data, or send data to external services.

= Does it require WhatsApp? =
No. WhatsApp is optional. You can use any custom URL instead.

= Does it work without WooCommerce? =
Yes. WooCommerce integration is optional and only used on product pages.

= Is JavaScript required? =
Yes, for opening the panel and searching Q&As. The plugin uses plain JavaScript without dependencies.

== Changelog ==

= 1.0.0 =
* Initial public release
