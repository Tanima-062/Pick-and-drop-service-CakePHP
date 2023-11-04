<?php
$json = array();
foreach($stations as $station){

	$json[] = array("id"=> $station['Station']['id'],"name"=> $station['Station']['name']);
}
echo json_encode($json, JSON_UNESCAPED_UNICODE);
?>