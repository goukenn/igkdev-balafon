<?php
// @author: C.A.D. BONDJE DOUE
// @file: UriDectectorMatch.php
// @date: 20221122 19:53:31
namespace IGK\DocumentParser;

use IGK\System\IO\Path;

///<summary></summary>
/**
* 
* @package IGK\DocumentParser
*/
class UriDectectorMatch{
    private $m_replacementFile;
    var $scheme;
    var $domain;
    var $path;
    var $query;
    var $hash;
    var $uri; 
    /**
     * extra definition
     * @var mixed
     */
    var $extra;
    /**
     * base uri or reference uri
     * @var mixed
     */
    var $fromUri;

    /**
     * get base form uri
     * @return string|null 
     */
    public function getBaseUri(){
        return $this->fromUri ?  dirname($this->fromUri) : null; 
    }

    public function setReplacementFile(string $file){
        $this->m_replacementFile = $file;
    }

    /**
     * get flattent replacement
     * @return mixed 
     */
    public function getFlattenReplacement():?string{
        if ($this->uri){
            if ($this->m_replacementFile){
                return str_replace($this->uri, 'url('.$this->m_replacementFile.')', $this->uri ); 
            }
            $path = Path::FlattenPath($this->path);        
            return str_replace($this->uri, 'url('.$path.$this->extra.')', $this->uri );
        }
        return null;
    }
    /**
     * retrieve the fullpath. request URL
     * @return string 
     */
    public function getFullPath(){
        return Path::FlattenPath($this->path).$this->extra;
    }
    public function __toString()
    {
        return $this->getFullPath();
    }
}