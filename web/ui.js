/****************************************************************************
 *
 * Pick-Up System - UI Helper Functions
 * $Revision: 1.2 $
 *
 * Copyright (C) 2003, Three Wise Men Software Development and Consulting
 * Written by Steve Vetzal
 *
 * SEE ACCOMPANYING FILES FOR REVISION HISTORY AND FULL LICENSE AGREEMENT
 */

/****************************************************************************
 *
 * WARNING - DO NOT MODIFY THIS FILE - YOU DO SO AT YOUR OWN RISK
 *
 */
 
// Listbox logic

function deselectAll(box) {
	for(var i=0; i<box.options.length; i++) {
		box.options[i].selected = false;
	}
}

function selectAll(box) {
	for(var i=0; i<box.options.length; i++) {
		box.options[i].selected = true;
	}
}

function move(fbox,tbox) {
	for(var i=0; i<fbox.options.length; i++) {
		if(fbox.options[i].selected && fbox.options[i].value != "") {
			var no = new Option();
			no.value = fbox.options[i].value;
			no.text = fbox.options[i].text;
			tbox.options[tbox.options.length] = no;
			fbox.options[i].value = "";
			fbox.options[i].text = "";
		}
	}

	deselectAll(fbox);
	deselectAll(tbox);

	bumpUp(fbox);
}

function bumpUp(box)  {
	for(var i=0; i<box.options.length; i++) {
		if(box.options[i].value == "")  {
			for(var j=i; j<box.options.length-1; j++)  {
				box.options[j].value = box.options[j+1].value;
				box.options[j].text = box.options[j+1].text;
			}
			var ln = i;
			break;
		}
	}
	if(ln < box.options.length)  {
		box.options.length -= 1;
		bumpUp(box);
	}
}

function toTop(box) {
	var temp;

	for (var i = 0; i < box.options.length; i++) {
		if ((i == 0) && (box.options[0].selected == true)) {
			break;
		}

		if (box.options[i].selected == true) {
			var steps = i;

			for (j = 1; j <= steps; j++) {
				moveUp(box);
			}
		}
	}
}

function moveUp(box) {
	var temp;

	for (var i = 0; i < box.options.length; i++) {

		if ((i == 0) && (box.options[0].selected == true)) {
			break;
		}

		if (box.options[i].selected == true) {

			temp = new String(box.options[i-1].value);
			tempText = box.options[i-1].text;
			tempArray = temp.split(",");
			tempID = tempArray[0];

			sel = new String(box.options[i].value);
			selText = box.options[i].text;
			selArray = sel.split(",");
			selID = selArray[0];

			selIndex = tempArray[1];
			tempIndex = selArray[1];

			box.options[i-1] = new Option(selText, selID +","+ selIndex);
			box.options[i] = new Option(tempText, tempID +","+ tempIndex);

			box.options[i-1].selected = true;
		}
	}
}

function moveDown(box) {
	var temp;
	var lastIndex = box.options.length-1;

	for (var i = lastIndex; i >=0; i--) {
		if ((i == lastIndex) && (box.options[lastIndex].selected == true)) {
			break;
		}

		if (box.options[i].selected == true) {

			temp = new String(box.options[i+1].value);
			tempText = box.options[i+1].text;
			tempArray = temp.split(",");
			tempID = tempArray[0];

			sel = new String(box.options[i].value);
			selText = box.options[i].text;
			selArray = sel.split(",");
			selID = selArray[0];

			selIndex = tempArray[1];
			tempIndex = selArray[1];

			box.options[i+1] = new Option(selText, selID +","+ selIndex);
			box.options[i] = new Option(tempText, tempID +","+ tempIndex);

			box.options[i+1].selected = true;
		}
	}
}

function toBottom(box) {
	var temp;
	var lastIndex = box.options.length-1;

	for (var i = lastIndex; i >=0; i--) {
		if ((i == lastIndex) && (box.options[lastIndex].selected == true)) {
			break;
		}

		if (box.options[i].selected == true) {
			var steps = lastIndex - i;

			for (j = 1; j <= steps; j++) {
				moveDown(box);
			}
		}
	}
}

