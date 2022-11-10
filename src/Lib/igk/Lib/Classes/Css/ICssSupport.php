<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ICssSupport.php
// @date: 20220423 09:18:43
// @desc: css support rule capture


namespace IGK\Css;

interface ICssSupport{

    function supports(string $rule);
}