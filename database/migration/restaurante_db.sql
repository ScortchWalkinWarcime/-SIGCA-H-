-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-05-2026 a las 21:28:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `restaurante_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas`
--

CREATE TABLE `alertas` (
  `cve_alerta` int(11) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `mensaje` varchar(150) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `cve_temp` int(11) DEFAULT NULL,
  `cve_agua` int(11) DEFAULT NULL,
  `cve_higiene` int(11) DEFAULT NULL,
  `cve_area` int(11) DEFAULT NULL,
  `cve_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas`
--

CREATE TABLE `areas` (
  `cve_area` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `cve_restaurante` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `areas`
--

INSERT INTO `areas` (`cve_area`, `nombre`, `cve_restaurante`) VALUES
(1, 'Congelador', 1),
(2, 'Refrigerador', 1),
(3, 'Camara Refrigeracion', 1),
(4, 'Camara Congelacion', 1),
(5, 'Buffet Frio', 1),
(6, 'Buffet Caliente', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control_agua`
--

CREATE TABLE `control_agua` (
  `cve_agua` int(11) NOT NULL,
  `ph` float DEFAULT NULL,
  `cloro` float DEFAULT NULL,
  `observaciones` varchar(150) DEFAULT NULL,
  `cve_usuario` int(11) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `potabilidad` varchar(20) DEFAULT NULL,
  `temperatura` float DEFAULT NULL,
  `turbidez` float DEFAULT NULL,
  `dureza` float DEFAULT NULL,
  `metales_pesados` varchar(50) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control_higiene`
--

CREATE TABLE `control_higiene` (
  `cve_higiene` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `manos_limpias` tinyint(1) DEFAULT NULL,
  `uniforme` tinyint(1) DEFAULT NULL,
  `cofia` tinyint(1) DEFAULT NULL,
  `sinjoyeria` tinyint(1) DEFAULT NULL,
  `incumplimiento` varchar(150) DEFAULT NULL,
  `cve_usuario` int(11) DEFAULT NULL,
  `guantes` varchar(20) DEFAULT NULL,
  `cubrebocas` varchar(20) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control_temperatura`
--

CREATE TABLE `control_temperatura` (
  `cve_temp` int(11) NOT NULL,
  `id_area` int(11) DEFAULT NULL,
  `valor` float DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `cve_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `control_temperatura`
--

INSERT INTO `control_temperatura` (`cve_temp`, `id_area`, `valor`, `fecha`, `cve_usuario`) VALUES
(101, 2, 5, '2026-04-30', 2),
(181, 1, -17, '2026-04-28', 1),
(182, 1, -19, '2026-04-28', 1),
(183, 1, -18, '2026-04-28', 1),
(184, 1, -20, '2026-04-28', 1),
(185, 1, -16, '2026-04-28', 1),
(186, 2, 2.5, '2026-04-28', 1),
(187, 2, 3, '2026-04-28', 1),
(188, 2, 4.2, '2026-04-28', 1),
(189, 2, 1.8, '2026-04-28', 1),
(190, 2, 5.5, '2026-04-28', 1),
(191, 3, 3.5, '2026-04-28', 1),
(192, 3, 2.8, '2026-04-28', 1),
(193, 3, 4.5, '2026-04-28', 1),
(194, 3, 1.2, '2026-04-28', 1),
(195, 3, 0.5, '2026-04-28', 1),
(196, 4, -19, '2026-04-28', 1),
(197, 4, -18, '2026-04-28', 1),
(198, 4, -17, '2026-04-28', 1),
(199, 4, -20, '2026-04-28', 1),
(200, 4, -16, '2026-04-28', 1),
(201, 1, -18, '2026-04-28', 1),
(202, 2, 3.1, '2026-04-28', 1),
(203, 3, 2.9, '2026-04-28', 1),
(204, 4, -19, '2026-04-28', 1),
(205, 2, 4, '2026-04-28', 1),
(206, 1, -17, '2026-04-28', 1),
(207, 2, 2.2, '2026-04-28', 1),
(208, 3, 3.7, '2026-04-28', 1),
(209, 4, -18, '2026-04-28', 1),
(210, 2, 5.2, '2026-04-28', 1),
(211, 1, -21, '2026-04-28', 1),
(212, 2, 1.5, '2026-04-28', 1),
(213, 3, 4.1, '2026-04-28', 1),
(214, 4, -17, '2026-04-28', 1),
(215, 2, 3.8, '2026-04-28', 1),
(216, 1, -18, '2026-04-28', 1),
(217, 2, 2.7, '2026-04-28', 1),
(218, 3, 3.3, '2026-04-28', 1),
(219, 4, -19, '2026-04-28', 1),
(220, 2, 4.6, '2026-04-28', 1),
(221, 1, -16, '2026-04-28', 1),
(222, 2, 0.9, '2026-04-28', 1),
(223, 3, 2.1, '2026-04-28', 1),
(224, 4, -20, '2026-04-28', 1),
(225, 2, 3.4, '2026-04-28', 1),
(226, 1, -18, '2026-04-28', 1),
(227, 2, 3.9, '2026-04-28', 1),
(228, 3, 1.7, '2026-04-28', 1),
(229, 4, -18, '2026-04-28', 1),
(230, 2, 2.6, '2026-04-28', 1),
(231, 1, -19, '2026-04-28', 1),
(232, 2, 4.8, '2026-04-28', 1),
(233, 3, 3, '2026-04-28', 1),
(234, 4, -17, '2026-04-28', 1),
(235, 2, 1.2, '2026-04-28', 1),
(236, 1, -18, '2026-04-28', 1),
(237, 2, 2.4, '2026-04-28', 1),
(238, 3, 4.2, '2026-04-28', 1),
(239, 4, -19, '2026-04-28', 1),
(240, 2, 3.3, '2026-04-28', 1),
(241, 1, -17, '2026-04-28', 1),
(242, 2, 3.6, '2026-04-28', 1),
(243, 3, 2.5, '2026-04-28', 1),
(244, 4, -20, '2026-04-28', 1),
(245, 2, 4.1, '2026-04-28', 1),
(246, 1, -18, '2026-04-28', 1),
(247, 2, 2, '2026-04-28', 1),
(248, 3, 3.8, '2026-04-28', 1),
(249, 4, -18, '2026-04-28', 1),
(250, 2, 5, '2026-04-28', 1),
(251, 1, -19, '2026-04-28', 1),
(252, 2, 1.9, '2026-04-28', 1),
(253, 3, 2.6, '2026-04-28', 1),
(254, 4, -17, '2026-04-28', 1),
(255, 2, 3.7, '2026-04-28', 1),
(256, 1, -18, '2026-04-28', 1),
(257, 2, 4.3, '2026-04-28', 1),
(258, 3, 3.4, '2026-04-28', 1),
(259, 4, -19, '2026-04-28', 1),
(260, 2, 2.8, '2026-04-28', 1),
(261, 1, -17, '2026-04-28', 1),
(262, 2, 3.2, '2026-04-28', 1),
(263, 3, 1.6, '2026-04-28', 1),
(264, 4, -18, '2026-04-28', 1),
(265, 2, 4.7, '2026-04-28', 1),
(266, 1, -20, '2026-04-28', 1),
(267, 2, 2.3, '2026-04-28', 1),
(268, 3, 3.9, '2026-04-28', 1),
(269, 4, -19, '2026-04-28', 1),
(270, 2, 1.4, '2026-04-28', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_reporte`
--

CREATE TABLE `detalle_reporte` (
  `cve_detalle` int(11) NOT NULL,
  `cve_reporte` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `cve_temp` int(11) DEFAULT NULL,
  `cve_agua` int(11) DEFAULT NULL,
  `cve_alerta` int(11) DEFAULT NULL,
  `cve_higiene` int(11) DEFAULT NULL,
  `cve_recepcion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametros_temperatura`
--

CREATE TABLE `parametros_temperatura` (
  `id_parametro` int(11) NOT NULL,
  `nombre_area` varchar(100) DEFAULT NULL,
  `temp_min` float DEFAULT NULL,
  `temp_max` float DEFAULT NULL,
  `descripcion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `parametros_temperatura`
--

INSERT INTO `parametros_temperatura` (`id_parametro`, `nombre_area`, `temp_min`, `temp_max`, `descripcion`) VALUES
(1, 'Congelador', NULL, -18, 'Temperatura máxima -18°C o menor'),
(2, 'Refrigerador', 0, 4, 'Temperatura entre 0°C y 4°C'),
(3, 'Camara Refrigeracion', 0, 4, 'Máximo 4°C'),
(4, 'Camara Congelacion', NULL, -18, 'Menor o igual a -18°C'),
(5, 'Buffet Frio', NULL, 7, 'Máximo 7°C'),
(6, 'Buffet Caliente', 60, NULL, 'Mínimo 60°C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recepcion_alimentos`
--

CREATE TABLE `recepcion_alimentos` (
  `cve_recepcion` int(11) NOT NULL,
  `producto` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `temperatura` float DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `cve_usuario` int(11) DEFAULT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `resultado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte_sanitario`
--

CREATE TABLE `reporte_sanitario` (
  `cve_reporte` int(11) NOT NULL,
  `cve_restaurante` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado_general` varchar(100) DEFAULT NULL,
  `cve_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `restaurante`
--

CREATE TABLE `restaurante` (
  `cve_restaurante` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `restaurante`
--

INSERT INTO `restaurante` (`cve_restaurante`, `nombre`, `direccion`) VALUES
(1, 'LafonditaClio', 'Av.Mexico13 apan hidalgo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salida_alimentos`
--

CREATE TABLE `salida_alimentos` (
  `cve_salida` int(11) NOT NULL,
  `producto` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cve_usuario` int(11) DEFAULT NULL,
  `temperatura` float DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `resultado` varchar(20) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `cve_usuario` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `contrasena` varchar(100) DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL,
  `cve_restaurante` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`cve_usuario`, `nombre`, `correo`, `contrasena`, `rol`, `cve_restaurante`) VALUES
(1, 'Diego Gutierrez Vergara', 'diego06@sigca.com', 'Administrador23', 'Admin', 1),
(2, 'DIEGO GUTIERREZ VERGARA', '22030040@itesa.edu.mx', NULL, 'Admin', NULL),
(3, 'Diana Gutierrez Vergara', 'Diana@sigca.com', '020506Diann', 'Usuario', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD PRIMARY KEY (`cve_alerta`),
  ADD KEY `cve_temp` (`cve_temp`),
  ADD KEY `cve_agua` (`cve_agua`),
  ADD KEY `cve_higiene` (`cve_higiene`),
  ADD KEY `cve_area` (`cve_area`),
  ADD KEY `cve_usuario` (`cve_usuario`);

--
-- Indices de la tabla `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`cve_area`),
  ADD KEY `cve_restaurante` (`cve_restaurante`);

--
-- Indices de la tabla `control_agua`
--
ALTER TABLE `control_agua`
  ADD PRIMARY KEY (`cve_agua`),
  ADD KEY `cve_usuario` (`cve_usuario`);

--
-- Indices de la tabla `control_higiene`
--
ALTER TABLE `control_higiene`
  ADD PRIMARY KEY (`cve_higiene`),
  ADD KEY `cve_usuario` (`cve_usuario`);

--
-- Indices de la tabla `control_temperatura`
--
ALTER TABLE `control_temperatura`
  ADD PRIMARY KEY (`cve_temp`),
  ADD KEY `id_area` (`id_area`),
  ADD KEY `cve_usuario` (`cve_usuario`);

--
-- Indices de la tabla `detalle_reporte`
--
ALTER TABLE `detalle_reporte`
  ADD PRIMARY KEY (`cve_detalle`),
  ADD KEY `cve_reporte` (`cve_reporte`),
  ADD KEY `cve_temp` (`cve_temp`),
  ADD KEY `cve_agua` (`cve_agua`),
  ADD KEY `cve_alerta` (`cve_alerta`),
  ADD KEY `cve_higiene` (`cve_higiene`),
  ADD KEY `cve_recepcion` (`cve_recepcion`);

--
-- Indices de la tabla `parametros_temperatura`
--
ALTER TABLE `parametros_temperatura`
  ADD PRIMARY KEY (`id_parametro`);

--
-- Indices de la tabla `recepcion_alimentos`
--
ALTER TABLE `recepcion_alimentos`
  ADD PRIMARY KEY (`cve_recepcion`),
  ADD KEY `cve_usuario` (`cve_usuario`);

--
-- Indices de la tabla `reporte_sanitario`
--
ALTER TABLE `reporte_sanitario`
  ADD PRIMARY KEY (`cve_reporte`),
  ADD KEY `cve_restaurante` (`cve_restaurante`),
  ADD KEY `cve_usuario` (`cve_usuario`);

--
-- Indices de la tabla `restaurante`
--
ALTER TABLE `restaurante`
  ADD PRIMARY KEY (`cve_restaurante`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`cve_usuario`),
  ADD KEY `cve_restaurante` (`cve_restaurante`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas`
--
ALTER TABLE `alertas`
  MODIFY `cve_alerta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `areas`
--
ALTER TABLE `areas`
  MODIFY `cve_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `control_agua`
--
ALTER TABLE `control_agua`
  MODIFY `cve_agua` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `control_higiene`
--
ALTER TABLE `control_higiene`
  MODIFY `cve_higiene` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `control_temperatura`
--
ALTER TABLE `control_temperatura`
  MODIFY `cve_temp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT de la tabla `detalle_reporte`
--
ALTER TABLE `detalle_reporte`
  MODIFY `cve_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `parametros_temperatura`
--
ALTER TABLE `parametros_temperatura`
  MODIFY `id_parametro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `recepcion_alimentos`
--
ALTER TABLE `recepcion_alimentos`
  MODIFY `cve_recepcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reporte_sanitario`
--
ALTER TABLE `reporte_sanitario`
  MODIFY `cve_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `restaurante`
--
ALTER TABLE `restaurante`
  MODIFY `cve_restaurante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `cve_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD CONSTRAINT `alertas_ibfk_1` FOREIGN KEY (`cve_temp`) REFERENCES `control_temperatura` (`cve_temp`),
  ADD CONSTRAINT `alertas_ibfk_2` FOREIGN KEY (`cve_agua`) REFERENCES `control_agua` (`cve_agua`),
  ADD CONSTRAINT `alertas_ibfk_3` FOREIGN KEY (`cve_higiene`) REFERENCES `control_higiene` (`cve_higiene`),
  ADD CONSTRAINT `alertas_ibfk_4` FOREIGN KEY (`cve_area`) REFERENCES `areas` (`cve_area`),
  ADD CONSTRAINT `alertas_ibfk_5` FOREIGN KEY (`cve_usuario`) REFERENCES `usuario` (`cve_usuario`);

--
-- Filtros para la tabla `areas`
--
ALTER TABLE `areas`
  ADD CONSTRAINT `areas_ibfk_1` FOREIGN KEY (`cve_restaurante`) REFERENCES `restaurante` (`cve_restaurante`);

--
-- Filtros para la tabla `control_agua`
--
ALTER TABLE `control_agua`
  ADD CONSTRAINT `control_agua_ibfk_1` FOREIGN KEY (`cve_usuario`) REFERENCES `usuario` (`cve_usuario`);

--
-- Filtros para la tabla `control_higiene`
--
ALTER TABLE `control_higiene`
  ADD CONSTRAINT `control_higiene_ibfk_1` FOREIGN KEY (`cve_usuario`) REFERENCES `usuario` (`cve_usuario`);

--
-- Filtros para la tabla `control_temperatura`
--
ALTER TABLE `control_temperatura`
  ADD CONSTRAINT `control_temperatura_ibfk_1` FOREIGN KEY (`id_area`) REFERENCES `areas` (`cve_area`),
  ADD CONSTRAINT `control_temperatura_ibfk_2` FOREIGN KEY (`cve_usuario`) REFERENCES `usuario` (`cve_usuario`);

--
-- Filtros para la tabla `detalle_reporte`
--
ALTER TABLE `detalle_reporte`
  ADD CONSTRAINT `detalle_reporte_ibfk_1` FOREIGN KEY (`cve_reporte`) REFERENCES `reporte_sanitario` (`cve_reporte`),
  ADD CONSTRAINT `detalle_reporte_ibfk_2` FOREIGN KEY (`cve_temp`) REFERENCES `control_temperatura` (`cve_temp`),
  ADD CONSTRAINT `detalle_reporte_ibfk_3` FOREIGN KEY (`cve_agua`) REFERENCES `control_agua` (`cve_agua`),
  ADD CONSTRAINT `detalle_reporte_ibfk_4` FOREIGN KEY (`cve_alerta`) REFERENCES `alertas` (`cve_alerta`),
  ADD CONSTRAINT `detalle_reporte_ibfk_5` FOREIGN KEY (`cve_higiene`) REFERENCES `control_higiene` (`cve_higiene`),
  ADD CONSTRAINT `detalle_reporte_ibfk_6` FOREIGN KEY (`cve_recepcion`) REFERENCES `recepcion_alimentos` (`cve_recepcion`);

--
-- Filtros para la tabla `recepcion_alimentos`
--
ALTER TABLE `recepcion_alimentos`
  ADD CONSTRAINT `recepcion_alimentos_ibfk_1` FOREIGN KEY (`cve_usuario`) REFERENCES `usuario` (`cve_usuario`);

--
-- Filtros para la tabla `reporte_sanitario`
--
ALTER TABLE `reporte_sanitario`
  ADD CONSTRAINT `reporte_sanitario_ibfk_1` FOREIGN KEY (`cve_restaurante`) REFERENCES `restaurante` (`cve_restaurante`),
  ADD CONSTRAINT `reporte_sanitario_ibfk_2` FOREIGN KEY (`cve_usuario`) REFERENCES `usuario` (`cve_usuario`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`cve_restaurante`) REFERENCES `restaurante` (`cve_restaurante`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
