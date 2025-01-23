<?php
// @author: C.A.D. BONDJE DOUE
// @file: IRegexMatcherContainer.php
// @date: 20241107 05:10:19
namespace IGK\System\Text;


///<summary></summary>
/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
interface IRegexMatcherContainer{
    function match(string $match, ?string $tokenID=null, ?string $refId=null, ?array $patterns=null);
    function begin(string $begin, ?string $end=null, ?string $tokenID=null, ?string $refId=null, ?array $patterns=null);
    function while(string $begin, ?string $end=null, ?string $tokenID=null, ?string $refId=null, ?array $patterns=null);
    function append(RegexMatcherPattern $pattern);
}