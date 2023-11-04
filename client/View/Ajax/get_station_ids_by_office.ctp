<?php
$json = array();
foreach($stationIds as $stationId){

	$json[] = $stationId;
}
echo json_encode($json, JSON_UNESCAPED_UNICODE);
?>