<?php
// @author: C.A.D. BONDJE DOUE
// @file: IServiceMailData.php
// @date: 20230519 12:38:59
namespace IGK\System\Mail;

/**
* get mail data service
* @package IGK\System\Mail
*/
interface IServiceMailData{
    /**
    * Returns Mail Data.
    * @return ?array
    */
    function getMailData(): ?array;
}