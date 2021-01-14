<?php

/**
 * Definition of the JavaScriptManager class.
 *
 * @package    qtype
 * @subpackage mooch
 * @copyright  2021 Oswald Jaskolla
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_mooch;

/**
 * Allows inclusion of ES6 modules in moodle without the need to transcompile
 * them to AMD.
 *
 * The qtype_mooch plugin uses ES6 modules for its javascript needs because
 * Chessground produces errors when translated from TypeScript to AMD via ES6.
 *
 * This feature assumes availabilitay of dynamic imports in the browser.
 */
class JavaScriptManager {
    /**
     * Creates a new JavaScriptManager
     * 
     * @param \moodle_page $page The page for wich JavaScript is managed.
     * @param string $plugin_root The URL of the plugin folder
     */
    public function __construct(\moodle_page $page, string $plugin_root) {
        $this->page = $page;
        $this->plugin_root = $plugin_root;
    }
   
    /**
     * Include a JavaScript file.
     *
     * @param string $module The URL of the file to include, relative to $this->plugin_root
     */
    public function include(string $module) {
        $module_url = "$this->plugin_root/$module";
       
        $args = (object)['module' => $module_url];
        $jscode = str_replace("_args_", json_encode($args), self::$js_template_include);
        $this->page->requires->js_init_code($jscode, false);
    }
   
    /**
     * Call a function in a JavaScript file.
     * 
     * @param string $module The URL of the file to include, relative to $this->plugin_root
     * @param string $function The name of the function to call within the module
     * @param string $argv Array of arguments that are passed to $function
     */
    public function call($module, $function, $argv = []) {
        $module_url = "$this->plugin_root/$module";
       
        $args = (object)[
            'module' => $module_url,
            'f' => $function,
            'argv' => $argv
        ];
        $jscode = str_replace("_args_", json_encode($args), self::$js_template_call);
        $this->page->requires->js_init_code($jscode, false);
    }
   
    /**
     * Make a translatable string available to JavaScript.
     *
     * The string will be available in the global variable
     *
     *     M.str.$component.$identifier.
     *
     * @param string $identifier THe identfieer of the string
     * @param string $component The component that defines the string
     */
    public function include_string(string $identifier, string $component) {
        $this->page->requires->string_for_js($identifier, $component);
    }
   
    private $page;
    private $plugin_root;
    private static $js_template_call = '(function(a){import(a.module).then((m)=>{m[a.f].apply(null, a.argv);});})(_args_);';
    private static $js_template_include = '(function(a){import(a.module);})(_args_);';
}
