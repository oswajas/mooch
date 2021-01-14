<?php

namespace qtype_mooch\chess;

class NotationUtility {
    /**
     * Check wheather at least the position and the side to move can be
     * extracted from a string that pretends to be FEN.
     *
     * @param string $fen The string to check.
     * @return boolean
     */
    public static function validateFen($fen) {
        $parts = preg_split('/\s+/', trim($fen));
       
        if (count($parts) < 2) {
            return false;
        }
       
        if (preg_match('/^[wb]$/i', $parts[1]) === false) {
            return false;
        }
       
        $ranks = explode('/', $parts[0]);
        if (count($ranks) != 8) {
            return false;
        }

        foreach ($ranks as $rank) {
            $files = 0;
            for ($i = 0; $i < strlen($rank); ++$i) {
                if (strpos("012345678", $rank[$i]) !== false) {
                    $files += $rank[$i];
                } else if (strpos("KQRBNPkqrbnp", $rank[$i]) !== false) {
                    $files += 1;
                } else {
                    return false;
                }
            }
            if ($files != 8) {
                return false;
            }
        }
       
        return true;
    }
   
    /**
     * Check whether source and destination square and possibly promotion piece
     * can be extracted from a string.
     *
     * @param string $move
     * @return boolean
     */
    public static function validateMove(string $move) {
        return !empty(self::normalizeMove($move));
    }
   
    /**
     * Normalize a move.
     *
     * Output format is
     *
     *   - Standard moves    [square]-[square]
     *   - Pawn promotion    [square]-[square]=[piececode]
     *   - King side castle  0-0-0
     *   - Queen side castle 0-0
     *
     * in lower case.
     *
     * @param string $move
     * @return string
     */
    public static function normalizeMove($move) {
        $parts = explode('|', $move, 2);
        $matches = [];
        preg_match('/([abcdefgh][12345678]).*([abcdefgh][12345678])([^qrbn]*([qrbn]))?/i', $parts[0], $matches);
        if (count($matches)) {
            $move = "$matches[1]-$matches[2]";
            if (isset($matches[4])) {
                $move .= "=$matches[4]";
            }
            return strtolower($move);
        } else if (preg_match('/[0Oo]\s*-\s*[0Oo]\s*-\s*[0oO]/', $parts[0])) {
            return "0-0-0";
        } else if (preg_match('/[0Oo]\s*-\s*[0oO]/', $parts[0])) {
            return "0-0";
        } else {
            return "";
        }
    }
}
?>