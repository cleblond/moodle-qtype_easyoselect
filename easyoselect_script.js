

M.qtype_easyoselect={
    insert_structure_into_applet : function(){
		var textfieldid = 'id_answer_0';
		if(document.getElementById(textfieldid).value != '') {
		
		var s = document.getElementById(textfieldid).value;
		document.MSketch.setMol(s, 'mrv:S');
		}
    },

    insert_applet : function(){

	var warningspan = document.getElementById('appletdiv');
        warningspan.innerHTML = '';

        var newApplet = document.createElement("applet");
        newApplet.code='chemaxon.marvin.applet.JMSketchLaunch';
        newApplet.archive='appletlaunch.jar';
        newApplet.name='MSketch';
        newApplet.width='650';
        newApplet.height='460';
        newApplet.tabIndex = -1; // Not directly tabbable
        newApplet.mayScript = true;     
	newApplet.id = 'MSketch';
	newApplet.setAttribute('codebase','/marvin');

	var param=document.createElement('param');
	param.name='codebase_lookup';
        param.value='false';
	newApplet.appendChild(param);

        var param=document.createElement('param');
	param.name='menubar';
        param.value='false';
	newApplet.appendChild(param);

	var param=document.createElement('param');
	param.name='menuconfig';
        param.value='../eolms/question/type/easyoselect/customization_mech_instructor.xml';
	newApplet.appendChild(param);

	var param=document.createElement('param');
	param.setAttribute('bondDraggedAlong','false');
	newApplet.appendChild(param);

	var param=document.createElement('param');
	param.name='chargeWithCircle';
        param.value='true';
	newApplet.appendChild(param);

	var param=document.createElement('param');
	param.name='defaultTool';
        param.value='electronFlow2';
	newApplet.appendChild(param);

        warningspan.appendChild(newApplet);

    }

}

/*
function getSmiles(textfieldid) {
	document.getElementById(textfieldid).value = document.getElementById('EASYOSELECT' + textfieldid).smiles();
}
*/

/*
function getSmilesEdit(buttonname){
    var buttonnumber= buttonname.slice(7,-1);
	textfieldid = 'id_answer_' + buttonnumber;
	document.getElementById(textfieldid).value = document.getElementById('JME').smiles();
}
*/

/*
///modified by crl for easyoselect sketch
function getSmilesEdit(buttonname, format){
    var buttonnumber = buttonname.slice(7,-1);
    var s = document.MSketch.getMol(format);
//	s = unix2local(s); // Convert "\n" to local line separator
	textfieldid = 'id_answer_' + buttonnumber;
	document.getElementById(textfieldid).value = s;
}
*/



/*
M.qtype_easyoselect={
    insert_structure_into_applet : function(){
		var textfieldid = 'id_answer_0';
		if(document.getElementById(textfieldid).value != '') {
		
		var s = document.getElementById(textfieldid).value;
		document.MSketch.setMol(s, 'mrv');
		}

	}
}

*/


M.qtype_easyoselect.init_getanswerstring = function(Y, moodle_version){
    var handleSuccess = function(o) {

    };
    var handleFailure = function(o) {
        /*failure handler code*/
    };
    var callback = {
        success:handleSuccess,
        failure:handleFailure
    };
    if (moodle_version >= 2012120300) { //Moodle 2.4 or higher
        YAHOO = Y.YUI2;
    }

    Y.all(".id_insert").each(function(node) {
    	node.on("click", function () {
        var buttonid = node.getAttribute('id');
        var s = document.MSketch.getMol('mrv');
	textfieldid = 'id_answer_' + buttonid.substr(buttonid.length - 1);
	document.getElementById(textfieldid).value = s;
    	});
    });
};






