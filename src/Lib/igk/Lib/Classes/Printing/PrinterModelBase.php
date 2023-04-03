<?php
// @author: C.A.D. BONDJE DOUE
// @file: PrinterModelBase.php
// @date: 20230202 07:47:01
namespace IGK\Printing;

use IGK\System\IO\Printer\IPrinterService;

///<summary></summary>
/**
* 
* @package IGK\Printing
*/
abstract class PrinterModelBase implements IPrinterModel{
    protected $_printer_service;
    public function setPrinterService(?IPrinterService $service) {
        $this->_printer_service = $service;
     }

    public function print(bool $exit = true) { 
        $srv = $this->_printer_service;
        if ($srv){
            $this->render($srv);
        }
        if ($exit){
            igk_exit();
        }
        return true;
    }
    /**
     * Render with printer service
     * @param IPrinterService $device 
     * @return mixed 
     */
    protected abstract function render(IPrinterService $device);
}