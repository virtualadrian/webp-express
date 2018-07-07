# WebP Express

Serve autogenerated WebP images instead of jpeg/png to browsers that supports WebP. Works on anything (media library, galleries, theme images etc).

## Description

This plugin let's you take advantage of the WebP image format with only a little effort. Install, configure, test, forget - and enjoy the increased performance of your website.

The plugin works by .htaccess magic coupled with an image converter. Basically, jpegs and pngs are routed to the image converter, unless the image converter has already converted the image. In that case, it is routed directly to the converted image. The images are saved in a subfolder to the "uploads" folder, preserving the same structure as the originals. In order to allow caching on CDN, the .htaccess rules add a "Vary" HTTP header when serving the WebP images.

The approach has the benefit that is works regardless of how an image found its way into your site. The plugin does not need to hook into Media Library events, Gallery events etc, because it does not need to maintain a complete collection of converted images. It makes it so much simpler -- and lighter.

*Note:*
The rules created in .htaccess are sensitive to the location of your image folder and the location of wordpress. If you at some point change one of these, the rules will have to be updated. .htaccess rules are updated whenever you change a setting (all configuration is actually stored in .htaccess, which allows the converter to run faster, than if it had the overhead of bootstrapping Wordpress)

*Note:*
Do not simply remove the plugin without deactivating it first. Deactivation takes care of removing the rules in the .htaccess file. With the rules there, but converter gone, your Google Chrome visitors will not see any jpeg images.

*Note:*
The plugin does not support multisite configurations. A donation may help fixing that ;)


## Installation

1. Upload the plugin files to the `/wp-content/plugins/webp-express` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure it (the plugin doesn't do anything until configured)
4. Verify that it works

You configure the plugin in Settings > WebP Express.

The *Image types to convert* option is initially set to "Do not convert any images!". This allows you to test that there is a working converter, before redirecting images to the converter.

WebPExpress uses [WebPConvert](https://github.com/rosell-dk/webp-convert) to convert images. This is how WebPConvert describes itself:
*The current state of WebP conversion in PHP is this: There are several ways to do it, but they all require something of the server setup. What works on one shared host might not work on another. WebPConvert combines these methods by iterating over them (optionally in the desired order) until one of them is successful - or all of them fail.*

These different converting methods are called *converters*.

The best converter is cwebp. So the first thing you should do is test whether the cwebp converter is working. Simply click "test" next to the converter. If it doesn't work, you can disable that converter (or try to make it work, by changing the server setup).
The next converter to try is wpc, which is equally good as cwebp in terms of quality / filesize ratio, but which is slower. wpc is an open source cloud service, which you will have to install on some other server. If this is too much work, continue to the next converter. In the converter settings, you can read about the individual converters. You can also head to the WebPConvert readme for more information.

Once, you have a converter, that works, when you click the "test"-button, you are ready to test the whole stack, and the rewrite rules. To do this, first make sure to select something other than "Do not convert any images!" in *Image types to convert*. Next, click "Save settings". This will save settings, as well as update the .htaccess.

If you are working in a browser that supports webp (ie Google Chrome), you will see a link "Convert test image (show debug)" just above the "Save settings" button. Click that to test if it works. The screen should show a textual report of the conversion process. If it shows an image, it means that the .htaccess redirection isn't working. It may be that your server just needs some time. Some servers has set up caching.

Note that the plugin does not change any HTML. In the HTML the image src is still set to ie "example.jpg". To verify that the plugin is working (without clicking the test button), do the following:

- Open the page in Google Chrome
- Right-click the page and choose "Inspect"
- Click the "Network" tab
- Reload the page
- Find a jpeg or png image in the list. In the "type" column, it should say "webp"

You can also append ?debug after any image url, in order to run a conversion, and see the conversion report. Btw: If you append ?reconvert after an image url, you will force a reconversion of the image.


## Limitations

* The plugin does not work on Microsoft IIS server
* The plugin has not been tested with multisite installation (it is on the roadmap!).
* The plugin has only been tested in Wordpress 4.7.5 and above, but I expect it to work in other versions.
* The plugin has not been tested in all possible Wordpress configurations. It has been tested in the following configurations: root install, subdir install, and subdir install with redirect from root (described as method 1 (here)[https://codex.wordpress.org/Giving_WordPress_Its_Own_Directory]).
* There might be compatability issues with other plugins. For example .htaccess rules from other plugins might interfere.

## Known compatability issues
* W3TotalCache: When CDN is enabled, W3TotalCache creates some .htaccess rules which interferes when they appear before the rules created by this plugin. You can move them by manually editing the .htaccess file

## Frequently Asked Questions

### How do I set the WebP quality?
You don't. The plugin will try to detect the quality of the source file, and use same quality as that. You do however have an option to select a maximum quality - usefull, because there is seldom any need for a quality above 85 on ordinary web content. In case quality of the source file cannot be determined (that feature requires that Imagick or GraphicsMagick is installed), it will be set to 70.

### How do I make this work with a CDN?
Chances are that the default setting of your CDN is not to forward any headers to your origin server. But the plugin needs the "Accept" header, because this is where the information is whether the browser accepts webp images or not. You will therefore have to make sure to configure your CDN to forward the "Accept" header.

The plugin takes care of setting the "Vary" HTTP header to "Accept" when routing WebP images. When the CDN sees this, it knows that the response varies, depending on the "Accept" header. The CDN is thus instructed not to cache the response on URL only, but also on the "Accept" header. This means that it will store an image for every accept header it meets. Luckily, there are (not that many variants for images)[https://developer.mozilla.org/en-US/docs/Web/HTTP/Content_negotiation/List_of_default_Accept_values#Values_for_an_image], so it is not an issue.

### How do I donate?
Putting this question in the "frequently" asked questions section is of course some mixture of humour, sarcasm and wishful thinking. In case there really is someone out there wanting to donate, you can simply write to me, and we can arrange. My contact information is available here https://www.bitwise-it.dk/contact. I have paypal and of course an ordinary bank account.

# Roadmap

* Test on multisite
* Display whether the server is able to detect quality of jpegs or not
* Make the fallback quality configurable (the quality to use, when quality of source file cannot be determined)