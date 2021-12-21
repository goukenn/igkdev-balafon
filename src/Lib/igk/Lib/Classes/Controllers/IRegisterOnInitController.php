<?php

namespace IGK\Controllers;

use IIGKController;

interface IRegisterOnInitController extends IIGKController{
    /**
     * get if the controller 
     * @return bool 
     */
    function getCanRegisterOnInit(): bool;
}