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
   $( "#virtual_form" ).validate({
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
