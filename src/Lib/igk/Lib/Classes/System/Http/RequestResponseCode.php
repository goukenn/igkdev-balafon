<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RequestResponseCode.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Http;

/**
 * request code 
 * @package IGK\System\Http
 */
abstract class RequestResponseCode{
    /**
    * Constant: ok.
    * @var mixed
    */
    const Ok = 200;
    /**
    * Constant: multiple choice.
    * @var mixed
    */
    const MultipleChoice = 300;
    /**
    * Constant: move permanently.
    * @var mixed
    */
    const MovePermanently = 301;
    /**
    * Constant: found.
    * @var mixed
    */
    const Found = 302;
    /**
    * Constant: see other.
    * @var mixed
    */
    const SeeOther = 303;
    /**
    * Constant: bad request.
    * @var mixed
    */
    const BadRequest = 400;
    /**
    * Constant: unauthorized.
    * @var mixed
    */
    const Unauthorized = 401;
    /**
    * Constant: payment required.
    * @var mixed
    */
    const PaymentRequired=402;
    /**
    * Constant: forbiden.
    * @var mixed
    */
    const Forbiden = 403;
    /**
    * Constant: not found.
    * @var mixed
    */
    const NotFound = 404;
    /**
    * Constant: method not allowed.
    * @var mixed
    */
    const MethodNotAllowed = 405;
    /**
    * Constant: not acceptable.
    * @var mixed
    */
    const NotAcceptable = 406;
    /**
    * Constant: proxy authentication required.
    * @var mixed
    */
    const ProxyAuthenticationRequired = 407;
    /**
    * Constant: request timeout.
    * @var mixed
    */
    const RequestTimeout = 408;
    /**
    * Constant: conflict.
    * @var mixed
    */
    const Conflict = 409;
    /**
    * Constant: gone.
    * @var mixed
    */
    const Gone = 410;
    /**
    * Constant: length required.
    * @var mixed
    */
    const LengthRequired = 411;
    /**
    * Constant: internal server error.
    * @var mixed
    */
    const InternalServerError = 500;
}