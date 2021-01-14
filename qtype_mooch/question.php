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
class qtype_mooch_question extends question_graded_automatically {
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
     * @see question_definition::get_expected_data()
     */
    public function get_expected_data()
    {
        return ['answer' => PARAM_RAW_TRIMMED];
    }

    /**
     * {@inheritDoc}
     * @see question_automatically_gradable::grade_response()
     */
    public function grade_response(array $response)
    {
        $answer = $this->get_matching_answer($response);
        if ($answer) {
            return [$answer->fraction,
                question_state::graded_state_for_fraction($answer->fraction)
            ];
        } else {
            return [0, question_state::$gradedwrong];
        }
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

    /**
     * Gets the answer that matches the given response.
     *
     * @param array $response The response
     * @return question_answer|NULL
     */
    public function get_matching_answer(array $response) {
        $response_move = NotationUtility::normalizeMove($response['answer']);
        /** @var question_answer $answer */
        foreach ($this->answers as $answer) {
            $answer_move = NotationUtility::normalizeMove($answer->answer);
            if ($answer_move == $response_move) {
                return $answer;
            }
        }
        return null;
    }
}
