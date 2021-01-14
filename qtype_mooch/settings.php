<?php

/**
 * Admin settings for the chess question type.
 *
 * Settings:
 *
 *  - qtype_mooch_themetype ('default' | 'builtin')
 *    Whether to uses the default theme or one of the builtin ones.
 *   
 *  - qtype_mooch_builtin_pieceset (string)
 *    A subdirectory of question/type/mooch/pix/theme/pieces that contains SVG
 *    for the pieces.
 *   
 *  - qtype_mooch_builtin_board (string)
 *    The name of a file in question/type/mooch/pix/theme/board without file
 *    extension. A file extension of '.svg' will be added to find the board
 *    when it should be displayed.
 * 
 * 
 * @package    qtype
 * @subpackage mooch
 * @copyright  2021 Oswald Jaskolla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see        \qtype_mooch\ThemeManager
 * @todo       Implement custom theme.
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

if ($ADMIN->fulltree) {
    /** @var admin_settingpage $settings */
    $settings->add(new admin_setting_configselect(
        'qtype_mooch_themetype',
        new lang_string('themetype', 'qtype_mooch'),
        new lang_string('themetype_desc', 'qtype_mooch'),
        'default',
        [
            'default' => new lang_string('themetype_default', 'qtype_mooch'),
            'builtin' => new lang_string('themetype_builtin', 'qtype_mooch'),
            // 'custom' => new lang_string('themetype_custom', 'qtype_mooch'),
        ]
    ));
   
    $values = array_filter(scandir(__DIR__.'/pix/theme/pieces'), function($v) {return $v[0] !== '.';});
    $items = array_map(function($v) {return ucfirst($v);}, $values);
    $settings->add(new admin_setting_configselect(
        'qtype_mooch_builtin_pieceset',
        new lang_string('pieceset', 'qtype_mooch'),
        new lang_string('pieceset_desc_builtin', 'qtype_mooch'),
        null,
        array_combine($values, $items)
    ));
    foreach ($values as $value) {
        $name = "pieceset_preview_$value";
        $settings->add(new admin_setting_description($name, '',
            "<p name='s__$name'>"
           ."<img style='width:3rem;height:3rem' src='$CFG->wwwroot/question/type/mooch/pix/theme/pieces/$value/bK.svg'>"
           ."<img style='width:3rem;height:3rem' src='$CFG->wwwroot/question/type/mooch/pix/theme/pieces/$value/wQ.svg'>"
           ."<img style='width:3rem;height:3rem' src='$CFG->wwwroot/question/type/mooch/pix/theme/pieces/$value/bR.svg'>"
           ."<img style='width:3rem;height:3rem' src='$CFG->wwwroot/question/type/mooch/pix/theme/pieces/$value/wB.svg'>"
           ."<img style='width:3rem;height:3rem' src='$CFG->wwwroot/question/type/mooch/pix/theme/pieces/$value/bN.svg'>"
           ."<img style='width:3rem;height:3rem' src='$CFG->wwwroot/question/type/mooch/pix/theme/pieces/$value/wP.svg'>"
           ."</p>"
       ));
       $settings->hide_if($name, 'qtype_mooch_builtin_pieceset', 'neq', $value);
       $settings->hide_if($name, 'qtype_mooch_themetype', 'neq', 'builtin');
    }   
    $settings->hide_if('qtype_mooch_builtin_pieceset', 'qtype_mooch_themetype', 'neq', 'builtin');

    $values = array_filter(scandir(__DIR__.'/pix/theme/boards'), function($v) {return $v[0] !== '.';});
    $values = array_map(function($v) {return preg_replace('/\.[^.]*$/', '', $v);}, $values);
    $items = array_map(ucfirst, $values);
    $settings->add(new admin_setting_configselect(
        'qtype_mooch_builtin_board',
        new lang_string('board', 'qtype_mooch'),
        new lang_string('board_desc_builtin', 'qtype_mooch'),
        null,
        array_combine($values, $items)
    ));
    $settings->hide_if('qtype_mooch_builtin_board', 'qtype_mooch_themetype', 'neq', 'builtin');
    foreach ($values as $value) {
        $sanitized_name = preg_replace('/\.[^.]*$/', '', $value);
        $name = "board_preview_$sanitized_name";
        $settings->add(new admin_setting_description($name, '',
            "<p name='s__$name'>"
            ."<img style='width:3rem;height:3rem' src='$CFG->wwwroot/question/type/mooch/pix/theme/boards/$value.svg'>"
            ."</p>"
            ));
        $settings->hide_if($name, 'qtype_mooch_builtin_board', 'neq', $value);
        $settings->hide_if($name, 'qtype_mooch_themetype', 'neq', 'builtin');
    }
   
//     $settings->add(new admin_setting_configtext(
//         'qtype_mooch_custom_pieceset',
//         new lang_string('pieceset', 'qtype_mooch'),
//         new lang_string('pieceset_desc_custom', 'qtype_mooch'),
//         null
//     ));
//     $settings->hide_if('qtype_mooch_custom_pieceset', 'qtype_mooch_themetype', 'neq', 'custom');

//     $settings->add(new admin_setting_configtext(
//         'qtype_mooch_custom_board',
//         new lang_string('board', 'qtype_mooch'),
//         new lang_string('board_desc_custom', 'qtype_mooch'),
//         null
//         ));
//     $settings->hide_if('qtype_mooch_custom_board', 'qtype_mooch_themetype', 'neq', 'custom');
}
