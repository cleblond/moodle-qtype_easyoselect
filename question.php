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
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $qa;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/shortanswer/question.php');

$generated_feedback="";

/**
 * Represents a easyoselect question.
 *
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_easyoselect_question extends qtype_shortanswer_question {
	// all comparisons in easyoselect are case sensitive
	public function compare_response_with_answer(array $response, question_answer $answer) {
        global $generated_feedback, $DB;


///var_dump($response);
///var_dump($answer);
$test = $DB->get_record('user', array('id'=>'1'));

$order_important = $this->orderimportant;








if (!array_key_exists('answer', $response) || is_null($response['answer'])) {

            return false;
        }


$cmlans = new SimpleXMLElement($answer->answer);
$cmlusr = new SimpleXMLElement($response['answer']);
	$arrows_correct=0;
	$i=0;
	$arrowsusrall="";


///check selected atoms
$selected_ans=explode(" ",$cmlans->MDocument[0]->MChemicalStruct[0]->molecule[0]->atomArray[0]->attributes()->isSelected);
$selected_ans_atom_id=explode(" ",$cmlans->MDocument[0]->MChemicalStruct[0]->molecule[0]->atomArray[0]->attributes()->atomID);
@$selected_ans_array = array_combine($selected_ans_atom_id,$selected_ans);


$selected_usr=explode(" ",$cmlusr->MDocument[0]->MChemicalStruct[0]->molecule[0]->atomArray[0]->attributes()->isSelected);
//echo "value";
//var_dump($selected_usr);
$selected_usr_atom_id=explode(" ",$cmlusr->MDocument[0]->MChemicalStruct[0]->molecule[0]->atomArray[0]->attributes()->atomID);
//echo "atom";
//var_dump($selected_usr_atom_id);
@$selected_usr_array = array_combine($selected_usr_atom_id,$selected_usr);

//echo "ans_array=";
//var_dump($selected_ans_array);
//echo "<br/>usr_array=";
//var_dump($selected_usr_array);


		if ( $selected_ans_array !== $selected_usr_array ) {
//	    	echo 'order important - returned 1';
		return 0;
		}




////check for MEFlow arrows selected

		$meflowans = $cmlans->MDocument[0]->MEFlow;
		$meflowusr = $cmlusr->MDocument[0]->MEFlow;

		///Quick check to see if number of selction matches
		if(count($meflowans) !== count($meflowusr)){return 0;}

		$selmeflowans='';
		///more ehaustive check
		for ($i=0; $i<count($meflowans); $i++){ 
		$selmeflowans = $selmeflowans.",".$meflowans[$i]->attributes()->isSelected;
//		echo "ans".$meflowans[$i]->attributes()->isSelected;	
//		echo $meflowusr[$i]->attributes()->isSelected ."<br";	
		//$meflowans[$i]->attributes()->isSelected = "false";
		}

		$selmeflowusr='';
		for ($i=0; $i<count($meflowusr); $i++){ 
		$selmeflowusr = $selmeflowusr.",".$meflowusr[$i]->attributes()->isSelected;
//		echo "usr".$meflowusr[$i]->attributes()->isSelected;	
//		echo $meflowusr[$i]->attributes()->isSelected ."<br";	
		//$meflowans[$i]->attributes()->isSelected = "false";
		}
//		echo "usr".$selmeflowusr."<br>";
//		echo "ans".$selmeflowans."<br>";

		if ($selmeflowusr === $selmeflowans) {
//	    	echo 'order important - returned 1';
		return 1;
		}
		else{
		return 0;
		}




    }
	
	public function get_expected_data() {

        return array('answer' => PARAM_RAW, 'easyoselect' => PARAM_RAW, 'mol' => PARAM_RAW);
    }
}
