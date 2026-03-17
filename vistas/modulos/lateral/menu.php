<style>
  .sidebar-menu span { font-weight: 600; }
</style>

<?php
$_fotoSidebar = (!empty($_SESSION["foto"]))
    ? htmlspecialchars($_SESSION["foto"])
    : "vistas/img/perfiles/default/anonymous.png";
?>

<!-- ── User Panel ── -->
<div class="user-panel egs-sidebar-user">
  <div class="pull-left image">
    <img src="<?= $_fotoSidebar ?>" class="img-circle" alt="Usuario">
  </div>
  <div class="pull-left info">
    <p><?= htmlspecialchars($_SESSION["nombre"] ?? "Usuario") ?></p>
    <a href="#">
      <i class="fa-solid fa-circle" style="color:#28d16c;font-size:7px;vertical-align:middle"></i>
      &nbsp;En línea
    </a>
  </div>
</div>

<ul class="sidebar-menu">

  <?php

  if ($_SESSION["perfil"] == "Super-Administrador") {

    echo '<!--<li class="active"><a href="index.php?ruta=inicio"><i class="fas fa-home"></i> <span>Inicio</span></a></li><li><a href="index.php?ruta=comercio"><i class="fas fa-files-o"></i> <span>Gestor Comercio</span></a></li>-->';
  }

  //if($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "Super-Administrador"){
  
  if ($_SESSION["perfil"] == "Super-Administrador" || $_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria") {

    echo '<!--<li class="treeview">
          
          <a href="#">
            <i class="fas fa-th"></i>
            <span>Gestor Categorías</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            
            <li><a href="index.php?ruta=categorias"><i class="far fa-circle"></i> Categorías</a></li>
            <li><a href="index.php?ruta=subcategorias"><i class="far fa-circle"></i> Subcategorías</a></li>
          
          </ul>

      </li>
      

      <li class="treeview">
        
          <a href="#">
             <i class="fas fa-product-hunt"></i>
             <span>Gestor Productos</span>
             <span class="pull-right-container">
                 <i class="fas fa-angle-left pull-right"></i>
             </span>
          </a>
          <ul class="treeview-menu">
             <li><a href="index.php?ruta=productos"><i class="fas fa-product-hunt"></i> <span> Gestor Productos</span></a></li>
          </ul>

      </li>-->';
  }
  if ($_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria" || $_SESSION["perfil"] == "Super-Administrador" || $_SESSION["perfil"] == "secretaria") {
    echo '<li><a href="index.php?ruta=inicio"><i class="fas fa-home"></i><span> Tablero</span></a></li>';
  }
  if ($_SESSION["perfil"] == "Super-Administrador" || $_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria") {

    echo '
      <li class="treeview">
      
          <a href="#">
            <i class="fab fa-product-hunt"></i> 
            <span>Productos</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            
            <li><a href="index.php?ruta=productos"><i class="fab fa-product-hunt"></i><span> Gestor Productos</span></a></li>
            <li><a href="index.php?ruta=almacenes"><i class="fas fa-warehouse"></i><span> Gestor Almacenes</span></a></li>
          
          </ul>

      </li>

       
       
      <li><a href="index.php?ruta=ventasR"><i class="fas fa-cart-arrow-down"></i><span> Gestor Ventas R</span></a></li>
      
      <li><a href="index.php?ruta=creararventa" target=”_blank”><i class="fa fa-shopping-cart"></i> <span>Gestor Ventas</span></a></li>';
  }

  if ($_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria" || $_SESSION["perfil"] == "Super-Administrador" || $_SESSION["perfil"] == "secretaria") {

    echo '<li><a href="index.php?ruta=pedidos"><i class="fas fa-box-open"></i><span> Gestor pedidos</span></a></li>

      <li class="treeview">
      
          <a href="#">
            <i class="fas fa-clipboard-list"></i> 
            <span>Órdenes de servicio</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            
            <li><a href="index.php?ruta=ordenes"><i class="far fa-circle"></i> Órdenes</a></li>
            <li><a href="index.php?ruta=reportePorFecheOrdenes"><i class="far fa-circle"></i> Estado del servicio</a></li>
            <li><a href="index.php?ruta=comisiones"><i class="far fa-circle"></i> Comisiones</a></li>
            <li><a href="index.php?ruta=busquedamaterial"><i class="fas fa-search"></i> Busqueda de Material</a></li>';
    if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria" || $_SESSION["perfil"] == "Super-Administrador" || $_SESSION["perfil"] == "secretaria") {
      echo '<li><a href="index.php?ruta=ordenesnew"><i class="fas fa-file-alt"></i> Todas las ordenes</a></li>';
    }

    echo '</ul>';

  }


  if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "Super-Administrador") {

    echo '

        <!--<li><a href="index.php?ruta=reportes"><i class="fas fa-signal"></i><span> Reportes</span></a></li>-->';
  }
  if ($_SESSION["perfil"] == "Super-Administrador") {

    echo '<li><a href="index.php?ruta=empresas"><i class="fas fa-building"></i><span> Empresas</span></a></li>';
  }
  if ($_SESSION["perfil"] == "index.php?ruta=administrador" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria" || $_SESSION["perfil"] == "Super-Administrador") {

    echo '<li><a href="index.php?ruta=CorteTotal"><i class="fas fa-cash-register"></i><span> Corte de caja</span></a></li>';
  }
  if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "editor" || $_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "Super-Administrador") {

    echo ' <li><a href="index.php?ruta=clientes"><i class="fas fa-users"></i> <span> Gestor Clientes</span></a></li>';
  }

  if ($_SESSION["perfil"] == "administrador") {
    echo '

      <li class="treeview">
      
          <a href="#">
            <i class="fas fa-handshake"></i> 
            <span>CRM</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            
            <li><a href="index.php?ruta=metas"><i class="far fa-circle"></i> Metas</a></li>
            <li><a href="index.php?ruta=createobjetivo"><i class="far fa-circle"></i> Agregar meta</a></li>
            <li><a href="index.php?ruta=objetivos"><i class="far fa-circle"></i> Reporte de metas</a></li>
            <li><a href="index.php?ruta=listaobjetivos"><i class="far fa-circle"></i> Lista de metas</a></li>
          
          </ul>

      </li>';


  }
  if ($_SESSION["perfil"] == "administrador" AND $_SESSION["empresa"] == 1) {

    echo '<li><a href="index.php?ruta=visitas"><i class="fas fa-map-marker"></i><span> Gestor Visitas</span></a></li>';
    echo '<li><a href="index.php?ruta=seguimiento"><i class="fas fa-route"></i><span> Seguimiento </span></a></li>';



  }
  if ($_SESSION["perfil"] == "tecnico") {

    echo '<li><a href="index.php?ruta=metas"><i class="fas fa-handshake"></i> <span> Mis Metas </span></a></li>
      <li><a href="index.php?ruta=pantallacitas"><i class="fas fa-tv"></i> <span>Pantalla</span></a></li>
      <li><a href="index.php?ruta=peticionorden"><i class="fas fa-sync"></i> &nbsp;<span> Petición Orden</span></a></li>
        <li><a href="index.php?ruta=peticionmaterial"><i class="fas fa-tools"></i> &nbsp;<span> Peticion de material</span></a></li>';

  }
  if ($_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "secretaria") {

    echo '<li class="treeview">
      
          <a href="#">
            <i class="fas fa-handshake"></i> 
            <span>CRM</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            <li><a href="index.php?ruta=crm"><i class="far fa-cirlce"></i> CRM</a></li>
            <li><a href="index.php?ruta=metas"><i class="far fa-circle"></i> Metas</a></li>
            
          </ul>

      </li>';

  }

  if ($_SESSION["perfil"] == "Super-Administrador" || $_SESSION["perfil"] == "administrador") {

    echo '<li class="treeview">
        <a href="#">
            <i class="fas fa-users-cog"></i>
            <span>Personal</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
        <li><a href="index.php?ruta=perfiles"><i class="fas fa-key"></i> <span> Gestor Perfiles</span></a></li>
        <li><a href="index.php?ruta=asesores"><i class="fas fa-user-tie"></i><span> Asesores de Ventas</span></a></li>

        <li><a href="index.php?ruta=tecnicos"><i class="fas fa-user-cog"></i><span> Técnicos</span></a></li>
        </ul>
        ';
  }
  if ($_SESSION["perfil"] == "vendedor" || $_SESSION["perfil"] == "administrador") {
    echo ' <li class="treeview">
      
          <a href="#">
            <i class="far fa-calendar-alt"></i>
            <span>Citas</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            
            <li><a href="index.php?ruta=pantallacitas"><i class="fas fa-tv"></i> Pantalla</a></li>
            <li><a href="index.php?ruta=listacitas"><i class="far fa-circle"></i> Lista de citas</a></li>
            <li><a href="index.php?ruta=crearcita"><i class="far fa-calendar-plus"></i> Agregar cita</a></li>
          
          </ul>

      </li>
      
      <li class="treeview">
      
          <a href="#">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Cotización</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
           <ul class="treeview-menu">
            
            <li><a href="index.php?ruta=cotizacion"><i class="fas fa-file-invoice-dollar"></i></i>        &nbsp; Nueva cotización</a></li>
            
            <li><a href="index.php?ruta=historial-cotizaciones"><i class="fas fa-hand-holding-usd"></i>      
            &nbsp;Historial cotizaciones </a></li>
            </li> </ul>
            
            
            <li class="treeview">
        <a href="#">
            <i class="far fa-edit"></i>
            <span>Peticiones</span>
            <span class="pull-right-container">
                <i class="fas fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
        <li><a href="index.php?ruta=peticionorden"><i class="fas fa-sync"></i> &nbsp;<span> Petición Orden</span></a></li>
        <li><a href="index.php?ruta=peticionmaterial"><i class="fas fa-tools"></i> &nbsp;<span> Peticion de material</span></a></li>
        <li><a href="index.php?ruta=listaPeticionesM"><i class="far fa-list-alt"></i> &nbsp;<span> Lista Peticion material &nbsp; &nbsp;</span></a></li>
        </ul>';

  }
  ?>

</ul>