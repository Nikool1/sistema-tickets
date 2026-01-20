
"CREACION TABLAS"
CREATE TABLE roles (
  id_rol INT AUTO_INCREMENT PRIMARY KEY,
  nombre_rol VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE usuarios (
  id_usuario INT NOT NULL AUTO_INCREMENT,
  nombre_usuario VARCHAR(50) NOT NULL,
  rut VARCHAR(12) NOT NULL,
  correo_institucional VARCHAR(50) NOT NULL,
  password VARCHAR(100) NOT NULL,
  activo TINYINT(1) NOT NULL,
  fecha_creacion DATE NOT NULL,
  id_rol INT NOT NULL,
  PRIMARY KEY (id_usuario),
  UNIQUE (rut),
  UNIQUE (correo_institucional),
  CONSTRAINT fk_usuarios_roles
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

CREATE TABLE categorias (
  id_categoria INT NOT NULL AUTO_INCREMENT,
  nombre_categoria VARCHAR(50) NOT NULL,
  descripcion VARCHAR(150) NOT NULL,
  PRIMARY KEY (id_categoria),
  UNIQUE (nombre_categoria)
);

CREATE TABLE estados (
  id_estado INT NOT NULL AUTO_INCREMENT,
  nombre_estado VARCHAR(30) NOT NULL,
  PRIMARY KEY (id_estado),
  UNIQUE (nombre_estado)
);

CREATE TABLE tickets (
  id_ticket INT NOT NULL AUTO_INCREMENT,
  descripcion TEXT NOT NULL,
  prioridad VARCHAR(10) NOT NULL,
  fecha_creacion DATE NOT NULL,
  fecha_actualizacion DATE NOT NULL,
  id_usuario INT NOT NULL,
  id_categoria INT NOT NULL,
  id_usuario2 INT NULL,
  id_estado INT NOT NULL,
  PRIMARY KEY (id_ticket),
  CONSTRAINT fk_tickets_usuario_creador
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
  CONSTRAINT fk_tickets_categoria
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria),
  CONSTRAINT fk_tickets_usuario_tecnico
    FOREIGN KEY (id_usuario2) REFERENCES usuarios(id_usuario),
  CONSTRAINT fk_tickets_estado
    FOREIGN KEY (id_estado) REFERENCES estados(id_estado)
);

CREATE TABLE bitacora_ticket (
  id_bitacora INT NOT NULL AUTO_INCREMENT,
  accion VARCHAR(50) NOT NULL,
  detalle TEXT NOT NULL,
  fecha_evento DATE NOT NULL,
  id_ticket INT NOT NULL,
  id_usuario INT NOT NULL,
  PRIMARY KEY (id_bitacora),
  CONSTRAINT fk_bitacora_ticket
    FOREIGN KEY (id_ticket) REFERENCES tickets(id_ticket),
  CONSTRAINT fk_bitacora_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);


"INSERTAR DATOS PREDEFINIDOS"
INSERT INTO roles (nombre_rol) VALUES
('Funcionario'),
('Tecnico'),
('Administrador');

INSERT INTO estados (nombre_estado) VALUES
('Enviado'),
('Aceptado'),
('En Proceso'),
('Resuelto'),
('Cerrado');

INSERT INTO categorias (nombre_categoria, `descripcion`) VALUES
('Credenciales', 'Accesos y contraseñas'),
('Soporte Técnico', 'Problemas de hardware/software'),
('Impresoras', 'Problemas de impresión'),
('Red', 'Conectividad y redes'),
('Sistemas Internos', 'Aplicaciones internas');


INSERT INTO usuarios (nombre_usuario, rut, correo_institucional, password, activo, fecha_creacion, id_rol)
VALUES ('Admin','11111111-3','admin@demo.cl','1234',1,CURDATE(),3);

INSERT INTO usuarios (nombre_usuario, rut, correo_institucional, password, activo, fecha_creacion, id_rol)
VALUES ('Tecnico','11111111-2','tecnico@demo.cl','1234',1,CURDATE(),2);

INSERT INTO usuarios (nombre_usuario, rut, correo_institucional, password, activo, fecha_creacion, id_rol)
VALUES ('Funcionario','11111111-1','funcionario@demo.cl','1234',1,CURDATE(),1);

