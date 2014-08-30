# ui - extremely lightweight PHP framework

Started off as a **HTML-templating** and **URL-routing** script for quickly creating web applications, ui is now more than just a user interface manager. It now supports **modular libraries**, **hooks**, basic **benchmarking** and **logging** as well. 

The primary reason for developing *ui* was speed and efficiency. The PHP frameworks I encountered (I looked at *Model-View-Process* based frameworks) seemed inefficient as they consumed too much memory due to overly-populated namespaces, unnecessary abstractions and encapsulations; or were inconvenient to use due to strict naming conventions. 

With prime focus on minimal-overhead and flexibility, most features are modularly added using libraries, instead of being supplied with the core. Adding/using libraries, hooks or controls is extremely flexible with a minimal directory structuring scheme.

## Download

ui is licensed under **gnu bla bla license** and **free to use and modify for personal and commercial purposes**. However, please contribute to the project by pushing any bug-fixes, libraries or general features you code. 
 

- [Download](../get/v1.1.zip) latest stable version (v1.1)
- [Download](../get/master.zip) zipped master/HEAD
- Clone using *git* `git clone http://git.theappbin.com/ui`
- [Browse source code](../src) online
- Examples
    - Static 4-page website using bootstrap template - [demo](http://theappbin.com/demo/ui/static) - [download](ui-example-static.zip)
     
## Requirements

- ui uses PHP namespaces and hence requires at least PHP 5.
- Fully featured URL routing requires .htaccess support, mod_rewrite and PATH\_INFO enabled. However, URL routing will work partially with all or some of these disabled (detailed in [url routing](concept/url-routing)).
- Depending upon the libraries you use, you might need CURL or other dependencies.

## Bugs and Suggestions

Please use the [issue tracker](../issues "ui issue tracker") to report any bugs, feature requests or suggestions in general.

You are encouraged to contribute to the project with new libraries, bug fixes, features, wiki additions, etc. Please see [how to contribute](contribute) for details.

## Wiki Index

- [Wiki Home](home)
- Basic Concepts
    - [Directory structure](concept/dir-structure)
    - [Config files](concept/config)
    - [URL Routing](concept/url-routing)
    - [HTML Templating](concept/templating)
- [Usage](usage)
- [Libraries](libraries)
- [Hooks](hooks)
- [Logging](logging)
- [Benchmarking](benchmarking)
- Existing libraries
    - [Translation/Localization](lib/lang) - `lib_lang`
    - [MySQL Database](lib/db) - `lib_db`
    - [Disk based data/configuration storage](lib/data) - `lib_data`
    - [Authentication](lib/auth) - `lib_auth` and `lib_auth2`
    - [Caching](lib/cache) - `lib_cache`
    - [Content Filtering](lib/filter) - `lib_filter`
    - [Useful snippets](lib/func) - `lib_func`
    - [Spintax](lib/spintax) - `lib_spintax`
    - [OpenID](openid) - `lib_openid`
    - [Facebook Graph API](lib/fb) - `lib_fb`
    - [Amazon Web Services](lib/aws) - S3 - `lib_aws`
    - [Several small APIs - bit.ly, tumblr, etc.](lib/apis) - `lib_apis`
    - [HTML forms](lib/form) - `lib_form`
- [Contributing](contribute)
- [Download](#Download)
- [Made with ui](made-with)
- [License](license)

### About me

![Yash Gupta](https://graph.facebook.com/yash.technofreak/picture?height=128)

I am Yash Gupta, presently doing a major in *Computer Science* from *[Indian Institute of Technology, New Delhi](http://www.iitd.ac.in/ "Link to IITD website")*. I like to code ([available as a freelancer](http://thetechnofreak.com/contact)), listen to music and play *DotA*. You can reach me via [my website](http://thetechnofreak.com/contact) or email me at `yashg2 at gmail.com`. I am also on [facebook](http://facebook.com/yash.technofreak).