<?php
/**
 * jBackend component for Joomla
 *
 * @author selfget.com (info@selfget.com)
 * @package jBackend
 * @copyright Copyright 2014 - 2015
 * @license GNU Public License
 * @link http://www.selfget.com
 * @version 2.1.3
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Routing class from com_jbackend
 */
class jBackendRouter extends JComponentRouterBase
{
  /**
   * Build the route for the com_jbackend component
   *
   * @param   array  &$query  An array of URL arguments
   *
   * @return  array  The URL arguments to use to assemble the subsequent URL
   */
  public function build(&$query)
  {
    $segments = array();

    if(isset($query['action']))
    {
      $segments[] = $query['action'];
      unset( $query['action'] );

      if(isset($query['module']))
      {
        $segments[] = $query['module'];
        unset( $query['module'] );

        if(isset($query['resource']))
        {
          $segments[] = $query['resource'];
          unset( $query['resource'] );

          if(isset($query['id']))
          {
            $segments[] = $query['id'];
            unset( $query['id'] );
          };

        };

      };

    }
    return $segments;
  }

  /**
   * Parse the segments of a URL
   *
   * @param   array  &$segments  The segments of the URL to parse
   *
   * @return  array  The URL attributes to be used by the application
   */
  public function parse(&$segments)
  {
    $vars = array();

    $count = count($segments);
    if ($count == 3)
    {
      $vars['action'] = $segments[0];
      $vars['module'] = $segments[1];
      $vars['resource'] = $segments[2];
    } else if ($count == 4) {
      $vars['action'] = $segments[0];
      $vars['module'] = $segments[1];
      $vars['resource'] = $segments[2];
      $vars['id'] = $segments[3];
    }

    return $vars;
  }

}

/**
 * jBackend router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function jBackendBuildRoute(&$query)
{
  $router = new jBackendRouter;

  return $router->build($query);
}

function jBackendParseRoute($segments)
{
  $router = new jBackendRouter;

  return $router->parse($segments);
}
