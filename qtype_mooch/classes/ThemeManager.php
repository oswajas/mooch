<?php

/**
 * Definition of the ThemeManager class.
 *
 * @package    qtype
 * @subpackage mooch
 * @copyright  2021 Oswald Jaskolla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_mooch;

/**
 * Extract chessboard theme information from a configuration object.
 * 
 * @property-read array Theme Associative array with keys 'type',
 *  'builtin_pieces' and 'builtin_board'
 * @see settings.php  
 */
class ThemeManager {
    /**
     * @param object $cfg Configuration object, In the most common case, this
     *  will be the global $CFG object from moodle.
     */
    public function __construct(\stdClass $cfg) {
        $this->cfg = $cfg;
    }

    public function __get(string $Property) {
        switch ($Property) {
          case 'Theme':
            return [
                'type' => $this->cfg->qtype_mooch_themetype ?? "default",
                'builtin_pieces' => $this->cfg->qtype_mooch_builtin_pieceset ?? '',
                'builtin_board' => $this->cfg->qtype_mooch_builtin_board ?? '',
                // 'custom_pieces' => $this->cfg->qtype_mooch_custom_pieceset ?? '',
                // 'custom_board' => $this->cfg->qtype_mooch_custom_board ?? '',
            ];
          default:
            throw new \coding_exception("Unknown property ThemeManager::$Property");
        }
    }
    
    private $cfg;
}