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
* Html head base uri node.
* @package IGK\System\Html\Dom
*/
final class HtmlHeadBaseUriNode extends HtmlNode{

    /**
    * Property: item.
    * @var mixed
    */
    static $sm_item;

    /**
    * Accept render.
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

    /**
    * .ctr
    */
    public function __construct(){
        parent::__construct("base");
    }

    /**
    * Returns Is Visible.
    */

    public function getIsVisible(){
        return !defined('IGK_NO_BASEURL') && (!igk_io_basedir_is_root());
    }

    /**
    * Returns Item.
    */

    public static function getItem(){
        if(self::$sm_item == null){
            self::$sm_item=new HtmlHeadBaseUriNode();
        }
        return self::$sm_item;
    }
}