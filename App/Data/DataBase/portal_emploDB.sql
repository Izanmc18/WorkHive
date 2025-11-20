-- 1. Borramos la base de datos antigua si existe para evitar conflictos
DROP DATABASE IF EXISTS portal_empleo;

-- 2. Creamos la base de datos nueva
CREATE DATABASE portal_empleo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE portal_empleo;

-- --------------------------------------------------------
-- ESTRUCTURA DE TABLAS
-- --------------------------------------------------------

-- Tabla de usuarios base
CREATE TABLE usuarios (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  correo VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL, -- Ahora guardará el Hash ($2y$10$...)
  fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  es_admin BOOLEAN NOT NULL DEFAULT FALSE,
  verificado BOOLEAN NOT NULL DEFAULT FALSE
);

-- Tabla familias profesionales
CREATE TABLE familias (
  id_familia INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE
);

-- Ciclos formativos
CREATE TABLE ciclos (
  id_ciclo INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  tipo ENUM('basico','medio','superior','especializacion') NOT NULL,
  id_familia INT NOT NULL,
  FOREIGN KEY (id_familia) REFERENCES familias(id_familia) ON DELETE CASCADE
);

-- Tabla alumnos
CREATE TABLE alumnos (
  id_alumno INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL UNIQUE,
  nombre VARCHAR(100) NOT NULL,
  apellido1 VARCHAR(100),
  apellido2 VARCHAR(100),
  direccion VARCHAR(250),
  edad INT,
  curriculum_url VARCHAR(255),
  foto_perfil VARCHAR(255),
  FOREIGN KEY (id_user) REFERENCES usuarios(id_user) ON DELETE CASCADE
);

-- Tabla empresas
CREATE TABLE empresas (
  id_empresa INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL UNIQUE,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  logo_url VARCHAR(255),
  direccion VARCHAR(250),
  validacion BOOLEAN NOT NULL DEFAULT 0,
  FOREIGN KEY (id_user) REFERENCES usuarios(id_user) ON DELETE CASCADE
);

-- Relación Alumnos - Ciclos
CREATE TABLE alumno_ciclo (
  id_alumno INT NOT NULL,
  id_ciclo INT NOT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE DEFAULT NULL,
  PRIMARY KEY (id_alumno, id_ciclo, fecha_inicio),
  FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno) ON DELETE CASCADE,
  FOREIGN KEY (id_ciclo) REFERENCES ciclos(id_ciclo) ON DELETE CASCADE
);

-- Ofertas
CREATE TABLE ofertas (
  id_oferta INT AUTO_INCREMENT PRIMARY KEY,
  id_empresa INT NOT NULL,
  descripcion TEXT NOT NULL,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE DEFAULT NULL,
  activa BOOLEAN NOT NULL DEFAULT TRUE,
  titulo VARCHAR(150) NOT NULL,
  FOREIGN KEY (id_empresa) REFERENCES empresas(id_empresa) ON DELETE CASCADE
);

-- Relación Ofertas - Ciclos
CREATE TABLE oferta_ciclo (
  id_oferta INT NOT NULL,
  id_ciclo INT NOT NULL,
  PRIMARY KEY (id_oferta, id_ciclo),
  FOREIGN KEY (id_oferta) REFERENCES ofertas(id_oferta) ON DELETE CASCADE,
  FOREIGN KEY (id_ciclo) REFERENCES ciclos(id_ciclo) ON DELETE CASCADE
);

-- Solicitudes
CREATE TABLE solicitudes (
  id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
  id_oferta INT NOT NULL,
  id_alumno INT NOT NULL,
  fecha_solicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  estado ENUM('pendiente','aceptada','rechazada') DEFAULT 'pendiente',
  comentario TEXT,
  FOREIGN KEY (id_oferta) REFERENCES ofertas(id_oferta) ON DELETE CASCADE,
  FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno) ON DELETE CASCADE
);

-- Tokens de seguridad
CREATE TABLE tokens (
  id_token INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_expiracion DATETIME DEFAULT NULL,
  FOREIGN KEY (id_user) REFERENCES usuarios(id_user) ON DELETE CASCADE
);

-- Recuperación de contraseñas
CREATE TABLE recuperacion_password (
  id_recuperacion INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  id_token INT,
  fecha_solicitud DATETIME NOT NULL,
  fecha_uso DATETIME DEFAULT NULL,
  FOREIGN KEY (id_user) REFERENCES usuarios(id_user) ON DELETE CASCADE,
  FOREIGN KEY (id_token) REFERENCES tokens(id_token)
);

-- Histórico de contraseñas
CREATE TABLE historico_passwords (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  password_antigua VARCHAR(255) NOT NULL,
  fecha_cambio DATETIME NOT NULL,
  FOREIGN KEY (id_user) REFERENCES usuarios(id_user) ON DELETE CASCADE
);