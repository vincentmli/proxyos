<html>
<head>
    <title>Test Hide/Show Fields</title>
    <!--script language="javascript" type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script-->
    <script language="javascript" type="text/javascript" src="jquery-1.11.0.js"></script>
    <script language="javascript" type="text/javascript" src="showhide.js"></script>
</head>
<body>
    <?php
        //this will show you the values in the form the data when you hit the submit button
        if ($_POST) {
            echo "Form was submitted, here are the form values: <pre>";
            print_r($_POST);
            echo "</pre>";
        }
    ?>
    <form method="GET">
        <p>Name: <input type="text" name="player_name" /></p>
        <p>Email: <input type="text" name="player_email" /></p>
	<p>static address:
	<textarea name="static_address" rows="4" cols="50">
192.168.3.164/24 dev eth1 scope global
192.168.3.165/24 dev eth1 scope global
	</textarea>
        <p>Age: 
             <select id="age" name="age">
             <?php
                  //sorry but if you're over 30 you're too old to join, lol
                  for ($age = 6; $age <= 30; $age++)
                       echo "<option value=\"$age\">$age</option>";
             ?>
             </select>
        </p>
        <div id="parentPermission">
                <p>Parent's Name: <input type="text" name="parent_name" /></p>
                <p>Parent's Email: <input type="text" name="parent_email" /></p>
                <p>You must have parental permission before you can play.</p>
        </div>
        <p align="center"><input type="submit" value="Join Game!" /></p> 
 </form>
<?php print_r($_GET);?>
</body>
</html>
