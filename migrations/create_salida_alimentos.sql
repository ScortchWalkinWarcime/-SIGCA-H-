-- Migration: create table salida_alimentos
CREATE TABLE IF NOT EXISTS salida_alimentos (
  cve_salida INT NOT NULL AUTO_INCREMENT,
  producto VARCHAR(255) NOT NULL,
  estado VARCHAR(100) DEFAULT NULL,
  cve_usuario INT DEFAULT NULL,
  temperatura VARCHAR(50) DEFAULT NULL,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  proveedor VARCHAR(255) DEFAULT NULL,
  observaciones TEXT,
  resultado VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (cve_salida)
);

-- Optionally add a foreign key to usuario if your schema has it:
-- ALTER TABLE salida_alimentos ADD CONSTRAINT fk_salida_usuario FOREIGN KEY (cve_usuario) REFERENCES usuario(cve_usuario);
