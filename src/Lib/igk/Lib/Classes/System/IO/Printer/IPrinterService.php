<?php
// @author: C.A.D. BONDJE DOUE
// @file: IPrinterService.php
// @date: 20220701 19:15:24
namespace IGK\System\IO\Printer;

use IGK\System\IInjectable;

///<summary></summary>
/**
* 
* @package IGK\System\IO\Print
*/
interface IPrinterService extends IInjectable{
    //draw string
    function text($text, $x, $y);
    function rect($x, $y , $w, $h);

    function setFontStyle($style);
    
    /**
     * output pdf document
     * @return mixed 
     */
    function printPdf();

}