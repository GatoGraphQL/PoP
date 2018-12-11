![PoP](https://assets.getpop.org/wp-content/themes/getpop/img/pop-logo-horizontal.png)

# PoP — “Platform of Platforms”

PoP is a framework for building component-based websites. It enables the creation of a wide arrange of applications, from a data API to a complex website and anything in between, such as single-page applications, progressive web apps, social networks, decentralized sites, and many others.

PoP is composed of several layers, which can be progressively enabled to unlock further capabilities:

1. **A data API:**<br/>For fetching and posting data, accessible under `/page-url/?output=json`, and making all database data normalized under a relational structure
2. **A configuration API:**<br/>Retrieved through an API, configuration values must not be hardcoded on client-side files, making the application become more modular and maintainable
3. **Rendering on client-side:**<br/>Consume the API data through JavaScript templates to render the website in the client
4. **Isomorphic rendering on server-side:**<br/>Process the JavaScript templates in the server to produce the HTML already in the server

## API foundations

The component-based architecture of the API is based on the following foundations:

1. Everything is a module
2. The module is its own API
3. Reactivity

### 1. Everything is a module

A PoP application contains a top-most module (also called a component) which wraps other modules, which themselves wrap other modules, and so on until reaching the last level:

![In PoP, everything is a module](https://uploads.getpop.org/wp-content/uploads/2018/12/everything-is-a-module.jpg)

Hence, in PoP everything is a module, and the top-most module represents the page.

### 2. The module is its own API

Every module, at whichever level inside the component hierarchy (i.e. the composition of modules starting from the top-most, all the way down to the last level), is independently accessible through the API simply by passing along its module path in the URL: `/page-url/?output=json&modulefilter=modulepaths&modulepaths[]=path-to-the-module`

### 3. Reactivity

Updating database data and configuration data saved in the client throughout the user session makes the layouts using this data be automatically re-rendered.

_Note: Implementation of this feature is still in progress and not yet available_

## Framework design goals

The current design and implementation of the framework was done to achieve the following goals:

- High level of modularity:
    - Strict top-down module hierarchy: ancestor modules know their descendants, but not the other way around
    - One-way setting of props across modules, from ancestors to descendants
    - Configuration values through API, allowing to decouple modules from each other
- Minimal effort to produced a maximum output:
    - Isomorphism to produce HTML code client and server-side
    - Support to send any content on the website as transactional emails
    - Native code splitting
    - Native A/B testing
    - Native client-side state management
    - Native client-side layout cache
- Clearly decoupled responsibilities among PHP, JavaScript, JavaScript templates and CSS:
    - JavaScript templates for markup
    - JavaScript for user interactivity/dynamic functionality
    - CSS for styles
    - PHP for creating the modules
- Easy to divide responsibilities across team members
- JavaScript as progressive enhancement
- Aggressive caching, implemented across several layers: 
    - Pages and configuration in server
    - Content and assets through CDN
    - Content and assets through service workers and local storage in client
- Self documentation: 
    - The website is already the documentation for the API
    - Component pattern libraries are automatically generated by rendering each module on their own (through `modulefilter=modulepaths&modulepaths[]=path-to-the-module`)

## Open specification

PoP is in the process of decoupling the API specification from the implementation, resulting in the following parts:

1. The API (JSON response) specification
2. PoP Server, to serve content based on the API specification
3. PoP.js, to consume the content in the client

We will soon release the current implementation of PoP Server and PoP.js:

- PoP Server for WordPress, based on PoP Server for PHP
- PoP.js through vanilla JS and Handlebars templates

Through the open specification, we will promote PoP being implemented in other technologies (eg: Node.js, Java, .NET, etc), as to enable any site implementing the specification to be able to interact with any other such site, no matter which their underlying technology. 

_Note: The release of the deliverables mentioned above will be done in stages, and expected to be fully completed by the second quarter of 2019._

### CMS-agnostic (work in progress)

Because it was originally conceived for WordPress, PoP's current implementation is in PHP, which can be perfectly used for other PHP-based CMSs (such as Joomla or Drupal). For this reason, we are transforming the codebase to make PoP become CMS-agnostic, splitting plugins into 2 entities: a generic implementation that should work for every CMS (eg: "pop-engine") and a specific one for WordPress (eg: "pop-engine-wp"), so that only the latter one should be re-implemented for other CMSs. 

_Note: This task is a work in progress and nowhere near completion: plenty of code has been implemented following the WordPress architecture (eg: basing the object model on posts, pages and custom post types), and must be assesed if it is compatible for other CMSs._

## The API (JSON response) specification

The examples below demonstrate the structure of the API specification.

### 1. Data API layer

At its most basic, PoP is an API for retrieving data, accessible under `/page-url/?output=json`, which normalizes database data under a relational structure under section `databases`, indicates which are the results for each component through entry `dbobjectids` under section `datasetmoduledata`, and where to find those results in the database through entry `dbkeys` under section `modulesettings`. 

_Response from calling `/page-url/?output=json`:_

```
{
  databases {
    primary {
      posts: {
        1: {
          author: 7, 
          comments: [88]
        },
        2: {
          recommendedby: [5], 
          comments: [23]
        },
      },
      users: {
        5: {
          name: "Leo"
        },
        7: {
          name: "Pedro"
        },
        18: {
          name: "Romualdo"
        }
      },
      comments: {
        23: {
          author: 7, 
          post_id: 2, 
          content: "Great stuff!"
        },
        88: {
          author: 18, 
          post_id: 1, 
          content: "I love this!"
        }
      }
    }
  },
  datasetmoduledata {
    "topmodule" {
      modules: {
        "datamodule1": {
          dbobjectids: [1], 
        },
        "datamodule2": {
          dbobjectids: [2], 
        }
      }
    }
  },
  modulesettings {
    "topmodule" {
      modules: {
        "datamodule1": {
          dbkeys: {
            id: "posts",
            author: "users",
            comments: "comments"
          }
        },
        "datamodule2": {
          dbkeys: {
            id: "posts",
            recommendedby: "users",
            comments: "comments"
          }
        }
      }
    }
  }
}
```

### 2. Configuration API layer

In addition to retrieving database data, the API can also return configuration values:

```
{
  modulesettings {
    "topmodule" {
      modules: {
        "layoutpostmodule": {
          configuration: {
            class: "text-center"
          },
          modules: {
            "titlemodule": {
              configuration: {
                class: "title bg-info",
                htmltag: "h3"
              }
            },
            "postcontentmodule": {
              configuration: {
                maxheight: "400px"
              },
              modules: {
                "authoravatarmodule": {
                  configuration: {
                    class: "img-thumbnail",
                    maxwidth: "50px"
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}
```

## Differences with other APIs

The PoP API has several similarities and differences with REST and GraphQL:

<table>
<thead><th>&nbsp;</th><th>REST</th><th>GraphQL</th><th>PoP</th></thead>
<tr><th>Endpoint</th><td>Custom endpoints based on resources</td><td>1 endpoint for the whole application</td><td>1 endpoint per page, simply adding parameter `output=json` to the page URL</td>tr>
<tr><th>Retrieved data</th><td>All data for a resource</td><td>All data for all resources in a component</td><td>All data for all resources in a component, for all components in a page</td></tr>
<tr><th>How are data fields retrieved?</th><td>Implicitly: already known on server-side</td><td>Explicitly: only known on client-side</td><td>Implicitly: already known on server-side</td></tr>
<tr><th>Does it under/over-fetch data?</th><td>Yes</td><td>No</td><td>No</td></tr>
<tr><th>Is data normalized?</th><td>No</td><td>No</td><td>Yes</td></tr>
<tr><th>Support for configuration values?</th><td>No</td><td>No</td><td>Yes</td></tr>
<tr><th>Cacheable on server-side?</th><td>Yes</td><td>No</td><td>Yes</td></tr>
</table>

## When to use PoP

Among many others, PoP is suitable for the following use cases:

### To reduce the complexity of the application, and maximize the output from a small team

From a single source of truth it is possible to produce HTML on the server-side, client-side and for transactional emails, so only 1 person can handle all of them. In addition, the architecture provides several features out of the box (code splitting, A/B testing, client-side state management and layout cache), so there is no need to implement them as is typically the case. Finally, the database data + configuration API can be used to power more than one application (eg: a website + a mobile app + interact with 3rd party services) with no big effort.

### To make the application easily maintainable in the long term

PoP modules are focused on a single task or goal. Hence, to modify a functionality, quite likely just a single module need be updated, and because of the high degree of modularity attained by the architecture, other modules will not be affected. In addition, each module is cleary decoupled through the use of PHP, CSS, JavaScript templates and JavaScript for functionalities, so only the appropriate team members will need work on the module (for instance, there is no CSS in JS).

PoP also attains a significant level of self-documentation on two aspects: the website is already the documentation for the API, and component pattern libraries can be automatically generated by rendering each module on their own, reducing the amount of documentation that needs be produced.

### To bring the benefits of components to the server-side

Using components as the building unit of a website has many advantages over other methods, such as through templates. Modern frameworks bring the magic of components to the client-side for functionality (such as JavaScript libraries React and Vue) and for styles through component pattern libraries (such as Bootstrap). PoP extends the benefits of coding with components to the server-side.

### To avoid JavaScript fatigue

PoP is not a JavaScript framework, but a framework spanning into the server and client-side. While developers can add client-side JavaScript to enhance the application, it is certainly not a requirement, and powerful applications can be created with minimum to no knowledge of JavaScript.

### To implement COPE or similar techniques

[COPE](https://www.programmableweb.com/news/cope-create-once-publish-everywhere/2009/10/13) (Create Once, Publish Everywhere) is a technique for creating several outputs (website, AMP, newsletters, mobile app, etc) from a single source of truth. PoP supports the implementation of COPE or similar techniques, through an array of native features: API supporting configuration values, the creation of alternative component hierarchies, and the possibility to evaluate data and produce a response depending on the context.

### To decentralize data sources across domains

PoP supports to set a module as lazy, so that its data is loaded from the client instead of the server, and change the domain from which to fetch the data to any other domain or subdomain, on a module by module basis, which enables to split an application into microservices. In addition, PoP allows to establish more than one data source for any module, so a module can aggregate its data from several domains concurrently, as demonstrated by the content aggregator SukiPoP's [feed](https://sukipop.com/en/posts/), [events calendar](https://sukipop.com/en/calendar/) and [user's map](https://sukipop.com/en/users/?format=map).

### To make the most out of cloud services

From the two foundations "everything is a module" and "a module is its own API", we conclude that everything on a PoP site is an API. And since APIs are greatly suitable to take advantage of cloud services (for instance, serving and caching the API response through a CDN), then PoP calls the cloud "home".

## Motivation

PoP is a very ambitious project. We have been working on it for more than 5 years, and we are still on the early stages of it (or so we hope!) It was created by Leonardo Losoviz, and aided by his wife Jun-E Tan, as a solution to connect communities together directly from their own websites, offering an alternative to always depending on Facebook and similar platforms.

![Jun-E and Leo](https://uploads.getpop.org/wp-content/uploads/2018/12/jun-e-leo.jpg)

PoP didn't start straight as a framework, but as a website for connecting environmental movements in Malaysia, called [MESYM](https://www.mesym.com). After developing plenty of social networking features for this website, we became aware that the website code could be abstracted and turned into a framework for implementing any kind of social network. The PoP framework was thus born, after which we launched a few more websites: [TPPDebate](https://my.tppdebate.org) and [Agenda Urbana](https://agendaurbana.org). 

We then worked towards connecting all the platforms together, so each community could own its own data on their website and share it with the other communities, and break away from depending on the mainstream platforms. We implemented the decentralization feature in PoP, and launched the demonstration site [SukiPoP](https://sukipop.com), which aggregates data from these previous websites and enables different community members to interact with each other.

However, at this point PoP was not a progressive framework for building any kind of site, but a framework for building social networks and nothing else. It was all or nothing, certainly not ideal. For this reason, most of 2018 we have been intensively working on transforming PoP into an all-purpose site builder, which led us to design the component-based architecture for the API, split the framework into several layers, and decouple the API specification from the implementation. 

Through the upcoming new PoP it will be finally possible to build any type of site. If the open specification succeeds at attracting the development community and eventually gets implemented for other CMSs and technologies, our dream of connecting sites together will be nearer to realization. This is the dream that drives us forward and keeps us working long into the night.

## Examples of PoP sites

The following are PoP websites in the wild. These 2 sites below were built for demonstration purposes, so you are encouraged to play with them (create a random post or event, follow a user, add a comment, etc):

- Social Network [PoP Demo](https://demo.getpop.org)
- Decentralized Social Network [SukiPoP](https://sukipop.com)

These 2 sites are proper, established social networks:

- Malaysia-based environmental platform [MESYM](https://www.mesym.com)
- Buenos Aires-based civic society-activism platform [Agenda Urbana](https://agendaurbana.org)

## Installation

Have your WordPress instance running (the latest version of WordPress can be downloaded from [here](https://wordpress.org/download/). Then download, install and activate the 7 plugins from this repository:

- pop-cms
- pop-cms-wp
- pop-cmsmodel
- pop-cmsmodel-wp
- pop-engine
- pop-engine-wp
- pop-examplemodules

The first 6 plugins are needed to produce the PoP API, and the 7th plugin (pop-examplemodules) provides basic implementations of modules for all supported hierarchies (home, author, single, tag, page and 404). 

That's it. You can then access PoP's API by adding parameter `output=json` to any URL on the site: https://yoursite.com/?output=json.

![If adding parameter output=json to your site produces a JSON response, then you got it!](https://uploads.getpop.org/wp-content/uploads/2018/12/api-json-response.png)

_Note 1: Currently PoP runs in WordPress only. Hopefully, in the future it will be available for other CMSs and technologies too._

_Note 2: Only the API has been released so far; we are currently implementing the client-side and server-side rendering layers, which should be released during the first quarter of 2019._

_Note 3: The retrieved fields are defined in plugin pop-examplemodules. You can explore the contents of this plugin, and modify it to bring more or less data._

### Enhancement: enable PoP only if required

Because currently PoP only works as an API and not to render the site, it can then be enabled only if needed, which is when parameter `output=json` is in the URL or when we are in the wp-admin area. 

Simply add this line to wp-config.php:

```
define('POP_SERVER_DISABLEPOP', !($_REQUEST['output'] == 'json' || substr($_SERVER['REQUEST_URI'], 0, 10) == '/wp-admin/'));
```
<!-- 
## Linked resources

- [Implementing a module](Link to pop-examplemodules/README.md)
- Documentation:
  - [fieldprocessors](https://getpop.org/en/documentation/...)




-->
<!-- 
Below is a technical summary. For a more in-depth description, please visit [PoP's documentation page](https://getpop.org/en/documentation/overview/).

## What is PoP?

PoP creates [Single-Page Application](https://en.wikipedia.org/wiki/Single-page_application) websites, by combining [Wordpress](https://wordpress.org) and [Handlebars](http://handlebarsjs.com/) into an [MVC architecture](https://en.wikipedia.org/wiki/Model-view-controller) framework:

- Wordpress is the model/back-end
- Handlebars templates are the view/front-end
- the PoP engine is the controller

![How it works](https://uploads.getpop.org/wp-content/uploads/2016/10/Step-5-640x301.png)

## Design principles

1. PoP provides the WordPress website of its own API:

 - Available via HTTP
 - By adding parameter `output=json` to any URL

2. Decentralized

 - All PoP websites can communicate among themselves
 - Fetch/process data in real time

## What can be implemented with it?

- Niche social networks
- Decentralized websites
- Content aggregators
- Server back-end for mobile apps
- Microservices
- APIs for Wordpress websites

## Installation

We are currently creating scripts to automate the installation process, we expect them to be ready around mid-October 2018.

Until then, we provide a zip file including all code (PoP, WordPress and plugins), and a database dump from the [GetPoP Demo website](https://demo.getpop.org/), to set-up this same site in a quick-and-dirty manner in your localhost. Download the files and read the installation instructions [here](https://github.com/leoloso/PoP/blob/master/install/getpop-demo/install.md).

## Configuration

PoP allows the configuration of the following properties, done in file wp-config.php:

- `POP_SERVER_USEAPPSHELL` (_true_|_false_): Load an empty Application Shell (or appshell), which loads the page content after loading.

- `POP_SERVER_USESERVERSIDERENDERING` (_true_|_false_): Produce HTML on the server-side for the first-loaded page.

- `POP_SERVER_USECODESPLITTING` (_true_|_false_): Load only the .js and .css that is needed on each page and nothing more.

- `POP_SERVER_USEPROGRESSIVEBOOTING` (_true_|_false_): If doing code splitting, load JS resources on 2 stages: critical ones immediately, and non-critical ones deferred, to lower down the Time to Interactive of the application.

- `POP_SERVER_GENERATEBUNDLEGROUPFILES` and `POP_SERVER_GENERATEBUNDLEFILES` (_true_|_false_): (Only if doing code-splitting) When executing the `/generate-theme/` build script, generate a single bundlegroup and/or a series of bundle files for each page on the website containing all resources it needs.

- `POP_SERVER_GENERATEBUNDLEFILESONRUNTIME` (_true_|_false_): (Only if doing code-splitting) Generate the bundlegroup or bundle files on runtime, so no need to pre-generate these.

- `POP_SERVER_GENERATELOADINGFRAMERESOURCEMAPPING` (_true_|_false_): (Only if doing code-splitting) Pre-generate the mapping listing what resources are needed for each route in the application, created when executing the `/generate-theme/` build script.

- `POP_SERVER_ENQUEUEFILESTYPE` (_resource_|_bundle_|_bundlegroup_): (Only if doing code-splitting) Choose how the initial-page resources are loaded:

    - "resource": Load the required resources straight
    - "bundle": through a series of bundle files, each of them comprising up to x resources (defined through constant `POP_SERVER_BUNDLECHUNKSIZE`)
    - "bundlegroup": through a unique bundlegroup file

- `POP_SERVER_BUNDLECHUNKSIZE` (_int_): (Only if doing code-splitting) How many resources to pack inside a bundle file. Default: 4.

- `POP_SERVER_TEMPLATERESOURCESINCLUDETYPE` (_header_|_body_|_body-inline_): (Only if doing server-side rendering, code-splitting and enqueue type = "resource") Choose how to include those resources depended by a module (mainly CSS styles):

    - "header": Link in the header
    - "body": Link in the body, right before the module HTML
    - "body-inline": Inline in the body, right before the module HTML

- `POP_SERVER_GENERATERESOURCESONRUNTIME` (_true_|_false_): Allow to extract configuration code from the HTML output and into Javascript files on runtime.

- `POP_SERVER_USEMINIFIEDRESOURCES` (_true_|_false_): Include the minified version of .js and .css files.

- `POP_SERVER_USEBUNDLEDRESOURCES` (_true_|_false_): (Only if not doing code-splitting) Insert script and style assets from a single bundled file.

- `POP_SERVER_USECDNRESOURCES` (_true_|_false_): Whenever available, use resources from a public CDN.

- `POP_SERVER_SCRIPTSAFTERHTML` (_true_|_false_): If doing server-side rendering, re-order script tags so that they are included only after rendering all HTML.

- `POP_SERVER_REMOVEDATABASEFROMOUTPUT` (_true_|_false_): If doing server-side rendering, remove all database data from the HTML output.

- `POP_SERVER_TEMPLATEDEFINITION_TYPE` (_0_|_1_|_2_): Allows to replace the name of each module with a base36 number instead, to generate a smaller response (around 40%).

    - 0: Use the original name of each module
    - 1: Use both
    - 2: Use the base36 counter number

- `POP_SERVER_TEMPLATEDEFINITION_CONSTANTOVERTIME` (_true_|_false_): When mangling the template names (template definition type is set to 2), use a database of all template definitions, which will be constant over time and shared among different plugins, to avoid errors in the website from accessed pages (localStorage, Service Workers) with an out-of-date configuration.

- `POP_SERVER_TEMPLATEDEFINITION_USENAMESPACES` (_true_|_false_): If the template definition type is set to 2, then we can set namespaces for each plugin, to add before each template definition. It is needed for decentralization, so that different websites can communicate with each other without conflict, mangling all template definitions the same way. (Otherwise, having different plugins activated will alter the mangling counter, and produce different template definitions).

- `POP_SERVER_USECACHE` (_true_|_false_): Create and re-use a cache of the settings of the requested page.

- `POP_SERVER_COMPACTJSKEYS` (_true_|_false_): Common keys from the JSON code sent to the front-end are replaced with a compact string. Output response will be smaller.

- `POP_SERVER_USELOCALSTORAGE` (_true_|_false_): Save special loaded-in-the-background pages in localStorage, to not have to retrieve them again (until software version changes).

- `POP_SERVER_ENABLECONFIGBYPARAMS` (_true_|_false_): Enable to set the application configuration through URL param "config".

- `POP_SERVER_DISABLEJS` (_true_|_false_): Strip the output of all Javascript code.

- `POP_SERVER_USEGENERATETHEMEOUTPUTFILES` (_true_|_false_): Indicates that we are using all the output files produced from running `/generate-theme/` in this environment, namely:

    - resourceloader-bundle-mapping.json
    - resourceloader-generatedfiles.json
    - All `pop-memory/` files

- `POP_SERVER_SKIPLOADINGFRAMERESOURCES` (_true_|_false_): When generating file `resources.js`, with the list of resources to dynamically load on the client, do not include those resources initially loaded in the website (through "loading-frame").

### Decentralization: enabling crossdomain

To have a website consume data coming from other domains, crossdomain access must be allowed. For this, edit your .htaccess file like this:

    <IfModule mod_headers.c>
      SetEnvIf Origin "http(s)?://(.+\.)?(source-website.com|aggregator-website.com)$" AccessControlAllowOrigin=$0
      Header add Access-Control-Allow-Origin %{AccessControlAllowOrigin}e env=AccessControlAllowOrigin

      # Allow for cross-domain setting of cookies, so decentralized log-in also works
      Header set Access-Control-Allow-Credentials true
      Header add Access-Control-Allow-Methods GET
      Header add Access-Control-Allow-Methods POST
    </IfModule>

#### Important

For POST operations to work, we need to make sure the user's browser isn't blocking third-party cookies, otherwise [cross-origin credentialed requests will not work](https://stackoverflow.com/questions/24687313/what-exactly-does-the-access-control-allow-credentials-header-do#24689738). In Chrome, this configuration is set under Settings > Advanced Settings > Privacy > Content Settings > Block third-party cookies and site data.

### Integration between the Content CDN and Service Workers

To allow the website's service-worker.js be able to cache content coming from the content CDN, access to reading the ETag header must be granted:

    <IfModule mod_headers.c>
      Header add Access-Control-Allow-Headers ETag
      Header add Access-Control-Expose-Headers ETag
    </IfModule>

## Optimization

_**Important:** Similar to the installation process, there is room for improvement for the optimization process. If you would like to help us, please [check here](https://github.com/leoloso/PoP/issues/49)._

PoP allows to mangle, minify and bundle together all required .css, .js and .tmpl.js files (suitable for PROD environment), both at the plug-in and website levels:

- **At the plug-in level** (it generates 1.js + 1 .tmpl.js + 1.css files per plug-in): execute `bash -x plugins/PLUGIN-NAME/build/minify.sh` for each plugin
- **At the website level** (it generates 1.js + 1 .tmpl.js + 1.css files for the whole website): execute `bash -x themes/THEME-NAME/build/minify.sh` for the theme

Executing the `minify.sh` scripts requires the following software (_I'll welcome anyone proposing a better way to do this_):
 
1. [UglifyJS](https://github.com/mishoo/UglifyJS2)

 To minify (as to reduce the file size of) JS files

2. [UglifyCSS](https://github.com/fmarcia/UglifyCSS)

 To minify (as to reduce the file size of) CSS files

3. [Google's minimizer Min](https://github.com/mrclay/minify)

 To bundle and minify files. The min webserver must be deployed under http://min.localhost/.

The following environment variables are used in `minify.sh`: `POP_APP_PATH`, `POP_APP_MIN_PATH` and `POP_APP_MIN_FOLDER`. To set their values, for Mac, execute `sudo nano ~/.bash_profile`, then add and save:
    
      export POP_APP_PATH=path to your website (eg: "/Users/john/Sites/PoP")
      export POP_APP_MIN_PATH=path to Google's min website (eg: "/Users/john/Sites/min")
      export POP_APP_MIN_FOLDER=path to folder in min, used for copy files to minimize (eg: "PoP", with the folder being /Users/john/Sites/min/PoP/)

The `minify.sh` script copies all files to minimize under folder `POP_APP_MIN_FOLDER`, from where it minimizes them. The structure of this folder must be created in advance, as follows:
 
 for each theme:
  
      apps/THEME-NAME/css/
      apps/THEME-NAME/js/
      themes/THEME-NAME/css/
      themes/THEME-NAME/js/
     
 for each plug-in:
  
      plugins/PLUGIN-NAME/css/
      plugins/PLUGIN-NAME/js/

## Want to help?

We are looking for developers who want to become involved. Check here the issues we need your help with:

https://github.com/leoloso/PoP/issues?q=is%3Aissue+is%3Aopen+label%3A%22help+wanted%22

### Many thanks to BrowserStack!

Open to Open Source projects for free, PoP uses the Live, Web-Based Browser Testing provided by [BrowserStack](https://www.browserstack.com/).

![BrowserStack](http://www.softcrylic.com/wp-content/uploads/2017/03/browser-stack.png)

-->