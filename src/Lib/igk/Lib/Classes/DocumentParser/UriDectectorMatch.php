<?php
// @author: C.A.D. BONDJE DOUE
// @file: UriDectectorMatch.php
// @date: 20221122 19:53:31
namespace IGK\DocumentParser;


///<summary></summary>
/**
* 
* @package IGK\DocumentParser
*/
class UriDectectorMatch{
    var $scheme;
    var $domain;
    var $path;
    var $query;
    var $hash;
    var $uri; 
    /**
     * base uri or reference uri
     * @var mixed
     */
    var $fromUri;

    public function getBaseUri(){
        return $this->fromUri ?  dirname($this->fromUri) : null; 
    }

}