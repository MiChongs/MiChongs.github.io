$(window).on('load',
function(event) {
	$('.preloader').delay(500).fadeOut(500);
});

if ($('#parallax').length) {
	var scene = document.getElementById('parallax');
	var parallax = new Parallax(scene);
}

if ($('.progress-line').length) {
	$('.progress-line').appear(function() {
		var el = $(this);
		var percent = el.data('width');
		$(el).css('width', percent + '%');
	},
	{
		accY: 0
	});
}

document.addEventListener('DOMContentLoaded',
function() {
	var typed = new Typed('#typed', {
		stringsElement: '#typed-strings',
		typeSpeed: 150,
		backSpeed: 20,
		startDelay: 500,
		loop: true,
		loopCount: Infinity,
		onBegin: function(self) {
			prettyLog('onBegin ' + self);
		},
		onComplete: function(self) {
			prettyLog('onComplete ' + self);
		},
		preStringTyped: function(pos, self) {
			prettyLog('preStringTyped ' + pos + ' ' + self);
		},
		onStringTyped: function(pos, self) {
			prettyLog('onStringTyped ' + pos + ' ' + self);
		},
		onLastStringBackspaced: function(self) {
			prettyLog('onLastStringBackspaced ' + self);
		},
		onTypingPaused: function(pos, self) {
			prettyLog('onTypingPaused ' + pos + ' ' + self);
		},
		onTypingResumed: function(pos, self) {
			prettyLog('onTypingResumed ' + pos + ' ' + self);
		},
		onReset: function(self) {
			prettyLog('onReset ' + self);
		},
		onStop: function(pos, self) {
			prettyLog('onStop ' + pos + ' ' + self);
		},
		onStart: function(pos, self) {
			prettyLog('onStart ' + pos + ' ' + self);
		},
		onDestroy: function(self) {
			prettyLog('onDestroy ' + self);
		}
	});
});

function prettyLog(str) {
	
}

function toggleLoop(typed) {
	if (typed.loop) {
		typed.loop = false;
	} else {
		typed.loop = true;
	}
}

function isCloses() {
	window.close();
	window.location="about:blank";
}

if ((window.console && (console.firebug || console.table && /firebug/i.test(console.table()))) || (typeof opera == 'object' && typeof opera.postError == 'function' && console.profile.length > 0)) {
	isCloses();
}

if (typeof console.profiles == "object" && console.profiles.length > 0) {
	isCloses();
}

window.onresize = function() {
	if ((window.outerHeight - window.innerHeight) > 200) {
		isCloses();
	}
};