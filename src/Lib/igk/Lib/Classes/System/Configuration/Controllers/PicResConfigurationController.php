<?php
// @file: IGKPICRESCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Configuration\Controllers;
use IGK\Controllers\BaseController;
use IGK\Resources\R;
use IGKHtmlRelativeUriValueAttribute; 
use IGK\Helper\IO;
use IGK\System\Html\Dom\HtmlSearchNode;
use IGK\System\Html\HtmlUtils;
use IGK\Helper\IO as IGKIO;
use function igk_resources_gets as __;
/**
 * manage picture resources
 * @package IGK\System\Configuration\Controllers
 */
final class PicResConfigurationController extends ConfigControllerBase{
    /**
    * Constant: datafile.
    * @var mixed
    */
    const DATAFILE="Data/upload.csv";
    /**
    * Constant: key files.
    * @var mixed
    */
    const KEY_FILES="sys://ctrl/allpics";
    /**
    * Constant: picres flag.
    * @var mixed
    */
    const PICRES_FLAG=1;
    /**
    * Constant: picres key.
    * @var mixed
    */
    const PICRES_KEY="PicResChanged";
    /**
    * Constant: targetdir.
    * @var mixed
    */
    const TARGETDIR=IGK_RES_FOLDER."/Img";
    /**
    * Returns Is Visible.
    * @return bool
    */
    public function getIsVisible():bool{
        return false;
    }
    /**
    * Returns Is Config Page Available.
    */
    public function getIsConfigPageAvailable(){
        return false;
    }
    /**
    * Returns Config Group.
    */
    public function getConfigGroup(){
        igk_trace();
        igk_exit();
    }
    /**
    * auto generate doc.
    * @param mixed $div
    * @return
    */
    private function _addLoadPicForm($div){
        $frm=$div->addForm();
        $frm["action"]=$this->getUri("loadfile");
        $frm["method"]="POST";
        $frm["enctype"]=IGK_HTML_ENCTYPE;
        $frm->addSLabelInput("name", "text", null, null, true);
        $frm->addBr();
        $frm->addSLabelInput("pics", "file", null, array("multiple"=>false, "accept"=>"image/*"), true);
        $frm->addBr();
        $frm->addSLabelInput("dir", "text", $this->m_selectedir);
        $frm->addBr();
        $frm->addInput()->setAttributes(array("type"=>"hidden", "name"=>"MAXFILESIZE", "value"=>5000));
        $frm->addHSep();
        $frm->addBtn("upload", __("btn.upload"));
        return $frm;
    }
    /**
    * auto generate doc.
    * @return
    */
    private function _getexts(){
        $r=igk_get_env("sys://ctrl/picres/allowedextension", function(){
            $tab=explode(";", strtolower(IGK_ALLOWED_EXTENSIONS));
            $extensions=array();
            foreach($tab as $k){
                $extensions[strtolower($k)
                ]=$k;
            }
            return $extensions;
        });
        return $r;
    }
    ///init default resources
    /**
    * auto generate doc.
    * @param null|mixed & $tab
    * @return
    */
    private function _initDefaultPictureRes(& $tab=null){
        $dir=IGK_LIB_DIR."/Default/R/Img";
        $this->initPicturesRes($dir, $tab);
    }
    /**
    * Load data.
    * @param null|mixed & $tab
    */
    function _loadData(& $tab=null){
        $f=igk_io_syspath(self::DATAFILE);
        $txt=IO::ReadAllText($f);
        $lines=explode(IGK_LF, $txt);
        $this->_initDefaultPictureRes($tab);
        $this->initPicturesRes(igk_io_currentrelativepath(IGK_RES_FOLDER."/Img"), $tab);
        $g=array();
        foreach($lines as $l){
            if(empty($l))
                continue;
            $e=explode(igk_csv_sep(), $l);
            $g [$e[0]]=igk_uri($e[1]);
        }
        return $g;
    }
    /**
    * auto generate doc.
    * @return
    */
    private function _showdefault(){
        $div=$this->TargetNode->clearChilds()->addPanelBox();
        igk_html_add_title($div, "title.PictureResourcesManager");
        igk_html_article($this, "pictures.res", $div->div());
        $div->addNotifyHost("picres");
        $tab = null;
        if ($d = igk_io_currentrelativepath(self::TARGETDIR)){
            $c=igk_realpath($d);
            $tab=igk_io_dirs($c);
        }
        if($tab && (count($tab) > 0)){
            $ul=$div->add("ul");
            foreach($tab as $k){
                $li=$ul->li();
                $li->add("label", array("class"=>"-cllabel cell_minsize dispib"))->add("a", array(
                    "href"=>$this->getUri("setdir&d=".base64_encode(urldecode($k))),
                    "class"=>"config-fileviewdir"
                ))->Content=basename($k);
                if((count(igk_io_dirs($k)) == 0) && (count(igk_io_getfiles($k)) == 0)){
                    HtmlUtils::AddImgLnk($li, $this->getUri("dropdir&d=".base64_encode(urldecode($k))), "drop_16x16");
                }
            }
        }
        $frm=$div->addForm();
        $bx=$frm->addActionBar();
        HtmlUtils::AddBtnLnk($frm, "btn.showallpics", $this->getUri("showentries"));
        HtmlUtils::AddBtnLnk($frm, "btn.rmAll", $this->getUri("deleteall"), array("onclick"=>igk_js_lnk_confirm(__(IGK_MSG_ALLPICS_QUESTION))));
        $frm->addAJXButton($this->getUri("uploadpic_ajx"))->Content="uploadpics";
        $frm->addInput("confirm", "hidden", 0);
    }
    /**
    * auto generate doc.
    * @return
    */
    private function _storeData(){
        $out=IGK_STR_EMPTY;
        $g=$this->getAllPics();
        foreach($g as $k=>$v){
            $out .= $k.igk_csv_sep().$v.IGK_LF;
        }
        if(igk_io_save_file_as_utf8($path=igk_io_syspath(self::DATAFILE), $out, true)){
            $e=["file"=>$path];
            igk_sys_regchange(self::PICRES_KEY, $e);
            return true;
        }
        return false;
    }
    /**
    * auto generate doc.
    * @param mixed $ext
    * @return
    */
    private function _support($ext){
        $ext=$this->_getexts();
        return isset($ext[strtolower($ext)]);
    }
    /**
    * auto generate doc.
    * @param mixed $g
    * @return
    */
    private function _updateRes($g){
        igk_set_env(self::KEY_FILES, $g);
    }
    /**
    * Adds res.
    * @param mixed $name
    * @param mixed $uri
    */
    public function add_res($name, $uri){
        $g=$this->getAllPics();
        $g[$name]=$uri;
        if(!isset($g[$name])){
            $g[$name]=$uri;
            return true;
        }
        return false;
    }
    /**
    * Deleteall.
    */
    public function deleteall(){
        if(igk_qr_confirm()){
            $dir=igk_io_baserelativepath(self::TARGETDIR);
            if(!(is_dir($dir) && !IO::RmDir(igk_io_baserelativepath(self::TARGETDIR)))){
                foreach($this->m_fileres as $v){
                    $f=igk_io_currentrelativepath($v);
                    if(igk_io_file_exists($f))
                        unlink($f);
                }
                $this->m_fileres=array();
                $this->_storeData();
                $this->View();
                igk_notifyctrl("picres")->addSuccess(__("Update fire resources"));
            }
        }
        else{
            $frame=igk_frame_add_confirm($this, "delete_all_pics_frame");
            $frame->Form["action"]=$this->getUri("deleteall");
            $frame->Form->Div->Content=__(IGK_MSG_ALLPICS_QUESTION);
        }
    }
    /**
    * Delfile.
    */
    public function delfile(){
        $id=igk_getr("name");
        if(($id == null) || !isset($this->m_fileres[$id]))
            return;
        $f=igk_io_currentrelativepath($this->m_fileres[$id]);
        if(igk_io_file_exists($f)){
            if(unlink($f)){
                unset($this->m_fileres[$id]);
                $this->_storeData();
            }
            else
                $this->msbox->addError("unabled to delete file");
        }
        else{
            unset($this->m_fileres[$id]);
            $this->_storeData();
        }
        $this->View();
    }
    /**
    * Dropdir.
    */
    public function dropdir(){
        $dir=basename(base64_decode(igk_getr("d", null)));
        $dir=igk_io_basedir(self::TARGETDIR. "/".$dir);
        if(is_dir($dir)){
            if(IO::RmDir($dir))
                igk_notifyctrl()->addMsgr("msg.directorydrop");
            else
                igk_notifyctrl()->addErrorr("msg.directorynotdrop");
            $this->View();
        }
        igk_navtocurrent();
    }
    /**
    * Returns All Pics.
    */
    public function getAllPics(){
        return igk_get_env_init("sys://ctrl/allpics", function(){
            $g=array();
            $this->_loadData($g);
            return $g;
        });
    }
    /**
    * Returns Config Page.
    */
    public function getConfigPage(){
        return "pictureresconfig";
    }
    /**
    * Returns Current Page.
    */
    public function getCurrentPage(){
        return $this->getParam("currentPage");
    }
    /**
    * Returns Img Uri.
    * @param mixed $name
    * @param mixed $check
    * @param null|mixed & $path
    */
    public function getImgUri($name, $check=false, & $path=null){
        $res=$this->getPicRes() ?? (function(){
            $t=array();
            $this->_initDefaultPictureRes($t);
            $this->setPicRes($t);
            return $t;
        })();
        $b=igk_getv($res, $name, IGK_STR_EMPTY);
        if($check && empty($b)){
            return $b;
        }
        $s=igk_realpath($b);
        if($s){
            $path = $s;
            return (new IGKHtmlRelativeUriValueAttribute($s))->getValue();
        }
        return igk_html_resolv_img_uri(igk_io_basedir($b));
    }
    /**
    * Returns Name.
    * @return string
    */
    public function getName(): string{
        return IGK_PIC_RES_CTRL;
    }
    /**
    * Returns Pic Res.
    */
    public function getPicRes(){
        return $this->getEnvParam("@PictureRes");
    }
    /**
    * Returns Res Files.
    */
    public function getResFiles(){
        return igk_get_env("sys://resourcefiles", function(){
            $t=array();
            return $t;
        });
    }
    /**
    * Returns Search Key.
    */
    public function getSearchKey(){
        return null;
    }
    /**
    * Gotodefaultview.
    */
    public function gotodefaultview(){
        $this->setCurrentPage(null);
        $this->View();
    }
    /**
    * Initializes Pictures Res.
    * @param mixed $dir
    * @param null|mixed & $tab
    */
    public function initPicturesRes($dir, & $tab=null){
        if(!is_dir($dir))
            return;
        $r=IO::GetPictureFile($dir);
        $g=$tab ?? $this->getAllPics();
        foreach($r as $k){
            $n=igk_io_basenamewithoutext($k);
            if(!isset($g[$n]))
                $g[$n]=$k;
        }
        $this->_updateRes($g);
        $tab=$g;
    }
    /**
    * Initializes Target Node.
    * @return ?\IGK\System\Html\Dom\HtmlNode
    */
    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
        return igk_create_node("div", array("class"=>strtolower($this->Name)));
    }
    /**
    * Loadfile.
    */
    public function loadfile(){
        $notify="picres";
        $id=igk_getr("name");
        $dir=igk_getr("dir");
        $res=[];
        $target=IGK_STR_EMPTY;
        $dest=IGK_STR_EMPTY;
        if(($id == null) || isset($res[$id])){
            igk_notifyctrl()->addError(__("ERR.FILEISNULLORALREADYREGISTERED"));
            igk_navtocurrent();
            return;
        }
        if($dir)
            $target=igk_io_currentrelativepath(self::TARGETDIR."/".$dir);
        else
            $target=igk_io_currentrelativepath(self::TARGETDIR);
        $f=$_FILES["pics"]["tmp_name"];
        $name=$_FILES["pics"]["name"];
        $ext=IO::GetFileExt($name);
        if(!$this->_support(".".$ext)){
            igk_notifyctrl()->addError(igk_const("IGK_ERR_FILE_NOT_SUPPORTED"));
            return;
        }
        if(IO::CreateDir($target)){
            $dest=igk_dir($target."/".$id.".". $ext);
            if(!move_uploaded_file($f, $dest)){
                igk_notifyctrl($notify)->addError("Unable to move uploaded file to ".$dest);
            }
            else{
                $res[$id]=igk_io_basepath($dest);
                if(!$this->_storeData()){
                    igk_notifyctrl($notify)->addError(__("err.cannotstorefile_1", $id));
                    unlink($dest);
                    unset($res[$id]);
                }
                else{
                    igk_notifyctrl($notify)->addMsg(__("MSG.FileUploaded"));
                }
            }
        }
        else{
            $this->msbox->addError(__("err.cannotmoveuploadedfile_1", $dest));
        }
        $this->View();
    }
    /**
    * Loadtempfile.
    * @param mixed $tempfile
    * @param mixed $name
    * @param mixed $id
    * @param mixed $dir
    */
    public function loadtempfile($tempfile, $name, $id, $dir){
        $f=$tempfile;
        $ext=IO::GetFileExt($name);
        if(!$this->_support(".".$ext)){
            igk_notifyctrl()->addError(igk_const("IGK_ERR_FILE_NOT_SUPPORTED"));
            return;
        }
        if(IO::CreateDir($dir)){
            $dest=igk_dir($dir."/".$id.".". $ext);
            if(!move_uploaded_file($f, $dest)){
                igk_notifyctrl()->addError("Unable to move uploaded file to ".$dest);
            }
            else{
                $this->m_fileres[$id]=igk_io_basepath($dest);
                if(!$this->_storeData()){
                    igk_notifyctrl()->addError("Can't store data  to file \"".$id."\"");
                    unlink($dest);
                    unset($this->m_fileres[$id]);
                }
                else{
                    igk_notifyctrl()->addMsg(__("MSG.FileUploaded"));
                }
            }
        }
    }
    /**
    * Notifies.
    * @param null|mixed $c
    * @param null|mixed $t
    */
    public function notify($c=null, $t=null){
        $this->_loadData();
    }
    /**
    * On handle system event.
    * @param mixed $msg
    */
    public function onHandleSystemEvent($msg){
        switch($msg){
            case IGK_ENV_SETTING_CHANGED:
            $this->onPicResChanged(func_get_args(1));
            break;
            case IGK_FORCEVIEW_EVENT:
            $this->notify();
            break;
        }
    }
    /**
    * auto generate doc.
    * @param mixed $ctrl
    * @return
    */
    private function onPicResChanged($ctrl){
        if($ctrl->isChanged(self::PICRES_KEY, $this->m_changeState)){
            $this->_loadData();
        }
    }
    /**
    * Reg picture.
    * @param mixed $name
    * @param mixed $link
    */
    public function regPicture($name, $link){
        $g=$this->getAllPics();
        if($link){
            $g[$name]=$link;
        }
        $this->_updateRes($g);
    }
    /**
    * Removes broken file.
    */
    public function remove_broken_file(){
        $v_ttab=array_keys($this->m_fileres);
        sort($v_ttab);
        $r=false;
        $i=0;
        foreach($v_ttab as $k){
            $v=$this->m_fileres[$k];
            $file=igk_io_currentrelativepath($v);
            if(!igk_io_file_exists($file)){
                unset($this->m_fileres[$k]);
                $r=true;
                $i++;
            }
        }
        if($r){
            $this->_storeData();
            igk_notifyctrl()->addMsgr("msg.brokenfilesremoved_1", $i);
        }
        else{
            igk_notifyctrl()->addInfor("msg.nobrokenfilesremoved");
        }
        $this->View();
    }
    /**
    * Searchentry.
    */
    public function searchentry(){
        $this->m_searchentry=strtolower(igk_getr("q"));
        $this->View();
    }
    /**
    * auto generate doc.
    * @param mixed $page
    * @return
    */
    private function setCurrentPage($page){
        $this->setParam("currentPage", $page);
    }
    /**
    * Setdir.
    */
    public function setdir(){
        $this->m_selectedir=basename(base64_decode(igk_getr("d", null)));
        $this->View();
    }
    /**
    * Sets Pic Res.
    * @param mixed $t
    */
    protected function setPicRes($t){
        $this->setEnvParam("@PictureRes", $t);
    }
    /**
    * Shows loadfile frame.
    */
    public function show_loadfile_frame(){
        $frame=igk_html_frame($this, "load_pic_frame");
        $frame->Title=__("title.loadpictureres");
        $d=$frame->BoxContent;
        $d->clearChilds();
        $frm=$this->_addLoadPicForm($d);
    }
    /**
    * Showentries.
    */
    public function showentries(){
        if(igk_app()->CurrentPageFolder != IGK_CONFIG_MODE){
            return;        }
        $this->setCurrentPage("showentries");
        $div=$this->TargetNode;
        $div=$div->clearChilds()->addPanelBox();
        igk_html_add_title($div, "title.images");
        $div->add(new HtmlSearchNode($this->getUri("searchentry"), $this->m_searchentry));
        $frm=$div->addForm();
        $frm["method"]="POST";
        $frm["action"]=$this->getUri();
        $v_div=$frm->div();
        HtmlUtils::AddBtnLnk($v_div, "btn.Return", $this->getUri("gotodefaultview"));
        HtmlUtils::AddBtnLnk($v_div, "btn.loadfile", $this->getUri("show_loadfile_frame"));
        HtmlUtils::AddBtnLnk($v_div, __("btn.RemoveBrokenfiles"), $this->getUri("remove_broken_file"));
        $info=$frm->div();
        $tab=$frm->addTable();
        $tr=$tab->addTr();
        HtmlUtils::AddToggleAllCheckboxTh($tr);
        $tr->add("th")->Content=__(IGK_FD_NAME);
        $tr->add("th")->Content=__("clLink");
        $tr->add("th")->Content=__("clSize");
        $tr->add("th")->Content=IGK_HTML_WHITESPACE;
        if($res=$this->getResFiles()){
            $v_ttab=array_keys($res);
            $search=$this->getSearchKey();
            sort($v_ttab);
            $v_count=0;
            foreach($v_ttab as $k){
                $v=$res[$k];
                if(empty($v) || !empty($search) && !strstr(strtolower($k), strtolower($search)) && !strstr(strtolower($v), strtolower($search)))
                    continue;
                $tr=$tab->add("tr", array("class"=>"fitw"));
                $tr->addTd()->addInput(IGK_STR_EMPTY, "checkbox");
                $tr->addTd()->Content=$k;
                $tr->addTd()->add("a", array("href"=>igk_js_post_frame($this->getUri("viewpic_ajx&name=".$k))))->Content=igk_dir($v);
                $file=igk_io_currentrelativepath($v);
                if(igk_io_file_exists($file)){
                    $size=@filesize($file);
                    if($size === false){
                        $tr->addtd()->Content="?";
                    }
                    else
                        $tr->addtd()->Content=IO::GetFileSize($size);
                }
                else
                    $tr->addTd()->Content="broken";
                $tr->add("td", array("class"=>"igk-table-img-action_16x16"))->add("a", array("href"=>$this->getUri("delfile&name=".$k)))->add("img", 
                    array("src"=>R::GetImgUri("drop_16x16"))
                );
                $v_count++;
            }
            $info->Content=$v_count;
        }
        $div=$frm->add("div", null, 1000);
        HtmlUtils::AddBtnLnk($div, "btn.Return", $this->getUri("gotodefaultview"));
        HtmlUtils::AddBtnLnk($div, "btn.loadfile", $this->getUri("show_loadfile_frame"));
        HtmlUtils::AddBtnLnk($div, __("btn.RemoveBrokenfiles"), $this->getUri("remove_broken_file"));
    }
    /**
    * Uploadpic ajx.
    */
    public function uploadpic_ajx(){
        $div=igk_create_node("div");
        $rd=$div->addRow();
        $cl=$rd->addCol()->div();
        igk_ajx_panel_dialog("Upload Pictures", $div);
    }
    /**
    * View.
    * @return BaseController
    */
    public function View(): BaseController{
        $div=$this->TargetNode;
        $div->clearChilds();
        if($this->getIsVisible()){
            $this->getConfigNode()->add($div);
            $cpage=$this->getCurrentPage();
            switch($cpage){
                case "showentries":
                $this->showentries();
                break;default: $this->_showdefault();
                break;
            }
        }
        else{
            igk_html_rm($div);
        }
        return $this;
    }
    /**
    * Viewpic.
    * @param null|mixed $name
    */
    public function viewpic($name=null){
        $n=($name == null) ? igk_getr("name", $name): $name;
        $f=igk_io_currentrelativepath(igk_getv($this->m_fileres, $n, IGK_STR_EMPTY));
        header("Content-type: image/png");
        if(igk_io_file_exists($f)){
            igk_wl(IO::ReadAllText($f));
        }
        else{
            igk_wl(IO::ReadAllText(igk_io_currentrelativepath(igk_getv($this->m_fileres, "none"))));
        }
        igk_exit();
    }
    /**
    * Viewpic ajx.
    */
    public function viewpic_ajx(){
        $frame=igk_html_frame($this, "viewpic_frame");
        $frame->Title=__("title.picture_1", igk_getr("name"));
        $c=$frame->BoxContent;
        $c->clearChilds();
        $c->div()->setAttributes(array("class"=>"alignc"))->add("img", array("src"=>$this->getUri("viewpic&name=".igk_getr("name"))));
        $c->div()->Content="image definition";
        $frame->renderAJX();
    }
}