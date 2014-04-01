

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




M.qtype_easyoselect={
    insert_structure_into_applet : function(){
		var textfieldid = 'id_answer_0';
		if(document.getElementById(textfieldid).value != '') {
		
		var s = document.getElementById(textfieldid).value;
		document.MSketch.setMol(s, 'mrv');
		}

	}
}
