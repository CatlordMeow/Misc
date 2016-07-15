// @name vbb-utils
// @version 1.2
// eslint-disable-next-line
// @description Helper script for vBulletin 4.2 forums. Bells and whistlers.
// @author Catlord Meow!, http://sovserv.su/member.php?u=237
// @license GNU GPL v3; http://www.gnu.org/copyleft/gpl.html

(function closure() {

"use strict";

var
  sClickToExpand = "Кликните, чтобы развернуть",
	sShowBigger = "Показать в полном размере (%s x %s)",
	sShowSmaller = "Показано в полном размере.\n" +
		"Кликните, чтобы уменьшить до нормального размера";

function def(v) {
return typeof v !== "undefined";
}

function setText(elem, text) {
if (def(elem.textContent)) { elem.textContent = text; } else
if (def(elem.innerText)) { elem.innerText = text; }
}

function getNaturalHW(elem) {
if (def(elem.naturalWidth)) {
	return { width: elem.naturalWidth, height: elem.naturalheight };
}
var img = new Image();
img.src = elem.src;
return { width: img.width, height: img.height };
}

function fixAddEvent(o) {
if (o && !o.addEventListener) {
	o.addEventListener = function addEventListenerFix(e, f) {
		this.attachEvent("on" + e, f);
	};
}
return o;
}

function $(elid) {
return document.getElementById(elid);
}

function addClick(e, f) {
var m = e;
if (typeof m === "string") { m = $(e); }
fixAddEvent(m).addEventListener("click", f);
}

// Add a getElementsByClassName function if the browser doesn't have one
// Limitation: only works with one class name
// Copyright: Eike Send http://eike.se/nd
// License: MIT License

if (!document.getElementsByClassName) {
document.getElementsByClassName = function getElemsClassNameFix(search) {
	var d = document,
		elements, i, pattern,
		results = [];
	if (d.querySelectorAll) { // IE8
		return d.querySelectorAll("." + search);
	}
	if (d.evaluate) { // IE6, IE7
		pattern = ".//*[contains(concat(' ', @class, ' '), ' " +
			search + " ')]";
		elements = d.evaluate(pattern, d, null, 0, null);
		// eslint-disable-next-line no-constant-condition
		while (true) {
			i = elements.iterateNext();
			if (!i) { break; }
			results.push(i);
		}
	} else {
		elements = d.getElementsByTagName("*");
		pattern = new RegExp("(^|\\s)" + search + "(\\s|$)");
		for (i = 0; i < elements.length; i++) {
			if (pattern.test(elements[i].className)) {
				results.push(elements[i]);
			}
		}
	}
	return results;
};
}

function getTarget(e) {
	function gettrg(r) {
		return r ? r.srcElement || r.target : null;
	}
return gettrg(e) || gettrg(window.event);
}

function getOffsetRect(elem) {
var body = document.body,
	box = elem.getBoundingClientRect(),
	c = document.documentElement,
	clientLeft = c.clientLeft || body.clientLeft || 0,
	clientTop = c.clientTop || body.clientTop || 0,
	scrollLeft = window.pageXOffset || c.scrollLeft || body.scrollLeft,
	scrollTop = window.pageYOffset || c.scrollTop || body.scrollTop,
	x = box.left + scrollLeft - clientLeft,
	y = box.top + scrollTop - clientTop;
return { top: Math.round(y), left: Math.round(x) };
}

function strFormat(s) {
var i,
	t = s;
for (i = 1; i < arguments.length; i++) {
	t = t.replace(/%s/, arguments[i]);
}
return t;
}

// ========================================

function picClick(e) {
var m = getTarget(e).parentNode.firstChild,
	r = getOffsetRect(m),
	t = r.top,
	x = r.left;
m = m.cloneNode();
m.className = "realsizeimg";
var s = m.style;
s.left = x + "px";
s.top = t + "px";
m.title = sShowSmaller;
document.body.appendChild(m);
addClick(m, function origSizeClick(el) {
	var pi = getTarget(el);
	pi.parentNode.removeChild(pi);
});
}

function injectPictureRealSize() {
var a = document.querySelectorAll(".content img"),
	d, i, n, q, s;
for (i = 0; i < a.length; i++) {
	q = a[i];
	s = getNaturalHW(q);
	if (s.width <= q.clientWidth) { continue; }
	d = document.createElement("div");
	d.className = "bigimgwrap";
	d.appendChild(q.parentNode.replaceChild(d, q));
	n = document.createElement("div");
	setText(n, "+");
	n.className = "showrealsize";
	n.title = strFormat(sShowBigger, s.width, s.height);
	d.appendChild(n);
	addClick(n, picClick);
}
d = document.createElement("link");
d.rel = "stylesheet";
d.type = "text/css";
d.href = "http://fu.com/vbb quote.css";
document.head.appendChild(d);
}

// ============================================

function quoteCutClick(e) {
var t = getTarget(e),
 x = t.previousSibling;
while (x && x.nodeType !== 1) {
	x = x.previousSibling;
}
if (!x) { return; }
x.className = "message";
t.parentNode.removeChild(t);
}

function injectQuoteExpand() {
var a = document.getElementsByClassName("message"),
	e, i, m;
for (i = 0; i < a.length; i++) {
	m = a[i];
	if (m.scrollHeight <= 120) { continue; }
	m.className += " messageCut";
	e = document.createElement("div");
	setText(e, sClickToExpand);
	e.className = "quoteExpand";
	m.parentNode.appendChild(e);
	addClick(e, quoteCutClick);
}
}

function main() {
injectQuoteExpand();
injectPictureRealSize();
}

main();

}());
