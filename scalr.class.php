<?php
require('xml2array.class.php');

class ScalrClient
{
    private $valid_api_methods=array(
    'ApacheVhostCreate',
    'ApacheVhostsList',
    'BundleTaskGetStatus',
    'DmApplicationDeploy',
    'DmApplicationsList',
    'DmSourcesList',
    'DNSZoneCreate',
    'DNSZoneRecordAdd',
    'DNSZoneRecordRemove',
    'DNSZoneRecordsList',
    'DNSZonesList',
    'EventsList',
    'FarmGetDetails',
    'FarmGetStats',
    'FarmsList',
    'FarmTerminate',
    'FarmLaunch',
    'LogsList',
    'RolesList',
    'ScriptExecute',
    'ScriptGetDetails',
    'ScriptsList',
    'ServerImageCreate',
    'ServerLaunch',
    'ServerReboot',
    'ServerTerminate',
    'StatisticsGetGraphURL');

    private $api_key = '';
    private $secret_key = '';
    private $url = '';
    private $api_version = '';

    function __construct($api_key, $secret_key, $url='https://api.scalr.net/', $api_version='2.3.0')
    {
        $this->api_key = $api_key;
        $this->secret_key = $secret_key;
        $this->url = $url;
        $this->api_version = $api_version;
    }

    function __call($name, $arguments=null)
    {
        if (in_array($name, $this->valid_api_methods))
        {
            return $this->call($name, $arguments);
        }
    }

    function call($action, $more_params=null)
    {
        // Build query arguments list
        $params = array(
            'Action' => $action,
            'KeyID' => $this->api_key,
            'Version' => $this->api_version,
            'Timestamp' => date("c")
        );

        if ($more_params && is_array($more_params)) {
            $params = array_merge($params, $more_params);
        }

        // Sort arguments
        ksort($params);

        // Generate string for sign
        $string_to_sign = "";
        foreach ($params as $k => $v)
            $string_to_sign .= "{$k}{$v}";

        // Generate signature
        $params['Signature'] = base64_encode(hash_hmac('SHA256', $string_to_sign, $this->secret_key, 1));

        // Build query
        $query = http_build_query($params);

        // Execute query
        $reply = file_get_contents($this->url."?{$query}");
        return ArrayToXML::toArray($reply);
    }
}

?>
