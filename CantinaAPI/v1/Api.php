<?php 


	require_once '../includes/DbOperation.php';

	function isTheseParametersAvailable($params){
	
		$available = true; 
		$missingparams = ""; 
		
		foreach($params as $param){
			if(!isset($_POST[$param]) || strlen($_POST[$param])<=0){
				$available = false; 
				$missingparams = $missingparams . ", " . $param; 
			}
		}
		
		
		if(!$available){
			$response = array(); 
			$response['error'] = true; 
			$response['message'] = 'Parameters ' . substr($missingparams, 1, strlen($missingparams)) . ' missing';
			
		
			echo json_encode($response);
			
		
			die();
		}
	}
	
	
	$response = array();
	

	if(isset($_GET['apicall'])){
		
		switch($_GET['apicall']){
	
			case 'createProdutos':
				
				isTheseParametersAvailable(array('NomeProduto','PrecoProduto','QtdeEstoque','Descricao'));
				
				$db = new DbOperation();
				
				$result = $db->createProdutos(
					$_POST['NomeProduto'],
					$_POST['PrecoProduto'],
					$_POST['QtdeEstoque'],
					$_POST['Descricao']
				);
				

			
				if($result){
					
					$response['error'] = false; 

					
					$response['message'] = 'Produto adicionado com sucesso';

					
					$response['produtos'] = $db->getProdutos();

				}else{

					
					$response['error'] = true; 

				
					$response['message'] = 'Algum erro ocorreu por favor tente novamente';
				}
				header('location: http://localhost/siteTCC/Gerenciamento.php');
			break; 
			
		
			case 'getProdutos':
					$db = new DbOperation();
					$response['error'] = false; 
					$response['message'] = 'Pedido concluído com sucesso';
					$response['produtos'] = $db->getProdutos();
				
				//header('location: http://localhost/siteTCC/Gerenciamento.php');
			break;

			case 'selectProdutos':
				$id = $_POST['IDProduto'];
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Pedido concluído com sucesso';
				$response['produtos'] = $db->selectProdutos($id);
			break;

			case 'getClientePedidos':
				$id = $_POST['IDCliente'];
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Pedido concluído com sucesso';
				$response['PedidosCliente'] = $db->getClientePedidos($id);
			break; 	



			case 'createPedido':
				$db = new DbOperation();
				isTheseParametersAvailable(array('IDCliente','DataPedido','ValorPedido'));
				
				$result = $db->createPedido(
					$_POST['IDCliente'],
					$_POST['DataPedido'],
					$_POST['ValorPedido']
				);
				

			
				if($result){
					
					$response['error'] = false;
					$response['message'] = 'Pedido realizado com sucesso';
					$response['pedidos'] = $db->getPedidos();

				}
				else
				{

					$response['error'] = true; 
					$response['message'] = 'Algum erro ocorreu, por favor tente novamente';
				}
			break;

			case 'cadastraItens':

				$db = new DbOperation();
				isTheseParametersAvailable(array('IDProduto','QuantidadeVendida'));
				
				$result = $db->cadastraItens(
					$_POST['IDProduto'],
					$_POST['QuantidadeVendida']
				);

				if($result)
				{
					
					$response['error'] = false; 
					$response['message'] = 'Pedido realizado com sucesso';

				}
				else
				{
					$response['error'] = true; 
					$response['message'] = 'Algum erro ocorreu, por favor tente novamente';
				}

			break;

			case 'retornaIDPedido':
				$db = new DbOperation();
				$result = $db->retornaIDPedido();
				if (!empty($result)) 
				{
					$response['error'] = false;
					$response['message'] = 'IDPedido resgatado.';
					$response['IDspedido'] = $result;
				} 
				else 
				{
										
					$response['error'] = true; 
					$response['message'] = 'Algum erro ocorreu, por favor tente novamente';
				}
			break;

			case 'logar':
				$db = new DbOperation();
				isTheseParametersAvailable(array('email', 'senha'));

				$result = $db->logar(
					$_POST['email'],
					$_POST['senha']
				);
				if($result){
					$response['error'] = false; 
					$response['message'] = 'logado com sucesso';
				}else {
					$response['error'] = true; 
					$response['message'] = 'email ou senha incorretos!';
				}

			break;

			case 'registrarCliente':
				$db = new DbOperation();
				isTheseParametersAvailable(array('Nome','Telefone','Email', 'Senha'));
				
				$result = $db->createPedido(
					$_POST['Nome'],
					$_POST['Telefone'],
					$_POST['Email'],
					$_POST['Senha']
				);
			
				if($result){
					
					$response['error'] = false; 

					
					$response['message'] = 'Cliente registrado com sucesso';

				}else{

					
					$response['error'] = true; 

				
					$response['message'] = 'Algum erro ocorreu por favor tente novamente';
				}
				
				
			break;


			case 'updateProdutos':
				isTheseParametersAvailable(array('IDProduto','NomeProduto','PrecoProduto','QtdeEstoque','Descricao'));
				$db = new DbOperation();
				$result = $db->updateProdutos(
					$_POST['IDProduto'],
					$_POST['NomeProduto'],
					$_POST['PrecoProduto'],
					$_POST['QtdeEstoque'],
					$_POST['Descricao']
				);
				
				if($result){
					$response['error'] = false; 
					$response['message'] = 'Produto atualizado com sucesso';
					$response['produtos'] = $db->getProdutos();
				}else{
					$response['error'] = true; 
					$response['message'] = 'Algum erro ocorreu por favor tente novamente';
				}
				header('location: http://localhost/siteTCC/Gerenciamento.php');
			break; 
			
			
			case 'deleteProdutos':

				
				if(isset($_POST['IDProduto'])){
					$db = new DbOperation();
					if($db->deleteProdutos($_POST['IDProduto'])){
						$response['error'] = false; 
						$response['message'] = 'Produto excluído com sucesso';
						$response['produtos'] = $db->getProdutos();
						header('location: http://localhost/siteTCC/Gerenciamento.php');
					}else{
						$response['error'] = true; 
						$response['message'] = 'Algum erro ocorreu por favor tente novamente';
					}
				}else{
					$response['error'] = true; 
					$response['message'] = 'Não foi possível deletar, forneça um id por favor';
				}
				
			break; 


			case 'getItensPedidos':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Pedido concluído com sucesso';
				$response['pedidos'] = $db->getItensPedidos();
			break;
			case 'getPedidos':
				$db = new DbOperation();
				$response['error'] = false; 
				$response['message'] = 'Pedido concluído com sucesso';
				$response['pedidos'] = $db->getPedidos();
			break;

			case 'cadastraItensPedidos':
                $db = new DbOperation();
                isTheseParametersAvailable(array('IDPedido','IDProduto','QuantidadeVendida'));

                $result = $db->cadastraItensPedidos(
                    $_POST['IDPedido'],
                    $_POST['IDProduto'],
                    $_POST['QuantidadeVendida']
                );


                if($result){

                    $response['error'] = false; 


                    $response['message'] = 'Pedido realizado com sucesso';


                    //$response['pedidos'] = $db->();

                }else{


                    $response['error'] = true; 


                    $response['message'] = 'Algum erro ocorreu, por favor tente novamente';
                }


            break;
		}
		
	}else{
		 
		$response['error'] = true; 
		$response['message'] = 'Chamada de API inválida';
	}
	

	echo json_encode($response);
	
	
/*SELECT Email, Senha FROM Clientes WHERE Email = 'jailson@mendes.com' AND Senha = '123pato'

	CREATE TABLE Clientes(
		Email varchar(100) not null unique key,
		Senha Varchar(100) not null
	);



*/