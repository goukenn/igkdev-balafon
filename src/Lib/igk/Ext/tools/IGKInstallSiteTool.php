<?php
// @file: IGKInstallSiteTool.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\Controllers\ToolControllerBase;
use IGK\Helper\IO;
use IGK\System\Installers\InstallerUtils;
use IGK\System\IO\StringBuilder;

///<summary>Represente class: IGKInstallSiteTool</summary>
/**
* Represente IGKInstallSiteTool class
*/
class IGKInstallSiteTool extends ToolControllerBase{
    ///<summary></summary>
    /**
    * 
    */
    public function doAction(){
        $frame=igk_html_frame($this, "tool.installsite");
        $d=$frame->getBoxContent();
        $d->clearChilds();
        $d["class"]="google-Roboto";
        $frm=$d->addForm();
        $frm["action"]=$this->getUri("install");
        $frm->addSectionTitle(4)->Content="Install Site ";
        $frm->addInput("dir", "text")->setAttribute("placeholder", "target directory");
        $frm->addInput("btn.submit", "submit");
        $frm->addInput("frame", "hidden", 1);
        igk_html_form_initfield($frm);
        $frame->renderAJX();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getIsAvailable(){
        return true;
    }
    ///<summary>install site </summary>
    /**
    * install site
    */
    public function install(){
        $g=igk_getr("dir");
        $result = igk_create_xmlnode("response");

        if(empty($g) || !IO::CreateDir($g)){

            $result->msg="failed to create {$g} directory";
            igk_json(json_encode($result));
            igk_exit();
        }
        $root=$g."/src";
        $sep=DIRECTORY_SEPARATOR;
        $appdir=$root."/application";
        IO::CreateDir($appdir);
        igk_io_symlink(dirname(IGK_LIB_DIR), $appdir."/Lib");
        $dir=$root."/public";
        IO::CreateDir($dir);
        $appdir=igk_uri($appdir);
        $sb = new StringBuilder();
        $sb->appendLine(InstallerUtils::GetEntryPointSource([
            "app_dir"=>$appdir,
            "project_dir"=>$appdir."/Projects",
            "entry_app_dir"=>"../",
        ]));  
        igk_io_w2file($dir."/index.php",  "".$sb);
        if($frame=igk_getr("frame")){
            igk_ajx_replace_uri(igk_server()->HTTP_REFERER);
        }
    }
}
