<?php
// @author: C.A.D. BONDJE DOUE
// @file: MakeUtility.php
// @date: 20230303 10:11:38
namespace IGK\System\Console\Commands;

use IGK\Resources\R;
use IGK\System\IO\File\PHPScriptBuilder;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Commmands
*/
class MakeUtility {
    public static function CreateEmptyScriptCallback(){
        return function($file){
            $g = new PHPScriptBuilder;
            $g->type("function");
            $g->file(igk_io_collapse_path($file));
            igk_io_w2file($file, $g->render());
        };
    }
    /**
     * bind default lang supports
     * @param mixed $command 
     * @param mixed $dir 
     * @param mixed $bind 
     * @return void 
     * @throws IGKException 
     */
    public static function BindDefaultLangSupport($command, $dir, & $bind){
        if (!igk_getv($command->options, "--no-init-lang")){
            if ($v_langs = R::GetSupportedLangs()){
                $v_fc_php_empty_file = MakeUtility::CreateEmptyScriptCallback();
                foreach($v_langs as $k){
                    $bind[$dir."/Configs/Lang/lang.".$k.".presx"] = $v_fc_php_empty_file;
                }
            }
        }
    }
}