/**
 * * File: js/showhide.js
 * * Author: design1online.com, LLC
 * * Purpose: toggle the visibility of fields depending on the value of another field
 * **/
$(document).ready(function() {
    toggleFields(); //call this first so we start out with the correct visibility depending on the selected form values
   //this will call our toggleFields function every time the selection value of our underAge field changes
       $("#type").change(function() { 
		toggleFields(); 
	});
});
  //this toggles the visibility of our parent permission fields depending on the current selected value of the underAge field
function toggleFields() {
        if ($("#type").val() == "HTTP_GET") {
               $("#http_get").show();
               $("#tcp_check").hide();
               $("#ssl_get").hide();
               $("#misc_check").hide();
               $("#smtp_check").hide();
	}
        else if ($("#type").val() == "TCP_CHECK") {
               $("#tcp_check").show();
               $("#http_get").hide();
               $("#ssl_get").hide();
               $("#misc_check").hide();
               $("#smtp_check").hide();
	}
        else if ($("#type").val() == "SSL_GET") {
               $("#ssl_get").show();
               $("#tcp_check").hide();
               $("#http_get").hide();
               $("#misc_check").hide();
               $("#smtp_check").hide();
	}
        else if ($("#type").val() == "SMTP_CHECK") {
               $("#smtp_check").show();
               $("#tcp_check").hide();
               $("#http_get").hide();
               $("#misc_check").hide();
               $("#ssl_get").hide();
	}
        else if ($("#type").val() == "MISC_CHECK") {
               $("#misc_check").show();
               $("#tcp_check").hide();
               $("#http_get").hide();
               $("#smtp_check").hide();
               $("#ssl_get").hide();
	}
        else {
             $("#http_get").hide();
             $("#ssl_get").hide();
             $("#tcp_check").hide();
             $("#misc_check").hide();
             $("#smtp_check").hide();
	}
}
