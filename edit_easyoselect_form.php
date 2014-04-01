<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the editing form for the easyoselect question type.
 *
 * @package    qtype
 * @subpackage easyoselect
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/shortanswer/edit_shortanswer_form.php');


/**
 * Calculated question type editing form definition.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_easyoselect_edit_form extends qtype_shortanswer_edit_form {

    protected function definition_inner($mform) {
        global $PAGE, $CFG;
        $PAGE->requires->js('/question/type/easyoselect/easyoselect_script.js');
        $PAGE->requires->css('/question/type/easyoselect/easyoselect_styles.css');

        $mform->addElement('static', 'answersinstruct',
                get_string('correctanswers', 'qtype_easyoselect'),
                get_string('filloutoneanswer', 'qtype_easyoselect'));
        $mform->closeHeaderBefore('answersinstruct');

        $easyoselectbuildstring = "\n<script LANGUAGE=\"JavaScript1.1\" SRC=\"../../marvin/marvin.js\"></script>
        <script LANGUAGE=\"JavaScript1.1\">
        msketch_name = \"MSketch\";
        msketch_begin(\"../../marvin\", 650, 460);
        msketch_param(\"menuconfig\", \"customization_mech_instructor.xml\");
        msketch_param(\"background\", \"#ffffff\");
        msketch_param(\"sketchCarbonVisibility\", \"off\");
        msketch_param(\"rendering\", \"wireframe\");
        msketch_param(\"molbg\", \"#ffffff\");
        msketch_end();
        </script> ";

        // Output the marvin applet.
        $mform->addElement('html', html_writer::start_tag('div', array('style' => 'width:650px;')));
        $mform->addElement('html', html_writer::start_tag('div', array('style' => 'float: right;font-style: italic ;')));
        $mform->addElement('html', html_writer::start_tag('small'));
        $easyoselecthomeurl = 'http://www.chemaxon.com';
        $mform->addElement('html', html_writer::link($easyoselecthomeurl, get_string('easyoselecteditor', 'qtype_easyoselect')));
        $mform->addElement('html', html_writer::empty_tag('br'));
        $mform->addElement('html', html_writer::end_tag('small'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', $easyoselectbuildstring);
        $mform->addElement('html', html_writer::end_tag('div'));

        // Add structure to applet.
        $jsmodule = array(
            'name'     => 'qtype_easyoselect',
            'fullpath' => '/question/type/easyoselect/easyoselect_script.js',
            'requires' => array(),
            'strings' => array(
                array('enablejava', 'qtype_easyoselect')
            )
        );

        $PAGE->requires->js_init_call('M.qtype_easyoselect.insert_structure_into_applet',
                                      array(),
                                      true,
                                      $jsmodule);

        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_easyoselect', '{no}'),
                question_bank::fraction_options());

        $this->add_interactive_settings();
    }

    protected function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {

        $repeated = parent::get_per_answer_fields($mform, $label, $gradeoptions,
                $repeatedoptions, $answersoption);

        // Construct the insert button.
        $scriptattrs = 'onClick = "getSmilesEdit(this.name, \'mrv:S\')"';
        $insertbutton = $mform->createElement('button', 'insert',
        get_string('insertfromeditor', 'qtype_easyoselect'), $scriptattrs);

        array_splice($repeated, 2, 0, array($insertbutton));

        return $repeated;
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        return $question;
    }

    public function qtype() {
        return 'easyoselect';
    }
}