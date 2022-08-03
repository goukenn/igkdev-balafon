<?php
// @author: C.A.D. BONDJE DOUE
// @filename: GenCacheCommand.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger; 

class GenCacheCommand extends AppExecCommand{
    /**
     * command
     */
    var $command="--gen:cache";

    /**
     * description
     */
    var $desc = "generate web cache";
    
    var $options;

    public function __construct()
    {
        $this->options =  DbCommandHelper::GetDbCommandsProperties();
    }

    public function exec($command, $uri=null){
      
        DbCommandHelper::Init($command);
        Logger::print("generate cache");
        $path = "index.php"; 
        if ($uri !== null){
            $path = explode("?", $uri)[0];
           
        }
        $path = sha1($path);
        if (igk_io_path_ext($path)!=".php"){
            $path.=".php";
        }
        // igk_register_page_cache($uri, $path);       
        // $ctrl = igk_get_defaultwebpagectrl();
        // igk_wln("default : ".$ctrl->getName());
        $data = igk_curl_post_uri("http://localhost/".$uri,["igk_cache"=>1],null,[

        ]);
     
        igk_io_w2file( $file = igk_io_dir(implode(DIRECTORY_SEPARATOR, [igk_io_cachedir()."/pages", $path])), $data);
        Logger::success("done: ".$file);
    }
}