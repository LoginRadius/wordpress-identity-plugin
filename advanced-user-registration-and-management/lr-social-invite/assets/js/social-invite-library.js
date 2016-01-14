

jQuery(document).ready(function($) {

$LRSI = {};
$LRSI.util = {};	
//make utility
(function (util) {

	util.elementById = function (id) {
		return document.getElementById(id);
	};

	util.elementsByClass = function getElementsByClassName(classname, node) {
		node = node || document.body;
		var a = [],
			re = new RegExp('(^| )' + classname + '( |$)');
		var els = node.getElementsByTagName("*");
		for (var i = 0, j = els.length; i < j; i++) {
			if (re.test(els[i].className)) {
				a.push(els[i]);
			}
		}
		return a;
	};

	util.addEvent = function (type, element, handle) {
		var elements = [];
		if (element instanceof Array) {
			elements = element;
		}
		else {
			elements.push(element);
		}
		for (var i = 0; i < elements.length; i++) {
			elements[i]["on" + type] = handle;
		}
	};
	var cache = {};
	util.tmpl = function tmpl(str, data) {
		var fn = !/\W/.test(str) ? cache[str] = cache[str] || tmpl(util.elementById(str).innerHTML) : new Function("obj", "var p=[],print=function(){p.push.apply(p,arguments);};" + "with(obj){p.push('" + str.replace(/[\r\t\n]/g, " ").split("<%").join("\t").replace(/((^|%>)[^\t]*)'/g, "$1\r").replace(/\t=(.*?)%>/g, "',$1,'").split("\t").join("');").split("%>").join("p.push('").split("\r").join("\\'") + "');}return p.join('');");
		return data ? fn(data) : fn;
	};
	util.openWindow = function (_url) {
		_url = _url || this.href;

		var parser = document.createElement('a');
		parser.href = _url;
		var provider = util.getQueryParameterByName("provider", parser.search);


		window.open(_url, 'lrpopupchildwindow', 'menubar=1,resizable=1,width=450,height=500');
		return false;
	};
	util.addCss = function (element, styles) {
		if (element && element.style) {
			for (var s in styles) {
				if (element.style[s]) {
					element.style[s].cssText += styles[s];
				}
			}
			return true;
		}
		return false;
	};
	util.getPos = function getPos(el) {
		for (var lx = 0, ly = 0; el != null; lx += el.offsetLeft, ly += el.offsetTop, el = el.offsetParent)
		{ }
		return {
			x: lx,
			y: ly
		};
	};
	util.hasClass = function hasClassonelement(ele, cls) {
		return ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
	};
	util.removeclass = function removeClassonelement(ele, cls) {
		if (util.hasClass(ele, cls)) {
			var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
			ele.className = ele.className.replace(reg, ' ');
			return true;
		}
		return false;
	};
	util.addclass = function (ele, cls) {
		if (!util.hasClass(ele, cls)) {
			ele.className += " " + cls;
			return true;
		}
		return false;
	};
	util.getQueryParameterByName = function (name, search) {
		search = search || location.search;
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
		var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
			results = regex.exec(search);
		return results == null ? null : decodeURIComponent(results[1].replace(/\+/g, " "));
	};
	util.setVisible = function (visi, ele1, ele2) {

		var outp = util.elementById("lr-si-txtautocomplete");
		var pos = util.getPos(outp);
		var x = ele1;
		var t = ele2;
		x.style.position = 'absolute';
		// x.style.top = ((outp.offsetHeight + pos.y) - 152) + "px";
		//  x.style.left = (util.findPosX(t) + 2) + "px";
		x.style.visibility = visi;
	}
	util.setColor = function (_posi, _color, _forg) {
		var outp = document.getElementById("output");
		outp.childNodes[_posi].style.background = _color;
		outp.childNodes[_posi].style.color = _forg;
	}
})($LRSI.util);

//Specially for dom ready method
(function (util) {
	// Everything that has to do with properly supporting our document ready event. Brought over from the most awesome jQuery.
	var userAgent = navigator.userAgent.toLowerCase();

	// Figure out what browser is being used
	var browser = {
		version: (userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [])[1],
		safari: /webkit/.test(userAgent),
		opera: /opera/.test(userAgent),
		msie: (/msie/.test(userAgent) || /trident/.test(userAgent)) && (!/opera/.test(userAgent)),
		mozilla: (/mozilla/.test(userAgent)) && (!/(compatible|webkit)/.test(userAgent))
	};
	util.browser = browser;

	var readyBound = false;
	var isReady = false;
	var readyList = [];

	// Handle when the DOM is ready

	function domReady() {
		// Make sure that the DOM is not already loaded
		if (!isReady) {
			// Remember that the DOM is ready
			isReady = true;

			if (readyList) {
				for (var fn = 0; fn < readyList.length; fn++) {
					readyList[fn].call(window, []);
				}

				readyList = [];
			}
		}
	};

	// From Simon Willison. A safe way to fire onload w/o screwing up everyone else.

	function addLoadEvent(func) {
		var oldonload = window.onload;
		if (typeof window.onload != 'function') {
			window.onload = func;
		}
		else {
			window.onload = function () {
				if (oldonload) {
					oldonload();
				}
				func();
			};
		}
	};

	// does the heavy work of working through the browsers idiosyncracies (let's call them that) to hook onload.

	function bindReady() {
		if (readyBound) {
			return;
		}

		readyBound = true;

		// Mozilla, Opera (see further below for it) and webkit nightlies currently support this event
		if (document.addEventListener && !browser.opera) {
			// Use the handy event callback
			document.addEventListener("DOMContentLoaded", domReady, false);
		}

		// If IE is used and is not in a frame
		// Continually check to see if the document is ready
		if (browser.msie && window == top) (function () {
			if (isReady)
				return;
			try {
				// If IE is used, use the trick by Diego Perini
				// http://javascript.nwbox.com/IEContentLoaded/
				document.documentElement.doScroll("left");
			}
			catch (error) {
				setTimeout(arguments.callee, 0);
				return;
			}
			// and execute any waiting functions
			domReady();
		})();

		if (browser.opera) {
			document.addEventListener("DOMContentLoaded", function () {
				if (isReady)
					return;
				for (var i = 0; i < document.styleSheets.length; i++)
					if (document.styleSheets[i].disabled) {
						setTimeout(arguments.callee, 0);
						return;
					}
				// and execute any waiting functions
				domReady();
			}, false);
		}

		if (browser.safari) {
			var numStyles;
			(function () {
				if (isReady)
					return;
				if (document.readyState != "loaded" && document.readyState != "complete") {
					setTimeout(arguments.callee, 0);
					return;
				}
				if (numStyles === undefined) {
					var links = document.getElementsByTagName("link");
					for (var i = 0; i < links.length; i++) {
						if (links[i].getAttribute('rel') == 'stylesheet') {
							numStyles++;
						}
					}
					var styles = document.getElementsByTagName("style");
					numStyles += styles.length;
				}
				if (document.styleSheets.length != numStyles) {
					setTimeout(arguments.callee, 0);
					return;
				}

				// and execute any waiting functions
				domReady();
			})();
		}

		// A fallback to window.onload, that will always work
		addLoadEvent(domReady);
	};

	// This is the public function that people can use to hook up ready.
	util.ready = function (fn, args) {
		// Attach the listeners
		bindReady();

		// If the DOM is already ready
		if (isReady) {
			// Execute the function immediately
			fn.call(window, []);
		}
		else {
			// Add the function to the wait list
			readyList.push(function () {
				return fn.call(window, []);
			});
		}
	};

	bindReady();
})($LRSI.util);

(function ( object ) {

	var lr_posi = -1;
	var lr_oldins = "";
	var lr_arrowkey = "";
	
	// globals
	$LRSI.onSuccess = {};
	$LRSI.onError = {};

	var contactsInfo = {};
	var contacts = [];

	var providersuseEmailID = ["google", "yahoo"];
	var hubdomain = 'hub.loginradius.com';
	currentprovider = "";

	var words = [];
	var selectedIds = [];

	var current_token = "";

	var errorMessages = [
		{
			"id": "contacts",
			"message": "Enter friend's name"

		},
		{
			"id": "subject",
			"message": "Enter subject"

		},
		{
			"id": "message",
			"message": "Enter message"

		},
		{
			"id": "success",
			"message": "Message send successfully"

		}];

	window.setInterval("$LRSI.lookAt()", 100);
	//   setVisible("hidden");
	document.onkeydown = keygetter; //needed for Opera...
	document.onkeyup = keyHandler;

	//create validation div
	formValidMsg = function (parentelemnetId, errordivId, message) {
		var errordiv = object.util.elementById(errordivId);
		if (typeof errordiv != 'undefined' && errordiv != null) {
			errordiv.style.display = 'block';
		} else {
			var validationdiv = document.createElement("div");
			validationdiv.className = "lr-si-errormessage ";
			validationdiv.id = errordivId;
			validationdiv.innerHTML = message;
			object.util.elementById(parentelemnetId).appendChild(validationdiv);
		}
	}

	// Output validation message
	var outputValidMsg = function ( message ) {	
		var msgDiv = $( '#lr_si_response' );
		msgDiv.append( message );
	}

	// Global Function
	// Validation function
	$LRSI.validate = function (elemsubject, elemmessage) {

		var isvalid = true;

		var contactlenght = selectedIds.length;
		if (contactlenght <= 0) {
			formValidMsg("lr-si-autocomplete", "lr-si-error-autocomplete", getMessage("contacts"));
			if (isvalid == true) {
				isvalid = false;
			}
		} else {
			var contactrrordiv = object.util.elementById("lr-si-autocomplete");
			if (typeof contactrrordiv != 'undefined' && contactrrordiv != null) {
				//contactrrordiv.style.display = 'none';
			}
		}
		if (elemsubject.value == null || elemsubject.value == "") {
			formValidMsg("lr-si-subject", "lr-si-error-subject", getMessage("subject"));
			elemsubject.focus();
			if (isvalid==true)
				isvalid = false;
		} else {
			var subjecterrordiv = object.util.elementById("lr-si-error-subject");
			if (typeof subjecterrordiv != 'undefined' && subjecterrordiv != null) {
				subjecterrordiv.style.display = 'none';
			}
		}
		if (elemmessage.value == null || elemmessage.value == "") {

			formValidMsg("lr-si-message", "lr-si-error-message", getMessage("message"));
			elemmessage.focus();
			if (isvalid == true)
				isvalid = false;
		}
		else {
			var messageerrordiv = object.util.elementById("lr-si-error-message");
			if (typeof messageerrordiv != 'undefined' && messageerrordiv != null) {
				messageerrordiv.style.display = 'none';
			}
		}

		if (isvalid) {

			var allerrorsdiv = object.util.elementsByClass("lr-si-errormessage");
			for (var i = 0; i < allerrorsdiv.length; i++) {
				allerrorsdiv[0].style.display = 'none';
			}
		}

		return isvalid;
	}

	

	var getMessage = function (messageId) {
		for ( var i = 0; i < errorMessages.length; i++ ) {
			if ( errorMessages[i].id == messageId ) {
				return errorMessages[i].message;
			}
		};
	}

	// Remove selected tag on remove button
	$LRSI.removeContact = function (that) {
		var contactspantag = that.id.replace('lr_contacttag_remove', '');
		
		//contacttag container div
		var containerdiv = document.getElementById('lr_divspantag');
		for (var i in selectedIds) {
			if (selectedIds[i].Id == contactspantag) {
				selectedIds.splice(i, 1);
			}
		}
		var containerspan = document.getElementById(contactspantag);
		containerdiv.removeChild(containerspan);
	};

	// Reset widget settings GLOBAL
	$LRSI.resetWidget = function () {
		object.util.elementById("lr_si_providerbox").style.display = 'block';
		object.util.elementById("lr_si_messagebox").style.display = 'none';
		object.util.elementById('lrcustomfriendinvite_widgetpopup').style.display = "";
		
		object.util.elementById("lr_FriendInvitesubject").value = "";
		object.util.elementById("lr-si-txtautocomplete").value = "";
		object.util.elementById("lr_FriendInvitemessage").value = "";

		var containerdiv = object.util.elementById('lr_divspantag');
		var messagediv = object.util.elementById('lr_si_message');

		containerdiv.innerHTML = "";
		if (messagediv != null) {
			messagediv.parentNode.removeChild(messagediv);
		}
		var sendbtn = object.util.elementById("lr_friendinvite_box_bottom_btn");
		var headerdiv = object.util.elementById("lr_popupbox_heading");
  
		if (headerdiv != null && sendbtn != null) {
			$("#lr_popupbox_heading").removeClass('lr-header-twitter lr-header-google lr-header-facebook lr-header-yahoo lr-header-linkedin');
			$("#lr_si_box_bottom_btn").removeClass('lr-header-twitter lr-header-google lr-header-facebook lr-header-yahoo lr-header-linkedin');
		}
	}

	//#region auto complete
	$LRSI.lookAt = function () {
		var ins = $LRSI.util.elementById("lr-si-txtautocomplete");
		var elem1 = $LRSI.util.elementById("lr-contactlist-shadow");
		if(ins) {
			if (lr_oldins == ins.value)
				return;
			else if (lr_posi > -1);
			else if (ins.value.length > 0) {
				words = getWord(ins.value);
				if (words.length > 0) {
					clearOutput();

					for (var i = 0; i < words.length; ++i)
						addWord(words[i]);
					object.util.setVisible("visible", elem1, ins);
					lr_input = ins.value;
				}
				else {
					object.util.setVisible("hidden", elem1, ins);
					lr_posi = -1;
				}
			}
			else {
				object.util.setVisible("hidden", elem1, ins);
				lr_posi = -1;
			}
			lr_oldins = ins.value;
		}
	}

	function addWord(word) {
		var sp = document.createElement("div");
		var Idorname = word.split("/");

		sp.appendChild(document.createTextNode(Idorname[1]));
		sp.id = Idorname[0];
		sp.onmouseover = mouseHandler;
		sp.onmouseout = mouseHandlerOut;
		sp.onclick = mouseClick;
		var outp = document.getElementById("output");
		outp.appendChild(sp);

		var errorsdiv = object.util.elementById("lr-si-error-autocomplete");
		if (typeof errorsdiv != 'undefined' && errorsdiv != null) {
			errorsdiv.style.display = 'none';
		}
	}

	function clearOutput() {
		var outp = object.util.elementById("output");
		while (outp.hasChildNodes()) {
			noten = outp.firstChild;
			outp.removeChild(noten);
		}
		posi = -1;
	}

	function getWord(beginning) {
		var getWords = new Array();

		for (var i = 0; i < contacts.length; ++i) {
			if (contacts[i].Name.toLowerCase().indexOf(beginning) > -1) {
				if (isMailProvider()) {
					if (!isAllreadyinList(contacts[i].EmailID)) {
						getWords[getWords.length] = contacts[i].EmailID + "/" + contacts[i].Name + "(" + contacts[i].EmailID + ")";
					}
				} else {
					if (!isAllreadyinList(contacts[i].id)) {
						getWords[getWords.length] = contacts[i].id + "/" + contacts[i].Name;
					}
				}

			}
		}
		return getWords;
	}

	function keygetter(event) {
		if (!event && window.event)
			event = window.event;
		if (event)
			lr_arrowkey = event.keyCode;
		else
			lr_arrowkey = event.which;
	}

	function keyHandler() {

		if (document.getElementById("lr-contactlist-shadow").style.visibility == "visible") {
			var textfield = object.util.elementById("lr-si-txtautocomplete");
			if (lr_arrowkey == 13)//key down
			{
				if (words.length > 0 && posi <= words.length - 1) {
					//if (posi >= 0)
					//    object.util.setColor(posi, "#fff", "black");
					//else
					//    lr_input = textfield.value;
					//object.util.setColor(++posi, "#01A7E5", "white");


					var outp = object.util.elementById("output");

					//contacttag container div
					var containerdiv = object.util.elementById('lr_divspantag');

					//span contacttag
					var contacttag = document.createElement("span");
					contacttag.className = "removable lr-friendnamebox";
					contacttag.id = outp.childNodes[posi].id;
					contacttag.innerHTML = outp.childNodes[posi].firstChild.nodeValue;

					//span contacttag remove href
					var contacttagremove = document.createElement("a");
					contacttagremove.className = "remove lr-friendnamebox-removebox lr-friendname-removebtn";
					contacttagremove.id = "lr_contacttag_remove" + outp.childNodes[posi].id;

					object.util.addEvent('click', contacttagremove, function () {
						$LRSI.removeContact(this);
					});

					//append remove href  in contacttag span
					contacttag.appendChild(contacttagremove);

					//append span contacttag in container div
					containerdiv.appendChild(contacttag);

					textfield.value = "";
					selectedIds.push({ Id: outp.childNodes[posi].id, Name: outp.childNodes[posi].firstChild.nodeValue });
				}
			}
			else if (lr_arrowkey == 40) {
				if (words.length > 0 && posi <= words.length - 1) {
					if (posi >= 0)
						object.util.setColor(posi, "#fff", "black");
					else
						lr_input = textfield.value;
					object.util.setColor(++posi, "#01A7E5", "white");
				}
			}
			else if (lr_arrowkey == 13) { //Key up
				if (words.length > 0 && posi >= 0) {
					if (posi >= 1) {
						object.util.setColor(posi, "#fff", "black");
						object.util.setColor(--posi, "#01A7E5", "white");
						var outp = document.getElementById("output");

						//contacttag container div
						var containerdiv = object.util.elementById('lr_divspantag');

						//span contacttag
						//span contacttag
						var contacttag = document.createElement("span");
						contacttag.className = "removable lr-friendnamebox";
						contacttag.id = outp.childNodes[posi].id;
						contacttag.innerHTML = outp.childNodes[posi].firstChild.nodeValue;

						//span contacttag remove href
						var contacttagremove = document.createElement("a");
						contacttagremove.className = "remove lr-friendnamebox-removebox lr-friendname-removebtn";
						contacttagremove.id = "lr_contacttag_remove" + outp.childNodes[posi].id;

						object.util.addEvent('click', contacttagremove, function () {
							$LRSI.removeContact(this);
						});

						//append remove href  in contacttag span
						contacttag.appendChild(contacttagremove);
						textfield.value = "";
						selectedIds.push({ Id: outp.childNodes[posi].id, Name: outp.childNodes[posi].firstChild.nodeValue });
					}
					else {
						object.util.setColor(posi, "#fff", "black");
						textfield.value = lr_input;
						textfield.focus();
						posi--;
					}
				}
			}
			else if (lr_arrowkey == 27) { // Esc
				textfield.value = lr_input;
				var elem2 = object.util.elementsByClass("text")[0];
				var elem1 = object.util.elementsByClass("shadow")[0];
				object.util.setVisible("hidden", elem1, elem2);
				posi = -1;
				oldins = lr_input;
			}
			else if (lr_arrowkey == 8) { // Backspace
				posi = -1;
				oldins = -1;
			}
		}
	}

	function mouseHandler() {
		for (var i = 0; i < words.length; ++i) {
			object.util.setColor(i, "", "");
		}
		this.style.background = "#01A7E5";
		this.style.color = "white";
	}

	function mouseHandlerOut() {
		this.style.background = "";
		this.style.color = "";
	}

	function mouseClick() {

		//contacttag container div
		var containerdiv = document.getElementById('lr_divspantag');

		//span contacttag
		var contacttag = document.createElement("span");
		contacttag.className = "removable lr-friendnamebox";
		contacttag.id = this.id;
		contacttag.innerHTML = this.firstChild.nodeValue;

		//span contacttag remove href
		var contacttagremove = document.createElement("a");
		contacttagremove.className = "remove lr-friendnamebox-removebox lr-friendname-removebtn";
		contacttagremove.id = "lr_contacttag_remove" + this.id;

		object.util.addEvent('click', contacttagremove, function () {
			$LRSI.removeContact(this);
		});

		//append remove href  in contacttag span
		contacttag.appendChild(contacttagremove);

		//append span contacttag in container div
		containerdiv.appendChild(contacttag);



		object.util.elementById("lr-si-txtautocomplete").value = "";
		selectedIds.push({ Id: this.id, Name: this.firstChild.nodeValue });

		var elem2 = object.util.elementById("lr-si-txtautocomplete");
		var elem1 = object.util.elementById("lr-contactlist-shadow");
		object.util.setVisible("hidden", elem1, elem2);
		lr_posi = -1;
		lr_oldins = this.firstChild.nodeValue;
	}

	$LRSI.fbMessage = function( fb_app_id, name, id, link ){
		FB.init({ appId: fb_app_id, xfbml: true, cookie: true });
		FB.ui({
			method: 'send',
			name: name,
			to: id,
			link: link
		});
	};

	//Init Html5 SDK & get contacts after login
	$LRSI.initHtml5LoginradiusSdk = function () {

		LoginRadiusSDK.getContacts(0, function (contacts) {
			
			if (contacts.errorCode) {

				$LRSI.onError({
					"message": contacts.message,
					"description": contacts.description
				});

				//object.util.elementById("lr_si_loading_div").style.display = 'none';

				$LRSI.messagediv = document.createElement("div");
				$LRSI.messagediv.className = "lr-si-errormessage lr-offset1";
				$LRSI.messagediv.id = "lr_si_message";
				$LRSI.messagediv.innerHTML = "This time there is something problem on "+currentprovider+" so not possible to get contacts";

				object.util.addCss($LRSI.messagediv, {
					'float': 'left',
					'margin-left':'8px'
				});

				object.util.elementById("lr_si_providerbox").appendChild($LRSI.messagediv);

				setTimeout( $LRSI.resetWidget, 4000 );

			} else {

				if (contacts.Data.length > 0) {
					activeHeader();
					//object.util.elementById("lr_si_loading_div").style.display = 'none';
					object.util.elementById("lr_si_providerbox").style.display = 'none';
					object.util.elementById("lr_si_messagebox").style.display = 'block';
					object.util.elementById("lr-back-button").style.display = 'block';


					contacts.Data.sort(function(a, b) {
						if (a.Name.toLowerCase() < b.Name.toLowerCase()) return -1;
						if (a.Name.toLowerCase() > b.Name.toLowerCase()) return 1;
						return 0;
					});

					contactsInfo = contacts;
					setContacts();
				} else {
					object.util.elementById("lr_si_loading_div").style.display = 'none';
					$LRSI.messagediv = document.createElement("div");
					$LRSI.messagediv.className = "lr-si-errormessage lr-offset1";
					$LRSI.messagediv.id = "lr_si_message";
					$LRSI.messagediv.innerHTML = "You don't have any contact in this account.";

					object.util.addCss($LRSI.messagediv, {
						'float': 'left',
						'margin-left': '8px'
					});
					object.util.elementById("lr_si_providerbox").appendChild($LRSI.messagediv);

					setTimeout( $LRSI.resetWidget, 4000 );
				}
			}
		});
	}

	// Reset Form
	$LRSI.resetForm = function() {
		contactsInfo = [];
		contacts = [];
		selectedIds = [];
		$('.lr_si_response').html('');
		$('.lr-popupbox-namebox').html('');
		$('#lr_si_subject').val('');
		$('#lr_si_message').val('');
		$('.lr-social-invite-login-provider').hide();
		// $('.lr_si_messagebox').hide();
		// $('.lr_si_providerbox').hide();
		// $('.lrcustomfriendinvite_widgetpopup').hide();
	}

	// Collect Information from client & ready action 
	$LRSI.readySocialInvite = function( social_id, provider, email, name ) {

		currentprovider = provider;
		
		activeHeader();
		
		// Reset Form
		$LRSI.resetForm();

		// Set id for selected contact
		var id;
		if( provider == "twitter" || provider == "linkedin" ){
			id = social_id;
		}else {
			id = email;
		}

		selectContact( id, name);

		var token = "";
		$.ajax( {
			type: 'POST',
			async: false,
			url: ajaxurl,
			data: {
				action: 'social_invite_get_provider_token',
				provider: provider
			},
			success: function ( data, textStatus, XMLHttpRequest ) {
				try{
					obj = JSON.parse(data);
					token = obj[0].token;
				}catch(e){
					token = "";
				}
			},
			error: function ( xhr, textStatus, errorThrown ) {
				alert( 'Unexpected error occurred' );
			}
		} );

		var response = "";
		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				token: token,
				action: 'social_invite_is_token_valid'
			},
			success: function ( data, textStatus, XMLHttpRequest ) {
				try{
					response = JSON.parse(data);
				}catch(e){
					response = "false";
				}

				if(response == "false" || response == ""){


					// Expired Token
					$('.lr_si_messagebox').hide();
					$('.lr-' + provider + '-social-invite-login').show();
					$('.lr_si_providerbox').show();
					$('.lrcustomfriendinvite_widgetpopup').show();

				}else{
					

					// Open Social Invite
					current_token = response;
					sessionStorage.setItem("LRTokenKey", response);
					$LRSI.initHtml5LoginradiusSdk();
					$('.lrcustomfriendinvite_widgetpopup').show();
				}

			},
			error: function ( xhr, textStatus, errorThrown ) {
				alert( 'Unexpected error occurred' );
			}
		} );
	};

	// Response after sending a message
	$LRSI.showResponse = function( data ) {
		
		for( var i = 0; i < data.length; i++) {
			if( data[i].isPosted == true ) {
				outputValidMsg('<div class="lr_si_success">Message to ' + data[i].name + ' has been sent.</div>');
			}else {
				outputValidMsg('<div class="lr_si_error">Message to ' + data[i].name + ' has not been sent.</div>');
			}
		}
	};

	// Function in charge of sending all messages
	$LRSI.send_message = function() {

		var contacts = selectedIds;
		var token = current_token;
		var provider = currentprovider;
		var subject = $('#lr_si_subject').val();
		var message = $('#lr_si_message').val();

		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				contacts: contacts,
				token: token,
				provider: provider,
				subject: subject,
				message: message,
				action: 'social_invite_send_message'
			},
			success: function ( data, textStatus, XMLHttpRequest ) {
					
					$LRSI.resetForm();
					$('.lr_si_messagebox').hide();
					var data = $.parseJSON( data );
					$LRSI.showResponse( data );
			},
			error: function ( xhr, textStatus, errorThrown ) {
				alert( 'Send Message: Unexpected error occurred' );
			}
		} );
	};

	// Local function
	// Selects contact from list
	var selectContact = function( id, name) {

		//contactTag container div
		var containerDiv = $LRIC.util.elementById('lr_divspantag');

		//span contactTag
		var contactTag = document.createElement("span");
		contactTag.className = "removable lr-friendnamebox";
		contactTag.id = id;
		contactTag.innerHTML = name;

		//span contactTag remove href
		var contactTagRemove = document.createElement("a");
		contactTagRemove.className = "remove lr-friendnamebox-removebox lr-friendname-removebtn";
		contactTagRemove.id = "lr_contacttag_remove" + id;

		$LRIC.util.addEvent('click', contactTagRemove, function () {
			$LRSI.removeContact(this);
		});

		//append remove href  in contactTag span
		contactTag.appendChild(contactTagRemove);

		//append span contactTag in container div
		containerDiv.appendChild(contactTag);

		selectedIds.push({ Id: id, Name: name });
	}

	// GLOBAL
	// Close Popup
	$LRSI.closeWidget = function() {
		$('.lrcustomfriendinvite_widgetpopup').hide();
	};

	// Set contacts list int autocomplete array
	function setContacts() {
		if (typeof contactsInfo == 'undefined') return;
		if (typeof currentprovider == 'undefined') return;

		contacts = [];
		for (var i in contactsInfo.Data) {
			contacts.push({
				id: contactsInfo.Data[i].ID,
				Name: contactsInfo.Data[i].Name,
				EmailID: contactsInfo.Data[i].EmailID
			});
		}
	}

	// Check duplicate record
	function isAllreadyinList(selectId) {
		for (var i in selectedIds) {
			if (selectedIds[i].Id.toLowerCase() == selectId.toLowerCase()) {
				return true;
			}

		}
		return false;
	}

	// Check current provider emailid provider or not
	function isMailProvider() {
		if (providersuseEmailID.indexOf(currentprovider) > -1) {
			return true;
		}
		return false;
	}

	// Activates Header
	function activeHeader() {
		// Deactivate any active providers (should only be one)
		var headerdiv = $("#lr_popupbox_heading");
		var sendbtn = $("#lr_si_box_bottom_btn");

		if (headerdiv != null && sendbtn!=null) {
			$("#lr_popupbox_heading").removeClass('lr-header-twitter lr-header-google lr-header-facebook lr-header-yahoo lr-header-linkedin');
			$("#lr_si_box_bottom_btn").removeClass('lr-header-twitter lr-header-google lr-header-facebook lr-header-yahoo lr-header-linkedin');
		}

		// Activate clicked div
		headerdiv.addClass( "lr-header-" + currentprovider.toLowerCase() );
		sendbtn.addClass( " lr-header-" + currentprovider.toLowerCase() );
	}

	function SocialInviteSearchContacts( search ) {
		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				search: search,
				action: 'social_invite_search_contacts'
			},
			success: function ( data_contacts, textStatus, XMLHttpRequest ) {

					var contacts = $.parseJSON( data_contacts );
					$('.lr_social_invite_results').html('');
					$.each( contacts, function( index, key ){
						if(key.image_url == '') {
							key.image_url = mysteryperson;
						}
						if(key.provider == "facebook"){
							$('.lr_social_invite_results').append('<div class="lr_si_contact_container"><div class="lr_si_contact"><div class="lr_si_contact_img"><img src="' + key.image_url + '" /></div><div class="lr_si_contact_title">' + key.name + '</div><button type="button" class="lr-si-btn rounded" data-provider="' + key.provider + '" onclick="javascript:$LRSI.fbMessage(\'' + facebook_app_id + '\',\'' + key.name + '\',\'' + key.social_id + '\',\'' + facebook_share_url + '\')"><span class="buttonText">INVITE</span></button></div></div>');
						}else{
							$('.lr_social_invite_results').append('<div class="lr_si_contact_container"><div class="lr_si_contact"><div class="lr_si_contact_img"><img src="' + key.image_url + '" /></div><div class="lr_si_contact_title">' + key.name + '</div><button type="button" class="lr-si-btn rounded" data-provider="' + key.provider + '" onclick ="javascript:$LRSI.readySocialInvite(\'' + key.social_id + '\',\'' + key.provider + '\',\'' + key.email + '\',\'' + key.name + '\')"   class="if-button rounded"><span class="buttonText">INVITE</span></button></div></div>');
						}
					});

					//$('.lr_social_invite_results').html( $.parseJSON( data_contacts ) );

			},
			error: function ( xhr, textStatus, errorThrown ) {
				alert( 'Unexpected error occurred' );
			}
		} );
	};

	function SocialInviteSuccess( element ) {
			console.log( 'LR SOCIAL INVITE' );
			//var cursor = 0;
			// SocialInviteLoginRadiusSDK.getUserprofile(function (data) {
			// 	$('.lr-social-invite-results').html(JSON.stringify(data));
			// });

			// SocialInviteLoginRadiusSDK.getContacts(cursor , function( contacts){
			// 	$('.lr-social-invite-results').html(JSON.stringify(contacts));
			// });

			var token = sessionStorage.getItem("LRTokenKey");
			var provider = sessionStorage.getItem("social_invite_provider");

			sessionStorage.removeItem("LRTokenKey");
			sessionStorage.removeItem("social_invite_provider");

			if( token != null && provider != null) {
				$.ajax( {
					type: 'POST',
					url: ajaxurl,
					data: {
						token: token,
						provider: provider,
						action: 'social_invite_get_contacts'
					},
					success: function ( data, textStatus, XMLHttpRequest ) {
							
							SocialInviteSearchContacts('');
					},
					error: function ( xhr, textStatus, errorThrown ) {
						alert( 'Unexpected error occurred' );
					}
				} );
			}
	};

	// Local Function
	function loginMessage( event ) {
		console.log( 'LR SOCIAL INVITE LOGIN MSG' );
		var token = sessionStorage.getItem("LRTokenKey");
		var social_invite_provider = sessionStorage.getItem("social_invite_provider");

		if( token.length > 0 && currentprovider.length > 0 ){

			current_token = token;

			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: {
					token: token,
					provider: currentprovider,
					action: 'social_invite_update_provider_token'
				},
				success: function ( data, textStatus, XMLHttpRequest ) {

					var data = $.parseJSON( data );
				},
				error: function ( xhr, textStatus, errorThrown ) {
					console.log( 'Update Token: Unexpected error occurred' );
				}
			} );

			$LRSI.initHtml5LoginradiusSdk();

			sessionStorage.removeItem("LRTokenKey");
			sessionStorage.removeItem("social_invite_provider");
			
			//Hide provider icons
			$('.lr-social-invite-login-provider').hide();
			$('.lr_si_providerbox').hide();

			$('.lr_si_messagebox').show();
		}
	};

	function SocialInviteSwitch(element) {

		var element_switch = $('#lr-' + element.name + '-social-invite-switch');
		var element_trigger = $('#lr-' + element.name + '-social-invite-trigger');

		sessionStorage.setItem("social_invite_provider", element.name );
		element_trigger.trigger("click");
	};

	function SocialInviteSwitchLogin(element) {

		var element_switch = $('#lr-' + element.name + '-social-invite-login-switch');
		var element_trigger = $('#lr-' + element.name + '-social-invite-login-trigger');

		sessionStorage.removeItem("LRTokenKey");
		sessionStorage.setItem("social_invite_provider", element.name );
		element_trigger.trigger("click");
	};

	$(document).on('change','.lr-social-invite-switch', function () {
		LoginRadiusSDK.onlogin = SocialInviteSuccess;
		SocialInviteSwitch(this);
	});

	$(document).on('change','.lr-social-invite-login-switch', function () {
		LoginRadiusSDK.onlogin = loginMessage;
		SocialInviteSwitchLogin(this);
	});

	$('.lr_social_invite_search_input').keyup(function() {
			var search = $(this).val();

			if(search != '' && search.length > 1) {
				SocialInviteSearchContacts(search);
			}else{
				SocialInviteSearchContacts('');
			}

			return false;
	});

	SocialInviteSearchContacts('');
})($LRSI);

});