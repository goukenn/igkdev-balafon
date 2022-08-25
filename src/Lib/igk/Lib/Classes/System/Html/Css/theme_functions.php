<?php

// @author: C.A.D. BONDJE DOUE
// @filename: theme_functions.php
// @date: 20220805 17:02:34
// @desc: 

use IGK\System\Exceptions\ArgumentTypeNotValidException;

/**
 * 
 * @param string $propety
 * @param string $style 
 * @param string $prefix prefix of class to prepend 
 * @return void 
 * @throws IGKException 
 * @throws ArgumentTypeNotValidException 
 * @throws ReflectionException 
 */
function igk_theme_screen_mark($property, $style, $prefix = '.igk-')
{
    extract(igk_environment()->last(IGKEnvironmentConstants::CSS_UTIL_ARGS) ?? []);

    $xsm_screen[$prefix . "xsm-sm-$property"] =
        $xsm_screen[$prefix . "xsm-sm-lg-$property"] =
        $xsm_screen[$prefix . "xsm-sm-lg-xlg-$property"] =
        $sm_screen[$prefix . "xsm-sm-$property"] =
        $sm_screen[$prefix . "xsm-sm-lg-$property"] =
        $sm_screen[$prefix . "xsm-sm-lg-xlg-$property"] =
        $sm_screen[$prefix . "sm-lg-$property"] =
        $sm_screen[$prefix . "sm-lg-xlg-$property"] =
        $sm_screen[$prefix . "sm-lg-xlg-xxlg-$property"] =
        $lg_screen[$prefix . "xsm-sm-lg-$property"] =
        $lg_screen[$prefix . "xsm-sm-lg-xlg-$property"] =
        $lg_screen[$prefix . "sm-lg-$property"] =
        $lg_screen[$prefix . "sm-lg-xlg-$property"] =
        $lg_screen[$prefix . "sm-lg-xlg-xxlg-$property"] =
        $lg_screen[$prefix . "lg-xlg-$property"] =
        $lg_screen[$prefix . "lg-xlg-xxlg-$property"] =
        $xlg_screen[$prefix . "xsm-sm-lg-xlg-$property"] =
        $xlg_screen[$prefix . "sm-lg-xlg-$property"] =
        $xlg_screen[$prefix . "sm-lg-xlg-xxlg-$property"] =
        $xlg_screen[$prefix . "lg-xlg-$property"] =
        $xlg_screen[$prefix . "lg-xlg-xxlg-$property"] =
        $xlg_screen[$prefix . "xlg-xxlg-$property"] =
        $xxlg_screen[$prefix . "sm-lg-xlg-xxlg-$property"] =
        $xxlg_screen[$prefix . "lg-xlg-xxlg-$property"] =
        $xxlg_screen[$prefix . "xlg-xxlg-$property"]
        = $style;
}
