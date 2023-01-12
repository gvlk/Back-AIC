CREATE TABLE IF NOT EXISTS gerenciamento_riscos.user (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cpf` varchar(200) NOT NULL,
  `senha` varchar(300) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`)
);