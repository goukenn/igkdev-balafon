<?php

namespace IGK\System\Http;

/**
 * sent the output reponse
 * @package IGK
 */
interface IResponse{
    /**
     * send output
     * @return mixed 
     */
    function output();
}