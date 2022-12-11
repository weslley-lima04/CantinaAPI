<?php
 
class DbOperation
{
    
    private $con;
 
 
    function __construct()
    {
  
        require_once dirname(__FILE__) . '/DbConnect.php';
 
     
        $db = new DbConnect();
 

        $this->con = $db->connect();
    }

	function createProdutos($NomeProduto, $PrecoProduto, $QtdeEstoque, $Descricao){
		$stmt = $this->con->prepare("INSERT INTO Produtos (NomeProduto, IDEstabelecimento, PrecoProduto, QtdeEstoque, Descricao) VALUES (?, 1, ?, ?, ?)");
		$stmt->bind_param("sdis", $NomeProduto, $PrecoProduto, $QtdeEstoque, $Descricao);
		if($stmt->execute())
			return true; 
		return false; 

		
	}
	
	function createPedido($IDCliente, $DataPedido, $ValorPedido)
	{
		$stmt = $this->con->prepare("INSERT INTO Pedidos (IDCliente, DataPedido, ValorPedido) VALUES (?, ?, ?)");
		$stmt->bind_param("isd", $IDCliente, $DataPedido, $ValorPedido);
		if($stmt->execute())
		{
			return true; 
		}
		else
			return false;
	}

	function retornaIDPedido()
	{
		$stmt = $this->con->prepare("SELECT Count(IDPedido) AS Final_IDPedido FROM Pedidos");
		$stmt->execute();
		$stmt->bind_result($id);
		$idPedido = 0;
		while($stmt->fetch())
		{
			$idPedido = $id;
		}
		return $idPedido;
	}

	


	function getClientePedidos($IDCliente)
	{
		$sql = "SELECT IDPedido, IDCliente, DataPedido, ValorPedido FROM Pedidos WHERE IDCliente = ".$IDCliente;
		$stmt = $this->con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($IDPedido, $IDCliente, $DataPedido, $ValorPedido);

		$pedidos = array(); 
		
		while($stmt->fetch()){
			$pedido  = array();
			$pedido['IDPedido'] = $IDPedido;
			$pedido['IDCliente'] = $IDCliente;	
			$pedido['DataPedido'] = $DataPedido; 
			$pedido['ValorPedido'] = $ValorPedido;		
			array_push($pedidos, $pedido); 
		}
		
		return $pedidos;

	}


	function cadastraItens($IDProduto, $QuantidadeVendida)
	{

		$IDPedido = $this->retornaIDPedido();
		$stmt = $this->con->prepare("INSERT INTO ItensPedidos (IDPedido, IDProduto, QuantidadeVendida) VALUES (?, ?, ?)");
		$stmt->bind_param("iii", $IDPedido, $IDProduto, $QuantidadeVendida);
		if($stmt->execute())
			return true; 
		return false; 
	}

	function logar($email, $senha)
	{
		$stmt = $this->con->prepare("SELECT Email, Senha FROM Clientes WHERE Email = ? AND Senha = ?");
		$stmt->bind_param("ss", $email, $senha);
		$stmt->execute();
		$resultado = 0;
		
		while($stmt->fetch())
		{
			$resultado++;
		}
		
		//var_dump($sttLogin);
		//$sttLogin = $sttLogin[0]['Status'];

		$resp = array();
		if($resultado > 0)
		{
			$id['ID'] = $this->retornaIDCliente($email, $senha);
			$this->mudarStatusLogin($id['ID']);
			$sttLogin = $this->statusLogin($id['ID']);

			if($sttLogin)
			{
				$id = array();
				$id['ID'] = $this->retornaIDCliente($email, $senha);
				$dados = $this->pegarDadosUsuario($id['ID']);
				//armazenando os dados do usuário no array dados
				array_push($resp, $dados);
				return $resp;
			}
				
		}
		else
		 return false; 
		
	}
	
	//PEGA OS DADOS DO USUÁRIO SE statusLogin for 1

	function pegarDadosUsuario($id)
	{
		$stmt = $this->con->prepare("SELECT Clientes.IDCliente, Clientes.Nome,
										 Clientes.Telefone, Clientes.Email, Clientes.Senha
										 FROM Clientes WHERE Clientes.IDCliente = '$id'");
		$stmt->execute();
		$stmt->bind_result($IDCliente, $Nome, $Telefone, $Email, $Senha);
		

		//$dadosCliente = array(); 
		$dado  = array();
		while($stmt->fetch())
		{

			$dado['IDCliente'] = $IDCliente;
			$dado['Nome'] = $Nome;	
			$dado['Telefone'] = $Telefone; 
			$dado['Email'] = $Email;
			$dado['Senha'] = $Senha;
			//array_push($dadosCliente, $dado); 
		}

		$sttLogin = $this->statusLogin($dado['IDCliente']);
		$sttLogin = $sttLogin[0]["Status"];

		if($sttLogin)
		{
			$dado['statusLogin'] = $sttLogin;
			return $dado;
		}
		return false;
		
	}

	//MUDA O STATUS DO LOGIN DEPENDENDO DO QUE ESTEJA
	//função auxiliar
	function mudarStatusLogin($id)
	{
		$stmt = $this->con->prepare("SELECT statusLogin FROM Logado WHERE IDCliente = '$id'");
		$stmt->bind_result($status);
		$stmt->execute();

		$resultado = 0;
		$statusLg  = array();

		while($stmt->fetch()){
			$resultado++;
			$Lg = array();
			$Lg['statusLogin'] = $status; 
		
			array_push($statusLg, $Lg); 
		}

		
	
		if($resultado > 0)
		{
			$statusLoginFinal = $statusLg[0]['statusLogin'];
			if ($statusLoginFinal == 0)
			{
				$stmt = $this->con->prepare("UPDATE Logado SET statusLogin = 1,  DataLogin = NOW() WHERE IDCliente = '$id'");
			}else 
			{
				$stmt = $this->con->prepare("UPDATE Logado SET statusLogin = 0,  DataLogin = NOW() WHERE IDCliente = '$id'");
			}
		}else 
		{
			$stmt = $this->con->prepare("INSERT INTO Logado (statusLogin, IDCliente)VALUES (1 ,'$id')");
		}
		if($stmt->execute())
			return true;
		return false;	
		
		
	}

	//PEGA O STATUS :D

	function statusLogin($idCliente)
	{

		$stmt = $this->con->prepare("SELECT statusLogin FROM Logado WHERE IDCliente = '$idCliente'");
		$stmt->execute();
		$stmt->bind_result($id);
		$resultado = 0;

		while($stmt->fetch())
		{
			$resultado++;
		}
		
		$status= null;
		if($id == 1)
		{
			$status = true;
		}
		else
		{
			$status = false;
		}	

		$resp = array();
		if($resultado > 0)
		{
			$idArray = array();
			$idArray['Status'] = $status;
			array_push($resp, $idArray);
			return $resp;
		}
		else
		 return false; 
	}


	function retornaIDCliente($email, $senha)
	{

		$stmt = $this->con->prepare("SELECT IDCliente FROM Clientes WHERE Email = '$email' AND Senha = '$senha'");
		$stmt->execute();
		$stmt->bind_result($id);
		$idFinal = 0;
		while($stmt->fetch())
		{
	
			$idFinal = $id;
			
		}
		return $idFinal; 
	}


	function registrarCliente($Nome, $Telefone, $Email, $senha)
	{
		$stmt = $this->con->prepare("INSERT INTO Clientes (Nome, Telefone, Email, Senha) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("siss", $Nome, $Telefone, $Email, $senha);
		if($stmt->execute())
			return true; 
		return false; 
	}


		
	function getProdutos(){
		$stmt = $this->con->prepare("SELECT IDProduto, NomeProduto, PrecoProduto, QtdeEstoque, Descricao FROM Produtos");
		$stmt->execute();
		$stmt->bind_result($id, $NomeProduto, $PrecoProduto, $QtdeEstoque, $Descricao);
		
		$produtos = array(); 
		
		while($stmt->fetch()){
			$produto  = array();
			$produto['IDProduto'] = $id; 
			$produto['NomeProduto'] = $NomeProduto; 
			$produto['PrecoProduto'] = $PrecoProduto; 
			$produto['QtdeEstoque'] = $QtdeEstoque; 
			$produto['Descricao'] = $Descricao; 

			array_push($produtos, $produto); 
		}
		
		return $produtos; 
	}
	function getPedidos(){
		$stmt = $this->con->prepare("SELECT Pedidos.IDPedido,
									Pedidos.DataPedido,
									Pedidos.ValorPedido,
									Pedidos.Confirmado,
									Clientes.Nome,
									Produtos.NomeProduto,
									Produtos.PrecoProduto,
									ItensPedidos.QuantidadeVendida
									FROM Produtos INNER JOIN (ItensPedidos INNER JOIN
									(Pedidos INNER JOIN Clientes ON Clientes.IDCliente = Pedidos.IDCliente)
									ON ItensPedidos.IDPedido = Pedidos.IDPedido)
									ON ItensPedidos.IDProduto = Produtos.IDProduto
									ORDER BY IDPedido");
	$stmt->execute();
	$stmt->bind_result($IDPedido, $DataPedido, $ValorPedido, $Confirmado, $Nome, $NomeProduto, $PrecoProduto, $QuantidadeVendida);
	
	$pedidos = array(); 
	
	while($stmt->fetch()){
		$pedido  = array();
		$pedido['IDPedido'] = $IDPedido;
		$pedido['DataPedido'] = $DataPedido;
		$pedido['ValorPedido'] = $ValorPedido;
		$pedido['Confirmado'] = $Confirmado;
		$pedido['Nome'] = $Nome;
		$pedido['NomeProduto'] = $NomeProduto;
		$pedido['PrecoProduto'] = $PrecoProduto;
		$pedido['QuantidadeVendida'] = $QuantidadeVendida;
		array_push($pedidos, $pedido);
	}
	
	return $pedidos; 
	}
	
	function confirmarPedido($Confirmado, $IDPedido){
		$stmt = $this->con->prepare("UPDATE Pedidos SET Confirmado = ? WHERE IDPedido = ?");
		$stmt->bind_param("ii", $Confirmado, $IDPedido);
		if($stmt->execute())
			return true; 
		return false; 
	} 

	function cadastraItensPedidos($IDPedido, $IDProduto, $QuantidadeVendida)
    {
        $stmt = $this->con->prepare("INSERT INTO ItensPedidos (IDPedido, IDProduto, QuantidadeVendida) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $IDPedido, $IDProduto, $QuantidadeVendida);
        if($stmt->execute())
                return true; 
        return false; 
    }

	function getItensPedidos(){
		$stmt = $this->con->prepare("SELECT Produtos.NomeProduto,
									Produtos.PrecoProduto,
									ItensPedidos.QuantidadeVendida
									FROM Produtos INNER JOIN
									ItensPedidos ON ItensPedidos.IDProduto = Produtos.IDProduto
									ORDER BY Produtos.IDProduto");

	$stmt->execute();
	$stmt->bind_result($NomeProduto, $PrecoProduto, $QuantidadeVendida);
	
	$pedidos = array(); 
		
		while($stmt->fetch()){
			$pedido  = array();
			$pedido['NomeProduto'] = $NomeProduto; 
			$pedido['QuantidadeVendida'] = $QuantidadeVendida; 
			$pedido['PrecoProduto'] = $PrecoProduto; 
			
			array_push($pedidos, $pedido); 
		}
		return $pedidos; 
	}

	function selectProdutos($search){
		$stmt = $this->con->prepare("SELECT IDProduto, NomeProduto, PrecoProduto, QtdeEstoque, Descricao
		FROM Produtos WHERE IDProduto LIKE '%$search%' or
		IDEstabelecimento LIKE '%$search%' or
		NomeProduto LIKE '%$search%' or
		PrecoProduto LIKE '%$search%' or
		QtdeEstoque LIKE '%$search%' or
		Descricao LIKE '%$search%'");
		$stmt->execute();
		$stmt->bind_result($id, $NomeProduto, $PrecoProduto, $QtdeEstoque, $Descricao);
		
		$produtos = array(); 
		
		while($stmt->fetch()){
			$produto  = array();
			$produto['IDProduto'] = $id; 
			$produto['NomeProduto'] = $NomeProduto; 
			$produto['PrecoProduto'] = $PrecoProduto; 
			$produto['QtdeEstoque'] = $QtdeEstoque; 
			$produto['Descricao'] = $Descricao; 
			
			array_push($produtos, $produto); 
			
	}
	return $produtos; 
}
	
	
	function updateProdutos($id, $NomeProduto, $PrecoProduto, $QtdeEstoque, $Descricao){
		$stmt = $this->con->prepare("UPDATE Produtos SET NomeProduto = ?, PrecoProduto = ?, QtdeEstoque = ?, Descricao = ? WHERE IDProduto = ?");
		$stmt->bind_param("sdisi", $NomeProduto, $PrecoProduto, $QtdeEstoque, $Descricao, $id);
		if($stmt->execute())
			return true; 
		return false; 
	} 
	
	
	function deleteProdutos($id){
		$stmt = $this->con->prepare("DELETE FROM ItensPedidos WHERE IDProduto = ? ");
		$stmt->bind_param("i", $id);
		if($stmt->execute()){
			$stmt = $this->con->prepare("DELETE FROM Produtos WHERE IDProduto = ? ");
			$stmt->bind_param("i", $id);
			if($stmt->execute())
			return true;
		}return false; 
		
		
	}
}