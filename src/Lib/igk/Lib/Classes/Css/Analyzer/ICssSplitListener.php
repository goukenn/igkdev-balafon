<?php
// @author: C.A.D. BONDJE DOUE
// @file: ICssSplitListener.php
// @date: 20250627 06:18:09
namespace IGK\Css\Analyzer;
/**
* 
* @package IGK\Css\Analyzer
* @author C.A.D. BONDJE DOUE
*/
interface ICssSplitListener
{
    public function split(string $value): array;
}