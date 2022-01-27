<?php

namespace IGK\System\Http;

/**
 * sent the output response
 * @package IGK
 */
interface IResponse{
    /**
     * send output
     * @return mixed 
     */
    function output();
}