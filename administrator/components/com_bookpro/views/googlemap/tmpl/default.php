<?php
/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 63 2012-07-29 10:43:08Z quannv $
 **/

defined('_JEXEC') or die;
JHtml::_('behavior.formvalidation');
JHtml::_('jquery.framework');
JHtml::_('jquery.ui');
BookProHelper::setSubmenu(1);
$doc = JFactory::getDocument();
$doc->addScript("http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");

?>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <style type="text/css">
            body { }
            #sidebar_map { width: 1000px}
            #main {  }
            .infoWindow { width: 220px; }
    </style>
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript"><!--
			var map;
	 
			// HN
			var center = new google.maps.LatLng(21.027500,105.832329);
			 
			var geocoder = new google.maps.Geocoder();
			var infowindow = new google.maps.InfoWindow();
	
			//var location = new google.maps.LatLng(<?php //echo $this->$this->items->from ?>,<?php //echo $this->$this->items->to ?>);		
			 
			function init() {
			 
				var mapOptions = {
				  zoom: 13,
				  center: center,
				  mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				 
				map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
				var content ='<div class="infoWindow"><strong>'  + "abc" + '</strong>'
					            + '<br/>'     + "cde"
					            + '<br/>'     + "fgh" + '</div>';			
				var marker = new google.maps.Marker({
					map: map, 				
					position: center,
				});
				
				google.maps.event.addListener(marker, 'click', function() {
					infowindow.setContent(content);
					infowindow.open(map,marker);
				});
			}
		</script>
<!--<script src="https://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>

   <script type="text/javascript">
      var map;
      var TILE_SIZE = 256;
      var chicago = new google.maps.LatLng(<?php //echo $this->obj->from ?>,<?php //echo $this->obj->to ?>);

      function bound(value, opt_min, opt_max) {
        if (opt_min != null) value = Math.max(value, opt_min);
        if (opt_max != null) value = Math.min(value, opt_max);
        return value;
      }

      function degreesToRadians(deg) {
        return deg * (Math.PI / 180);
      }

      function radiansToDegrees(rad) {
        return rad / (Math.PI / 180);
      }

      function MercatorProjection() {
        this.pixelOrigin_ = new google.maps.Point(TILE_SIZE / 2,
            TILE_SIZE / 2);
        this.pixelsPerLonDegree_ = TILE_SIZE / 360;
        this.pixelsPerLonRadian_ = TILE_SIZE / (2 * Math.PI);
      }

      MercatorProjection.prototype.fromLatLngToPoint = function(latLng, opt_point) {
        var me = this;
        var point = opt_point || new google.maps.Point(0, 0);
        var origin = me.pixelOrigin_;

        point.x = origin.x + latLng.lng() * me.pixelsPerLonDegree_;

        // NOTE(appleton): Truncating to 0.9999 effectively limits latitude to
        // 89.189.  This is about a third of a tile past the edge of the world
        // tile.
        var siny = bound(Math.sin(degreesToRadians(latLng.lat())), -0.9999, 0.9999);
        point.y = origin.y + 0.5 * Math.log((1 + siny) / (1 - siny)) * -me.pixelsPerLonRadian_;
        return point;
      };

      MercatorProjection.prototype.fromPointToLatLng = function(point) {
        var me = this;
        var origin = me.pixelOrigin_;
        var lng = (point.x - origin.x) / me.pixelsPerLonDegree_;
        var latRadians = (point.y - origin.y) / -me.pixelsPerLonRadian_;
        var lat = radiansToDegrees(2 * Math.atan(Math.exp(latRadians)) - Math.PI / 2);
        return new google.maps.LatLng(lat, lng);
      };

      function createInfoWindowContent() {
        var numTiles = 1 << map.getZoom();
        var projection = new MercatorProjection();
        var worldCoordinate = projection.fromLatLngToPoint(chicago);
        var pixelCoordinate = new google.maps.Point(
            worldCoordinate.x * numTiles,
            worldCoordinate.y * numTiles);
        var tileCoordinate = new google.maps.Point(
            Math.floor(pixelCoordinate.x / TILE_SIZE),
            Math.floor(pixelCoordinate.y / TILE_SIZE));

        return ['<?php //echo $this->items->recipient_info ?>'          
               ].join('<br>');
      }

      function initialize() {
        var mapOptions = {
          zoom: 13,
          center: chicago,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById('map_canvas'),
            mapOptions);
        var marker = new google.maps.Marker({
            position: chicago,
            map: map,
            title:"<?php //$this->items->recipient_info ?>"
        });
        marker.setMap(map);
        //map.addOverlay(marker);
        var coordInfoWindow = new google.maps.InfoWindow();
        //coordInfoWindow.setContent(createInfoWindowContent());
        coordInfoWindow.setPosition(chicago);
        coordInfoWindow.open(map);

		
        google.maps.event.addListener(map, 'zoom_changed', function() {
          coordInfoWindow.setContent(createInfoWindowContent());
          coordInfoWindow.open(map);
        });
      }
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
--></head>
 <body onload="init()">
	<div class="span12" id='j-main-container'>
	 	<div id="map_canvas" style="width: 100% ; height: 450px"></div>
		<!--<section id="sidebar_map">
            <div id="directions_panel"></div>
        </section>

        <section id="main">
            <div id="map_canvas" style="width: 100%; height: 450px;"></div>
        </section>-->
	</div>
 