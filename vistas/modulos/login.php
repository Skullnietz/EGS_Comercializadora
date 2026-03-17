<?php
// 1. CONFIGURACI���N
header('Content-Type: text/html; charset=UTF-8');

function obtenerFondoEstacional() {
    $mes = (int)date("n"); 
    $dia = (int)date("j"); 

    $biblioteca = [

        // --- ENERO: Inicio, Planificación, Invierno ---
        1 => [
            "photo-1611988615248-5d4f0b9ac31e", "photo-1608751444940-a8ea4c0696ee", "photo-1706514447301-29fc91aeb6ec",
            "photo-1578147063111-9ffec96050cd", "photo-1611583786970-0cbe126a1a78", "photo-1735823440223-e4f45b5c4a45",
            "photo-1546766796-b3560d439487", "photo-1515251721820-48a70bdc846d", "photo-1531297461136-82lw9z21171d",
            "photo-1608889175166-d174c1b6c8c0" // ⛄ Nueva: Carretera nevada en invierno (JuJiy1hlnnY)
        ],

        // --- FEBRERO: Tecnología, Seriedad ---
        2 => [
            "photo-1519389950473-47ba0277781c", "photo-1555421689-49263376271e", "photo-1504384308090-c54be3855091",
            "photo-1518770660439-4636190af475", "photo-1451187580459-43490279c0fa", "photo-1526304640152-d4619684e484",
            "photo-1531482615713-2afd69097998", "photo-1556761175-5973dc0f32e7", "photo-1505373877841-8d25f7d46678",
            "photo-1491438590914-bc09fcaaf77a"
        ],

        // --- MARZO: Primavera, Logística ---
        3 => [
            "photo-1586528116311-ad8dd3c8310d", "photo-1497366216548-37526070297c", "photo-1566576912902-192ca15239bf",
            "photo-1506744038136-46273834b3fb", "photo-1517245386807-bb43f82c33c4", "photo-1522202176988-66273c2fd55f",
            "photo-1558403194-611308249627", "photo-1521791136064-7986c2920216", "photo-1507679799987-c73779587ccf",
            "photo-1581090700227-1e8a6c6f8d20" // 🚛 Nueva: Camión logístico en carretera
        ],

        // --- ABRIL: Crecimiento ---
        4 => [
            "photo-1460925895917-afdab827c52f", "photo-1487014679447-9f8336841d58", "photo-1507525428034-b723cf961d3e",
            "photo-1556740738-b6a63e27c4df", "photo-1553877607-3cc980cd655e", "photo-1573164713988-8665fc963095",
            "photo-1552664730-d307ca884978", "photo-1535713875002-d1d0cf377fde", "photo-1497215728101-856f4ea42174",
            "photo-1504639725590-34d0984388bd"
        ],

        // --- MAYO: Energía, Claridad ---
        5 => [
            "photo-1477959858617-67f85cf4f1df", "photo-1556761175-4b46a572b786", "photo-1513530534585-c7b1394c6d51",
            "photo-1500530855697-b586d89ba3ee", "photo-1542744173-8e7e53415bb0", "photo-1510519138101-570d1dca3d66",
            "photo-1526628953301-3e589a6a8b74", "photo-1495195129352-aeb325a55b65", "photo-1486312338219-ce68d2c6f44d",
            "photo-1523240795612-9a054b0db644"
        ],

        // --- JUNIO: Verano, Puertos ---
        6 => [
            "photo-1494412574643-35d3d4018828", "photo-1455849318743-b2233052fcff", "photo-1516321318423-f06f85e504b3",
            "photo-1581091226825-a6a2a5aee158", "photo-1465146344425-f00d5f5c8f07", "photo-1531297461136-82lw9z21171d",
            "photo-1504384308090-c54be3855091", "photo-1518770660439-4636190af475", "photo-1522071820081-009f0129c71c",
            "photo-1470770841072-f978cf4d019e"
        ],

        // --- JULIO: Azul profundo, Global ---
        7 => [
            "photo-1507525428034-b723cf961d3e", "photo-1506744038136-46273834b3fb", "photo-1521791136064-7986c2920216",
            "photo-1451187580459-43490279c0fa", "photo-1550751827-4bd374c3f58b", "photo-1497366216548-37526070297c",
            "photo-1486406146926-c627a92ad1ab", "photo-1556740758-90de63f607d8", "photo-1555421689-49263376271e",
            "photo-1505373877841-8d25f7d46678"
        ],

        // --- AGOSTO: Trabajo duro ---
        8 => [
            "photo-1486312338219-ce68d2c6f44d", "photo-1497215728101-856f4ea42174", "photo-1542744173-8e7e53415bb0",
            "photo-1460925895917-afdab827c52f", "photo-1477959858617-67f85cf4f1df", "photo-1518600506278-4e8ef466b810",
            "photo-1531297461136-82lw9z21171d", "photo-1557683316-973673baf926", "photo-1504384308090-c54be3855091",
            "photo-1616627452411-c03b1b1b80fd" // 🔨 Nueva: Trabajador en obra (BIeC4YK2MTA)
        ],

        // --- SEPTIEMBRE: Regreso, Estructura ---
        9 => [
            "photo-1507537297725-24a1c029d3ca", "photo-1435575653489-b0873ec954e2", "photo-1556761175-5973dc0f32e7",
            "photo-1498050108023-c5249f4df085", "photo-1483058712412-4245e9b90334", "photo-1519389950473-47ba0277781c",
            "photo-1487014679447-9f8336841d58", "photo-1522071820081-009f0129c71c", "photo-1551434678-e076c223a692",
            "photo-1542601906990-b4d3fb778b09"
        ],

        // --- OCTUBRE: Redes, Mapas ---
        10 => [
            "photo-1451187580459-43490279c0fa", "photo-1521791136064-7986c2920216", "photo-1510915228340-29c85a43dcfe",
            "photo-1550751827-4bd374c3f58b", "photo-1563986768609-322da13575f3", "photo-1507679799987-c73779587ccf",
            "photo-1531297461136-82lw9z21171d", "photo-1557804506-669a67965ba0", "photo-1494412574643-35d3d4018828",
            "photo-1486406146926-c627a92ad1ab"
        ],

        // --- NOVIEMBRE: Cierre, Lluvia/Cristal ---
        11 => [
            "photo-1554224155-8d04cb21cd6c", "photo-1518133910546-b6c2fb7d79e3", "photo-1462206092226-f46025ffe607",
            "photo-1497215728101-856f4ea42174", "photo-1556740758-90de63f607d8", "photo-1484503704168-bfb994137d5e",
            "photo-1505373877841-8d25f7d46678", "photo-1551288049-bebda4e38f71", "photo-1516321318423-f06f85e504b3",
            "photo-1523240795612-9a054b0db644"
        ],

        // --- DICIEMBRE: Fin de Año, Festivo ---
        12 => [
            "photo-1668018774680-ea07d89ed7bc", // 🎆 Nueva: Festividad navideña (zgNFArWRC0U)
            "photo-1637675793836-fe2ee7cf2ce9",
            "photo-1457269449834-928af64c684d", "photo-1518600506278-4e8ef466b810", "photo-1575129382318-3df171f20a1a",
            "photo-1636777405172-37604daaa12b", "photo-1489674267075-cee793167910", "photo-1455156218388-5e61b526818b",
            "photo-1516466723877-e4ec1d736c8a"
        ]
    ];

    $lista_mes = isset($biblioteca[$mes]) ? $biblioteca[$mes] : $biblioteca[1];
    $indice = ($dia - 1) % count($lista_mes);
    return "https://images.unsplash.com/" . $lista_mes[$indice] . "?q=80&w=1920&auto=format&fit=crop";
}

$imagen_fondo = obtenerFondoEstacional();
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
    </style>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/2.2.1/js.cookie.min.js"></script>
</head>
<body>

    <div class="loader"></div>

    <div class="fondo-animado" style="background-image: url('<?php echo $imagen_fondo; ?>');"></div>

    <div class="capa-overlay"></div>

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
            
            console.log("--- EGS VISUAL ---");
            console.log("Temporada: <?php echo date('F'); ?>");
            console.log("Visual: Dron Effect");
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