<?php
// @author: C.A.D. BONDJE DOUE
// @file: ExceptionErrorRequestResponse.php
// @date: 20231016 11:32:52
namespace IGK\System\Http;

use IGK\System\Html\Dom\HtmlItemBase;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\System\Http
*/
class ExceptionErrorRequestResponse extends ErrorRequestResponse{
    private $m_exception; 
    public function __construct(\Exception $ex, $header=null){
        parent::__construct($ex->getCode(), $ex->getMessage(), $header);
        $this->m_exception = $ex;
    } 
    /**
     * get exception title
     * @return HtmlItemBase 
     * @throws IGKException 
     */
    public function title(){
        $n = igk_create_node('div');
        $n->h1()->Content = get_class($this->m_exception);
        return $n;
    }
}