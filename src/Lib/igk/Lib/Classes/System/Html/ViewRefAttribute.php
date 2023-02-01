<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewRefAttribute.php
// @date: 20221231 16:35:47
namespace IGK\System\Html;


///<summary></summary>
/**
* view ref template expression
* @package IGK\System\Html
*/
class ViewRefAttribute implements IHtmlTemplateAttribute{
    var $data;
    /**
     * get expression
     * @return string 
     */
    public function expression():string{
        return $this->data;
    }
}