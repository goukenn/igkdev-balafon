<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKSubDomainManager.php
// @date: 20220803 13:48:54
// @desc: 


///<summary>subdomain manager</summary>

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Helper\IO;
use IGK\Models\Subdomains;
use IGK\System\Database\MySQL\Controllers\DbConfigController;

/**
* subdomain manager
*/
final class IGKSubDomainManager extends IGKObject{
    private static $sm_instance;
    private static $sm_isSubDomain;
    private static $sm_subDomainName;
    private static $sm_cached_domains;
    ///<summary></summary>
    /**
    * 
    */
    private function __construct(){}
    ///<summary></summary>
    ///<param name="domain"></param>
    ///<param name="servername"></param>
    /**
    * 
    * @param mixed $domain
    * @param mixed $servername
    */
    public static function AcceptDomain($domain, $servername){
        if(IGKValidator::IsIpAddress($domain) && IGKValidator::IsIpAddress($servername)){
            return false;
        }
        $x1=igk_io_path_ext($domain);
        $x2=igk_io_path_ext($servername);
        if($x1 == $x2){
            $x1=igk_io_basenamewithoutext($domain);
            $x2=igk_io_basenamewithoutext($servername);
            if(preg_match("/\.".$x1."$/i", $x2)){
                return true;
            }
        }
        return false;
    }
    private static function GetCacheFile(){
        return igk_io_cachedir()."/.domains.cache";
    }
    ///<summary>get the domain controller or return false</summary>
    /**
    * get the domain controller or return false
    */
    public function checkDomain($uri=null, & $row=null){
        if(igk_is_atomic()){
            return false;
        }
        $subdomain= IGKSubDomainManager::SubDomainUriName($uri);
        $cache_file = self::GetCacheFile();
        if (is_null(self::$sm_cached_domains)){
            self::$sm_cached_domains = [];
            if (is_file($cache_file)){
                if ((self::$sm_cached_domains = unserialize(file_get_contents($cache_file))) ===false){
                    self::$sm_cached_domains = [];
                }
            }
        }
        
        $t = array_merge(self::$sm_cached_domains, $this->getRegList()?? []);  
         
        $cl = SysDbController::resolveClass(Subdomains::class); 
    
        if(!empty($subdomain)){
            $s=$subdomain;
            if (is_callable($t)){
                igk_die("Rgister list is a callable");
            }
            if(isset($t[$s])){
                $c =$t[$s];
                $row = (object)$c;
                return igk_getctrl($row->clCtrl, false);
            } 
            // $v_start = igk_sys_request_time();
            // $q = "SELECT * FROM `tbigk_subdomains` WHERE `clName`='tonerafrika'";
            // $c = mysqli_connect("mysql", "root", "rootbonaje", "igkdev.dev");
            // if ($rep = mysqli_query($c, $q)){
            //     while($p = mysqli_fetch_assoc($rep)){
            //         igk_wln("\n", $p);
            //     }
            // }
            // mysqli_close($c);
            // $v_duration = igk_sys_request_time() - $v_start;
            // igk_wln("duration ", $v_duration, $t);

            // $v_start = igk_sys_request_time();
            // $g = Subdomains::select_row([
            //     "clName"=>$subdomain
            // ]);
            // $v_duration = igk_sys_request_time() - $v_start;
            // igk_wln( __FILE__.":".__LINE__, "\n\n vs one", $v_duration, $g);
            // igk_exit();

            // $v_start = igk_sys_request_time();
            // $g = Subdomains::select_row([
            //     "clName"=>$subdomain
            // ]);
            // $v_duration = igk_sys_request_time() - $v_start;
            // igk_wln_e( __FILE__.":".__LINE__, "\n\nvs", $v_duration, $g);

            if ($raw = Subdomains::select_row([
                "clName"=>$subdomain
            ])){
                // $v_duration = igk_sys_request_time() - $v_start;
                // igk_wln_e("duration ", $v_duration, $t);

                if ($ctrl = igk_getctrl($raw->clCtrl, false)){
                    $row = $raw;
                    $this->reg_domain($subdomain, $raw->clCtrl, $raw);
                    self::$sm_cached_domains[$subdomain] = $raw->to_array();
                    igk_io_w2file($cache_file, serialize(self::$sm_cached_domains));
                    return $ctrl;
                }
            }  
        }
        return false;
    }

    /**
     * Get Subdomain controller
     * @return BaseController|void 
     * @throws IGKException 
     */
    public static function GetSubDomainCtrl(){
        if (self::$sm_isSubDomain){
            $subdomain = self::$sm_subDomainName;
            if (isset(self::$sm_cached_domains[$subdomain])){
                $rt = (object)self::$sm_cached_domains[$subdomain];
                if ($ctrl = igk_getctrl($rt->clCtrl, false)){
                    return $ctrl;
                } 
            }

            if ($raw = Subdomains::select_row([
                "clName"=>$subdomain
            ])){
                if ($ctrl = igk_getctrl($raw->clCtrl, false)){
                    return $ctrl;
                }
            }
        }
    }
    ///<summary></summary>
    /**
    * 
    */
    public function Clear(){
        igk_environment()->{IGK_ENV_SESS_DOM_LIST} = null; 
    }
    ///<summary></summary>
    /**
    * 
    */
    public function domainList(){
        if (is_array($t=$this->getRegList()))
            return array_keys($t);
        return null;
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function GetBaseDomain(){
        $srv = igk_server()->SERVER_NAME;
        // + | auto dectect base domain
        if ($srv == "localhost"){
            return $srv;
        }
        if (preg_match("/(\.)?localhost$/", $srv)){
            return "localhost";
        }
        $tab = explode(".", $srv);
        if ( (($c = count($tab))>2) && !IGKValidator::IsIpAddress($srv)){
            return implode(".", array_slice($tab, $c - 2));
        }
        return  $srv; 
    }

    ///<summary></summary>
    /**
    * 
    */
    public static function getInstance(){
        if(self::$sm_instance == null){
            $k=new IGKSubDomainManager();
            self::$sm_instance=$k;
        }
        return self::$sm_instance;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getRegList(){
        if (!($c = igk_environment()->get(IGK_ENV_SESS_DOM_LIST)))
            $c = [];
        return $c;
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function GetSubDomain(){
        $srv=igk_server_name();
        if(preg_match("/^(www\.)/i", $srv)){
            $srv=substr($srv, 4);
        }
        if(!empty($srv)){
            $d=self::GetBaseDomain(); 
            if(preg_match("/(\.".$d."$)/i", $srv)){
                $srv=substr($srv, 0, strlen($srv) - strlen($d)-1);
            }
            else
                $srv="";
        }
        return $srv;
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function GetSubDomainName(){
        return self::$sm_subDomainName;
    }
    ///<summary>init domain server</summary>
    /**
    * init domain server
    */
    public static function Init(){
        self::$sm_isSubDomain=false;
        self::$sm_subDomainName=false;
        $srv=igk_sys_srv_domain_name();
        $domain=self::GetBaseDomain();
        $rdomain=null;
        $ip_server = IGKValidator::IsIPAddress($srv);
        if(($srv !== "localhost") && !$ip_server && ($srv !== $domain) && (preg_match("/(www)?\.".$domain."$/i", $srv) || self::AcceptDomain($domain, $srv))){
            $rdomain=defined("IGK_COOKIE_DOMAIN") ? igk_const("IGK_COOKIE_DOMAIN") : self::Resolv($domain);
            ini_set("session.cookie_domain", ".".$rdomain);
            self::$sm_isSubDomain=true;
            self::$sm_subDomainName=self::GetSubDomain();
        }
        else{
            $rdomain=$srv;
            $_path="/";
            if(!$ip_server){
                if(igk_server_request_onlocal_server())
                    $srv="localhost";
                else
                    $srv=".".$srv;
            }
            if(!empty($bdir=igk_io_rootbasedir())){
                $_path=$bdir;
            }
            ini_set("session.cookie_domain", ($srv == "localhost") ? null: $srv);
            ini_set("session.cookie_path", $_path);
        }
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="ctrl"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $ctrl
    */
    public static function IsControl($n, $ctrl){
        $t=self::getInstance()->getRegList();
        return isset($t[$n]) && ($t[$n]->ctrl === $ctrl);
    }
    ///<summary></summary>
    /**
    * 
    */
    public static function IsSubDomain(){
        return self::$sm_isSubDomain;
    }
    ///<summary></summary>
    /**
    * 
    */
    protected function onDomainChanged(){
        $this->Clear();
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="ctrl"></param>
    ///<param name="row" default="null"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $ctrl
    * @param mixed $row the default value is null
    */
    public function reg_domain($n, $ctrl, $row=null){
        if(empty($n) || !igk_reflection_class_implement($ctrl, IIGKUriActionRegistrableController::class)){
            return false;
        } 
        $t=$this->getRegList();
        if(!isset($t[$n])){
            $t[$n]=(object)array("ctrl"=>$ctrl, "row"=>$row);
            $this->updateRegList($t);
            return true;
        }
        return false;
    }
    ///<summary>resole domain to match server name</summary>
    /**
    * resole domain to match server name
    */
    public static function Resolv($domain){
        $servername=igk_server_name();
        $ex1=igk_io_path_ext($domain);
        $ex2=igk_io_path_ext($servername);
        if($ex1 == $ex2)
            return $domain;
        $x1=igk_io_basenamewithoutext($domain);
        $x2=igk_io_basenamewithoutext($servername);
        if(preg_match("/\.".$x1."$/i", $x2))
            return $x1.".".$ex2;
        return $domain;
    }
    
    
    ///<summary></summary>
    ///<param name="t"></param>
    /**
    * 
    * @param mixed $t
    */
    private function updateRegList($t){
        igk_environment()->{IGK_ENV_SESS_DOM_LIST} = $t; 
    }

    public static function SubDomainUriName(?string $uri=null){
        $domain=igk_io_domain_uri_name($uri);
        $bdom=self::GetBaseDomain();
        $s="";
        if(($domain === $bdom) || IGKValidator::IsIpAddress($domain) || ($domain == "localhost")){
            return $s;
        }
        // remove port
        $domain = explode(":", $domain)[0];
        //
        if (($pos =  strrpos($domain, $bdom))!==false){
            $s = rtrim(substr($domain,0, $pos ), ".");
            return $s;
        }
        $tab=array();
        if(preg_match_all(IGK_SUBDOMAIN_URI_NAME_REGEX, trim($domain), $tab))
            $s=igk_getv($tab["name"], 0);
        return $s;
    }
    public static function DomainUriName($uri=null){
        $domain=$uri == null ? igk_io_baseuri(): $uri;
        $domain=preg_replace_callback("#((http(s)?://)?(www\.)?){0,1}#i", function($tmatch){
            return "";
        }
        , $domain);
        return igk_getv(explode("/", $domain), 0);
    }
}