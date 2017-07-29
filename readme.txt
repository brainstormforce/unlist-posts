=== Unlist Posts & Pages ===
Contributors: brainstormforce, Nikschavan
Donate link: https://www.paypal.me/BrainstormForce
Tags: post, unlist posts, hide posts, 
Requires at least: 4.4
Tested up to: 4.8.1
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Unlist Posts from dispying anywhere on the site, only access the post with a direct link.

== Description ==

Unlisted means your post/page will not come up in search results or on anywhere else on your site. Only those who have the link of the post can view it just like any normal post on the site. 

The Post could still be seen by anyone, but only if they guess the link!

The plugin updates MySQL query directly in WP_Query class of WordPress to leave out the posts which are unlisted by the user. As this modifies the core WP_Query, this also works very well with all the plugins which pull out posts/pages from database by using WP_Query, For eg. Posts slider plugins, <a href="https://www.ultimatebeaver.com/modules/advanced-posts/?utm_source=wp-repo&utm_campaign=unlist-posts&utm_medium=other-plugins">Advanced Posts</a> in <a href="https://www.ultimatebeaver.com/?utm_source=wp-repo&utm_campaign=unlist-posts&utm_medium=other-plugins">Ultimate Addon for Beaver Builder</a> etc.

= Supported & Actively Developed =
Need help with something? Have an issue to report? [Get in touch](https://github.com/Nikschavan/unlist-posts "Unlist Posts & Pages on GitHub"). with us on GitHub.

= Limitations Of the Plugin = 

- WP_Query has an a flag to 'Suppress' the filters. If any plugin is using this flag when querying the posts then the unlisted post will not be hidden in that plugin's output. 
- Similarly if any plugin is using a custom MySQL query, then the unlisted posts will not be hidden from it's output.

== Installation ==

1. Go to the *Plugins* menu and click *Add New*.
2. Search for *Unlist Posts*.
3. Click *Install Now* next to the *Unlist Posts* plugin.
4. Activate the plugin.

Just select option "Unlist Post" in any post of any type and that post will be hidden from the whole site, it can be only accessed if you have the direct link to the post.

== Changelog ==

= 1.0.3 =
* Fix: duplicate post ids being saved in the options array.

= 1.0.2 =
* New - Exclude the posts from wp_list_pages()

= 1.0.1 =
* New - Hide a page from search engines if it is unlisted.
* New - Hide the post from comments query if it is unlisted.
* Updated code architecture to be PHPCS compatible.

= 1.0.0 =
* Initial Release.
