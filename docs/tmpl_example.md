#Template example

File: `./sites/default/index.twig`

    <!DOCTYPE html >
    <html>
    <head>
  
      <!-- Print metadata -->
      {{ html.metadata }}
  
      <!-- Load site favicon -->
      <link rel="shortcut icon" href="./sites/default/css/favicon.ico">
  
      <!-- Load site styles -->
      <link rel="stylesheet" href="./sites/default/css/styles.css" type="text/css" />
    </head>

    <body>
  
      <!-- HEADER -->
      <div class="header">
        <div class="container">
          <h1>My new Web Site</h1>
          <p class="lead">This is my new web site build with BraDyCMS!</p>
        </div>
      </div> <!-- end of header-->
    
      <!-- MAIN MENU -->
      <div class="container">
        <div class="navbar navbar-default">
          <div class="container">
            {{ html.menu('main', 'nav navbar-nav') }}
          </div>
        </div>
      </div><!-- end of main menu -->
    
    
      <!-- MAIN BODY -->
      <div class="body container">
    
        <!-- Start ofonditional displaying of content, depending on context (html.getContext) -->
        <!-- 1. Context is HOME -->
        {% if html.getContext == 'home' %}
          <div class="row">
            <!-- Show in a big column the body of the welcome article -->
            <div class="col-md-9">
              <h1>{{ html.getArticle('welcome').title }}</h1>

              <div class="body">
                {{ html.getArticle('welcome').text }}
              </div>
            </div><!-- /.end of left, big column -->

            <!-- Show in a side column a list of articles tagged as news -->
            <div class="col-md-3">
              {{ html.tagBlog('news') }}
            </div> <!-- /.end of small, right column -->

          </div><!-- /. end of home section -->

        
        <!-- 2. Context is ARTICLE -->
        {% elseif html.getContext == 'article' %}
          <div class="row">
            <div class="col-md-9">
              <!-- Show formatted article in main column -->
              {{ html.articleBody }}
            </div>
        
            <div class="col-md-3">
              <div class="well">
                <!-- Show list of similar articles in left column -->
                {{ html.similarBlog }}
              </div>
          </div><!--/. end of article section -->
        
        <!-- 3. Context is TAGS -->
        {% elseif html.getContext == 'tags' %}
          {{ html.tagBlog }}
      
        <!-- 4. Context is SEARCH -->
        {% elseif html.getContext == 'search' %}
          {{ html.searchResults }}

        {% endif %} <!--/. end of conditional contents displaying -->

      </div> <!--/. end of main body container (.body .container) -->
  
  
      <!-- FOOTER -->
      <div class="footer">
        <div class="container">
          <p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
          {{ html.menu('foot', 'inline') }}
          <p>Powered by <a href="http://bradypus.net" target="_blank">BraDypUS <small>COMMUNICATING CULTURAL HERITAGE</small></a></p>
        </div>
      </div><!-- Footer end -->

      <!-- Load jQuery -->
      {{ html.jQuery('1.10.2') }}
  
      <!-- Search form submission script -->
      <script>
        (function(){
          $('#searchForm').submit(function(){
            if($('#search').val() != '' ){
              window.location.href = $(this).data('path') + './search:' + $('#search').val();
            }
          });
      })();
      </script>
    </body>
    </html>