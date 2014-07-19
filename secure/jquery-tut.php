<head>
<meta charset="utf-8" />
<style type="text/css">
label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
</style>
<script language="javascript" type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.min.js"></script>
<script>
$(function() {
   $( "#mytestform" ).validate({
           rules: {
                   aname: "required"
           }
   });
});
</script>
</head>
<body>
<form id="mytestform" name="" method="get"  action="">
                  <p>
                   <label for="aname">Name:&nbsp;</label>
                  <input name="aname" size="20" />
                   </p>
                   <p>
                  <label for="food">Do you like Italian food:&nbsp;</label>
                  <select id="italianstatus" name="italian_food">
                          <option value="yes" selected="selected">Hell Yes!</option>
                          <option value="no">Makes me wanna puke</option>
                          <option value="sometimes">Just on Monday</option>
                  </select>
                  <input class="submit" type="submit" value="Submit"/>
                   </p>
   </form>

</body>
