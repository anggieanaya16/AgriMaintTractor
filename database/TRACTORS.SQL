-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS mantenimiento_tractores;

-- Usar la base de datos
USE mantenimiento_tractores;

-- Tabla de roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Tabla de tractores
CREATE TABLE tractores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_proximo_mantenimiento DATE,
    dias_mantenimiento INT DEFAULT 30,
    estado VARCHAR(50) DEFAULT 'Activo'
);

-- Tabla de asignaciones de tractores a operarios
CREATE TABLE asignaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tractor_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_asignacion DATE,
    FOREIGN KEY (tractor_id) REFERENCES tractores(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de reportes de mantenimiento
CREATE TABLE reportes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tractor_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha DATE NOT NULL,
    observaciones TEXT,
    archivo_reporte VARCHAR(255),
    FOREIGN KEY (tractor_id) REFERENCES tractores(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de registro de correos enviados
CREATE TABLE emails_enviados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tractor_id INT NOT NULL,
    fecha_envio DATE NOT NULL,
    UNIQUE KEY tractor_fecha (tractor_id, fecha_envio)
);

-- Tabla para reseteo de contraseñas
CREATE TABLE password_resets (
    user_id INT PRIMARY KEY,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Insertar roles iniciales
INSERT INTO roles (nombre) VALUES ('Administrador'), ('Operario');

-- Insertar usuario administrador inicial (contraseña encriptada con bcrypt)
INSERT INTO usuarios (nombre, correo, contraseña, rol_id)
VALUES ('Administrador Principal', 'admin@gmail.com', '$2y$10$P2FLpKQEpVmK/oOo16Bu6uL5XmW9BbS/EmrT.VAhhNE59PeDblc1W', 1);
