document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    // Initialize Select2 if present
    if ($.fn.select2) $('.select2').select2();

    /* ═══════════════════════════════════════════
       Date / Time helpers
       ═══════════════════════════════════════════ */
    var diasEs = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
    var mesesEs = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

    // Horarios laborales
    var horasLV  = ['10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','16:00','16:30','17:00','17:30','18:00','18:30'];
    var horasSab = ['09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30'];

    var pcSelectedFecha = 'hoy';
    var pcSelectedHora  = '10:00';

    function getNextDayOfWeek(dayIndex) {
        var d = new Date();
        var diff = dayIndex - d.getDay();
        if (diff <= 0) diff += 7;
        d.setDate(d.getDate() + diff);
        return d;
    }

    function calcFecha(tipo) {
        var d = new Date();
        switch (tipo) {
            case 'hoy':       return d;
            case 'manana':    d.setDate(d.getDate() + 1); return d;
            case 'lunes':     return getNextDayOfWeek(1);
            case 'martes':    return getNextDayOfWeek(2);
            case 'miercoles': return getNextDayOfWeek(3);
            case 'jueves':    return getNextDayOfWeek(4);
            case 'viernes':   return getNextDayOfWeek(5);
            case 'sabado':    return getNextDayOfWeek(6);
            default:          return d;
        }
    }

    function esSabado(dow)  { return dow === 6; }
    function esDomingo(dow) { return dow === 0; }
    function getHorasParaDia(dow) {
        if (esSabado(dow))  return horasSab;
        if (esDomingo(dow)) return [];
        return horasLV;
    }

    function pcRenderTimeGrid() {
        var d = pcSelectedFecha === 'personalizado'
            ? (function(){ var v = $('#pcFechaCustom').val(); return v ? new Date(v.replace(/-/g,'/')) : new Date(); })()
            : calcFecha(pcSelectedFecha);

        var dow = d.getDay();
        var horas = getHorasParaDia(dow);
        var $grid = $('#pcTimeGrid');
        var $info = $('#pcHorarioTexto');

        if (esDomingo(dow)) {
            $grid.html('<div style="text-align:center;padding:12px;color:#ef4444;font-size:12px;"><i class="fa-solid fa-ban" style="margin-right:5px;"></i>Domingo no disponible — elige otro día</div>');
            $info.text('Domingos no se atiende');
            $('#pcHorarioInfo').css({'background':'#fef2f2','border-color':'#fecaca','color':'#991b1b'});
            pcSelectedHora = '';
            return;
        }

        $info.text(esSabado(dow) ? 'Sábado: 9:00 – 14:30' : 'L-V: 10:00–14:00 y 16:00–18:30');
        $('#pcHorarioInfo').css({'background':'#fffbeb','border-color':'#fde68a','color':'#92400e'});

        // Filtrar horas pasadas si es hoy
        var esHoy = false;
        var ahora = new Date();
        var hoyDate = new Date(); hoyDate.setHours(0,0,0,0);
        var fechaSel = new Date(d); fechaSel.setHours(0,0,0,0);
        if (fechaSel.getTime() === hoyDate.getTime()) esHoy = true;

        var horaActual = ahora.getHours().toString().padStart(2,'0') + ':' + ahora.getMinutes().toString().padStart(2,'0');
        var horasFiltradas = esHoy ? horas.filter(function(h){ return h > horaActual; }) : horas;

        if (esHoy && horasFiltradas.length === 0) {
            $grid.html('<div style="text-align:center;padding:12px;color:#d97706;font-size:12px;"><i class="fa-solid fa-clock" style="margin-right:5px;"></i>Ya no hay horarios disponibles hoy — elige otro día</div>');
            pcSelectedHora = '';
            return;
        }

        var html = '';
        var found = false;
        horasFiltradas.forEach(function(h) {
            var isActive = (h === pcSelectedHora);
            if (isActive) found = true;
            html += '<button type="button" class="egs-pc-time-chip' + (isActive ? ' active' : '') + '" data-hora="' + h + '">' + h + '</button>';
        });
        $grid.html(html);

        if (!found && horasFiltradas.length) {
            pcSelectedHora = horasFiltradas[0];
            $grid.find('.egs-pc-time-chip').first().addClass('active');
        }
    }

    function pcUpdateDatePreview() {
        var d;
        if (pcSelectedFecha === 'personalizado') {
            var val = $('#pcFechaCustom').val();
            d = val ? new Date(val.replace(/-/g,'/')) : new Date();
        } else {
            d = calcFecha(pcSelectedFecha);
        }
        var txt = diasEs[d.getDay()] + ' ' + d.getDate() + ' de ' + mesesEs[d.getMonth()];
        if (pcSelectedHora) txt += ' — ' + pcSelectedHora;
        $('#pcDatePreviewText').text(txt);
    }

    function pcBuildFechaFinal() {
        var d;
        if (pcSelectedFecha === 'personalizado') {
            var val = $('#pcFechaCustom').val();
            if (!val) return null;
            d = new Date(val.replace(/-/g,'/'));
        } else {
            d = calcFecha(pcSelectedFecha);
        }
        if (esDomingo(d.getDay()) || !pcSelectedHora) return null;

        var yyyy = d.getFullYear();
        var mm = (d.getMonth() + 1).toString().padStart(2,'0');
        var dd = d.getDate().toString().padStart(2,'0');
        return yyyy + '-' + mm + '-' + dd + 'T' + pcSelectedHora;
    }

    // Initialize time grid
    pcRenderTimeGrid();
    pcUpdateDatePreview();

    // Date chip click
    $(document).on('click', '.egs-pc-qd-chip', function() {
        var fecha = $(this).data('fecha');
        if (!fecha) return;
        $('.egs-pc-qd-chip').removeClass('active');
        $(this).addClass('active');
        pcSelectedFecha = fecha;
        if (fecha === 'personalizado') {
            $('#pcCustomDateWrap').slideDown(150);
        } else {
            $('#pcCustomDateWrap').slideUp(150);
        }
        pcRenderTimeGrid();
        pcUpdateDatePreview();
    });

    $('#pcFechaCustom').on('change', function() {
        pcRenderTimeGrid();
        pcUpdateDatePreview();
    });

    // Time chip click
    $(document).on('click', '.egs-pc-time-chip', function() {
        var hora = $(this).data('hora');
        if (!hora) return;
        $('.egs-pc-time-chip').removeClass('active');
        $(this).addClass('active');
        pcSelectedHora = hora;
        pcUpdateDatePreview();
    });

    /* ═══════════════════════════════════════════
       FullCalendar Init
       ═══════════════════════════════════════════ */
    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
        },
        initialView: 'dayGridMonth',
        locale: 'es',
        navLinks: true,
        businessHours: true,
        editable: true,
        selectable: true,

        eventDisplay: 'block',

        events: {
            url: 'ajax/citas.ajax.php',
            method: 'POST',
            extraParams: { accion: 'mostrar' },
            failure: function () { console.log('Error al cargar eventos'); }
        },

        eventDidMount: function(info) {
            var color = info.event.backgroundColor || info.event.borderColor || '';
            if (color) {
                info.el.style.setProperty('background-color', color, 'important');
                info.el.style.setProperty('border-color', color, 'important');
            }
        },

        dateClick: function (info) {
            document.getElementById('formularioCita').reset();
            // Set date from clicked cell — find matching chip or use custom
            var clickedDate = new Date(info.dateStr.replace(/-/g,'/'));
            var hoy = new Date(); hoy.setHours(0,0,0,0);
            var diff = Math.round((clickedDate - hoy) / 86400000);

            var chipMap = { 0: 'hoy', 1: 'manana' };
            var dowMap = { 1: 'lunes', 2: 'martes', 3: 'miercoles', 4: 'jueves', 5: 'viernes', 6: 'sabado' };

            if (chipMap[diff] !== undefined) {
                pcSelectedFecha = chipMap[diff];
            } else if (diff > 1 && diff <= 7 && dowMap[clickedDate.getDay()]) {
                pcSelectedFecha = dowMap[clickedDate.getDay()];
            } else {
                pcSelectedFecha = 'personalizado';
                var yy = clickedDate.getFullYear();
                var mmm = (clickedDate.getMonth()+1).toString().padStart(2,'0');
                var ddd = clickedDate.getDate().toString().padStart(2,'0');
                $('#pcFechaCustom').val(yy + '-' + mmm + '-' + ddd);
                $('#pcCustomDateWrap').show();
            }

            // Update chips visual
            $('.egs-pc-qd-chip').removeClass('active');
            $('.egs-pc-qd-chip[data-fecha="' + pcSelectedFecha + '"]').addClass('active');
            if (pcSelectedFecha !== 'personalizado') $('#pcCustomDateWrap').hide();

            pcRenderTimeGrid();
            pcUpdateDatePreview();

            $('#modalAgregarCita').modal('show');
        },

        // ═══ Event Click → Rich Detail Modal ═══
        eventClick: function (info) {
            var ev = info.event;
            var props = ev.extendedProps;
            var evColor = ev.backgroundColor || '#6366f1';

            // Basic data
            var idOrden = props.id_orden;
            var hasOrden = idOrden && parseInt(idOrden) > 0;
            var portada = hasOrden ? (props.orden_portada || '') : '';
            // Si hay multimedia con más de 1 imagen, usar la segunda (la primera es portada)
            var heroImg = portada;
            if (hasOrden && props.orden_multimedia) {
                try {
                    var mmArr = typeof props.orden_multimedia === 'string' ? JSON.parse(props.orden_multimedia) : props.orden_multimedia;
                    if (Array.isArray(mmArr) && mmArr.length > 1 && mmArr[1].foto) {
                        heroImg = mmArr[1].foto;
                    }
                } catch(e) {}
            }
            var hasHero = hasOrden && portada;

            // Fecha string
            var fechaStr = '';
            if (ev.start) {
                fechaStr = diasEs[ev.start.getDay()] + ' ' + ev.start.getDate() + ' de ' + mesesEs[ev.start.getMonth()] + ', ' +
                    ev.start.getHours().toString().padStart(2,'0') + ':' + ev.start.getMinutes().toString().padStart(2,'0');
            }

            // Estado badge helper
            var estado = hasOrden ? (props.orden_estado || '') : '';
            function renderEstadoBadge($el) {
                if (!estado) { $el.hide(); return; }
                var estadoColors = {
                    'Ent': { bg: '#f0fdf4', color: '#16a34a', border: '#bbf7d0' },
                    'ok':  { bg: '#eff6ff', color: '#2563eb', border: '#bfdbfe' },
                    'ter': { bg: '#f0fdf4', color: '#16a34a', border: '#bbf7d0' },
                    'REV': { bg: '#fffbeb', color: '#d97706', border: '#fde68a' },
                    'AUT': { bg: '#fef2f2', color: '#dc2626', border: '#fecaca' },
                    'SUP': { bg: '#faf5ff', color: '#8b5cf6', border: '#e9d5ff' }
                };
                var ec = null;
                Object.keys(estadoColors).forEach(function(k) { if (estado.indexOf(k) !== -1) ec = estadoColors[k]; });
                if (!ec) ec = { bg: '#f1f5f9', color: '#64748b', border: '#e2e8f0' };
                $el.text(estado).css({ background: ec.bg, color: ec.color, border: '1px solid ' + ec.border }).show();
            }

            // ═══ HERO MODE (order + portada) ═══
            if (hasHero) {
                $('#dcHero').show();
                $('#dcHeroImg').attr('src', heroImg);
                $('#dcHeroOrdenNum').text('Orden #' + idOrden);
                $('#dcHeroTitle').text(ev.title || 'Sin título');
                $('#dcHeroFechaHora span').text(fechaStr);
                renderEstadoBadge($('#dcHeroEstadoBadge'));
                $('#dcHeader').hide();
            } else {
                $('#dcHero').hide();
                $('#dcHeader').css('background', 'linear-gradient(135deg, ' + evColor + ', ' + adjustColor(evColor, -20) + ')').show();
                $('#dcTitle').text(ev.title || 'Sin título');
                $('#dcFechaHora span').text(fechaStr);
            }

            // ═══ ORDEN COMPACT (order pero sin portada) ═══
            if (hasOrden && !hasHero) {
                $('#dcOrdenNum').text('Orden #' + idOrden);
                var equipo = props.equipo || '';
                $('#dcEquipo').text(equipo || 'Sin descripción del equipo');
                renderEstadoBadge($('#dcEstadoBadge'));
                $('#dcOrdenCompact').show();
            } else if (hasHero) {
                // En hero, mostrar equipo en sección compacta también
                var equipo = props.equipo || '';
                if (equipo) {
                    $('#dcOrdenNum').text('');
                    $('#dcEquipo').text(equipo);
                    $('#dcEstadoBadge').hide();
                    $('#dcOrdenCompact').show();
                } else {
                    $('#dcOrdenCompact').hide();
                }
            } else {
                $('#dcOrdenCompact').hide();
            }

            // ═══ EQUIPO DETALLE (marca, modelo) ═══
            var marca = hasOrden ? (props.orden_marca || '') : '';
            var modelo = hasOrden ? (props.orden_modelo || '') : '';
            if (marca || modelo) {
                if (marca) { $('#dcMarca').text(marca); $('#dcMarcaBlock').show(); } else { $('#dcMarcaBlock').hide(); }
                if (modelo) { $('#dcModelo').text(modelo); $('#dcModeloBlock').show(); } else { $('#dcModeloBlock').hide(); }
                $('#dcEquipoDetalle').show();
            } else {
                $('#dcEquipoDetalle').hide();
            }

            // ═══ DESCRIPCIÓN ═══
            var desc = props.description || '';
            if (desc) {
                $('#dcDescripcion').text(desc);
                $('#dcDescSection').show();
            } else {
                $('#dcDescSection').hide();
            }

            // ═══ CLIENTE ═══
            var clienteNombre = props.cliente_nombre || '';
            if (hasOrden) {
                // Siempre mostrar sección de cliente cuando hay orden
                $('#dcClienteSection').show();

                if (clienteNombre) {
                    var initials = clienteNombre.split(' ').map(function(w){ return w.charAt(0).toUpperCase(); }).slice(0,2).join('');
                    $('#dcClienteAvatar').text(initials).css({'background':'linear-gradient(135deg,#6366f1,#8b5cf6)','font-size':'17px'});
                    $('#dcClienteNombre').text(clienteNombre);

                    // Teléfono
                    var tel = props.cliente_telefono || '';
                    if (tel) {
                        $('#dcClienteTel span').text(tel);
                        $('#dcClienteTel').show();
                        var waNum = tel.replace(/\D/g,'');
                        if (waNum.length === 10) waNum = '52' + waNum;
                        $('#dcBtnWhatsApp').attr('href', 'https://wa.me/' + waNum).show();
                    } else {
                        $('#dcClienteTel').hide();
                        $('#dcBtnWhatsApp').hide();
                    }

                    // Badges & Calificación
                    var badges = props.cliente_badges;
                    if (badges) {
                        // ── Cliente nuevo: solo mostrar badge, no calificación ──
                        if (badges.es_nuevo) {
                            $('#dcClienteCalifCard').hide();
                            $('#dcClienteBadges').html(buildBadge('fa-seedling', '#f5f3ff', '#8b5cf6', 'Cliente nuevo') +
                                '<span style="font-size:11px;font-weight:600;color:#8b5cf6;margin-left:4px;">Cliente nuevo</span>');
                            $('#dcClienteBadgesWrap').show();
                            $('#dcClienteStats').hide();
                        } else {
                            $('#dcClienteBadgesWrap').hide();

                            // ── Calificación de entrega ──
                            var ce = badges.calif_entrega;
                            if (ce !== null && ce !== undefined) {
                                var califColor, califLabel, califBg, califBorder, starsCount;
                                if (ce >= 90)      { califColor = '#16a34a'; califBg = '#f0fdf4'; califBorder = '#bbf7d0'; califLabel = 'Excelente'; starsCount = 5; }
                                else if (ce >= 70)  { califColor = '#2563eb'; califBg = '#eff6ff'; califBorder = '#bfdbfe'; califLabel = 'Bueno'; starsCount = 4; }
                                else if (ce >= 50)  { califColor = '#d97706'; califBg = '#fffbeb'; califBorder = '#fde68a'; califLabel = 'Regular'; starsCount = 3; }
                                else                { califColor = '#dc2626'; califBg = '#fef2f2'; califBorder = '#fecaca'; califLabel = 'Bajo'; starsCount = 2; }

                                $('#dcCalifCircle').css({ background: califBg, 'border-color': califColor });
                                $('#dcCalifNum').text(Math.round(ce) + '%').css('color', califColor);
                                $('#dcCalifLabel').text(califLabel).css('color', califColor);

                                // Estrellas
                                var starsHtml = '';
                                for (var si = 0; si < 5; si++) {
                                    starsHtml += '<i class="fa-' + (si < starsCount ? 'solid' : 'regular') + ' fa-star" style="font-size:11px;color:' + (si < starsCount ? califColor : '#cbd5e1') + ';"></i>';
                                }
                                $('#dcCalifStars').html(starsHtml);

                                // Indicador de entrega
                                $('#dcEntregaIcon').css('color', califColor);
                                $('#dcEntregaVal').text(Math.round(ce) + '%').css('color', califColor);
                                $('#dcIndicadorEntrega').show();

                                $('#dcCalifRow').show();
                            } else {
                                $('#dcCalifRow').hide();
                                $('#dcIndicadorEntrega').hide();
                            }

                            // ── Tiempo de recogida ──
                            var ar = badges.avg_recogida;
                            if (ar !== null && ar !== undefined) {
                                var recColor, recLabel;
                                if (ar <= 7)       { recColor = '#16a34a'; recLabel = '~' + Math.round(ar) + ' días'; }
                                else if (ar <= 14)  { recColor = '#2563eb'; recLabel = '~' + Math.round(ar) + ' días'; }
                                else if (ar <= 30)  { recColor = '#d97706'; recLabel = '~' + Math.round(ar) + ' días'; }
                                else                { recColor = '#dc2626'; recLabel = '~' + Math.round(ar) + ' días'; }

                                $('#dcRecogidaIcon').css('color', recColor);
                                $('#dcRecogidaVal').text(recLabel).css('color', recColor);
                                $('#dcIndicadorRecogida').show();
                            } else {
                                $('#dcIndicadorRecogida').hide();
                            }

                            // ── Alerta de cancelación ──
                            var pc = badges.prob_cancelacion;
                            if (pc !== null && pc !== undefined && pc > 30) {
                                var alertColor, alertBg, alertBorder;
                                if (pc >= 60)      { alertColor = '#dc2626'; alertBg = '#fef2f2'; alertBorder = '#fecaca'; }
                                else               { alertColor = '#d97706'; alertBg = '#fffbeb'; alertBorder = '#fde68a'; }
                                $('#dcAlertaCancelacion').css({ background: alertBg, color: alertColor, border: '1px solid ' + alertBorder, display: 'flex' });
                                $('#dcAlertaCancelacionTxt').text('Prob. cancelación: ' + Math.round(pc) + '%');
                            } else {
                                $('#dcAlertaCancelacion').hide();
                            }

                            $('#dcClienteCalifCard').show();

                            // ── Stats grid (4 columnas) ──
                            var statsHtml = '<div class="egs-dc-mini-stats" style="grid-template-columns:repeat(4,1fr);">';
                            statsHtml += '<div class="egs-dc-mini-stat"><div class="egs-dc-mini-stat-val" style="color:#1e293b;">' + (badges.total_ordenes || 0) + '</div><div class="egs-dc-mini-stat-lbl">Órdenes</div></div>';
                            statsHtml += '<div class="egs-dc-mini-stat"><div class="egs-dc-mini-stat-val" style="color:#16a34a;">' + (badges.entregadas || 0) + '</div><div class="egs-dc-mini-stat-lbl">Entregadas</div></div>';
                            statsHtml += '<div class="egs-dc-mini-stat"><div class="egs-dc-mini-stat-val" style="color:#2563eb;">' + (badges.resueltas || 0) + '</div><div class="egs-dc-mini-stat-lbl">Resueltas</div></div>';
                            statsHtml += '<div class="egs-dc-mini-stat"><div class="egs-dc-mini-stat-val" style="color:#ef4444;">' + (badges.canceladas || 0) + '</div><div class="egs-dc-mini-stat-lbl">Canceladas</div></div>';
                            statsHtml += '</div>';
                            $('#dcClienteStats').html(statsHtml).show();
                        }
                    } else {
                        $('#dcClienteCalifCard').hide();
                        $('#dcClienteBadgesWrap').hide();
                        $('#dcClienteStats').hide();
                    }
                } else {
                    // Orden sin cliente asignado
                    $('#dcClienteAvatar').html('<i class="fa-solid fa-user-slash" style="font-size:16px;"></i>').css({'background':'#f1f5f9','font-size':'16px','color':'#94a3b8'});
                    $('#dcClienteNombre').text('Sin cliente asignado');
                    $('#dcClienteTel').hide();
                    $('#dcBtnWhatsApp').hide();
                    $('#dcClienteCalifCard').hide();
                    $('#dcClienteBadgesWrap').hide();
                    $('#dcClienteStats').hide();
                }
            } else {
                $('#dcClienteSection').hide();
                $('#dcBtnWhatsApp').hide();
            }

            // ═══ TÉCNICO / ASESOR ═══
            var tecNombre = props.tecnico_nombre || '';
            if (tecNombre) {
                $('#dcTecnicoNombre').text(tecNombre);
                var tecFoto = props.tecnico_foto;
                if (tecFoto) { $('#dcTecnicoFoto').attr('src', tecFoto).show(); $('#dcTecnicoIcon').hide(); }
                else { $('#dcTecnicoFoto').hide(); $('#dcTecnicoIcon').show(); }
                $('#dcTecnicoBlock').show();
            } else {
                $('#dcTecnicoBlock').hide();
            }

            var aseNombre = props.asesor_nombre || '';
            if (aseNombre) {
                $('#dcAsesorNombre').text(aseNombre);
                var aseFoto = props.asesor_foto;
                if (aseFoto) { $('#dcAsesorFoto').attr('src', aseFoto).show(); $('#dcAsesorIcon').hide(); }
                else { $('#dcAsesorFoto').hide(); $('#dcAsesorIcon').show(); }
                $('#dcAsesorBlock').show();
            } else {
                $('#dcAsesorBlock').hide();
            }

            if (!tecNombre && !aseNombre) { $('#dcEquipoSection').hide(); } else { $('#dcEquipoSection').show(); }

            // ═══ INFO EXTRA (solo si tiene orden) ═══
            if (hasOrden) {
                var total = props.orden_total;
                if (total && parseFloat(total) > 0) {
                    $('#dcTotal').text('$' + parseFloat(total).toLocaleString('es-MX', {minimumFractionDigits:2}));
                    $('#dcTotalBlock').show();
                } else { $('#dcTotalBlock').hide(); }

                var fechaIng = props.orden_fecha_ingreso;
                if (fechaIng) {
                    var fi = new Date(fechaIng.replace(/-/g,'/'));
                    if (!isNaN(fi)) {
                        $('#dcFechaIngreso').text(fi.getDate() + ' ' + mesesEs[fi.getMonth()].substring(0,3) + ' ' + fi.getFullYear());
                    } else {
                        $('#dcFechaIngreso').text(fechaIng);
                    }
                    $('#dcFechaIngresoBlock').show();
                } else { $('#dcFechaIngresoBlock').hide(); }

                var showExtra = ($('#dcTotalBlock').is(':visible') || $('#dcFechaIngresoBlock').is(':visible'));
                $('#dcInfoExtra').css('display', showExtra ? 'grid' : 'none');
            } else {
                $('#dcInfoExtra').hide();
            }

            // ═══ OBSERVACIONES ═══
            $('#dcObservaciones').val(desc);
            $('#dcObsStatus').text('');
            var currentCitaId = ev.id;

            $('#dcBtnGuardarObs').off('click').on('click', function() {
                var newDesc = $('#dcObservaciones').val().trim();
                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

                $.ajax({
                    url: 'ajax/citas.ajax.php',
                    type: 'POST',
                    data: { accion: 'actualizarDescripcion', idCita: currentCitaId, descripcion: newDesc },
                    success: function(resp) {
                        if (resp === 'ok') {
                            ev.setExtendedProp('description', newDesc);
                            if (newDesc) { $('#dcDescripcion').text(newDesc); $('#dcDescSection').show(); }
                            else { $('#dcDescSection').hide(); }
                            $('#dcObsStatus').text('Guardado ✓').css('color', '#16a34a');
                        } else {
                            $('#dcObsStatus').text('Error al guardar').css('color', '#ef4444');
                        }
                    },
                    error: function() {
                        $('#dcObsStatus').text('Error de conexión').css('color', '#ef4444');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk" style="margin-right:4px;"></i>Guardar');
                        setTimeout(function() { $('#dcObsStatus').text(''); }, 3000);
                    }
                });
            });

            // ═══ VER ORDEN BUTTON ═══
            if (hasOrden) {
                $('#dcBtnOrden').attr('href', 'index.php?ruta=infoOrden&idOrden=' + idOrden).show();
            } else {
                $('#dcBtnOrden').hide();
            }

            // ═══ GOOGLE CALENDAR ═══
            if (ev.start) {
                var startDate = ev.start;
                var endDate = ev.end || new Date(startDate.getTime() + 60 * 60 * 1000);
                var detalles = [];
                if (hasOrden) detalles.push('Orden #' + idOrden);
                if (clienteNombre) detalles.push('Cliente: ' + clienteNombre);
                if (props.equipo) detalles.push('Equipo: ' + props.equipo);
                if (desc) detalles.push('\nNotas: ' + desc);

                var gcalUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE' +
                    '&text=' + encodeURIComponent(ev.title) +
                    '&dates=' + formatGCalDate(startDate) + '/' + formatGCalDate(endDate) +
                    (detalles.length ? '&details=' + encodeURIComponent(detalles.join('\n')) : '');
                $('#dcBtnGCal').attr('href', gcalUrl).show();
            } else {
                $('#dcBtnGCal').hide();
            }

            // ═══ ELIMINAR ═══
            $('#dcBtnEliminar').off('click').on('click', function() {
                swal({
                    title: '¿Estás seguro?',
                    text: 'Se eliminará la cita "' + ev.title + '"',
                    icon: 'warning',
                    buttons: ['Cancelar', 'Sí, eliminar'],
                    dangerMode: true
                }).then(function(confirmar) {
                    if (confirmar) {
                        var datos = new FormData();
                        datos.append("idCita", ev.id);
                        $.ajax({
                            url: "ajax/citas.ajax.php",
                            method: "POST",
                            data: datos,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function(resp) {
                                if (resp == "ok") {
                                    ev.remove();
                                    $('#modalDetalleCita').modal('hide');
                                    swal('Eliminado', 'La cita ha sido eliminada.', 'success');
                                } else {
                                    swal('Error', 'No se pudo eliminar la cita', 'error');
                                }
                            }
                        });
                    }
                });
            });

            // Open modal
            $('#modalDetalleCita').modal('show');
        }
    });

    calendar.render();

    /* ═══════════════════════════════════════════
       Helper Functions
       ═══════════════════════════════════════════ */
    function formatGCalDate(d) {
        return d.getFullYear().toString() +
            ('0' + (d.getMonth() + 1)).slice(-2) +
            ('0' + d.getDate()).slice(-2) + 'T' +
            ('0' + d.getHours()).slice(-2) +
            ('0' + d.getMinutes()).slice(-2) +
            ('0' + d.getSeconds()).slice(-2);
    }

    function adjustColor(hex, amount) {
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
        var r = Math.max(0, Math.min(255, parseInt(hex.substring(0,2), 16) + amount));
        var g = Math.max(0, Math.min(255, parseInt(hex.substring(2,4), 16) + amount));
        var b = Math.max(0, Math.min(255, parseInt(hex.substring(4,6), 16) + amount));
        return '#' + r.toString(16).padStart(2,'0') + g.toString(16).padStart(2,'0') + b.toString(16).padStart(2,'0');
    }

    function buildBadge(icon, bg, color, title) {
        return '<span class="egs-dc-badge-icon" style="background:' + bg + ';" title="' + title + '">' +
            '<i class="fas ' + icon + '" style="color:' + color + ';"></i></span>';
    }

    /* ═══════════════════════════════════════════
       Auto-color por orden
       ═══════════════════════════════════════════ */
    var pcColorReq = null;
    $(document).on('input change', '#idOrden', function() {
        var idOrden = $(this).val();
        if (pcColorReq && pcColorReq.readyState !== 4) pcColorReq.abort();

        if (!idOrden || idOrden < 1) {
            $('#colorCita').val('#3a87ad');
            $('#pcAutoColorDot').css('background', '#3a87ad');
            $('#pcAutoColorText').text('Sin orden — se usará color por defecto').css('color', '#64748b');
            return;
        }

        $('#pcAutoColorText').html('<i class="fa-solid fa-spinner fa-spin"></i> Consultando...');

        pcColorReq = $.ajax({
            url: 'ajax/citas.ajax.php',
            type: 'POST',
            data: { accion: 'colorPorOrden', idOrden: idOrden },
            dataType: 'json',
            global: false,
            success: function(resp) {
                if (resp && resp.ok) {
                    $('#colorCita').val(resp.color);
                    $('#pcAutoColorDot').css('background', resp.color);
                    var labels = { '#16a34a':'Excelente', '#2563eb':'Bueno', '#d97706':'Regular', '#dc2626':'Bajo', '#8b5cf6':'Cliente nuevo' };
                    var label = labels[resp.color] || 'General';
                    var detail = '';
                    if (resp.es_nuevo) { detail = 'Cliente nuevo (' + resp.total_ordenes + ' órdenes)'; }
                    else if (resp.calif !== null) {
                        detail = label + ' — Calif: ' + resp.calif + '%';
                        if (resp.avg_recogida) detail += ' · Recoge: ~' + resp.avg_recogida + ' días';
                    } else { detail = 'Sin historial suficiente'; }
                    $('#pcAutoColorText').text(detail).css('color', '#334155');
                } else {
                    $('#colorCita').val('#3a87ad');
                    $('#pcAutoColorDot').css('background', '#3a87ad');
                    $('#pcAutoColorText').text(resp.error || 'Orden no encontrada').css('color', '#ef4444');
                }
            },
            error: function(xhr) {
                if (xhr.statusText === 'abort') return;
                $('#colorCita').val('#3a87ad');
                $('#pcAutoColorDot').css('background', '#3a87ad');
                $('#pcAutoColorText').text('Error al consultar').css('color', '#ef4444');
            }
        });
    });

    /* ═══════════════════════════════════════════
       Form Submit
       ═══════════════════════════════════════════ */
    var isSaving = false;

    $(document).off('submit', '#formularioCita').on('submit', '#formularioCita', function (e) {
        e.preventDefault();
        e.stopPropagation();

        if (isSaving) return;

        var titulo = $('#tituloCita').val();
        var color = $('#colorCita').val();
        var idOrden = $('#idOrden').val();

        if (!pcSelectedHora) {
            swal({ icon: 'warning', title: 'Hora requerida', text: 'Selecciona una hora disponible.' });
            return;
        }

        var fechaFinal = pcBuildFechaFinal();
        if (!fechaFinal) {
            swal({ icon: 'warning', title: 'Fecha inválida', text: 'Selecciona una fecha y hora válidas.' });
            return;
        }

        // Set the hidden field
        $('#fechaCita').val(fechaFinal);

        isSaving = true;
        var $btn = $('#btnGuardarCitaPC');
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Guardando...');

        var descripcion = $('#descripcionCita').val() || '';

        var datos = new FormData();
        datos.append("tituloCita", titulo);
        datos.append("fechaCita", fechaFinal);
        datos.append("colorCita", color);
        datos.append("idOrden", idOrden && idOrden > 0 ? idOrden : '');
        datos.append("descripcionCita", descripcion);

        $.ajax({
            url: "ajax/citas.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respuesta) {
                if (respuesta.indexOf('duplicado_hora') === 0) {
                    var parts = respuesta.split('|');
                    swal({ icon: 'warning', title: 'Horario ocupado', text: 'Ya existe una cita a esa hora: "' + parts[1] + '" (' + parts[2] + '). Elige otra hora.' });
                } else if (respuesta.indexOf('duplicado_orden') === 0) {
                    var parts = respuesta.split('|');
                    swal({ icon: 'warning', title: 'Cita duplicada', text: 'Ya existe una cita para esta orden en ese día: "' + parts[1] + '" (' + parts[2] + ').' });
                } else if (respuesta == "ok") {
                    $('#modalAgregarCita').modal('hide');
                    document.getElementById('formularioCita').reset();

                    // Reset chips + grid
                    pcSelectedFecha = 'hoy';
                    pcSelectedHora = '10:00';
                    $('.egs-pc-qd-chip').removeClass('active').first().addClass('active');
                    $('#pcCustomDateWrap').hide();
                    $('#colorCita').val('#3a87ad');
                    $('#pcAutoColorDot').css('background', '#3a87ad');
                    $('#pcAutoColorText').text('Sin orden — se usará color por defecto').css('color', '#64748b');
                    pcRenderTimeGrid();
                    pcUpdateDatePreview();

                    calendar.refetchEvents();
                    swal({ icon: 'success', title: 'Cita agendada', text: titulo, showConfirmButton: false, timer: 1800 });
                } else {
                    swal({ icon: 'error', title: 'Error', text: 'No se pudo guardar: ' + respuesta });
                }
            },
            error: function () {
                swal({ icon: 'error', title: 'Error de conexión', text: 'No se pudo guardar la cita.' });
            },
            complete: function () {
                isSaving = false;
                $btn.prop('disabled', false).html('<i class="fa-solid fa-check" style="margin-right:5px;"></i>Agendar');
            }
        });
    });
});
