(function($) {
  var globalName = 'FoodTruckGMap';

  var defaultMapOptions = {
      zoom: 13,
      panControl: false,
      zoomControl: true,
      mapTypeControl: false,
      scaleControl: false,
      streetViewControl: false,
      overviewMapControl: false,
      key: ''
  }

  var defaultStyle = [{
      "featureType": "poi.business",
      "stylers": [{
          "visibility": "off"
      }]
  }];

  var MapClass = function($dom, options) {

      var _this = this;

      this.$container = $dom && $dom.length ? $dom : $($dom);
      this.map = false;
      this.options = $.extend(defaultMapOptions, options || {});
      this.markers = [];

      // create map
      this.map = new window.google.maps.Map(_this.$container[0], _this.options);
      this.bounds = new google.maps.LatLngBounds();

      //suppress poi windows
      google.maps.InfoWindow.prototype.set = function() {};

      this.addMarker = function(latlng, icon, clickCallback) {

          if (!this.map) return false;

          var opts = {
              position: latlng, // obj {lat: , lng: }
              map: _this.map,
          }

          if (icon) opts.icon = icon;

          var marker = new google.maps.Marker(opts);

          this.markers.push(marker);

          this.bounds.extend(opts.position);
          if (this.markers.length > 1) {
              this.map.fitBounds(this.bounds);
          } else {
              this.map.setCenter(this.bounds.getCenter());
          }
          setTimeout(function() {
              if (this.map.getZoom() > this.options.zoom) this.map.setZoom(this.options.zoom);
          }.bind(this), 1000);

          clickCallback && marker.addListener('click', clickCallback);

          return this;

      }

      this.reset = function() {
          for (var i = 0; i < this.markers.length; i++) this.markers[i].setMap(null);
          this.markers = [];
          this.bounds = new google.maps.LatLngBounds();
      }

      this.setStyle = function(json) {
          this.map.set('styles', json);
          return this;
      }

      this.setStyle(window.FOODTRUCK_GMAP_STYLE || defaultStyle);

      return this;
  }

  var readyCallbacks = [];

  var googleMapsInitialising = false;

  var googleMapsInitialised = false;

  var onGoogleReady = function(callback) {

      if (googleMapsInitialised) {
          callback();
          return;
      }

      readyCallbacks.push(callback);

      if (googleMapsInitialising) return;
      googleMapsInitialising = true;

      var apiKey = window.FOODTRUCK_GMAP_APIKEY || 'AIzaSyAcB9Jwud7F5F_fO2BFHCIGswomX5pjKEQ';

      var s = document.createElement('script');
      s.async = true;
      s.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=' + globalName + '._googleMapsReady&key=' + apiKey;
      document.getElementsByTagName('script')[0].appendChild(s);
  }

  var exprt = {};

  exprt._googleMapsReady = function() {
      googleMapsInitialised = true;
      for (var i = 0; i < readyCallbacks.length; i++) readyCallbacks[i]();
      readyCallbacks = [];
  }

  exprt.ready = function(callback) {
      onGoogleReady(callback);
  }

  exprt.create = function($dom, options) {

      if (!googleMapsInitialised) return false;

      var map = new MapClass($dom, options);

      return map;
  }

  exprt.parseLocation = function(address, callback) {
      exprt.ready(function() {
          var geoclient = new google.maps.Geocoder();
          geoclient.geocode({
              'address': address
          }, function(results, status) {
              (status == google.maps.GeocoderStatus.OK) && callback && callback(results[0].geometry.location, results[0].formatted_address);
          });
      });
  }

  window[globalName] = exprt;

})(window.jQuery);



//=include includes/*
(function($) {

  if (!$) {
      console.error('jQuery needs to be loaded before the theme\'s Javscript');
      return false;
  }

  var exprt = {};
  // var $window = $(window);

  var renderLocationsModule = function() {
      var render = function() {
          var container = $(this);
          var detailContainer = container.find('.js-location-container');
          var detailBack = detailContainer.find('.js-location-back');
          var detailDetails = detailContainer.find('.js-location-details');
          var detailMap = detailContainer.find('.js-location-map');
          var listContainer = container.find('.js-location-list-container');
          var itemsDOM = container.find('.js-location-expand-container');
          var itemsMap = container.find('.js-locations-all-map');
          var items = [];
          var marker = null; // Feature: container.attr('data-marker') || null
          var detailMapGObj = false;
          var mainMapGObj = false;

          detailBack.on('click', function(e) {
              e.preventDefault();
              detailContainer.hide();
              listContainer.show();
              if (detailMapGObj) detailMapGObj.reset();
              positionPage();
              if (mainMapGObj) google.maps.event.trigger(mainMapGObj.map, "resize");
          });

          var positionPage = function() {
              window.scrollTo(0, container.offset().top - 150);
          }

          var loadItem = function(obj) {
              detailDetails.html(obj.html);
              detailContainer.show();
              listContainer.hide();

              if (obj._foundAddr)
                  detailDetails.append('<hr />' + obj._foundAddr + '<br /><a href="https://maps.google.com?saddr=Current+Location&daddr=' + obj._foundAddr + '" target="_blank">' + (window.FOODTRUCK_TXT_DIRECTIONS || 'Directions') + '</a>');

              positionPage();

              if (!detailMapGObj) return;

              google.maps.event.trigger(detailMapGObj.map, "resize");

              if (obj._latlng) {
                  detailMapGObj.addMarker(obj._latlng, marker);
              } else {
                  FoodTruckGMap.parseLocation(obj.address, function(googLatLng) {
                      detailMapGObj.addMarker(googLatLng, marker);
                  });
              }

          }

          var loadAll = function() {
              itemsDOM.each(function() {
                  var html = $(this).html();
                  var address = $(this).find('script[data-addr]').html();
                  var preGeoCode = {};
                  try {
                      preGeoCode = $.parseJSON($(this).find('script[data-geocode]').html()) || {};
                  } catch (error) {
                      preGeoCode = {};
                  }
                  var obj = {
                      address: address,
                      html: html,
                      _latlng: preGeoCode.lat ? {
                          lat: preGeoCode.lat,
                          lng: preGeoCode.lng
                      } : false,
                      _foundAddr: preGeoCode.formatted || false
                  };
                  items.push(obj);
                  $(this).on('click', loadItem.bind(null, obj))
              });

              if (!items.length) return;

              FoodTruckGMap.ready(function() {
                  var windowWidth = window.innerWidth || document.documentElement.clientWidth;
                  detailMapGObj = FoodTruckGMap.create(detailMap, {
                      zoom: 15,
                      scrollwheel: (windowWidth > 980)
                  });
                  mainMapGObj = FoodTruckGMap.create(itemsMap, {
                      scrollwheel: false
                  });
                  for (var i = 0; i < items.length; i++) {
                      if (items[i]._latlng) {
                          mainMapGObj.addMarker(items[i]._latlng, marker, loadItem.bind(null, items[i]));
                          continue;
                      }
                      items[i].address && (function(item) {
                          FoodTruckGMap.parseLocation(items[i].address, function(googLatLng, addr) {
                              item._latlng = googLatLng;
                              item._foundAddr = addr;
                              mainMapGObj.addMarker(googLatLng, marker, loadItem.bind(null, item));
                          });
                      })(items[i]);
                  };
              });
          }

          loadAll();

          itemsDOM.on('click', loadItem);
      };

      $('.js-location-module').each(function() {
          var container = $(this);
          if (container.data('_rendered')) return;
          container.data('_rendered', true);
          render.call(this);
      });

  }


  exprt.locationsMapReady = renderLocationsModule;

  window.FoodTruckFramework = exprt;
})(window.jQuery);


