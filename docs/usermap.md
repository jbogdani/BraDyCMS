# Build and embed maps

BraDyCMS has a built-in (core) plugin that helps you to easily create and embed 
in articles bodies beautiful dynamic maps. You can use both [Leaflet.js](http://leafletjs.com) 
paired with [OpenStreetMap](http://osm.org) (**default option**) or [Google Maps](https://developers.google.com/maps/) 
to build your maps.

---

### Simple map

`Hands up`: Since version 3.9 you do not have to create a custom map to include a 
simple map with only one marker in your articles.

You can use the custom tag `map` with a few attributes, e.g.:

    [[map marker="44.51618,11.34238"]]BraDypUS[[/map]]
or
    [[map marker="44.51618,11.34238" zoom="7"]]BraDypUS[[/map]]

If you want to use Google Maps, instead of Leaflet, you should add the 
`platform` attribute:

    [[map platform="google" marker="44.51618,11.34238"]]BraDypUS[[/map]]
or
    [[map platform="google" marker="44.51618,11.34238" zoom="7"]]BraDypUS[[/map]]


The `marker` attribute is **required** and should contain the `x`,`y` coordinates of 
the single marker you want to show on your map, separated by comma (`,`).

The `zoom` attribute is **optional**, but **recommended** and should contain 
the initial zoom of the map. If not provided an the best zoom level 
will be automatically calculated.

The `content` of the tag will be used as text for the marker popup. Please note that
you **can use rich HTML** text here!

---

The plugin is located in: 

    Main menu > Plugins > User maps
Or use [direct link](#usermap/view).

---

### Create a new map

To create a new map click on the `Add a new user map` button and enter a map id.
**Please remember**, form ids must be unique. No spaces, dashes, hyphens or 
other special characters are allowed in the map id. Once the new map has been 
created you can customize it to meet your needs. See the `Custom map syntax` for
details.

---

### Embed a map in article's body

Embedding a map in an article's body is very simple. Just use the custom tag 
`[[map]]` with the name/id of your map.

For example, to embed the map named `whereweare` in an article's body just 
add this simple tag:

    [[map]]whereweare[[/map]]
You can aso provide fixed dimensions for your map using the `width` and `height`
attributes, eg.:a subject directly in the form definition, overwriting 
the custom one defined in the configuration file, e.g:

    [[map iwdth="500px" height="300px"]]whereweare[[/map]]
If these attributes are missing the default values of `width="100%"` and `height="400px"`
will be used.

> The Usermaps plugin requires the inclusion on the modules/usermap/usermap.js
javascript file in the page template, in the head section of the document 
or before the closing of the `body` tag:

    <script src="modules/usermap/usermap.js"></script>

> Usermaps module requires jQuery. Please be sure to include the jQuery library
before using the usermaps plugin.

---

### Custom map syntax
The configuration of a user map must follow a simple but rigid syntax. 
The configuration file must be a valid [json file](http://www.json.org/). 
BraDyCMS integrates a real-time validator to help finding any syntax error.


#### General data

- `platform`, string, optional, default: *leaflet*. Use *google* to use Google Maps
as mapping platform
- `scrollWheelZoom`, boolean, optional, default false. Enables or disables 
the scroll wheel zoom.
- `attribution`, string, optional, default false. A html string to display 
in the right low corner of the map
- `zoom`, int, optional, default false. Initial map zoom
- `center`, array, optional, default false. Initial geographical center of the 
map expressed as an array of latitude and longitude, e.g.: `[44.51618,11.34238]`
- `zoomToBounds`, boolean, optional, default false. If true the map will 
automatically zoom and center to show all markers.
- `markers`, array, required. Array of markers. See below for a in-depth marker description.
- `type`, string, optional, default *ROADMAP*. This option is available only with
Google Maps platform. Set this to one the following values (case insensitive):
  - *ROADMAP* (normal, default 2D map),
  - *SATELLITE* (photographic map),
  - *HYBRID* (photographic map + roads and city names),
  - *TERRAIN (map with mountains, rivers, etc.)*.

---

#### Markers definition

- `coord`, array, required. Marker's coordinates expressed as an array of 
latitude and longitude, e.g.: `[44.51618,11.34238]`
- `name`, string, required. Text or HTML to use on marker balloons.

---

### Example of a simple map, with only one marker

    {
      "scrollWheelZoom": "false",
      "attribution": <a href=\"http:\/\/bradypus.net\" title=\"BraDypUS. Communicating Cultural Heritage\">BraDypUS</a>",
      "zoomToBounds": true,
      "markers": [
        {
          "coord": [44.51618, 11.34238],
          "name": "<strong>BraDypUS</strong>. Via A. Fioravanti, 72.<br /> 40129 Bologna Italy"
        }
      ]
    }
