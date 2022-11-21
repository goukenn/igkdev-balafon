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
     * handle response
     * @param mixed $r 
     * @return mixed 
     * @throws IGKException 
     */
    public function HandleReponse($r)
    {
        $e = 0;
        if (is_object($r) && ($r instanceof \IGK\System\Http\IResponse)) {
            ob_get_level() &&  ob_clean();
            $r->output();
            $e = 1;
        } else if ($r instanceof HtmlItemBase) {
            ob_get_level() &&  ob_clean();
            $b = new WebResponse($r);
            $b->output();
            $e = 1;
        } else if (is_array($r) || is_object($r)) {
            ob_get_level() &&  ob_clean();
            switch (igk_server()->CONTENT_TYPE) {
                case 'application/xml':
                    $r = igk_xml_render('response', $r);
                    $b = new WebResponse($r);
                    $b->output();
                    break;
                case 'text/html':
                    $sb = new StringBuilder();  
                    $header = '';
                    $sb->appendLine('response:');
                    foreach($r as $k=>$v){
                        $ds = is_object($v) || is_array($v) ? json_encode($v) : $v;
                        $sb->appendLine("\t".$k .": " .$ds);
                    }
                    $b = new WebResponse($sb.'');
                    $b->output();
                    break;
                default:
                    $b = new JsonResponse($r);
                    $b->output();
                    break;
            }
            $e = 1;
        }
        // stop : on exit
        if ($e)
            igk_exit();
        return $r;
    }
}
