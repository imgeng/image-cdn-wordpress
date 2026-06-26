=== ImageEngine - Optimize the Images on Your WordPress Site Like No Other Plugin ===
Contributors: imageengine
Tags: image cdn, ImageEngine, avif, webp, jpegxl
Requires at least: 5.3
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically optimize and serve WEBP, AVIF and JPEGXL with ImageEngine.

== Description ==

ImageEngine Kube is enterprise-grade image optimization software that runs inside your infrastructure — not ours. You deploy it as a container in Kubernetes or Docker, point your existing CDN and image URLs to it, and get the same advanced optimization (device detection, auto-format conversion, smart compression) that powers 100,000+ domains — with none of the per-request billing.


= Key Features of ImageEngine: =

* Automatic Optimization: Effortlessly reduces image sizes by up to 80%, significantly speeding up page loading times on every device.
* Advanced Format Support: Automatically serves the best image format (WebP, JPEG XL, JPEG 2000, or AVIF) from a global CDN, ensuring optimal performance.
* Customizable Settings: Easily configure which WordPress directories to include, define excluded directories or extensions, and toggle HTTPS support.
* Seamless Integration: Works harmoniously with WordPress Cache Enabler, WooCommerce, Gutenberg, and major WordPress page builders like Elementor, Divi, and ThriveThemes.
* Enhanced Web Performance: Lower your website's bounce rates and improve SEO by optimizing over 60% of your site’s content - its images.
* Supports ImageEngine Directives: Fine-tune image quality, format conversion, and resizing directly through ImageEngine’s directive settings.
* Third-Party CDN Support: Using an existing CDN? ImageEngine works seamlessly with top Content Delivery Networks (CDNs) for fast, worldwide image delivery.

== Why ImageEngine? ==

Developed for simplicity, and control ImageEngine is designed for WordPress developers and users looking to enhance their website’s performance with minimal effort. Our unique value proposition lies in our device detection technology, which giants such as Google and Amazon use to ensure your images are optimized for any device, anywhere.

== What Our Customers Are Saying: ==

* “With ImageEngine, our webpage response time dropped to less than 2 seconds for 90% of our pages.” - A satisfied customer
* “Simple setup, great results. I appreciate the easy implementation and solid reliability of ImageEngine.” - Happy user.
* “An excellent product with even better support. ImageEngine was a clear leader in Page Speed performance upgrades.” - Grateful client.

See more of our reviews on G2.com.


== System Requirements ==

* PHP >=7.4
* WordPress >=5.3

== Author ==

* [ImageEngine](https://imageengine.io/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine)

> Note: this plugin was based on the [CDN Enabler](https://github.com/keycdn/cdn-enabler) plugin, but has diverged and will not track it.

== Installation ==

The following are the steps to install the Image CDN plugin

1. In your WordPress Administration Panels, click on Add New option under Plugins from the menu.
2. Type `ImageEngine` in the search box in the top right corner.
3. Click the "Install Now" button.
4. Activate the plugin.
5. Click `ImageEngine` in the left hand menu and follow the instructions to enable the service.
6. Copy and paste your ImageEngine Kube delivery address.
7. Test the connection by clicking the button
8. Enable and Save the setup.


== Screenshots ==

1. Initial screen after installing the ImageEngine plugin

2. After adding the delivery address, enable the plugin to start optimizing images.

3. Statistics panel.

4. Advanced settings.


== Changelog ==
= 1.2.8 =
* Kube compatability
* Version update

= 1.2.7 =
* Removed config .gitignore file
* Version update

= 1.2.6 =
* Updated vendor dependencies
* Version update

= 1.2.5 =
* Tested up to 6.8.1

= 1.2.4 =
* Added Image Engine "User email address needs verification!" exception to the register/login process
* Tested up to 6.7.1
* Readme updates

= 1.2.3 =
* Fixed: Email validation issues
* Tested up to 6.6.1

= 1.2.2 =
* Fixed: Image Engine create subscription, added newer endpoint
* Tested up to 6.5.5

= 1.2.1 =
* Fixed: analytics display
* Readme updates

= 1.2.0 =
* Added Image Engine registration in the settings page
* Added Image Engine login in the settings page
* Added Image Engine analytics in the settings page
* Tested up to 6.5.3

= 1.1.12 =
* Fixed: display of settings and test errors
* Tested up to 6.3.1

= 1.1.11 =
* Tested up to 6.3

= 1.1.10 =
* Tested up to 6.2.2

= 1.1.9 =
* Full Thrive Themes support
* ECT ClientHint fix

= 1.1.8 =
* Fixed: Add new client hints [#24](https://github.com/imgeng/image-cdn-wordpress/issues/24)
* Header: Permissions-Policy code updated
* version update

= 1.1.7 =
* Fixed: Update name of client hints [#19](https://github.com/imgeng/image-cdn-wordpress/issues/19)
* Header: Permissions-Policy code updated
* version update

= 1.1.6 =
* Readme updates

= 1.1.5 =
* New logo
* New ImageEngine control panel

= 1.1.4 =
* UI/UX improvements

= 1.1.3 =
* Simplify handling of WP installations within subdirectories
* Automatically detect path setting and remove it from the settings page
* HTTPS enabled by default

= 1.1.2 =
* Confirmed WordPress 5.7 compatibility
* Switched from jQuery to Javascript's fetch API
* Added recommendations in "Test Configuration"

= 1.1.1 =
* Improved CORS compatibility
* Removed downlink and ect hint
* Added Permissions-Policy header

= 1.1.0 =
* Fixed compatibility issue with Divi
* Increased performance
* Improved srcset handing
* Added support for Advanced Custom Forms REST API (ACF to REST API)
* Added support for WooCommerce REST API
* Added "image_cdn_url" and "image_cdn_html" filters for custom themes

= 1.0.5 =
* User interface updates

= 1.0.4 =
* Removed rtt hint

= 1.0.3 =
* Support for Client Hints and Feature Policy

= 1.0.2 =
* Updated readme with better documentation

= 1.0.1 =
* Fixed issue with blank content rendering

= 1.0.0 =
* Initial stable release

= 0.9.0 =
* Initial beta release

= 1.0.0 =
* Added live configuration testing
