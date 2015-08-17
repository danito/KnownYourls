# KnownYourls

Plug-in to use your own Yourls url-shortener with Known.

install yourls from yourls.org on your server.

copy the yourls folder to IdnoPlugins and activate the plugin in Known/admin/plugins.

Save the secret token and the url to the yourls-api.php.

Shorten Url is triggered by yourKnown.site/share (use the bookmarklet or the Firefox app). 
It shortens the url you want to share, not the shorturl of your post.

If your Yourls is on the same server than your Known site (on a subdirectory), you might get some problems with your url-rewrite in your htaccess file. You might add some exceptions for your Known folder :

...
RewriteCond %{REQUEST_FILENAME} !Known
...


Thanks to @mapkyca 
Inspired by https://github.com/mapkyca/KnownBitlyShorten a plugin to use Bitly url shortener
