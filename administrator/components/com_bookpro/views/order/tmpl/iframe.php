<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
    </style>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
    <script type="text/javascript">
    var i;
    function initialize() {
    	
    	var trip_location = <?php echo $this->item->trip_location; ?>,
                     options = {
                         zoom: 12,
                         center: new google.maps.LatLng(48.8622295,2.3384993),
                         mapTypeId: google.maps.MapTypeId.ROADMAP
                     },
                     map1 = new google.maps.Map( 
                         document.getElementById( 'map' ), 
                         options 
                     );
    	
    	
                 for( i = 0; i < trip_location.length; i++ ) {
                     var latlng = new google.maps.LatLng(trip_location[i].latitude, trip_location[i].longitude),
                         marker = new google.maps.Marker( {position: latlng, map: map1,
                             title: "Updated time: " + trip_location[i].updated_time} );
                     marker.setMap( map1 );
                     //bindInfoWindow(marker, map, infowindow, "<p>" + property_list[index].updated_time + "</p>");
                 };
                             
    }   	
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</head>
<body>
	<div id="map" style="height: 350px"/>
</body>
</html>