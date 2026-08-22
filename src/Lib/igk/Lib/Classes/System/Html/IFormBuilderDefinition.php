<?php
// @author: C.A.D. BONDJE DOUE
// @file: IFormBuilderDefinition.php
// @date: 20260723 10:31:39
namespace IGK\System\Html;

use IGK\System\Html\Dom\HtmlItemBase;

/**
* form builder field definitions structures
* @package IGK\System\Html
* @author C.A.D. BONDJE DOUE
* @property ?string $type type |radio|text|password|email|number|date
* @property ?string $placeholder message place holder
* @property ?string $label_text label to display
* @property ?array  $label_attribs label attributes
* @property ?string|HtmlItemBase $after_input hosting after input
* @property ?string|HtmlItemBase $afterComponent hosting after input
* @property ?string $tips message to show on error
*/
interface IFormBuilderDefinition{

}