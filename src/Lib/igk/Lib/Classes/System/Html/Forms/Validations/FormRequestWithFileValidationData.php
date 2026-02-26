<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormRequestWithFileValidationData.php
// @date: 20241123 10:44:36
namespace IGK\System\Html\Forms\Validations;
use IGK\System\Http\Request;
/**
* 
* @package IGK\System\Html\Forms\Validations
* @author C.A.D. BONDJE DOUE
*/
class FormRequestWithFileValidationData{

    /**
    * Property: data.
    * @var mixed
    */
    private $m_data;

    /**
    * .ctr
    * @param mixed $data
    */
    public function __construct($data)
    {
        !$data ?? igk_die('missing data');
        $this->m_data = (object)$data;
    }

    /**
    * check if isset innaccessible property
    * @param mixed $name
    */
    public function __isset($name)
    { 
       return isset($this->m_data->$name) || ( $this->isSupportFileRequest() && 
        key_exists($name, $this->m_data->{Request::FILES_FIELD}));
    }

    /**
    * Empty.
    * @param mixed $name
    */
    public function __empty($name){
        igk_wln_e("check form empty");
    }

    /**
    * .destructor
    * @param mixed $name
    */
    public function __get($name){
        if (isset($this->m_data->$name)){
            return igk_getv($this->m_data, $name);
        }
        return igk_getv($this->m_data->{Request::FILES_FIELD}, $name);
    }

    /**
    * Returns true if Support File Request.
    */
    public function isSupportFileRequest(){
        return Request::IsSupportFileRequest($this->m_data);
    }
}