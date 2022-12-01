<?php 

include_once dirname(__FILE__) . '/DbConnect.php';

$response = array();

$db = new DbConnect();
$con = $db->connect();

$stmt = $con->prepare("SELECT Count(IDPedido) FROM Pedidos");
$stmt->execute();
$stmt->bind_result($id_bind);

$ids = array();

while($stmt->fetch())
{
	$id = array();
	$id['IDPedido'] = $id_bind;

	array_push($ids, $id);
}

$response["LastID"] = $ids;


echo json_encode($response);



 ?>