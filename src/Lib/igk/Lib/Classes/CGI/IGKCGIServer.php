<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKCGIServer.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\CGI;
use IGK\System\Html\HtmlReader;

/**
* auto generate doc.
* @package IGK\CGI
*/
class IGKCGIServer
{
    /**
    * Property: sm temp file.
    * @var mixed
    */
    private static $smTempFile;
    /**
    * Callback handler for reg file callback.
    * @var mixed
    */
    private static $sm_regFileCallback;
    /**
    * Property: server info.
    * @var mixed
    */
    private static $sm_serverInfo;
    /**
    * Property: instance.
    * @var mixed
    */
    private static $sm_instance;
    /**
    * .destructor
    * @param mixed $v
    */
    public function __get($v){
        return igk_getv(self::$sm_serverInfo, $v);
    }
    /**
    * .ctr
    * @return
    */
    private function __construct(){
    }
    /**
    * auto generate doc.
    * @param mixed $file
    * @return
    */
    private static function RegFile($file){
        if (!self::$sm_regFileCallback){            
            self::$smTempFile = [];
            self::$sm_regFileCallback = 1;
            register_shutdown_function(function(){
                foreach(self::$smTempFile as $k){
                    @unlink($k);
                } 
            });
        }
        self::$smTempFile[] = $file;
    }
    /**
    * auto generate doc.
    * @return
    */
    private static function getInstance(){
        self::$sm_instance = new IGKCGIServer();
        return self::$sm_instance;
    }
    /**
    * Upload data.
    */
    public static function UploadData(){
        if (self::$sm_instance){
            return self::$sm_instance->UploadedData;
        }
        return null;
    }
    /**
    * Prepares.
    */
    public static function Prepare(){
        parse_str(igk_server()->QUERY_STRING, $_GET);
        $fin=fopen("php://stdin", "r");
        if(!$fin){
            return false;
        }else {
            $_readObj = new \stdClass();
            $ctype = igk_server()->CONTENT_TYPE;
            switch($ctype){
                case "application/json":
                case "application/x-www-form-urlencoded":
                    $size = igk_server()->CONTENT_LENGTH;
                    if ($size > 0){
                        $data = fread($fin, $size);
                        $_readObj->UploadedData = $data;
                    }
                    self::$sm_serverInfo = $_readObj;
                    if (($ctype == "application/x-www-form-urlencoded")&& ($_readObj->UploadedData)){
                        $c = urldecode($_readObj->UploadedData);
                        parse_str($c, $_POST);
                    }
                    return self::getInstance();
            }
            $inf = explode(";", $ctype);
            array_shift($inf);
            $_cattr=  HtmlReader::ReadAttributes(implode(" ", $inf));
            $boundary = igk_getv($_cattr, "boundary"); 
            $_type =null;
            $_attr =null;
            $_dtype = null;
            $h = -1; 
            $key = 0;
            $v = "";
            while($l = fgets($fin)){
                $cl = trim($l);
                if (empty($cl) && ($h==1) ){
                    $h = 0; 
                }
                else {
                    switch($h){
                        case -1:
                         $key = $cl;
                         $h = 1;
                        break;
                        case 1:
                            $inf = explode(";", $l);
                            $def = array_shift($inf);
                            $hinfo = explode(":", $def);
                            $hvalue = trim($hinfo[1]);
                            switch($hinfo[0]){
                                case "Content-Disposition":
                                    $_type = $hvalue;
                                    $_attr=  HtmlReader::ReadAttributes(implode(" ", $inf));
                                    break;
                                case "Content-Type":
                                    $_dtype = $hvalue;
                                    break;
                            }
                        break;
                        case 0:
                            if ($_attr){
                                $n = $_attr["name"];
                                if (array_key_exists("filename", $_attr)){
                                    $error = 0;
                                    $cf = null;
                                    if (strpos(trim($cl), $key) !== 0){                                        
                                        $cf = igk_io_sys_tempnam("cgi");
                                        $wfile = fopen($cf,"w+");
                                        if (!$wfile) 
                                            $error = 1; 
                                        else 
                                            fwrite($wfile, $l);
                                        self::RegFile($cf);
                                        while($tl = fgets($fin)){
                                            if (strpos(trim($tl), $key) === 0){
                                                break;
                                            } 
                                            if ($wfile)
                                                fwrite($wfile, $tl);
                                        }
                                        if ($wfile)
                                            fclose($wfile);
                                    } else{
                                        $error = -2;
                                    }
                                    $finfo = [
                                        "name"=>$_attr["filename"],
                                        "tmp_name"=>$cf,
                                        "type"=>$_dtype,
                                        "size"=>$error?-1: filesize($cf),
                                        "error"=>$error
                                    ];
                                    $_FILES[$n]=$finfo;
                                    $_type = null;
                                    $_attr = null;
                                    $_dtype = null;
                                    $v = "";
                                    $h = 1;
                                }
                                else {
                                    if (strpos($cl, $key) === 0){ 
                                        $_POST[$n] = $v;
                                        $_type = null;
                                        $_attr = null;
                                        $_dtype = null;
                                        $v = "";
                                        $h = 1;
                                    }
                                    else {
                                        $v .= $l;
                                    }
                                }
                            }
                        break;
                    }
                }
            }
            fseek($fin, 0, SEEK_SET);
            return 1;
        }
        return false;
    }
}