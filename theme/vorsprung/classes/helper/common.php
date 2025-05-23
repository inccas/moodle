<?php

namespace theme_vorsprung\helper;

class common {
    /**
     * @param string $haystack String to test
     * @param string $needle Possible leading string of $haystack
     * @return bool true if $haystack starts with $needle, false otherwise
     */
    static function startsWith(string $haystack, string $needle): bool {
        $length = strlen($needle);
        return substr($haystack, 0, $length) === $needle;
    }
    /**
     * Test if a given string ends with some other strings
     * @param string $haystack String to test
     * @param string|array $needle Possible trailing string of $haystack
     * @retrun bool true if $haystack ends with one $needle, false otherwise
     */
    static function endswith(string $haystack, $needle): bool {
        if (empty($haystack)) return false;
        $hlen = strlen($haystack);
        $needles = is_array($needle) ? $needle : [$needle];
        foreach ($needles as $needle) {
            $nlen = strlen($needle);
            if ($nlen > $hlen) continue;
            if (substr_compare($haystack, $needle, $hlen - $nlen, $nlen) === 0) return true;
        }
        return false;
    }
}