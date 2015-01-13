$(document).ready(function() {
   $( "#real_server_form" ).validate({
           rules: {
                   ip: {
                        required: true,
                        ipvalidation: true,
                   },
                   port: {
                        required: true,
                        portvalidation: true,
                   },
           },
           messages: {
                   ip: {
                        required: "IP required",
                   },
                   port: {
                        required: "Port required",
                    },
           },
   });
/*
   $( "#virtual_form" ).validate({
           rules: {
                   ip: {
                        required: false,
                        ipvalidation: true,
                   },
                   port: {
                        required: false,
                        portvalidation: true,
                   },
           },
           messages: {
                   ip: {
                        required: "IP required",
                   },
                   port: {
                        required: "Port required",
                    },
           },
   });
*/
   $.validator.addMethod("ipvalidation", function(value) {
                var split = value.split('.');
                if (split.length != 4)
                        return false;
                for (var i=0; i<split.length; i++) {
                        var s = split[i];
                        if (s.length==0 || isNaN(s) || s<0 || s>255)
                        return false;
                }
                return true;
             }, ' Invalid IP Address');

   $.validator.addMethod("portvalidation",
           function(value, element) {
                   return /^\d+$/.test(value);
           },
   	   "invalid port"
   );
});
//make exlusive virtual_server <ip port>  or <group vsg> or <fwmark int>


function delIPPort() {
//remove selected index from selected
    //    //see http://api.jquery.com/remove/
}
function addIPPort() {
    var ip = $("#virtual_ipaddress_ip"); // see http://api.jquery.com/category/selectors/
    var mask = $("#virtual_ipadress_mask");
    //http://api.jquery.com/val/ and http://api.jquery.com/append/
   $("#virtual_ipaddress").append("<option>" + ip.val() + "/" + mask.val() + "</option>"); 
   mask.val("");
   ip.val("");
}
