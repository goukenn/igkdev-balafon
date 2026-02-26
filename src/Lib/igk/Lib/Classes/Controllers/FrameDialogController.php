<?php
// @file: IGKFrameDialogCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Controllers;
use IGK\System\Html\Dom\HtmlDialogFrameNode;
use IGK\IFrameController;

/**
* auto generate doc.
* @package IGK\Controllers
*/
final class FrameDialogController extends NonVisibleControllerBase implements IFrameController{
    const FRAME_KEYS="FRAMES";
    public function __construct(){
        parent::__construct();
    }

    /**
    * auto generate doc.
    */
    public function close_frame_ajx(){
        $href=base64_decode(igk_getr("href"));
        $tag=igk_getquery_args($href);
        $this->closeFrame(igk_getv($tag, "id"));
        igk_wl(igk_app()->Doc->body->render());
        igk_exit();
    }

    /**
    * auto generate doc.
    */
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

    /**
    * auto generate doc.
    * @param null|mixed $id
    * @param null|mixed $navigate
    */
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
                igk_navto($closeuri);
                igk_exit();
            }
            else if($navigate){
                igk_navtocurrent();
                igk_exit();
            }
        }
    }

    /**
    * auto generate doc.
    */
    public function closeFrame_ajx(){
        $id=igk_getr("id");
        igk_frame_close($id);
    }

    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $frame
    * @param mixed $remove
    */
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

    /**
    * auto generate doc.
    * @param mixed $id
    * @param mixed $owner
    * @param null|mixed $closeuri
    * @param null|mixed $reloadcallback
    */
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

    /**
    * auto generate doc.
    * @param mixed $id
    */
    public function getFrame($id){
        $frames=$this->getFrames();
        if(isset($frames[$id])){
            return $frames[$id];
        }
        return null;
    }

    /**
    * auto generate doc.
    */
    public function getFrameIds(){
        if($frames=$this->getFrames()){
            return array_keys($frames);
        }
        return array();
    }

    /**
    * auto generate doc.
    */
    public function getFrames(){
        return $this->getParam(self::FRAME_KEYS);
    }

    /**
    * auto generate doc.
    * @return string
    */
    public function getName(): string{
        return IGK_FRAME_CTRL;
    }

    /**
    * auto generate doc.
    * @param mixed $id
    */
    public function IsFrameAvailable($id){
        return $this->getFrame($id) != null;
    }
}