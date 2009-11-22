/**
 * script file for sch printer gui
 */
 
function collapse(ElemId) {
	var elem = document.getElementById(ElemId);
	if (elem.style.display == '')
		elem.style.display = 'none';
	else
		elem.style.display = '';
}

function delConfirm(page, id) {
	if (confirm("Biztos, hogy törlöd a nyomtatót\nTipp: csak állítsd offline-ra a kliensben, hogy késõbb ne kelljen újra felvenni!"))
		window.location = page + '?p=delPrinter&id=' + id;
}

function checkForm(oForm) {
	//get type is selected
	var selected_type = false;
	var i=0;
	while (i < oForm.elements['type'].length && !oForm.elements['type'][i].checked) ++i;
	if (i < oForm.elements['type'].length) selected_type = true;
	
	//get colors are selected
	var selected_colors = false;
	while (i < oForm.elements['colors[]'].length && !oForm.elements['colors[]'][i].checked) ++i;
	if (i < oForm.elements['colors[]'].length) selected_colors = true;
	
	//if data is missing: alert or error in div or whatever

	if (!selected_type) {
		alert('Típust kötelezõ kiválasztani!');
		return false;
	}
	if (!selected_colors) {
		alert('Szín(eke)t kötelezõ kiválasztani!');
		return false;
	}
	if (oForm.elements['model'].value.length < 3) {
		alert('Modell min. 3 karakter');
		return false;
	}
	if (oForm.elements['loc'].value.length < 3) {
		alert('Szoba min. 3 karakter');
		return false;
	}
	//return bool
}
