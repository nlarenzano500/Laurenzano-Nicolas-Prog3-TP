-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 13-12-2022 a las 22:37:59
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de datos: `la_comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id_pedido` varchar(5) NOT NULL,
  `id_mesa` varchar(5) NOT NULL,
  `puntaje_mesa` tinyint(4) NOT NULL,
  `puntaje_restaurante` tinyint(4) NOT NULL,
  `puntaje_mozo` tinyint(4) NOT NULL,
  `puntaje_cocinero` tinyint(4) NOT NULL,
  `texto` varchar(66) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`id_pedido`, `id_mesa`, `puntaje_mesa`, `puntaje_restaurante`, `puntaje_mozo`, `puntaje_cocinero`, `texto`) VALUES
('P0005', 'M0001', 4, 5, 6, 7, 'Probando la encuesta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `codigo` varchar(5) NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 4
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`codigo`, `estado`) VALUES
('M0001', 4),
('M0002', 4),
('M0003', 4),
('M0004', 4),
('M0005', 4),
('M0006', 4),
('M0008', 4),
('M0010', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `operaciones`
--

CREATE TABLE `operaciones` (
  `id` int(11) NOT NULL,
  `usuario` varchar(8) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `operaciones`
--

INSERT INTO `operaciones` (`id`, `usuario`, `tipo`, `fecha`) VALUES
(5, 'admin', 'login', '2022-12-13 13:15:34'),
(6, 'mozo 01', 'login', '2022-12-13 13:15:46'),
(7, 'bar01', 'login', '2022-12-13 13:15:50'),
(8, 'user02', 'login', '2022-12-13 13:16:08'),
(9, 'cerve01', 'login', '2022-12-13 13:16:11'),
(10, 'admin', 'login', '2022-12-13 13:16:19'),
(11, 'admin', 'consulta usuario', '2022-12-13 13:18:41'),
(12, 'admin', 'suspende usuario', '2022-12-13 13:19:58'),
(13, 'admin', 'mod usuario', '2022-12-13 13:20:44'),
(14, 'admin', 'mod usuario', '2022-12-13 13:21:59'),
(15, 'admin', 'mod usuario', '2022-12-13 13:22:12'),
(16, 'admin', 'login', '2022-12-13 13:22:53'),
(17, 'admin', 'carga mesa', '2022-12-13 13:25:12'),
(18, 'mozo 01', 'mod mesa', '2022-12-13 13:28:09'),
(19, 'mozo 01', 'mod mesa', '2022-12-13 13:33:13'),
(20, 'user02', 'mod mesa', '2022-12-13 13:33:24'),
(21, 'user02', 'mod mesa', '2022-12-13 13:33:54'),
(22, 'user02', 'login', '2022-12-13 13:34:12'),
(23, 'user02', 'mod mesa', '2022-12-13 13:34:34'),
(24, 'mozo 01', 'mod mesa', '2022-12-13 13:35:08'),
(25, 'mozo 01', 'mod mesa', '2022-12-13 13:37:30'),
(26, 'mozo 01', 'mod mesa', '2022-12-13 13:37:47'),
(27, 'mozo 01', 'login', '2022-12-13 13:38:33'),
(28, 'mozo 01', 'mod mesa', '2022-12-13 13:38:57'),
(29, 'mozo 01', 'mod mesa', '2022-12-13 13:40:23'),
(30, 'mozo 01', 'mod mesa', '2022-12-13 13:41:14'),
(31, 'mozo 01', 'mod mesa', '2022-12-13 13:41:39'),
(32, 'mozo 01', 'mod mesa', '2022-12-13 13:43:00'),
(33, 'mozo 01', 'mod mesa', '2022-12-13 13:44:33'),
(34, 'mozo 01', 'mod mesa', '2022-12-13 13:45:05'),
(35, 'mozo 01', 'mod mesa', '2022-12-13 13:45:21'),
(36, 'user02', 'mod mesa', '2022-12-13 13:45:26'),
(37, 'admin', 'consulta usuarios', '2022-12-13 14:11:39'),
(38, 'admin', 'carga usuario', '2022-12-13 14:23:00'),
(39, 'admin', 'consulta usuarios', '2022-12-13 14:23:19'),
(40, 'admin', 'consulta usuarios', '2022-12-13 14:27:02'),
(41, 'admin', 'exporta usuarios', '2022-12-13 14:44:15'),
(42, 'admin', 'exporta usuarios', '2022-12-13 14:45:53'),
(43, 'admin', 'importa usuarios', '2022-12-13 14:52:33'),
(44, 'admin', 'importa usuarios', '2022-12-13 15:00:27'),
(45, 'admin', 'importa usuarios', '2022-12-13 15:01:49'),
(46, 'user02', 'consulta usuarios', '2022-12-13 15:10:30'),
(47, 'cliente', 'mod usuario', '2022-12-13 15:11:45'),
(48, 'cliente', 'mod usuario', '2022-12-13 15:12:29'),
(49, 'admin', 'mod usuario', '2022-12-13 15:12:45'),
(50, 'admin', 'login', '2022-12-13 15:12:55'),
(51, 'admin', 'exporta usuarios', '2022-12-13 15:13:04'),
(52, 'admin', 'importa usuarios', '2022-12-13 15:13:12'),
(53, 'admin', 'login', '2022-12-13 15:14:00'),
(54, 'admin', 'importa usuarios', '2022-12-13 15:20:39'),
(55, 'admin', 'importa usuarios', '2022-12-13 15:20:55'),
(56, 'admin', 'login', '2022-12-13 15:21:08'),
(57, 'admin', 'exporta usuarios', '2022-12-13 15:21:13'),
(58, 'user02', 'exporta pedidos', '2022-12-13 15:53:48'),
(59, 'user02', 'exporta pedidos', '2022-12-13 15:54:47'),
(60, 'user02', 'exporta pedidos', '2022-12-13 15:57:42'),
(61, 'user02', 'exporta pedidos', '2022-12-13 15:58:14'),
(62, 'user02', 'exporta pedidos', '2022-12-13 15:59:10'),
(63, 'user02', 'exporta pedidos', '2022-12-13 15:59:32'),
(64, 'user02', 'exporta pedidos', '2022-12-13 16:00:52'),
(65, 'user02', 'exporta pedidos', '2022-12-13 16:37:59'),
(66, 'user02', 'exporta pedidos', '2022-12-13 16:42:05'),
(67, 'user02', 'exporta pedidos', '2022-12-13 16:42:42'),
(68, 'user02', 'exporta pedidos', '2022-12-13 16:43:13'),
(69, 'user02', 'exporta pedidos', '2022-12-13 16:43:42'),
(70, 'user02', 'exporta pedidos', '2022-12-13 16:50:41'),
(71, 'user02', 'exporta pedidos', '2022-12-13 16:51:08'),
(72, 'user02', 'exporta pedidos', '2022-12-13 16:52:24'),
(73, 'user02', 'exporta pedidos', '2022-12-13 16:52:55'),
(74, 'user02', 'exporta pedidos', '2022-12-13 16:55:40'),
(75, 'user02', 'exporta pedidos', '2022-12-13 16:56:45'),
(76, 'user02', 'exporta pedidos', '2022-12-13 16:57:07'),
(77, 'user02', 'exporta pedidos', '2022-12-13 16:58:36'),
(78, 'user02', 'exporta pedidos', '2022-12-13 17:00:09'),
(79, 'user02', 'exporta pedidos', '2022-12-13 17:01:13'),
(80, 'user02', 'exporta pedidos', '2022-12-13 17:02:05'),
(81, 'admin', 'exporta usuarios', '2022-12-13 17:09:15'),
(82, 'admin', 'exporta usuarios', '2022-12-13 17:09:59'),
(83, 'admin', 'exporta usuarios', '2022-12-13 17:10:23'),
(84, 'admin', 'importa usuarios', '2022-12-13 17:15:43'),
(85, 'admin', 'importa usuarios', '2022-12-13 17:18:21'),
(86, 'admin', 'importa usuarios', '2022-12-13 17:20:08'),
(87, 'admin', 'importa usuarios', '2022-12-13 17:21:56'),
(88, 'admin', 'importa usuarios', '2022-12-13 17:24:38'),
(89, 'admin', 'importa usuarios', '2022-12-13 17:25:21'),
(90, 'admin', 'importa usuarios', '2022-12-13 17:27:31'),
(91, 'admin', 'importa usuarios', '2022-12-13 17:27:59'),
(92, 'admin', 'consulta operaciones', '2022-12-13 17:46:27'),
(93, 'admin', 'consulta operaciones', '2022-12-13 17:46:49'),
(94, 'admin', 'consulta operaciones', '2022-12-13 17:47:21'),
(95, 'admin', 'consulta operaciones', '2022-12-13 17:47:46'),
(96, 'admin', 'consulta operaciones', '2022-12-13 17:56:46'),
(97, 'admin', 'consulta operaciones', '2022-12-13 17:56:58'),
(98, 'admin', 'consulta operaciones', '2022-12-13 17:57:20'),
(99, 'cerve01', 'mod pedido', '2022-12-13 18:18:09'),
(100, 'admin', 'consulta operaciones', '2022-12-13 18:29:10'),
(101, 'admin', 'consulta operaciones', '2022-12-13 18:29:47'),
(102, 'admin', 'consulta operaciones', '2022-12-13 18:30:29'),
(103, 'admin', 'consulta operaciones', '2022-12-13 18:30:57'),
(104, 'admin', 'consulta operaciones', '2022-12-13 18:32:45'),
(105, 'admin', 'consulta operaciones', '2022-12-13 18:33:03'),
(106, 'admin', 'consulta operaciones', '2022-12-13 18:33:34'),
(107, 'admin', 'consulta operaciones', '2022-12-13 18:34:10'),
(108, 'admin', 'consulta operaciones', '2022-12-13 18:34:23'),
(109, 'admin', 'consulta operaciones', '2022-12-13 18:34:27'),
(110, 'admin', 'consulta usuarios', '2022-12-13 18:34:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `codigo` varchar(5) NOT NULL,
  `id_mesa` varchar(5) NOT NULL,
  `importe` float NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `cliente` varchar(40) NOT NULL,
  `foto` varchar(60) NOT NULL,
  `tiempo_estimado` tinyint(4) DEFAULT NULL,
  `tiempo_excedido` varchar(10) DEFAULT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`codigo`, `id_mesa`, `importe`, `estado`, `cliente`, `foto`, `tiempo_estimado`, `tiempo_excedido`, `creado`) VALUES
('P0003', 'M0003', 2800, 1, 'Cliente 04', 'pedido_P0003.jpg', NULL, NULL, '2022-12-09 00:00:00'),
('P0004', 'M0008', 3400, 2, 'Cliente 05', 'pedido_P0004.gif', 15, NULL, '2022-12-09 00:00:00'),
('P0005', 'M0001', 3400, 4, 'Cliente 01', 'pedido_P0005.jpg', 66, '08:35', '2022-12-13 00:15:00'),
('P0006', 'M0006', 4200, 4, 'Cliente 06', 'pedido_P0006.jpg', 15, '13:30', '2022-12-12 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_pedidos`
--

CREATE TABLE `productos_pedidos` (
  `id_pedido` varchar(5) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` tinyint(4) NOT NULL,
  `precio_unidad` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `productos_pedidos`
--

INSERT INTO `productos_pedidos` (`id_pedido`, `id_producto`, `cantidad`, `precio_unidad`) VALUES
('P0003', 4, 3, 600),
('P0003', 1, 2, 500),
('P0004', 5, 2, 1200),
('P0004', 2, 2, 500),
('P0004', 5, 2, 1200),
('P0004', 2, 2, 500),
('P0004', 5, 2, 1200),
('P0004', 2, 2, 500),
('P0005', 5, 2, 1200),
('P0005', 2, 2, 500),
('P0006', 4, 3, 600),
('P0006', 5, 2, 1200);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_stock`
--

CREATE TABLE `productos_stock` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(40) NOT NULL,
  `precio` float NOT NULL,
  `sector` enum('barra_vino','barra_cerveza','cocina','postres') NOT NULL,
  `vendidos` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `productos_stock`
--

INSERT INTO `productos_stock` (`id`, `descripcion`, `precio`, `sector`, `vendidos`) VALUES
(1, 'cerveza', 500, 'barra_cerveza', 3),
(2, 'cerveza roja', 500, 'barra_cerveza', 0),
(4, 'cerveza rubia', 600, 'barra_cerveza', 0),
(5, 'milanesa a caballo', 1200, 'cocina', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `perfil` enum('admin','socio','mozo','bartender','cervecero','cocinero') NOT NULL,
  `usuario` varchar(8) NOT NULL,
  `clave` varchar(250) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `sector` enum('barra_vino','barra_cerveza','cocina','postres') DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `perfil`, `usuario`, `clave`, `nombre`, `sector`, `fecha_baja`) VALUES
(23, 'admin', 'admin', '$2y$10$s8DXGEYN/Wv0pQZQkQAYWenA.TgFZsMRY2oLTI98ChqlWBUJ3VaL.', 'Administrador', NULL, NULL),
(93, 'socio', 'user02', '$2y$10$RphKZy3lURzMOmuAM40HjO8GFEiKy4l7dWtXdPWQQUo2KVYwrcA72', 'Pedro Socio', NULL, NULL),
(94, 'socio', 'user03', '$2y$10$X16k5dRVdDEPosYxydLDQ.1/GGqCElUYLybkNHZ28bL4DzYrwD/n6', 'Pancho Socio', NULL, NULL),
(95, 'socio', 'user05', '$2y$10$QbNT3g4A.k8EMGzlOtYHpOIo4b2HZUA90bhLvASTn3g9zNh5Ot5mm', 'Julio Socio', NULL, NULL),
(96, 'mozo', 'mozo01', '$2y$10$zOdPdiLP./dsb0s4q7uPk.QZLoWLBtezmehO6zVQ0Vslgm5jrgwKG', 'Pepe Mozo', NULL, NULL),
(97, 'bartender', 'bar01', '$2y$10$L9tzV7mlJgQr61b5rPY07OJw1QhajfOv3dBNhXIlj8bbe8sNo86WG', 'Bartender 01', 'barra_vino', NULL),
(98, 'cervecero', 'cerve01', '$2y$10$QNdJtgXolVltmzgkGm2ssOLtbBVrmkjZaygmI1cP.GnBUQZ/3uUu.', 'Cervecero 01', 'barra_cerveza', NULL),
(99, 'cocinero', 'coci01', '$2y$10$Pe.XDKA4.7hXNdlf8D0AYuDaO3vcAANjq5b/wQB6QrI3H8RnZkYc2', 'Cocinero 01', 'cocina', NULL),
(100, 'cocinero', 'coci02', '$2y$10$6cyTAa2m0/nlB4ypLcC2bO.qvauTqTHC/8k42.IZxUH82cwVxkK2a', 'Cocinero 02', 'postres', NULL),
(101, 'mozo', 'mozo02', '$2y$10$wMULCParZqF/csKbmkm50erTUIRqfF4EXyXiKRZEFRZKxrxz88s6O', 'Mozo 02', NULL, NULL),
(102, 'mozo', 'mozo05', '$2y$10$wI9dtt9jItC/lFHjOHf/2.bgFYy.Yvy5/JfFe6ZwCBXyBF/hsuKRm', 'Mozo 05', NULL, NULL),
(103, 'mozo', 'mozo13', '$2y$10$Nu.8SgO6pllv9sc0EQ3ejOkpow1u8zptCijKwWQ11k9wmHYTJ/Dum', 'Mozo 13', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD UNIQUE KEY `codigo_mesa` (`id_pedido`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD UNIQUE KEY `codigo_mesa` (`codigo`);

--
-- Indices de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD UNIQUE KEY `codigo_3` (`codigo`),
  ADD KEY `codigo` (`codigo`),
  ADD KEY `codigo_2` (`codigo`);

--
-- Indices de la tabla `productos_stock`
--
ALTER TABLE `productos_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `operaciones`
--
ALTER TABLE `operaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de la tabla `productos_stock`
--
ALTER TABLE `productos_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;
COMMIT;
