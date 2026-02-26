<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldAttribute.php
// @date: 20221111 13:50:08
namespace IGK\System\Html;
/**
* 
* @package IGK\System\Html
*/
class FormFieldAttribute{

    /**
    * auto generate doc.
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