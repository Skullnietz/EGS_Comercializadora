<div class="content-wrapper">

    <section class="content-header">
        <h1>
            Calendario de Citas
            <small>Panel de Control</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Calendario de Citas</li>
        </ol>
    </section>

    <section class="content">

        <div class="egs-section" style="border-radius:14px;overflow:hidden;border:1px solid #e2e8f0;box-shadow:0 4px 24px rgba(15,23,42,.08);">

            <!-- Título con gradiente -->
            <div class="egs-title-bar" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <i class="fa-solid fa-calendar-check" style="font-size:18px;opacity:.9;"></i>
                    <span style="font-size:16px;font-weight:700;">Agenda de Citas</span>
                </div>
                <button type="button" class="btn" data-toggle="modal" data-target="#modalAgregarCita"
                    style="background:rgba(255,255,255,.18);color:#fff;border:1.5px solid rgba(255,255,255,.3);border-radius:8px;padding:7px 16px;font-size:12px;font-weight:600;transition:all .15s;">
                    <i class="fa-solid fa-plus" style="margin-right:5px;"></i>Nueva Cita
                </button>
            </div>

            <div class="egs-body" style="padding:20px;background:#fff;border-radius:0 0 14px 14px;">
                <div id="calendar"></div>
            </div>

        </div>

    </section>

</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL: Agregar Cita (chips de fecha + grid de hora)
     ═══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalAgregarCita" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width:520px;margin:60px auto;">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(15,23,42,.22);">

            <form role="form" id="formularioCita">

                <!-- Header -->
                <div style="background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:20px 24px;color:#fff;">
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;text-shadow:none;font-size:22px;">&times;</button>
                    <h4 style="margin:0;font-size:17px;font-weight:700;">
                        <i class="fa-regular fa-calendar-plus" style="margin-right:8px;"></i>Agendar Cita
                    </h4>
                    <p style="margin:6px 0 0;font-size:12px;opacity:.8;">Selecciona cuándo y agrega los detalles</p>
                </div>

                <!-- Body -->
                <div style="padding:22px 24px;max-height:68vh;overflow-y:auto;">

                    <!-- Título -->
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="egs-pc-lbl"><i class="fa-solid fa-tag" style="margin-right:4px;color:#6366f1;"></i>T&iacute;tulo de la Cita</label>
                        <input type="text" class="form-control egs-pc-input" id="tituloCita" name="tituloCita"
                            placeholder="Ej: Revisión equipo cliente" required>
                    </div>

                    <!-- No. de Orden -->
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="egs-pc-lbl">
                            <i class="fa-solid fa-file-lines" style="margin-right:4px;color:#6366f1;"></i>No. de Orden o Pedido <span style="color:#94a3b8;font-weight:400;">(opcional)</span>
                        </label>
                        <input type="number" class="form-control egs-pc-input" id="idOrden" name="idOrden"
                            placeholder="Ingresa el ID de la orden o pedido" min="1">
                    </div>

                    <!-- Comentarios -->
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="egs-pc-lbl">
                            <i class="fa-solid fa-comment-dots" style="margin-right:4px;color:#6366f1;"></i>Comentarios / Observaciones
                        </label>
                        <textarea class="form-control egs-pc-input" id="descripcionCita" name="descripcionCita" rows="2"
                            placeholder="¿De qué trata la cita? Ej: Revisión de pantalla, entrega de equipo..."
                            style="resize:vertical;min-height:50px;"></textarea>
                        <small style="font-size:10px;color:#94a3b8;margin-top:4px;display:block;">Opcional — Aparecerá al consultar la cita</small>
                    </div>

                    <!-- ═══ Quick Date Chips ═══ -->
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="egs-pc-lbl">
                            <i class="fa-regular fa-clock" style="margin-right:4px;color:#6366f1;"></i>¿Cuándo?
                        </label>
                        <div class="egs-pc-quick-dates" id="pcQuickDates">
                            <button type="button" class="egs-pc-qd-chip active" data-fecha="hoy"><i class="fa-solid fa-sun"></i> Hoy</button>
                            <button type="button" class="egs-pc-qd-chip" data-fecha="manana"><i class="fa-solid fa-forward"></i> Mañana</button>
                            <button type="button" class="egs-pc-qd-chip" data-fecha="lunes"><i class="fa-regular fa-calendar"></i> Lunes</button>
                            <button type="button" class="egs-pc-qd-chip" data-fecha="martes"><i class="fa-regular fa-calendar"></i> Martes</button>
                            <button type="button" class="egs-pc-qd-chip" data-fecha="miercoles"><i class="fa-regular fa-calendar"></i> Miércoles</button>
                            <button type="button" class="egs-pc-qd-chip" data-fecha="jueves"><i class="fa-regular fa-calendar"></i> Jueves</button>
                            <button type="button" class="egs-pc-qd-chip" data-fecha="viernes"><i class="fa-regular fa-calendar"></i> Viernes</button>
                            <button type="button" class="egs-pc-qd-chip" data-fecha="sabado"><i class="fa-regular fa-calendar"></i> Sábado</button>
                            <button type="button" class="egs-pc-qd-chip egs-pc-qd-custom" data-fecha="personalizado"><i class="fa-solid fa-pen"></i> Elegir fecha</button>
                        </div>

                        <div id="pcCustomDateWrap" style="display:none;margin-top:10px;">
                            <input type="date" class="form-control egs-pc-input" id="pcFechaCustom">
                        </div>

                        <div id="pcDatePreview" style="margin-top:8px;padding:8px 12px;background:#f1f5f9;border-radius:8px;font-size:12px;color:#475569;display:flex;align-items:center;gap:6px;">
                            <i class="fa-regular fa-calendar-check" style="color:#6366f1;"></i>
                            <span id="pcDatePreviewText">Hoy — <?php echo date('l j \d\e F'); ?></span>
                        </div>
                    </div>

                    <!-- ═══ Time Grid ═══ -->
                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="egs-pc-lbl">
                            <i class="fa-regular fa-clock" style="margin-right:4px;color:#6366f1;"></i>Hora
                        </label>
                        <div id="pcHorarioInfo" style="margin-bottom:8px;padding:6px 10px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;font-size:11px;color:#92400e;display:flex;align-items:center;gap:6px;">
                            <i class="fa-solid fa-info-circle"></i>
                            <span id="pcHorarioTexto">L-V: 10:00–14:00 y 16:00–18:30</span>
                        </div>
                        <div class="egs-pc-time-grid" id="pcTimeGrid"></div>
                        <input type="hidden" id="fechaCita" name="fechaCita">
                    </div>

                    <!-- Color automático -->
                    <div class="form-group" style="margin-bottom:6px;">
                        <label class="egs-pc-lbl">
                            <i class="fa-solid fa-palette" style="margin-right:4px;color:#6366f1;"></i>Etiqueta de color
                        </label>
                        <input type="hidden" id="colorCita" name="colorCita" value="#3a87ad">
                        <div id="pcAutoColorPreview" style="padding:10px 14px;border-radius:8px;background:#f1f5f9;border:1.5px solid #e2e8f0;font-size:12px;color:#64748b;display:flex;align-items:center;gap:8px;">
                            <span id="pcAutoColorDot" style="width:14px;height:14px;border-radius:50%;background:#3a87ad;flex-shrink:0;"></span>
                            <span id="pcAutoColorText">Sin orden — se usará color por defecto</span>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div style="padding:14px 24px 20px;display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #f1f5f9;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"
                        style="border-radius:8px;padding:8px 18px;font-size:13px;">Cancelar</button>
                    <button type="submit" class="btn" id="btnGuardarCitaPC"
                        style="border-radius:8px;padding:8px 22px;font-size:13px;font-weight:600;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;transition:opacity .15s;">
                        <i class="fa-solid fa-check" style="margin-right:5px;"></i>Agendar
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL: Detalle de Cita (contexto completo)
     ═══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalDetalleCita" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width:560px;margin:50px auto;">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(15,23,42,.22);">

            <!-- Header dinámico (color del evento) -->
            <div id="dcHeader" style="padding:20px 24px;color:#fff;position:relative;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;text-shadow:none;font-size:22px;position:absolute;right:16px;top:14px;">&times;</button>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                    <i class="fa-solid fa-calendar-day" style="font-size:18px;opacity:.9;"></i>
                    <h4 id="dcTitle" style="margin:0;font-size:17px;font-weight:700;"></h4>
                </div>
                <div id="dcFechaHora" style="font-size:12px;opacity:.85;display:flex;align-items:center;gap:6px;">
                    <i class="fa-regular fa-clock"></i> <span></span>
                </div>
            </div>

            <!-- Body -->
            <div style="padding:0 24px 16px;max-height:62vh;overflow-y:auto;">

                <!-- Equipo / Orden -->
                <div id="dcOrdenSection" style="display:flex;gap:14px;align-items:flex-start;padding:16px 0;border-bottom:1px solid #f1f5f9;">
                    <div id="dcFotoWrap" style="width:80px;height:80px;border-radius:10px;overflow:hidden;flex-shrink:0;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                        <img id="dcFoto" src="" style="width:100%;height:100%;object-fit:cover;display:none;">
                        <i class="fa-solid fa-image" id="dcFotoPlaceholder" style="font-size:24px;color:#cbd5e1;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            <span id="dcOrdenNum" style="font-size:14px;font-weight:700;color:#1e293b;"></span>
                            <span id="dcEstadoBadge" style="font-size:10px;font-weight:600;padding:3px 10px;border-radius:20px;"></span>
                        </div>
                        <div id="dcEquipo" style="font-size:13px;color:#475569;margin-top:4px;line-height:1.4;"></div>
                        <div id="dcMarcaModelo" style="font-size:11px;color:#94a3b8;margin-top:2px;"></div>
                    </div>
                </div>

                <!-- Notas -->
                <div id="dcDescSection" style="padding:12px 0;border-bottom:1px solid #f1f5f9;display:none;">
                    <div class="egs-dc-section-label"><i class="fa-regular fa-comment-dots" style="margin-right:4px;"></i>Notas</div>
                    <p id="dcDescripcion" style="margin:0;font-size:13px;color:#334155;line-height:1.5;"></p>
                </div>

                <!-- Cliente -->
                <div id="dcClienteSection" style="padding:14px 0;border-bottom:1px solid #f1f5f9;">
                    <div class="egs-dc-section-label"><i class="fa-solid fa-user" style="margin-right:4px;"></i>Cliente</div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div id="dcClienteAvatar" style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:800;flex-shrink:0;"></div>
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                <span id="dcClienteNombre" style="font-size:14px;font-weight:700;color:#1e293b;"></span>
                                <span id="dcClienteBadges"></span>
                            </div>
                            <div id="dcClienteTel" style="font-size:12px;color:#64748b;margin-top:2px;display:flex;align-items:center;gap:5px;">
                                <i class="fa-brands fa-whatsapp" style="color:#22c55e;"></i><span></span>
                            </div>
                        </div>
                    </div>
                    <!-- Mini stats -->
                    <div id="dcClienteStats" style="display:none;margin-top:10px;display:grid;grid-template-columns:repeat(3,1fr);gap:8px;"></div>
                </div>

                <!-- Técnico y Asesor -->
                <div style="padding:14px 0;border-bottom:1px solid #f1f5f9;display:flex;gap:20px;" id="dcEquipoSection">
                    <div id="dcTecnicoBlock" style="flex:1;display:none;">
                        <div class="egs-dc-section-label"><i class="fa-solid fa-wrench" style="margin-right:4px;"></i>Técnico</div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="egs-dc-avatar-sm" id="dcTecnicoAvatar">
                                <img id="dcTecnicoFoto" src="" style="width:100%;height:100%;object-fit:cover;display:none;">
                                <i class="fa-solid fa-wrench" id="dcTecnicoIcon" style="font-size:12px;color:#94a3b8;"></i>
                            </div>
                            <span id="dcTecnicoNombre" style="font-size:13px;font-weight:600;color:#334155;"></span>
                        </div>
                    </div>
                    <div id="dcAsesorBlock" style="flex:1;display:none;">
                        <div class="egs-dc-section-label"><i class="fa-solid fa-headset" style="margin-right:4px;"></i>Asesor</div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="egs-dc-avatar-sm" id="dcAsesorAvatar">
                                <img id="dcAsesorFoto" src="" style="width:100%;height:100%;object-fit:cover;display:none;">
                                <i class="fa-solid fa-headset" id="dcAsesorIcon" style="font-size:12px;color:#94a3b8;"></i>
                            </div>
                            <span id="dcAsesorNombre" style="font-size:13px;font-weight:600;color:#334155;"></span>
                        </div>
                    </div>
                </div>

                <!-- Info extra -->
                <div id="dcInfoExtra" style="padding:14px 0;display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div id="dcTotalBlock" class="egs-dc-stat-box" style="display:none;">
                        <div class="egs-dc-stat-label">Total</div>
                        <div id="dcTotal" class="egs-dc-stat-val"></div>
                    </div>
                    <div id="dcFechaIngresoBlock" class="egs-dc-stat-box" style="display:none;">
                        <div class="egs-dc-stat-label">Ingreso</div>
                        <div id="dcFechaIngreso" class="egs-dc-stat-val" style="font-size:13px;"></div>
                    </div>
                </div>

            </div>

            <!-- Footer acciones -->
            <div style="padding:14px 24px 18px;display:flex;gap:8px;flex-wrap:wrap;border-top:1px solid #f1f5f9;background:#f8fafc;">
                <a id="dcBtnOrden" href="#" class="egs-dc-action-btn" style="background:#eef2ff;color:#4f46e5;border-color:#c7d2fe;">
                    <i class="fa-solid fa-eye"></i> Ver Orden
                </a>
                <a id="dcBtnGCal" href="#" target="_blank" class="egs-dc-action-btn" style="background:#eff6ff;color:#2563eb;border-color:#bfdbfe;">
                    <i class="fa-brands fa-google"></i> Calendar
                </a>
                <a id="dcBtnWhatsApp" href="#" target="_blank" class="egs-dc-action-btn" style="background:#f0fdf4;color:#16a34a;border-color:#bbf7d0;">
                    <i class="fa-brands fa-whatsapp"></i> WhatsApp
                </a>
                <button type="button" id="dcBtnEliminar" class="egs-dc-action-btn" style="background:#fff;color:#ef4444;border-color:#fecaca;">
                    <i class="fa-solid fa-trash-can"></i> Eliminar
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ═══ CSS ═══ -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<style>
    /* ═══ Form helpers ═══ */
    .egs-pc-lbl { font-size:12px;font-weight:600;color:#475569;margin-bottom:6px;display:block; }
    .egs-pc-input { border-radius:8px !important;border:1.5px solid #e2e8f0 !important;padding:10px 12px !important;font-size:13px !important;transition:border-color .2s !important; }
    .egs-pc-input:focus, #modalAgregarCita .form-control:focus {
        border-color:#6366f1 !important;box-shadow:0 0 0 3px rgba(99,102,241,.12) !important;outline:none !important;
    }

    /* ═══ Quick Date Chips ═══ */
    .egs-pc-quick-dates { display:flex;flex-wrap:wrap;gap:6px; }
    .egs-pc-qd-chip {
        padding:6px 12px;border-radius:20px;border:1.5px solid #e2e8f0;background:#fff;color:#475569;
        font-size:12px;font-weight:500;cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:5px;
    }
    .egs-pc-qd-chip:hover { border-color:#6366f1;color:#6366f1;background:#f5f3ff; }
    .egs-pc-qd-chip.active { background:#6366f1;color:#fff;border-color:#6366f1; }
    .egs-pc-qd-chip i { font-size:11px; }
    .egs-pc-qd-custom { border-style:dashed; }

    /* ═══ Time Grid ═══ */
    .egs-pc-time-grid { display:grid;grid-template-columns:repeat(6,1fr);gap:5px; }
    .egs-pc-time-chip {
        padding:7px 4px;border-radius:8px;border:1.5px solid #e2e8f0;background:#fff;color:#475569;
        font-size:12px;font-weight:500;cursor:pointer;text-align:center;transition:all .15s;
    }
    .egs-pc-time-chip:hover { border-color:#6366f1;color:#6366f1; }
    .egs-pc-time-chip.active { background:#6366f1;color:#fff;border-color:#6366f1; }

    /* ═══ Detail modal ═══ */
    .egs-dc-section-label { font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px; }
    .egs-dc-avatar-sm { width:32px;height:32px;border-radius:50%;overflow:hidden;background:#f1f5f9;flex-shrink:0;display:flex;align-items:center;justify-content:center; }
    .egs-dc-stat-box { padding:10px;background:#f8fafc;border-radius:8px;text-align:center; }
    .egs-dc-stat-label { font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase; }
    .egs-dc-stat-val { font-size:16px;font-weight:800;color:#1e293b;margin-top:2px; }
    .egs-dc-action-btn {
        border-radius:8px;padding:8px 14px;font-size:12px;font-weight:600;border:1px solid;
        flex:1;text-align:center;transition:all .15s;cursor:pointer;text-decoration:none !important;
        display:inline-flex;align-items:center;justify-content:center;gap:4px;
    }
    .egs-dc-action-btn:hover { opacity:.85; }
    .egs-dc-badge-icon {
        display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;margin-left:2px;
    }
    .egs-dc-badge-icon i { font-size:10px; }
    .egs-dc-mini-stats { display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:10px; }
    .egs-dc-mini-stat { padding:8px;background:#f8fafc;border-radius:8px;text-align:center; }
    .egs-dc-mini-stat-val { font-size:14px;font-weight:800; }
    .egs-dc-mini-stat-lbl { font-size:10px;color:#94a3b8;margin-top:1px; }

    /* ═══ FullCalendar Overrides ═══ */
    .fc .fc-toolbar { margin-bottom:16px !important;flex-wrap:wrap;gap:10px; }
    .fc .fc-toolbar-title { font-size:18px !important;font-weight:700 !important;color:#1e293b !important;text-transform:capitalize; }
    .fc .fc-button {
        background:#fff !important;color:#475569 !important;border:1.5px solid #e2e8f0 !important;
        border-radius:8px !important;padding:6px 14px !important;font-size:12px !important;
        font-weight:600 !important;text-transform:capitalize !important;box-shadow:none !important;transition:all .15s !important;
    }
    .fc .fc-button:hover { background:#f8fafc !important;border-color:#6366f1 !important;color:#6366f1 !important; }
    .fc .fc-button-active, .fc .fc-button:active { background:linear-gradient(135deg,#6366f1,#8b5cf6) !important;color:#fff !important;border-color:#6366f1 !important; }
    .fc .fc-button-group > .fc-button { border-radius:0 !important; }
    .fc .fc-button-group > .fc-button:first-child { border-radius:8px 0 0 8px !important; }
    .fc .fc-button-group > .fc-button:last-child { border-radius:0 8px 8px 0 !important; }
    .fc .fc-today-button { border-radius:8px !important; }
    .fc .fc-button:disabled { opacity:.5 !important; }
    .fc .fc-col-header-cell { background:#f8fafc !important;border-color:#e2e8f0 !important;padding:10px 0 !important; }
    .fc .fc-col-header-cell-cushion { font-size:12px !important;font-weight:700 !important;color:#475569 !important;text-transform:uppercase !important;letter-spacing:.04em !important;text-decoration:none !important; }
    .fc .fc-daygrid-day { border-color:#f1f5f9 !important;transition:background .12s; }
    .fc .fc-daygrid-day:hover { background:#faf5ff !important; }
    .fc .fc-daygrid-day-number { font-size:13px !important;font-weight:600 !important;color:#64748b !important;padding:8px 10px !important;text-decoration:none !important; }
    .fc .fc-day-today { background:rgba(99,102,241,.06) !important; }
    .fc .fc-day-today .fc-daygrid-day-number {
        background:#6366f1 !important;color:#fff !important;border-radius:50% !important;
        width:28px !important;height:28px !important;display:inline-flex !important;align-items:center !important;
        justify-content:center !important;padding:0 !important;margin:4px 6px !important;
    }
    .fc .fc-event, .fc .fc-daygrid-event {
        border-radius:6px !important;border:none !important;padding:3px 8px !important;
        font-size:12px !important;font-weight:600 !important;cursor:pointer !important;
        transition:transform .12s,box-shadow .12s !important;box-shadow:0 1px 3px rgba(0,0,0,.1) !important;
    }
    .fc .fc-event:hover { transform:translateY(-1px) !important;box-shadow:0 4px 12px rgba(0,0,0,.15) !important; }
    .fc .fc-daygrid-event-dot { border-color:currentColor !important; }
    .fc .fc-event-title { font-weight:600 !important; }
    .fc .fc-event-time { font-weight:500 !important;opacity:.85; }
    .fc .fc-list { border-radius:10px !important;overflow:hidden !important;border-color:#e2e8f0 !important; }
    .fc .fc-list-day-cushion { background:#f8fafc !important;padding:10px 16px !important; }
    .fc .fc-list-day-text, .fc .fc-list-day-side-text { font-size:13px !important;font-weight:700 !important;color:#1e293b !important;text-decoration:none !important; }
    .fc .fc-list-event:hover td { background:#faf5ff !important; }
    .fc .fc-list-event-dot { border-color:currentColor !important; }
    .fc { --fc-border-color:#e2e8f0;--fc-today-bg-color:rgba(99,102,241,.06); }

    @media (max-width:767px) {
        .fc .fc-toolbar { flex-direction:column;align-items:stretch; }
        .fc .fc-toolbar-chunk { display:flex;justify-content:center; }
        .fc .fc-toolbar-title { text-align:center;font-size:16px !important; }
        .egs-pc-time-grid { grid-template-columns:repeat(4,1fr); }
    }
</style>

<!-- ═══ JS ═══ -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>
<script src="vistas/js/calendario.js?v=<?php echo time(); ?>"></script>
