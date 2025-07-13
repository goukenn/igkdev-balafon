<?php
// @author: C.A.D. BONDJE DOUE
// @file: MailStyleValue.php
// @date: 20250427 08:37:58
namespace IGK\System\Http\Mail;
use IGK\System\Html\IHtmlStyleAtribute;
/**
* 
* @package IGK\System\Http\Mail
* @author C.A.D. BONDJE DOUE
*/
/**
 * get mail style value
 */
final class MailStyleValue implements IHtmlStyleAtribute
{
    var $value;
    public function __construct(string $value)
    {
        $this->value = $value;
    }
    public function __toString()
    {
        return $this->getValue() . '';
    }
    public function getValue($options = null)
    {
        return $this->value;
    }
}