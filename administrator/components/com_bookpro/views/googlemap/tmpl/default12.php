<?php

// Get a db connection.
$db = JFactory::getDbo();
 
// Create a new query object.
$query = $db->getQuery(true);
 
// Select all records from the user profile table where key begins with "custom.".
$query->select ( ' a.from, a.to,a.recipient_info, a.address');
$query->from($db->quoteName('#__bookpro_orders') . ' AS a');

$db->setQuery($query);
$column1= $db->loadObjectList();
//$column1= $db->fetchAll(PDO::FETCH_ASSOC);

/*
echo "<pre>";
print_r($column1);
echo "</pre>";*/


/*$column2= $db->loadRowList();
echo "<pre>";
print_r($column2);
echo "</pre>";*/

//$newArray	= array($db->loadAssocList(),$db->loadRowList());
				
/*echo "<pre>";
print_r($newArray);
echo "</pre>";*/
/*$arr = array();
$arr["1"] = 1;
$arr["0"] = 1;
$arr["3"] = 1;
foreach ($arr as $key=>$value){
	echo $key.$value + "<br/>";
	
}*/
 //$lat=$column['from'];
 //$lon=$column['to'];
 
//echo ("addMarker($lat, $lon,'');");


//Mang ban dau
/*array (
		[0]=>array { [from]: abc, xyz [to] cde, fgh}
		[1]=> array { [from]: a, b [to]: c,d}
	}

//Mang sau khi tach
array{
	[0]=>array {
			[from]=>array{
							[lat]: abc 
							[lng]: xyz	
						}
			[to]=>array{
							[lat]: cde 
							[lng]: fgh
						}
		}
	[1]=>array {
			[from]=>array{
							[lat]: a 
							[lng]: b	
						}
			[to]=>array{
							[lat]:c 
							[lng]: d
						]
				}
	}	*/

echo json_encode($column1);
