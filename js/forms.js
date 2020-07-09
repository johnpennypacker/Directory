/**
 * create a tabbed-interface with search forms
 */
(function () {
	'use strict';
}());

var tabs;


function getQueryVariable(variable) {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0;i<vars.length;i++) {
		var pair = vars[i].split("=");
		if(pair[0] === variable){return pair[1];}
	}
	return(false);
}



function getFieldSets() {
	var fieldsets = document.querySelectorAll('#forms fieldset');
	var f = [];
	for(var x in fieldsets) {
		if(fieldsets[x].nodeName !== 'FIELDSET') { continue; }
		f.push(fieldsets[x]);
	}
	return f;
}





function setUpInitialTab(str, fieldsets) {
	if(str === 'advanced') {
		fieldsets[1].style.display = 'block';
		tabs.firstChild.nextSibling.className = 'active';
	} else {
		fieldsets[0].style.display = 'block';
		tabs.firstChild.className = 'active';
	}
}


function hideFieldSets() {
	var f = getFieldSets();
	for(var x in f) {
		f[x].style.display = 'none';
	}
	tabs.firstChild.className = '';
	tabs.firstChild.nextSibling.className = '';
}


function tabClickHandler(el, a) {
	hideFieldSets();
	if(el.style.display === 'none') {
		el.style.display = 'block';
	} else {
		el.style.display = 'none';
	}
	a.parentNode.className = 'active';
}



function initTabs() {
	var forms = document.getElementById('forms');
	if(!forms) {
		return;
	}
	var fieldsets = getFieldSets();
	tabs = document.createElement('ul');
	tabs.id = 'tabs';
	

	for(var x in fieldsets) {
		// let's get a little closure
		(function() {
			var legends = fieldsets[x].getElementsByTagName('legend');
			var a = document.createElement('a');
			a.innerHTML = legends[0].innerHTML;
			
			var fieldset = fieldsets[x];
			var link = a;
			a.addEventListener('click', function(){
				tabClickHandler(fieldset, link);
			});

			var l = document.createElement('li');
			l.appendChild(a);
			tabs.appendChild(l);
		}());
		// ooh, that felt terrific
	}
	forms.insertBefore(tabs, forms.firstChild);
	hideFieldSets();

	if(getQueryVariable('rm') === 'advanced') {
		setUpInitialTab('advanced', fieldsets);
	} else {
		setUpInitialTab('basic', fieldsets);
	}
	
}

window.addEventListener('load', initTabs, false);

