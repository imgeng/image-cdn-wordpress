=== ImageEngine Optimizer CDN – Convert to WebP & AVIF ===
Contributors: imageengine
Tags: image cdn, cdn, ImageEngine, image optimizer, content delivery network, image convert, avif, webp
Requires at least: 4.6
Tested up to: 5.7
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==

[ImageEngine’s Image CDN](https://imageengine.io/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine) plugin accelerates your WordPress or WooCommerce performance by optimizing images, converting them to WebP, JPEG2000, or AVIF, and delivering them through the ImageEngine content delivery network. The result is smaller image payload, faster page loading, improved Google PageSpeed Insights scores (Speed Index, Largest Contentful Paint, Time to Interactive), and a better user experience leading to more conversions or sales. To use this plugin with the ImageEngine Optimizer CDN, get a [free trial account here](https://imageengine.io/signup?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine).

https://vimeo.com/496085924

= How ImageEngine Works =

This plugin rewrites your image URLs to include the ImageEngine Delivery Address you recieve when you sign up for an ImageEngine account. This rewrite will allow ImageEngine to access your original images, instantly optimize and convert them, and deliver via the ImageEngine CDN. After configuring and enabling the plug in, image are delivered this way:

* When a visitor requests an image, ImageEngine CDN servers use client hints or device detection to identify the requesting devices and browser characteristics.
* Based on the browser characteristics, ImageEngine will resize, compress and convert images to WebP, JPEG 2000, or AVIF.
* The optimized image is delivered from the nearest ImageEngine CDN region. Subsequent requests are served instantly with WebP, JPEG 2000, or AVIF images and stored on ImageEngine's global CDN.

Other static content like fonts, CSS and JavaScript are also optimized by applying Brotli or gzip compression.

= What Makes ImageEngine Better Than Other CDNs or Digital Asset Management Platforms? =

* Delivers optimized WebP, JPEG 2000, or AVIF images 30% faster than other CDNs or Digital Asset Management platforms.
* Achieves up to 80% image payload reduction with no perceptible change in quality.
* Simple to install. Easy to test your configuration before enabling it. No need to move or upload images.
* Only CDN with true device-aware edge servers to drive superior, fine-tuned image optimization.
* Automatic image optimization of JPG, PNG, GIF, SVG, BMP, TIF into next generation formats like WebP, JPEG 2000, AVIF, Animated WebP, or MP4. You can also safely serve static, non-image files through ImageEngine.
* Delivers via its scalable global CDN network, with support for HTTPS, HTTP/2, WAF, and DDoS protection.

= Support Resources =

* [Quick Start documentation](https://imageengine.io/docs/setup/quick-start?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine)
* Contact our [customer success specialists](https://imageengine.io/docs/setup/overview#customer-success-24x7-support?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine)
* Read our full [WordPress Documentation](https://imageengine.io/docs/integration-guides/imageengine-wordpress?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine)
* [Test your website](https://demo.imgeng.in/) for image optimization improvements
* [Best practices](https://imageengine.io/docs/implementation/best-practices?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine) including resource hints, client hints, responsive images
* [ImageEngine Directives documentation](https://imageengine.io/docs/implementation/directives?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine)
* [Custom domain (CNAME) with HTTPS configuration](https://imageengine.io/docs/implementation/domain-name?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine)
* [Usage and performance statistics](https://imageengine.io/docs/analytics/statistics?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine) available in your [ImageEngine control panel](https://my.scientiamobile.com/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine)

= Pricing Plans =

You can get started with ImageEngine easily with a [free, no credit card required, 60 day trial](https://imageengine.io/signup?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine). We offer three plans:

* **Basic** - $49/month. Up to **100 GB** per month of optimized image payload. Includes HTTPs, advanced control panes, performance statistics, and email support
* **Standard** - $99/month. Up to **250 GB** per month. Includes 3 custom domains (CNAME) with HTTPS support. Priority onboarding support.
* **Pro** - pricing scales with usage volume.  WAF with DDoS protection. Dedicated edge servers available. Ticketed enterprise support. [Contact us](https://imageengine.io/contact?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine).

= Features & benefits =

* Maximize web performance by automatically serving optimized WebP, JPEG 2000, or AVIF images  from a CDN
* Set the WordPress directories that should be included
* Custom filters "image_cdn_url" and "image_cdn_html" included
* Define excluded directories or extensions
* Enable or disable HTTPS support
* Turn on or off quickly without deactivating the plugin
* Test the CDN integration before saving your changes to make sure it will work properly
* Supports [ImageEngine Directives](https://imageengine.io/docs/implementation/directives/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine). Users of ImageEngine can also configure the Directives for their assets to control image quality, automatic format conversion and automatic image resizing.
* Compatible with the [WordPress Cache Enabler](https://wordpress.org/plugins/cache-enabler/) plugin.
* Compatible with  WooCommerce, Gutenberg, Elementor, WPBakery / Visual Composer, Oxygen Builder, Divi and other popular page builders.

== System Requirements ==

* PHP >=5.6
* WordPress >=4.6

== Author ==

* [ImageEngine](https://imageengine.io/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine)

> Note: this plugin was based on the [CDN Enabler](https://github.com/keycdn/cdn-enabler) plugin, but has diverged and will not track it.

== Installation ==

The following are the steps to install the Image CDN plugin

1. In your WordPress Administration Panels, click on Add New option under Plugins from the menu.
2. Type `ImageEngine` in the search box in the top right corner.
3. Click the "Install Now" button on the Image CDN – WordPress CDN Plugin.
4. Activate the plugin
4. Go to `Settings` -> `Image CDN` and follow in the instructions on how to enable the service.


== Frequently Asked Questions ==

= What is an Image CDN =

An [Image CDN](https://imageengine.io/what-is-an-image-cdn?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine) is just like a normal CDN, but with built in features to optimize images. With ImageEngine, this optimization happens automatically, according to the capabilities of the device or browser.

= What kinds of files can I serve through the CDN? =

You can serve any static files, for example, images, videos, audio files, css, javascript, documents, etc. Normally, all the files in the content directories (usually `wp-content` and `wp-includes`) are static and are safe to serve.  If you include other directories, be sure to exclude any files types that are not static.

= Can I use this plugin if my site is not yet on the public internet? =

If the CDN cannot contact your server over the internet, it will not be able to serve your content.  You can configure the plugin, but you should leave it disabled until your site is publicly accessible.  Some CDNs, like [ImageEngine](https://imageengine.io/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine) support fetching assets from behind password-protected sites and other origins like AWS S3.  These options may work for sites that are not publicly accessible.

= How do I obtain a free ImageEngine trial =

To get started with ImageEngine you need to [sign up for an account](https://imageengine.io/signup?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine). Then you'll be provided with a unique CDN hostname to use with this plugin. The trial is free for 60 days, and can be cancelled anytime.

== Upgrade Notice ==

Upgrades can be performed in the normal WordPress way, nothing else will need to be done.

== Screenshots ==

1. The Image CDN configuration screen showing a successful configuration test.

2. Annotated screenshot of the configuration screen showing how to configure ImageEngine.

3. Annotated screenshot of the ImageEngine control panel and hostnames.


== Changelog ==

= 1.1.3 =
* Simplify handling of WP installations within subdirectories
* Automatically detect path setting and remove it from the settings page

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
