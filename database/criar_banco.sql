CREATE DATABASE IF NOT EXISTS `sistema`;
USE `sistema`;

DROP TABLE IF EXISTS `usuario`;

CREATE TABLE `usuario` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `tipo` enum('admin','user') DEFAULT 'user'
);

-- Login no sistema
-- -- Email: adm@adm.com
-- -- Senha: Admin123!
INSERT INTO `usuario` (`id`, `nome`, `email`, `senha_hash`, `tipo`) VALUES
(1, 'Administrador', 'adm@adm.com', '$2y$10$OG9jKA4UWAEHsxh4/m/Ig.a4KKsC0pD9v/.18u31IaH0/KjcTnJyu', 'admin'),
(2, 'renan', 'renan@gmail.com', '$2y$10$Hn1eC5k6To7RQsxgeoJt0eHLWuo24G.zmy5SzHs1Ay/kCiJFmwVcm', 'user'),
(3, 'teste', 'teste@teste.com', '$2y$10$L99CfrFdjgYWGzIfY2cC2ecfR0kXa92LvBgGZH8cw4.1NPVVetejW', 'user');

