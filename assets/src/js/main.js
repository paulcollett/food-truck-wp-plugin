//=include includes/*
(function($){

  if(!$) {
      console.error('jQuery needs to be loaded before the theme\'s Javscript');
      return false;
  }

  var exprt = {};
  var $body;
  var $header;
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
      var marker = container.attr('data-marker') || null;
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
        window.scrollTo(0, container.offset().top - $header.height() - 150);
      }

      var loadItem = function(obj) {
        detailContainer.show();
        listContainer.hide();
        detailDetails.html(obj.html);
        if(obj._foundAddr) detailDetails.append('<hr />' + obj._foundAddr + '<br /><a href="https://maps.google.com?saddr=Current+Location&daddr=' + obj._foundAddr + '" target="_blank">View Directions</a>');
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
          var address = $(this).find('script').html();
          var obj = {
              address: address,
              html: html,
              _latlng: false,
              _foundAddr: false
          };
          items.push(obj);
          $(this).on('click',loadItem.bind(null, obj))
        });

        if(!items.length) return;

        var gMapKey = window.FoodTruckGMapKey || 'AIzaSyAcB9Jwud7F5F_fO2BFHCIGswomX5pjKEQ';

        FoodTruckGMap.ready(function(){
            var windowWidth = window.innerWidth || document.documentElement.clientWidth;
            detailMapGObj = FoodTruckGMap.create(detailMap, {
              zoom: 15,
              scrollwheel: (windowWidth > 980),
              key: gMapKey
            });
            mainMapGObj = FoodTruckGMap.create(itemsMap, {
              scrollwheel: false,
              key: gMapKey
            });
            for (var i = 0; i < items.length; i++) {
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
