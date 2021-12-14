<?php

// @file: HtmlNoChildBase.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021

namespace IGK\System\Html\Dom;


abstract class HtmlNoChildBase extends HtmlItemBase{
   public final function getCanAddChilds(){
       return false;
   }
}