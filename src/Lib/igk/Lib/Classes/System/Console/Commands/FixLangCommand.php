<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FixLangCommand.php
// @date: 20220803 13:48:57
// @desc: 


use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder;

class FixLangCommand extends AppExecCommand{
    var $command = "--fix:lang";
    var $category = "Fixing";
    var $desc = "fix language file";
    public function exec($command, $path=null)
    { 
        Logger::info("fix lang file");
        if (file_exists($f = realpath($path))){
            $ext = igk_io_path_ext($f);
            if ($ext!="presx"){
                return -1;
            } 

            $l = [];
            include($f);
            ksort($l, SORT_NATURAL | SORT_FLAG_CASE);
            $o = "";

            foreach($l as $k=>$v){
               $o.= "\$l['".addslashes($k)."'] = \"{$v}\";".PHP_EOL;
            }
            $author = $this->getAuthor($command);
       
            $builder = new PHPScriptBuilder();
            $builder->desc("lang file")->type("function")
            ->author($author)
            ->defs($o);
            igk_io_w2file($f, $builder->render()); 
            Logger::success("fix lang file: ".$f);
        }

    }
}