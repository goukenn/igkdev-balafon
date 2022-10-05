<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerUriTrait.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Controllers;


trait ControllerUriTrait {
     ///<summary></summary>
    ///<param name="function" default="null"></param>
    /**
    * 
    * @param mixed $function the default value is null
    */
    public function getUri(?string $function=null){
        $out=[];
        $out["c"]= $this->getName();
        $g = "";
        if($function){
            $t=explode("&", $function);
            $f = str_replace('_', '-', $t[0]);
            $out["f"] = $f; //implode('&', $t);
            if (!empty($g = trim(implode('&', array_slice($t,1))))){
                $g = "&".$g;
            }
        }
        return "./?".http_build_query($out).$g;
    }

    ///<summary></summary>
    ///<param name="uri"></param>
    /**
    * 
    * @param mixed $uri
    */
    public function getUril($uri){
        $out="?c=".strtolower($this->getName());
        if($uri)
            $out .= "&".$uri;
        return $out;
    }

    ///<summary></summary>
    ///<param name="page"></param>
    /**
    * 
    * @param mixed $page
    */
    public function getUriv($page){
        $out="?c=".strtolower($this->getName());
        if($page)
            $out .= "&v=".$page;
        return $out;
    }
}