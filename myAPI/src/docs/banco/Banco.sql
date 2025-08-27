-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema BancoDoHelio
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema BancoDoHelio
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `BancoDoHelio` DEFAULT CHARACTER SET utf8 ;
USE `BancoDoHelio` ;

-- -----------------------------------------------------
-- Table `BancoDoHelio`.`Usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `BancoDoHelio`.`Usuario` (
  `idUsuario` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'user') DEFAULT 'user',
  `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `ultimo_login` TIMESTAMP NULL,
  PRIMARY KEY (`idUsuario`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `BancoDoHelio`.`Fornecedor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `BancoDoHelio`.`Fornecedor` (
  `idFornecedor` INT NOT NULL AUTO_INCREMENT,
  `nomeFornecedor` VARCHAR(45) NOT NULL,
  `telefone` VARCHAR(45) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `endereco` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`idFornecedor`),
  UNIQUE INDEX `id_UNIQUE` (`idFornecedor` ASC))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `BancoDoHelio`.`Categoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `BancoDoHelio`.`Categoria` (
  `idCategoria` INT NOT NULL AUTO_INCREMENT,
  `nomeCategoria` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idCategoria`),
  UNIQUE INDEX `id_UNIQUE` (`idCategoria` ASC))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `BancoDoHelio`.`Produto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `BancoDoHelio`.`Produto` (
  `idProduto` INT NOT NULL AUTO_INCREMENT,
  `nomeProduto` VARCHAR(45) NOT NULL,
  `precoProduto` DECIMAL(10,2) NOT NULL,
  `quantidade_estoqueProduto` INT NOT NULL,
  `Fornecedor_idFornecedor` INT NULL,
  `categoria_idCategoria` INT NULL,
  PRIMARY KEY (`idProduto`),
  INDEX `fk_Produto_Fornecedor_idx` (`Fornecedor_idFornecedor` ASC),
  INDEX `fk_Produto_categoria1_idx` (`categoria_idCategoria` ASC),
  UNIQUE INDEX `idFornecedor_UNIQUE` (`idProduto` ASC),
  CONSTRAINT `fk_Produto_Fornecedor`
    FOREIGN KEY (`Fornecedor_idFornecedor`)
    REFERENCES `BancoDoHelio`.`Fornecedor` (`idFornecedor`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Produto_categoria1`
    FOREIGN KEY (`categoria_idCategoria`)
    REFERENCES `BancoDoHelio`.`Categoria` (`idCategoria`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `BancoDoHelio`.`Usuario`
-- -----------------------------------------------------
START TRANSACTION;
USE `BancoDoHelio`;
INSERT INTO `BancoDoHelio`.`Usuario` (`nome`, `email`, `senha`, `role`) VALUES 
('Administrador', 'admin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Usuário Teste', 'user@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

COMMIT;

-- -----------------------------------------------------
-- Data for table `BancoDoHelio`.`Fornecedor`
-- -----------------------------------------------------
START TRANSACTION;
USE `BancoDoHelio`;
INSERT INTO `BancoDoHelio`.`Fornecedor` (`idFornecedor`, `nomeFornecedor`, `telefone`, `email`, `endereco`) VALUES (1, 'Paulo Rodrigo', '12345678', 'paulao@email.com', 'Rua Jacinto Leite 199');
INSERT INTO `BancoDoHelio`.`Fornecedor` (`idFornecedor`, `nomeFornecedor`, `telefone`, `email`, `endereco`) VALUES (2, 'Davi Dancuart', '12996324', 'furque@email.com', 'Rua Jacinto Pinto 24');
INSERT INTO `BancoDoHelio`.`Fornecedor` (`idFornecedor`, `nomeFornecedor`, `telefone`, `email`, `endereco`) VALUES (3, 'Sergio Berranteiro', '87654321', 'matadordeonca@email.com', 'Rua Berranteiro 124');
INSERT INTO `BancoDoHelio`.`Fornecedor` (`idFornecedor`, `nomeFornecedor`, `telefone`, `email`, `endereco`) VALUES (4, 'Manuel Gomez', '40028922', 'canetaazul@email.com', 'Rua Emanuel Da Silva 200');

COMMIT;

-- -----------------------------------------------------
-- Data for table `BancoDoHelio`.`Categoria`
-- -----------------------------------------------------
START TRANSACTION;
USE `BancoDoHelio`;
INSERT INTO `BancoDoHelio`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES (1, 'Frutas');
INSERT INTO `BancoDoHelio`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES (2, 'Doces');
INSERT INTO `BancoDoHelio`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES (3, 'Limpeza');
INSERT INTO `BancoDoHelio`.`Categoria` (`idCategoria`, `nomeCategoria`) VALUES (4, 'Material Escolar');

COMMIT;

-- -----------------------------------------------------
-- Data for table `BancoDoHelio`.`Produto`
-- -----------------------------------------------------
START TRANSACTION;
USE `BancoDoHelio`;
INSERT INTO `BancoDoHelio`.`Produto` (`idProduto`, `nomeProduto`, `precoProduto`, `quantidade_estoqueProduto`, `Fornecedor_idFornecedor`, `categoria_idCategoria`) VALUES (1, 'Banana', 6.00, 142, 1, 1);
INSERT INTO `BancoDoHelio`.`Produto` (`idProduto`, `nomeProduto`, `precoProduto`, `quantidade_estoqueProduto`, `Fornecedor_idFornecedor`, `categoria_idCategoria`) VALUES (2, 'Tomate', 8.00, 121, 1, 1);
INSERT INTO `BancoDoHelio`.`Produto` (`idProduto`, `nomeProduto`, `precoProduto`, `quantidade_estoqueProduto`, `Fornecedor_idFornecedor`, `categoria_idCategoria`) VALUES (3, 'Maça', 4.00, 60, 1, 1);
INSERT INTO `BancoDoHelio`.`Produto` (`idProduto`, `nomeProduto`, `precoProduto`, `quantidade_estoqueProduto`, `Fornecedor_idFornecedor`, `categoria_idCategoria`) VALUES (4, 'Donut', 14.00, 20, 2, 2);
INSERT INTO `BancoDoHelio`.`Produto` (`idProduto`, `nomeProduto`, `precoProduto`, `quantidade_estoqueProduto`, `Fornecedor_idFornecedor`, `categoria_idCategoria`) VALUES (5, 'Bomba Recheada', 18.00, 14, 2, 2);
INSERT INTO `BancoDoHelio`.`Produto` (`idProduto`, `nomeProduto`, `precoProduto`, `quantidade_estoqueProduto`, `Fornecedor_idFornecedor`, `categoria_idCategoria`) VALUES (6, 'Veja', 5.00, 112, 3, 3);
INSERT INTO `BancoDoHelio`.`Produto` (`idProduto`, `nomeProduto`, `precoProduto`, `quantidade_estoqueProduto`, `Fornecedor_idFornecedor`, `categoria_idCategoria`) VALUES (7, 'Caneta', 1.00, 343, 4, 4);

COMMIT;
DROP TABLE IF EXISTS `BancoDoHelio`.`Usuario`;
SELECT * FROM `BancoDoHelio`.`Usuario`;
DESCRIBE `BancoDoHelio`.`Usuario`;