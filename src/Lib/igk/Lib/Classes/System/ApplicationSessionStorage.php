<?php
// @author: C.A.D. BONDJE DOUE
// @file: ApplicationSessionStorage.php
// @date: 20230207 12:01:14
namespace IGK\System;


///<summary></summary>
/**
* 
* @package IGK\System
*/
class ApplicationSessionStorage{
    /**
     * store create document
     * @var ?array
     */
    var $documents;

    /**
     * store controller cnofiguration
     * @var mixed
     */
    var $controllers;

    /**
     * store application session data
     * @var mixed
     */
    var $sessions;

    /**
     * store controller parameters
     * @var mixed
     */
    var $ctrlParams;


    var $components;
}