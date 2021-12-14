<?php


namespace IGK\System\Http;

///<summary> default response </summary>
abstract class Response{
    /**
     * reponse body
     * @var mixed
     */
    private $body;

    public function getBody(){}
    public function setBody($body){
    }
}