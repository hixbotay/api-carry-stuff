<?php
/**
 *
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id$
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
$data = $displayData;
$doc = JFactory::getDocument();
$doc->addScript('https://www.google.com/jsapi');

?>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
         <?php echo $data->data;?>
        ]);

        var options = {          
          <?php if ($data->option){
          	echo $data->option;
          }?>
          
         
        };

        var chart = new google.visualization.<?php echo $data->type?>(document.getElementById('chart_div<?php echo $data->id;?>'));

        chart.draw(data, options);
      }
</script>
 	<div id="chart_div<?php echo $data->id;?>"></div>
 	

