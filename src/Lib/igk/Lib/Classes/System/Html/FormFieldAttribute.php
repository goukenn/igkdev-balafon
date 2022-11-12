<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldAttribute.php
// @date: 20221111 13:50:08
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
*/
class FormFieldAttribute{
    var $attribs;
    public function __construct(array $tab)
    {
        $this->attribs = $tab;
    }
}