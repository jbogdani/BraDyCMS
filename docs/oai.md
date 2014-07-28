# BraDyCMS' OAI-PMH interface

## Intro

BraDyCMS supports by default the Open Archives Initiative Protocol for Metadata Harvesting, **[OAI-PMH](http://www.openarchives.org/pmh)**

The BraDypUS implementation of this interface uses the `oaiprovider-php` freely available on [GitHub](https://github.com/martijnvogten/oaiprovider-php).

This interface can be reached postponing `/oai` to the main url of the website. If your website is available the the URL `http://my-host.ext` the URL of the OAI-PMH interface will be `http://my-host.ext/oai`.

All the configuration can be handled using a single [JSON](http://www.json.org) file named `config.json` and located in the `sites/default/modules/metadata` folder of the web directory where BraDyCMS is installed.

## Configuration

- `repositoryName`: (string) The name of the repository you are setting up
- `baseURL`: (string) The full URl where the interface is available
- `protocolVersion`: (string) The OAI-PMH version. The BraDyCMS supports the version 2.0
- `adminEmail`: (string) System adiministrator email address
- `sets`: (array) Array with information about available sets. A repository can contain one or more sets
 - `sets.spec`: (string) Set's ID
 - `sets.name`: (string) Set's name
- `table`: (object) Object that maps table fields to the repository object
 - `table.name`: (string) Full name of the table containing the record data
 - `table.id`: (string) The name of the table field containing the unique ID of the record to use in the DOI string
 - `table.lastchanged`: (string) The name of the table field containing the datetime of the last changes applied to the record
 -`table.deleted`: (string) The name of the table field containing the article's availability status as a boolean value
 - `table.category`: (string) The name of the table field containing the article's category. The values of this field **MUST** match the list of the <code>sets.spec</code> described above
 - `table.title`: (string) The name of the table field containing the article's title (DC.title)
 - `table.translated_title`: (string) The name of the table field containing the article's second or translated title. This parameter is optional
 - `table.creator`: (string) The name of the table field containing the article's creator (DC.creator)
 - `table.description`: (string) The name of the table field containing the article's description or abstract (DC.description)
- `publisher`: (string) Publisher's name
- `doi_prefix`: (string) Publisher's DOI prefix
- `journal_suffix`: (string) Journal's doi prefix
- `issn`: (string) Journals ISSN number

##Example of configuration file

File: `./sites/default/modules/oai/config.json`

    {
      "repositoryName"  : "E-Review. Rivista degli Istituti Storici dell'Emilia-Romagna in Rete",
      "baseURL"      : "http://e-review.it/OAI",
      "protocolVersion"  : "2.0",
      "adminEmail"    : "oai@e-review.it",
      "sets"        : [
        {
          "spec" : "dossier_2013",
          "name" : "Sezione principale, anno 2013"
        },
        {
          "spec" : "formazione",
          "name" : "Sezione dedicata alla formazione"
        },
        {
          "spec" : "patrimonio",
          "name" : "Sezione dedicata al patrimonio"
        },
        {
          "spec" : "usopubblico",
          "name" : "Sezione dedicata all'usopubblico"
        }
      ], 

      "table" :
      {
        "name" : "erevarticles",
        "id" : "doi",
        "lastchanged" : "updated",
        "deleted" : "status",
        "category" : "section",
        "title" : "title",
        "translated_title": "english_title",
        "creator" : "author",
        "description" : "summary"
      }, 
      "publisher": "BraDypUS",
      "doi_prefix": "10.12977",
      "journal_suffix": "erev",
      "issn": "ISSN:2282-4979"
    }
