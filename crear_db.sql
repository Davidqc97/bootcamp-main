--
-- Base de datos: `formulario_db`
--

-- 1. CREAR LA BASE DE DATOS (si no existe)
CREATE DATABASE IF NOT EXISTS `formulario_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `formulario_db`;

-- --------------------------------------------------------

--
-- ESTRUCTURA PARA LA TABLA `usuarios` (Tabla Principal)
--
CREATE TABLE `usuarios` (
  `user_idx` int(11) NOT NULL AUTO_INCREMENT,
  `sexo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idioma` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `altura_cm` int(11) DEFAULT NULL,
  `peso_kg` decimal(5,2) DEFAULT NULL,
  `training_age_anos` int(11) DEFAULT NULL,
  `dias_disp_sem` int(11) DEFAULT NULL,
  `tiempo_por_sesion_min` int(11) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- ESTRUCTURAS PARA LAS TABLAS DE CATÁLOGO (Opciones para `mh_*`)
--

CREATE TABLE `catalogo_equipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `catalogo_lesiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `catalogo_objetivos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- DATOS PARA LAS TABLAS DE CATÁLOGO (Valores iniciales)
--

INSERT INTO `catalogo_equipos` (`id`, `nombre`) VALUES
(1, 'Ninguno (Peso Corporal)'),
(2, 'Mancuernas'),
(3, 'Barra y Discos'),
(4, 'Gimnasio Completo');

INSERT INTO `catalogo_lesiones` (`id`, `nombre`) VALUES
(1, 'Ninguna'),
(2, 'Hombro'),
(3, 'Rodilla'),
(4, 'Espalda Baja');

INSERT INTO `catalogo_objetivos` (`id`, `nombre`) VALUES
(1, 'Perder Grasa'),
(2, 'Ganar Músculo'),
(3, 'Ganar Fuerza'),
(4, 'Mejorar Salud');

-- --------------------------------------------------------

--
-- ESTRUCTURAS PARA LAS TABLAS PIVOTE (Relaciones `mh_*`)
--

CREATE TABLE `usuario_equipos` (
  `id_usuario` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  PRIMARY KEY (`id_usuario`,`id_equipo`),
  KEY `id_equipo` (`id_equipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuario_lesiones` (
  `id_usuario` int(11) NOT NULL,
  `id_lesion` int(11) NOT NULL,
  PRIMARY KEY (`id_usuario`,`id_lesion`),
  KEY `id_lesion` (`id_lesion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuario_objetivos` (
  `id_usuario` int(11) NOT NULL,
  `id_objetivo` int(11) NOT NULL,
  PRIMARY KEY (`id_usuario`,`id_objetivo`),
  KEY `id_objetivo` (`id_objetivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- FKs para las tablas pivote
--
ALTER TABLE `usuario_equipos`
  ADD CONSTRAINT `usuario_equipos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`user_idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuario_equipos_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `catalogo_equipos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `usuario_lesiones`
  ADD CONSTRAINT `usuario_lesiones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`user_idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuario_lesiones_ibfk_2` FOREIGN KEY (`id_lesion`) REFERENCES `catalogo_lesiones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `usuario_objetivos`
  ADD CONSTRAINT `usuario_objetivos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`user_idx`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuario_objetivos_ibfk_2` FOREIGN KEY (`id_objetivo`) REFERENCES `catalogo_objetivos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;