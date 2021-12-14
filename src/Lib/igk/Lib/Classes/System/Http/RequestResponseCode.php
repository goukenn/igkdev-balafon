<?php
namespace IGK\System\Http;

/**
 * request code 
 * @package IGK\System\Http
 */
abstract class RequestResponseCode{
    const Ok = "200";
    const MultipleChoice = 300;
    const MovePermanently = 301;
    const Found = 302;
    const SeeOther = 303;

    const BadRequest = 400;
    const Unauthorized = 401;
    const PaymentRequired=402;
    const Forbiden = 403;
    const NotFound = 404;
    const MethodNotAllowed = 405;
    const NotAcceptable = 406;
    const ProxyAuthenticationRequired = 407;
    const RequestTimeout = 408;
    const Conflict = 409;
    const Gone = 410;
    const LengthRequired = 411;
    const InternalServerError = 500;

}