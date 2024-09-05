-- Define los posibles estados de una pagina, si fue denunciada/no denunciada/suspendida
CREATE TABLE `paginas_estados` (
    `id_estado` int(11) NOT NULL AUTO_INCREMENT,
    `descripcion`  varchar(45) NOT NULL,
    PRIMARY KEY (`id_estado`),
    UNIQUE KEY `descripcion_UNIQUE`  (`descripcion`)
);

-- Define los posibles estados de una denuncia, si todas sus paginas fueron dadas de baja o no
CREATE TABLE `denuncia_estados` (
    `id_denuncia_estados` int(11) NOT NULL AUTO_INCREMENT,
    `descripcion`  varchar(45) NOT NULL,
    PRIMARY KEY (`id_denuncia_estados`),
    UNIQUE KEY `descripcion_UNIQUE`  (`descripcion`)
);

-- Define la tabla de denuncias, donde se registran las denuncias, el estado de la denuncia y sus timestamps
CREATE TABLE `denuncias` (
    `id_denuncia` int(11) NOT NULL AUTO_INCREMENT,
    `id_denuncia_estados` int(11) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY ( `id_denuncia_estados`) REFERENCES `denuncia_estados`(`id_denuncia_estados`),
    PRIMARY KEY (`id_denuncia`)
);

-- Define la tabla de paginas, donde se registran las paginas, el estado de la pagina y sus timestamps
CREATE TABLE `paginas` (
    `id_pagina` int(11) NOT NULL AUTO_INCREMENT,
    `usuario` varchar(45) NOT NULL,
    `pagina` varchar(45) NOT NULL,
    `pag_url` varchar(255) NOT NULL,
    `id_estado` int(11) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id_pagina`),
    FOREIGN KEY ( `id_estado`) REFERENCES `paginas_estados`(`id_estado`)
);

-- Define la tabla de paginas_denunciadas_en, donde se registra la relación entre las denuncias y las paginas
-- la relación es ManyToMany
CREATE TABLE `denuncias_paginas` (
    `id_pagina` int(11) NOT NULL,
    `id_denuncia` int(11) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id_pagina`,`id_denuncia`),
    FOREIGN KEY (`id_denuncia`) REFERENCES `denuncias`(`id_denuncia`),
    FOREIGN KEY ( `id_pagina`) REFERENCES `paginas`(`id_pagina`)
)

-- Carga de datos
INSERT INTO `paginas_estados` (`descripcion`)
VALUES ('No Denunciado'), ('Denunciado'), ('Suspendido');

INSERT INTO `denuncia_estados` (`descripcion`)
VALUES ('Pendiente'), ('Finalizado');


SET FOREIGN_KEY_CHECKS = 0;

-- Borrar las tablas principales
DROP TABLE IF EXISTS `paginas`;
DROP TABLE IF EXISTS `denuncias`;
DROP TABLE IF EXISTS `denuncia_estados`;
DROP TABLE IF EXISTS `paginas_estados`;
DROP TABLE IF EXISTS `denuncias_paginas`;
-- Reactivar las restricciones de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO `paginas` (`usuario`, `pagina`, `pag_url`, `id_estado`, `created_at`, `updated_at`) VALUES
('usuario1', 'Facebook', 'http://www.facebook.com/1', 1,NOW(), NOW()),
('usuario2', 'Facebook', 'http://www.facebook.com/2', 1, NOW(), NOW()),
('usuario3', 'Facebook', 'http://www.facebook.com/3', 1, NOW(), NOW()),
('usuario4', 'Facebook', 'http://www.facebook.com/4', 1, NOW(), NOW()),
('usuario5', 'Facebook', 'http://www.facebook.com/5', 1, NOW(), NOW()),
('usuario6', 'Facebook', 'http://www.facebook.com/6', 1, NOW(), NOW()),
('usuario7', 'Facebook', 'http://www.facebook.com/7', 1, NOW(), NOW()),
('usuario8', 'Facebook', 'http://www.facebook.com/8', 1, NOW(), NOW()),
('usuario9', 'Facebook', 'http://www.facebook.com/9', 1, NOW(), NOW()),
('usuario10', 'Facebook', 'http://www.facebook.com/10', 1, NOW(), NOW()),
('usuario11', 'Facebook', 'http://www.facebook.com/11', 1, NOW(), NOW()),
('usuario12', 'Facebook', 'http://www.facebook.com/12', 1, NOW(), NOW());

SELECT * FROM `paginas`;
SELECT * FROM `denuncias_paginas`;