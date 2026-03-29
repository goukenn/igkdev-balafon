<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldAttribute.php
// @date: 20221111 13:50:08
namespace IGK\System\Html;
/**
* auto generate doc.
* @package IGK\System\Html
*/
class FormFieldAttribute{
    /**
    * Property: attribs.
    * @var mixed
    */
    var $attribs;
    /**
    * .ctr
    * @param array $tab
    */
    public function __construct(array $tab)
    {
        $this->attribs = $tab;
    }
}