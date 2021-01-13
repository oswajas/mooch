<?php

/**
 * Defines the editing form for the chess question type.
 *
 * @package    qtype
 * @subpackage mooch
 * @copyright  2021 Oswald Jaskolla <post@osjas.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \qtype_mooch\chess\NotationUtility;
use \qtype_mooch\util\UID;

/**
 * Chess question type editing form definition.
 */
class qtype_mooch_edit_form extends question_edit_form {
    /**
     * Add any question-type specific form fields.
     * 
     * @param MoodleQuickForm $mform the form being built.
     * @see question_edit_form::definition_inner()
     */
    protected function definition_inner($mform) {
        /** @var renderer_base $OUTPUT */
        global $OUTPUT;
        
        $this->removeRequired($mform, 'name');
        $this->removeRequired($mform, 'questiontext');
        
        $uid = UID::weak();

        $chesswidget = $OUTPUT->render_from_template('qtype_mooch/edit', ['uid' => $uid]);
        $element_position = $mform->createElement('static', 'position',
            get_string('position', 'qtype_mooch'), $chesswidget
        );
        $mform->insertElementBefore($element_position, 'questiontext');

        $element_fen = $mform->createElement('text', 'fen',
            get_string('fen', 'qtype_mooch'),
            ['size' => 50,'maxlength' => 128,'data-qtype-mooch-id-fen-field' => $uid]
            );
        $mform->insertElementBefore($element_fen, 'questiontext');

        $this->add_per_answer_fields($mform, get_string('answer', 'qtype_mooch'),
            question_bank::fraction_options(), 1, 1);
    }

    /**
     * {@inheritDoc}
     * @see question_edit_form::data_preprocessing()
     */
    protected function data_preprocessing($question) {
        return $this->data_preprocessing_answers($question);
    }

    /**
     * {@inheritDoc}
     * @see question_edit_form::qtype()
     */
    public function qtype()
    {
        return 'chess';
    }

    /**
     * {@inheritDoc}
     * @see question_edit_form::validation()
     */
    public function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);

        if (!NotationUtility::validateFen($fromform['fen'])) {
            $errors['fen'] = get_string('invalidfen', 'qtype_mooch');
        }
        
        return $errors;
    }

    /**
     * Remove the "required" state from a form element
     *
     * @param MoodleQuickForm $mform The form
     * @param string $name The name of the form element
     */
    private function removeRequired(MoodleQuickForm $mform, string $name) {
        unset($mform->_rules[$name]);
        while (($key = array_search($name, $mform->_required)) !== false) {
            unset($mform->_required[$key]);
        }
    }
}
