<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ResponseHandler.php
// @date: 20220630 08:41:30
// @desc: 
namespace IGK\System\Http;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Http\JsonResponse;
use IGK\System\Http\WebResponse;
use IGK\System\IO\StringBuilder;

/**
 * response handler
 * @package IGK\System\Http\ReponseHandler
 */
class ResponseHandler
{
    /**
    * Request handler mime type.
    */
    public function requestHandlerMimeType(){
         if($v_qoptions =Request::getInstance()->getQueryInfo()->query_options){
                if ($fmt = igk_getv($v_qoptions, 'fmt')){
                    return igk_getv([
                        'json'=>'application/json',
                        'xml'=>'application/xml',
                        'txt'=>'text/plain',
                        'html'=>'text/html'], $fmt);
                }
            }
    }
    /**
    * handle response
    * @param mixed $r
    * @param mixed $code
    * @throws IGKException
    * @return mixed
    */
    public function HandleReponse($r, $code = RequestResponseCode::Ok)
    {
        $e = 0;
        if ($r instanceof IResponseData){
            $code = $r->getCode();
        }
        if (is_object($r) && ($r instanceof \IGK\System\Http\IResponse)) {
            ob_get_level() &&  ob_clean();
            $r->output();
            $e = 1;
        } else if ($r instanceof HtmlItemBase) {
            ob_get_level() &&  ob_clean();
            $b = new WebResponse($r);
            $b->code = $code;
            $b->output();
            $e = 1;
        } else if (is_array($r) || is_object($r)) {
            $code = igk_getv($r, $c_key = Request::ARRAY_RESPONSE_CODE) ?? $code;
            igk_unset($r, $c_key);
            ob_get_level() &&  ob_clean();
            $fmt_mime_type =igk_server()->CONTENT_TYPE;
            if($v_qoptions = $this->requestHandlerMimeType()){
                    $fmt_mime_type = $v_qoptions;
            }
            switch ($fmt_mime_type) {
                case 'application/xml':
                    $r = igk_xml_render('response', $r);
                    $b = new XmlResponse($r);
                    $b->code = $code; 
                     $b->output();
                    exit;
                    break;
                case 'text/html':
                    $sb = new StringBuilder(); 
                    $sb->appendLine('response:');
                    foreach($r as $k=>$v){
                        $ds = is_object($v) || is_array($v) ? json_encode($v) : $v;
                        $sb->appendLine("\t".$k .": " .$ds);
                    }
                    $b = new WebResponse($sb.'');
                    $b->code = $code;
                    $b->output();
                    break;
                default:
                    $b = new JsonResponse($r);
                    $b->code = $code;
                    $b->output();
                    break;
            }
            $e = 1;
        }
        if ($e)
            igk_exit();
        return $r;
    }
}