<?php
// @file: ControllerTypeBase.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;
 

abstract class ControllerTypeBase extends BaseController{
    ///<summary></summary>
    public function __construct(){
        parent::__construct();
    }
    ///<summary></summary>
    public static function GetAdditionalConfigInfo(){
        return null;
    }
    ///<summary>get de default string content</summary>
    public static function GetAdditionalDefaultViewContent(){
        static $viewcomment=null;
        if($viewcomment === null)
            $viewcomment=implode("\n* ", explode("\n", trim(igk_ob_get_func(function() use (& $viewcomment){
            include(IGK_LIB_DIR."/Inc/default.view.comment.inc");
        }))))."\n*/";
        $r="<?php\n/**\n* ".igk_html_eval_article("{$viewcomment}\n\$t->clearChilds();\nigk_html_article(\$this , \"default\", \$t);\n", ["author"=>igk_sys_getconfig("developer", IGK_AUTHOR), "date"=>date("Y-m-d H:i:s"), "version"=>1.0 ]);
        return $r;
    }
    ///<summary></summary>
    public static function GetCtrlCategory(){
        return "DEFAULT";
    }
    ///<summary></summary>
    ///<param name="t" ref="true"></param>
    public static function SetAdditionalConfigInfo(& $t){
        return 1;
    }
}
