jQuery.support.cors = true;

jQuery(function($){
	//setup the other text field behaviors
	$("div.sm_other_wrap.start_hide").hide();
	$(".sm_select_has_other").bind("change click", function(){
		var $this = $(this);
		var jSel = 'input[name="'+$this.attr("name") + '_manual_input"]';
		var show_it = Boolean($this.val().match(/other$/));
		$(jSel).parent().toggle(show_it);
	});

    $("form.sm_form #cus_cp_form").focus(function(){
		$(".sm_which_town, #sm_town_list", $(this).next("#cp_msg").addClass("cp_error")).show();
		$("#sm_chosen_town").hide();
	}).keyup(function(){

		var $this = $(this);
		if ($this.val().length < 5) return;
		if ($this.val() == $this.attr("data_lastval")) return;
		$this.attr("data_lastval", $this.val());

		var success_function = function(data, status, xhr) {
			data = $.parseJSON( data );

			$("#cus_cas_cp_form").val(data.cas_cp);
			var $msgBox = $this.parent().find("#cp_msg");

			if ($msgBox.length == 0){
				$msgBox = $this.parent().append("<div id=\"cp_msg\"></div>").find("#cp_msg");
			}

			switch(data.cas_cp){
				case 1:
					$("#cus_id_ville_form").val("");
				break;
				case 2:
				case 3:
					$("#cp_msg").addClass("cp_error").html("<div class=\"sm_which_town\">Spécifiez la ville svp : </div><div id=\"sm_town_list\"></div><div id=\"sm_chosen_town\">Ville choisie : <span id=\"sm_chosen_town_name\"></span> <a href=\"\" id=\"sm_change_town_link\">(éditer)</a><input name=\"cus_ville_absente\" id=\"cus_ville_absente\" value=\"\" type=\"hidden\"></div>");
					$("#sm_change_town_link").click(function(){
						$(".sm_which_town, #sm_town_list").show();
						$("#cp_msg").addClass("cp_error");
						$("#sm_chosen_town").hide();
						return false;
					});

					$.each(data.villes, function(i, e){
						e.libelle = e.libelle.replace(/<[^<]+>/g,"");
						var $link = $("<a href=\"\" class=\"sm_cp_link\">"+e.libelle+"</a>").click(function(){
							$("#cus_id_ville_form").val(e['id']);
                            $("#cus_cas_cp_form").val(data['cas_cp']);
							$("#cus_id_cp_form").val(data['id_cp']);
							$("#cp_msg").removeClass("cp_error");
							$("#sm_town_list").hide();
							$(".sm_which_town").hide();
							$("#sm_chosen_town").show();
							$("#sm_chosen_town_name").html(e.libelle);
							$("#cus_ville_absente").val(e.libelle);
							return false;
						});
						var $wrapped = $("<div class=\"sm_town_itm\"></div>").append($link);
						if (e['id'] == 0){
							$wrapped.addClass("sm_other_link");
						}
						$("#sm_town_list").append($wrapped);
					});
				break;
			}
		}

		var ajax_link = "https://www.123devis.com/formulaires/ajax_on_cp/" + $this.val();
		if ( window.XDomainRequest ) {
			var xdr = new XDomainRequest();
			xdr.onprogress = function(){}
			xdr.onload = function(){
				success_function( xdr.responseText );
			}
			xdr.open("get", ajax_link);
			xdr.send();
		}else {
			$.ajax(ajax_link, {
				type: "GET",
				success: success_function,
				crossDomain: true,
				error: function (xhr, ajaxOptions, thrownError) {
					alert("ERREUR Validation du Code Postal : URL = '"+ajax_link+"'"
						+", Status : "+xhr.status
						+", Erreur : "+thrownError
					);
				}

			});
		}
	});

});
// Numeric only control handler
jQuery.fn.ForceNumericOnly = function()
{
	return this.each(function()
	{
		jQuery(this).keydown(function(e)
		{
			var key = e.charCode || e.keyCode || 0;
			// allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
			return (
				key == 8 ||
				key == 9 ||
				key == 46 ||
				(key >= 37 && key <= 40) ||
				(key >= 48 && key <= 57) ||
				(key >= 96 && key <= 105));
		});
	});
};
(function ( $ ) {
	$.fn.checkPhoneNumber = function() {

		var set_notice = function($field, show, message_str){
			var $itm_cntnr = $field.closest(".sm_item");
			var $exists = $itm_cntnr.find(".warning");

			//escape the cases where we do noting
			if ((show && $exists.length == 1) || (!show && $exists.length == 0)) {
				return;
			}

			if (!show && $exists.length == 1) {
				$exists.remove();
				return;
			}

			var $message = $('<label class="warning">' + message_str + '</label>');

			$message.insertAfter( $field );
		}


		return this.each(function(){
			var $form = $(this);

			var $phone 		= $('#cus_primary_phone_form', $form);
			var $sphone		= $('#cus_alternate_phone_form', $form);
			var $name		= $('#cus_last_name_form', $form);

			if ($name.length == 0 || $phone.length == 0) {
				return;
			}

			var bfunc = function(e){
				//clear for better experience
				set_notice($phone, false);

				if ($sphone.length) {
					set_notice($phone, false);
				}

				var name = $name.val();
				var phone = $phone.val();

				if ($sphone.length){
					var sphone = $sphone.val();
				} else var sphone = '';

				var phone_regex = /^(0|\+33|0033)[1-9]( *[0-9]{2}){4}$/;
				if (name == '' || ! ((phone_regex).test(phone) || (phone_regex).test(sphone)) ){
					return;
				}

				$.ajax({
					type	: 'POST',
					url		: 'https://www.123devis.com/formulaires/check_valid_phone/' + name + '-' + phone +'-' + sphone,
					dataType: 'json',
					async	: true,
					crossDomain: true,
					success	: function(result){	// recuparation du status du tel dans la PNS
						if(result){
							if(result.resMain && result.resMain.iStatus == '2'){
								set_notice($phone, true, result.resMain.sErrorMessage);
							}
							if(result.resSec && result.resSec.iStatus == '2'){
								set_notice($sphone, true, result.resSec.sErrorMessage);
							}
						}
					}
				});
			};

			$name.on("blur", bfunc);
			$phone.on("keyup", bfunc);
			if ($sphone.length) $sphone.on("keyup", bfunc);
		});
	}
}( jQuery ));