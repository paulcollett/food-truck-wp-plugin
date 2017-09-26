// GMap Class
// paulcollett.com
(function($){
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
		"stylers": [{"visibility": "off"}]
	}];

	var MapClass = function($dom,options){

		var _this = this;

		this.$container = $dom && $dom.length ? $dom : $($dom);
		this.map = false;
		this.options = $.extend(defaultMapOptions,options||{});
		this.markers = [];

		// create map
		this.map = new window.google.maps.Map(_this.$container[0],_this.options);
		this.bounds = new google.maps.LatLngBounds();

		//suppress poi windows
		google.maps.InfoWindow.prototype.set = function(){};

		this.addMarker = function(latlng,icon, clickCallback){

			if(!this.map) return false;

			var opts = {
				position: latlng.length ? new google.maps.LatLng(latlng[0],latlng[1]) : latlng,
				map: _this.map,
			}

			if(icon) opts.icon = icon;

			var marker = new google.maps.Marker(opts);
			this.markers.push(marker);

			this.bounds.extend(opts.position);
			if(this.markers.length > 1) {
				this.map.fitBounds(this.bounds);
			} else {
				this.map.setCenter(this.bounds.getCenter());
			}
			setTimeout(function(){
				if(this.map.getZoom() > this.options.zoom) this.map.setZoom(this.options.zoom);
			}.bind(this), 1000);

			clickCallback && marker.addListener('click', clickCallback);

			return this;

		}

		this.reset = function(){
			for (var i = 0; i < this.markers.length; i++) this.markers[i].setMap(null);
			this.markers = [];
			this.bounds = new google.maps.LatLngBounds();
		}

		this.setStyle = function(json){
			this.map.set('styles',json);
			return this;
		}

		this.setStyle(defaultStyle);

		return this;
	}

	var readyCallbacks = [];

	var googleMapsInitialising = false;

	var googleMapsInitialised = false;

	var onGoogleReady = function(callback){

		if(googleMapsInitialised){
			callback();
			return;
		}

		readyCallbacks.push(callback);

		if(googleMapsInitialising) return;
    googleMapsInitialising = true;

    var apiKey = 'AIzaSyAcB9Jwud7F5F_fO2BFHCIGswomX5pjKEQ';

		var s = document.createElement('script');
		s.async = true;
		s.src = 'http://maps.googleapis.com/maps/api/js?v=3.exp&callback=' + globalName + '._googleMapsReady&key=' + apiKey;
		document.getElementsByTagName('script')[0].appendChild(s);
	}

	var exprt = {};

	exprt._googleMapsReady = function(){
		googleMapsInitialised = true;
		for (var i = 0; i < readyCallbacks.length; i++) readyCallbacks[i]();
		readyCallbacks = [];
	}

	exprt.ready = function(callback){
		onGoogleReady(callback);
	}

	exprt.create = function($dom,options){

		if(!googleMapsInitialised) return false;

		var map = new MapClass($dom,options);

		return map;
	}

	exprt.parseLocation = function(address,callback){
		exprt.ready(function(){
			var geoclient = new google.maps.Geocoder();
			geoclient.geocode({'address': address}, function(results, status){
				(status == google.maps.GeocoderStatus.OK) && callback && callback(results[0].geometry.location, results[0].formatted_address);
			});
		});
	}

	window[globalName] = exprt;

})(jQuery);
/*
(function($){

	var $maps = $('div[data-map]');

	if($maps.length){
		var cname = 'map-gm-container';
		$maps.addClass(cname);
		$('head').append('<style>.' + cname+ '{background:rgba(0,0,0,0.05)} .' + cname+ ' img{max-width:none!important}.'
			+ cname + ' a{display:none!important}.gm-style-cc{height:0!important}</style>');
	}

	$maps.each(function(){

		var $this = $(this);

		GMap.ready(function(){
			var map = GMap.create($this);
			var marker = $this.attr('data-marker');
			var locationString = $this.data('map');
			GMap.parseLocation(locationString,function(googLatLng){
				map.addMarker(googLatLng,marker);
			});
		});

	});

})(jQuery);*/
