<?php
/**
 * routes.php — Mapa de rutas permitidas del sistema EGS.
 *
 * Cada string corresponde al valor de $_GET["ruta"] y al nombre del archivo
 * en vistas/modulos/<ruta>.php que se incluye.
 *
 * Para agregar una ruta nueva:  solo añade el slug al array.
 * Para desactivar una ruta:     comenta o elimina la entrada correspondiente.
 *
 * IMPORTANTE: No incluir rutas de entorno de prueba aquí.
 *             Los archivos de prueba fueron eliminados del repositorio.
 */
return [

    /* ── Dashboard ─────────────────────────────── */
    'inicio',
    'comentarios-hoy',
    'cambios-estado-hoy',
    'todas-notificaciones',

    /* ── Catálogo de productos ──────────────────── */
    'categorias',
    'subcategorias',
    'productos',
    'categoriasOrden',
    'productoswoocommerce',
    'banner',
    'stock',

    /* ── Órdenes de servicio ────────────────────── */
    'ordenes',
    'ordenesnew',
    'lasordenes',
    'EstadoOrden',
    'infoOrden',
    'seguimiento',
    'Historialdecliente',

    /* ── Ventas ─────────────────────────────────── */
    'ventas',
    'ventasR',
    'ventasD',
    'creararventa',
    'CorteTotal',

    /* ── Pedidos ────────────────────────────────── */
    'pedidos',
    'AgregarPedido',
    'infopedido',
    'peticionorden',

    /* ── Cotizaciones ───────────────────────────── */
    'cotizacion',
    'historial-cotizaciones',
    'validar-cotizacion',
    'imprimir-cotizacion',

    /* ── Clientes ───────────────────────────────── */
    'clientes',
    'cliente',
    'infoCliente',
    'crm',

    /* ── Personal: técnicos y asesores ─────────── */
    'tecnicos',
    'asesores',
    'comisiones',
    'comisionesDos',

    /* ── Metas y objetivos ──────────────────────── */
    'metas',
    'objetivos',
    'createobjetivo',
    'listaobjetivos',
    'objetivosventas',
    'objetivoselectronica',
    'objetivosimpresoras',
    'objetivossistemas',
    'objetivosventasext',

    /* ── Citas ──────────────────────────────────── */
    'pantallacitas',
    'crearcita',
    'listacitas',

    /* ── Inventario y almacenes ─────────────────── */
    'almacenes',
    'peticionmaterial',
    'listaPeticionesM',
    'busquedamaterial',

    /* ── Tickets de soporte ─────────────────────── */
    'tickets',
    'crearTicket',
    'infoTicket',

    /* ── Administración ─────────────────────────── */
    'perfiles',
    'perfil',
    'usuarios',
    'empresas',
    'visitas',
    'reportes',
    'reportePorFecheOrdenes',

    /* ── Proveedores / compras ──────────────────── */
    'categoriasp',
    'ingreso',

    /* ── Misceláneos ────────────────────────────── */
    'preguntas',
    'salir',

];
