<?php
// @file: IGKHtmlActiveAttrib.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;

use IGKObject;

final class HtmlActiveAttrib extends IGKObject{
    ///<summary></summary>
    public static function getInstance(){
        $key='sys://html/active/attribInstance';
        $b=igk_get_env($key);
        if($b)
            return $b;
        $b=new self();
        igk_set_env($key, $b);
        return $b;
    }
}
