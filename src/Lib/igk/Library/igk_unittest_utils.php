<?php

// @author: C.A.D. BONDJE DOUE
// @filename: igk_unittest_utils.php
// @date: 20260513 18:51:39
// @desc: unittest utility helpers

/**
 * 
 * @param int $expected ponderation must be greather than 0,
 * @param string $test test name 
 * @param mixed ...$expected 
 * @return int 
 */
function igk_unittest_run(int $point_expected, string $test, ...$expected)
{
    $score = 0;
    if (empty($expected) || ($point_expected <= 0)) {
        return $score;
    }
    $fc = function ($func, $expected, $input, $testName) {
        $result = $func($input);
        if ($result == $expected) {
            return true;
        }
        echo "FAILED: " . $testName . "\n";
        echo "INPUT: " . json_encode($input) . "\n";
        echo "EXPECTED: <" . json_encode($expected) . ">" . "\n";
        echo "OUTPUT  : <" . json_encode($result) . ">" . "\n";
    };
    $func = $test;
    $T = 0;
    for ($offset = 0; $offset < count($expected); $offset += 2) {
        $T++;
        $arg = [$func];
        array_push($arg, ...array_slice($expected, $offset, 2));
        $arg[] = sprintf('%s_%s', $test, $T);

        if (call_user_func_array($fc, $arg)) {
            $score++;
        }
    }
    $score = round($point_expected * ($score / $T));
    return $score;
}
