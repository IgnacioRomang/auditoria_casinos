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
    `id_denuncia` int(11) ,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id_pagina`),
    FOREIGN KEY ( `id_estado`) REFERENCES `paginas_estados`(`id_estado`),
    FOREIGN KEY ( `id_denuncia`) REFERENCES `denuncias`(`id_denuncia`)
);

-- Define la tabla de paginas_denunciadas_en, donde se registra la relación entre las denuncias y las paginas
-- la relación es ManyToMany
CREATE TABLE `pagina_en_denunciada` (
    `id_pagina` int(11) NOT NULL,
    `id_denuncia` int(11) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (`id_denuncia`) REFERENCES `paginas`(`id_pagina`),
    FOREIGN KEY ( `id_pagina`) REFERENCES `paginas`(`id_pagina`),
    PRIMARY KEY (`id_pagina`,`id_denuncia`)
)

-- Carga de datos
INSERT INTO `paginas_estados` (`descripcion`)
VALUES ('No Denunciado'), ('Denunciado'), ('Suspendido');

INSERT INTO `denuncia_estados` (`descripcion`)
VALUES ('Pendiente'), ('Finalizado');
