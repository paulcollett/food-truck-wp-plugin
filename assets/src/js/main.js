//=include includes/*
(function($){

  if(!$) {
      console.error('jQuery needs to be loaded before the theme\'s Javscript');
      return false;
  }

  var exprt = {};
  var $window = $(window);

  var renderLocationsModule = function() {
    var render = function() {
      var container = $(this);
      var detailContainer = container.find('.js-location-container');
      var detailBack = detailContainer.find('.js-location-back, .button[href=#]');
      var detailDetails = detailContainer.find('.js-location-details');
      var detailMap = detailContainer.find('.js-location-map');
      var listContainer = container.find('.js-location-list-container');
      var itemsDOM = container.find('.js-location-expand-container');
      var itemsMap = container.find('.js-locations-all-map');
      var items = [];
      var marker = null; // Feature: container.attr('data-marker') || null
      var detailMapGObj = false;
      var mainMapGObj = false;

      detailBack.on('click', function(e){
        e.preventDefault();
        detailContainer.hide();
        listContainer.show();
        if(detailMapGObj) detailMapGObj.reset();
        positionPage();
        if(mainMapGObj) google.maps.event.trigger(mainMapGObj.map, "resize");
      });

      var positionPage = function(){
        window.scrollTo(0, container.offset().top - 150);
      }

      var loadItem = function(obj) {
        detailDetails.html(obj.html);
        detailContainer.show();
        listContainer.hide();

        if(obj._foundAddr)
            detailDetails.append('<hr />' + obj._foundAddr + '<br /><a href="https://maps.google.com?saddr=Current+Location&daddr=' + obj._foundAddr + '" target="_blank">' + (window.FOODTRUCK_TXT_DIRECTIONS || 'Directions') + '</a>');

        positionPage();

        if(!detailMapGObj) return;

        google.maps.event.trigger(detailMapGObj.map, "resize");

        if(obj._latlng) {
          detailMapGObj.addMarker(obj._latlng,marker);
        } else {
          FoodTruckGMap.parseLocation(obj.address,function(googLatLng){
            detailMapGObj.addMarker(googLatLng,marker);
          });
        }

      }

      var loadAll = function() {
        itemsDOM.each(function(){
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
            _latlng: preGeoCode.lat ? { lat: preGeoCode.lat, lng: preGeoCode.lng } : false,
            _foundAddr: preGeoCode.formatted || false
          };
          items.push(obj);
          $(this).on('click',loadItem.bind(null, obj))
        });

        if(!items.length) return;

        FoodTruckGMap.ready(function(){
            var windowWidth = window.innerWidth || document.documentElement.clientWidth;
            detailMapGObj = FoodTruckGMap.create(detailMap, {
              zoom: 15,
              scrollwheel: (windowWidth > 980)
            });
            mainMapGObj = FoodTruckGMap.create(itemsMap, {
              scrollwheel: false
            });
            for (var i = 0; i < items.length; i++) {
                if(items[i]._latlng) {
                  mainMapGObj.addMarker(items[i]._latlng,marker,loadItem.bind(null, items[i]));
                  continue;
                }
                items[i].address && (function(item){
                    FoodTruckGMap.parseLocation(items[i].address,function(googLatLng, addr){
                        item._latlng = googLatLng;
                        item._foundAddr = addr;
                        mainMapGObj.addMarker(googLatLng,marker,loadItem.bind(null, item));
                    });
                })(items[i]);
            };
        });
      }

      loadAll();

      itemsDOM.on('click', loadItem);
    };

    $('.js-location-module').each(function(){
      var container = $(this);
      if(container.data('_rendered')) return;
      container.data('_rendered', true);
      render.call(this);
    });

  }


  exprt.locationsMapReady = renderLocationsModule;

  window.FoodTruckFramework = exprt;
})(window.jQuery);
