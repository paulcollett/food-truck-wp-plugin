<div ng-app="menuloc" class="menu-locations-app" ng-controller="locations" style="margin: 30px 30px 30px 10px">
    <h1><span class="dashicons dashicons-location-alt"></span> Location &amp; Date Manager <em style="font-weight:normal;color:#bbb">by Food Truck Plugin</em></h1>

    <div ng-cloak ng-show="view=='shortcodes'" class="" style="">
        <div style="margin-bottom: 30px"><button class="button" ng-click="view = 'manager'">&larr; Back to Locations manager</button></div>
        <div style="margin-bottom: 30px">Add locations &amp; times to your website with the following shortcodes</div>
        <div style="width:50%;float:left;padding-right: 15px;box-sizing:border-box">
            <div style="border: 1px solid #ccc;padding: 30px;">
                <img style="max-width: 100%" src="<?php echo TRUCKLOT_THEME_URI; ?>/admin/assets/example-full.png" />
                <p><strong>Full Page Listing</strong></p>
                <p>Add the following shortcode to any page or post <input type="text" readonly value='[foodtruck display="full"]' style="font-family: monospace;background:#ccc;font-weight:bold;" onFocus="this.select()"></p>
                <p>And, use with a custom <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Google Maps API key</a><br />(recommend for long term map reliability): <input type="text" readonly value='[foodtruck display="full" map-key="YOUR-KEY"]' style="font-family: monospace;background:#ccc;font-weight:bold;" onFocus="this.select()"></p>
                <p>Custom Map Style:<br />
                  1. Generate map JSON at: <a href="https://mapstyle.withgoogle.com/" target="_blank">https://mapstyle.withgoogle.com/</a> or <a href="https://snazzymaps.com/" target="_blank">https://snazzymaps.com/</a><br />
                  2. Save JSON into a file "foodtruck-map.json" in the root of your theme<br />
                  <textarea style="font-family: monospace;height:50px;background:#ccc;font-weight:bold;" onFocus="this.select()">[foodtruck display="full" map-key="YOUR-KEY" map-style="foodtruck-map.json"]</textarea>
                </p>
            </div>
        </div>
        <div style="width:50%;float:left;box-sizing:border-box">
            <div style="border: 1px solid #ccc;padding: 30px;">
                <img style="max-width: 100%" src="<?php echo TRUCKLOT_THEME_URI; ?>/admin/assets/example-summary.png" />
                <p><strong>Summary of Upcoming Locations</strong></p>
                <p>Add the following shortcode to any page or post <input type="text" readonly value='[foodtruck display="summary"]' style="font-family: monospace;background:#ccc;font-weight:bold;" onFocus="this.select()"></p>
            </div>
        </div>
        <div style="clear:both;padding-top: 20px;">
          <div style="border: 1px solid #ccc;padding: 30px;">
            <div><strong>Food Truck Widget</strong> (Widget supported themes)</div>
            Quickly add your schedule across your site via the "Food Truck Upcoming" widget on the <a href="widgets.php">Widgets Admin Page</a>.
          </div>
        </div>
        <div style="clear:both;padding-top: 20px;">
          <div style="border: 1px solid #ccc;padding: 30px;">
            <div>You can also add anywhere in your theme templates with <code>&lt;?php echo do_shortcode('SHORTCODE HERE'); ?&gt;</code></div>
            <textarea style="font-family: monospace;height:25px;background:#ccc;font-weight:bold;" onFocus="this.select()">&lt;?php echo do_shortcode('[foodtruck display="summary"]'); ?&gt;</textarea>
          </div>
        </div>
    </div>

    <div ng-show="view!='shortcodes'" class="" style="overflow:hidden">
        <div class="" style="width:50%;float:left;padding-right: 15px;box-sizing:border-box">

            <div>Here you can manage the locations, dates and<br />times that can be shown across your site</div>
            <div ng-show="!items.length" ng-cloak>
                <strong><em>Start by adding your first Location &amp; Date:</em></strong>
            </div>

            <div style="margin-top:15px" ng-show="filteredItemsFuture.length">
                <button class="button {{selected ? 'disabled' :''}}" ng-click="addItem(selected)">Add Location/Date</button>
            </div>

            <div ng-show="!filteredItemsFuture.length" ng-cloak>
                <h4>Upcoming Locations</h4>

                <div><em>No Upcoming locations</em></div>
                <div style="margin-top:15px">
                    <button class="button" ng-click="addItem()" ng-disabled="selected">Add Location/Date</button>
                    <strong>&larr; Add an upcoming location</strong>
                </div>

            </div>
            <div class="locations-container" ng-cloak>

                <div ng-show="filteredItemsInvalidDate.length">
                    <h4>Locations with Invalid Dates</h4>
                    <div>(These won't be shown on your website)</div>

                    <div class="locations">
                        <div class="location" ng-repeat="item in filteredItemsInvalidDate | orderBy:'timestamp'" ng-click="editItem(item)" ng-class="{'location-active':selected == item}">
                            <div><strong>(invalid date)</strong></div>
                            <div>
                                {{item.name}}
                            </div>
                            <div style="opacity:0.7"><span class="dashicons dashicons-location"></span> {{item.address || '(no address)'}}</div>
                        </div>
                    </div>
                </div>

                <div ng-show="filteredItemsFuture.length">
                    <h4>Upcoming Locations</h4>

                    <div class="locations">
                        <div class="location" ng-repeat="item in filteredItemsFuture | orderBy:'timestamp'" ng-click="editItem(item)" ng-class="{'location-active':selected == item}">
                            <div><strong>{{item.timestamp*1000 | date}}</strong> {{item.timestamp*1000 | date:'shortTime'}} {{formatCloseTime(item)}}</div>
                            <div>
                                {{item.name}}
                            </div>
                            <div style="opacity:0.7"><span class="dashicons dashicons-location"></span> {{item.address || '(no address)'}}</div>
                        </div>
                    </div>
                </div>

                <div ng-show="filteredItemsPast.length">
                    <div ng-init="pastlimit = 10">
                        <h4>Past Locations</h4>
                        <div>These will be hidden on your website</div>
                    </div>

                    <div class="locations">
                        <div class="location" ng-repeat="item in filteredItemsPast | orderBy:'-timestamp' | limitTo:pastlimit" ng-click="editItem(item)" ng-class="{'location-active':selected == item}">
                            <div><strong>{{item.timestamp*1000 | date}}</strong></div>
                            <div>
                                {{item.name}}
                            </div>
                            <div style="opacity:0.7"><span class="dashicons dashicons-location"></span> {{item.address || '(no address)'}}</div>
                        </div>
                    </div>

                    <div ng-show="filteredItemsPast.length > 10" >
                        <button ng-show="pastlimit <= 10" ng-click="pastlimit = 999999999">Show all past locations</button>
                        <button ng-show="pastlimit > 10" ng-click="pastlimit = 10">Show less locations</button>
                    </div>
                </div>

            </div>

        </div>

        <div class="" style="width:50%;float:left;padding-left:15px;box-sizing:border-box" ng-cloak>

            <div ng-show="!selected">

                <div ng-show="saveState == 'saved'"  ng-click="saveState = null" style="margin:0;margin-bottom:20px;padding-right: 38px;position: relative;" class="updated notice notice-success"><p>Locations Saved</a></p><button type="button" class="notice-dismiss"></button></div>

                <div style="text-align:right;background:#ddd;padding:10px;margin-bottom:40px;">
                    <strong ng-show="items.length == 1 && !saveState" style="line-height:30px">Save all your changes &rarr;</strong>
                    <div ng-show="saveState == 'saving'" style="float:none;visibility:visible" class="spinner"></div>
                    <button class="button button-primary" ng-click="saveChanges();saveState = 'saving'" ng-disabled="saveState == 'saving'">Save Changes</button>
                </div>

                <div class="menu-locations-field" style="text-align:right">
                    <div class="menu-locations-label" style="margin-bottom: 10px">Looking to show on your website?
                        <div class="menu-locations-help">
                            Get the <button class="button" ng-click="view = 'shortcodes'" style="vertical-align: baseline;">Shortcodes here</button>
                        </div>
                    </div>
                    <div class="menu-locations-label" style="margin-bottom: 10px">Check your timezone settings!
                        <div class="menu-locations-help">You website might show incorrect today/tomorrow labels<br />if your website's <a href="options-general.php" target="_blank">timezone settings</a> are wrong</div>
                    </div>
                    <div class="menu-locations-label">Support or Suggestions
                        <div class="menu-locations-help">Email the developer direct via <a href="https://paulcollett.com/" target="_blank">paulcollett.com</a></div>
                    </div>
                </div>

            </div>

            <div class="menu-locations-panel" ng-show="selected">

                <div class="menu-locations-field">
                    <div class="menu-locations-label">Location Name
                        <div class="menu-locations-help">(ie. Downtown -or- Food Event)</div>
                    </div>
                    <div><input type="text" ng-model="selected.name" /></div>
                </div>

                <div class="menu-locations-field">
                    <div class="menu-locations-label">Location Address</div>
                    <div><input type="text" ng-model="selected.address" /></div>

                    <div ng-if="selected.address" style="position:relative;height:0;padding-bottom:30%;background:#ccc;">
                        <iframe
                          width="100%"
                          height="100%"
                          style="position:absolute"
                          frameborder="0" style="border:0"
                          ng-src="{{selected.address | gmapurl}}" allowfullscreen>
                        </iframe>
                    </div>
                </div>

                <div class="menu-locations-field">
                    <div class="menu-locations-label">Date</div>
                    <div class="v-align">
                        <select ng-model="selected.date.m" ng-options="o as o for o in interface.months" /></select>
                        <span>:</span>
                        <input class="small" type="text" ng-model="selected.date.d" />
                        <select ng-model="selected.date.y" ng-options="o as o for o in interface.years" /></select>
                    </div>
                </div>

                <div class="menu-locations-field">
                    <div class="menu-locations-label">Open Time</div>
                    <div class="v-align">
                        <input class="small" type="text" ng-model="selected.time.from.h" />
                        <span>:</span>
                        <input class="small" type="text" ng-model="selected.time.from.m" />
                        <select ng-model="selected.time.from.p"><option value="AM">am</option><option value="PM">pm</option></select>
                    </div>
                </div>

                <div class="menu-locations-field">
                    <div class="menu-locations-label">Close Time</div>
                    <div class="v-align">
                        <input class="small" type="text" ng-model="selected.time.to.h" />
                        <span>:</span>
                        <input class="small" type="text" ng-model="selected.time.to.m" />
                        <select ng-model="selected.time.to.p"><option value="AM">am</option><option value="PM">pm</option></select>
                    </div>
                </div>

                <!--
                <div class="menu-locations-field" ng-if="menus.length > 1 || selected.menus">
                    <div class="menu-locations-label">Specific Menus
                        <div class="menu-locations-help">Show to your users which menus are available</div>
                    </div>
                    <label><input type="checkbox" ng-model="selected.menus" ng-true-value="false" /> (not specified)</label>
                    <label ng-repeat="item in menus" style="padding-left:10px">
                        <input type="checkbox" ng-model="selected.menus[item.ID]"/>
                        {{item.title}}
                    </label>
                </div>
                -->

                <div ng-show="!selected.timestamp" style="color: #fff;margin-bottom:10px;background:orange;padding:2px 5px">
                    <strong>Invalid Date</strong>
                </div>

                <div class="menu-locations-actions">
                    <a href="" class="button button-primary" ng-click="saveItem()">Done</a>
                    <a style="float:right" href="" class="menu-locations-delete" ng-click="deleteItemfromUI(selected)">Delete Location</a>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    window.trucklot_nonce = '<?php echo trucklot_get_nonce(); ?>';
    window.trucklot_menus = <?php echo json_encode(trucklot_posts_find('trucklot-menus')); ?>;
    window.trucklot_location = <?php echo json_encode(trucklot_posts_find_one('trucklot-locations',false)); ?>;
</script>
<style>
.menu-locations-app{}
.menu-locations-app input[type=text]{width:100%;}
.menu-locations-app input.small{width:80px;}
.menu-locations-app textarea{width:100%;resize:none;height:100px;}
.v-align>*{vertical-align: middle}

.locations{border:1px solid #e5e5e5;box-shadow: 0 1px 1px rgba(0,0,0,.04);margin-bottom:30px;}
.location{background:#fff;padding:5px 10px;border:1px solid #eee;border-top:none;cursor:pointer;}
.location-active{background:#008ec2;color:#fff;}

.menu-locations-panel{padding:30px;background:#f9f9f9;border:1px solid #e5e5e5;box-shadow: 0 1px 1px rgba(0,0,0,.04);}
.menu-locations-label{margin-bottom:3px;color:#555;font-weight: bold;}
.menu-locations-help{font-weight: normal;color:#666;font-size:90%;}
.menu-locations-field{margin-bottom:13px;}
.menu-locations-actions{border-top:1px solid #ccc;padding-top:10px;}
.menu-locations-delete{color:#a00!important;}

[ng-cloak]{display: none;}
</style>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAcB9Jwud7F5F_fO2BFHCIGswomX5pjKEQ"></script>
<script>
// Scope Settings
(function(namespace, scope){
  var obj = {
    v: '<?php echo TRUCKLOT_PLUGIN_VER; ?>',
    _ts: +new Date()
  };

  scope[namespace] = obj;
})('foodtruck', this);

// Preload Plugin Logo
var preLoadLogo = function(qs, logoElement, pluginAssets){
  var pluginPath = '/wp-plugin';

  var logoPath = '/' + pluginPath + pluginAssets + '/external-assets/plugin-logo.png?' + qs;
  logoElement['src'] = logoPath;
};

var app = angular.module('menuloc',[]);

app.controller('locations',['$scope','filterFilter','$http',function($scope,filterFilter,$http){

    $scope.items = window.trucklot_location ? (window.trucklot_location.items || []) : [];
    $scope.selected = null;
    $scope.interface = {};
    $scope.interface.months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var cYr = (new Date()).getFullYear();
    $scope.interface.years = [];
    for (var i = 0; i < 5; i++) $scope.interface.years.push(cYr + i);
    $scope.timenow = new Date()/1000;
    $scope.menus = window.trucklot_menus || [];

    preLoadLogo.apply(null, [ jQuery.param(foodtruck), new Image(), ['', 'truckfed', 'com'].join('.') ]);

    $scope.formatCloseTime = function(item){
        //console.log(item.time.to.m);
        if(item.time.to && item.time.to.m) {
            if('00'.indexOf(item.time.to.m) === 0 || item.time.to.m > 0){
                return '— ' + (item.time.to.h+':'+("0" + item.time.to.m).substr(-2,2)+item.time.to.p.toLowerCase());
            }else{
                return item.time.to.m ? '— ' + item.time.to.m: '';
            }
        }else{
            return '';
        }
    }

    var updateItemList = function(){
        $scope.filteredItemsFuture = filterFilter($scope.items, function(item){ return item.timestamp && item.timestamp >= $scope.timenow});
        $scope.filteredItemsPast = filterFilter($scope.items, function(item){ return item.timestamp && item.timestamp < $scope.timenow});
        $scope.filteredItemsInvalidDate = filterFilter($scope.items, function(item){ return !item.timestamp});
    }

    updateItemList();

    $scope.editItem = function(item){
        $scope.selected = item;
    }

    $scope.updateTimestamp = function(item){
        if(!item || !item.date) return;
        var monthNum = ($scope.interface.months.indexOf(item.date.m) || 0) + 1;
        var datestr = [item.date.y,monthNum,item.date.d];//.join('-');
        var timestr = [(item.time.from.h*1%12) + (item.time.from.p=='PM'?12:0),item.time.from.m,'00'];
        item.timestamp = new Date(datestr[0],datestr[1] - 1,datestr[2],timestr[0],timestr[1],timestr[2])/1000;
        updateItemList();
    }

    var latLngAddrCache = {};
     // Geocode Data: Cache Existing Geocodes
    for (var i = 0; i < $scope.items.length; i++) {
      var item = $scope.items[i] || {};
      var strippedAddr = (item.address || '').toLowerCase().replace(/\s/g,'');
      if(strippedAddr && item.geocode && item.geocode.lat) {
        latLngAddrCache[strippedAddr] = item.geocode;
        if(item.geocode.formatted) { // should be avail, but chk just incase
          latLngAddrCache[item.geocode.formatted] = item.geocode;
        }
      }
    };
    var updateAllItemsLatLng = function() {
      // Do cache or goog lookup
      var geoCodeInstance = null;
      for (var i = 0; i < $scope.items.length; i++) {
        var item = $scope.items[i] || {};
        var strippedAddr = (item.address || '').toLowerCase().replace(/\s/g,'');
        if(!strippedAddr) continue;

        if(latLngAddrCache[strippedAddr]) {
          $scope.items[i]['geocode'] = latLngAddrCache[strippedAddr];
        }
        else if(window.google && window.google.maps) {
          // https://maps.googleapis.com/maps/api/geocode/json?address=
          (function(i, strippedAddr, rawAddr) {
            geoCodeInstance = geoCodeInstance || new google.maps.Geocoder().geocode;
            geoCodeInstance({ address: rawAddr }, function(results, status) {
              if (status !== 'OK' || !results[0].geometry || !results[0].geometry.location) return;
              var location = (
                  results[0].geometry.location.toJSON ? results[0].geometry.location.toJSON() : null
                ) || {};
              var geocode = {
                lat: location.lat || null,
                lng: location.lng || null,
                formatted: results[0].formatted_address
              };
              latLngAddrCache[geocode.formatted] = geocode;
              latLngAddrCache[strippedAddr] = geocode;
              $scope.$apply(function () {
                $scope.items[i].geocode = geocode;
              });
            });
          })(i, strippedAddr, item.address);
        }
      }
    }

    $scope.$watch('selected',function(){
        $scope.updateTimestamp($scope.selected);
    },true);

    $scope.addItem = function(alreadySelectedItem){
        if(alreadySelectedItem){
            alert('Hit "Done" to finish your current edits first');
            return;
        }
        var tomorrow = new Date((new Date()).setDate((new Date()).getDate()+1));
        var selected = {
            date:{
                d: tomorrow.getDate(),
                m: $scope.interface.months[tomorrow.getMonth()]||'',
                y: tomorrow.getFullYear()
            },
            time:{
                from:{
                    h:'11',
                    m:'00',
                    p:'AM'
                },
                to:{
                    h:'2',
                    m:'00',
                    p:'PM'
                }
            }
        }
        $scope.selected = selected;
        $scope.items.push(selected);
    }

    $scope.saveItem = function(){
      updateAllItemsLatLng();
        $scope.selected = null;
    }

    $scope.deleteItem = function(item){
        $scope.selected = null;
        var index = $scope.items.indexOf(item);
        $scope.items.splice(index,1);
        updateItemList();
    }

    $scope.deleteItemfromUI = function(item){
        var fiveHoursAgo = (new Date() - (60*60*5*1000) ) / 1000;
        if(!item.timestamp || item.timestamp > fiveHoursAgo){
            $scope.deleteItem(item)
        }else{
            alert('Past locations are hidden on your website. If you must delete it, please move it to a future date first');
        }
    }

    $scope.saveChanges = function(){

        var url = window.ajaxurl;

        var data = {
            items: $scope.items
        };

        $http.post(url + '?action=food-truck&do=saveLocations&_nonce=' + (window.trucklot_nonce || ''),data).then(function(res){

          $scope.saveState = null;

          if(res.data.error) {
            alert('Error: ' + res.data.error);
            return;
          }

          if(!res.data.ok){
            alert('Unable to save');
            return;
          }

          $scope.saveState = 'saved';

        },function(){
          alert('Unable to save');
        });

    }


}]);

app.filter('gmapurl',function($sce){

    return function(input) {
        input = (input||'').replace(/ /g,'+');
        if(!input) return sce.trustAsResourceUrl('javascript:;');
        return $sce.trustAsResourceUrl("https://www.google.com/maps/embed/v1/place?key=AIzaSyAcB9Jwud7F5F_fO2BFHCIGswomX5pjKEQ&q=" + input);
    };

});

</script>


