<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ServeCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\System\Console\App;
use IGK\System\Console\AppCommand;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use function igk_getv as getv;

class ServeActionCommand extends AppExecCommand{
    var $command = "--serve"; 
 
    var $category = "server";

    var $desc = "serve a project with the php built-in Server";

    var $options = [ 
        "--type"=>"defaut action type class"
    ]; 
    public function exec($command, $controller="", $actionName=""){
        $port = getv($command->options, "-p", 5000);
        $bdir = getv($command->options, "-basedir",igk_io_basedir());
        $server = "localhost:".$port;
        Logger::print("Serve Single Project");
        Logger::print("document root: $bdir");
        Logger::print("uri: http://$server");
        `php -S $server -t $bdir '{$bdir}/index.php'`;

    }
    public function help(){
        Logger::print("-");
        Logger::info("Serve Project");
        Logger::print("-\n");
        Logger::print("Usage : ". App::Gets(App::GREEN, $this->command). " ctrl name [options]" );

        Logger::print("-p:". App::Gets(App::GREEN, $this->command). " port number to use default is5000" );
        Logger::print("-basedir:". App::Gets(App::GREEN, $this->command). " set public directory" );

        Logger::print("\n\n");
    }
}