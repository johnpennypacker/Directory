/**
 * search as-you-type javascript for the directory redesign
 */
(function () {
	'use strict';
}());

// this gives us a way to prevent simultaneous ajax requests
var ajaxing = false;
var nextAjaxSearch = '';
var lastAjaxSearch = '';
var timers = [];

function queueAjax(query) {
	nextAjaxSearch = query;
}

function getAjaxQueue() {
	if(nextAjaxSearch !== '') {
		refreshSearchResults(nextAjaxSearch);
	}
	nextAjaxSearch = '';
}



function updateResultsMessages(messages, errors) {
	var err, m, s, x;
	s = document.getElementById('page-headline');

	err = document.querySelectorAll('.error');
	if(err[0]) {
		err[0].parentNode.removeChild(err[0]);
	}
	if(errors.length > 0) {
		err = document.createElement('div');
		err.className = 'error';
		s.parentNode.insertBefore(err, s);
		if (s.nextSibling) {
			s.parentNode.insertBefore(err, s.nextSibling);
		}
		else {
			s.parentNode.appendChild(err);
		}
		err.innerHTML = '';
		for(x in errors) {
			err.innerHTML += '<p>' + errors[x] + '</p>';
		}
	} 
	
	m = document.querySelectorAll('.message');
	if(m[0]) {
		m[0].parentNode.removeChild(m[0]);
	}
	if(messages.length > 0) {
		m = document.createElement('div');
		m.className = 'message';
		if (s.nextSibling) {
			s.parentNode.insertBefore(m, s.nextSibling);
		}
		else {
			s.parentNode.appendChild(m);
		}
		m.innerHTML = '';
		for(x in messages) {
			m.innerHTML += '<p>' + messages[x] + '</p>';
		}
	}

}


/**
 * Creates a div with a loading message
 */
function displayLoader() {
	var d = document.createElement('div');
	d.id = 'directory-loading';
	d.innerHTML = '<div class="loading"></div> Loading...';
	var s = document.getElementById('search-string').nextSibling;
	s.parentNode.insertBefore(d, s);
}

/**
 * removes the loading div
 */
function removeLoader() {
	var loader = document.getElementById('directory-loading');
	loader.parentNode.removeChild(loader);
}

function clearTimeouts() {
	for(var i in timers) {
		window.clearTimeout(timers[i]);
	}
}

function refreshSearchResults(searchString) {
	var resultsEl;
	
	if(searchString.length < 3) {
		// don't do ajax for short strings or empty strings
		clearTimeouts();
		resultsEl = document.getElementById('results');
		resultsEl.innerHTML = '';
		updateResultsMessages(false, false);
		history.pushState({}, 'Directory', basePath);
		return;
	}
	
	if(ajaxing === true) {
		queueAjax(searchString);
		return;
	}
	
	if(searchString === lastAjaxSearch) {
		// no sense searching for the same query twice in a row
		return;
	}
	lastAjaxSearch = searchString; // remember the search

	ajaxing = true;
	displayLoader();
	var request = new XMLHttpRequest();
	request.open('GET', basePath + '/?output=json&search_string=' + searchString, true);

	clearTimeouts();

	request.onload = function() {
		var data, authLink;
		if(request.status >= 200 && request.status < 400) {
			// Success!
			data = JSON.parse(request.responseText);
			resultsEl = document.getElementById('results');
			resultsEl.innerHTML = '';
			updateResultsMessages(data.messages, data.errors);
			for(var x in data) {
				if(x*1 != x) { // only display results with a numeric index (not errors / messages)
					continue;
				}
				(function(arg){
					timers.push(window.setTimeout(function() {
						var wrapper = document.createElement('div');
						wrapper.innerHTML = arg.card;
						//var degree = Math.random() * (1 - -1) + -1;
						//wrapper.style['-webkit-transform'] = 'rotate('+degree+'deg)';
						arg.el.appendChild(wrapper);
					}, (100*arg.i)));
				}({'el': resultsEl, 'card': data[x], 'i': x}));
			}
		} else {
			// We reached our target server, but it returned an error
			//console.log(request);
		}
		// append the query to the URL
		history.pushState(data, 'Results for: ' + searchString, basePath + '?search_string=' + searchString);
		// update the auth url (if it exists)
		authLink = document.querySelectorAll('.auth-message a');
		if(authLink[0]) {
			authLink[0].href = basePath + '/authenticate?destination=%2F%3Fsearch_string=' + searchString;
		}
		ajaxing = false;
		removeLoader();
		getAjaxQueue();
	};

	request.onerror = function() {
		// There was a connection error of some sort
		ajaxing = false;
		removeLoader();
		getAjaxQueue();
	};

	request.send();

}

function initLiveSearch() {
	var el = document.getElementById('search-string');
	if(!el) {
		return;
	}
	
	var r = document.getElementById('results');
	r.className = 'animated';

	el.autocomplete = 'off';
	el.addEventListener('keyup', function() {
		refreshSearchResults(el.value);
	});

	el.addEventListener('keypress', function(e) {
		if(e.keyCode === 13) {
			e.preventDefault();
			return false;
		}
	});
}


window.addEventListener('load', initLiveSearch, false);
