jQuery(function($){
	var kwid_test = /KWID=([^&]+)/;
	var kwid_data = location.href.match(kwid_test);

	if (kwid_data){
		var kwid = kwid_data[1];
		var path = window.location.pathname + window.location.search;
		var referrer = document.referrer;

		$.ajax({
			type: "POST",
			url: 'note_kwid.php',
			data: {
				kwid:kwid, 
				path:path,
				referrer:referrer
			},
			success: function(){}
		});
	}
});