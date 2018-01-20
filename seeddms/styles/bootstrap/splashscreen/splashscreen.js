$(document).ready(function(){

  // Calling our splashScreen plugin

  var now = new Date().getTime();
	var object = JSON.parse(localStorage.getItem("splash"));
	if(object) {
		var dateString = object.timestamp;
	}

	if(!object || dateString < (now-250000000)) {

		$('.splashscreen').splashScreen({
		});

		var object = {timestamp: new Date().getTime()}
		localStorage.setItem("splash", JSON.stringify(object));
	}

});
