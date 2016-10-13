if(typeof(exports) !== "undefined"){
	var $ = require('jquery');
}

var Url = {
	shorten: function (url,callback) {
		if(["",null,undefined].indexOf(url) >= 0) return false;
		var api_url = "http://api.bit.ly/v3/shorten";
		var params = {
			format: 'json',
			longUrl: url,
			login: 'revathskumar',
			apiKey: ''
		};

		return $.ajax({
			type: 'GET',
			url: api_url ,
			data: params,
			dataType: 'json',
			success: function(data, status_param) {
				var status = data.status_code;
				console.log(status);
				if(status == 200) {
					callback(status, data.data.url);
				} else {
					console.log(status);
					callback(status, data.status_txt);
				}
			},
			error: function (request, status, error) {
				callback(-1, "ajaxFailed");
			}
		});
	}
};


(function(exports, $){
	exports.shorten = Url.shorten;

}(typeof exports === 'undefined'? this['mymodule']={}: exports, $));