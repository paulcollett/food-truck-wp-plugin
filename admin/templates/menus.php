<div ng-app="menuloc" class="menu-locations-app" ng-controller="menus" style="margin: 30px 30px 30px 10px">
    <h1 style="margin-bottom:20px"><span class="dashicons dashicons-format-aside"></span> Menu Manager <em style="font-weight:normal;color:#bbb">by Food Truck Theme</em></h1>


    <div class="nav-tab-wrapper">
        <div ng-show="menus.length" style="margin-bottom:5px;float:left">
            <span>Editing:</span>
            <select ng-model="menu_id" ng-cloak>
                <option value="">-- Manage Menus --</option>
                <option value="{{item.ID}}" ng-repeat="item in menus">{{item.title}} ({{(item.items||[]).length}} Item{{(item.items||[]).length !== 1 ? 's' : ''}})</option>
            </select>
        </div>
        <div ng-show="menu_id" ng-init="page = page||'menu'" ng-cloak>
            <a href="" class="nav-tab" ng-class="{'nav-tab-active':page == 'menu'}" ng-click="page='menu'">
                Edit Menu
            </a>
            <a href="" class="nav-tab" ng-class="{'nav-tab-active':page == 'display'}" ng-click="page='display'">
                Display Options
            </a>
        </div>
    </div>

    <div ng-show="!menu_id" ng-init="$$view = 'list'" ng-cloak>

        <div ng-show="$$view == 'list'">

            <p style="margin-top:30px">You can have multiple menus/sections (ie. Lunch, Dinner, Winter Menu, Weddings) which can be shown together or separately across your website</p>

            <div class="menus-list" style="margin:30px 0;" ng-show="menus.length">
                <div ng-repeat="item in menus" style="background:#fff;border-top:1px solid #e5e5e5;padding:20px;">
                    <a class="menu-locations-delete" href="" ng-click="deleteMenu(item)" style="float:right;padding-top:3px">Delete Menu</a>
                    <strong ng-show="item.status=='hidden'" style="color:#a00">(Hidden on website) </strong>
                    <strong><a href="" ng-click="selectMenu(item)" style="font-size:17px;text-decoration:none">{{item.title}}</a></strong>
                    ({{(item.items||[]).length}} Item{{(item.items||[]).length !== 1 ? 's' : ''}})

                    <div>
                        <a href="" ng-click="selectMenu(item)">Edit Menu &amp; Items</a>
                        <strong ng-show="!item.items.length">&larr; Add your first item now!</strong>
                        <span ng-show="!item.items.length" style="color:#a00"> (Menu will be hidden without items)</span>
                    </div>

                </div>
            </div>

            <div style="margin:30px 0">
                <a class="button" style="vertical-align:middle" ng-click="$$view = 'add';new_menu={ID:'new'}">+ New Menu/Section</a>
                <span ng-show="!menus.length">
                    <strong>&larr; add your first menu/section to get started</strong>
                </span>
            </div>

        </div>
        <div ng-show="$$view == 'add'">
            <div class="menu-locations-field" style="padding-top:30px">
                <div class="menu-locations-label">
                    New Menu/Section Name
                    <div class="menu-locations-help" ng-show="!item.items">
                        Only have one menu? Try breaking it up into sections or simply call it "Menu" for starters. Later on you can create different menus for each occasion
                    </div>
                </div>
                <div style=""><input ng-model="new_menu.title" type="text" /></div>
            </div>
            <div class="menu-locations-actions">
                <a class="button button-primary" style="vertical-align:middle" ng-click="addMenu(new_menu);$$view='list'">Add New Menu</a>
                <a ng-show="menus.length" class="button" style="vertical-align:middle" ng-click="$$view='list'">Cancel</a>
            </div>

        </div>
    </div>

    <div ng-show="menu_id" ng-cloak>
        <div ng-show="page == 'menu'">

            <div class="" style="width:50%;float:left;padding-right: 15px;box-sizing:border-box">
                <div style="margin: 30px 0;" ng-show="!menu.items.length">
                    <em>No Menu Items</em>
                </div>
                <div ng-show="menu.items.length >= 10" style="margin-bottom: -20px;padding-top:30px">
                    <a class="button" href="" ng-click="newMenuItem(true)">+ Menu Item</a>
                </div>
                <div ng-show="menu.items.length" class="menu" style="margin:30px 0;border:1px solid #eee;border-top:none">
                    <div ng-click="editMenuItem(item)" class="menu-item" ng-class="{'menu-item-active':item==menu_item}" ng-repeat="item in menu.items">
                        <div><button ng-disabled="$first" ng-click="move(menu.items, $index, $index - 1)">&uarr;</button></div>
                        <div><button ng-disabled="$last" ng-click="move(menu.items, $index, $index + 1)">&darr;</button></div>
                        <div>
                            <div style="display:inline-block;width:50px;height:50px;background:#ccc">
                                <img ng-if="item.image.thumbnail" ng-src="{{item.image.thumbnail}}" width="50" />
                            </div>
                        </div>
                        <div class="menu-item-title">
                            <strong>{{item.name || '(no name)'}}</strong>
                            <div>{{item.desc}}</div>
                        </div>
                        <div ng-click="editMenuItem(item)">{{item.price}}</div>
                    </div>
                </div>
                <a class="button" href="" ng-click="newMenuItem()">+ Menu Item</a>
                <span ng-show="!menu.items.length">
                    <strong>&larr; add your first item</strong>
                </span>
            </div>
            <div style="width:50%;float:left;padding: 15px;box-sizing:border-box">

                <div ng-show="!menu_item">

                    <div ng-show="saveState == 'saved'"  ng-click="saveState = null" style="margin:0;margin-bottom:20px;padding-right: 38px;position: relative;" class="updated notice notice-success"><p>Menu &amp; Items Saved</a></p><button type="button" class="notice-dismiss"></button></div>

                    <div style="text-align:right;background:#ddd;padding:10px;margin-bottom:5px;">
                        <div ng-show="saveState == 'saving'" style="float:none;visibility:visible" class="spinner"></div>
                        <button class="button button-primary" ng-click="saveMenu(menu);saveState = 'saving'" ng-disabled="saveState == 'saving'">Save Menu Changes</button>
                    </div>

                    <div class="menu-locations-field" ng-show="menu.items.length > 2" style="text-align:right;padding-top:40px">
                        <strong>Show this menu on a page?</strong>
                        <div>Add the shortcode <code>[truckmenu id="{{menu.ID}}"]</code> to any page or post</div>
                    </div>

                </div>

                <div ng-show="menu_item" class="menu-locations-panel">
                    <div class="menu-locations-field">
                        <div class="menu-locations-label">Item Name</div>
                        <input type="text" ng-model="menu_item.name" />
                    </div>

                    <div class="menu-locations-field">
                        <div class="menu-locations-label">Item Description</div>
                        <textarea ng-model="menu_item.desc"></textarea>
                    </div>

                    <div class="menu-locations-field">
                        <div class="menu-locations-label">Display Price</div>
                        <small>(include currency symbols if required)</small>
                        <input type="text" ng-model="menu_item.price" />
                    </div>

                    <div class="menu-locations-field">
                        <div class="menu-locations-label">Item Photo</div>
                        <div cm-file-selector data-file="menu_item.image"></div>
                    </div>

                    <div class="menu-locations-actions">
                        <div>
                            <a class="button button-primary" href="" ng-click="doneMenuItem()">Done</a>
                            <!-- <a class="button" href="" ng-click="resetMenuItem(item)">Cancel</a> -->
                        </div>
                        <a href="" ng-click="removeItem(item)" style="float:right;" class="menu-locations-delete">Delete Item</a>
                    </div>
                </div>

            </div>

        </div>

        <div ng-show="page == 'display'" style="padding-top:30px">

            <!--
            <div class="menu-locations-field" ng-init="menu.status = null">
                <div class="menu-locations-label">Visible on Website</div>
                <label><input type="radio" ng-model="menu.status" /> Visible</label>
                <label style="padding-left:15px"><input type="radio" ng-model="menu.status" value="hidden" /> Hidden</label>
            </div>
            -->
            <div class="menu-locations-field">
                <div class="menu-locations-label">Title (shown in admin panel)</div>
                <input type="text" ng-model="menu.title" />
            </div>

            <div class="menu-locations-field">
                <label><input type="checkbox" ng-model="menu.hide_title" /> Hide Menu Title</label>
            </div>

            <div class="menu-locations-field" ng-hide="menu.hide_title">
                <div class="menu-locations-label">Display Title
                    <div class="menu-locations-help">Leave blank to show main title</div>
                </div>
                <input type="text" ng-model="menu.display_title" />
            </div>

            <div class="menu-locations-field">
                <div class="menu-locations-label">Text to show after title and before menu items</div>
                <input type="text" ng-model="menu.text_after_title" />
            </div>

            <div class="menu-locations-field">
                <div class="menu-locations-label">Text to show after menu items</div>
                <input type="text" ng-model="menu.text_after_menu" />
            </div>

            <div class="menu-locations-field" ng-show="menu.items.length > 2">
                <div class="menu-locations-label">Add the Menu to a Page:
                    <div class="menu-locations-help">Add the shortcode <code>[truckmenu id="{{menu.ID}}"]</code> to any page or post</div>
                </div>
            </div>

            <div class="menu-locations-actions">
                <a class="button button-primary" href="" ng-click="saveMenu(menu);saveState = 'saving';menu_item=null;page = 'menu'">Save Menu Changes</a>
            </div>

        </div>
    </div>

</div>

<script>
    window.trucklot_nonce = '<?php echo trucklot_get_nonce(); ?>';
    window.trucklot_menus = <?php echo json_encode(trucklot_posts_find('trucklot-menus')); ?>;
</script>

<style>

.menu-locations-app{}
.menu-locations-app input[type=text]{width:100%;}
.menu-locations-app input.small{width:80px;}
.menu-locations-app textarea{width:100%;resize:none;height:100px;}
.v-align>*{vertical-align: middle}

.locations{border:1px solid #e5e5e5;box-shadow: 0 1px 1px rgba(0,0,0,.04);margin-bottom:30px;}
.location{background:#fff;padding:5px 10px;border:1px solid #eee;border-top:none;}
.location-active{background:#008ec2;color:#fff;}

.menu-locations-panel{padding:30px;background:#f9f9f9;border:1px solid #e5e5e5;box-shadow: 0 1px 1px rgba(0,0,0,.04);}
.menu-locations-label{margin-bottom:3px;color:#555;font-weight: bold;}
.menu-locations-help{font-weight: normal;color:#666;font-size:90%;}
.menu-locations-field{margin-bottom:13px;}
.menu-locations-actions{border-top:1px solid #ccc;padding-top:10px;}
.menu-locations-delete{color:#a00!important;}

.menus-list{border-bottom:1px solid #e5e5e5;box-shadow: 0 1px 1px rgba(0,0,0,.04);}
.menu-item{cursor:pointer;background:#fff;border-top:1px solid #e5e5e5;display:flex;align-items:center;cursor:pointer;padding-right:5px;}
.menu-item>*{display:inline-block;vertical-align: middle;padding:2px;}
.menu-item .menu-item-title{flex-grow:1;}
.menu-item-active{background:lightblue;}

[ng-cloak]{display: none;}
</style>
<script type="text/html" id="trucklot-file">
<div>
      <ul ng-if="multiple">
        <li ng-if="f.id" ng-repeat="f in file">
          {{f.filename}}
          <a href="#" ng-click="remove(f,file)">Remove</a>
        </li>
      </ul>
      <div ng-if="file.id && !multiple">

        <div ng-if="file.type == 'image'">
          <img ng-src="{{file.thumbnail_medium}}" style="max-width:300px;max-height:170px" />
        </div>
        <div ng-if="file.type != 'image'">
          <img ng-src="{{file.icon}}">
        </div>
        <div> {{file.filename}}</div>

        <div ng-if="file.type == 'image'">
            Original Size: {{file.width}} &times; {{file.height}}px
        </div>

        <div ng-if="file.type != 'image'">
            {{file.filesize}}
        </div>
        <div>
          <a href="#" ng-click="removeFile()">Remove</a>
        </div>
    </div>
    <div ng-if="!file.id">
      <button class="button" ng-click="browse()">Select Image</button>
    </div>
</div>
</script>
<script>
var app = angular.module('menuloc',[]);

app.controller('menus',['$scope','$http',function($scope,$http){

    $scope.menus = window.trucklot_menus || [];
    $scope.menu_id = null;
    $scope.menu = null;
    $scope.menu_item_original = null;
    $scope.menu_item = null;

    //Goto edit view if only one menu exists
    /*if($scope.menus.length == 1){
        $scope.menu_id = $scope.menus[0].ID;
        $scope.menu = $scope.menus[0];
    }*/

    $scope.selectMenu = function(menu){
        $scope.menu_id = menu.ID || 'new';
        $scope.menu = menu;
        $scope.menu_item = null;
        $scope.saveState = null;
    }

    $scope.newMenuItem = function(addtotop){
        if(!$scope.menu) return;
        $scope.menu.items = $scope.menu.items||[];
        var item = {};
        if(addtotop){
            $scope.menu.items.unshift(item);
        }else{
            $scope.menu.items.push(item);
        }

        $scope.editMenuItem(item);
    }

    $scope.editMenuItem = function(item){

        if($scope.menu_item == item){
            return;
        }

        if($scope.menu_item && angular.equals($scope.menu_item,{})){
            $scope.removeItem($scope.menu_item);
        }

        $scope.menu_item = item;
        $scope.menu_item_original = angular.copy(item);
    }

    $scope.resetMenuItem = function(item){

        if(angular.equals($scope.menu_item_original,{})){
            $scope.removeItem(item);
            return;
        }

        item = angular.copy($scope.menu_item_original, $scope.menu_item);

        $scope.doneMenuItem();
    }

    $scope.removeItem = function(item){

        var index = $scope.menu.items.indexOf(item);
        $scope.menu.items.splice(index, 1);

        $scope.doneMenuItem();
    }

    $scope.removeMenu = function(menu){

        var index = $scope.menus.indexOf(menu);
        $scope.menus.splice(index, 1);
    }

    $scope.doneMenuItem = function(item){

        $scope.menu_item = null;
    }

    $scope.addMenu = function(menu){
        if(!menu.title){
            alert('You\'ll need to add a title to your menu');
            return;
        }

        menu.items = menu.items || [];

        $scope.menus.push(menu);

        $scope.saveMenu(menu);

    }

    $scope.deleteMenu = function(menu){

        if(menu.items && menu.items.length > 1){
            if(!confirm('Delete "' + menu.title + '" & ' + menu.items.length + ' items?')){
                return;
            }
        }

        $scope.removeMenu(menu);

        var url = window.ajaxurl;

        var data = {
            ID: menu.ID || false
        };

        $http.post(url + '?action=food-truck&do=deleteMenu&_nonce=' + (window.trucklot_nonce || ''),data).then(function(res){

          $scope.saveState = null;

          if(res.data.error) {
            alert('Error: ' + res.data.error);
            return;
          }

          if(!res.data.ok){
            alert('Unable to save');
            return;
          }

          menu.ID = res.data.ID || false;
          menu.title = res.data.title || false;

          $scope.saveState = 'saved';

        },function(){
          alert('Unable to save');
        });

    }


    $scope.saveMenu = function(menu){

        var url = window.ajaxurl;

        var data = menu;

        if(data.ID == 'new') data.ID = null;

        $http.post(url + '?action=food-truck&do=saveMenu&_nonce=' + (window.trucklot_nonce || ''),data).then(function(res){

          $scope.saveState = null;

          if(res.data.error) {
            alert('Error: ' + res.data.error);
            return;
          }

          if(!res.data.ok){
            alert('Unable to save');
            return;
          }

          menu.ID = res.data.ID || false;
          menu.title = res.data.title || false;

          $scope.saveState = 'saved';

        },function(){
          alert('Unable to save');
        });

    }

    // Move list items up or down or swap
    $scope.move = function(array,oldIndex,newIndex){
      if(newIndex < 0 || newIndex >= array.length) return;
      array.splice(newIndex,0,array.splice(oldIndex,1)[0]);
    }

    $scope.$watch('menu_id',function(newId){
        if(!newId){
            $scope.menu = false;
            return;
        }
        $scope.menu_id = '' + newId;
        for (var i = 0; i < $scope.menus.length; i++) {
            if($scope.menus[i].ID == newId){
                $scope.menu = $scope.menus[i];
                $scope.menu_item = null;
                $scope.saveState = null;
                return;
            }
        };
    });


}]);

app.controller('CMFileSelector',function($scope){

  $scope.file = $scope.file || ($scope.multiple?[]:{});

  //$scope.remove = CovermenuUtils.remove;

  var settings = {
    title: 'Select Menu Item Image',
    button: {
      text: 'Use this Image'
    },
    //allowLocalEdits: false,
    //displaySettings: false,
    //displayUserSettings: false,
    multiple: $scope.multiple || false,
    library : { type : 'image'},//audio, video, application/pdf, ... etc
  };

  if($scope.type){
    settings.library.type = $scope.type == 'all' ? null : $scope.type;
  }

  var media = window.wp && window.wp.media && window.wp.media(settings);


  if(!media){
    console.warn('no media library');
    return;
  }
  //info: http://stackoverflow.com/questions/21540951/custom-wp-media-with-arguments-support

  var filterMediaResponseData = function(attachment){

    if(!attachment) return false;

    var filtered = {
      id: attachment.id,
      filename: attachment.filename,
      mime: attachment.mime,
      type: attachment.type,
      icon: attachment.icon,
      size: attachment.filesize,
      width: attachment.width,
      height: attachment.height,
      orientation: attachment.orientation,
      thumbnail: (attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : false),
      thumbnail_medium: (attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : false)
    };

    console.log(filtered);

    return filtered;

  }

  var mediaSelected = function(){
    $scope.$apply(function(){
      if(settings.multiple){
        var attachment = media.state().get('selection').toJSON();
        for (var i = 0; i < attachment.length; i++) {
          attachment[i] = filterMediaResponseData(attachment[i]);
        };
        $scope.file = attachment || [];
      }else{
        var attachment = media.state().get('selection').first().toJSON();
        $scope.file = filterMediaResponseData(attachment);
      }
    });
  }

  media.on( 'select', mediaSelected );

  media.on('open',function() {
      var selection = media.state().get('selection');

      var files = [].concat($scope.file);

      for (var i = 0; i < files.length; i++){
        if(!files[i].id) continue;
        files[i] = wp.media.attachment(files[i].id);
        files[i].fetch();
      };

      selection.add(files);

  });

  $scope.remove = function(item){
    var index = $scope.file.indexOf(item);
    $scope.file.splice(index, 1);
  }

/*
  //on close, if there is no select files, remove all the files already selected in your main frame
  frame.on('close',function() {
      var selection = frame.state('insert-image').get('selection');
      if(!selection.length){
          #remove file nodes
          #such as: jq("#my_file_group_field").children('div.image_group_row').remove();
          #...
      }
  });
*/
  $scope.removeFile = function(){
    $scope.file = {};
  }

  $scope.browse = function(){

    if(!media){
      alert('Wordpress file selector is unavailable');
      return;
    }

    media.open();

  }

});

app.directive('cmFileSelector',function(){

  return {
    scope:{
      file:'=',
      multiple:'@',
      type:'@'
    },
    //restrict: 'E',
    replace:true,
    template: jQuery('#trucklot-file').html(),
    controller:'CMFileSelector'//,
    //controllerAs:'cmfm'
  };

});


</script>


