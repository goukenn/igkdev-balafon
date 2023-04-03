<?php
// @author: C.A.D. BONDJE DOUE
// @file: IPrinterModel.php
// @date: 20230202 07:40:14
namespace IGK\Printing;

use IGK\System\IO\Printer\IPrinterService;

///<summary></summary>
/**
* 
* @package IGK\Printing
*/
interface IPrinterModel{
    /**
     * set printer model 
     * @param null|IGK\Printing\IPrinterService $service 
     * @return mixed 
     */
    function setPrinterService(?IPrinterService $service);
    /**
     * invoke printing
     * @param bool $exit exit after printing
     * @return mixed 
     */
    function print(bool $exit=true);
}