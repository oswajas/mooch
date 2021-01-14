<?php

namespace qtype_mooch\util;

/**
 * A class for generating unique identifiers.
 */
class UID {
    /**
     * Generates a unique identifier that is not suitable for cryptographic purposes.
     *
     * @return string A unique identifier that is not suitable for
     *  cryptographic purposes
     */
    public static function weak() {
        $string = trim(base64_encode(uniqid("", true)), '=');
        return substr($string, intval(strlen($string)/2));
    }
}