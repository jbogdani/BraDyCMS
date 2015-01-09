/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2014 
 * @license			All rights reserved
 * @since				Mar 1, 2014
 */

(function(window){
  
  loadLib = function(platform, callback){
    
    var jsPath, cssPath, checkVar;
    if (!platform || platform == 'google'){
      $.getScript('https://www.google.com/jsapi', function(){
        google.load('maps', '3', { other_params: 'sensor=false', callback:  callback });
      });
      return;
      jsPath = 'https://maps.googleapis.com/maps/api/js';
      checkVar = 'google';
    } else if (platform == 'leaflet'){
      jsPath = 'http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js';
      cssPath = 'http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css';
      checkVar = 'L';
    } else {
      return false;
    }
    
    
    
    if(typeof window[checkVar] == 'undefined'){
      if(typeof cssPath != 'undefined'){
        $('head').append('<link rel="stylesheet" href="' + cssPath + '" />');
      }
      $.getScript(jsPath, function(){
        callback();
      });
    }
  };
  
  var usermap = {

    init: function(){
      if($('.usermap').length > 0){
        var $this = this;
        
        $.each($('.usermap'), function(i, el){
          $this.buildMap(el);
        });
      }
    },


    buildMap: function(el,platform){
      var cfgFile = $(el).data('cfg');
      var platform = $(el).data('platform') || 'leaflet';
      
      $this = this;
      
      // simple map from markup
      if ($(el).data('marker')){
        loadLib(platform, function(){
          var coordinates = $(el).data('marker').split(',');
          var data = {
            zoom: parseInt($(el).data('zoom')),
            scrollWheelZoom: $(el).data('scrollWheelZoom'),
            center: [ parseFloat(coordinates[0]), parseFloat(coordinates[1]) ],
            markers: [
              {
                coord: [ parseFloat(coordinates[0]), parseFloat(coordinates[1]) ],
                name: $(el).data('cfg')
              }
            ]
          };
          
          switch(platform){
            case 'leaflet':
              $this.runLeaflet(el, data);
              break;
            case 'google':
              $this.runGoogleMaps(el, data);
              break;
            default:
              return false;
              break;
          }
          return;
        });
      }
      
      // map from configuration
      $.ajax({
        url: 'sites/default/modules/usermaps/' + cfgFile + '.map',
        dataType: 'json',
        success: function( data ) {
          data.platform ? platform = data.platform : '';
          
          loadLib(platform, function(){
            switch(platform){
              case 'leaflet':
                $this.runLeaflet(el, data);
                break;
              case 'google':
                $this.runGoogleMaps(el, data, window.google);
                break;
              default:
                return false;
                break;
            }
          });
        },
        error: function( data ) {
          $('el').html('Error in loading map configuration file. The map can not be build');
        }
      });
      
    },
    
    runGoogleMaps: function(el, cfg){
      if (!cfg.markers){
        console.log('No marker is defined in the map configuration file');
        return false;
      }
      var mapOptions = {};
      cfg.zoom            ? mapOptions.zoom = cfg.zoom : '';
      cfg.center          ? mapOptions.center = new google.maps.LatLng(cfg.center[0], cfg.center[1]) : '';
      mapOptions.scrollwheel = (cfg.scrollWheelZoom && cfg.scrollWheelZoom != 'false') ? true : false;
      
      (cfg.type && ['SATELLITE', 'TERRAIN', 'HYBRID', 'ROADMAP'].indexOf(cfg.type.toUpperCase()) > -1) ? mapOptions.mapTypeId = google.maps.MapTypeId[cfg.type.toUpperCase()] : ''

      var map = new google.maps.Map($(el)[0], mapOptions);
      var bounds = new google.maps.LatLngBounds();
      var infowindow = new google.maps.InfoWindow({
        maxWidth: 150
      });
  
      $.each(cfg.markers, function(i, marker){
        var latLng = new google.maps.LatLng(marker.coord[0], marker.coord[1]);
        bounds.extend(latLng);
        var gmarker = new google.maps.Marker({
          position: latLng,
          map: map,
          title: marker.name
        });
        google.maps.event.addListener(gmarker, 'click', function() {
          infowindow.setContent(marker.name);
          infowindow.open(map, gmarker);
        });
      });
      if (!cfg.zoom || (cfg.zoomToBounds && cfg.zoomToBounds !== 'false')){
        map.fitBounds(bounds);
      }
      
    },
    
    runLeaflet: function(el, cfg){
      /**
       * cfg
       *  scrollWheelZoom: boolean
       *  zoom
       *  center
       *  zoomToBounds
       *  markers
       *    coord
       *    name
       */
      if (!cfg.markers){
        console.log('No marker is defined in the map configuration file');
        return false;
      }
      var map = L.map($(el).attr('id'), {
        scrollWheelZoom: (cfg.scrollWheelZoom && cfg.scrollWheelZoom !== 'false')
      });

      if (cfg.zoom && cfg.center && cfg.zoom !== 'false' && cfg.center !== 'false'){
        map.setView(cfg.center, cfg.zoom);
      }

      L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: cfg.attribution
      }).addTo(map);

      var bounds = [];
      $.each(cfg.markers, function(i, marker){
        bounds.push(marker.coord);
        L.marker(marker.coord).addTo(map)
          .bindPopup(marker.name);
      });

      if (!cfg.zoom || cfg.zoomToBounds && cfg.zoomToBounds !== 'false'){
        map.fitBounds(bounds);
      }
    }
  };

  window.usermap = usermap;

})(window);

$(document).ready(function(){
  usermap.init();
});

