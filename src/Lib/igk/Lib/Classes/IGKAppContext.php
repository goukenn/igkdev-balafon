<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKAppContext.php
// @date: 20220803 13:48:54
// @desc: 
///<summary>represent application context
/**
* represent application context
*/
abstract class IGKAppContext{

    /**
    * Constant: initializing.
    * @var mixed
    */
    const initializing="initializing";

    /**
    * Constant: running.
    * @var mixed
    */
    const running="running";

    /**
    * Constant: starting.
    * @var mixed
    */
    const starting="starting";
}