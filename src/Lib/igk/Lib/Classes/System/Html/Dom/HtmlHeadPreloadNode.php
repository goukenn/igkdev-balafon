<?php

// @author: C.A.D. BONDJE DOUE
// @filename: HtmlHeadPreloadNode.php
// @date: 20220829 11:54:40
// @desc: 
namespace IGK\System\Html\Dom;
use IGK\System\Html\Dom\HtmlNoTagNode;

final class HtmlHeadPreloadNode extends HtmlNoTagNode{
    private static $sm_instance;

    private function __construct(){
        parent::__construct();
    }
    public static function getItem(){
        if (is_null(self::$sm_instance)){
            self::$sm_instance = new self;
        }
        return self::$sm_instance;
    }
}
