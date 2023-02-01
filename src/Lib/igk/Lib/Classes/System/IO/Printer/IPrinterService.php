<?php
// @author: C.A.D. BONDJE DOUE
// @file: IPrinterService.php
// @date: 20220701 19:15:24
namespace IGK\System\IO\Printer;

use IGK\System\IInjectable;

///<summary></summary>
/**
* use to print pdf with an library
* @package IGK\System\IO\Print
 * @method void setFillColor($r)
 * @method void setTextColor($r)
 * @method void setDrawColor($r)
*/
interface IPrinterService extends IInjectable{
    function resetDevice();
    //draw string
    function text($text, $x, $y);
    function rect($x, $y , $w, $h);
    function cell(string $text, $x, $y, $w, $h, ?array $options=null);
    function write(string $text, float $h);

    function setFontStyle($style); 
    
    /**
     * output pdf document
     * @return mixed 
     */
    function printPdf();

    function addPage();

}