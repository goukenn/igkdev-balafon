<?php
// @file: ControllerTypeBase.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;
 
/**
 * controller type base 
 */
abstract class ControllerTypeBase extends BaseController{   
    public static function GetAdditionalConfigInfo(){
        return null;
    }
    public static function GetAdditionalDefaultViewContent(){
        static $viewcomment=null;
        if($viewcomment === null)
            $viewcomment=implode("\n* ", explode("\n", trim(igk_ob_get_func(function() use (& $viewcomment){
            include(IGK_LIB_DIR."/Inc/default.view.comment.inc");
        }))))."\n*/";
        $r="<?php\n/**\n* ".igk_html_eval_article("{$viewcomment}\n\$t->clearChilds();\nigk_html_article(\$this , \"default\", \$t);\n", 
            [
                "author"=>igk_sys_getconfig("developer", IGK_AUTHOR), 
                "date"=>date(Constants::MYSQL_DATETIME_FORMAT), 
                "version"=>1.0 ,
                "desc"=>"",
            ]);
        return $r;
    }
    public static function GetCtrlCategory(){
        return "DEFAULT";
    }
    public static function SetAdditionalConfigInfo(& $t){
        return 1;
    }
}
