<?php
// @author: C.A.D. BONDJE DOUE
// @file: XmlNodeLoader.php
// @date: 20230525 14:13:24
namespace IGK\System\Html\XML;

use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Html\XML
*/
final class XmlNodeLoader extends XmlNode{
    var $tagname = 'igk:xml-loader';
    private function __construct(){
        parent::__construct();
    }
    public function getCanRenderTag()
    {
        return false;
    }
    /**
     * 
     * @param string $src 
     * @return static 
     * @throws IGKException 
     */
    public static function CreateFromContent(string $src){
        $n = new static;
        $n->load($src);
        return $n;
    }
}