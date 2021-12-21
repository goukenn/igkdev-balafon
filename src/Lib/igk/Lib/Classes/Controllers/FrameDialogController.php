<?php
// @file: IGKFrameDialogCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;

use IGK\System\Html\Dom\HtmlDialogFrameNode;
use IIGKFrameController;

final class FrameDialogController extends NonVisibleControllerBase implements IIGKFrameController{
    const FRAME_KEYS="FRAMES";
    ///<summary></summary>
    public function __construct(){
        parent::__construct();
    }
    ///<summary></summary>
    public function close_frame_ajx(){
        $href=base64_decode(igk_getr("href"));
        $tag=igk_getquery_args($href);
        $this->closeFrame(igk_getv($tag, "id"));
        igk_wl(igk_app()->Doc->body->render());
        igk_exit();
    }
    ///<summary></summary>
    public function closeAllFrame(){
        $frame=$this->getFrames();
        $c=array_keys($frame);
        $i=0;
        foreach($c as $v){
            igk_frame_close($v);
            $i++;
        }
        igk_navtocurrent();
    }
    ///<summary>Close frame</summary>
    ///<param name="id" default='null'>id of frame to close on server side</param>
    ///<param name="navigate" default='null'>navigation uri</param>
    public function closeFrame($id=null, $navigate=null){
        $v_id=($id != null) ? $id: igk_getr("id", 0);
        $closeuri=null;
        $navigate=$navigate === null ? igk_getr("navigate", false): $navigate;
        $frames=$this->getFrames();
        if($frames){
            if(isset($frames[$v_id]) && ($frame=$frames[$v_id])){
                $args=igk_getquery_args($frame->closeUri);
                if(($closeuri=urldecode(igk_getr("closeuri"))) == null)
                    $closeuri=urldecode(igk_getv($args, "closeuri"));
                $frame->remove();

                $method = $frame->getcloseMethodUri();
                
                if(method_exists(get_class($frame->Owner), "frameClosed")){
                    $frame->Owner->frameClosed();
                }
                $frame->closeMethod();
                $frame->Dispose();
                unset($frames[$v_id]);
                unset($frame);
                $this->setParam(self::FRAME_KEYS, (count($frames) > 0) ? $frames: null);
            }
            else{
                igk_wln("Frame not found [".$v_id."] - ".count($frames));
                return;
            }
        }
        if(!igk_is_ajx_demand()){
            if($closeuri){
                igk_navtocurrent($closeuri);
                igk_exit();
            }
            else if($navigate){
                igk_navtocurrent();
                igk_exit();
            }
        }
    }
    ///<summary></summary>
    public function closeFrame_ajx(){
        $id=igk_getr("id");
        igk_frame_close($id);
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="frame"></param>
    ///<param name="remove" default="true"></param>
    public function ContainFrame($id, $frame, $remove=true){
        $frames=$this->getFrames();
        if(isset($frames[$id])){
            if($frame !== $frames[$id]){
                if($remove){
                    unset($frames[$id]);
                    $this->setParam(self::FRAME_KEYS, $frames);
                    return true;
                }
                return false;
            }
            return true;
        }
        else{        }
        return false;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="owner"></param>
    ///<param name="closeuri" default="null"></param>
    ///<param name="reloadcallback" default="null"></param>
    public function createFrame($id, $owner, $closeuri=null, $reloadcallback=null){
 
        if(($id == null) || !is_string($id))
            return null;
        $frames=$this->getFrames();
        if(!$frames == null){
            $frames=array();
        }
        if(isset($frames[$id])){
            $v_dial=$frames[$id];
            $b=$v_dial->getOwner();;
            if($b === $owner)
                return $v_dial;
        }
        $v_dial=new HtmlDialogFrameNode($this, $id, $owner, $reloadcallback);
        $v_dial->clearChilds();
        $cluri=null;
        if($closeuri){
            $cluri="&closeuri=".urlencode($closeuri);
        }
        else{
            $cluri="&navigate=1";
        }
        $v_dial->setCloseUri($this->getUri("closeFrame&id=".$id.$cluri));
        $v_dial["id"]=$id;
        $frames[$id]=$v_dial;
        $this->setParam(self::FRAME_KEYS, $frames);
        return $v_dial;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    public function getFrame($id){
        $frames=$this->getFrames();
        if(isset($frames[$id])){
            return $frames[$id];
        }
        return null;
    }
    ///<summary></summary>
    public function getFrameIds(){
        if($frames=$this->getFrames()){
            return array_keys($frames);
        }
        return array();
    }
    ///<summary></summary>
    public function getFrames(){
        return $this->getParam(self::FRAME_KEYS);
    }
    ///<summary></summary>
    public function getName(){
        return IGK_FRAME_CTRL;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    public function IsFrameAvailable($id){
        return $this->getFrame($id) != null;
    }
}
