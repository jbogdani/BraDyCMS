# Build and embed forms in articles

BraDyCMS has a built-in (core) plugin to help you deal with web-form and collect 
data from your users easily.
You can create all the web forms you need and embed them in article bodies. 
You can easily customize all fields and add file uploading buttons, just editing
the main configuration file.

The plugin is located in: 

    Main menu > Plugins > User forms
Or use [direct link](#userform/view).

---

### Create a new custom form

To create a new custom form click the `Add new user form` button and enter a form id.
**Please remember**, form ids must be unique. No spaces, dashes, hyphens or 
other special characters are allowed in the form id. Once the new form has been 
created you can customize it to meet your needs. See the `Custom form syntax` for
details.

---

### Embed a form in article's body

Embedding a form in an article's body is very simple. Just use the custom tag 
`[[userform]]` with the name/id of your form.

For example, to embed the form named `contactus` in an article's body just 
add this simple tag:

    [[userform]]contactus[[/userform]]
You can aso provide a subject directly in the form definition, overwriting 
the custom one defined in the configuration file, e.g:

    [[userform subject="Contact us"]]contactus[[/userform]]

> BraDyCMS will do the rest for you: form **formatting**, **validation** and 
text and attachments delivery.

---

### Custom form syntax
The configuration of a user form must follow a simple but rigid syntax. 
The configuration file must be a valid [json file](http://www.json.org/). 
BraDyCMS integrates a real-time validator to help finding any syntax error.
		


#### General data

- `to`, string, required. A valid email address to use to send the form data to.
- `from_email`, string, required. A valid email address. This address will be 
shown in the email header as the sender of the automatic email
- `from_name`, string, optional, default none. A person name or other. This name 
will be  shown in the email header as the sender of the automatic email, together
with the `from_email` address
- `subject`, string, required. Email message subject. This setting can be 
overwritten in the article content of template file where the form is rendered.
- `success_text`, string, required. Message to display to users filling the 
form when the email message is successfully sent
- `error_text`, string, required. Message to display when email to users filling 
the form when the email message is NOT successfully sent
- `inline`, boolean, optional, default: false. If this value is present and is true input 
labels will be displayed inline, on the right of the text inputs, otherwise 
(default value) the label will be displayed above the field.


Partial example
    {
      "to": "info@bradypus.com",
      "from_email": "info@bradypus.com",
      "from_name": "BraDypUS communicating cultural heritage",
      "subject":"Contact form",
      "success_text":"Your message was successfully sent",
      "error_text": "Sorry, something went wrong and it was not possible to send your message"
      "inline": true,
      ...

---

#### Form element's data

Form elements should be defined as an array. There is not a limit to the number of form elements that can be added to a form.

Each element should have a unique name and one of the predefined types.

<table class="table table-bordered table-striped">
	<tr>
		<th>Parameter</th>
		<th>Type</th>
		<th>Required/optional</th>
		<th>Description</th>
	</tr>

	<tr>
		<th>name</th>
		<td>string</td>
		<td>required</td>
		<td>Unique name. No whitespaces or special characters should be used. This is not visible to users and is used only for internal reference</td>
	</tr>
	
	<tr>
		<th>label</th>
		<td>string</td>
		<td>optional</td>
		<td>This label will be visible to users.</td>
	</tr>
	
	<tr>
		<th>placeholder</th>
		<td>string</td>
		<td>optional</td>
		<td>Placehoder visible to users.</td>
	</tr>

  <tr>
		<th>type</th>
		<td>string</td>
		<td>required</td>
		<td>The field type to display to users. One of the following can be used:<br />
			<ul>
				<li>text: a simple, one line, input field will be shown. This is the default value</li>
				<li>longtext: a simple multiline input field will be shown</li>
				<li>select: a drop down list vill be shown. For select to work properly the <code>options</code> parameter must be provided.</li>
				<li>upload: this will show a button that can be used to upload files. For upload to work properly the <code>sizeLimit</code> and <code>allowedExtensions</code> can be provided</li>
			</ul>
		</td>
	</tr>
	
	<tr>
		<th>options</th>
		<td>array</td>
		<td>required if type is select</td>
		<td>array of values to use as predefined options for drop down list</td>
	</tr>
	
	<tr>
		<th>is_required</th>
		<td>boolean</td>
		<td>optional</td>
		<td>if true or if defined the field value can not be empty, users can not submit the form and warnig message will be shown.</td>
	</tr>
	
	<tr>
		<th>is_email</th>
		<td>boolean</td>
		<td>optional</td>
		<td>if true or if defined the field value will be checked to match a valid email pattern. in case of errors users can not submit the form and warnig message will be shown.</td>
	</tr>
	
	<tr>
		<th>sizeLimit</th>
		<td>integer</td>
		<td>optional</td>
		<td>Size limit for files to upload in bytes. This option is available only if field type is <code>upload</code>. If user tries to upload a bigger file a warning message is shown and the file is not uploaded</td>
	</tr>
	
	<tr>
		<th>allowedExtensions</th>
		<td>array</td>
		<td>optional</td>
		<td>Array with allowed file extensions. This option is available only if field type is <code>upload</code>. If user tries to upload a file with a different extension a warnin message is shown and the file is not uploaded.</td>
	</tr>

</table>

----

## Full example of a simple contact form


    {
      "to": "info@bradypus.com",
      "from_email": "info@bradypus.com",
      "from_name": "BraDypUS communicating cultural heritage",
      "subject":"Contact form",
      "success_text":"Your message was successfully sent",
      "error_text": "Sorry, something went wrong and it was not possible to send your message"
      "inline": true,
      "elements": [
        {
          "name": "name",
          "label": "Name",
          "placehoder": "Name",
          "type": "text",
          "is_required": "true"
        },
        {
          "name": "email",
          "label": "Email address",
          "placeholder": "Email address",
          "type": "text",
          "is_required": "true",
          "is_email": "true"
        },
        {
          "name": "phone_no",
          "label": "Phone number",
          "placeholder": "Phone number",
          "type": "text"
        },
        {
          "name": "location",
          "label": "Location",
          "type": "text"
        },
        {
          "name": "how_did_you_hear_about_us",
          "label": "How did you hear about us?",
          "placehoder": "How did you hear about us?",
          "type": "select",
          "options": [
            "google",
            "email message",
            "friends"
          ]
        },
        {
          "name": "comments",
          "label": "Comments",
          "placeholder": "Comments",
          "type": "longtext"
        },
        {
          "name": "uploadcv",
          "label": "Upload your CV",
          "type": "upload",
          "sizeLimit": "2097152",
          "allowedExtensions": [
            "pdf",
            "doc",
            "docx",
            "odt"
          ]
        }
      ]
    }