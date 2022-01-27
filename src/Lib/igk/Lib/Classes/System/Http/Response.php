<?php


namespace IGK\System\Http;

use IGK\System\Html\Dom\HtmlItemBase;

///<summary> default response </summary>
abstract class Response{
    /**
     * response body
     * @var mixed
     */
    private $body;

    public function getBody(){}
    public function setBody($body){
    }

    public static function HandleResponse($r){
        $e = 0;
        if (is_object($r) && (($r instanceof \IGK\System\Http\IResponse) || ($r instanceof \IGK\System\Http\RequestResponse))) {
            ob_get_level() &&  ob_clean();
            $r->output();
            $e = 1;
        } else if ($r instanceof HtmlItemBase) {
            ob_get_level() &&  ob_clean();
            $b = new WebResponse($r);
            $b->output();
            $e = 1;
        } else if (is_array($r)) {
            ob_get_level() &&  ob_clean();
            igk_json(json_encode($r));
            $e = 1;
        }
        if ($e)
            igk_exit();
        return $r;
    }
}