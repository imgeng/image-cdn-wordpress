=== Image CDN - WordPress CDN Plugin ===
Contributors: imageengine
Tags: image cdn, cdn, ImageEngine, image optimization, content delivery network, content distribution network
Requires at least: 4.6
Tested up to: 5.3
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enable CDN URLs for your static assets such as images, CSS or JavaScript files.

== Description ==

The Image CDN plugin improves your site's performance by serving static assets through a content delivery network.  This plugin is optimized for [ImageEngine](https://imageengine.io/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine) but will work with almost any CDN.

= How it works =

This plugin works by rewriting the URLs to your assets (images, javascript, css, etc), switching your domain for the CDN.  Users of [ImageEngine](https://imageengine.io/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine) can also configure the [Directives](https://imageengine.io/docs/implementation/directives/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine) for their assets, controlling things like image quality, automatic format conversion and automatic image resizing.

> Unlike other CDN plugins, the Image CDN plugin makes it simple to test your configuration before enabling it.

= Features =

* Maximize web performance by serving static assets from a CDN
* Set the WordPress directories that should be included
* Define excluded directories or extensions
* Enable or disable HTTPS support
* Turn on or off quickly without deactivating the plugin
* Test the CDN integration before saving your changes to make sure it will work properly
* Supports [ImageEngine Directives](https://imageengine.io/docs/implementation/directives/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine)
* Compatible with the [WordPress Cache Enabler](https://wordpress.org/plugins/cache-enabler/) plugin

= System Requirements =

* PHP >=7.0
* WordPress >=4.6

= Contribute =

* Anyone is welcome to contribute to the plugin on [GitHub](https://github.com/scientiamobile/image-cdn-wordpress).
* Please merge (squash) all your changes into a single commit before you open a pull request.

= Author =

* [ImageEngine](https://imageengine.io/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine)

> Note: this plugin was based on the [CDN Enabler](https://github.com/keycdn/cdn-enabler) plugin, but has diverged and will not track it.

== Frequently Asked Questions ==

= What is an Image CDN =

An [Image CDN](https://imageengine.io/what-is-an-image-cdn?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine) is just like a normal CDN, but with built in featurs to optimize images. With ImageEngine, this optimization happens automatically, according to the capabilities of the device or browser.

= What kinds of files can I serve through the CDN? =

You can serve any static files, for example, images, videos, audio files, css, javascript, documents, etc. Normally, all the files in the content directories (usually `wp-content` and `wp-includes`) are static and are safe to serve.  If you include other directories, be sure to exclude any files types that are not static.

= Can I use this plugin if my site is not yet on the public internet? =

If the CDN cannot contact your server over the internet, it will not be able to serve your content.  You can configure the plugin, but you should leave it disabled until your site is publicly accessible.  Some CDNs, like [ImageEngine](https://imageengine.io/?utm_source=wordpress.org&utm_medium=page&utm_term=wp-imageengine&utm_campaign=wp-imageengine) support fetching assets from behind password-protected sites and other origins like AWS S3.  These options may work for sites that are not publicly accessible.

== Upgrade Notice ==

Upgrades can be performed in the normal WordPress way, nothing else will need to be done.

== Screenshots ==

1. The Image CDN configuration screen showing a successful configuration test.

== Changelog ==

= 0.9.0 =
* Initial beta release

= 1.0.0 =
* Added live configuration testing
