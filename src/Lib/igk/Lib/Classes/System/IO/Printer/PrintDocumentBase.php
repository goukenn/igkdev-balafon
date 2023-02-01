<?php
// @author: C.A.D. BONDJE DOUE
// @file: PrintDocumentBase.php
// @date: 20220701 19:14:25
namespace IGK\System\IO\Printer;


///<summary></summary>
/**
* 
* @package IGK\System\IO\Print
*/
abstract class PrintDocumentBase{
    protected $printer;
    public function __construct(IPrinterService $printer)
    {
        $this->printer = $printer;
    }
    public function printPdf(){
        $this->generateDocument($this->printer);
        $this->printer->printPdf();
        $this->printer->resetDevice();
    }
    /**
     * override this method to generate printing documnet
     * @return void 
     */
    protected abstract function generateDocument(IPrinterService $printer);

    protected function header(IPrinterService $printer){

    }

    protected function footer(IPrinterService $printer){

    }
    
}