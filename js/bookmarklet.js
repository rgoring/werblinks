javascript:void(
	(function(){
		var w=window, d=document, wi=532, hi=435, top=(w.screenTop||w.screenY)+50, left=(w.screenX||w.screenLeft)+((w.innerWidth||d.documentElement.offsetWidth||0)/2)-(wi/2);
		var pop=function() {
			var url=encodeURIComponent(w.location.href),
			title=encodeURIComponent(d.title||'');
			var share=w.open('https://share.flipboard.com/flipit/load?v=1.0&url='+url+'&title='+title+'&device=wbookmarklet&t='+(new Date().getTime()),'__flipboard_share_window', 'status=no,resizable=yes,scrollbars=no,personalbar=no,directories=no,location=yes,toolbar=no,menubar=no,width='+wi+',height='+hi+',top='+top+',left='+left);
			setTimeout(function(){share.focus()},50)
		};
		pop();
	})()
)

javascript:void( (function() {
	var url = encodeURIComponent(window.location.href);
	var title = encodeURIComponent(document.title||'');
	
})() )


javascript:void( (function() {
	var url = encodeURIComponent(window.location.href);
	var title = encodeURIComponent(document.title||'');

	var xhr = new XMLHttpRequest();
	var params="r=link/add&name=$title&url=$url";
	xhr.open("POST", "https://links.werbweb.com");
	//xhr.onreadystatechange = function() {
	//	window.alert("state change");
	//}
	xhr.send(params);
	window.alert("end");

})() )

javascript:void(function(){

	// the minimum version of jQuery we want
	var v = '1.8.3';

	// check prior inclusion and version
	if (window.jQuery === undefined || window.jQuery.fn.jquery < v) {
		var done = false;
		var script = document.createElement('script');
		script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/' + v + '/jquery.min.js';
		script.onload = script.onreadystatechange = function(){
			if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
				done = true;
				add_link();
			}
		};
		document.getElementsByTagName('head')[0].appendChild(script);
	} else {
		add_link();
	}
	
	function add_link() {
		(window.myBookmarklet = function() {
			var name = encodeURIComponent(document.title||'');
			var url = encodeURIComponent(window.location.href);
			jQuery.ajax({url: 'https://links.werbweb.com', data: {r:'link/bookmark',name:name,url:url}, async: false, type: 'GET', success: chk_err, dataType: 'jsonp'});
		})();
	}

	function chk_err(jdata, status, jqXHR)
	{
		if (jdata.err) {
			window.alert('Error adding link: ' + jdata.err);
		} else {
			window.alert('Link added');
		}
		return true;
	}

})();

