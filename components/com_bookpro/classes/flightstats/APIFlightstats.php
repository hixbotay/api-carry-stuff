<?php

/**
 * @author Vitaly Dyatlov <md.xytop@gmail.com>
 */


require_once dirname(__FILE__) . '/flightstats.php';
require_once dirname(__FILE__) . '/Exception.php';

class APIFlightstats extends Flightstats
{
    public function getFlights($from, $to, $departing)
    {
        $departing = JFactory::getDate($departing)->format('Y/m/d');
        $name = "v1/json/from/$from/to/$to/departing/$departing";
        $params = array();
        $flights=$this->{$name}($params);
        return $flights;

    }

}
