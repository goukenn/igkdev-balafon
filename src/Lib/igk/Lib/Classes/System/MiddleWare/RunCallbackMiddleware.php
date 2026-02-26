<?php
// @file: IGKRunCallbackMiddleware.php
// @author: C.A.D. BONDJE DOUE
// @copyright: igkdev © 2019
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Middlewares;
/**
* Represent IGKRunCallbackMiddleware class
*/
class RunCallbackMiddleware extends BalafonMiddleware{

    /**
    * Callback handler for callback.
    * @var mixed
    */
    private $callback;
    /**
    * 
    * @param closure callback
    */

    public function __construct($callback){
        $this->callback=$callback;
    }
    /**
    * 
    */

    public function invoke(){
        $r=call_user_func_array($this->callback, array($this->getService()));
        if(!$r)
            $this->next();
    }
}