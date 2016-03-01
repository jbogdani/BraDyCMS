# Password protect sections of your site with passwords

BraDyCMS has a built-in (core) plugin to help you easily setup and manage one or more password protected sections of your web site.

You can easily add or edit authorized users to access specific parts of the sites and also define which parts of the site to set under protection.

There is also the possibility to permit users to register on the website and confirm their registration.

## Admins
### How can I control access some parts of my website?
Protecting one or more areas of a web site is a matter of:
1. defining which articles will not be visible to generic users (which articles am I going to protect?)
2. defining a list of users who, once authenticated, are allowed to view these articles.

#### Select the content to protect
Protecting an article form being viewed by anyone, by a special username and password authentication is as easy as adding one or more tags to an article. The first step to setup one or more password protected areas is to define one or more tags, each for each protected area.

First at all decide the tag or tags to use for each protected area and [add them in the sites tag list](#tags/manage).

After this tag [all articles](#article/all) you want to protect with one or more the defined tags. **Notice** that an article can belong to one, two or more protected areas. It depends on the tags you use.

#### Select the authenticated users who can access protected content

You can easily setup a list of users and specify for each of them what tags he can access after authentication.

The plugin is located in:

    Main menu > Articles > Password protected tags
Or use [direct link](#protectedtags/users).

For each user you should enter a valid email address, a (strong) password and select one ore more tags he can access after authentication. This list is his **whitelist** of tags.

The sum of the **whitelists** of all users constitutes the main tags **blacklist**, ie. tags that unauthenticated users can not access.

### How can I enable user registration?

If needed some content (tags) can be protected from generic unlogged users, while still available for registered users. If the **User registration is enabled** for one or more protected tags, then users can register on the website using an email address as username.
Here, two options are available: **Ask for email confirmation** and **Don't Ask for email confirmation**. If the first option (default option) is selected the system will send a verification code in the user's mailbox that can be used to complete the registration and access the protected pages. No need for admins is required. If the second options is selected users will automatically log in.

You can easily enable this feature by adding few information for each **tag** in the **Enable user's registration** section of the plugin:
- **Email address** will be used as the *FROM* address in the email message sent for the confirmation to the user. This recipient will receive a copy of the email message sent to the user.
- **Subject** will be used as the subject of the email messagge.
- **Text** will be used as the body of the messagge. **Do not forget** to use the placeholders `%name%`, `%password%`, `%email%` and `%code%`, that will be automatically replaced with the user's email address and the confirmation code.

---

If you have setup a list of tags, used them to tag article you want to protect, and defined a list of users who are allowed, once authenticated, to access these articles... **congrats, you just finished protecting one or more areas of your sites**.

---

## Designers
### How to setup templates to support password protected content

Designers can use three special methods of the [html object](#docs/read/tmpl_html) to easily setup one or more password protection for part or parts of the site content. These methods are:
* `html.canView`: returns `true` or `false` and tells you if the current content is protected or not (if content is in the **blacklist** and users is **notauthenticared**)
* `html.loginForm`: shows a well formatted html form to use for secure login
* `html.registertForm`: shows a well formatted html form to use for secure user registration and registration confirmation.
* `html.logoutButton`: if user is authenticated shows a button to use for logout

For a detailed description of these methods please refer to [their specific documentation](#docs/read/tmpl_html).

#### Example
    {# check if current content (tag blog or single article) is protected #}
    {% if html.canView == false %}

    {# protected content #}
    <div class="container>
      <div class="row">
        <div class="col-sm-4 col-sm-offset-4">
          {{ html.loginForm({
            'email_cont': 'form-group',
            'email_input': 'form-control',
            'password_cont': 'form-group',
            'password_input': 'form-control',
            'submit_input': 'btn btn-success'
            }) }}
        </div>
      </div>
    </div>
    {% else %}
      {# unprotected content #}
      {% if file_exists('sites/default/' ~ html.getContext ~ '.twig') %}
        {% include html.getContext ~ '.twig' %}
      {% else %}
        {% include 'not_found.twig' %}
      {% endif %}
    {% endif %}
