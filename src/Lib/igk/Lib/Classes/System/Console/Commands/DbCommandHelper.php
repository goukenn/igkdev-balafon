<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbCommandHelper.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\System\Console\Logger;

/**
 * db command helper
 * @package IGK\System\Console\Commands
 */
abstract class DbCommandHelper{
    public static function GetUsageCommandHelp():array{
        $tab = self::GetDbCommandsProperties();
        $tab = array_fill_keys(array_keys($tab), null );
        
        return $tab;
    }
    public static function GetDbCommandsProperties(){
        return [
            "-db_name"=>"db_name",
            "-db_user"=>"db_user",
            "-db_pwd"=>"db_pwd",
            "-db_server"=>"db_server",
            "-db_prefix"=>"db_prefix",
            "-db_driver"=>"db_driver",
            "-db_port"=>"db_port",
            "-db_connexion_string"=>"db_connexion_string",
        ];
    }
    public static function Init($command){ 
        $cnf = igk_configs();
        foreach(self::GetDbCommandsProperties() as $k=>$v){
            if (property_exists($command->options, $k)){   
                $cnf->$v = $command->options->{$k};
             }    
        }  
        // + | activate query debug if requested  
        if (property_exists($command->options, "--querydebug")){
            igk_environment()->querydebug = 1;
        }
    }
    public static function ShowUsage(){
        foreach(array_keys(self::GetDbCommandsProperties()) as $k){
            Logger::print($k);            
        }
    }
}