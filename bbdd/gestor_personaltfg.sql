-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 15-04-2026 a las 19:33:36
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
-- Base de datos: `gestor_personaltfg`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `calle` varchar(150) DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `piso` varchar(50) DEFAULT NULL,
  `puerta` varchar(50) DEFAULT NULL,
  `escalera` varchar(50) DEFAULT NULL,
  `codigoPostal` varchar(20) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `nombrePropietario` varchar(150) DEFAULT NULL,
  `telefonoPropietario` varchar(30) DEFAULT NULL,
  `emailPropietario` varchar(150) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaModificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `direcciones`
--

INSERT INTO `direcciones` (`id`, `nombre`, `descripcion`, `ubicacion`, `calle`, `numero`, `piso`, `puerta`, `escalera`, `codigoPostal`, `ciudad`, `provincia`, `email`, `role`, `userId`, `fechaCreacion`, `fechaModificacion`) VALUES
(1, 'Casa User', '', '', 'Valencia', '11', '', '1', '', '46160', 'Valencia', 'Llíria', 'user@example.com', 'propietario', 2, '2026-04-15 18:21:48', '2026-04-15 18:21:48'),
(2, 'Empresa', '', '', 'Carrer 8', '1', '', '1', '', '46182', 'Valencia', 'Paterna', 'tuweb@ejemplo.com', 'propietario', 5, '2026-04-15 18:21:48', '2026-04-15 18:21:48'),
(3, 'Casa Admin', '', '', 'General', '2', '', '5', '', '46183', 'Valencia', 'Eliana, l\'', 'admin@email.com', 'propietario', 3, '2026-04-15 18:21:48', '2026-04-15 18:21:48'),
(4, 'Casa Axis', '', '', 'Doctor moliner', '21', '', '', '', '46183', 'Valencia', 'Eliana, l\'', 'user@example.com', 'propietario', 2, '2026-04-15 18:38:15', '2026-04-15 18:38:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `permisos` text DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaModificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `activo`, `permisos`, `fechaCreacion`, `fechaModificacion`) VALUES
(1, 'administrador', 'Rol administrador con permisos completos', 1, NULL, '2026-04-15 18:01:32', '2026-04-15 18:01:32'),
(3, 'empleado', 'Usuario que guarda direcciones', 1, NULL, '2026-04-15 18:43:21', '2026-04-15 18:43:21'),
(5, 'gerente', 'Puede ver direcciones y ver quien lo crea', 1, '{\"view_direcciones\":true,\"view_creator\":true,\"edit_direcciones\":false,\"delete_direcciones\":false}', '2026-04-15 19:30:03', '2026-04-15 19:30:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `apellidos` varchar(150) DEFAULT NULL,
  `DNI` varchar(50) DEFAULT NULL,
  `fechaNacimiento` date DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaModificacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `apellidos`, `DNI`, `fechaNacimiento`, `telefono`, `email`, `password`, `role`, `fechaCreacion`, `fechaModificacion`) VALUES
(1, 'admin@local.test', '', '', '0000-00-00', '', 'admin@local.test', '$2y$10$A2lREMM/DnxafjKXch2tyeAU5M9Dabi2Xky5TxD3qR1OxEso6MFaK', 'administrador', '2026-04-15 18:01:32', '2026-04-15 18:30:33'),
(2, 'user', 'user2', '11112222444Q', '2026-03-12', '635145', 'user@example.com', '$2y$10$AOLybdGqxF7555IdyiKfUuBH5jEAznNok4JY5WpyGRxCf4WjeRAWW', 'empleado', '2026-04-15 18:21:48', '2026-04-15 18:43:39'),
(3, 'admin@email.com', '', '', '0000-00-00', '', 'admin@email.com', '$2y$10$IDGyE5Drb8vPqrUEMR5U5.Ur4E2DE2G8cTsVV6KYqe3FAAD.17JRW', 'administrador', '2026-04-15 18:21:48', '2026-04-15 18:30:33'),
(4, 'cliente@email.com', '', '', '0000-00-00', '', 'cliente@email.com', '$2y$10$ZkAbtirkown4XJw/epDJWOJlWg9yhGJ.R5IMWX08xOxjHpYr2doa.', 'empleado', '2026-04-15 18:21:48', '2026-04-15 18:43:30'),
(5, 'tuweb@ejemplo.com', '', '', '0000-00-00', '', 'tuweb@ejemplo.com', '$2y$10$pl4B7XIxpNng2pqr2lCHEefk4VWJrrEWzJe91dabcQQbzwXalE0Ya', 'empleado', '2026-04-15 18:21:48', '2026-04-15 18:43:37'),
(6, 'test@test.com', '', '', '0000-00-00', '', 'test@test.com', '$2y$10$bvm96YkPSqMpJudJ8URcgekBfjByxsyEyq4eV5/Yy2fDEk7qM8fb2', 'empleado', '2026-04-15 18:21:48', '2026-04-15 18:43:33'),
(7, 'gerente@email.com', '', '', '0000-00-00', '', 'gerente@email.com', '$2y$10$jmTv2hnflRnEBHMdxkPK2..ZqR36mf0uheGZm1/pGpfW0TsUa3g.2', 'gerente', '2026-04-15 19:12:56', '2026-04-15 19:12:56');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `direcciones_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
