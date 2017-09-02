{% raw %}
# Few words about Twig

BraDyCMS uses [Twig](http://twig.sensiolabs.org) as unique template engine
and you must refer to [the official documentation](http://twig.sensiolabs.org/doc/templates.html)
for basic and advanced Twig usage.

> HTML is a static markup language, but the content of your CMS is (hopefully) dynamic, and generated on the fly on each request.
> Think of the TWIG syntax as a way to put dynamic content inside your static HTML, styled with CSS.


The main thing you should know about Twig is that,
as [the official documentation](http://twig.sensiolabs.org/doc/templates.html) states,
there are two kinds of delimiters: `{% ... %}` and `{{ ... }}`.
The former one is used to execute statements such as for-loops, the latter prints the
result of an expression to the template.

The simplicity of BraDyCMS is in the fact that there is only one object behind the complexity
of all your content. It is called `html` and is fully documented [here](#docs/read/tmpl_html).

Essentially, you can think of the `html` object as a [namespace](http://en.wikipedia.org/wiki/Namespace).

This object has many attributes or methods that you can call easily from your template files,
and that are used to produce dynamic content. Some of these methods return simple strings,
others well-formatted HTML and others [PHP arrays](http://www.w3schools.com/php/php_arrays.asp)
or [PHP complex objects](http://php.net/manual/it/language.types.object.php)

Calling a method of the `html` object is as simple as writing `html.methodName`, e.g.:
    <!DOCTYPE html >
    <html>
    <head>
      {{ html.metadata }}
      ...

----

Twig can also be used to show different content depending on different conditions,
e.g. (using [if](http://twig.sensiolabs.org/doc/tags/if.html)):
    <div class="{% if html.getContext == 'home'%}white-backgroung{% endif %}">
    ...
    </div>
or (using [if and else](http://twig.sensiolabs.org/doc/tags/if.html))
    <div class="{% if html.getContext == 'home'%}white-backgroung{% else %}black-backgroung{% endif %}">
    ...
    </div>

----

Twig can also be used for looping in lists (array, or objects), e.g. (using [for](http://twig.sensiolabs.org/doc/tags/for.html))
    <div class="article-list">
      {% for article in html.getArticlesByTag('news') %}
        <div class="article-item">
          <h2>{{ article.title }}</h2>
          <div class="article-content">
            {{ article.text }}
          </div>
        </div>
      {% endfor %}
    </div>

----

Separate template files are included in the reference file by the Twig
[`include` statement](http://twig.sensiolabs.org/doc/tags/include.html), e.g.:
    <div class="content">
      {% if html.getContext == 'home' %}
        {% include 'homepage.twig %}
      {% else %}
        ...
      {% endif %}
    </div>

---

### Caching system

In production environment (the site configuration debug mode is turned off),
Twig will use a caching system to improve the speed of the entire system. The caching
system is automatically turned off in development environment (the site configuration
debug mode is turned on).

This means that you could find some problems in editing templates, applying and
viewing the changes. A typical example could be the following:
1. (Site is in debug mode)
2. The administrator edits one or more template files using, for example, the Template manager module
3. All changes are saved and success message is correctly shown
4. The administrator opens the web site to check the changes, but none of them
has been applied.

In fact all changes **have correctly been saved** but the live web site is still using
the old cache to display the template. You should **delete** the cache in order to
visualize your changes. The new cache will be automatically created on the first
visit of the live web site.

To delete the cache use the `Empty cache` button in the [Template manager module](#template/dashboard)
or use the `Empty cache` button at the bottom of the [Site configuration module](#cfg/edit).

{% endraw %}
