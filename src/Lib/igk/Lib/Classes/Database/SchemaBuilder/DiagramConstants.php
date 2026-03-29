<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DiagramConstants.php
// @date: 20220622 08:47:20
// @desc: 
namespace IGK\Database\SchemaBuilder;
use IGK\Constants;
/**
* Diagram constants.
* @package IGK\Database\SchemaBuilder
*/
class DiagramConstants{
    /**
    * Constant: guid length.
    * @var mixed
    */
    const GUID_LENGTH =  Constants::GUID_LENGTH;
    /**
    * Constant: name length.
    * @var mixed
    */
    const NAME_LENGTH = 30;
    /**
    * Constant: title length.
    * @var mixed
    */
    const TITLE_LENGTH = 60;
    /**
    * Constant: path length.
    * @var mixed
    */
    const PATH_LENGTH  = 255;
}