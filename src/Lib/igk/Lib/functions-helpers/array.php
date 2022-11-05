<?php

if (!function_exists("igk_array_peek_last")) {
    /**
     * load array
     * @param mixed $tab 
     * @return mixed 
     */
    function igk_array_peek_last($tab)
    {
        if (($c = count($tab)) > 0) {
            return $tab[$c - 1];
        }
        return null;
    }
}