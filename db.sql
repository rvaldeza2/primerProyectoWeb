CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL
);

INSERT INTO usuarios (usuario, contrasena) VALUES
('admin', '1234');

CREATE TABLE IF NOT EXISTS inscripciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_estudiante VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    curso VARCHAR(100) NOT NULL,
    fecha_inscripcion DATE NOT NULL
);

INSERT INTO inscripciones (nombre_estudiante, correo, telefono, curso, fecha_inscripcion) VALUES
('Ana López', 'ana.lopez@correo.com', '55123456', 'Programación en PHP', '2026-08-15'),
('Carlos Méndez', 'carlos.mendez@correo.com', '55234567', 'Bases de Datos', '2026-08-20'),
('Lucía Ramírez', 'lucia.ramirez@correo.com', '55345678', 'Diseño Web', '2026-09-01');
