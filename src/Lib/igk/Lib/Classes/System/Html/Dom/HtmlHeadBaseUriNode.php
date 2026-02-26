<?php
// @file: IGKHtmlHeadBaseUri.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;

/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
final class HtmlHeadBaseUriNode extends HtmlNode{
    static $sm_item;

    /**
    * auto generate doc.
    * @param null|mixed $options
    * @return bool
    */
    protected function _acceptRender($options = null):bool{
        if(($doc=$options->Document)){
            $b=$doc->getBaseUri();
            if(!empty($b)){
                $this["href"]=$b;
                return 1;
            }
        }
        return false;
    }
    public function __construct(){
        parent::__construct("base");
    }

    /**
    * auto generate doc.
    */
    public function getIsVisible(){
        return !defined('IGK_NO_BASEURL') && (!igk_io_basedir_is_root());
    }

    /**
    * auto generate doc.
    */
    public static function getItem(){
        if(self::$sm_item == null){
            self::$sm_item=new HtmlHeadBaseUriNode();
        }
        return self::$sm_item;
    }
}