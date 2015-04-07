/* 
*	{SMF Market - simple item marketplace tied into SMF}
*    Copyright (C) {2015}  {Ryan 'iBeNifty' Malacina}
*	 You can find the full license included with these files.
*/

function isNormalInteger(str) {
    var n = ~~Number(str);
    return String(n) === str && n >= 0;
}

function numbersonly(myfield, e, dec) {
	var key;
	var keychar;

	if (window.event)
		key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;
	keychar = String.fromCharCode(key);

	// control keys
	if ((key==null) || (key==0) || (key==8) || 
		(key==9) || (key==13) || (key==27) )
	return true;

	// numbers
	else if ((("0123456789").indexOf(keychar) > -1))
		return true;

	// decimal point jump
	else if (dec && (keychar == ".")) {
		myfield.form.elements[dec].focus();
		return false;
	}
	else
		return false;
}

function validateSearch() {
	var x = document.forms["searchForm"]["search"].value;
	if (!x.trim()) {
		alert("You must search for something!");
		return false;
	}
}

function validateItem() {
	var x = document.forms["post-offer"]["item"].value;
	if (!x.trim()) {
		alert("You must give an item name!");
		return false;
	}
}