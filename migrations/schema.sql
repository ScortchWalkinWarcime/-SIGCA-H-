-- Schema dump for SIGCA application
-- Run in MySQL (phpMyAdmin or mysql client)

CREATE DATABASE IF NOT EXISTS `restaurante_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `restaurante_db`;

-- Restaurantes
CREATE TABLE IF NOT EXISTS restaurantes (
  cve_restaurante INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  direccion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
  cve_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  correo VARCHAR(255) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  rol VARCHAR(50) DEFAULT 'user',
  cve_restaurante INT,
  FOREIGN KEY (cve_restaurante) REFERENCES restaurantes(cve_restaurante) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Areas
CREATE TABLE IF NOT EXISTS areas (
  cve_area INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  cve_restaurante INT,
  FOREIGN KEY (cve_restaurante) REFERENCES restaurantes(cve_restaurante) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Control Agua
CREATE TABLE IF NOT EXISTS control_agua (
  cve_agua INT AUTO_INCREMENT PRIMARY KEY,
  area INT,
  ph DECIMAL(6,2),
  cloro DECIMAL(8,3),
  observaciones TEXT,
  cve_usuario INT,
  potabilidad VARCHAR(50),
  temperatura DECIMAL(6,2),
  turbidez VARCHAR(50),
  dureza VARCHAR(50),
  metales_pesados TEXT,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (area) REFERENCES areas(cve_area),
  FOREIGN KEY (cve_usuario) REFERENCES usuarios(cve_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Control Higiene
CREATE TABLE IF NOT EXISTS control_higiene (
  cve_higiene INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  manos_limpias VARCHAR(50),
  uniforme VARCHAR(50),
  cofia VARCHAR(50),
  sinjoyeria VARCHAR(50),
  incumplimiento TINYINT(1) DEFAULT 0,
  cve_usuario INT,
  guantes VARCHAR(50),
  cubrebocas VARCHAR(50),
  observaciones TEXT,
  FOREIGN KEY (cve_usuario) REFERENCES usuarios(cve_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Control Temperatura
CREATE TABLE IF NOT EXISTS control_temperatura (
  cve_temp INT AUTO_INCREMENT PRIMARY KEY,
  id_area INT,
  valor DECIMAL(6,2),
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  cve_usuario INT,
  FOREIGN KEY (id_area) REFERENCES areas(cve_area),
  FOREIGN KEY (cve_usuario) REFERENCES usuarios(cve_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Recepción de Alimentos
CREATE TABLE IF NOT EXISTS recepcion_alimentos (
  cve_recepcion INT AUTO_INCREMENT PRIMARY KEY,
  producto VARCHAR(255) NOT NULL,
  proveedor VARCHAR(255),
  estado VARCHAR(50),
  temperatura DECIMAL(6,2),
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  cve_usuario INT,
  observaciones TEXT,
  resultado VARCHAR(50),
  FOREIGN KEY (cve_usuario) REFERENCES usuarios(cve_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Alertas
CREATE TABLE IF NOT EXISTS alertas (
  cve_alerta INT AUTO_INCREMENT PRIMARY KEY,
  tipo VARCHAR(100),
  mensaje TEXT,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  cve_temp INT NULL,
  cve_agua INT NULL,
  cve_higiene INT NULL,
  cve_area INT NULL,
  cve_usuario INT NULL,
  FOREIGN KEY (cve_temp) REFERENCES control_temperatura(cve_temp),
  FOREIGN KEY (cve_agua) REFERENCES control_agua(cve_agua),
  FOREIGN KEY (cve_higiene) REFERENCES control_higiene(cve_higiene),
  FOREIGN KEY (cve_area) REFERENCES areas(cve_area),
  FOREIGN KEY (cve_usuario) REFERENCES usuarios(cve_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Detalle reporte
CREATE TABLE IF NOT EXISTS detalle_reporte (
  cve_detalle INT AUTO_INCREMENT PRIMARY KEY,
  cve_reporte INT,
  fecha_inicio DATETIME,
  fecha_final DATETIME,
  cve_temp INT,
  cve_agua INT,
  cve_alerta INT,
  cve_higiene INT,
  cve_recepcion INT,
  FOREIGN KEY (cve_temp) REFERENCES control_temperatura(cve_temp),
  FOREIGN KEY (cve_agua) REFERENCES control_agua(cve_agua),
  FOREIGN KEY (cve_alerta) REFERENCES alertas(cve_alerta),
  FOREIGN KEY (cve_higiene) REFERENCES control_higiene(cve_higiene),
  FOREIGN KEY (cve_recepcion) REFERENCES recepcion_alimentos(cve_recepcion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reporte sanitario
CREATE TABLE IF NOT EXISTS reporte_sanitario (
  cve_reporte INT AUTO_INCREMENT PRIMARY KEY,
  cve_restaurante INT,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  estado_general VARCHAR(255),
  cve_usuario INT,
  FOREIGN KEY (cve_restaurante) REFERENCES restaurantes(cve_restaurante),
  FOREIGN KEY (cve_usuario) REFERENCES usuarios(cve_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Parametros de temperatura (opcional)
CREATE TABLE IF NOT EXISTS parametros_temperatura (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_area VARCHAR(255),
  min_valor DECIMAL(6,2),
  max_valor DECIMAL(6,2),
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
