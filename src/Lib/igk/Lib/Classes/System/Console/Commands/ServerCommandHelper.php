<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbCommandHelper.php
// @date: 20220803 13:48:57
// @desc: 
// usage command exemple : 
// --user:4 -db_server:0.0.0.0 --querydebug --debug --controller:TonerAfrikaController -srv_request_uri://localhost:7300 -srv_host:'presentation' -srv_name:'jum' -srv_root:src/public -srv_https:1


namespace IGK\System\Console\Commands;

use IGK\System\Console\Logger;
use IGK\System\IO\Path;

/**
 * db command helper
 * @package IGK\System\Console\Commands
 */
abstract class ServerCommandHelper
{
    //load command serve command
    public static function GetDbCommandsProperties()
    {
        return [
            "-srv_request_uri" => "request-uri",
            "-srv_host" => "host",
            "-srv_name" => "server_name",
            "-srv_root" => "server_root",
            "-srv_https" => "https",
            "-srv_geox" => "geox",
            "-srv_geoy" => "geoy",
            "-srv_city" => "city",
            "-srv_country_code" => "country_code",
            "-srv_country_name" => "country_name",
            "-srv_region" => "region",
            "-srv_request" => "request", // request args

        ];
    }
    public static function GetUsageCommandHelp()
    {
        $tab = self::GetDbCommandsProperties();
        $tab = array_fill_keys(array_keys($tab), null);

        return $tab;
    }
    public static function Init($command)
    {
        $cnf = igk_configs();
        foreach (self::GetDbCommandsProperties() as $k => $v) {
            if (property_exists($command->options, $k)) {
                $cnf->$v = $command->options->{$k};
            }
        }
        if ($root = $cnf->{'server_root'}) {
            if (is_dir($root)) {
                $root = realpath($root);
            }
        }
        $_SERVER['REQUEST_URI'] = $cnf->{'request-uri'} ?? igk_getv($_SERVER, 'REQUEST_URI');
        $_SERVER['HTTP_HOST'] = $cnf->{'host'} ?? igk_getv($_SERVER, 'HTTP_HOST');
        $_SERVER['SERVER_NAME'] = $cnf->{'server_name'} ?? igk_getv($_SERVER, 'SERVER_NAME');
        $_SERVER['DOCUMENT_ROOT'] = $root ?? igk_getv($_SERVER, 'DOCUMENT_ROOT');
        $_SERVER['HTTPS'] = $cnf->{'https'} ? 'on' : 0;


        $_SERVER['GEOIP_LATITUDE'] = $cnf->{'geox'};
        $_SERVER['GEOIP_LONGITUDE'] = $cnf->{'geoy'};
        $_SERVER['GEOIP_COUNTRY_CODE'] = $cnf->{'country_code'};
        $_SERVER['GEOIP_COUNTRY_NAME'] = $cnf->{'country_name'};
        $_SERVER['GEOIP_REGION'] = $cnf->{'region'};
        $_SERVER['GEOIP_CITY'] = $cnf->{'city'};

        if ($r = $cnf->{'request'}){
            parse_str($r, $tab);
            $_REQUEST = $tab;
        }
        igk_server()->prepareServerInfo();
        igk_server()->IS_WEBAPP = 0;
        Path::getInstance()->prepareData();
    }
    public static function ShowUsage()
    {
        foreach (array_keys(self::GetDbCommandsProperties()) as $k) {
            Logger::print($k);
        }
    }
}
