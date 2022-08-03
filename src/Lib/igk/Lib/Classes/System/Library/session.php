<?php
// @author: C.A.D. BONDJE DOUE
// @filename: session.php
// @date: 20220803 13:48:55
// @desc: 

// @file: session.php
namespace IGK\System\Library;

use IGK\Helper\IO;
use IGK\System\IO\Path;
use IGK\System\Http\RequestHeader;
use IGKEvents;
use IGKException;
use IGKSessionFileSaveHandler;

///<summary>session library</summary>
/**
 * 
 * @package IGK\System\Library
 */
class session extends \IGKLibraryBase{
    public function init():bool{
        // initialize function
        require_once IGK_LIB_CLASSES_DIR ."/IGKSessionFileSaveHandler.php";
        require_once __DIR__."/Session/.functions.pinc";  
        igk_reg_hook(IGKEvents::HOOK_BEFORE_INIT_APP, function(){
            $this->start(); 
        }); 
        // register_shutdown_function(function(){
            // $header = new RequestHeader(); 
            // if (($id = session_id()) && ($header->PRAGMA == 'no-cache')){
            //     if (session_status()==1){
            //         //restart the session before destroy becose it's closed
            //         session_start();
            //     }                
            //     @session_destroy();
            // }
            // echo ("shut down ...... ". $header("CONNECTION"));
        // });
        return true;
    }
    /**
     * core start session
     * @return void|bool 
     * @throws IGKException 
     */
    public function start($reset=0){
        $ie_diagnonstic=igk_server()->HTTP_REFERER == "diagnostics://5/";
        $idstorage=
        $cookieName=null;
        if($ie_diagnonstic){
            igk_exit();
        }
        $handle_func=igk_get_env("sys://handle/file_request");
        if($handle_func){
            foreach($handle_func as $v){
                if($v()){
                    return;            }
            }
        }
        if(defined("IGK_NO_SESSION"))
            return;
        if($reset){
            igk_env_count_reset(__FUNCTION__);
        }
        $f=igk_env_count(__FUNCTION__);
        $is_no_start=empty(session_id());
        if(($f > 1) || (!$is_no_start)){
            return;    }
        $cookieName=session_name();
        if(defined('IGK_SESS_DIR') && IO::CreateDir(IGK_SESS_DIR)){
            ini_set("session.save_path", IGK_SESS_DIR);
            IGKSessionFileSaveHandler::Init();
        }
        ini_set("session.cookie_same", "Strict");
        //+ | security fix
        ini_set("session.cookie_secure", igk_sys_srv_is_secure());
        ini_set("session.cookie_httponly", 1);
        ini_set("session.cookie_samesite", "strict");
        //+ $idstorage= trim(isset($_COOKIE) && isset($_COOKIE[$cookieName]) ? $_COOKIE[$cookieName]: trim(igk_getr($cookieName)));
    //+ check if the session is passed prio to cookie value.
    //+ 2020 Edge and chrome on Mac no need for session_id https://www.php.net/manual/en/function.session-id.php
    
        $idstorage=trim(isset($_COOKIE) && isset($_COOKIE[$cookieName]) ? false: trim(igk_getr($cookieName, igk_getv($_SERVER, $cookieName))));
        if($idstorage){
            igk_bind_session_id($idstorage);
            if(!isset($_COOKIE[$cookieName])){
                setcookie($cookieName, $idstorage);
            }
        }
        $dom=igk_get_cookie_domain();       
        $opts=[];
        if(!empty($dom))
            $opts["domain"]=$dom;
        if(count($opts) > 0){
            if(version_compare(PHP_VERSION, IGK_PHP_MIN_VERSION, ">=")){
                if(!session_set_cookie_params($opts)){
                    igk_ilog("set cookie options failed");
                    igk_ilog($opts);
                }
            }
            else{
                session_set_cookie_params(10, "/", $opts["domain"], igk_sys_srv_is_secure(), true);
            }
        }
        $b=session_start();
        return $b;
    }
    /**
     * restart session with new id
     * @param mixed $id 
     * @return bool 
     */
    public function restart($id){
        session_id($id);
        return session_start();
    }
    /**
     * close the started session
     * @return void 
     */
    public function close(){
        @session_write_close();
    }
    /**
     * destroy session
     * @return void 
     */
    public function destroy(){ 
        $sess_id = session_name();
        igk_session_destroy();
        // + | -----------------------------------------------------------
        // + | clear user session cookies
        // + |
        setcookie(igk_sys_domain_name()."/uid", null);      
 
        if ($sess_id){
            setcookie($sess_id, null);
            unset($_COOKIE[$sess_id]);
        }
        $this->close(); 
        $_SESSION=array();  
    }
    /**
     * unlink session file
     * @param mixed $id 
     * @return bool 
     */
    public function unlink($id){
        $d = ini_get("session.save_path");
        $f = igk_io_dir($d . "/" . IGK_SESSION_FILE_PREFIX . $id);
        if (file_exists($f)) {
            return unlink($f);
        }
        return false;
    }
}