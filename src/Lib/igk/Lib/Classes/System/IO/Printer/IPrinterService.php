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
 * @method void image(string $file_or_uri, $x=null, $y=null, $w=null, $h=null, $type=null)
 * @method void output()
 * @method void setXY($x, $y=null)
 */
interface IPrinterService extends IInjectable{
    /**
     * L : landscape
     * P : portrait
     * @param string $orientation 
     * @return mixed 
     */
    function setOrientation(string $orientation); 
    function setOptions(object $options);
    function getOptions();
    function resetDevice();
    //draw string
    /**
     * draw text( )
     * @param mixed $text 
     * @param int $x x-axis position  
     * @param int $y y-axis position 
     * @return void
     */
    function text($text, ?int $x=null, ?int $y=null);
    function rect($x, $y , $w, $h, $style='');
    /**
     * 
     * @param string $text 
     * @param mixed $x 
     * @param mixed $y 
     * @param mixed $w 
     * @param mixed $h 
     * @param null|array|IPrinterServiceCellOptions $options 
     * @return mixed 
     */
    function cell(string $text, $x, $y, $w, $h, $options=null);
    function write(string $text, float $h);

    function setFontStyle($style); 
    function setFont(string $family, string $style='', int $size=12); 

    function createCellOptions(): IPrinterServiceCellOption;
    
    /**
     * output pdf document
     * @return mixed 
     */
    function printPdf();

    function addPage();

    function getPageWidth() : int;
    function getPageHeight() : int;
    /**
     * change the current font size
     * @param mixed $size 
     * @return mixed 
     */
    function setFontSize($size);

}