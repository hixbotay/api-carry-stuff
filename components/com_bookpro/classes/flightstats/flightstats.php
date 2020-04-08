<?php

/**
 * @author Vitaly Dyatlov <md.xytop@gmail.com>
 */



require_once dirname(__FILE__) . '/Exception.php';

class Flightstats
{
    /**
     * Your EAN-issued account ID.
     * This number is used for tracking sales for statistics and commissions purposes on live sites.
     *
     * @var int
     */
    protected $appid;
    /**
     * Your EAN-issued access key to the API
     * Determines your access to live bookings, your authentication method (IP or signature-based) and request quotas.
     *
     * @var string
     */
    protected $appkey;
    protected $flight_api_url = 'api.flightstats.com/flex/schedules/rest/';
    protected $method = 'GET';
    protected $protocol = 'https://';

    public function __construct($appid, $appkey)
    {
        $this->appid = $appid;
        $this->appkey = $appkey;

    }

    public function set_method( $method )
    {
        $this->method = strtoupper($method);
    }

    public function set_protocol( $protocol )
    {
        $this->protocol = strtolower($protocol);
    }


    public function __call($name, $args)
    {

        $response = $this->safeCall($name, $args);

        if( isset($response['EanWsError']) )
        {
            $app=JFactory::getApplication();
            $exception=new ExpediaException( $response['EanWsError'] );
            $exception_data=$exception->getData();
            return $exception_data;
        }
        return $response;
    }

    /**
     * SOAP calls going here
     *
     * @param string $name
     * @param array $args
     */
    public function safeCall($name, $args)
    {
        $url = $this->protocol . $this->flight_api_url . $name;
        $ch = curl_init();
        if (count($args)) {
            assert(count($args) == 1);

            $data = array(
                    'appId' => $this->appid,
                    'appKey' => $this->appkey,
                ) + $args[0];
            $url .= '?' . http_build_query($data);
            if( $this->method == 'GET' ) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            }
            else {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            }
        }

        $header[] = "Accept: application/json";
        $header[] = "Accept-Encoding: gzip";
        $header[] = "Content-length: 0";

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'rw+');
        $result = curl_exec($ch);
        $response = json_decode($result, true);

        return $response;
    }

    public function get_http_dump()
    {
        return $this->verbose_log;
    }
}
