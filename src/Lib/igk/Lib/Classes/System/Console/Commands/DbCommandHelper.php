<?php

namespace IGK\System\Console\Commands;

/**
 * db command helper
 * @package IGK\System\Console\Commands
 */
abstract class DbCommandHelper{
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
}