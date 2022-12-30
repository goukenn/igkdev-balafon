<?php
// @file: IGKSessionFileSaveHandler.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Helper\IO;

class IGKSessionFileSaveHandler{
    var $savePath, $sessName;
    ///<summary>.ctr</summary>
    protected function __construct(){    }
    ///<summary></summary>
    ///<param name="id"></param>
    private function _getFile($id){
        return igk_uri(implode(DIRECTORY_SEPARATOR, [$this->savePath, IGK_SESSION_FILE_PREFIX.$id]));
    }
    ///<summary></summary>
    public function close(){
        return true;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    public function destroy($id){
        if(file_exists($f=$this->_getFile($id))){
            unlink($f);
        }
        return true;
    }
    ///<summary></summary>
    ///<param name="maxlifetime"></param>
    public function gc($maxlifetime){
        foreach(glob($this->savePath.DIRECTORY_SEPARATOR.IGK_SESSION_FILE_PREFIX."*") as $v){
            if(filemtime($v) + $maxlifetime < time() && file_exists($v)){
                unlink($v);
            }
        }
    }
    ///<summary></summary>
    public static function Init(){
        if(!defined("IGK_SESS_DIR")){
            return;
        }
        $handler = new self();
        session_set_save_handler([$handler, "open"], [$handler, "close"], array($handler, 'read'), array($handler, 'write'), array($handler, 'destroy'), array($handler, 'gc'));
        register_shutdown_function('igk_sess_write_close');
    }
    ///<summary></summary>
    ///<param name="savepath"></param>
    ///<param name="sessname"></param>
    public function open($savepath, $sessname){
        if(defined("IGK_SESS_DIR")){
            $savepath=IGK_SESS_DIR;
        }
        $this->savePath=$savepath;
        $this->sessName=$sessname;
        return IO::CreateDir($this->savePath);
    }
    ///<summary></summary>
    ///<param name="id"></param>
    public function read($id){
        if(file_exists($f=$this->_getFile($id))){
            return file_get_contents($f);
        }
        return (string)null;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<param name="data"></param>
    public function write($id, $data){
        $f=$this->_getFile($id);
        // igk_ilog("write _session ".$id. " : ".igk_io_request_uri());
        return igk_io_w2file($f, $data);
    }
}
