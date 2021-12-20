<?php
///<summary>an element that must implement to_array method</summary>
interface IIGKArrayObject extends ArrayAccess, Countable{
    function to_array();
}