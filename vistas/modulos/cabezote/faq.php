<!--=====================================
PREGUNTAS FRECUENTES
======================================-->
<?php
if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "tecnico" | $_SESSION["perfil"] == "vendedor") {
	
	echo'<!-- faq-menu -->
	<li class="dropdown faq-menu">
		
		<!-- dropdown-toggle -->
		<a href="index.php?ruta=preguntas">
			
			<i class="far fa-question-circle"></i>
			
		</a>
	
		
	</li>';
	
}