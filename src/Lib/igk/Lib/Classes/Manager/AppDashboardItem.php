<?php
// @author: C.A.D. BONDJE DOUE
// @file: AppDashboardItem.php
// @date: 20260903 15:43:56
namespace IGK\Manager;

use IGK\Helper\Activator;

/**
* 
* @package IGK\Manager
* @author C.A.D. BONDJE DOUE
*/
class AppDashboardItem{
    /**
     * real source controller 
     * @var mixed
     */
    var $controller;
    /**
     * entry uri 
     */
    var $uri;
    /**
     * expected title 
     */
    var $title;
    /**
     * the description 
     */
    var $description;
    /**
     * application icon
     */
    var $icon;

    /**
     * 
     * @param mixed $n 
     * @return ?AppDashboardItem
     */
    public static function CreateNewInstance($n){
        $l = Activator::CreateNewInstance(static::class, $n);
        if (!$l || !$l->controller){
            return null;
        }
        if (empty($l->title )){
            $l->title = get_class($l->controller);
        }
        return $l;
    }
}