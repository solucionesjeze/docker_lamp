CREATE TABLE IF NOT EXISTS mensajes ( 
  id INT AUTO_INCREMENT PRIMARY KEY, 
  texto VARCHAR(255) NOT NULL 
); 
 
INSERT INTO mensajes (texto) VALUES ('Hola desde MySQL + Docker!'); 
