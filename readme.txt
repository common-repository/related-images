=== Related Images ===
Contributors: johannesfosseus
Tags: images, related images, cms
Requires at least: 3.5
Tested up to: 4.0 beta3

Add images in different positions.
Excellent if you run a newspaper on the web and need variety in the pictures to your posts.

== Description ==

= This is still in beta =
So please dp not use in any production env.

= Use case =
You run a magazine, and you need a small tightly cropped image on the home page, and a different picture on the article page. Related Imges solves this and let you relate one or more images to different positions in a post.

= Usage =
On post pages you will get a box. Here you can press "Open the Media Manager" and then choose images. From the media manager you press "Add related images". Choose positions in a select list, and save the post.

To display images on your pages use something like <?php echo \RI\Frontend::get()->get_image( 'startpage', 'size' ); ?> (inside the loop) where you add the position and the images size.

Oh: This plugin use namespacing, and need at least php 5.3,

== Installation ==

1. Drop plugin in the plugin folder (or install from backend)
2. Activate

== Screenshots ==
1. Box where you add and position images to a post

== Changelog ==

= 2.0b2 =
* Clean up from lod files
* Adding custom positions
* Bugfixing some minor things

= 2.0b =
* This is a complete rewrite if the plugin

= 1.2.4 =
* Bugfix png images
* Bugfix by Jabberwo, get_correct_post_id(), many thanks

= 1.2.3 =
* Added a remove method, so the "Quick Edit" will not remove related images
* Added better cheack for revisions and post_id:s

= 1.2.2 =
* bugfix, wrong post version number used

= 1.2.1 =
* bugfix

= 1.2 =
* Prepare for wp 3.2, jquery fix
* Added settings page

= 1.1 =
* Added caption, height/width and Description til the template tags
* Added the box on Pages

= 1.0 =
* First public version