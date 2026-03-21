<?php
// 1. CONFIGURACIÓN
header('Content-Type: text/html; charset=UTF-8');

/**
 * Obtiene la imagen del día de Bing.
 * Cachea el resultado en un archivo temporal para no consultar la API en cada carga.
 * Si Bing falla, usa un fallback de Unsplash.
 */
function obtenerBingImagenDelDia() {
    // --- Cache local (1 archivo por día) ---
    $cacheDir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'egs_bing_cache';
    $cacheFile = $cacheDir . DIRECTORY_SEPARATOR . 'bing_' . date('Y-m-d') . '.json';

    // Si ya tenemos cache de hoy, devolver directamente
    if (file_exists($cacheFile)) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if (!empty($cached['url'])) {
            return $cached;
        }
    }

    // --- Consultar API de Bing ---
    $apiUrl = 'https://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1&mkt=es-MX';
    $result = ['url' => '', 'title' => '', 'copyright' => ''];

    try {
        $ctx = stream_context_create([
            'http' => ['timeout' => 5, 'ignore_errors' => true],
            'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false]
        ]);
        $response = @file_get_contents($apiUrl, false, $ctx);

        if ($response !== false) {
            $data = json_decode($response, true);
            if (!empty($data['images'][0])) {
                $img = $data['images'][0];
                // Bing devuelve URLs relativas como /th?id=... o /az/hprichbg/rb/...
                $imgUrl = $img['url'];
                if (strpos($imgUrl, 'http') !== 0) {
                    $imgUrl = 'https://www.bing.com' . $imgUrl;
                }
                // Forzar resolución UHD (1920x1080)
                $imgUrl = preg_replace('/\d+x\d+/', '1920x1080', $imgUrl);

                $result['url']       = $imgUrl;
                $result['title']     = isset($img['title']) ? $img['title'] : '';
                $result['copyright'] = isset($img['copyright']) ? $img['copyright'] : '';
            }
        }
    } catch (Exception $e) {
        // silenciar errores
    }

    // --- Fallback si Bing no respondió ---
    if (empty($result['url'])) {
        $fallbacks = [
            'photo-1506744038136-46273834b3fb',
            'photo-1451187580459-43490279c0fa',
            'photo-1519389950473-47ba0277781c',
            'photo-1477959858617-67f85cf4f1df',
            'photo-1507525428034-b723cf961d3e',
        ];
        $pick = $fallbacks[date('j') % count($fallbacks)];
        $result['url']       = 'https://images.unsplash.com/' . $pick . '?q=80&w=1920&auto=format&fit=crop';
        $result['title']     = '';
        $result['copyright'] = 'Unsplash';
    }

    // --- Guardar cache ---
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    // Limpiar caches de días anteriores
    foreach (glob($cacheDir . DIRECTORY_SEPARATOR . 'bing_*.json') as $old) {
        if ($old !== $cacheFile) @unlink($old);
    }
    @file_put_contents($cacheFile, json_encode($result));

    return $result;
}

$_bingData     = obtenerBingImagenDelDia();
$imagen_fondo  = $_bingData['url'];
$_bingTitle    = $_bingData['title'];
$_bingCopy     = $_bingData['copyright'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Comercializadora EGS</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --egs-azul-claro: #368bc2;
            --egs-azul-medio: #205b8a;
            --egs-azul-oscuro: #103250;
        }

        html, body {
            margin: 0;
            height: 100%;
            width: 100%;
            font-family: "Montserrat", sans-serif;
            background-color: #1B2030;
            overflow: hidden;
        }

        /* --- 1. FONDO ANIMADO CINEM���TICO --- */
        .fondo-animado {
            position: fixed;
            top: 0; left: 0; 
            width: 100%; height: 100%;
            z-index: 0;
            
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            
            /* Preparaci���n para el efecto zoom */
            transform: scale(1.2); 
            
            /* Animaci���n: 45s de movimiento suave tipo dron */
            animation: bajadaCinematica 45s ease-in-out infinite alternate;
        }

        @keyframes bajadaCinematica {
            0% {
                transform: scale(1.15) translateY(-40px);
            }
            100% {
                transform: scale(1.25) translateY(40px);
            }
        }

        /* --- 2. CAPA OVERLAY --- */
        .capa-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 1;
            background: linear-gradient(to bottom, rgba(16, 50, 80, 0.3) 0%, rgba(27,32,48,0.95) 100%);
            pointer-events: none;
        }

        /* --- 3. CONTENIDO --- */
        .header {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100vh;
            color: #fff;
        }

        .brand-corner {
            position: absolute;
            top: 30px; left: 30px;
            z-index: 20;
            border: 2px solid rgba(255,255,255,0.3);
            padding: 5px 15px; border-radius: 5px;
            background: rgba(0,0,0,0.2);
            backdrop-filter: blur(5px);
        }
        .logo-text { font-weight: 900; font-size: 1.5em; letter-spacing: 2px; color: white; }

        .login-container {
            width: 90%; max-width: 450px;
            background: rgba(16, 32, 50, 0.80); 
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.7);
            text-align: center;
        }

        .user-img img {
            width: 130px; height: 130px; border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.2);
            background: white; object-fit: contain; padding: 5px;
            margin-bottom: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .form-group { position: relative; margin-bottom: 25px; }
        .form-group input {
            background: rgba(0, 0, 0, 0.4); border: none;
            border-bottom: 2px solid rgba(255,255,255,0.3);
            border-radius: 5px 5px 0 0; height: 55px; color: #fff;
            padding-left: 50px; font-size: 1rem; transition: all 0.3s;
        }
        .form-group input::placeholder { color: #bbb; }
        .form-group input:focus {
            background: rgba(0, 0, 0, 0.6); border-bottom-color: var(--egs-azul-claro);
            color: white; outline: none;
        }
        .form-group i { position: absolute; left: 18px; top: 18px; color: #ddd; font-size: 1.1rem; }

        .btn-ingresar {
            background: linear-gradient(135deg, var(--egs-azul-medio), var(--egs-azul-oscuro));
            color: white; border: none; padding: 14px;
            border-radius: 50px; font-weight: 700; font-size: 1.1em;
            width: 100%; margin-top: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            letter-spacing: 1px; transition: 0.3s;
        }
        .btn-ingresar:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, var(--egs-azul-claro), var(--egs-azul-medio));
            color: white;
        }

        .reloj-widget { margin-bottom: 30px; color: white; }
        .hora-grande { font-size: 3em; font-weight: 800; line-height: 1; text-shadow: 0 4px 10px rgba(0,0,0,0.4); }
        .fecha-texto { font-size: 0.9em; text-transform: uppercase; letter-spacing: 2px; opacity: 0.9; font-weight: 600; margin-bottom: 5px;}

        .loader {
            position: fixed; left: 0; top: 0; width: 100%; height: 100%; z-index: 9999;
            background: #1B2030 url('vistas/img/plantilla/ajax-loader.gif') 50% 50% no-repeat;
        }

        /* --- Crédito Bing Image of the Day --- */
        .bing-credit {
            position: fixed;
            bottom: 12px;
            right: 16px;
            z-index: 15;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.8);
            font-size: 11px;
            font-weight: 400;
            max-width: 420px;
            line-height: 1.3;
            transition: opacity .3s;
            cursor: default;
        }
        .bing-credit:hover { opacity: 1; color: #fff; }
        .bing-credit i { font-size: 13px; opacity: .7; flex-shrink: 0; }
        .bing-credit-title { font-weight: 600; }

        @media (max-width: 576px) {
            .bing-credit { max-width: 260px; font-size: 10px; bottom: 8px; right: 8px; }
        }
    </style>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/2.2.1/js.cookie.min.js"></script>
</head>
<body>

    <div class="loader"></div>

    <div class="fondo-animado" style="background-image: url('<?php echo $imagen_fondo; ?>');"></div>

    <div class="capa-overlay"></div>

    <?php if (!empty($_bingTitle) || !empty($_bingCopy)): ?>
    <div class="bing-credit">
        <i class="fas fa-camera"></i>
        <span>
            <?php if (!empty($_bingTitle)): ?>
                <span class="bing-credit-title"><?= htmlspecialchars($_bingTitle) ?></span>
            <?php endif; ?>
            <?php if (!empty($_bingCopy)): ?>
                <br><?= htmlspecialchars($_bingCopy) ?>
            <?php endif; ?>
        </span>
    </div>
    <?php endif; ?>

    <div class="header">
        <div class="brand-corner"><span class="logo-text">EGS</span></div>

        <div class="login-container">
            <div class="reloj-widget">
                <div class="fecha-texto" id="fechaTexto">CARGANDO...</div>
                <div class="hora-grande" id="horaTexto">--:--</div>
            </div>

            <div class="user-img">
                <img loading="lazy" src="vistas/img/plantilla/Captura3.PNG" alt="Comercializadora EGS">
            </div>

            <form method="post">
                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" class="form-control" placeholder="Correo Electr&oacute;nico" name="ingEmail" required>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input id="contra" type="password" class="form-control" placeholder="Contrase&ntilde;a" name="ingPassword" autocomplete="off" required>
                </div>

                <button type="submit" id="entrar" class="btn btn-ingresar">INGRESAR</button>
                <?php
                    $login = new ControladorAdministradores();
                    $login -> ctrIngresoAdministrador();
                ?>
            </form>
        </div>
    </div>

    <script>
        $(window).on('load', function() { $(".loader").fadeOut("slow"); });
        
        $(document).ready(function(){
            $('#contra').keypress(function(e){ if(e.keyCode==13) $('#entrar').click(); });
            actualizarReloj(); setInterval(actualizarReloj, 1000);
            
            console.log("--- EGS LOGIN ---");
            console.log("Fondo: Bing Image of the Day");
            console.log("Título: <?= addslashes($_bingTitle) ?>");
        });

        function actualizarReloj() {
            const ahora = new Date();
            const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            let fechaStr = ahora.toLocaleDateString('es-ES', opciones).toUpperCase();
            document.getElementById('fechaTexto').innerText = fechaStr;
            let horas = ahora.getHours();
            let minutos = ahora.getMinutes();
            const ampm = horas >= 12 ? 'PM' : 'AM';
            horas = horas % 12; horas = horas ? horas : 12; 
            minutos = minutos < 10 ? '0' + minutos : minutos;
            document.getElementById('horaTexto').innerText = horas + ':' + minutos + ' ' + ampm;
        }
    </script>
</body>
</html>