<?php
// @file: IGKDataBindingScript.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* auto generate doc.
*/
final class IGKDataBindingScript extends IGKObject{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $_args, $_shifparent;
    /**
     * Constructor.
     */

    public function __construct(){
        $this->_args=array();
        $this->_shifparent=0;
    }
    /**
     * Returns the current top argument when the "args" property is accessed.
     * @param string $n The property name being accessed.
     * @return mixed The peeked argument value, or null if not "args".
     */

    public function __get($n){
        if($n === "args")
            return $this->peek();
        return null;
    }
    /**
     * Checks whether the given property name is "args".
     * @param string $n The property name to check.
     * @return bool True if $n equals "args", false otherwise.
     */

    public function __isset($n){
        return ($n === 'args');
    }
    /**
     * Returns the total number of arguments in the stack.
     * @return int The count of stored arguments.
     */

    public function Count(){
        return count($this->_args);
    }
    /**
     * Returns the current top argument without removing it from the stack.
     * @return mixed The top argument value, or null if the stack is empty.
     */

    public function getArgs(){
        return $this->peek();
    }
    /**
     * Peeks at the top argument, accounting for parent shift offset.
     * @return mixed The top argument value, or null if unavailable.
     */

    public function peek(){
        if((($c=count($this->_args)) - $this->_shifparent) > 0){
            return $this->_args[$c - (1 + $this->_shifparent)];
        }
        return null;
    }
    /**
     * Removes and returns the top argument from the stack.
     * @return mixed The removed top argument value.
     */

    public function pop(){
        return array_pop($this->_args);
    }
    /**
     * Pushes a new argument onto the top of the stack.
     * @param mixed $data The data to push onto the argument stack.
     */

    public function push($data){
        array_push($this->_args, $data);
    }
    /**
     * Resets the parent shift offset to zero.
     */

    public function resetShift(){
        $this->_shifparent=0;
    }
    /**
     * Sets the parent shift offset to one, skipping the topmost argument.
     */

    public function shiftParent(){
        $this->_shifparent=1;
    }
}
