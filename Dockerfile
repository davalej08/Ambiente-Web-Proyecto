# David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
# Ambiente Web Cliente/Servidor · SC-502
# Universidad Fidélitas · Costa Rica
#
# Imagen para publicar Ventica en Render (u otro hosting con Docker).
# Render no trae PHP listo, por eso se usa esta imagen oficial de PHP
# con la extensión pdo_mysql para conectarse a la base de Aiven.
#
# Las credenciales NO van aquí: se ponen en Render > Environment como
# DB_HOST, DB_PORT, DB_USER, DB_PASS y DB_NAME (config/database.php las lee).

FROM php:8.2-cli

# Extensión para MySQL
RUN docker-php-ext-install pdo_mysql

# Límites de subida de imágenes (2 MB por foto)
RUN echo "upload_max_filesize=3M" > /usr/local/etc/php/conf.d/ventica.ini \
    && echo "post_max_size=4M" >> /usr/local/etc/php/conf.d/ventica.ini \
    && echo "date.timezone=America/Costa_Rica" >> /usr/local/etc/php/conf.d/ventica.ini

WORKDIR /app
COPY . .

# Carpetas donde se guardan fotos subidas y correos de prueba
RUN mkdir -p public/static/img/uploads/productos-nuevos public/static/img/uploads/logos storage/correos \
    && chmod -R 777 public/static/img/uploads storage

# Render indica el puerto en la variable PORT
CMD php -S 0.0.0.0:${PORT:-10000} -t public public/index.php
