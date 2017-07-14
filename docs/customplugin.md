# Authoring plugins

## What are custom plugins and how to use?
Plugins helps you in adding new functionalities to your web site. BraDyCMS permits you
to easily build a plugin and use it in your content or in template file by defining
a custom tag that can be further customized with parameters. Any PHP or JAVASCRIPT code
can be run inside a custom tags.

All user defined plugins will be saved in the `./sites/default/modules` directory.

Plugins are written in plain PHP and each plugin is defined as a single
[PHP object](http://php.net/manual/it/language.types.object.php). This object can
have all the **static** methods you need.

---

## Naming convention

### Plugin name
Custom plugins' **name** should not contain special characters or spaces.
Underscores and camel case can be used. The main php file containing the plugin's
code should be placed inside a directory inside the modules directory, with
the `inc` extension, e.g. `./sides/default/modules/myFirstPlugin/myFirstPlugin.inc`
is a valid module file name.
The unique name of the plugin (e.g.`myFirstPlugin`) should be used also for:
- the name of the main folder containing all plugin files (e.g. `sites/default/modules/myFirstPlugin`)
- the name of the main php file, having `.inc` extension, containing the main code
(e.g.: `./sites/default/modules/myFirstPlugin/myFirstPlugin.inc`)
- the name of the php class contained in the `./sites/default/modules/myFirstPlugin/myFirstPlugin.inc`
file, e.g.:
```php
class myFirstPlugin
{
	public static function something()
	{
		// Do somenthing
	}
}
```

### Special methods
The main class can have all the **static** methods you need, named following the
main php naming convention. But there are two **magic** methods that BraDyCMS will
call in two special occasions.

#### init($data)
The `init` method contains the php code that will be run when the plugin is called
in the article content or in template files, using the custom tag (see below).
The `init` method **must** be defined if you plan to use your plugin in template
files or in article's content.
This is the main public method and should be defined as a public static method, e.g.:

File: `./sites/default/modules/myFirstPlugin/myFirstPlugin.inc`:
```php
class myFirstPlugin
{

  public static function init($data)
  {
    ... do something
  }

  ...
}
```		
The `$data` parameter is an array containing all optional parameters. The plugin's
content is stored in `$data['content']`.

#### admin()
The `admin` method contains the php code that will be run when the plugin is called in the
administrator backend. If the `admin` method is defined, the plugin will be
automatically listed and made available in the main menu of the administrator backend.
If not defined it can still be used in template files or article content (`init`
method should be defined) but will not be visible in the administrator backend.
The `admin` method should be defined as a public static method, e.g.:

File: `./sites/default/modules/myFirstPlugin/myFirstPlugin.inc`:

```php
class myFirstPlugin{
  public static function admin()
  {
    // do something
  }
}
```

### Plugins' usage in template files and article contents
To use (call) a plugin inside an **article content** is very easy. Just use a custom tag,
having the same name of the plugin and add (optional) content and/or parameters, e.g.:

```html
[[myFirstPlugin]][[/myFirstPlugin]]
```
or
```html
[[myFirstPlugin param1="hello" param2="world"]]some content here; it can be
<em>rich</em> <big>html</big>[[/myFirstPlugin]]
```

To use (call) a plugin in **template files** use the `ct` method of the [html object](tmpl_html), e.g.:

```html
{{ html.ct('myFirstPlugin') }}
```
or

```html
{{ html.ct('myFirstPlugin', '{"content": "some content; it can be <em>rich</em> <big>html</big>","param1": "hello","param2":"world"}') }}
```

---

## Full example
The following example defines a very simple plugin that shows the [gravatar image](https://gravatar.com/)
for a certain email address. The plugin will be called `gravatar`.

##### Step 1: Create the folder and files
```bash
$ mkdir sites/default/modules/gravatar
$ touch sites/default/modules/gravatar/gravatar.inc
```

##### Step 2: Define the init method

File: `./sites/default/modules/gravatar/gravatar.inc`:
```php
<?php

class gravatar
{
  public static function init($data)
  {
    // Users email can be provided as custom tag's content or as a named parameter:
    if($data['content']) {

      $email = $data['content'];

    } else if($data['email']) {

      $email = $data['email'];
    } else {
      return false;
    }

    // an optional `size` parameter can be provided
    $size = $data['size'] ? $data['size'] : 40;

		//an optional `name` parameter can be used for alt attribute
		$alt = $data['name'] ? $data['name'] : $email;

    // define gravatar url
    $grav_url = "http://www.gravatar.com/avatar/" .
		 		md5( strtolower( trim( $email ) ) ) .
				"?s=" . $size;

    //return html image tag:
    return '<img src="' . $grav_url . '" alt="' . $alt . '" />';
  }
}
?>
```

##### Step 3: use the plugin in article content

Include the custom tag `gravatar` inside the article's content:

```html
[[gravatar]]info@bradypus.net[[/gravatar]]
```
or (same)
```html
[[gravatar email="info@bradypus.net"]][[/gravatar]]
```
or (optional size and name parameters provided)
```html
[[gravatar size="60" name="BraDypUS. Communicating Cultural Heritage"]]info@bradypus.net[[/gravatar]]
```

##### Step 3bis: use the plugin in templatefiles

```html
{{ html.ct('gravatar', '{"content":"info@bradypus.net"}') }}
```
or (same)
```html
{{ html.ct('gravatar', '{"email": "info@bradypus.net"}') }}
```
or (optional size and name parameters provided)
```html
{{ html.ct('gravatar', '{"email": "info@bradypus.net", name="BraDypUS. Communicating Cultural Heritage" "size":"60"}') }}
