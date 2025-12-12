<?php
// @file: IGKSessionFileSaveHandler.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\Helper\IO;
/**
 * session file handler 
 * @package 
 */
class IGKSessionFileSaveHandler{
    var $savePath, $sessName;
    protected function __construct(){    }
    private function _getFile($id){
        return igk_uri(implode(DIRECTORY_SEPARATOR, [$this->savePath, IGK_SESSION_FILE_PREFIX.$id]));
    }
    public function close(){
        return true;
    }
    public function destroy($id){
        if($f=$this->_getFile($id)){
            @unlink($f);
        }
        return true;
    }
    public function gc($maxlifetime){
        foreach(glob($this->savePath.DIRECTORY_SEPARATOR.IGK_SESSION_FILE_PREFIX."*") as $v){
            if(filemtime($v) + $maxlifetime < time() && file_exists($v)){
                @unlink($v);
            }
        }
    }
    public static function Init(){
        if(!defined("IGK_SESS_DIR")){
            return;
        }
        $handler = new self();
        if (version_compare(PHP_VERSION, '8.4', '>=')) {
            session_set_save_handler($handler->getSessionHandlers()); 
        } else {
            session_set_save_handler([$handler, "open"], [$handler, "close"], array($handler, 'read'), array($handler, 'write'), array($handler, 'destroy'), array($handler, 'gc'));
        }
        register_shutdown_function('igk_sess_write_close');
    }
    /**
     * new in session handle to just register a single object 
     * @return SessionHandlerInterface 
     */
    public function getSessionHandlers(): SessionHandlerInterface {

        $handler = new class implements SessionHandlerInterface{
            var $host;
            function close(): bool {
                return $this->host->close();
            }
            function destroy(string $id): bool {
                return $this->host->destroy($id);
            }
            function gc(int $maxlifetime): int{
                return $this->host->gc($maxlifetime);
            }
            function open(string $save_path, string $session_name): bool {
                return $this->host->open($save_path, $session_name);
            }
            function read(string $id): string {
                return $this->host->read($id);
            }
            function write(string $id, string $data): bool {
                return $this->host->write($id, $data);
            }          
        };
        $handler->host = $this;
        return $handler;
       
    }
     /**
     * @param string $savepath 
     * @param string $sessname 
     * @return bool 
     */
    public function open($savepath, $sessname){
        if(defined("IGK_SESS_DIR")){
            $savepath=IGK_SESS_DIR;
        }
        $this->savePath=$savepath;
        $this->sessName=$sessname;
        return IO::CreateDir($this->savePath);
    }
    public function read($id){
        if(file_exists($f=$this->_getFile($id))){
            return file_get_contents($f);
        }
        return (string)null;
    }
    /**
     * @param string $id id of the session 
     * @param mixed $data mixed data to write
     */
    public function write($id, $data){
        $f=$this->_getFile($id);
        return igk_io_w2file($f, $data);
    }
}