# Customize database schema

The need to change the default database structure to add new fields (columns) for better structuring or describing your articles is very common.

BraDyCMS supports adding new fields using a very easy procedure, directly in the administrator backend. You must be site **administrator** to be able to follow this procedure.


----

#### Be careful! If you don't understand what this article is about, you probably shouldn't read further.

----

## Adding new fields

1. Open the [Site configuration](#cfg/edit) module (`Main menu > Site Configuration > Site Configuration`)
2. Enable the `Debug mode`
3. Scroll down to the `Custom fields` section and add information about the new field. For each field the following information must be supplied:
  * `Field name`, **required**. Please enter only lower case characters. No diacritic signs or special characters should be used.
  * `Field type`, **required**, deafult value: input. If `input` is chosen a single line text input will be displayed in the article edit form, `longtext` will display a multiline text input and `select` will display a drop down menu populated by values entered in the `Default field values`.
  * `Field label`, optional. You can enter here a meaningful text describing the field. This text will be used as label for the input in the article editing form.
  * `Default field values`, optional, **required** if `Field type` is `select`. Enter here the default value of the text inputs in the article editing form. If the `Field type` is `select`, enter here the values (that will appear in the drop down menu) separated by commas (,), e.g.: `opion 1,option2,option 3,etc`
  * `Translatable`, optional, default false. If set to `1` this field will be available for translation in the article translation module
  * `Rich text`, optional, default false. If set to `1` rich HTML can be entered using a WYSIWYG editor (in this field) in the article editing form.
4. After the first custom field data is entered, save the form and a second custom field can be entered, than save again and so on.
5. Once the the setting is complete create a new article (or edit an existing one). In the lower part of the form the new text inputs will be available. Enter some text in each field and save the article. This can be a draft one (the `Is published` can be set to `Draft`). Please **fill all custom fields** with some text, even dummy one. The text can be changed or erased after. Save all edits.
6. Return back to `Site configuration` module (see step 1) and disable the Debug mode (step 2).


## Deleting custom fields

Deleting all values of the custom fields to null, or deleting them in [Site configuration](#cfg/edit) module will disable the custom field. These fields will not available anymore in the article editing form. Please note that in this case the database structure **will not be affected**. The once defined columns will be still there, but will be not be used anymore by the CMS. If you want to permanently delete these fields from the database schema you should use an **external tool**.


## Troubleshooting

It is most important to enter some text in the custom fields in article editing mode, after setting them up, in **debug** mode (step 5 of the guide above). This will change the database schema. BraDyCMS uses (RedbeanPHP)[http://redbeanphp.com] as unique ORM; when the database is in debug mode Redbean is in [fluid state](http://redbeanphp.com/fluid_and_frozen). Any structural change applied in debug mode will be applied directly to the database.

If you have added some custom fields and you are not able to save edits of existing articles, or you cannot add new articles, probably the site configuration does not reflect the existing database schema. Please follow steps 1, 2, 4, 5 and 6 of the guide above.

Report any issue in [GitHub](https://github.com/jbogdani/BraDyCMS/issues)
