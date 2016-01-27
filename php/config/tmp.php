<?php
// $c = array();
// if($_POST['system'])
// 	$c = array($_POST['system']);
// else
// 	$c[] = array("Incremental encoders", "Absolute encoders", "singleturn");
// if($_POST['Design'])
// 	$c = array($_POST['Design']);
// else
// 	$c[] = array("Solid shaft encoder", "hollow shaft encoder");

// if($_POST['Resolution'])
// 	$c = array($_POST['Resolution']);
// else
// 	$c[] = array(">100 to 300", ">300 to 720", ">720 to 2500");

// if($_POST['Shaft'])
// 	$c = array($_POST['Shaft']);
// else
// 	$c[] = array("6 mm", "10 mm", "12 mm");

// if($_POST['Output'])
// 	$c = array($_POST['Output']);
// else
// 	$c[] = array("TTL", "HTL", "SSI");

// if($_POST['Features'])
// 	$c = array($_POST['Features']);
// else
// 	$c[] = array("IO-Link interface");

// if($_POST['Bit'])
// 	$c = array($_POST['Bit']);
// else
// 	$c[] = array("12", "13", "24", "25");

// if($_POST['Type'])
// 	$c = array($_POST['Type']);
// else
// 	$c[] = array("RO (58 mm)", "RU (58 mm)", "RV (58 mm)");

// if($_POST['Connection'])
// 	$c = array($_POST['Connection']);
// else
// 	$c[] = array("connector", "Cable", "terminals");

// if($_POST['approval'])
// 	$c = array($_POST['approval']);
// else
// 	$c[] = array("cNRTLus (TUV)", "e1");

// $c = utf8_string_array_encode($c);

// $tableName = "products_7";
// $cols = "System,Design,Resolution,Shaft,Output,Features,Bit,Type,Connection,approval,imgurl";
// $conn = sqlConnectDefault();
// for ($i=0;$i<$_POST['id'];$i++){
// 	$select = array();
// 	for ($j=0; $j<10; $j++) {
// 		$select[] = $c[$j][array_rand($c[$j])];
// 	}
// 	$select[] = "'https://www.ifm.com/tedo/foto/400_00".rand(10,36).".gif'";
// 	simpleInsert($select, $tableName, $cols, $conn);
	
// }



// $conn = sqlConnectDefault();
// $upd = new updateSQL($conn);
// $upd->update = "name = 'sensor', price = '4.20'";
// $upd->tableName = "products_7";
// $upd->where = "name != 'a'";
// if ($upd->executeQuery())
// $statusMessage = makeStatusMessage(26, "success", "Product added!");
// mysqli_close($conn);
// return;


phpinfo();
?>