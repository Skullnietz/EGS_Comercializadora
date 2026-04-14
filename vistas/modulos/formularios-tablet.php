<?php
// Validar sesión
if (!isset($_SESSION["validarSesionBackend"]) || $_SESSION["validarSesionBackend"] != "ok") {
    echo '<script>window.location = "inicio";</script>';
    exit();
}
?>

<!-- Import Signature Pad JS -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

<style>
/* Ocultar elementos distracciones para modo quiosco */
.main-header, .main-sidebar, .main-footer { display: none !important; }
.content-wrapper { margin-left: 0 !important; padding-top:0 !important; background: #eef2f5 !important; }

.tablet-kiosk {
    padding: 30px;
    font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    color: #333;
    max-width: 1000px;
    margin: 0 auto;
}
.kiosk-btn {
    width: 100%;
    height: 300px;
    border-radius: 20px;
    font-size: 32px;
    font-weight: 800;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    transition: transform 0.2s;
    cursor: pointer;
    border: none;
}
.kiosk-btn:active { transform: scale(0.97); }
.btn-ingreso { background: linear-gradient(135deg, #007bff, #0056b3); }
.btn-salida { background: linear-gradient(135deg, #28a745, #1d8835); }
.kiosk-btn i { font-size: 80px; margin-bottom: 20px; }

.kiosk-panel {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    display: none; 
    animation: fadeIn 0.4s ease;
}
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.kiosk-header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 15px; }
.kiosk-header h1 { font-weight: 800; color: #2c3e50; }

.order-confirm {
    background: #fdf5ce;
    border-left: 8px solid #f39c12;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    font-size: 24px;
    line-height: 1.5;
}
.order-confirm b { color: #d35400; font-size: 28px; }

.kiosk-form-group { margin-bottom: 35px; }
.kiosk-form-group label {
    display: block;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 15px;
    color: #34495e;
}
.kiosk-input {
    width: 100%;
    padding: 18px;
    font-size: 20px;
    border: 2px solid #bdc3c7;
    border-radius: 10px;
    background: #fafafa;
    transition: border 0.3s, background 0.3s;
    resize: vertical;
}
.kiosk-input:focus { border-color: #3498db; background: white; outline: none; }

/* Radio Buttons estilo Chips gigantes */
.radio-chips { display: flex; gap: 15px; flex-wrap: wrap; }
.radio-chips label {
    flex: 1;
    text-align: center;
    background: #ecf0f1;
    border: 2px solid #bdc3c7;
    border-radius: 10px;
    padding: 20px;
    font-size: 22px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    color: #7f8c8d;
    margin: 0;
}
.radio-chips input[type="radio"] { display: none; }
.radio-chips input[type="radio"]:checked + label {
    background: #3498db;
    border-color: #2980b9;
    color: white;
}

#signature-wrapper {
    border: 3px dashed #bdc3c7;
    border-radius: 15px;
    background: #fff;
    margin-bottom: 10px;
    overflow: hidden;
}
#signature-pad { touch-action: none; width: 100%; height: 300px; cursor: crosshair; }

.kiosk-action-btn {
    font-size: 24px;
    padding: 15px 40px;
    border-radius: 12px;
    font-weight: bold;
}
</style>

<div class="content-wrapper">
  <div class="tablet-kiosk" id="appRoot">
     
     <!-- SCREEN 1: HOME -->
     <div class="row" id="screenHome">
        <div class="col-md-12 text-center" style="margin-bottom:40px;">
           <img src="vistas/img/plantilla/logoEGS (1).png" style="width:150px; margin-bottom:20px;">
           <h2 style="font-weight:900; color:#2c3e50; font-size:35px;">ASISTENTE DE RECEPCIÓN Y ENTREGA</h2>
           <p style="font-size:22px; color:#7f8c8d;">Seleccione una opción para comenzar</p>
        </div>
        <div class="col-md-6 col-sm-12" style="margin-bottom:20px;">
           <button class="kiosk-btn btn-ingreso" onclick="app.startFlow('REV')">
               <i class="fa fa-tablet-alt"></i>
               INGRESO DE EQUIPO <br><small style="font-size:18px;">(Órdenes en REV)</small>
           </button>
        </div>
        <div class="col-md-6 col-sm-12">
           <button class="kiosk-btn btn-salida" onclick="app.startFlow('Entregado')">
               <i class="fa fa-box-open"></i>
               SALIDA DE EQUIPO <br><small style="font-size:18px;">(Órdenes Entregadas)</small>
           </button>
        </div>
     </div>

     <!-- SCREEN 2: CONFIRM ORDER -->
     <div id="screenConfirm" class="kiosk-panel">
         <div class="kiosk-header">
             <h1 id="confirmTitle">Confirmación de Equipo</h1>
         </div>
         <div class="order-confirm" id="confirmData">
            <!-- Data Injected VIA JS -->
         </div>
         <div style="display:flex; justify-content:space-between; margin-top:40px;">
            <button class="btn btn-danger kiosk-action-btn" onclick="app.goHome()"><i class="fa fa-arrow-left"></i> NO, VOLVER</button>
            <button class="btn btn-success kiosk-action-btn" onclick="app.goToForm()"><i class="fa fa-check"></i> SÍ, ES CORRECTO</button>
         </div>
     </div>

     <!-- SCREEN 3: FORM INGRESO -->
     <div id="screenFormIngreso" class="kiosk-panel">
         <div class="kiosk-header">
             <h1>FORMATO DE INGRESO (ORDEN <span class="lbl-orden-id"></span>)</h1>
             <p style="font-size:20px; color:#7f8c8d;">Favor de responder las preguntas, nos ayudará a agilizar el servicio.</p>
         </div>
         <form id="formIngreso">
             <div class="kiosk-form-group">
                 <label>1. Mencione brevemente qué problema o falla presenta su equipo:</label>
                 <textarea class="kiosk-input" id="ing_falla" rows="3" required placeholder="Toque para escribir..."></textarea>
             </div>
             <div class="kiosk-form-group">
                 <label>2. En caso de que su equipo tenga contraseña anótela por favor:</label>
                 <input type="text" class="kiosk-input" id="ing_pass" placeholder="El equipo no trae / La contraseña es...">
             </div>
             <div class="kiosk-form-group">
                 <label>3. Mencione si se va a realizar respaldo de su información, y dónde está:</label>
                 <textarea class="kiosk-input" id="ing_respaldo" rows="2" placeholder="Documentos, Escritorio, No requerido..."></textarea>
             </div>
             <div class="kiosk-form-group">
                 <label>4. En caso de requerir borrar su disco, ¿qué sistema operativo le gustaría que le entreguemos en su equipo? (¿Mismo que trae u otro?):</label>
                 <textarea class="kiosk-input" id="ing_so" rows="2" placeholder="Windows 10, Windows 11..."></textarea>
             </div>
             <div class="kiosk-form-group">
                 <label>5. ¿Desea agregar alguna información adicional?:</label>
                 <textarea class="kiosk-input" id="ing_adic" rows="2" placeholder="Viene con cargador, rallón en pantalla..."></textarea>
             </div>
             <div class="kiosk-form-group">
                 <label>6. ¿Qué tanto le urge su equipo?</label>
                 <div class="radio-chips">
                    <input type="radio" name="ing_urgencia" id="urg1" value="Hoy" required>
                    <label for="urg1">HOY</label>
                    <input type="radio" name="ing_urgencia" id="urg2" value="1-2 dias">
                    <label for="urg2">1-2 DÍAS</label>
                    <input type="radio" name="ing_urgencia" id="urg3" value="+ de 2 dias">
                    <label for="urg3">+ DE 2 DÍAS</label>
                 </div>
             </div>
         </form>
         <div style="display:flex; justify-content:space-between; margin-top:40px;">
            <button class="btn btn-default kiosk-action-btn" onclick="app.goHome()"><i class="fa fa-times"></i> CANCELAR</button>
            <button class="btn btn-primary kiosk-action-btn" onclick="app.goToSignature()"><i class="fa fa-pen"></i> CONTINUAR A FIRMA</button>
         </div>
     </div>

     <!-- SCREEN 4: FORM SALIDA -->
     <div id="screenFormSalida" class="kiosk-panel">
         <div class="kiosk-header">
             <h1>FORMATO DE SALIDA (ORDEN <span class="lbl-orden-id"></span>)</h1>
             <p style="font-size:20px; color:#7f8c8d;">Por favor marque la opción correcta. Sus respuestas nos ayudarán a mejorar.</p>
         </div>
         <form id="formSalida">
             <div class="kiosk-form-group">
                 <label>1. ¿El técnico le explicó la reparación que se realizó, de acuerdo a su orden?</label>
                 <div class="radio-chips">
                    <input type="radio" name="sal_exp_rep" id="sr1_si" value="Si" required><label for="sr1_si">SÍ</label>
                    <input type="radio" name="sal_exp_rep" id="sr1_no" value="No"><label for="sr1_no">NO</label>
                 </div>
             </div>
             <div class="kiosk-form-group">
                 <label>2. ¿Mostró que el equipo estuviera funcionando correctamente?</label>
                 <div class="radio-chips">
                    <input type="radio" name="sal_mos_fun" id="sr2_si" value="Si" required><label for="sr2_si">SÍ</label>
                    <input type="radio" name="sal_mos_fun" id="sr2_no" value="No"><label for="sr2_no">NO</label>
                 </div>
             </div>
             <div class="kiosk-form-group">
                 <label>3. ¿Le explicaron el tiempo de garantía y cómo aplica?</label>
                 <div class="radio-chips">
                    <input type="radio" name="sal_exp_gar" id="sr3_si" value="Si" required><label for="sr3_si">SÍ</label>
                    <input type="radio" name="sal_exp_gar" id="sr3_no" value="No"><label for="sr3_no">NO</label>
                 </div>
             </div>
             <div class="kiosk-form-group" style="background: #e1f5fe; padding:20px; border-radius:10px;">
                 <i class="fa fa-info-circle text-info" style="font-size:24px;"></i> 
                 <b style="font-size:20px;">Nota de Mantenimiento:</b> <span style="font-size:18px;">Si usted pidió mantenimiento, pida que le abran el equipo (si es posible) para mostrar que se haya realizado.</span>
             </div>
             <div class="kiosk-form-group">
                 <label>4. ¿Le entregaron el equipo limpio?</label>
                 <div class="radio-chips">
                    <input type="radio" name="sal_limpio" id="sr4_si" value="Si" required><label for="sr4_si">SÍ</label>
                    <input type="radio" name="sal_limpio" id="sr4_no" value="No"><label for="sr4_no">NO</label>
                 </div>
             </div>
             <div class="kiosk-form-group">
                 <label>5. ¿Cómo calificaría el servicio brindado?</label>
                 <div class="radio-chips">
                    <input type="radio" name="sal_calif" id="sr5_b" value="Bueno" required><label for="sr5_b" style="background:#e8f5e9; border-color:#81c784;">BUENO</label>
                    <input type="radio" name="sal_calif" id="sr5_r" value="Regular"><label for="sr5_r" style="background:#fff3e0; border-color:#ffb74d;">REGULAR</label>
                    <input type="radio" name="sal_calif" id="sr5_m" value="Malo"><label for="sr5_m" style="background:#ffebee; border-color:#e57373;">MALO</label>
                 </div>
             </div>
         </form>
         <div style="display:flex; justify-content:space-between; margin-top:40px;">
            <button class="btn btn-default kiosk-action-btn" onclick="app.goHome()"><i class="fa fa-times"></i> CANCELAR</button>
            <button class="btn btn-primary kiosk-action-btn" onclick="app.goToSignature()"><i class="fa fa-pen"></i> CONTINUAR A FIRMA</button>
         </div>
     </div>

     <!-- SCREEN 5: SIGNATURE & FINISH -->
     <div id="screenSignature" class="kiosk-panel">
         <div class="kiosk-header">
             <h1>Firme para confirmar</h1>
             <p style="font-size:20px; color:#7f8c8d;">Utilice su dedo o lápiz capacitivo en el recuadro inferior.</p>
         </div>
         <div id="signature-wrapper">
             <canvas id="signature-pad"></canvas>
         </div>
         <div class="text-right" style="margin-bottom:20px;">
             <button class="btn btn-warning btn-lg" onclick="app.clearSignature()"><i class="fa fa-eraser"></i> Limpiar Firma</button>
         </div>
         <div style="display:flex; justify-content:space-between; margin-top:40px;">
            <button class="btn btn-default kiosk-action-btn" onclick="app.goBackToForm()"><i class="fa fa-arrow-left"></i> REGRESAR AL FORMULARIO</button>
            <button class="btn btn-success kiosk-action-btn" style="font-size:28px;" onclick="app.saveForm()"><i class="fa fa-save"></i> GUARDAR Y FINALIZAR</button>
         </div>
     </div>

     <!-- SCREEN 6: SUCCESS -->
     <div id="screenSuccess" class="kiosk-panel text-center" style="padding: 100px 30px;">
         <i class="fa fa-check-circle" style="font-size:120px; color:#2ecc71; margin-bottom:30px;"></i>
         <h1 style="font-weight:900; font-size:40px;">¡Gracias! El formulario ha sido guardado exitosamente.</h1>
         <p style="font-size:24px; color:#7f8c8d; margin-top:20px;">Que tenga un excelente día.</p>
         <button class="btn btn-primary kiosk-action-btn" style="margin-top:50px;" onclick="app.goHome()">VOLVER AL INICIO</button>
     </div>

  </div>
</div>

<script>
// Deshabilitar sidebar en este modulo
$("body").addClass("sidebar-collapse");

// Controller Logic para el Quiosco
const app = {
    currentFlow: null, // 'REV' o 'Entregado'
    currentOrder: null,
    signaturePad: null,

    init: function() {
        this.hideAll();
        $("#screenHome").show();

        // Inicializar canvas de firma responsivo
        const canvas = document.getElementById('signature-pad');
        window.onresize = () => this.resizeCanvas();
        
        this.signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });
    },

    hideAll: function() {
        $(".kiosk-panel").hide();
        $("#screenHome").hide();
    },

    goHome: function() {
        this.currentFlow = null;
        this.currentOrder = null;
        if(this.signaturePad) this.signaturePad.clear();
        document.getElementById("formIngreso").reset();
        document.getElementById("formSalida").reset();
        this.hideAll();
        $("#screenHome").fadeIn();
    },

    startFlow: function(flow) {
        this.currentFlow = flow;
        
        swal({
            title: 'Buscando Orden...',
            text: 'Aguarde un momento por favor.',
            allowOutsideClick: false,
            onOpen: () => { swal.showLoading(); }
        });

        $.ajax({
            url: "ajax/formularios-tablet.ajax.php",
            method: "POST",
            data: { estado: flow },
            dataType: "json",
            success: function(respuesta) {
                if(respuesta && respuesta.error) {
                    swal({
                        type: 'error',
                        title: 'Error de BD',
                        text: respuesta.error,
                        confirmButtonText: 'Cerrar'
                    });
                    return;
                }

                if(!respuesta || !respuesta.id) {
                    swal({
                        type: 'warning',
                        title: 'Atención',
                        text: 'No se encontraron órdenes recientes con el estado solicitado.',
                        confirmButtonText: 'Cerrar'
                    });
                    return;
                }
                
                app.currentOrder = respuesta;
                swal.close();
                app.showConfirmScreen();
            },
            error: function() {
                swal({
                    type: 'error',
                    title: 'Error',
                    text: 'Hubo un error de conexión al consultar las órdenes.',
                    confirmButtonText: 'Cerrar'
                });
            }
        });
    },

    showConfirmScreen: function() {
        this.hideAll();
        const o = this.currentOrder;
        
        let html = `Equipo a recibir:<br>
                    <b>${o.marcaDelEquipo || ''} ${o.modeloDelEquipo || ''}</b><br>
                    No. de Serie: <span style="color:#2c3e50; font-weight:bold;">${o.numeroDeSerieDelEquipo || 'No Registrado'}</span><br><br>
                    Cliente detectado:<br>
                    <b>${o.nombre_cliente}</b><br><br>
                    Orden Original No. <b>${o.id}</b> - Creada el: ${o.fecha}`;
        
        $("#confirmData").html(html);
        $("#confirmTitle").text(this.currentFlow === 'REV' ? 'Confirmación de Ingreso' : 'Confirmación de Entrega');
        $(".lbl-orden-id").text(o.id);
        
        $("#screenConfirm").fadeIn();
    },

    goToForm: function() {
        this.hideAll();
        window.scrollTo(0,0);
        if(this.currentFlow === 'REV') {
            $("#screenFormIngreso").fadeIn();
        } else {
            $("#screenFormSalida").fadeIn();
        }
    },

    goBackToForm: function() {
        this.hideAll();
        if(this.currentFlow === 'REV') {
            $("#screenFormIngreso").fadeIn();
        } else {
            $("#screenFormSalida").fadeIn();
        }
    },

    goToSignature: function() {
        // Validación HTML5 Manual porque buttons no son type="submit"
        const formObj = this.currentFlow === 'REV' ? document.getElementById("formIngreso") : document.getElementById("formSalida");
        if(!formObj.checkValidity()) {
            formObj.reportValidity();
            return;
        }

        this.hideAll();
        $("#screenSignature").show();
        
        // Resize canvas NOW that it's visible so offsetWidth is correct > 0
        this.resizeCanvas();
        this.signaturePad.clear();
    },

    resizeCanvas: function() {
        const canvas = document.getElementById('signature-pad');
        const ratio =  Math.max(window.devicePixelRatio || 1, 1);
        const parentWidth = canvas.parentElement.offsetWidth;
        
        if (parentWidth === 0) return; // Still hidden
        
        // Only set if different to avoid clearing unintentionally
        if(canvas.width !== parentWidth * ratio) {
            canvas.width = parentWidth * ratio;
            canvas.height = 300 * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }
    },

    clearSignature: function() {
        this.signaturePad.clear();
    },

    saveForm: function() {
        if(this.signaturePad.isEmpty()) {
            swal({
                type: 'warning',
                title: 'Firma Requerida',
                text: 'Por favor, dibuje su firma para continuar.',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        const dataObj = {
            tipo_formulario: this.currentFlow === 'REV' ? "INGRESO DE EQUIPO" : "SALIDA DE EQUIPO",
            marcaModelo: this.currentOrder.marcaDelEquipo + " " + this.currentOrder.modeloDelEquipo,
            respuestas: {},
            firma: this.signaturePad.toDataURL()
        };

        if(this.currentFlow === 'REV') {
            dataObj.respuestas["Problema_o_falla"] = $("#ing_falla").val();
            dataObj.respuestas["Contraseña"] = $("#ing_pass").val();
            dataObj.respuestas["Respaldo_Info"] = $("#ing_respaldo").val();
            dataObj.respuestas["Sistema_Operativo"] = $("#ing_so").val();
            dataObj.respuestas["Info_Adicional"] = $("#ing_adic").val();
            dataObj.respuestas["Urgencia"] = $("input[name='ing_urgencia']:checked").val();
        } else {
            dataObj.respuestas["Explicaron_reparacion"] = $("input[name='sal_exp_rep']:checked").val();
            dataObj.respuestas["Mostro_funcionamiento"] = $("input[name='sal_mos_fun']:checked").val();
            dataObj.respuestas["Explicaron_garantia"] = $("input[name='sal_exp_gar']:checked").val();
            dataObj.respuestas["Entregaron_limpio"] = $("input[name='sal_limpio']:checked").val();
            dataObj.respuestas["Calificacion_servicio"] = $("input[name='sal_calif']:checked").val();
        }

        const idCreador = '<?php echo isset($_SESSION["id"]) ? $_SESSION["id"] : "1"; ?>';

        swal({
            title: 'Guardando',
            text: 'Enviando respuestas al sistema...',
            allowOutsideClick: false,
            onOpen: () => { swal.showLoading(); }
        });

        $.ajax({
            url: "ajax/formularios-tablet.ajax.php",
            method: "POST",
            data: {
                guardarFormulario: true,
                idOrden: app.currentOrder.id,
                formData: JSON.stringify(dataObj),
                idCreador: idCreador
            },
            dataType: "json",
            success: function(respuesta) {
                swal.close();
                if(respuesta.status == "ok") {
                    app.hideAll();
                    $("#screenSuccess").fadeIn();
                } else {
                    swal({
                        type: 'error',
                        title: 'Error',
                        text: 'Hubo un error al guardar los datos.',
                        confirmButtonText: 'Cerrar'
                    });
                }
            },
            error: function() {
                swal({
                    type: 'error',
                    title: 'Error',
                    text: 'Error de red o conexión al intentar guardar.',
                    confirmButtonText: 'Cerrar'
                });
            }
        });
    }

};

$(document).ready(function() {
    app.init();
});
</script>
