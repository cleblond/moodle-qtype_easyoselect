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
 * easyoselect question renderer class.
 *
 * @package    qtype
 * @subpackage easyoselect
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class qtype_easyoselect_renderer extends qtype_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {
        global $CFG, $PAGE;
        $question = $qa->get_question();
        $questiontext = $question->format_questiontext($qa);
        $placeholder = false;
        $myanswerid = "my_answer".$qa->get_slot();
        $correctanswerid = "correct_answer".$qa->get_slot();

        if (preg_match('/_____+/', $questiontext, $matches)) {
            $placeholder = $matches[0];
        }

        $name2 = 'EASYOSELECT'.$qa->get_slot();
        $result = '';
        if ($options->readonly) {
            $result .= html_writer::tag('input', '', array('type' => 'button', 'value' => 'My Response',
            'onClick' => 'var s = document.getElementById("'.$myanswerid.'").value;
            document.getElementById("'.$name2.'").setMol(s, "mrv");'));
            $result .= html_writer::tag('input', '', array('type' => 'button', 'value' => 'Correct Answer',
            'onClick' => 'var s = document.getElementById("'.$correctanswerid.'").value;
            document.getElementById("'.$name2.'").setMol(s, "mrv");'));
            $result .= html_writer::tag('BR', '', array());
        }

        $toreplaceid = 'applet'.$qa->get_slot();
        $toreplace = html_writer::tag('span',
                                      get_string('enablejavaandjavascript', 'qtype_easyoselect'),
                                      array('id' => $toreplaceid));

        if ($placeholder) {
            $toreplace = html_writer::tag('span',
                                      get_string('enablejavaandjavascript', 'qtype_easyoselect'),
                                      array('class' => 'ablock'));
            $questiontext = substr_replace($questiontext,
                                            $toreplace,
                                            strpos($questiontext, $placeholder),
                                            strlen($placeholder));
        }

        $result .= html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        if (!$placeholder) {
            $answerlabel = html_writer::tag('span', get_string('answer', 'qtype_easyoselect', ''),
                                            array('class' => 'answerlabel'));
            $result .= html_writer::tag('div', $answerlabel.$toreplace, array('class' => 'ablock'));
        }

        if ($qa->get_state() == question_state::$invalid) {
            $lastresponse = $this->get_last_response($qa);
            $result .= html_writer::nonempty_tag('div',
                                                $question->get_validation_error($lastresponse),
                                                array('class' => 'validationerror'));
        }

        if (!$options->readonly) {
                $question = $qa->get_question();
                $answertemp = $question->get_correct_response();
            if ($question->hideproducts == 0) {
                $strippedxml = $this->remove_xml_tags($answertemp['answer'], 'MEFlow');
            } else {
                $strippedxml = $this->remove_xml_tags($answertemp['answer'], 'MEFlow');
                $strippedxml = $this->remove_xml_tags($strippedxml, 'productList');
            }

                $strippedanswerid = "stripped_answer".$qa->get_slot();
                $result .= html_writer::tag('textarea', $strippedxml,
                array('id' => $strippedanswerid, 'style' => 'display:none;', 'name' => $strippedanswerid));
        }

        if ($options->readonly) {
                $currentanswer = $qa->get_last_qt_var('answer');
                $strippedanswerid = "stripped_answer".$qa->get_slot();
                $result .= html_writer::tag('textarea', $currentanswer, array('id' => $strippedanswerid,
                'style' => 'display:none;', 'name' => $strippedanswerid));
                $answertemp = $question->get_correct_response();

                // Hack - Buttons to show correct and user answers!
                $result .= html_writer::tag('textarea', $qa->get_last_qt_var('answer'),
                array('id' => $myanswerid, 'name' => $myanswerid, 'style' => 'display:none;'));

                $result .= html_writer::tag('textarea', $answertemp['answer'],
                array('id' => $correctanswerid, 'name' => $correctanswerid, 'style' => 'display:none;'));
        }

        $result .= html_writer::tag('div',
                                    $this->hidden_fields($qa),
                                    array('class' => 'inputcontrol'));

        $this->require_js($toreplaceid, $qa, $options->readonly, $options->correctness);

        return $result;
    }






    protected function remove_xml_tags ($xmlstring, $tag) {
        $dom = new SimpleXMLElement($xmlstring);

        // Remove any selections from atoms...
        $pieces = explode(" ", $dom->MDocument[0]->MChemicalStruct[0]->molecule[0]->atomArray[0]->attributes()->isSelected);
        $allfalse = '';

        for ($i = 0; $i <= count($pieces) - 1; $i++) {
            $allfalse = $allfalse." false";
        }
        $dom->MDocument[0]->MChemicalStruct[0]->molecule[0]->atomArray[0]->attributes()->isSelected = $allfalse;

        // Remove any selections of MeFlow arrows...
        $meflow = $dom->MDocument[0]->MEFlow;
        for ($i = 0; $i <= count($meflow) - 1; $i++) {
            $meflow[$i]->attributes()->isSelected = "false";
        }

        return $dom->saveXML();
    }

    protected function require_js($toreplaceid, question_attempt $qa, $readonly, $correctness) {
        global $PAGE, $CFG;

        $marvinconfig = get_config('qtype_easyoselect_options');
	$marvinpath = $marvinconfig->path;

        $jsmodule = array(
            'name'     => 'qtype_easyoselect',
            'fullpath' => '/question/type/easyoselect/module.js',
            'requires' => array(),
            'strings' => array(
                array('enablejava', 'qtype_easyoselect')
            )
        );
        $topnode = 'div.que.easyoselect#q'.$qa->get_slot();
        $appleturl = new moodle_url('appletlaunch.jar');
        $feedbackimage = '';

        if ($correctness) {
            $feedbackimage = $this->feedback_image($this->fraction_for_last_response($qa));
        }

        $name = 'EASYOSELECT'.$qa->get_slot();
        $appletid = 'easyoselect'.$qa->get_slot();
        $strippedanswerid = "stripped_answer".$qa->get_slot();

        $PAGE->requires->js_init_call('M.qtype_easyoselect.insert_easyoselect_applet',
                                      array($toreplaceid,
                                            $name,
                                            $appletid,
                                            $topnode,
                                            $appleturl->out(),
                                            $feedbackimage,
                                            $readonly,
                                            $strippedanswerid,
                                            $CFG->wwwroot,
                                            $marvinpath),
                                      false,
                                      $jsmodule);

    }

    protected function fraction_for_last_response(question_attempt $qa) {
        $question = $qa->get_question();
        $lastresponse = $this->get_last_response($qa);
        $answer = $question->get_matching_answer($lastresponse);

        if ($answer) {
            $fraction = $answer->fraction;
        } else {
            $fraction = 0;
        }
        return $fraction;
    }


    protected function get_last_response(question_attempt $qa) {
        $question = $qa->get_question();
        $responsefields = array_keys($question->get_expected_data());
        $response = array();
        foreach ($responsefields as $responsefield) {
            $response[$responsefield] = $qa->get_last_qt_var($responsefield);
        }
        return $response;
    }

    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();

        $answer = $question->get_matching_answer($this->get_last_response($qa));
        if (!$answer) {
            return '';
        }

        $feedback = '';
        if ($answer->feedback) {
            $feedback .= $question->format_text($answer->feedback, $answer->feedbackformat,
                    $qa, 'question', 'answerfeedback', $answer->id);
        }
        return $feedback;
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();
        $answer = $question->get_matching_answer($question->get_correct_response());
        if (!$answer) {
            return '';
        }
    }

    protected function hidden_fields(question_attempt $qa) {
        $question = $qa->get_question();

        $hiddenfieldshtml = '';
        $inputids = new stdClass();
        $responsefields = array_keys($question->get_expected_data());
        foreach ($responsefields as $responsefield) {
            $hiddenfieldshtml .= $this->hidden_field_for_qt_var($qa, $responsefield);
        }
        return $hiddenfieldshtml;
    }
    protected function hidden_field_for_qt_var(question_attempt $qa, $varname) {
        $value = $qa->get_last_qt_var($varname, '');
        $fieldname = $qa->get_qt_field_name($varname);
        $attributes = array('type' => 'hidden',
                            'id' => str_replace(':', '_', $fieldname),
                            'class' => $varname,
                            'name' => $fieldname,
                            'value' => $value);
        return html_writer::empty_tag('input', $attributes);
    }
}
