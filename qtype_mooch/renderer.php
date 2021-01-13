<?php

/**
 * Definition of the qtype_mooch_renderer class.
 * 
 * @package    qtype
 * @subpackage mooch
 * @copyright  2021 Oswald Jaskolla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \qtype_mooch\util\UID;
use \qtype_mooch\JavaScriptManager;
use \qtype_mooch\ThemeManager;

/**
 * The renderer for displaying chess questions.
 */
class qtype_mooch_renderer extends qtype_renderer {
    /**
     * {@inheritDoc}
     * @see qtype_renderer::formulation_and_controls()
     */
    public function formulation_and_controls(
        question_attempt $qa,
        question_display_options $options)
    {
        /** @var moodle_page */
        global $PAGE;
        /** @var renderer_base */
        global $OUTPUT;
        /** @var qtype_mooch_question $question */
        $question = $qa->get_question();
        
        $this->require_chess_attempt($PAGE, $options);
        
        $context = [
            'answer_field_name' => $qa->get_qt_field_name('answer'),
            'attempted_answer' => $qa->get_last_qt_var('answer'),
            'attempt_correct' => $qa->get_state()->is_correct(),
            'uid' => UID::weak(),
            'readonly' => $options->readonly,
            'fen' => $question->fen,
            'correctness' => ($options->correctness == question_display_options::VISIBLE),
            'correct_answer' => $question->get_correct_response()['answer']
        ];
        
        $html = parent::formulation_and_controls($qa, $options);
        $html .= $OUTPUT->render_from_template('qtype_mooch/show', $context);
        
        return $html;
    }

    /**
     * Include the javascript the is needed to show the chess board.
     * 
     * @param moodle_page $page The page on which the chess board is displayed
     * @param question_display_options $options Display options
     */
    private function require_chess_attempt(moodle_page $page, question_display_options $options) {
        global $CFG;
        
        if (self::$js_included) {
            return;
        } else {
            self::$js_included = true;
        }

        $config = [
            'viewOnly' => false,
            'highlight' => [
                'lastMove' => false,
                'check' => false,
            ],
            'movable' => [
                'free' => true,
                'color' => 'both',
                'showDests' => false,
            ],
            'premovable' => [
                'enabled' => false,
                'showDests' => false,
            ],
            'draggable' => [
                'enabled' => true,
            ],
            'selectable' => [
                'enabled' => true,
            ],
            'drawable' => [
                'enabled' => true,
            ],
        ];

        if ($options->readonly) {
            $config['viewOnly'] = true;
        }

        $plugin_url = "$CFG->wwwroot/question/type/mooch";
        
        $thmgr = new ThemeManager($CFG);
        $jsmgr = new JavaScriptManager($page, $plugin_url);
        $jsmgr->call("js/chess-question-attempt.js", 'init', [$config, $thmgr->Theme]);
        $jsmgr->include_string('reset', 'moodle');
        $jsmgr->include_string('whitetomove', 'qtype_mooch');
        $jsmgr->include_string('blacktomove', 'qtype_mooch');
    }

    private static $js_included = false;
}
