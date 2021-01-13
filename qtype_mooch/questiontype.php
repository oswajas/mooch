<?php 

/**
 * Definition of the qtype_mooch class.
 *
 * @package    qtype
 * @subpackage mooch
 * @copyright  2021 Oswald Jaskolla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \qtype_mooch\JavaScriptManager;
use \qtype_mooch\ThemeManager;

/**
 * The chess question type class.
 */
class qtype_mooch extends question_type {
    /**
     * {@inheritDoc}
     * @see question_type::extra_question_fields()
     */
    public function extra_question_fields() {
        return ['question_mooch', 'fen'];
    }
    
    /**
     * {@inheritDoc}
     * @see question_type::questionid_column_name()
     */
    public function questionid_column_name() {
        return 'questionid';
    }
    
    /**
     * {@inheritDoc}
     * @see question_type::display_question_editing_page()
     */
    public function display_question_editing_page($mform, $question, $wizardnow) {
        global $CFG, $PAGE;

        $config = [
            'highlight' => [
                'lastMove' => false,
            ],
            'draggable' => [
                'deleteOnDropOff' => true
            ],
            'drawable' => [
                'enabled' => false,
            ],
        ];

        $plugin_url = "$CFG->wwwroot/question/type/mooch";

        $thmgr = new ThemeManager($CFG);
        $jsmgr = new JavaScriptManager($PAGE, $plugin_url);
        $jsmgr->call('js/chess-question-edit.js', 'init', [$config, $thmgr->Theme]);
        $jsmgr->include_string('reset', 'moodle');
        $jsmgr->include_string('sidetomove', 'qtype_mooch');
        $jsmgr->include_string('white', 'qtype_mooch');
        $jsmgr->include_string('black', 'qtype_mooch');

        parent::display_question_editing_page($mform, $question, $wizardnow);
    }
    
    /**
     * {@inheritDoc}
     * @see question_type::save_question_options()
     */
    public function save_question_options($question) {
        parent::save_question_options($question);
        $this->save_question_answers($question);
    }
    
    /**
     * {@inheritDoc}
     * @see question_type::initialise_question_instance()
     */
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
    
        $question->answers = array();
        if (empty($questiondata->options->answers)) {
            return;
        }
        foreach ($questiondata->options->answers as $a) {
            $question->answers[$a->id] = new question_answer($a->id, $a->answer,
                $a->fraction, $a->feedback, $a->feedbackformat);
        }
    }
}
