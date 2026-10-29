<?php
/*
 * David Céspedes Camacho, Ronald Calderón, Susan Echavarría Céspedes, Sofía Loaiza Cabalceta
 * Ambiente Web Cliente/Servidor · SC-502
 * Universidad Fidélitas · Costa Rica
 *
 * Pantalla: Admin · Detalle de Solicitud (RF-03) (Figma: Admin - Detalle de Solicitud)
 * Ruta: admin/solicitudes/detalle?id=1
 *
 * Expediente completo de una solicitud de ingreso: identidad del negocio,
 * representante legal, ubicación, muestra de catálogo y el dictamen del
 * administrador (aprobar o rechazar). Para rechazar es obligatorio escribir
 * la observación que se le enviará al emprendedor.
 */

$titulo       = 'Detalle de Solicitud · Administración';
$estilos      = ['admin', 'admin_gestion', 'admin_solicitudes'];
$scripts      = ['validaciones', 'solicitud_detalle'];
$navbar       = false;
$footer       = false;
$claseBody    = 'body-panel';
$seccionPanel = 'solicitudes';

if (!isset($solicitud)) {
    $muestras = [
        1 => [
            'tienda' => 'Miel Dorada de Orosi', 'rubro' => 'Apicultura Sostenible', 'categoria' => 'Comida & Botánica Artesanal',
            'zona' => 'Valle de Orosi, Paraíso, Cartago', 'expediente' => '#CR-2025-081', 'recibido' => '14 Mar, 2025', 'puntaje' => 98,
            'imagen' => 'productos/miel-cruda.jpg',
            'cedula' => '3-101-789456', 'cedulaEstado' => 'Validada ante Registro Nac.', 'modalidad' => 'Taller Familiar Rur-Agro', 'modalidadNota' => '3 Colmenares Certificados',
            'memoria' => 'Miel Dorada de Orosi es un emprendimiento agroartesanal fundado en las laderas nubosas de Cartago. Nos especializamos en la producción y extracción en frío de miel 100% pura y derivados de colmenas de rescate. Preservamos la flora autóctona sin agroquímicos, promoviendo el comercio justo y la divulgación de técnicas tradicionales de apicultura biológica en la zona protectora del Río Grande de Orosi.',
            'documentos' => ['Permiso_Sanitario_SENASA_2025.pdf', 'Cedula_Fisica_Representante.pdf'],
            'rep' => ['nombre' => 'Esteban Brenes Sanabria', 'iniciales' => 'EB', 'cedula' => '3-0442-0193', 'rol' => 'Apicultor Certificado INA', 'telefono' => '+506 8841-2910', 'sinpe' => true, 'correo' => 'contacto@mieldorada.cr', 'instagram' => '@mieldorada.orosi'],
            'checklist' => [['Identidad Tributaria Tributa-CR', 'Al Día'], ['Cuenta IBAN para liquidación', 'CR05...8912']],
            'lugar' => 'Taller & Apiario Central', 'direccion' => '2.5 km Este del Templo Colonial, Orosi, Cartago', 'coordenadas' => 'Lat 9.7972° N, Long 83.8569° W', 'envio' => 'Envía vía Correos CR y PYME Express',
            'compromiso' => 'El solicitante se compromete a mantener sellos de trazabilidad verde y etiquetado con advertencia de alérgenos según normativa nacional.',
            'productos' => [
                ['nombre' => 'Frasco Miel Cruda de Orosi', 'descripcion' => 'Extracción en frío, rica en néctar de flores nativas de Orosi. Sin pasteurizar ni aditivos.', 'precio' => 6500, 'sku' => 'MO-01', 'medida' => '500g', 'rubro' => 'Cosecha nubosa', 'imagen' => 'productos/miel-cruda.jpg'],
                ['nombre' => 'Polen Granulado de Abeja', 'descripcion' => 'Secado solar artesanal, fuente pura de proteínas bioactivas y antioxidantes de altura.', 'precio' => 4800, 'sku' => 'MO-02', 'medida' => '250g', 'rubro' => 'Superalimento natural', 'imagen' => 'productos/salsa-maracuya.jpg'],
                ['nombre' => 'Velas de Cera de Abeja', 'descripcion' => 'Cera virgen recolectada de opérculos, mecha de algodón puro e infusión de lavanda.', 'precio' => 5200, 'sku' => 'MO-03', 'medida' => 'Dúo 20cm', 'rubro' => 'Aromaterapia hogar', 'imagen' => 'productos/jabon-romero.jpg'],
            ],
        ],
        2 => [
            'tienda' => 'Cerámica Boruca Ancestral', 'rubro' => 'Arte Indígena Brunka', 'categoria' => 'Arte & Cerámica',
            'zona' => 'Territorio Indígena Boruca, Buenos Aires, Puntarenas', 'expediente' => '#CR-2025-078', 'recibido' => '13 Mar, 2025', 'puntaje' => 94,
            'imagen' => 'productos/aretes-precolombinos.jpg',
            'cedula' => '3-002-654321', 'cedulaEstado' => 'Validada ante Registro Nac.', 'modalidad' => 'Colectivo Artesanal Indígena', 'modalidadNota' => '8 artesanas asociadas',
            'memoria' => 'Colectivo de mujeres Brunka que trabaja el barro con pigmentos naturales y técnicas heredadas de generación en generación. Cada pieza lleva grabados que cuentan la cosmovisión del pueblo Boruca y se cocina en horno de leña dentro del territorio.',
            'documentos' => ['Certificado_Indigena_ADI.pdf', 'Cedula_Juridica_Colectivo.pdf'],
            'rep' => ['nombre' => 'Marta Leiva Quintero', 'iniciales' => 'ML', 'cedula' => '6-0301-0455', 'rol' => 'Maestra ceramista Brunka', 'telefono' => '+506 8712-3340', 'sinpe' => true, 'correo' => 'colectivo@boruca.cr', 'instagram' => '@ceramica.boruca'],
            'checklist' => [['Identidad Tributaria Tributa-CR', 'Al Día'], ['Cuenta IBAN para liquidación', 'CR21...4410']],
            'lugar' => 'Taller Comunitario Boruca', 'direccion' => '300 m Norte de la Escuela de Boruca, Buenos Aires, Puntarenas', 'coordenadas' => 'Lat 9.0147° N, Long 83.3382° W', 'envio' => 'Envía vía Correos CR',
            'compromiso' => 'El colectivo se compromete a mantener el origen cultural de cada pieza y a indicar en la ficha del producto la técnica y la comunidad de origen.',
            'productos' => [
                ['nombre' => 'Aretes de Barro Precolombinos', 'descripcion' => 'Modelados a mano y pintados con pigmentos naturales. Cada par es único.', 'precio' => 7500, 'sku' => 'CB-01', 'medida' => 'Par', 'rubro' => 'Joyería de barro', 'imagen' => 'productos/aretes-precolombinos.jpg'],
                ['nombre' => 'Vasija Ceremonial Pequeña', 'descripcion' => 'Cocida en horno de leña, con grabados de la cosmovisión Brunka.', 'precio' => 18500, 'sku' => 'CB-02', 'medida' => '18 cm', 'rubro' => 'Cerámica ancestral', 'imagen' => 'productos/tabla-teca.jpg'],
                ['nombre' => 'Set de Tazas de Barro', 'descripcion' => 'Esmalte mineral natural y acabado mate. Aptas para bebidas calientes.', 'precio' => 14200, 'sku' => 'CB-03', 'medida' => 'Set x2', 'rubro' => 'Hogar artesanal', 'imagen' => 'productos/cafe-geisha.jpg'],
            ],
        ],
        3 => [
            'tienda' => 'Quesos y Lácteos Monteverde', 'rubro' => 'Lechería Artesanal', 'categoria' => 'Comida Artesanal',
            'zona' => 'Santa Elena, Monteverde, Puntarenas', 'expediente' => '#CR-2025-072', 'recibido' => '12 Mar, 2025', 'puntaje' => 81,
            'imagen' => 'productos/salsa-maracuya.jpg',
            'cedula' => '3-101-561203', 'cedulaEstado' => 'Validada ante Registro Nac.', 'modalidad' => 'Quesería Familiar', 'modalidadNota' => '2 fincas proveedoras',
            'memoria' => 'Quesería familiar de Monteverde que elabora quesos frescos y semimaduros con leche de fincas de la zona. Falta adjuntar el carné de manipulación de alimentos de SENASA para completar el expediente.',
            'documentos' => ['Cedula_Fisica_Representante.pdf'],
            'rep' => ['nombre' => 'Andrés Cordero Vargas', 'iniciales' => 'AC', 'cedula' => '1-1180-0742', 'rol' => 'Maestro quesero', 'telefono' => '+506 8650-1177', 'sinpe' => true, 'correo' => 'hola@quesosmonteverde.cr', 'instagram' => '@quesos.monteverde'],
            'checklist' => [['Identidad Tributaria Tributa-CR', 'Al Día'], ['Carné de manipulación SENASA', 'Pendiente']],
            'lugar' => 'Quesería Santa Elena', 'direccion' => '1 km Oeste de la Escuela de Santa Elena, Monteverde', 'coordenadas' => 'Lat 10.3010° N, Long 84.8270° W', 'envio' => 'Envío con cadena de frío (por coordinar)',
            'compromiso' => 'El solicitante se compromete a mantener la cadena de frío y a etiquetar fecha de elaboración y vencimiento en cada producto.',
            'productos' => [
                ['nombre' => 'Queso Fresco de Monteverde', 'descripcion' => 'Leche de la zona, sal marina y cuajo natural. Se vende al vacío.', 'precio' => 4500, 'sku' => 'QM-01', 'medida' => '400g', 'rubro' => 'Lácteos frescos', 'imagen' => 'productos/salsa-maracuya.jpg'],
                ['nombre' => 'Queso Semimaduro con Hierbas', 'descripcion' => 'Maduración de 45 días con culantro de monte y pimienta.', 'precio' => 7800, 'sku' => 'QM-02', 'medida' => '350g', 'rubro' => 'Madurados', 'imagen' => 'productos/chocolate-bribri.jpg'],
                ['nombre' => 'Natilla Artesanal', 'descripcion' => 'Crema espesa de leche entera, sin estabilizantes.', 'precio' => 2900, 'sku' => 'QM-03', 'medida' => '250g', 'rubro' => 'Tradición tica', 'imagen' => 'productos/miel-cruda.jpg'],
            ],
        ],
        4 => [
            'tienda' => 'Hilados y Telas de Sarchí', 'rubro' => 'Textiles Tradicionales', 'categoria' => 'Ropa & Textiles',
            'zona' => 'Sarchí Norte, Alajuela', 'expediente' => '#CR-2025-069', 'recibido' => '11 Mar, 2025', 'puntaje' => 92,
            'imagen' => 'productos/tote-yute.jpg',
            'cedula' => '3-102-448801', 'cedulaEstado' => 'Validada ante Registro Nac.', 'modalidad' => 'Taller Artesanal Verificado', 'modalidadNota' => '5 telares activos',
            'memoria' => 'Taller de Sarchí dedicado a telas tejidas en telar de pedal con algodón y fibras naturales. Elaboran bolsos, manteles y textiles para el hogar con tintes vegetales de la zona.',
            'documentos' => ['Patente_Municipal_Sarchi.pdf', 'Cedula_Fisica_Representante.pdf'],
            'rep' => ['nombre' => 'Rocío Alfaro Jiménez', 'iniciales' => 'RA', 'cedula' => '2-0511-0388', 'rol' => 'Maestra tejedora', 'telefono' => '+506 8390-5521', 'sinpe' => true, 'correo' => 'info@hiladossarchi.cr', 'instagram' => '@hilados.sarchi'],
            'checklist' => [['Identidad Tributaria Tributa-CR', 'Al Día'], ['Cuenta IBAN para liquidación', 'CR33...7720']],
            'lugar' => 'Taller de Telares Sarchí', 'direccion' => '200 m Sur del Parque Central de Sarchí Norte', 'coordenadas' => 'Lat 10.0870° N, Long 84.3480° W', 'envio' => 'Envía vía Correos CR y PYME Express',
            'compromiso' => 'El solicitante se compromete a usar fibras y tintes de origen natural y a declarar la composición de cada prenda.',
            'productos' => [
                ['nombre' => 'Tote de Yute Tejido', 'descripcion' => 'Tejido en telar de pedal con asas reforzadas y forro de algodón.', 'precio' => 12500, 'sku' => 'HS-01', 'medida' => '40x35 cm', 'rubro' => 'Bolsos', 'imagen' => 'productos/tote-yute.jpg'],
                ['nombre' => 'Mantel de Algodón Teñido', 'descripcion' => 'Teñido con añil y achiote, lavable a mano.', 'precio' => 16800, 'sku' => 'HS-02', 'medida' => '1.5 m', 'rubro' => 'Hogar', 'imagen' => 'productos/tabla-teca.jpg'],
                ['nombre' => 'Camino de Mesa Bordado', 'descripcion' => 'Bordado a mano con motivos de flora costarricense.', 'precio' => 9800, 'sku' => 'HS-03', 'medida' => '1.2 m', 'rubro' => 'Hogar', 'imagen' => 'productos/jabon-romero.jpg'],
            ],
        ],
        5 => [
            'tienda' => 'Botánica de la Península', 'rubro' => 'Cosmética Natural', 'categoria' => 'Belleza Natural',
            'zona' => 'Cóbano, Península de Nicoya, Puntarenas', 'expediente' => '#CR-2025-064', 'recibido' => '09 Mar, 2025', 'puntaje' => 95,
            'imagen' => 'productos/jabon-romero.jpg',
            'cedula' => '3-101-702918', 'cedulaEstado' => 'Validada ante Registro Nac.', 'modalidad' => 'Laboratorio Artesanal', 'modalidadNota' => 'Registro Sanitario vigente',
            'memoria' => 'Laboratorio artesanal que elabora jabones, bálsamos y aceites con plantas cultivadas en la península. Todos los productos son libres de parabenos y no se prueban en animales.',
            'documentos' => ['Registro_Sanitario_MinSalud.pdf', 'Cedula_Fisica_Representante.pdf'],
            'rep' => ['nombre' => 'Elena Solís Mora', 'iniciales' => 'ES', 'cedula' => '5-0388-0214', 'rol' => 'Química farmacéutica', 'telefono' => '+506 8433-7012', 'sinpe' => true, 'correo' => 'hola@botanicapeninsula.cr', 'instagram' => '@botanica.peninsula'],
            'checklist' => [['Identidad Tributaria Tributa-CR', 'Al Día'], ['Cuenta IBAN para liquidación', 'CR58...3305']],
            'lugar' => 'Laboratorio Botánica', 'direccion' => '150 m Este del Banco Nacional, Cóbano', 'coordenadas' => 'Lat 9.7300° N, Long 85.1170° W', 'envio' => 'Envía vía Correos CR',
            'compromiso' => 'La solicitante se compromete a mantener el registro sanitario al día y a listar los ingredientes completos en cada etiqueta.',
            'productos' => [
                ['nombre' => 'Jabón de Romero y Avena', 'descripcion' => 'Jabón en frío con aceite de coco y romero cultivado en la península.', 'precio' => 3800, 'sku' => 'BP-01', 'medida' => '100g', 'rubro' => 'Cuidado de la piel', 'imagen' => 'productos/jabon-romero.jpg'],
                ['nombre' => 'Bálsamo de Cacao y Cera', 'descripcion' => 'Bálsamo labial con manteca de cacao y cera de abeja local.', 'precio' => 2500, 'sku' => 'BP-02', 'medida' => '15g', 'rubro' => 'Cuidado labial', 'imagen' => 'productos/chocolate-bribri.jpg'],
                ['nombre' => 'Aceite Corporal de Coco', 'descripcion' => 'Prensado en frío e infusionado con flores de ylang-ylang.', 'precio' => 6900, 'sku' => 'BP-03', 'medida' => '120ml', 'rubro' => 'Aceites', 'imagen' => 'productos/miel-cruda.jpg'],
            ],
        ],
        6 => [
            'tienda' => 'Joyería de Cacao Talamanca', 'rubro' => 'Joyería Botánica', 'categoria' => 'Accesorios',
            'zona' => 'Puerto Viejo, Talamanca, Limón', 'expediente' => '#CR-2025-058', 'recibido' => '08 Mar, 2025', 'puntaje' => 78,
            'imagen' => 'productos/chocolate-bribri.jpg',
            'cedula' => '3-101-330754', 'cedulaEstado' => 'Validada ante Registro Nac.', 'modalidad' => 'Taller Familiar', 'modalidadNota' => '2 artesanos',
            'memoria' => 'Taller de Talamanca que trabaja semillas, cáscara de cacao y fibras locales para elaborar collares, pulseras y aretes. Falta validar la cobertura de envío de Correos de Costa Rica en la zona.',
            'documentos' => ['Cedula_Fisica_Representante.pdf'],
            'rep' => ['nombre' => 'Kevin Blandón Rojas', 'iniciales' => 'KB', 'cedula' => '7-0245-0611', 'rol' => 'Artesano joyero', 'telefono' => '+506 8566-4098', 'sinpe' => false, 'correo' => 'talamanca@joyeriacacao.cr', 'instagram' => '@joyeria.cacao'],
            'checklist' => [['Identidad Tributaria Tributa-CR', 'Al Día'], ['Cobertura de Correos CR', 'Por validar']],
            'lugar' => 'Taller Playa Cocles', 'direccion' => '400 m Sur del puente de Playa Cocles, Puerto Viejo', 'coordenadas' => 'Lat 9.6540° N, Long 82.7510° W', 'envio' => 'Cobertura por validar',
            'compromiso' => 'El solicitante se compromete a usar materiales recolectados de forma sostenible y a declarar el origen de las semillas.',
            'productos' => [
                ['nombre' => 'Collar de Semillas de Cacao', 'descripcion' => 'Semillas curadas al sol con cierre de coco pulido.', 'precio' => 8900, 'sku' => 'JC-01', 'medida' => '45 cm', 'rubro' => 'Collares', 'imagen' => 'productos/chocolate-bribri.jpg'],
                ['nombre' => 'Aretes de Cáscara de Cacao', 'descripcion' => 'Livianos, laqueados con resina natural.', 'precio' => 5600, 'sku' => 'JC-02', 'medida' => 'Par', 'rubro' => 'Aretes', 'imagen' => 'productos/aretes-precolombinos.jpg'],
                ['nombre' => 'Pulsera de Fibra y Semilla', 'descripcion' => 'Trenzada a mano, talla ajustable.', 'precio' => 4200, 'sku' => 'JC-03', 'medida' => 'Ajustable', 'rubro' => 'Pulseras', 'imagen' => 'productos/tote-yute.jpg'],
            ],
        ],
    ];

    // El id llega por la URL (?id=1). Si no existe, se muestra la primera solicitud.
    $idSolicitud = isset($_GET['id']) ? (int) $_GET['id'] : 1;
    if (!isset($muestras[$idSolicitud])) {
        $idSolicitud = 1;
    }
    $solicitud       = $muestras[$idSolicitud];
    $solicitud['id'] = $idSolicitud;
}

$representante   = $solicitud['rep'];
$totalDocumentos = count($solicitud['documentos']);
$totalChecklist  = count($solicitud['checklist']);
$totalProductos  = count($solicitud['productos']);

// Estado de la solicitud (los datos de muestra no lo traen: se toma como pendiente)
$estadoSolicitud = valor($solicitud, 'estado', 'pendiente');
$yaResuelta      = $estadoSolicitud !== 'pendiente';
$textosEstado    = ['pendiente' => 'En revisión', 'aprobado' => 'Aprobada', 'rechazado' => 'Rechazada'];

require __DIR__ . '/../general/header.php';

// header.php ya cargó Vista.php e inició la sesión
$tokenCsrf = valor($_SESSION, 'csrf_token', '');
?>

<div class="panel">
    <?php require __DIR__ . '/../general/sidebar_admin.php'; ?>

    <div class="panel__principal">
        <?php require __DIR__ . '/../general/barra_admin.php'; ?>

        <main id="contenido" class="panel__contenido">

            <!-- Volver y datos del expediente -->
            <div class="de-superior">
                <a class="de-volver" href="<?php echo e(ruta('admin/solicitudes')); ?>"><?php echo icono('izquierda'); ?> Volver a Emprendimientos Pendientes</a>
                <p class="de-superior__datos">
                    <span class="so-insignia so-insignia--expediente"><span class="punto"></span> Expediente <?php echo e($solicitud['expediente']); ?></span>
                    <span class="so-insignia so-insignia--expediente"><?php echo icono('reloj'); ?> Recibido: <?php echo e($solicitud['recibido']); ?></span>
                </p>
            </div>

            <!-- Encabezado de la solicitud -->
            <section class="de-cabecera">
                <img class="de-cabecera__logo" src="<?php echo e(recurso('img/uploads/' . $solicitud['imagen'])); ?>" alt="" width="64" height="64">
                <div class="de-cabecera__info">
                    <p class="de-cabecera__etiquetas">
                        <span class="so-insignia so-insignia--nueva">SOLICITUD DE INGRESO</span>
                        <span class="de-cabecera__rubro"><?php echo icono('hoja'); ?> <?php echo e($solicitud['rubro']); ?></span>
                        <span class="so-insignia so-insignia--obs" id="estadoSolicitud"><?php echo e($textosEstado[$estadoSolicitud]); ?></span>
                    </p>
                    <h1 class="ad-titulo"><?php echo e($solicitud['tienda']); ?></h1>
                    <p class="de-cabecera__zona"><?php echo icono('ubicacion'); ?> <?php echo e($solicitud['zona']); ?> • Costa Rica</p>
                </div>
                <div class="de-puntaje">
                    <small>Puntaje preliminar</small>
                    <strong><?php echo (int) $solicitud['puntaje']; ?>/100 <?php echo icono('escudo'); ?></strong>
                </div>
            </section>

            <div class="de-cuadricula">

                <!-- Columna izquierda -->
                <div class="de-columna">

                    <section class="de-tarjeta" aria-labelledby="tituloIdentidad">
                        <div class="de-tarjeta__cabecera">
                            <h2 id="tituloIdentidad"><span class="de-tarjeta__icono"><?php echo icono('tienda'); ?></span> Identidad del Negocio</h2>
                            <span class="ad-chip ad-chip--teal"><?php echo e($solicitud['categoria']); ?></span>
                        </div>

                        <div class="de-datos">
                            <div class="de-dato">
                                <small>Cédula jurídica</small>
                                <strong><?php echo e($solicitud['cedula']); ?></strong>
                                <span class="de-dato__ok"><?php echo icono('verificado'); ?> <?php echo e($solicitud['cedulaEstado']); ?></span>
                            </div>
                            <div class="de-dato">
                                <small>Modalidad operativa</small>
                                <strong><?php echo e($solicitud['modalidad']); ?></strong>
                                <span><?php echo icono('verificado'); ?> <?php echo e($solicitud['modalidadNota']); ?></span>
                            </div>
                        </div>

                        <h3 class="de-subtitulo">Memoria descriptiva</h3>
                        <p class="de-memoria"><?php echo e($solicitud['memoria']); ?></p>

                        <h3 class="de-subtitulo">Comprobantes adjuntos por el emprendedor</h3>
                        <?php if ($totalDocumentos === 0): ?>
                            <p class="de-memoria">El emprendedor todavía no adjuntó archivos.</p>
                        <?php endif; ?>
                        <ul class="de-documentos">
                            <?php for ($i = 0; $i < $totalDocumentos; $i++): ?>
                                <li>
                                    <?php echo icono('recibo'); ?>
                                    <span><?php echo e($solicitud['documentos'][$i]); ?></span>
                                    <button class="de-documentos__ver" type="button" aria-label="Ver <?php echo e($solicitud['documentos'][$i]); ?>"><?php echo icono('ojo'); ?></button>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </section>

                    <section class="de-tarjeta" aria-labelledby="tituloUbicacion">
                        <div class="de-tarjeta__cabecera">
                            <h2 id="tituloUbicacion"><span class="de-tarjeta__icono"><?php echo icono('ubicacion'); ?></span> Ubicación y Despacho Físico</h2>
                            <span class="de-envio"><?php echo icono('camion'); ?> <?php echo e($solicitud['envio']); ?></span>
                        </div>

                        <!-- Espacio del mapa: en el avance con base de datos se cambia por un mapa real -->
                        <div class="de-mapa" role="img" aria-label="Ubicación de <?php echo e($solicitud['lugar']); ?>">
                            <div class="de-mapa__pie">
                                <div>
                                    <strong><?php echo e($solicitud['lugar']); ?></strong>
                                    <small><?php echo e($solicitud['direccion']); ?></small>
                                </div>
                                <span class="de-mapa__coordenadas"><?php echo e($solicitud['coordenadas']); ?></span>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Columna derecha -->
                <div class="de-columna">

                    <section class="de-tarjeta" aria-labelledby="tituloRepresentante">
                        <div class="de-tarjeta__cabecera">
                            <h2 id="tituloRepresentante"><span class="de-tarjeta__icono"><?php echo icono('usuario'); ?></span> Representante Legal</h2>
                        </div>

                        <div class="de-representante">
                            <span class="de-representante__avatar"><?php echo e($representante['iniciales']); ?></span>
                            <div>
                                <strong><?php echo e($representante['nombre']); ?></strong>
                                <small>Céd. Identidad: <?php echo e($representante['cedula']); ?></small>
                                <span class="de-dato__ok"><?php echo icono('verificado'); ?> <?php echo e($representante['rol']); ?></span>
                            </div>
                        </div>

                        <ul class="de-contactos">
                            <li>
                                <span class="de-contactos__icono"><?php echo icono('mensaje'); ?></span>
                                <div>
                                    <small>Teléfono / WhatsApp
                                        <?php if ($representante['sinpe']): ?>
                                            <span class="de-sinpe">SINPE Móvil Activo</span>
                                        <?php endif; ?>
                                    </small>
                                    <strong><?php echo e($representante['telefono']); ?></strong>
                                </div>
                            </li>
                            <li>
                                <span class="de-contactos__icono"><?php echo icono('correo'); ?></span>
                                <div>
                                    <small>Correo electrónico</small>
                                    <strong><?php echo e($representante['correo']); ?></strong>
                                </div>
                            </li>
                            <li>
                                <span class="de-contactos__icono"><?php echo icono('compartir'); ?></span>
                                <div>
                                    <small>Instagram comercial</small>
                                    <strong><?php echo e($representante['instagram']); ?></strong>
                                </div>
                            </li>
                        </ul>

                        <div class="de-checklist">
                            <h3 class="de-subtitulo">Checklist automático</h3>
                            <ul>
                                <?php for ($i = 0; $i < $totalChecklist; $i++): ?>
                                    <li>
                                        <span><?php echo icono('verificado'); ?> <?php echo e($solicitud['checklist'][$i][0]); ?></span>
                                        <strong><?php echo e($solicitud['checklist'][$i][1]); ?></strong>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                    </section>

                    <section class="de-compromiso" aria-labelledby="tituloCompromiso">
                        <small>Compromiso tico</small>
                        <h2 id="tituloCompromiso">100% Hecho en Costa Rica</h2>
                        <p><?php echo e($solicitud['compromiso']); ?></p>
                        <?php echo icono('hoja', 'de-compromiso__hoja'); ?>
                    </section>
                </div>
            </div>

            <!-- Muestra de catálogo -->
            <section class="de-tarjeta de-tarjeta--ancha" aria-labelledby="tituloCatalogo">
                <div class="de-tarjeta__cabecera">
                    <div>
                        <h2 id="tituloCatalogo"><span class="de-tarjeta__icono"><?php echo icono('caja'); ?></span> Muestra de Catálogo Postulado</h2>
                        <p class="de-tarjeta__sub"><?php echo $totalProductos; ?> productos iniciales preparados para indexación en Ventica Marketplace</p>
                    </div>
                    <span class="ad-chip ad-chip--suave"><?php echo $totalProductos; ?> Artículos Revisados</span>
                </div>

                <ul class="de-productos">
                    <?php for ($i = 0; $i < $totalProductos; $i++): ?>
                        <?php $producto = $solicitud['productos'][$i]; ?>
                        <li class="de-producto">
                            <div class="de-producto__foto">
                                <img src="<?php echo e(recurso('img/uploads/' . $producto['imagen'])); ?>" alt="<?php echo e($producto['nombre']); ?>" width="320" height="200">
                                <span class="de-producto__medida"><?php echo e($producto['medida']); ?></span>
                            </div>
                            <div class="de-producto__cuerpo">
                                <small class="de-producto__rubro"><?php echo e($producto['rubro']); ?></small>
                                <h3><?php echo e($producto['nombre']); ?></h3>
                                <p><?php echo e($producto['descripcion']); ?></p>
                                <div class="de-producto__pie">
                                    <span><small>Precio público</small><strong><?php echo colones($producto['precio']); ?></strong></span>
                                    <span class="de-producto__sku">SKU: <?php echo e($producto['sku']); ?></span>
                                </div>
                            </div>
                        </li>
                    <?php endfor; ?>
                </ul>
            </section>

            <!-- Dictamen -->
            <section class="de-tarjeta de-tarjeta--ancha" aria-labelledby="tituloDictamen">
                <div class="de-tarjeta__cabecera">
                    <div>
                        <h2 id="tituloDictamen"><span class="de-tarjeta__icono"><?php echo icono('recibo'); ?></span> Dictamen de Auditoría &amp; Resolución</h2>
                        <p class="de-tarjeta__sub">La resolución notificará automáticamente al emprendedor vía correo y WhatsApp</p>
                    </div>
                    <span class="de-registro"><?php echo icono('candado'); ?> Acción registrada por Superadmin</span>
                </div>

                <?php if ($yaResuelta): ?>
                    <div class="mensaje <?php echo $estadoSolicitud === 'aprobado' ? 'mensaje--exito' : 'mensaje--error'; ?>" role="status">
                        <p>Esta solicitud ya fue <?php echo $estadoSolicitud === 'aprobado' ? 'aprobada' : 'rechazada'; ?>.<?php echo valor($solicitud, 'observacion', '') !== '' ? ' Observación: ' . e($solicitud['observacion']) : ''; ?></p>
                    </div>
                <?php endif; ?>

                <div id="mensajeDictamen" class="mensaje mensaje--exito ad-oculto" role="status">
                    <p id="textoMensajeDictamen"></p>
                </div>

                <form id="formDictamen" method="post" action="<?php echo e(ruta('admin/solicitudes/resolver')); ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo e($tokenCsrf); ?>">
                    <input type="hidden" name="id_solicitud" value="<?php echo (int) $solicitud['id']; ?>">
                    <input type="hidden" name="decision" id="decision" value="">
                    <input type="hidden" id="nombreEmprendimiento" value="<?php echo e($solicitud['tienda']); ?>">

                    <div class="campo" id="campo-observaciones">
                        <label class="campo__etiqueta" for="observaciones">Observaciones para el emprendedor (opcional si aprueba, obligatorio si rechaza):</label>
                        <textarea class="campo__entrada de-observaciones" id="observaciones" name="observaciones" rows="4" maxlength="500" placeholder="Comentarios de auditoría o motivo de rechazo si aplica..."></textarea>
                        <p class="campo__error" id="errorObservaciones">Escribe el motivo del rechazo (mínimo 10 caracteres).</p>
                    </div>

                    <div class="de-dictamen__botones">
                        <button class="ad-boton ad-boton--claro de-boton" type="button" id="botonRechazar" <?php echo $yaResuelta ? 'disabled' : ''; ?>><?php echo icono('cerrar'); ?> Rechazar Solicitud</button>
                        <button class="ad-boton ad-boton-verde de-boton" type="button" id="botonAprobar" <?php echo $yaResuelta ? 'disabled' : ''; ?>><?php echo icono('check'); ?> Aprobar Emprendimiento</button>
                    </div>
                </form>
            </section>

        </main>
    </div>
</div>

<?php require __DIR__ . '/../general/footer.php'; ?>
