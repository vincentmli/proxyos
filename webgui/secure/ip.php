<?php
$static_ipaddress = array(
    '192.168.3.164/24' => 'dev eth1',
    '192.168.3.165/24' => 'dev eth1',
    );

// this cycle echoes all associative array
// key where value equals "apple"
while ($ip_dev = current($static_ipaddress)) {
	$ipmask = key($static_ipaddress);
	$pieces = explode('/', $ipmask);
	echo "ip: $pieces[0], mask: $pieces[1]" . "<BR>";
        echo key($static_ipaddress) . $ip_dev . "<BR>" ;
	
    	next($static_ipaddress);
}

$a = array (
		"a1" => array ( "ip1",  "ip2", "ip3"),
		"a2" => array ( "ip4",  "ip5", "ip6"),
	);

foreach ($a as $laddrgname => $ips ) {
	echo "local_address_group $laddrgname " . " { <BR>";
		foreach ($ips as $ip) {
			echo "$ip" . "<BR>";
		}
	echo " } <BR>";
}

function displayArrayRecursively($arr, $indent='') {
    if ($arr) {
        foreach ($arr as $key => $value) {
		echo "$key " . " { <BR>";
            if (is_array($value)) {
                //
                displayArrayRecursively($value, $indent . '--');
            } else {
                //  Output
                echo "$indent $value " . " } <BR>";
            }
        }
    }
}

function mask2cidr($mask){
  $long = ip2long($mask);
  $base = ip2long('255.255.255.255');
  return 32-log(($long ^ $base)+1,2);

  /* xor-ing will give you the inverse mask,
      log base 2 of that +1 will return the number
      of bits that are off in the mask and subtracting
      from 32 gets you the cidr notation */
       
}

$cidr = mask2cidr("255.255.255.0");
//echo "$cidr \n";

function CIDRtoMask($int) {
    return long2ip(-1 << (32 - (int)$int));
}
$netmask = CIDRtoMask("24");
//echo "$netmask \n";

//$search_expression = "apple bear \"Tom Cruise\" or 'Mickey Mouse' another word";
//$words = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[\s,]+/", $search_expression, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
$search_expression = 'misc_path "t1 t2 asdgfsdg3546%*&"';
//$words = preg_split("/[\s,]*\"([^\"]+)\"[\s,]*|" . "[\s,]+/", $search_expression, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
if (strstr($search_expression,"misc_path")) {
$words = preg_split("/[\s,]*(\"[^\"]+\")/", $search_expression, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
print_r($words);
}

//displayArrayRecursively($a);

/*
foreach ($a as $v1) {
	echo key($v1) . "\n";
	foreach ($v1 as $v2 ) {
		echo "$v2\n";
	}
}
*/
		
/*
Use [ ] in the field name to send multiple values:

<input type="hidden" name="your_field_name[]" value="1" />
<input type="hidden" name="your_field_name[]" value="2" />
<input type="hidden" name="your_field_name[]" value="3" />

You will get an array of values in the your_field_name field.

*/
	
				
				
?>

