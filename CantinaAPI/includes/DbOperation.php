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
		$sql = "SELECT * FROM Pedidos WHERE IDCliente = ".$IDCliente;
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
		while($stmt->fetch()){
			$resultado++;
		}
		//echo $resultado;
		if($resultado > 0)
		{
			$id = $this->retornaIDCliente($email, $senha);
			echo "O id do cliente é: ".$id;
			return $id;
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
									Clientes.Nome,
									Pedidos.ValorPedido
									FROM Pedidos INNER JOIN
									Clientes ON Clientes.IDCliente = Pedidos.IDCliente
									ORDER BY Pedidos.IDPedido");
		$stmt->execute();
		$stmt->bind_result($IDPedido, $DataPedido, $Nome, $ValorPedido);
		
		$pedidos = array(); 
		
		while($stmt->fetch()){
			$pedido  = array();
			$pedido['IDPedido'] = $IDPedido;
			$pedido['DataPedido'] = $DataPedido; 
			$pedido['Nome'] = $Nome;		
			$pedido['ValorPedido'] = $ValorPedido;		
			array_push($pedidos, $pedido); 
		}
		
		return $pedidos; 
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