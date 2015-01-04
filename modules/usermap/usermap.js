/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2014 
 * @license			All rights reserved
 * @since				Mar 1, 2014
 */

var usermap = {
  
  init: function(){
    
    if($('.usermap').length > 0){
      var $this = this;
      $.each($('.usermap'), function(i, el){
        $this.buildMap(el);
      });
    }
  },
  
  
  buildMap: function(el){
    var cfgFile = $(el).data('cfg');
    
    var $this = this;
    
    this.loadLeaflet(function(){
      $.ajax({
        url: 'sites/default/modules/usermaps/' + cfgFile + '.map',
        dataType: 'json',
        success: function( data ) {
          $this.run(el, data);
        },
        error: function( data ) {
          $('el').html('Error in loading map configuration file. The map can not be build');
        }
      });
    });
  },
  
  run: function(el, cfg){
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
    
    if (cfg.zoomToBounds && cfg.zoomToBounds !== 'false'){
      map.fitBounds(bounds);
    }
    
  },
  
  loadLeaflet: function(callback){
    console.log(callback);
    if (typeof L === 'undefined'){
      $('head').append('<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />');
        $.getScript('http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js', function(){
          callback();
        });
    } else{
      callback();
    }
  }
};

$(document).ready(function(){
  usermap.init();
});

