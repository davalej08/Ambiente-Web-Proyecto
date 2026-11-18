-- ============================================================
-- David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
-- Ambiente Web Cliente/Servidor · SC-502
-- Universidad Fidélitas · Costa Rica
-- ============================================================
--  VENTICA · 03_datos_prueba.sql
--  Datos de prueba para recorrer la aplicación con los 3 roles.
--  Se corre después de 01_tablas.sql y 02_procedimientos.sql.
--
--  Cuentas de prueba (todas con la contraseña Ventica2026):
--    admin@ventica.cr        Administrador
--    emprendedor@ventica.cr  Emprendedor (Beneficio La Pastora)
--    cliente@ventica.cr      Cliente (Mariana Solano)
--
--  Las fechas de los pedidos se calculan desde HOY (NOW()), así los
--  reportes siempre muestran datos de los últimos días y meses.
--  Las fotos están en public/static/img/uploads/productos/.
-- ============================================================

USE ventica;

-- ---------- Usuarios (1 admin, 12 emprendedores, 9 clientes) ----------
INSERT INTO FIDE_USUARIO_TB (ID_USUARIO, CEDULA, NOMBRE, PRIMER_APELLIDO, SEGUNDO_APELLIDO, CONTRASENA_HASH, FECHA_REGISTRO, ID_ROL, ID_ESTADO) VALUES
    (1, '201371211', 'Admin', 'Ventica', NULL, '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 400 DAY), 3, 1),
    (2, '301741422', 'Minor', 'Valverde', 'Mora', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 330 DAY), 2, 1),
    (3, '402111633', 'Laura', 'Montes', 'Jiménez', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 308 DAY), 2, 1),
    (4, '502481844', 'Andrea', 'Quirós', 'Castro', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 286 DAY), 2, 1),
    (5, '602852055', 'Carlos', 'Alvarado', 'Araya', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 264 DAY), 2, 1),
    (6, '703222266', 'José', 'Rojas', 'Solís', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 242 DAY), 2, 1),
    (7, '103592477', 'Valeria', 'Mora', 'Chacón', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 220 DAY), 2, 1),
    (8, '203962688', 'Elena', 'Solís', 'Brenes', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 198 DAY), 2, 1),
    (9, '304332899', 'Kevin', 'Blandón', 'Quesada', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 176 DAY), 2, 1),
    (10, '404703110', 'Esteban', 'Brenes', 'Arias', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 154 DAY), 2, 1),
    (11, '505073321', 'Marta', 'Leiva', 'Campos', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 132 DAY), 2, 1),
    (12, '605443532', 'Mariana', 'Quesada', 'Vargas', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 110 DAY), 2, 1),
    (13, '705813743', 'Andrés', 'Cordero', 'Rojas', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 88 DAY), 2, 1),
    (14, '406183954', 'Mariana', 'Solano', 'Mora', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 260 DAY), 1, 1),
    (15, '406554165', 'Carlos', 'Umaña', 'Jiménez', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 240 DAY), 1, 1),
    (16, '306924376', 'Elena', 'Vargas', 'Castro', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 220 DAY), 1, 1),
    (17, '107294587', 'Valeria', 'Jiménez', 'Araya', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 200 DAY), 1, 1),
    (18, '107664798', 'Sofía', 'Alfaro', 'Solís', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 180 DAY), 1, 1),
    (19, '208035009', 'Esteban', 'Mora', 'Chacón', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 160 DAY), 1, 3),
    (20, '508405220', 'Kattia', 'Rodríguez', 'Brenes', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 140 DAY), 1, 1),
    (21, '608775431', 'Diego', 'Chaves', 'Quesada', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 120 DAY), 1, 1),
    (22, '709145642', 'Natalia', 'Brenes', 'Arias', '$2y$10$ZBA4dW6uGbV0HOBDDKmVVeb26R2qKvJFKu52FGJCumkPlMSZDvFmi', DATE_SUB(NOW(), INTERVAL 100 DAY), 1, 1);

-- Correo (con el que se inicia sesión) y celular de cada usuario
INSERT INTO FIDE_CORREO_TB (CORREO, ID_USUARIO) VALUES
    ('admin@ventica.cr', 1),
    ('emprendedor@ventica.cr', 2),
    ('laura@apiariomontes.cr', 3),
    ('hola@chibchacr.com', 4),
    ('taller@baulaartesanos.cr', 5),
    ('donchepe@chileras.cr', 6),
    ('hola@reciclatico.cr', 7),
    ('botica@delvalle.cr', 8),
    ('cacao@caribeno.cr', 9),
    ('contacto@mieldorada.cr', 10),
    ('colectivo@boruca.cr', 11),
    ('amigurumis@puravida.cr', 12),
    ('hola@quesosmonteverde.cr', 13),
    ('cliente@ventica.cr', 14),
    ('carlos.umana@gmail.com', 15),
    ('elena.vargas@outlook.com', 16),
    ('valeria.jimenez@gmail.com', 17),
    ('sofi.alfaro@outlook.com', 18),
    ('esteban.mora@hotmail.com', 19),
    ('kattia.rod@gmail.com', 20),
    ('diego.chaves@gmail.com', 21),
    ('nati.brenes@yahoo.com', 22);

INSERT INTO FIDE_TELEFONO_TB (NUMERO, ID_TIPO_TELEFONO, ID_USUARIO) VALUES
    ('2222-0000', 3, 1),
    ('8842-9011', 1, 2),
    ('8711-3420', 1, 3),
    ('8890-1122', 1, 4),
    ('8650-2211', 1, 5),
    ('8301-4455', 1, 6),
    ('8422-7788', 1, 7),
    ('8433-7012', 1, 8),
    ('8566-4098', 1, 9),
    ('8841-2910', 1, 10),
    ('8712-3340', 1, 11),
    ('8390-5521', 1, 12),
    ('8650-1177', 1, 13),
    ('8888-1234', 1, 14),
    ('8701-2233', 1, 15),
    ('8302-9988', 1, 16),
    ('8455-6677', 1, 17),
    ('7109-4455', 1, 18),
    ('8320-1100', 1, 19),
    ('8612-3030', 1, 20),
    ('8899-7766', 1, 21),
    ('8577-1290', 1, 22);

-- ---------- Direcciones (clientes y emprendimientos). CODIGO_POSTAL = código del distrito ----------
INSERT INTO FIDE_DIRECCION_TB (ID_DIRECCION, ID_DISTRITO, OTRAS_SENAS, CODIGO_POSTAL) VALUES
    (1, 40301, '200 m norte de la iglesia, casa verde', '40301'),
    (2, 40201, 'Frente al parque, casa 4', '40201'),
    (3, 30201, '100 m este del colegio', '30201'),
    (4, 11801, 'Condominio Las Brisas casa 12', '11801'),
    (5, 10101, 'Barrio Escalante, calle 33', '10101'),
    (6, 20113, '300 m oeste del cementerio', '20113'),
    (7, 50101, 'Barrio Los Ángeles, casa esquinera', '50101'),
    (8, 60201, '50 m sur de la plaza', '60201'),
    (9, 70201, 'Frente a la escuela', '70201'),
    (10, 11701, 'Finca La Pastora, 2 km al sur del templo', '11701'),
    (11, 20201, NULL, '20201'),
    (12, 11801, 'Taller frente al Indoor Club', '11801'),
    (13, 50201, NULL, '50201'),
    (14, 20501, NULL, '20501'),
    (15, 40101, NULL, '40101'),
    (16, 30203, NULL, '30203'),
    (17, 70401, NULL, '70401'),
    (18, 30201, NULL, '30201'),
    (19, 60301, NULL, '60301'),
    (20, 40101, NULL, '40101'),
    (21, 61201, NULL, '61201');

INSERT INTO FIDE_USUARIO_DIRECCION_TB (ID_USUARIO, ID_DIRECCION, ALIAS, ES_PRINCIPAL) VALUES
    (14, 1, 'Casa', 1),
    (15, 2, 'Casa', 1),
    (16, 3, 'Casa', 1),
    (17, 4, 'Casa', 1),
    (18, 5, 'Casa', 1),
    (19, 6, 'Casa', 1),
    (20, 7, 'Casa', 1),
    (21, 8, 'Casa', 1),
    (22, 9, 'Casa', 1);

-- ---------- Emprendimientos (8 aprobados, 3 pendientes, 1 rechazado) ----------
INSERT INTO FIDE_EMPRENDIMIENTO_TB (ID_EMPRENDIMIENTO, ID_USUARIO, NOMBRE_NEGOCIO, DESCRIPCION, LOGO_URL, ID_DIRECCION, FECHA_SOLICITUD, FECHA_VERIFICACION, OBSERVACION, ID_ESTADO) VALUES
    (1, 2, 'Beneficio La Pastora', 'Somos la familia Valverde. Por tres generaciones cultivamos micro-lotes de café de altura en Dota, cosechando a mano solo la cereza madura y tostando cada semana en pequeñas tandas.', NULL, 10, DATE_SUB(NOW(), INTERVAL 320 DAY), DATE_SUB(NOW(), INTERVAL 318 DAY), NULL, 5),
    (2, 3, 'Apiario Montes del Aguacate', 'Rescatamos colmenas en los Montes del Aguacate y cosechamos miel cruda de bosque tropical sin pasteurizar. Cada frasco cuenta la floración de la temporada.', NULL, 11, DATE_SUB(NOW(), INTERVAL 298 DAY), DATE_SUB(NOW(), INTERVAL 296 DAY), NULL, 5),
    (3, 4, 'Orfebrería Chibcha CR', 'Reinterpretamos la orfebrería precolombina en piezas de plata y bronce hechas a mano en nuestro taller de Curridabat.', NULL, 12, DATE_SUB(NOW(), INTERVAL 276 DAY), DATE_SUB(NOW(), INTERVAL 274 DAY), NULL, 5),
    (4, 5, 'Taller Baula Artesanos', 'Ebanistas de Nicoya que trabajan teca y maderas recuperadas con torno y acabados naturales. Nada de árboles nuevos: solo madera con segunda vida.', NULL, 13, DATE_SUB(NOW(), INTERVAL 254 DAY), DATE_SUB(NOW(), INTERVAL 252 DAY), NULL, 5),
    (5, 6, 'Chileras Don Chepe', 'La receta de chilera de la abuela, con maracuyá ateniense y chiles frescos de la finca. Embotellamos a mano en vidrio reutilizable.', NULL, 14, DATE_SUB(NOW(), INTERVAL 232 DAY), DATE_SUB(NOW(), INTERVAL 230 DAY), NULL, 5),
    (6, 7, 'ReciclaTico Estudio', 'Convertimos sacos de exportación de café en bolsos y accesorios. Moda circular hecha por mujeres de Heredia.', NULL, 15, DATE_SUB(NOW(), INTERVAL 210 DAY), DATE_SUB(NOW(), INTERVAL 208 DAY), NULL, 5),
    (7, 8, 'Botica Botánica del Valle', 'Jabones y bálsamos en frío con plantas que cultivamos en el valle de Orosi. Sin parabenos y sin pruebas en animales.', NULL, 16, DATE_SUB(NOW(), INTERVAL 188 DAY), DATE_SUB(NOW(), INTERVAL 186 DAY), NULL, 5),
    (8, 9, 'Cacao Caribeño Ancestral', 'Familias bribris de Talamanca que fermentan y tuestan cacao criollo como lo hicieron sus abuelos. Chocolate de origen, del árbol a la barra.', NULL, 17, DATE_SUB(NOW(), INTERVAL 166 DAY), DATE_SUB(NOW(), INTERVAL 164 DAY), NULL, 5),
    (9, 10, 'Miel Dorada de Orosi', 'Emprendimiento agroartesanal en las laderas de Orosi. Extraemos miel en frío y elaboramos derivados de colmenas de rescate.', NULL, 18, DATE_SUB(NOW(), INTERVAL 11 DAY), NULL, NULL, 4),
    (10, 11, 'Cerámica Boruca Ancestral', 'Colectivo de mujeres brunkas que trabaja el barro con pigmentos naturales y técnicas heredadas de generación en generación.', NULL, 19, DATE_SUB(NOW(), INTERVAL 12 DAY), NULL, NULL, 4),
    (11, 12, 'Amigurumis Pura Vida', 'Tejemos fauna costarricense en hilo de algodón: perezosos, tucanes y ranitas para regalar.', NULL, 20, DATE_SUB(NOW(), INTERVAL 13 DAY), NULL, 'Falta una foto del taller y la muestra de dos productos para completar el expediente.', 4),
    (12, 13, 'Quesos y Lácteos Monteverde', 'Quesería familiar que elabora quesos frescos y semimaduros con leche de fincas de la zona.', NULL, 21, DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY), 'No se adjuntó el permiso sanitario y no se puede garantizar la cadena de frío en los envíos.', 6);

INSERT INTO FIDE_EMPRENDIMIENTO_METODO_PAGO_TB (ID_EMPRENDIMIENTO, ID_METODO_PAGO) VALUES
    (1, 1),
    (1, 2),
    (2, 1),
    (3, 1),
    (3, 2),
    (4, 1),
    (4, 3),
    (5, 1),
    (6, 1),
    (6, 2),
    (7, 1),
    (7, 2),
    (7, 3),
    (8, 1),
    (9, 1),
    (9, 2),
    (10, 1),
    (11, 1),
    (12, 1);

-- Correo y teléfono propios de algunos emprendimientos, y sus redes sociales
INSERT INTO FIDE_CORREO_TB (CORREO, ID_EMPRENDIMIENTO) VALUES
    ('ventas@lapastora.cr', 1),
    ('pedidos@chibchacr.com', 3);

INSERT INTO FIDE_TELEFONO_TB (NUMERO, ID_TIPO_TELEFONO, ID_EMPRENDIMIENTO) VALUES
    ('2541-1020', 3, 1),
    ('2271-3344', 3, 3);

INSERT INTO FIDE_RED_SOCIAL_TB (ID_EMPRENDIMIENTO, PLATAFORMA, URL_PERFIL) VALUES
    (1, 'Instagram', 'https://instagram.com/beneficiolapastora'),
    (1, 'Facebook', 'https://facebook.com/beneficiolapastora'),
    (3, 'Instagram', 'https://instagram.com/chibchacr'),
    (7, 'Instagram', 'https://instagram.com/boticadelvalle'),
    (8, 'TikTok', 'https://tiktok.com/@cacaocaribeno');

-- ---------- Productos ----------
INSERT INTO FIDE_PRODUCTO_TB (ID_PRODUCTO, ID_EMPRENDIMIENTO, ID_CATEGORIA, NOMBRE_PRODUCTO, DESCRIPCION, PRECIO, CANTIDAD_DISPONIBLE, FECHA_PUBLICACION, ID_ESTADO) VALUES
    (1, 1, 8, 'Café Geisha Reserva Especial', 'Micro-lote de altura con notas a flor de jazmín y miel. Grano entero, bolsa de 340 g.', 9800, 24, DATE_SUB(NOW(), INTERVAL 300 DAY), 1),
    (2, 1, 8, 'Café Tarrazú Tueste Medio', 'Café de Dota con cuerpo balanceado y acidez cítrica. Molido para chorreador, 340 g.', 6500, 40, DATE_SUB(NOW(), INTERVAL 289 DAY), 1),
    (3, 1, 8, 'Chocolate de Altura 75%', 'Barra artesanal para acompañar el café de la tarde, 80 g.', 3800, 4, DATE_SUB(NOW(), INTERVAL 278 DAY), 1),
    (4, 1, 5, 'Tabla de Servir de Café', 'Tabla rústica en madera de cafeto podado, ideal para el café de la tarde.', 12000, 0, DATE_SUB(NOW(), INTERVAL 267 DAY), 7),
    (5, 2, 8, 'Miel Cruda de Bosque Tropical', 'Cosecha silvestre sin pasteurizar, frasco de 500 g.', 5800, 30, DATE_SUB(NOW(), INTERVAL 256 DAY), 1),
    (6, 2, 8, 'Miel de Flor de Cafeto', 'Recolectada por abejas en los cafetales florecidos, 350 g.', 5400, 18, DATE_SUB(NOW(), INTERVAL 245 DAY), 1),
    (7, 2, 3, 'Velas de Cera de Abeja', 'Dúo de velas de cera virgen con mecha de algodón e infusión de lavanda.', 5200, 12, DATE_SUB(NOW(), INTERVAL 234 DAY), 1),
    (8, 3, 6, 'Aretes Precolombinos', 'Inspirados en las ranas doradas chibchas, en plata 950. Edición limitada.', 14500, 8, DATE_SUB(NOW(), INTERVAL 223 DAY), 1),
    (9, 3, 6, 'Collar de Semillas y Plata', 'Semillas de jaboncillo curadas al sol con cierre de plata.', 8900, 10, DATE_SUB(NOW(), INTERVAL 212 DAY), 1),
    (10, 4, 5, 'Tabla de Madera Teca Noble', 'Tallada a mano en teca recuperada, con acabado de aceite mineral.', 18500, 6, DATE_SUB(NOW(), INTERVAL 201 DAY), 1),
    (11, 4, 5, 'Tabla de Picar Rústica', 'Tabla de cocina de guanacaste y teca, incluye bolsa de manta.', 12000, 9, DATE_SUB(NOW(), INTERVAL 190 DAY), 1),
    (12, 5, 8, 'Salsa Maracuyá & Habanero', 'Pulpa natural de maracuyá ateniense con chile habanero. Botella de 250 ml.', 4200, 35, DATE_SUB(NOW(), INTERVAL 179 DAY), 1),
    (13, 5, 8, 'Chilera Tradicional', 'Vegetales encurtidos con chile panameño, receta de la abuela. 500 ml.', 3500, 22, DATE_SUB(NOW(), INTERVAL 168 DAY), 1),
    (14, 6, 6, 'Tote Bag de Yute Cafetalero', 'Confeccionado con sacos reales de exportación, forro de algodón.', 16000, 7, DATE_SUB(NOW(), INTERVAL 157 DAY), 1),
    (15, 6, 6, 'Cartuchera de Yute', 'Cartuchera con zíper reciclado, ideal para lápices o maquillaje.', 7500, 15, DATE_SUB(NOW(), INTERVAL 146 DAY), 1),
    (16, 6, 7, 'Libreta con Tapa de Yute', 'Libreta A5 de papel reciclado con tapa de saco de café.', 5900, 0, DATE_SUB(NOW(), INTERVAL 135 DAY), 2),
    (17, 7, 4, 'Jabón Romero y Cera de Abeja', 'Jabón en frío con romero del valle y cera de abeja local, 100 g.', 6500, 26, DATE_SUB(NOW(), INTERVAL 124 DAY), 1),
    (18, 7, 4, 'Bálsamo de Cacao y Cera', 'Bálsamo labial con manteca de cacao, 15 g.', 2500, 3, DATE_SUB(NOW(), INTERVAL 113 DAY), 1),
    (19, 8, 8, 'Chocolate Fino 75% Bribri', 'Cacao criollo de Talamanca fermentado y tostado artesanalmente, 80 g.', 3800, 50, DATE_SUB(NOW(), INTERVAL 102 DAY), 1),
    (20, 8, 8, 'Nibs de Cacao Tostado', 'Trocitos de cacao para batidos, avena y repostería, 150 g.', 4500, 20, DATE_SUB(NOW(), INTERVAL 91 DAY), 1),
    (21, 9, 8, 'Frasco Miel Cruda de Orosi', 'Extracción en frío, rica en néctar de flores nativas de Orosi.', 6500, 20, DATE_SUB(NOW(), INTERVAL 80 DAY), 1),
    (22, 9, 3, 'Velas de Cera de Orosi', 'Cera virgen recolectada de opérculos, mecha de algodón puro.', 5200, 10, DATE_SUB(NOW(), INTERVAL 69 DAY), 1),
    (23, 10, 6, 'Aretes de Barro Boruca', 'Modelados a mano y pintados con pigmentos naturales. Cada par es único.', 7500, 12, DATE_SUB(NOW(), INTERVAL 58 DAY), 1),
    (24, 10, 5, 'Vasija Ceremonial Pequeña', 'Cocida en horno de leña, con grabados de la cosmovisión brunka.', 18500, 4, DATE_SUB(NOW(), INTERVAL 47 DAY), 1);

-- ---------- Fotos de los productos ----------
INSERT INTO FIDE_IMAGEN_PRODUCTO_TB (ID_PRODUCTO, URL_IMAGEN, ORDEN) VALUES
    (1, 'productos/cafe-geisha.jpg', 1),
    (2, 'productos/cafe-geisha.jpg', 1),
    (3, 'productos/chocolate-bribri.jpg', 1),
    (4, 'productos/tabla-teca.jpg', 1),
    (5, 'productos/miel-cruda.jpg', 1),
    (6, 'productos/miel-cruda.jpg', 1),
    (7, 'productos/miel-cruda.jpg', 1),
    (8, 'productos/aretes-precolombinos.jpg', 1),
    (9, 'productos/aretes-precolombinos.jpg', 1),
    (10, 'productos/tabla-teca.jpg', 1),
    (11, 'productos/tabla-teca.jpg', 1),
    (12, 'productos/salsa-maracuya.jpg', 1),
    (13, 'productos/salsa-maracuya.jpg', 1),
    (14, 'productos/tote-yute.jpg', 1),
    (15, 'productos/tote-yute.jpg', 1),
    (16, 'productos/tote-yute.jpg', 1),
    (17, 'productos/jabon-romero.jpg', 1),
    (18, 'productos/jabon-romero.jpg', 1),
    (19, 'productos/chocolate-bribri.jpg', 1),
    (20, 'productos/chocolate-bribri.jpg', 1),
    (21, 'productos/miel-cruda.jpg', 1),
    (22, 'productos/miel-cruda.jpg', 1),
    (23, 'productos/aretes-precolombinos.jpg', 1),
    (24, 'productos/tabla-teca.jpg', 1);

-- ---------- Pedidos (total = productos + envío de Correos de Costa Rica ₡2,400) ----------
-- Cada pedido se entrega en la dirección principal del cliente
INSERT INTO FIDE_PEDIDO_TB (ID_PEDIDO, ID_USUARIO, ID_DIRECCION, ID_METODO_PAGO, FECHA_PEDIDO, NOMBRE_RECEPTOR, TELEFONO_RECEPTOR, TOTAL, ID_ESTADO) VALUES
    (1, 14, 1, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 330 DAY), INTERVAL '19:10' HOUR_MINUTE), 'Mariana Solano Mora', '8888-1234', 29100, 10),
    (2, 14, 1, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 329 DAY), INTERVAL '8:16' HOUR_MINUTE), 'Mariana Solano Mora', '8888-1234', 15900, 10),
    (3, 21, 8, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 323 DAY), INTERVAL '19:22' HOUR_MINUTE), 'Diego Chaves Quesada', '8899-7766', 25200, 10),
    (4, 17, 4, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 322 DAY), INTERVAL '8:15' HOUR_MINUTE), 'Valeria Jiménez Araya', '8455-6677', 16200, 10),
    (5, 14, 1, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 313 DAY), INTERVAL '11:05' HOUR_MINUTE), 'Mariana Solano Mora', '8888-1234', 15000, 10),
    (6, 20, 7, 2, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 302 DAY), INTERVAL '18:01' HOUR_MINUTE), 'Kattia Rodríguez Brenes', '8612-3030', 27000, 10),
    (7, 14, 1, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 293 DAY), INTERVAL '17:15' HOUR_MINUTE), 'Mariana Solano Mora', '8888-1234', 21200, 10),
    (8, 14, 1, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 275 DAY), INTERVAL '18:43' HOUR_MINUTE), 'Mariana Solano Mora', '8888-1234', 27300, 10),
    (9, 18, 5, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 268 DAY), INTERVAL '9:22' HOUR_MINUTE), 'Sofía Alfaro Solís', '7109-4455', 24200, 10),
    (10, 17, 4, 3, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 259 DAY), INTERVAL '10:58' HOUR_MINUTE), 'Valeria Jiménez Araya', '8455-6677', 7600, 10),
    (11, 15, 2, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 258 DAY), INTERVAL '13:51' HOUR_MINUTE), 'Carlos Umaña Jiménez', '8701-2233', 63200, 10),
    (12, 17, 4, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 218 DAY), INTERVAL '11:54' HOUR_MINUTE), 'Valeria Jiménez Araya', '8455-6677', 19400, 10),
    (13, 16, 3, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 194 DAY), INTERVAL '17:24' HOUR_MINUTE), 'Elena Vargas Castro', '8302-9988', 24900, 10),
    (14, 18, 5, 2, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 186 DAY), INTERVAL '8:59' HOUR_MINUTE), 'Sofía Alfaro Solís', '7109-4455', 9900, 10),
    (15, 14, 1, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 180 DAY), INTERVAL '20:29' HOUR_MINUTE), 'Mariana Solano Mora', '8888-1234', 50300, 10),
    (16, 16, 3, 3, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 164 DAY), INTERVAL '19:23' HOUR_MINUTE), 'Elena Vargas Castro', '8302-9988', 14600, 10),
    (17, 22, 9, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 163 DAY), INTERVAL '12:09' HOUR_MINUTE), 'Natalia Brenes Arias', '8577-1290', 38700, 10),
    (18, 16, 3, 3, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 126 DAY), INTERVAL '14:24' HOUR_MINUTE), 'Elena Vargas Castro', '8302-9988', 35300, 10),
    (19, 22, 9, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 126 DAY), INTERVAL '19:32' HOUR_MINUTE), 'Natalia Brenes Arias', '8577-1290', 29300, 10),
    (20, 22, 9, 2, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 119 DAY), INTERVAL '8:37' HOUR_MINUTE), 'Natalia Brenes Arias', '8577-1290', 19900, 10),
    (21, 15, 2, 2, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 100 DAY), INTERVAL '8:01' HOUR_MINUTE), 'Carlos Umaña Jiménez', '8701-2233', 14400, 10),
    (22, 14, 1, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 100 DAY), INTERVAL '9:17' HOUR_MINUTE), 'Mariana Solano Mora', '8888-1234', 25400, 10),
    (23, 21, 8, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 88 DAY), INTERVAL '12:46' HOUR_MINUTE), 'Diego Chaves Quesada', '8899-7766', 16900, 10),
    (24, 22, 9, 3, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 86 DAY), INTERVAL '13:38' HOUR_MINUTE), 'Natalia Brenes Arias', '8577-1290', 62900, 10),
    (25, 16, 3, 3, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 86 DAY), INTERVAL '20:39' HOUR_MINUTE), 'Elena Vargas Castro', '8302-9988', 20800, 10),
    (26, 18, 5, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 55 DAY), INTERVAL '16:25' HOUR_MINUTE), 'Sofía Alfaro Solís', '7109-4455', 16500, 10),
    (27, 22, 9, 2, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 46 DAY), INTERVAL '7:38' HOUR_MINUTE), 'Natalia Brenes Arias', '8577-1290', 30900, 10),
    (28, 15, 2, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 39 DAY), INTERVAL '7:03' HOUR_MINUTE), 'Carlos Umaña Jiménez', '8701-2233', 13700, 10),
    (29, 14, 1, 2, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 35 DAY), INTERVAL '12:51' HOUR_MINUTE), 'Mariana Solano Mora', '8888-1234', 24800, 10),
    (30, 21, 8, 3, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 32 DAY), INTERVAL '19:41' HOUR_MINUTE), 'Diego Chaves Quesada', '8899-7766', 8200, 10),
    (31, 16, 3, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 25 DAY), INTERVAL '8:53' HOUR_MINUTE), 'Elena Vargas Castro', '8302-9988', 14400, 10),
    (32, 16, 3, 2, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 20 DAY), INTERVAL '14:19' HOUR_MINUTE), 'Elena Vargas Castro', '8302-9988', 54600, 10),
    (33, 17, 4, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 19 DAY), INTERVAL '8:30' HOUR_MINUTE), 'Valeria Jiménez Araya', '8455-6677', 35600, 10),
    (34, 15, 2, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 16 DAY), INTERVAL '19:13' HOUR_MINUTE), 'Carlos Umaña Jiménez', '8701-2233', 27300, 10),
    (35, 18, 5, 2, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 5 DAY), INTERVAL '9:15' HOUR_MINUTE), 'Sofía Alfaro Solís', '7109-4455', 10000, 8),
    (36, 14, 1, 1, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 3 DAY), INTERVAL '12:18' HOUR_MINUTE), 'Mariana Solano Mora', '8888-1234', 25800, 8),
    (37, 15, 2, 2, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 2 DAY), INTERVAL '19:58' HOUR_MINUTE), 'Carlos Umaña Jiménez', '8701-2233', 47700, 8),
    (38, 20, 7, 3, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL '18:11' HOUR_MINUTE), 'Kattia Rodríguez Brenes', '8612-3030', 37200, 4),
    (39, 21, 8, 2, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 0 DAY), INTERVAL '12:15' HOUR_MINUTE), 'Diego Chaves Quesada', '8899-7766', 31800, 8),
    (40, 20, 7, 2, DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 0 DAY), INTERVAL '19:26' HOUR_MINUTE), 'Kattia Rodríguez Brenes', '8612-3030', 27700, 8);

INSERT INTO FIDE_DETALLE_PEDIDO_TB (ID_PEDIDO, ID_PRODUCTO, CANTIDAD, PRECIO_UNITARIO, SUBTOTAL, ID_ESTADO) VALUES
    (1, 9, 3, 8900, 26700, 10),
    (2, 20, 3, 4500, 13500, 10),
    (3, 18, 1, 2500, 2500, 10),
    (3, 5, 1, 5800, 5800, 10),
    (3, 8, 1, 14500, 14500, 10),
    (4, 15, 1, 7500, 7500, 10),
    (4, 18, 1, 2500, 2500, 10),
    (4, 3, 1, 3800, 3800, 10),
    (5, 12, 3, 4200, 12600, 10),
    (6, 1, 2, 9800, 19600, 10),
    (6, 18, 2, 2500, 5000, 10),
    (7, 15, 2, 7500, 15000, 10),
    (7, 3, 1, 3800, 3800, 10),
    (8, 17, 3, 6500, 19500, 10),
    (8, 6, 1, 5400, 5400, 10),
    (9, 11, 1, 12000, 12000, 6),
    (9, 1, 1, 9800, 9800, 10),
    (10, 7, 1, 5200, 5200, 10),
    (11, 7, 3, 5200, 15600, 10),
    (11, 10, 1, 18500, 18500, 10),
    (11, 9, 3, 8900, 26700, 10),
    (12, 6, 1, 5400, 5400, 10),
    (12, 5, 2, 5800, 11600, 10),
    (13, 17, 1, 6500, 6500, 10),
    (13, 14, 1, 16000, 16000, 10),
    (14, 18, 3, 2500, 7500, 10),
    (15, 10, 1, 18500, 18500, 10),
    (15, 1, 3, 9800, 29400, 10),
    (16, 3, 1, 3800, 3800, 10),
    (16, 12, 2, 4200, 8400, 6),
    (17, 9, 2, 8900, 17800, 6),
    (17, 10, 1, 18500, 18500, 6),
    (18, 7, 2, 5200, 10400, 10),
    (18, 15, 3, 7500, 22500, 6),
    (19, 10, 1, 18500, 18500, 10),
    (19, 12, 2, 4200, 8400, 10),
    (20, 15, 2, 7500, 15000, 10),
    (20, 18, 1, 2500, 2500, 10),
    (21, 11, 1, 12000, 12000, 10),
    (22, 10, 1, 18500, 18500, 10),
    (22, 20, 1, 4500, 4500, 10),
    (23, 8, 1, 14500, 14500, 10),
    (24, 10, 2, 18500, 37000, 10),
    (24, 2, 2, 6500, 13000, 10),
    (24, 13, 3, 3500, 10500, 6),
    (25, 5, 1, 5800, 5800, 10),
    (25, 12, 3, 4200, 12600, 10),
    (26, 17, 1, 6500, 6500, 10),
    (26, 3, 2, 3800, 7600, 6),
    (27, 8, 1, 14500, 14500, 10),
    (27, 12, 1, 4200, 4200, 10),
    (27, 1, 1, 9800, 9800, 10),
    (28, 19, 1, 3800, 3800, 10),
    (28, 15, 1, 7500, 7500, 10),
    (29, 12, 3, 4200, 12600, 10),
    (29, 1, 1, 9800, 9800, 10),
    (30, 5, 1, 5800, 5800, 10),
    (31, 11, 1, 12000, 12000, 10),
    (32, 12, 1, 4200, 4200, 10),
    (32, 14, 3, 16000, 48000, 10),
    (33, 12, 1, 4200, 4200, 10),
    (33, 8, 2, 14500, 29000, 10),
    (34, 7, 2, 5200, 10400, 10),
    (34, 8, 1, 14500, 14500, 10),
    (35, 19, 2, 3800, 7600, 9),
    (36, 1, 2, 9800, 19600, 8),
    (36, 3, 1, 3800, 3800, 4),
    (37, 13, 1, 3500, 3500, 8),
    (37, 1, 1, 9800, 9800, 4),
    (37, 14, 2, 16000, 32000, 8),
    (38, 8, 2, 14500, 29000, 4),
    (38, 5, 1, 5800, 5800, 4),
    (39, 1, 3, 9800, 29400, 8),
    (40, 5, 1, 5800, 5800, 8),
    (40, 17, 3, 6500, 19500, 4);

-- ---------- Reseñas (1 a 5 estrellas) ----------
INSERT INTO FIDE_RESENA_TB (ID_USUARIO, ID_PRODUCTO, CALIFICACION, COMENTARIO, FECHA_RESENA) VALUES
    (21, 18, 5, 'Calidad increíble, mejor que lo que venden en el súper y apoyando a una familia tica.', DATE_SUB(NOW(), INTERVAL 319 DAY)),
    (21, 5, 5, 'Simplemente extraordinario. Apenas abrí el paquete toda la cocina olió a miel y flores. Pagué por SINPE en 10 segundos.', DATE_SUB(NOW(), INTERVAL 319 DAY)),
    (17, 18, 5, 'Simplemente extraordinario. Apenas abrí el paquete toda la cocina olió a miel y flores. Pagué por SINPE en 10 segundos.', DATE_SUB(NOW(), INTERVAL 318 DAY)),
    (17, 3, 5, 'Llegó rapidísimo por Correos y súper bien empacado. Ya pedí otra vez.', DATE_SUB(NOW(), INTERVAL 318 DAY)),
    (14, 15, 5, 'Me encantó la nota escrita a mano. Se nota el cariño en cada detalle. Orgullo tico.', DATE_SUB(NOW(), INTERVAL 289 DAY)),
    (14, 17, 5, 'Llegó rapidísimo por Correos y súper bien empacado. Ya pedí otra vez.', DATE_SUB(NOW(), INTERVAL 271 DAY)),
    (14, 6, 5, 'Me encantó la nota escrita a mano. Se nota el cariño en cada detalle. Orgullo tico.', DATE_SUB(NOW(), INTERVAL 271 DAY)),
    (18, 1, 5, 'Llegó rapidísimo por Correos y súper bien empacado. Ya pedí otra vez.', DATE_SUB(NOW(), INTERVAL 264 DAY)),
    (17, 7, 5, 'Llegó rapidísimo por Correos y súper bien empacado. Ya pedí otra vez.', DATE_SUB(NOW(), INTERVAL 255 DAY)),
    (15, 7, 4, 'Bonito acabado y buen precio. Le daría 5 si viniera con bolsa de regalo.', DATE_SUB(NOW(), INTERVAL 254 DAY)),
    (15, 10, 5, 'Llegó rapidísimo por Correos y súper bien empacado. Ya pedí otra vez.', DATE_SUB(NOW(), INTERVAL 254 DAY)),
    (15, 9, 5, 'Me encantó la nota escrita a mano. Se nota el cariño en cada detalle. Orgullo tico.', DATE_SUB(NOW(), INTERVAL 254 DAY)),
    (17, 5, 4, 'Bonito acabado y buen precio. Le daría 5 si viniera con bolsa de regalo.', DATE_SUB(NOW(), INTERVAL 214 DAY)),
    (16, 17, 5, 'Me encantó la nota escrita a mano. Se nota el cariño en cada detalle. Orgullo tico.', DATE_SUB(NOW(), INTERVAL 190 DAY)),
    (14, 10, 5, 'Calidad increíble, mejor que lo que venden en el súper y apoyando a una familia tica.', DATE_SUB(NOW(), INTERVAL 176 DAY)),
    (14, 1, 5, 'Calidad increíble, mejor que lo que venden en el súper y apoyando a una familia tica.', DATE_SUB(NOW(), INTERVAL 176 DAY)),
    (16, 7, 5, 'Calidad increíble, mejor que lo que venden en el súper y apoyando a una familia tica.', DATE_SUB(NOW(), INTERVAL 122 DAY)),
    (22, 10, 4, 'Bonito acabado y buen precio. Le daría 5 si viniera con bolsa de regalo.', DATE_SUB(NOW(), INTERVAL 122 DAY)),
    (22, 12, 5, 'Me encantó la nota escrita a mano. Se nota el cariño en cada detalle. Orgullo tico.', DATE_SUB(NOW(), INTERVAL 122 DAY)),
    (22, 18, 5, 'Llegó rapidísimo por Correos y súper bien empacado. Ya pedí otra vez.', DATE_SUB(NOW(), INTERVAL 115 DAY)),
    (15, 11, 4, 'Bonito acabado y buen precio. Le daría 5 si viniera con bolsa de regalo.', DATE_SUB(NOW(), INTERVAL 96 DAY)),
    (16, 12, 5, 'Me encantó la nota escrita a mano. Se nota el cariño en cada detalle. Orgullo tico.', DATE_SUB(NOW(), INTERVAL 82 DAY)),
    (22, 8, 5, 'Llegó rapidísimo por Correos y súper bien empacado. Ya pedí otra vez.', DATE_SUB(NOW(), INTERVAL 42 DAY)),
    (22, 1, 5, 'Simplemente extraordinario. Apenas abrí el paquete toda la cocina olió a miel y flores. Pagué por SINPE en 10 segundos.', DATE_SUB(NOW(), INTERVAL 42 DAY)),
    (15, 19, 5, 'Me encantó la nota escrita a mano. Se nota el cariño en cada detalle. Orgullo tico.', DATE_SUB(NOW(), INTERVAL 35 DAY)),
    (14, 12, 5, 'Calidad increíble, mejor que lo que venden en el súper y apoyando a una familia tica.', DATE_SUB(NOW(), INTERVAL 31 DAY)),
    (16, 14, 5, 'Simplemente extraordinario. Apenas abrí el paquete toda la cocina olió a miel y flores. Pagué por SINPE en 10 segundos.', DATE_SUB(NOW(), INTERVAL 16 DAY)),
    (17, 12, 4, 'Muy bueno, solo que tardó un día más de lo esperado. Lo recomiendo.', DATE_SUB(NOW(), INTERVAL 15 DAY)),
    (15, 8, 5, 'Simplemente extraordinario. Apenas abrí el paquete toda la cocina olió a miel y flores. Pagué por SINPE en 10 segundos.', DATE_SUB(NOW(), INTERVAL 12 DAY)),
    (15, 1, 5, 'Me encantó la nota escrita a mano. Se nota el cariño en cada detalle. Orgullo tico.', DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (16, 10, 4, 'Bonito acabado y buen precio. Le daría 5 si viniera con bolsa de regalo.', DATE_SUB(NOW(), INTERVAL 7 DAY));

-- ---------- Favoritos de la clienta de prueba ----------
INSERT INTO FIDE_FAVORITO_TB (ID_USUARIO, ID_PRODUCTO) VALUES
    (14, 1),
    (14, 8),
    (14, 17);

-- ---------- Carrito guardado de otro cliente (Carlos Umaña) ----------
INSERT INTO FIDE_CARRITO_TB (ID_CARRITO, ID_USUARIO) VALUES
    (1, 15);

INSERT INTO FIDE_CARRITO_DETALLE_TB (ID_CARRITO, ID_PRODUCTO, CANTIDAD) VALUES
    (1, 5, 2),
    (1, 19, 1);

