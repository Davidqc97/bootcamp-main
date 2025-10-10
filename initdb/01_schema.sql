-- Tabla de usuarios mejorada
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  nombre_completo VARCHAR(100),
  rol VARCHAR(20) DEFAULT 'usuario',
  estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo',
  ultimo_login TIMESTAMP NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_usuario (usuario),
  INDEX idx_email (email),
  INDEX idx_estado (estado)
);

-- Tabla de mensajes mejorada
CREATE TABLE IF NOT EXISTS mensajes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  celular VARCHAR(20),
  categoria VARCHAR(50) NOT NULL,
  correo VARCHAR(120) NOT NULL,
  descripcion TEXT NOT NULL,
  estado VARCHAR(50) DEFAULT 'Nuevo',
  prioridad ENUM('baja', 'media', 'alta') DEFAULT 'media',
  respuesta TEXT,
  respondido_por INT,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (respondido_por) REFERENCES usuarios(id) ON DELETE SET NULL,
  INDEX idx_usuario_id (usuario_id),
  INDEX idx_estado (estado),
  INDEX idx_prioridad (prioridad),
  INDEX idx_creado_en (creado_en)
);