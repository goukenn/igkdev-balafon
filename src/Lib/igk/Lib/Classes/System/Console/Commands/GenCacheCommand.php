<?php
// @author: C.A.D. BONDJE DOUE
// @filename: GenCacheCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Commands\DbCommandHelper;
use IGK\System\Console\Logger;

/**
* Gen cache command.
* @package IGK\System\Console\Commands
*/
class GenCacheCommand extends AppExecCommand{
    /**
     * command
     */
    var $command="--gen:cache";
    /**
     * description
     */
    var $desc = "generate web cache";
    /**
    * Property: options.
    * @var mixed
    */
    var $options;
    /**
    * .ctr
    */
    public function __construct()
    {
        $this->options =  DbCommandHelper::GetDbCommandsProperties();
    }
    /**
    * Exec.
    * @param mixed $command
    * @param null|mixed $uri
    */
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
        $data = igk_curl_post_uri("http://localhost/".$uri,["igk_cache"=>1],null,[
        ]);
        igk_io_w2file( $file = igk_dir(implode(DIRECTORY_SEPARATOR, [igk_io_cachedir()."/pages", $path])), $data);
        Logger::success("done: ".$file);
    }
}