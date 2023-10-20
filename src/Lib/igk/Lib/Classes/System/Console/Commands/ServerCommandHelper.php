<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbCommandHelper.php
// @date: 20220803 13:48:57
// @desc: 
// usage command exemple : 
// --user:4 -db_server:0.0.0.0 --querydebug --debug --controller:TonerAfrikaController -srv_request_uri://localhost:7300 -srv_host:'presentation' -srv_name:'jum' -srv_root:src/public -srv_https:1


namespace IGK\System\Console\Commands;

use IGK\System\Console\Logger;
use IGK\System\Console\ServerFakerInput;
use IGK\System\Http\Request;
use IGK\System\IO\Path;
use IGK\System\Uri;
use IGKValidator;

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
            '-srv_host' => "host",
            '-srv_name' => "server_name",
            '-srv_root' => "server_root",
            '-srv_https' => "https", // <- enable https
            '-srv_geox' => "geox", 
            '-srv_geoy' => "geoy",
            '-srv_city' => "city",
            '-srv_country_code' => "country_code",
            '-srv_country_name' => "country_name",
            '-srv_region' => "region",
            '-srv_request' => "request", // + | <- request args
            '-srv_request_uri' => "request-uri",
            '-srv_baseuri' => "base_uri", // + | <- set command environment base uri
            '-srv_referer'=>"referer",
            '-srv_ajx'=>'ajx' // set command to ajx request 

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
        global $_REQUEST;
        $cnf = igk_configs();
        $v_request_faker_key = Request::REQUEST_JSON_DATA_ENV_KEY;

        foreach (self::GetDbCommandsProperties() as $k => $v) {
            if (property_exists($command->options, $k)) {
                $cnf->$v = $command->options->{$k};
            }
        } 
        if ($root = $cnf->{'server_root'}) {
            if (is_dir($root)) {
                $root = realpath($root);
            }
        } else {
            if ($envs = $command->app->getConfigs()->env) {
                if (!is_array($envs)) {
                    $envs = [$envs];
                }
                foreach ($envs as $k) {
                    if ($k->name == 'IGK_BASE_DIR') {
                        $root = realpath($k->value);
                        break;
                    }
                }
            }
            //$root = IGK_BASE_DIR; //$command->app->getConfigs()->env;
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
        if ($cnf->{'ajx'}){
            $_SERVER['HTTP_IGK_AJX'] = 1;
        }

        if ($r = $cnf->{'request'}) {
            parse_str($r, $tab);
            $_REQUEST = $tab;
            igk_environment()->set($v_request_faker_key, new ServerFakerInput($r));
        }
        if (property_exists($command->options, '-srv_baseuri')) {
            igk_environment()->setBaseUri($cnf->{'base_uri'} ?? '');
        }
        if (($ref = $cnf->{'referer'}) && !isset($_SERVER['HTTP_REFERER'])){
            if (!IGKValidator::IsUri($ref)){
                $g = Uri::FromParseUrl(parse_url(igk_io_baseuri()));
                $ref = $g->getSiteUri().$ref;
            }
            $_SERVER['HTTP_REFERER'] = $ref;
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
