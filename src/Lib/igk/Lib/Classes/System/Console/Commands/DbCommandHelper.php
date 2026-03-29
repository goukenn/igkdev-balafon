<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbCommandHelper.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\Helper\SysUtils;
use IGK\System\Console\Logger;
/**
 * db command helper
 * @package IGK\System\Console\Commands
 */
abstract class DbCommandHelper
{

    /**
    * Seeds.
    * @param null|mixed $ctrl
    * @param null|mixed $class
    */
    public static function Seed($ctrl=null, $class = null){
        if ($ctrl) {
            if ($c = SysUtils::GetControllerByName($ctrl, false)) {
                $inf = get_class($c);
                if (!empty($class))
                    $inf .= "::" . $class;
                Logger::print("seed... " . $inf . " query debug: " . igk_environment()->querydebug);
                $c::register_autoload();  
                $c::seed($class);
                Logger::success("seed complete");
                return 1;
            } else {
                Logger::danger("controller [$ctrl] not found");
            }
        } else {
            $c = igk_sys_getall_ctrl();
            foreach ($c as $t) {
                $t::register_autoload();
                Logger::info("seed:" . get_class($t));
                if ($t::seed()) {
                    Logger::success("seed:" . get_class($t));
                }
            }
        }
    }

    /**
    * Returns Usage Command Help.
    * @return array
    */
    public static function GetUsageCommandHelp(): array
    {
        $tab = self::GetDbCommandsProperties();
        $tab = array_fill_keys(array_keys($tab), null);
        return $tab;
    }

    /**
    * auto generate doc.
    * @return array{-db_name: string, -db_user: string, -db_pwd: string, -db_server: string, -db_prefix: string, -db_driver: string, -db_port: string, -db_connexion_string: string}
    */

    public static function GetDbCommandsProperties()
    {
        return [
            "-db_name" => "db_name",
            "-db_user" => "db_user",
            "-db_pwd" => "db_pwd",
            "-db_server" => "db_server",
            "-db_prefix" => "db_prefix",
            "-db_driver" => "db_driver",
            "-db_port" => "db_port",
            "-db_charset"=>"db_charset",
            "-db_connexion_string" => "db_connexion_string",
        ];
    }

    /**
    * Initializes.
    * @param mixed $command
    */
    public static function Init($command)
    {
        $cnf = igk_configs();
        foreach (self::GetDbCommandsProperties() as $k => $v) {
            if (property_exists($command->options, $k)) {
                $cnf->$v = $command->options->{$k};
            }
        }
        self::_CheckInitCommand($cnf, $command);
        // + | activate query debug if requested  
        if (property_exists($command->options, "--querydebug")) {
            igk_environment()->querydebug = 1;
        }
    }

    /**
    * Shows Usage.
    */
    public static function ShowUsage()
    {
        foreach (array_keys(self::GetDbCommandsProperties()) as $k) {
            Logger::print($k);
        }
    }
    /**
     * check and fallback
     * @param mixed $cnf 
     * @param mixed $command 
     * @return void 
     */
    private static function _CheckInitCommand($cnf, $command)
    {
        /**
         * check for data environment data server
         */
        if ($cnf->default_dataadapter == 'MYSQL') {
            if (
                !property_exists($command->options, '-db_server') &&
                ($env = getenv('IGK_MYSQL_DB_SERVER'))
            ) {
                $cnf->db_server = $env;
            }
            if (
                !property_exists($command->options, '-db_user') &&
                ($env = getenv('IGK_MYSQL_DB_USER'))
            ) {
                $cnf->db_user = $env;
            }
            if (
                !property_exists($command->options, '-db_name') &&
                ($env = getenv('IGK_MYSQL_DB_NAME'))
            ) {
                $cnf->db_name = $env;
            }
            if (
                !property_exists($command->options, '-db_pwd') &&
                ($env = getenv('IGK_MYSQL_DB_PWD'))
            ) {
                $cnf->db_pwd = $env;
            }
            if (
                !property_exists($command->options, '-db_charset') &&
                ($env = getenv('IGK_MYSQL_DB_CHARSET'))
            ) {
                $cnf->db_charset = $env;
            }

            array_filter(getenv(), function($v, $k)use($cnf){
                if (preg_match('/^IGK_APP_CONF_/', $k, $t)){
                    $k = strtolower( substr($k, strlen($t[0])));
                    $cnf->{$k} = $v;
                }
            }, 1);
        }
    }
}