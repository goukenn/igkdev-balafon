<?php
// @file: IGKSystemController.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;

use IGK\Resources\R;
use IGK\System\Configuration\Controllers\UsersConfigurationController;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Http\JsonResponse;
use IGKResourceUriResolver;

/**
 * represent system controller
 * @package IGK\Controllers
 */
final class SystemController extends NonVisibleControllerBase{
    public function getAppUri(?string $path=null):?string{
        empty($path) && igk_die("null path not allowed");
        $uri = igk_register_temp_uri(static::class);
        return implode("/", [$uri, $path]);        
    }
    public function logout(){
     
        UsersConfigurationController::ctrl()->logout();   
        if ($sess = igk_app()->getApplication()->getLibrary()->session){
            $sess->destroy();            
        }
        igk_clearall_cookie();
        $redirect = urldecode(igk_getr("redirect_uri", "/")); 
        igk_navto($redirect);
    }
    ///<summary></summary>
    public function __construct(){
        parent::__construct();
    }
    ///<summary></summary>
    ///<param name="frm"></param>
    private function _buildForm($frm){
        $this->m_fontList=$this->_getFontList();
        igk_notifyctrl()->setNotifyHost($frm->div());
        $frm->div()->Content="Font list ";
        $frm->ClearChilds();
        $frm["action"]=$this->getUri("installfont_ajx");
        $div=$frm->div();
        $div["style"]="min-height: 300px;max-height: 400px; min-width: 400px; overflow-x:none; overflow-y:auto;";
        $i=0;
        if($this->m_fontList != null){
            foreach($this->m_fontList->fonts as $k=>$v){
                $f=igk_io_dir($this->getFontDir()."/".basename($v));
                $uri=$this->getUri("installfont_ajx");
                $cdiv=$div->div()->setAttributes(array(
                    "style"=>"font-family: '".$k."';",
                    "class"=>"igk-list-item",
                    "igk-js-click"=>IGK_STR_EMPTY,
                    "id"=>"font_".$i,
                    "igk-font-name"=>base64_encode($k),
                    "onclick"=>"javascript:window.igk.system.fonts.installFont(this, '{$uri}'); return false;"
                ));
                if(file_exists($f))
                    $cdiv["style"] .= "color:#9A9A9A;";
                else
                    $cdiv["style"] .= "color:#3A3A3A;";
                $cdiv->setContent($k);
                $i++;
            }
        }
        else{
            $div->addNotifyBox("danger")->Content="/!\ No fonts definition found";
        }
    }
    ///<summary></summary>
    private function _getFontList(){
        $file=igk_sys_cgi_folder()."/cscgi/fontlist.cgi";
        if(file_exists($file)){
            $count=0;
            $resolver=IGKResourceUriResolver::getInstance();
            $resolver->fulluri=1;
            $uri=$resolver->resolve($file);
            $source=igk_curl_post_uri($uri, null, array("CONNECTTIMEOUT"=>10, "TIMEOUT"=>10));
            if(igk_curl_status() != 200){
                return null;
            }
            $node=HtmlReader::Load($source, null);
            if($node){
                $tab=array();
                foreach($node->getElementsByTagName("item") as $k){
                    $tab[$k["name"]]=$k->getinnerHtml();
                }
                return (object)array("fonts"=>$tab, "count"=>count($tab));
            }
            else{
                igk_notifyctrl("sys://Config")->addWarning("<div style=\"color:#feaacc;\">Error : ".$source. " uri: ".$uri. " can't load cgi </div>");
            }
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="lang" default="null"></param>
    public function changeLang_ajx($lang=null){
        $doc=igk_get_last_rendered_document();
        if($doc !== null){
            $old = igk_app()->session->lang;
            R::ChangeLang($lang);
            $u=igk_sys_srv_referer();
            $new = igk_app()->session->lang;
            if($u){         
                $u=igk_getv(explode("?", $u), 0);
                if(!igk_io_invoke_uri($u, 0)){
                    igk_ilog_assert(!igk_sys_env_production(), "Failed to invoke uri - ".$u);
                }  
                HtmlRenderer::RenderDocument($doc, 1, null);
            } else {
                igk_do_response(new JsonResponse([
                    "old"=>$old,
                    "new"=>$new,
                    "success"=>$new != $old
                ]));
                igk_exit();
            }
        }
        else{
            igk_ilog("last rendered is null". igk_app()->settings->CurrentDocumentIndex);
            igk_navto(igk_io_baseuri());
        }
        igk_exit();
    }
    ///<summary></summary>
    ///<param name="name"></param>
    public function changeTheme($name){
        $s=igk_sys_srv_referer();
        if(empty($s)){
            igk_navto(igk_io_baseuri());
        }
        igk_configs()->globaltheme=$name;
        igk_save_config(); 
        igk_css_render_balafon_style(igk_app()->getDoc());
        igk_exit();
    }
    ///<summary></summary>
    public function getFontDir(){
        return igk_io_syspath(IGK_RES_FONTS);
    }
    ///<summary></summary>
    public function getFontList(){
        static $fontlist=null;
        if($fontlist === null){
            if(igk_getconfigwebpagectrl()->getIsConnected()){
                $fontlist=$this->_getFontList();
            }
            else
                $fontlist=null;
        }
        return $fontlist;
    }
    ///<summary></summary>
    public function getName(){
        return IGK_SYS_CTRL;
    }
    ///<summary></summary>
    protected function initComplete($context=null){
        parent::initComplete();
    }
    ///<summary></summary>
    ///<param name="name" default="null"></param>
    public function installfont($name=null){
        $n=($name == null) ? base64_decode(igk_getr("n")): $name;
        if($this->m_fontList && isset($this->m_fontList->fonts[$n])){
            $file=$this->m_fontList->fonts[$n];
            $target=igk_io_currentrelativepath(IGK_RES_FOLDER."/Fonts/".basename($file));
            copy($file, $target);
            igk_app()->getDoc()->Theme->addFont($n, igk_io_dir(igk_io_basepath($target)));
            igk_notifyctrl()->addMsgr("msg.fontinstalled");
            return true;
        }
        return false;
    }
    ///<summary></summary>
    public function installfont_ajx(){
        if(igk_parsebool($this->installfont())){
            $node=$this->getParam("sys:viewnode");
            $item=$this->getParam("sys:binding");
            $frm=$item["form"];
            $ctrl=$item["ctrl"];
            if($ctrl){
                $ctrl->View();
                igk_js_ajx_view_ctrl($ctrl);
            }
            $this->_buildForm($frm);
            $frm->renderAJX();
        }
    }
    ///<summary>Represente IsFunctionExposed function</summary>
    ///<param name="n"></param>
    public function IsFunctionExposed($n){
        return true;
    }
    ///<summary></summary>
    public function mod_rewrite(){
        if(igk_getv($_SERVER, 'IGK_REWRITE_MOD') || (igk_server()->REDIRECT_URL && (igk_getr('rwc') > 0))){
            igk_wl(1);
            igk_exit();
        }
        igk_wl(0);
        igk_exit();
    }
    ///<summary></summary>
    public function update(){    }
    ///<summary></summary>
    public function upload(){
        igk_wln_e("upload file ");
    }
    ///<summary></summary>
    public function viewFontList(){
        $r=$this->_getFontList();
        igk_wl($r);
    }
    ///<summary></summary>
    ///<param name="node"></param>
    ///<param name="ctrl" default="null"></param>
    public function viewInstallFontForm($node, $ctrl=null){
        $frm=$node->addForm();
        $this->setParam("sys:binding", array("form"=>$frm, "ctrl"=>$ctrl));
        $this->_buildForm($frm);
    }
}
