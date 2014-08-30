# ui - extremely lightweight PHP framework

Started off as a **HTML-templating** and **URL-routing** script for quickly creating web applications, ui is now more than just a user interface manager. It now supports **modular libraries**, **hooks**, basic **benchmarking** and **logging** as well. 

The primary reason for developing *ui* was speed and efficiency. The PHP frameworks I encountered (I looked at *Model-View-Process* based frameworks) seemed inefficient as they consumed too much memory due to overly-populated namespaces, unnecessary abstractions and encapsulations; or were inconvenient to use due to strict naming conventions. 

With prime focus on minimal-overhead and flexibility, most features are modularly added using libraries, instead of being supplied with the core. Adding/using libraries, hooks or controls is extremely flexible with a minimal directory structuring scheme.
     
## Requirements

- ui uses PHP namespaces and hence requires at least PHP 5.
- Fully featured URL routing requires .htaccess support, mod_rewrite and PATH\_INFO enabled. However, URL routing will work partially with all or some of these disabled (detailed in [url routing](https://github.com/1upon0/ui/wiki/url-routing)).
- Depending upon the libraries you use, you might need CURL or other dependencies.

## Need Help?

Please browse through the [wiki](https://github.com/1upon0/ui/wiki)!