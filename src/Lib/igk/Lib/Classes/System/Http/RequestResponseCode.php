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
    * auto generate doc.
    * @var mixed
    */
    const Ok = 200;

    /**
    * auto generate doc.
    * @var mixed
    */
    const MultipleChoice = 300;

    /**
    * auto generate doc.
    * @var mixed
    */
    const MovePermanently = 301;

    /**
    * auto generate doc.
    * @var mixed
    */
    const Found = 302;

    /**
    * auto generate doc.
    * @var mixed
    */
    const SeeOther = 303;

    /**
    * auto generate doc.
    * @var mixed
    */
    const BadRequest = 400;

    /**
    * auto generate doc.
    * @var mixed
    */
    const Unauthorized = 401;

    /**
    * auto generate doc.
    * @var mixed
    */
    const PaymentRequired=402;

    /**
    * auto generate doc.
    * @var mixed
    */
    const Forbiden = 403;

    /**
    * auto generate doc.
    * @var mixed
    */
    const NotFound = 404;

    /**
    * auto generate doc.
    * @var mixed
    */
    const MethodNotAllowed = 405;

    /**
    * auto generate doc.
    * @var mixed
    */
    const NotAcceptable = 406;

    /**
    * auto generate doc.
    * @var mixed
    */
    const ProxyAuthenticationRequired = 407;

    /**
    * auto generate doc.
    * @var mixed
    */
    const RequestTimeout = 408;

    /**
    * auto generate doc.
    * @var mixed
    */
    const Conflict = 409;

    /**
    * auto generate doc.
    * @var mixed
    */
    const Gone = 410;

    /**
    * auto generate doc.
    * @var mixed
    */
    const LengthRequired = 411;

    /**
    * auto generate doc.
    * @var mixed
    */
    const InternalServerError = 500;
}