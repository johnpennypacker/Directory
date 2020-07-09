/**
 * search as-you-type javascript for the directory redesign
 */
function queueAjax(e){nextAjaxSearch=e}function getAjaxQueue(){""!==nextAjaxSearch&&refreshSearchResults(nextAjaxSearch),nextAjaxSearch=""}function updateResultsMessages(e,t){var n,r,a,i;if(a=document.getElementById("page-headline"),(n=document.querySelectorAll(".error"))[0]&&n[0].parentNode.removeChild(n[0]),0<t.length)for(i in(n=document.createElement("div")).className="error",a.parentNode.insertBefore(n,a),a.nextSibling?a.parentNode.insertBefore(n,a.nextSibling):a.parentNode.appendChild(n),n.innerHTML="",t)n.innerHTML+="<p>"+t[i]+"</p>";if((r=document.querySelectorAll(".message"))[0]&&r[0].parentNode.removeChild(r[0]),0<e.length)for(i in(r=document.createElement("div")).className="message",a.nextSibling?a.parentNode.insertBefore(r,a.nextSibling):a.parentNode.appendChild(r),r.innerHTML="",e)r.innerHTML+="<p>"+e[i]+"</p>"}
/**
 * Creates a div with a loading message
 */function displayLoader(){var e=document.createElement("div");e.id="directory-loading",e.innerHTML='<div class="loading"></div> Loading...';var t=document.getElementById("search-string").nextSibling;t.parentNode.insertBefore(e,t)}
/**
 * removes the loading div
 */function removeLoader(){var e=document.getElementById("directory-loading");e.parentNode.removeChild(e)}function clearTimeouts(){for(var e in timers)window.clearTimeout(timers[e])}function refreshSearchResults(r){var a;if(r.length<3)
// don't do ajax for short strings or empty strings
return clearTimeouts(),(a=document.getElementById("results")).innerHTML="",updateResultsMessages(!1,!1),void history.pushState({},"Directory",basePath);if(!0!==ajaxing){if(r!==lastAjaxSearch){lastAjaxSearch=r,// remember the search
ajaxing=!0,displayLoader();var i=new XMLHttpRequest;i.open("GET",basePath+"/?output=json&search_string="+r,!0),clearTimeouts(),i.onload=function(){var e,t;if(200<=i.status&&i.status<400)for(var n in
// Success!
e=JSON.parse(i.responseText),(a=document.getElementById("results")).innerHTML="",updateResultsMessages(e.messages,e.errors),e)1*n==n&&function(t){timers.push(window.setTimeout(function(){var e=document.createElement("div");e.innerHTML=t.card,
//var degree = Math.random() * (1 - -1) + -1;
//wrapper.style['-webkit-transform'] = 'rotate('+degree+'deg)';
t.el.appendChild(e)},100*t.i))}({el:a,card:e[n],i:n});
// append the query to the URL
history.pushState(e,"Results for: "+r,basePath+"?search_string="+r),(
// update the auth url (if it exists)
t=document.querySelectorAll(".auth-message a"))[0]&&(t[0].href=basePath+"/authenticate?destination=%2F%3Fsearch_string="+r),ajaxing=!1,removeLoader(),getAjaxQueue()},i.onerror=function(){
// There was a connection error of some sort
ajaxing=!1,removeLoader(),getAjaxQueue()},i.send()}}else queueAjax(r)}function initLiveSearch(){var e=document.getElementById("search-string"),t;e&&(document.getElementById("results").className="animated",e.autocomplete="off",e.addEventListener("keyup",function(){refreshSearchResults(e.value)}),e.addEventListener("keypress",function(e){if(13===e.keyCode)return e.preventDefault(),!1}))}
// this gives us a way to prevent simultaneous ajax requests
var ajaxing=!1,nextAjaxSearch="",lastAjaxSearch="",timers=[];window.addEventListener("load",initLiveSearch,!1);