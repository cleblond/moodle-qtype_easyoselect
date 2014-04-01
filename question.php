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
 * easyoselect Molecular Editor question definition class.
 *
 * @package    qtype
 * @subpackage easyoselect
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $qa;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/shortanswer/question.php');

class qtype_easyoselect_question extends qtype_shortanswer_question {
    // All comparisons in easyoselect are case sensitive.
    public function compare_response_with_answer(array $response, question_answer $answer) {
        global $DB;

        if (!array_key_exists('answer', $response) || is_null($response['answer'])) {

            return false;
        }

        $cmlans = new SimpleXMLElement($answer->answer);
        $cmlusr = new SimpleXMLElement($response['answer']);
        $i = 0;
        $arrowsusrall = "";

        // Check selected atoms.
        $selectedans = explode(" ", $cmlans->MDocument[0]->MChemicalStruct[0]->molecule[0]->atomArray[0]->attributes()->isSelected);
        $selectedansatomid = explode(" ",
        $cmlans->MDocument[0]->MChemicalStruct[0]->molecule[0]->atomArray[0]->attributes()->atomID);
        @$selectedansarray = array_combine($selectedansatomid, $selectedans);

        $selectedusr = explode(" ", $cmlusr->MDocument[0]->MChemicalStruct[0]->molecule[0]->atomArray[0]->attributes()->isSelected);
        $selectedusratomid = explode(" ",
        $cmlusr->MDocument[0]->MChemicalStruct[0]->molecule[0]->atomArray[0]->attributes()->atomID);
        @$selectedusrarray = array_combine($selectedusratomid, $selectedusr);

        if ( $selectedansarray !== $selectedusrarray ) {
            return 0;
        }

        // Check for MEFlow arrows selected.

        $meflowans = $cmlans->MDocument[0]->MEFlow;
        $meflowusr = $cmlusr->MDocument[0]->MEFlow;

        // Quick check to see if number of selction matches.
        if (count($meflowans) !== count($meflowusr)) {
            return 0;
        }

        $selmeflowans = '';
        // More ehaustive check!
        for ($i = 0; $i < count($meflowans); $i++) {
            $selmeflowans = $selmeflowans.",".$meflowans[$i]->attributes()->isSelected;
        }

        $selmeflowusr = '';
        for ($i = 0; $i < count($meflowusr); $i++) {
            $selmeflowusr = $selmeflowusr.",".$meflowusr[$i]->attributes()->isSelected;
        }

        if ($selmeflowusr === $selmeflowans) {
            return 1;
        } else {
            return 0;
        }
    }

    public function get_expected_data() {

        return array('answer' => PARAM_RAW, 'easyoselect' => PARAM_RAW, 'mol' => PARAM_RAW);
    }
}
