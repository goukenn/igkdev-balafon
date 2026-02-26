<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlHeadPreloadNode.php
// @date: 20220829 11:54:40
// @desc: 
namespace IGK\System\Html\Dom;
use IGK\System\Html\Dom\HtmlNoTagNode;

/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
final class HtmlHeadPreloadNode extends HtmlNoTagNode{

    /**
    * auto generate doc.
    * @var mixed
    */
    private static $sm_instance;
    /**
     * Constructor.
     */
    private function __construct(){
        parent::__construct();
    }
    /**
     * Returns the singleton instance of the head preload node.
     *
     * @return self
     */

    public static function getItem(){
        if (is_null(self::$sm_instance)){
            self::$sm_instance = new self;
        }
        return self::$sm_instance;
    }
}