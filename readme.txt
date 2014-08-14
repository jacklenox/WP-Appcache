=== WP Appcache ===
Contributors: jacklenox
Tags: appcache, caching, application cache, html5
Requires at least: 3.6
Tested up to: 3.9.2
Stable tag: 0.1.3
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows your WordPress site to leverage the awesomeness of the HTML5 Application Cache.

== Description ==

This plugin allows your site to make use of HTML5's Application Cache.

The HTML5 Application Cache is supported by almost all browsers (save for IE9 and below). When a site uses it, visitors' browsers download (or cache) content from that site into memory. Entire pages can be downloaded and stored. The browser will then load the cached pages from memory ahead of trying to contact the server.

This means that pages load incredibly quickly (sometimes less than 100ms). Once a page has loaded, the browser will then perform a background check to see if any content has changed on the site. If it has, that content is automatically loaded into memory too. Any cached content is available offline. In the case of a user browsing your site on a mobile device, they will still be able to view any content from your site (that they have already viewed) at lightning speeds. Even with a poor connection.

The important drawback to note here is that the cache is consulted before the server. This means that if content has changed, it won't be immediately reflected (the user would have to refresh again). However, this plugin has discreet workarounds to mostly prevent this happening.

This plugin takes a pragmatic approach and doesn't include any files in the manifest. It therefore only uses implicit caching. This means caching anything that the user actually visits. This may be improved upon in future releases.

This plugin also makes use of the WordPress Heartbeat API to continue background updates of the cache. This means that in the event of a user, for example, reading a post on your website. When you add a new post, the cache will be  updated while they're reading and when they return to the homepage, the new post will appear (if you have posts on your homepage of course).

= Demo =

See this plugin in action at this demo site: http://wp-appcache-demo.haveposts.com/

== Installation ==

1. Install the plugin directly from your dashboard or upload it to your `plugins` directory
1. Activate the plugin through the 'Plugins' menu in your dashboard
1. Sit back, relax, and let the caching commence

== Frequently Asked Questions ==

= Why doesn't this plugin have any settings? =
There don't really need to be any at this stage.

= Does this plugin cache the dashboard too? =
Not yet, but I'm experimenting with this right now, and it's looking pretty awesome!

= What does this plugin place in the manifest file? =
Nothing. The value of this plugin is entirely derived from implicit caching. I.e. anything that the user looks at on your site will be cached.

== Screenshots ==

Screenshots don't really do this plugin justice. I could screenshot load times of less than 100 milliseconds, but that would be crass.

== Changelog ==

= 0.1.3 =
* Minor readme and documentation fixes.

= 0.1.2 =
* Initial commit.

== Further reading ==

* [Application Cache is a Douchebag](http://alistapart.com/article/application-cache-is-a-douchebag) by Jake Archibald
* [A Beginner's Guide to Using the Application Cache](http://www.html5rocks.com/en/tutorials/appcache/beginner/) by Eric Bidelman
* [JavaScript and Application Cache](http://blog.jamesdbloom.com/JavaScriptAndApplicationCache.html) by James D Bloom
* [Getting off(line): appcache, localStorage for HTML5 apps that work offline - John Allsopp](https://www.youtube.com/watch?v=dN8e-QdYyCk)