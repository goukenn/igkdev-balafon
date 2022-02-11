<?php
namespace IGK\System\Http;

interface IHeaderResponse{
    function getResponseHeaders() : ?array;
}