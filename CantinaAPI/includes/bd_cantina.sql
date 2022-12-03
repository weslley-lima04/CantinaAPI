DROP DATABASE IF EXISTS Cantina;

	CREATE DATABASE Cantina;

	USE Cantina;

	CREATE TABLE Estabelecimento(
			IDEstabelecimento INT NOT NULL auto_increment PRIMARY KEY,
			CNPJ VARCHAR(14) NOT NULL UNIQUE KEY,
			NomeEstabelecimento VARCHAR(100) NOT NULL,
			Endereco VARCHAR(100) NOT NULL,
			Telefone VARCHAR(14) NULL,
			Email VARCHAR(100) NOT NULL,
			Senha VARCHAR(30) NOT NULL

	);

	INSERT INTO Estabelecimento
	VALUES(null, '98563256987456', 'Cantina do Tonhao', 'Rua do Tonhao', '9911547896325', 'tonhao@tonhao.com', 'tonho');

	CREATE TABLE Clientes(
		IDCliente INT auto_increment NOT NULL PRIMARY KEY,
		Nome VARCHAR(100) NOT NULL,
		Telefone BIGINT(14) NOT NULL,
		Email VARCHAR(100) NOT NULL UNIQUE KEY,
        Senha VARCHAR(100) NOT NULL
);


	
	CREATE TABLE Produtos(
			IDProduto INT NOT NULL auto_increment PRIMARY KEY,
			IDEstabelecimento INT NOT NULL,
			NomeProduto VARCHAR(40) NOT NULL,
			PrecoProduto DECIMAL(4,2) NOT NULL,
			QtdeEstoque INT NOT NULL,
			Descricao VARCHAR(250) NOT NULL,
			CONSTRAINT FK_Estabelecimento FOREIGN KEY (IDEstabelecimento) REFERENCES Estabelecimento(IDEstabelecimento)
	);

	

	INSERT INTO Produtos
	VALUES(null, 1, 'Fatia de Bolo', '4.00', '45', 'Bolo de chocolate com morango'),
	(null, 1, 'Brigadeiro', '2.00', '41', 'chocolate com granulados'),
	(null, 1, 'Mousse', '10.00', '20', 'Mousse de chocolate'),

	(null, 1, 'Suco Natural', '5.00', '10', 'Vários sabores'),
	(null, 1, 'Refrigerante', '5.00', '85', 'Vários sabores'),
	(null, 1, 'Água', '2.00', '85', 'Sem gás'),

	(null, 1, 'Coxinha', '6.00', '45', 'Frango com Catupiry'),
	(null, 1, 'Pão de Queijo', '2.50', '41', 'Quente e crocante'),
	(null, 1, 'Misto Quente', '6.00', '20', 'Com queijo e presunto'),

	(null, 1, 'Combo Batata Frita e Bacon', '10.00', '10', 'Cheedar Opcional'),
	(null, 1, 'Combo Coca e Burguer', '10.00', '10', 'Hamburguer simples'),
	(null, 1, 'Combo Café e Pão de Queijo', '5.00', '85', 'Acompanha 10 pães de queijo'),
	(null, 1, 'Salada', '6.00', '85', 'Tomate, cebola e alface');



	CREATE TABLE Pedidos(
		IDPedido INT NOT NULL auto_increment PRIMARY KEY,
		IDCliente INT NOT NULL,
		DataPedido VARCHAR(25) NOT NULL,
		ValorPedido DECIMAL(5,2) NOT NULL,
		CONSTRAINT FK_Clientes_IDCliente FOREIGN KEY (IDCliente) REFERENCES Clientes(IDCliente)
		
);


	CREATE TABLE ItensPedidos(
		IDPedido INT NOT NULL,
		IDProduto INT NOT NULL,
		QuantidadeVendida INT NOT NULL,
		CONSTRAINT FK_Produtos_IDProduto FOREIGN KEY (IDProduto) REFERENCES Produtos (IDProduto),
		CONSTRAINT FK_Pedidos_IDPedido FOREIGN KEY (IDPedido) REFERENCES Pedidos (IDPedido)
	);

	INSERT INTO Clientes
    VALUES(null, 'Rubesvaldo', '40028922', 'rubinho.peres@gmail.com', '123456');

    INSERT INTO Pedidos Values
	(null, 1, "22-08-11", "75.00");