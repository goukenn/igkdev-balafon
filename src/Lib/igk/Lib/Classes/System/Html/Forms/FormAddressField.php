<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FormAddressField.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Forms;

use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlNoTagNode;
use IGKException;

class FormAddressField extends HtmlNode implements IFormValidationNode{
    private $m_fields;
    protected $tagname = "igk:form-address-field";

    public function getCanRenderTag(){
        return false;
    }

    public function __construct($fiedname)
    {
        $this->fieldname = $fiedname;
        $this->m_fields = [
            "address.street",
            "address.number"=>["type"=>"int"],
            "address.box",
            "address.city",
            "address.postalcode"=>["type"=>"int"],
            "address.country",
        ];
        parent::__construct();

    }
    public function validateRequest(& $outputdata, & $errors){

        $outputdata[$this->fieldname] = (object)[
            "street"=>igk_getr("address_street"),
            "number"=>igk_getr("address_number"),
            "box"=>igk_getr("address_box"),
            "city"=>igk_getr("address_city"),
            "postalcode"=>igk_getr("address_postalcode"),
            "country"=>igk_getr("address_country"),
        ];
        return true;
    }
   
    protected function initialize()
    {
        parent::initialize();
    }
    protected function _acceptRender($options = null):bool
    {
        if (!parent::_acceptRender($options))
        return false;
    $this->fields($this->m_fields);        
    return true;
    }
    /**
     * get field by name
     * @param mixed $name 
     * @return mixed 
     * @throws IGKException 
     */
    public function getField($name){
        return igk_getv($this->m_fields, $name);
    }
}