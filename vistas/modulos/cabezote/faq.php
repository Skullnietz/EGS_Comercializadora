<?php
if ($_SESSION["perfil"] == "administrador" || $_SESSION["perfil"] == "tecnico" || $_SESSION["perfil"] == "vendedor") {
	echo '
	<li class="dropdown faq-menu">
		<a href="index.php?ruta=preguntas" title="Ayuda">
			<i class="fa-regular fa-circle-question"></i>
		</a>
	</li>';
}
