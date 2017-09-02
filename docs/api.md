# BraDyCMS API documentantion

<p class="bg-danger text-danger">This functionality is still under development and may change in the future. Please read carefully the following doc, that will be carefully updated to reflect in real time all changes.</p>

BraDyCMS integrates, from v. 3.4, an [RESTful](http://en.wikipedia.org/wiki/Representational_state_transfer) API which allows users to easily retrieve data from articles, menus, etc. in [JSON format](http://json.org), to be reused in many different ways (eg., to build an mobile app).

Up to present (v. 3.6) the API can be used only for retrieving data (read-only), but soon it will be possible to use this interface also for editing and erasing data.

## Base URL

The API can be reached by adding `/api/` to your base URL, eg. the API for the domain `http://bradypus.net` can be accessed at `http://bradypus.net/api/`.

## Parameters

<table class="table table-bordered table-striped">
	<tr>
		<th>Parameter</th>
		<th>Type</th>
		<th>Required/optional</th>
		<th>Description</th>
	</tr>

	<tr>
		<th>action</th>
		<td>strin</td>
		<td>required</td>
    <td>Action API should handle. Actually only <code>read</code></td>
	</tr>

	<tr>
		<th>menu</th>
		<td>mixed</td>
		<td>optional</td>
		<td>The menu name or array of menu names to return</td>
	</tr>

	<tr>
		<th>tag</th>
		<td>mixed</td>
		<td>optional</td>
		<td>The tag or array of tags to use as filter for getting articles.</td>
	</tr>

	<tr>
		<th>metadata</th>
		<td>boolean</td>
		<td>optional</td>
		<td>If true all site metadata will be returned.</td>
	</tr>

	<tr>
		<th>artid</th>
		<td>mixed</td>
		<td>optional</td>
		<td>Article textid or array of articles textid used as filter for getting articles.</td>
	</tr>

</table>


## Examples
<select class="url">
  <option value="http://bradypus.net/api/?action=read&menu=main">Menu: main</option>
  <option value="http://bradypus.net/api/?action=read&tag[]=gallerie&tag[]=mostre">Tags: gallerie & mostre</option>
  <option value="http://bradypus.net/api/?action=read&metadata=true">Metadata</option>
  <option value="http://bradypus.net/api/?action=read&artid=contatti">Artid: contatti</option>
  <option value="http://bradypus.net/api/?action=read&menu=main&tag[]=gallerie&tag[]=mostre&metadata=true&artid=contatti">Menu & Artid & Metadata & Artid</option>
</select>
<button class="btn btn-default go">Get data</button>
<code class="proccessedURL"></code>
<pre class="pre prettyprint lang-js"></pre>

<script>
  $('.tab-pane.active button.go').on('click', function(){

    var url = $('.tab-pane.active select.url').val();

    $('.tab-pane.active code.proccessedURL').text(url);

    $('.tab-pane.active pre.pre').text('Please wait.... getting data from ' + url);

    $.get(url, function(data){

      $('.tab-pane.active pre.pre').text(data);

      window.prettyPrint && prettyPrint();

    }, 'html');

  });
</script>
