-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-01-2018 a las 19:46:39
-- Versión del servidor: 5.7.14
-- Versión de PHP: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `login`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrera`
--

CREATE TABLE `carrera` (
  `cod_car` int(20) UNSIGNED NOT NULL,
  `nom_car` varchar(120) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `carrera`
--

INSERT INTO `carrera` (`cod_car`, `nom_car`) VALUES
(1, 'sistemas'),
(2, 'electronica'),
(12, 'modificado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidad`
--

CREATE TABLE `especialidad` (
  `cod_esp` bigint(20) UNSIGNED NOT NULL,
  `nom_esp` varchar(120) NOT NULL,
  `cod_car` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `especialidad`
--

INSERT INTO `especialidad` (`cod_esp`, `nom_esp`, `cod_car`) VALUES
(1, 'ti', 1),
(2, 'in sw', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE `menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `etiqueta` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `ruta` varchar(150) CHARACTER SET utf16 COLLATE utf16_spanish2_ci NOT NULL,
  `nivel` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`id`, `etiqueta`, `ruta`, `nivel`) VALUES
(1, '¿Quienes Somos?', 'pages/quienes_somos.php', 0),
(2, 'Productos', 'pages/productos.php', 0),
(3, 'Contáctenos', 'pages/contacto.php', 0),
(4, 'Facturación', 'pages/facturacion.php', 1),
(5, 'Clientes', 'pages/clientes.php', 1),
(6, 'Reportes', 'pages/reportes.php', 2),
(7, 'Proveedores', 'pages/proveedores.php', 3),
(8, 'Inventario', 'pages/inventario.php', 3),
(9, 'Usuarios', 'pages/usuarios.php', 4),
(10, 'Configuraciones', 'pages/config.php', 4),
(11, 'Académico', 'pages/academico/', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL COMMENT 'nombre completo',
  `username` varchar(15) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `password` varchar(64) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `nivel` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `username`, `password`, `nivel`) VALUES
(1, 'Administrador', 'admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 3),
(2, 'Usuario', 'user', 'd33931038d2904e5309afd65655801a020aa8e94', 0),
(3, 'Cajero', 'cashier', 'd33931038d2904e5309afd65655801a020aa8e94', 1),
(4, 'Supervisor', 'super', 'd33931038d2904e5309afd65655801a020aa8e94', 2),
(5, 'Super Administrador', 'sadmin', 'd33931038d2904e5309afd65655801a020aa8e94', 4);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrera`
--
ALTER TABLE `carrera`
  ADD PRIMARY KEY (`cod_car`),
  ADD UNIQUE KEY `cod_car` (`cod_car`);

--
-- Indices de la tabla `especialidad`
--
ALTER TABLE `especialidad`
  ADD PRIMARY KEY (`cod_esp`),
  ADD UNIQUE KEY `cod_esp` (`cod_esp`),
  ADD KEY `cod_car` (`cod_car`);

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
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
-- AUTO_INCREMENT de la tabla `carrera`
--
ALTER TABLE `carrera`
  MODIFY `cod_car` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT de la tabla `especialidad`
--
ALTER TABLE `especialidad`
  MODIFY `cod_esp` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `especialidad`
--
ALTER TABLE `especialidad`
  ADD CONSTRAINT `fk_carrera` FOREIGN KEY (`cod_car`) REFERENCES `carrera` (`cod_car`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
