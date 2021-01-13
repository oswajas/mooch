<?php

/**
 * Defines the qtype_mooch_question class.
 *
 * @package    qtype
 * @subpackage mooch
 * @copyright  2021 Oswald Jaskolla <post@osjas.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \qtype_mooch\chess\NotationUtility;

/**
 * Contrary to its name, this class is mainly concerned with handling responses
 * to chess questions. 
 */
class qtype_mooch_question extends question_definition implements question_automatically_gradable {
    /**
     * {@inheritDoc}
     * @see question_definition::get_correct_response()
     */
    public function get_correct_response()
    {
        foreach ($this->answers as $answer) {
            $state = question_state::graded_state_for_fraction($answer->fraction);
            if ($state == question_state::$gradedright) {
                return ['answer' => $answer->answer];
            }
        }
        return null;
    }

    /**
     * {@inheritDoc}
     * @see question_definition::get_expected_data()
     */
    public function get_expected_data()
    {
        return ['answer' => PARAM_RAW_TRIMMED];
    }
    
    /**
     * {@inheritDoc}
     * @see question_manually_gradable::is_same_response()
     */
    public function is_same_response(array $prevresponse, array $newresponse)
    {
        if (isset($prevresponse['answer']) && isset($newresponse['answer'])) {
            return NotationUtility::normalizeMove($prevresponse['answer'])
                == NotationUtility::normalizeMove($newresponse['answer']);
        } else  {
            return !isset($prevresponse['answer']) && !isset($newresponse['answer']);
        }
    }

    /**
     * {@inheritDoc}
     * @see question_manually_gradable::is_complete_response()
     */
    public function is_complete_response(array $response)
    {
        return (!empty(NotationUtility::normalizeMove($response['answer'])));
    }

    /**
     * {@inheritDoc}
     * @see question_automatically_gradable::get_validation_error()
     */
    public function get_validation_error(array $response)
    {
        if (empty(trim($response['answer']))) {
            return get_string('answerisempty', 'qtype_mooch');
        } else {
            return get_string('answerisnomove', 'qtype_mooch');
        }
    }

    /**
     * {@inheritDoc}
     * @see question_manually_gradable::classify_response()
     * @todo What's qtype_mooch_question::classify_response() for?
     */
    public function classify_response(array $response)
    {
        return [];
    }

    /**
     * {@inheritDoc}
     * @see question_automatically_gradable::get_right_answer_summary()
     * @todo What's qtype_mooch_question::get_right_answer_summary() for?
     */
    public function get_right_answer_summary()
    {
        return $this->get_correct_response()['answer'];
    }

    /**
     * {@inheritDoc}
     * @see question_automatically_gradable::grade_response()
     */
    public function grade_response(array $response)
    {
        $response_move = NotationUtility::normalizeMove($response['answer']);
        /** @var question_answer $answer */
        foreach ($this->answers as $answer) {
            $answer_move = NotationUtility::normalizeMove($answer->answer);
            if ($answer_move == $response_move) {
                return [
                    $answer->fraction,
                    question_state::graded_state_for_fraction($answer->fraction)
                ];
            }
        }
        return [0, question_state::$gradedwrong];
    }

    /**
     * {@inheritDoc}
     * @see question_manually_gradable::is_gradable_response()
     */
    public function is_gradable_response(array $response)
    {
        return $this->is_complete_response($response);
    }

    /**
     * {@inheritDoc}
     * @see question_automatically_gradable::get_hint()
     * @todo What's qtype_mooch_question::get_hint() for?
     */
    public function get_hint($hintnumber, question_attempt $qa)
    {
        return '';
    }

    /**
     * {@inheritDoc}
     * @see question_manually_gradable::summarise_response()
     */
    public function summarise_response(array $response)
    {
        return $response['answer'];
    }

    /**
     * {@inheritDoc}
     * @see question_manually_gradable::un_summarise_response()
     */
    public function un_summarise_response(string $summary)
    {
        return ['answer' => $summary];
    }
}
