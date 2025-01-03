=== Event Ticket Booking ===
Contributors: shankarravi614
Tags: event, ticket, booking, UPI, QR code
Requires at least: 5.6
Requires at least: 7.3
Tested up to: 6.7
Stable tag: 1.0.0
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
This is a free plugin that offers event and ticket booking functionality with payments processed via UPI(Not included third party payment process). The plugin features a QR code image upload interface, enabling you to upload QR codes image in the admin panel
(image gets uploaded to server's uploads folder, there is no delete button for QR code uploads done. You can ofcourse upload again and it will display the new QR code but the older one will remain seated in the upload folder).

You will have access to an introduction page, an event creation and management interface, and an admin dashboard that displays data about individuals booking event tickets, along with a dedicated QR code settings page.
To get started, you need to create an event and use the shortcode provided on the introduction page. The booking form will appear when the shortcode is utilized. Once the plugin is installed, a walkthrough will be 
available in the admin dashboard to guide you through the setup process.

== Features ==
* Event listing with ticket booking capability.
* UPI payment integration through QR codes.
* Easy-to-use QR code upload interface.
* A confirmation message will appear below the form after successful booking.
* Admin panel to view all event and bookings.

== Installation ==
1. Upload the zip plugin files to the `/wp-content/plugins/` directory OR WordPress dashboard, navigate to Plugins > Add New, then click on "Upload Plugin."
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the shortcode `[evtb_booking]` & `[evtb-event]` to display the event listing and booking form on any page or post.

== Frequently Asked Questions ==

= How do I create a booking? =
After installing and activating the plugin, use the `[evtb_booking]` shortcode on any page or post to display the booking form. Fill in the required details and follow the on-screen instructions.

= How do I create a an event? =
After installing and activating the plugin, use the `[evtb_event]` shortcode on any page or post to display the events as a card view.

= Can I customize the QR code? =
Yes! You can upload your QR code image through the plugin settings, and it will be displayed on the booking form page.

= What if the email is not received? =
This plugin doesn’t have a notification system to send messages via WhatsApp, email, or SMS. However, after a successful booking, a ticket number along with your name and email address will be generated and displayed on the page below the form. You can take a screenshot or photo of this ticket number to present when you arrive at the event.

== Changelog ==
= 1.0.0 =
* Initial release of the Event Ticket Booking Plugin.

== Upgrade Notice ==
= 1.0.0 =
Initial release. No upgrade necessary.

== Screenshots ==
1. The plugin menu and submenus in the admin
2. Event admin page/interface
3. Booking form on a page.
4. Admin panel showing ticket bookings.
5. QR code admin setting page

== Support ==
If you encounter any issues or have questions, please reach out to the plugin support forum.