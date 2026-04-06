<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padel Manager</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        :root {
            --green: #03ff03;
            --green-dim: #00cc00;
            --green-glow: rgba(3, 255, 3, 0.15);
            --bg: #090d16;
            --surface: #111827;
            --surface2: #1a2540;
            --border: #1e2d47;
            --border2: #2a3f60;
            --text: #edf2fa;
            --text-muted: #8898aa;
            --danger: #ff4757;
            --danger-dim: #cc1a2a;
            --warning: #ffa502;
            --info: #3d9bff;
            --radius: 12px;
            --radius-sm: 8px;
        }

        body {
            font-family: 'Barlow', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh
        }

        /* HEADER */
        .header {
            background: rgba(17, 24, 39, 0.95);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg {
            width: 20px;
            height: 20px
        }

        .logo-text {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px
        }

        .logo-text span {
            color: var(--green)
        }

        .header-badge {
            background: var(--green-glow);
            border: 1px solid rgba(3, 255, 3, 0.3);
            color: var(--green);
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: .5px;
        }

        /* TABS */
        .tab-nav {
            display: flex;
            gap: 0;
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            background: var(--surface);
        }

        .tab-btn {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            padding: 16px 28px;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--text-muted);
            position: relative;
            transition: color .2s;
        }

        .tab-btn:hover {
            color: var(--text)
        }

        .tab-btn.active {
            color: var(--green)
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--green);
            border-radius: 2px 2px 0 0;
        }

        .tab-count {
            background: var(--surface2);
            border: 1px solid var(--border2);
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 10px;
            margin-left: 8px;
        }

        .tab-btn.active .tab-count {
            background: var(--green-glow);
            border-color: rgba(3, 255, 3, 0.3);
            color: var(--green)
        }

        /* CONTENT */
        .content {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto
        }

        .tab-panel {
            display: none
        }

        .tab-panel.active {
            display: block
        }

        /* TOOLBAR */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem
        }

        .toolbar-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: .5px
        }

        .toolbar-title span {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 18px;
            margin-left: 8px
        }

        .btn-create {
            background: var(--green);
            color: #050a0f;
            font-family: 'Barlow', sans-serif;
            font-size: 14px;
            font-weight: 700;
            border: none;
            padding: 10px 22px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all .15s;
            letter-spacing: .3px;
        }

        .btn-create:hover {
            background: #00ee00;
            transform: translateY(-1px)
        }

        .btn-create:active {
            transform: translateY(0)
        }

        .btn-create svg {
            width: 16px;
            height: 16px
        }

        /* GRID */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.25rem
        }

        /* TOURNAMENT CARD */
        .t-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: border-color .2s, transform .2s;
            position: relative;
        }

        .t-card:hover {
            border-color: var(--border2);
            transform: translateY(-2px)
        }

        .t-card-accent {
            height: 3px;
            width: 100%
        }

        .t-card-accent.pending {
            background: var(--warning)
        }

        .t-card-accent.ready {
            background: var(--info)
        }

        .t-card-accent.active {
            background: var(--green)
        }

        .t-card-accent.ended {
            background: var(--text-muted)
        }

        .t-card-body {
            padding: 1.25rem
        }

        .t-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1rem
        }

        .t-card-name {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 20px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 4px;
            letter-spacing: .3px
        }

        .t-card-category {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 20px;
            background: var(--surface2);
            color: var(--text-muted);
            border: 1px solid var(--border2);
        }

        .status-badge {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .status-badge.pending {
            background: rgba(255, 165, 2, .1);
            color: var(--warning);
            border: 1px solid rgba(255, 165, 2, .3)
        }

        .status-badge.ready {
            background: rgba(61, 155, 255, .1);
            color: var(--info);
            border: 1px solid rgba(61, 155, 255, .3)
        }

        .status-badge.active {
            background: var(--green-glow);
            color: var(--green);
            border: 1px solid rgba(3, 255, 3, .3)
        }

        .status-badge.ended {
            background: rgba(136, 152, 170, .1);
            color: var(--text-muted);
            border: 1px solid rgba(136, 152, 170, .2)
        }

        .t-card-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .5rem;
            margin-bottom: 1.25rem
        }

        .t-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px
        }

        .t-meta-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: var(--surface2);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .t-meta-icon svg {
            width: 13px;
            height: 13px;
            opacity: .6
        }

        .t-meta-label {
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
            line-height: 1
        }

        .t-meta-val {
            color: var(--text);
            font-weight: 600;
            font-size: 13px;
            line-height: 1.2
        }

        .t-card-actions {
            display: flex;
            gap: 8px;
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border);
            background: rgba(0, 0, 0, .15);
        }

        .btn-action {
            flex: 1;
            padding: 9px 12px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all .15s;
            letter-spacing: .3px;
            font-family: 'Barlow', sans-serif;
        }

        .btn-action svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0
        }

        .btn-cancel {
            background: rgba(255, 71, 87, .1);
            color: var(--danger);
            border: 1px solid rgba(255, 71, 87, .3)
        }

        .btn-cancel:hover {
            background: rgba(255, 71, 87, .2)
        }

        .btn-ok {
            background: rgba(61, 155, 255, .1);
            color: var(--info);
            border: 1px solid rgba(61, 155, 255, .3)
        }

        .btn-ok:hover {
            background: rgba(61, 155, 255, .2)
        }

        .btn-start {
            background: var(--green-glow);
            color: var(--green);
            border: 1px solid rgba(3, 255, 3, .3)
        }

        .btn-start:hover {
            background: rgba(3, 255, 3, .25)
        }

        .btn-end {
            background: rgba(255, 165, 2, .1);
            color: var(--warning);
            border: 1px solid rgba(255, 165, 2, .3)
        }

        .btn-end:hover {
            background: rgba(255, 165, 2, .2)
        }

        /* PARTICIPANT CARD */
        .p-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: border-color .2s, transform .2s;
        }

        .p-card:hover {
            border-color: var(--border2);
            transform: translateY(-2px)
        }

        .p-card-body {
            padding: 1.25rem
        }

        .p-card-profile {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 1.1rem
        }

        .avatar {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 18px;
            font-weight: 800;
            flex-shrink: 0;
            position: relative;
            border: 2px solid;
        }

        .p-name {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 19px;
            font-weight: 800;
            letter-spacing: .3px;
            line-height: 1.2
        }

        .p-category-badge {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 20px;
            background: var(--surface2);
            color: var(--text-muted);
            border: 1px solid var(--border2);
            display: inline-block;
            margin-top: 4px;
        }

        .p-details {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 1.1rem
        }

        .p-detail {
            display: flex;
            align-items: center;
            gap: 9px;
            font-size: 13px
        }

        .p-detail-icon {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: var(--surface2);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .p-detail-icon svg {
            width: 12px;
            height: 12px;
            opacity: .55
        }

        .p-detail-label {
            color: var(--text-muted);
            min-width: 70px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px
        }

        .p-detail-val {
            color: var(--text);
            font-weight: 500
        }

        .tourney-tag {
            background: var(--green-glow);
            color: var(--green);
            border: 1px solid rgba(3, 255, 3, .2);
            font-size: 12px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .p-card-actions {
            display: flex;
            gap: 8px;
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border);
            background: rgba(0, 0, 0, .15);
        }

        .btn-reject {
            background: rgba(255, 71, 87, .1);
            color: var(--danger);
            border: 1px solid rgba(255, 71, 87, .3)
        }

        .btn-reject:hover {
            background: rgba(255, 71, 87, .2)
        }

        .btn-accept {
            background: var(--green-glow);
            color: var(--green);
            border: 1px solid rgba(3, 255, 3, .3)
        }

        .btn-accept:hover {
            background: rgba(3, 255, 3, .25)
        }

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 999;
            background: rgba(0, 0, 0, .75);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-overlay.open {
            display: flex
        }

        .modal {
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: var(--radius);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            animation: modalIn .2s ease;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(.96) translateY(8px)
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0)
            }
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: .3px
        }

        .modal-title span {
            color: var(--green)
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s;
        }

        .modal-close:hover {
            background: var(--surface2);
            color: var(--text)
        }

        .modal-close svg {
            width: 16px;
            height: 16px
        }

        .modal-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .form-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-muted)
        }

        .form-input,
        .form-select {
            background: var(--bg);
            border: 1px solid var(--border2);
            color: var(--text);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            font-size: 14px;
            font-family: 'Barlow', sans-serif;
            outline: none;
            transition: border-color .15s;
            width: 100%;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: var(--green)
        }

        .form-select option {
            background: var(--surface)
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .btn-modal-cancel {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border2);
            padding: 9px 20px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Barlow', sans-serif;
            transition: all .15s;
        }

        .btn-modal-cancel:hover {
            border-color: var(--border);
            color: var(--text)
        }

        .btn-modal-confirm {
            background: var(--green);
            color: #050a0f;
            border: none;
            padding: 9px 24px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Barlow', sans-serif;
            transition: all .15s;
        }

        .btn-modal-confirm:hover {
            background: #00ee00
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            border: 1px dashed var(--border2);
            border-radius: var(--radius);
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 1rem;
            opacity: .4
        }

        .empty-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: .5rem
        }

        .empty-sub {
            font-size: 14px;
            color: var(--text-muted);
            opacity: .7
        }

        /* TOAST */
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .toast {
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: var(--radius-sm);
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: toastIn .2s ease;
            min-width: 260px;
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(20px)
            }

            to {
                opacity: 1;
                transform: translateX(0)
            }
        }

        .toast.success {
            border-left: 3px solid var(--green);
            color: var(--green)
        }

        .toast.danger {
            border-left: 3px solid var(--danger);
            color: var(--danger)
        }

        .toast.warning {
            border-left: 3px solid var(--warning);
            color: var(--warning)
        }

        .toast.info {
            border-left: 3px solid var(--info);
            color: var(--info)
        }
    </style>
</head>

<body>

    <div class="toast-container" id="toastContainer"></div>

    <!-- HEADER -->
    <div class="header">
        <div class="logo">
            <div class="logo-icon">
                <img src="./img/logot1.png" alt="Logo" style="width:18px;height:18px">
            </div>
            <span class="logo-text">GO<span>PADEL</span></span>
        </div>
        <div class="header-badge">Panel de Gestión</div>
    </div>

    <!-- TABS -->
    <div class="tab-nav">
        <button class="tab-btn active" onclick="switchTab('torneos')">
            Torneos <span class="tab-count" id="countTorneos">0</span>
        </button>
        <button class="tab-btn" onclick="switchTab('participantes')">
            Participantes <span class="tab-count" id="countParticipantes">0</span>
        </button>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <!-- TORNEOS -->
        <div class="tab-panel active" id="tab-torneos">
            <div class="toolbar">
                <div class="toolbar-title">Torneos <span id="torneoSubtitle"></span></div>
                <button class="btn-create" onclick="openModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Crear Torneo
                </button>
            </div>
            <div class="cards-grid" id="torneosGrid"></div>
        </div>

        <!-- PARTICIPANTES -->
        <div class="tab-panel" id="tab-participantes">
            <div class="toolbar">
                <div class="toolbar-title">Participantes <span id="partSubtitle"></span></div>
            </div>
            <div class="cards-grid" id="participantesGrid"></div>
        </div>

    </div>

    <!-- MODAL -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">Nuevo <span>Torneo</span></div>
                <button class="modal-close" onclick="closeModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nombre del Torneo</label>
                    <input class="form-input" id="f-name" placeholder="Ej: Copa Verano 2025" />
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" id="f-cat">
                            <option value="">Seleccionar</option>
                            <option>Masculino</option>
                            <option>Femenino</option>
                            <option>Mixto</option>
                            <option>Sub-18</option>
                            <option>Senior +40</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha</label>
                        <input class="form-input" id="f-date" type="date" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Cuota de Inscripción</label>
                    <input class="form-input" id="f-fee" type="number" placeholder="Ej: 25" />
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-cancel" onclick="closeModal()">Cancelar</button>
                <button class="btn-modal-confirm" onclick="createTorneo()">Crear Torneo</button>
            </div>
        </div>
    </div>

    <script>
        const avatarColors = [
            { bg: 'rgba(61,155,255,.15)', border: 'rgba(61,155,255,.4)', text: '#3d9bff' },
            { bg: 'rgba(3,255,3,.1)', border: 'rgba(3,255,3,.35)', text: '#03ff03' },
            { bg: 'rgba(255,71,87,.12)', border: 'rgba(255,71,87,.35)', text: '#ff4757' },
            { bg: 'rgba(255,165,2,.12)', border: 'rgba(255,165,2,.35)', text: '#ffa502' },
            { bg: 'rgba(180,140,255,.12)', border: 'rgba(180,140,255,.35)', text: '#b48cff' },
            { bg: 'rgba(255,100,180,.12)', border: 'rgba(255,100,180,.35)', text: '#ff64b4' },
        ];

        let torneos = [
            { id: 1, name: 'Copa Verano 2025', category: 'Masculino', date: '2025-07-12', fee: 300, status: 'active' },
            { id: 2, name: 'Torneo Mixto Primavera', category: 'Mixto', date: '2025-06-08', fee: 200, status: 'ready' },
            { id: 3, name: 'Liga Femenina Junio', category: 'Femenino', date: '2025-06-22', fee: 250, status: 'pending' },
            { id: 4, name: 'Gran Prix Sub-18', category: 'Sub-18', date: '2025-08-01', fee: 150, status: 'ended' },
            { id: 5, name: 'Clásico Senior +40', category: 'Senior +40', date: '2025-07-19', fee: 250, status: 'pending' },
        ];

        let participantes = [
            { id: 1, name: 'Carlos Méndez', category: 'Masculino', phone: '+598 91 234 567', torneo: 'Copa Verano 2025', color: 0 },
            { id: 2, name: 'Laura Fernández', category: 'Femenino', phone: '+598 99 876 543', torneo: 'Liga Femenina Junio', color: 1 },
            { id: 3, name: 'Diego Sousa', category: 'Mixto', phone: '+598 94 112 334', torneo: 'Torneo Mixto Primavera', color: 2 },
            { id: 4, name: 'Valentina Cruz', category: 'Femenino', phone: '+598 92 445 667', torneo: 'Liga Femenina Junio', color: 3 },
            { id: 5, name: 'Matías Torres', category: 'Sub-18', phone: '+598 96 321 654', torneo: 'Gran Prix Sub-18', color: 4 },
            { id: 6, name: 'Sofía Rodríguez', category: 'Mixto', phone: '+598 98 778 990', torneo: 'Torneo Mixto Primavera', color: 5 },
        ];

        let nextId = 100;

        const statusLabel = { pending: 'Pendiente', ready: 'Inscripción Abierta', active: 'En Curso', ended: 'Finalizado' };
        const statusIcons = {
            pending: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,
            ready: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`,
            active: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>`,
            ended: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>`,
        };

        function fmtDate(d) {
            if (!d) return '—';
            const [y, m, day] = d.split('-');
            const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            return `${parseInt(day)} ${months[parseInt(m) - 1]} ${y}`;
        }

        function initials(name) {
            return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
        }

        function renderTorneos() {
            const grid = document.getElementById('torneosGrid');
            if (!torneos.length) {
                grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1"><div class="empty-icon">🏆</div><div class="empty-title">No hay torneos aún</div><div class="empty-sub">Creá tu primer torneo haciendo clic en el botón de arriba</div></div>`;
                document.getElementById('torneoSubtitle').textContent = '';
            } else {
                const counts = { pending: 0, ready: 0, active: 0, ended: 0 };
                torneos.forEach(t => counts[t.status] = (counts[t.status] || 0) + 1);
                document.getElementById('torneoSubtitle').textContent = `(${torneos.length})`;
                grid.innerHTML = torneos.map(t => tornoCard(t)).join('');
            }
            document.getElementById('countTorneos').textContent = torneos.length;
        }

        function tornoCard(t) {
            let actions = '';
            if (t.status === 'pending') {
                actions = `
      <button class="btn-action btn-cancel" onclick="deleteTorneo(${t.id})">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>Cancelar</button>
      <button class="btn-action btn-ok" onclick="changeStatus(${t.id},'ready')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Abrir Inscripción</button>`;
            } else if (t.status === 'ready') {
                actions = `
      <button class="btn-action btn-cancel" onclick="deleteTorneo(${t.id})">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>Cancelar</button>
      <button class="btn-action btn-start" onclick="changeStatus(${t.id},'active')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>Iniciar Torneo</button>`;
            } else if (t.status === 'active') {
                actions = `
      <button class="btn-action btn-end" onclick="changeStatus(${t.id},'ended')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>Finalizar Torneo</button>`;
            } else {
                actions = `<button class="btn-action" style="background:rgba(136,152,170,.08);color:var(--text-muted);border:1px solid rgba(136,152,170,.15);cursor:default;flex:1" disabled>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Torneo Completado</button>`;
            }
            return `<div class="t-card" id="tcard-${t.id}">
    <div class="t-card-accent ${t.status}"></div>
    <div class="t-card-body">
      <div class="t-card-header">
        <div>
          <div class="t-card-name">${t.name}</div>
          <span class="t-card-category">${t.category}</span>
        </div>
        <span class="status-badge ${t.status}">${statusLabel[t.status]}</span>
      </div>
      <div class="t-card-meta">
        <div class="t-meta-item">
          <div class="t-meta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
          <div><div class="t-meta-label">Fecha</div><div class="t-meta-val">${fmtDate(t.date)}</div></div>
        </div>
        <div class="t-meta-item">
          <div class="t-meta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 1 0 0 7h5a3.5 3.5 0 1 1 0 7H6"/></svg></div>
          <div><div class="t-meta-label">Cuota</div><div class="t-meta-val">${t.fee}</div></div>
        </div>
      </div>
    </div>
    <div class="t-card-actions">${actions}</div>
  </div>`;
        }

        function renderParticipantes() {
            const grid = document.getElementById('participantesGrid');
            if (!participantes.length) {
                grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1"><div class="empty-icon">👥</div><div class="empty-title">Sin solicitudes pendientes</div><div class="empty-sub">No hay participantes esperando aprobación</div></div>`;
            } else {
                document.getElementById('partSubtitle').textContent = `(${participantes.length})`;
                grid.innerHTML = participantes.map(p => partCard(p)).join('');
            }
            document.getElementById('countParticipantes').textContent = participantes.length;
        }

        function partCard(p) {
            const c = avatarColors[p.color % avatarColors.length];
            return `<div class="p-card" id="pcard-${p.id}">
    <div class="p-card-body">
      <div class="p-card-profile">
        <div class="avatar" style="background:${c.bg};border-color:${c.border};color:${c.text}">${initials(p.name)}</div>
        <div>
          <div class="p-name">${p.name}</div>
          <span class="p-category-badge">${p.category}</span>
        </div>
      </div>
      <div class="p-details">
        <div class="p-detail">
          <div class="p-detail-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.59 1.18h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9a16 16 0 0 0 6.29 6.29l.81-.81a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
          <span class="p-detail-label">Teléfono</span>
          <span class="p-detail-val">${p.phone}</span>
        </div>
        <div class="p-detail">
          <div class="p-detail-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg></div>
          <span class="p-detail-label">Torneo</span>
          <span class="tourney-tag">${p.torneo}</span>
        </div>
      </div>
    </div>
    <div class="p-card-actions">
      <button class="btn-action btn-reject" onclick="rejectParticipante(${p.id})">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Rechazar</button>
      <button class="btn-action btn-accept" onclick="acceptParticipante(${p.id})">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>Aceptar</button>
    </div>
  </div>`;
        }

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            event.currentTarget.classList.add('active');
            document.getElementById('tab-' + tab).classList.add('active');
        }

        function openModal() { document.getElementById('modalOverlay').classList.add('open') }
        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('open');
            ['f-name', 'f-cat', 'f-date', 'f-fee'].forEach(id => { document.getElementById(id).value = '' });
        }

        function createTorneo() {
            const name = document.getElementById('f-name').value.trim();
            const cat = document.getElementById('f-cat').value;
            const date = document.getElementById('f-date').value;
            const fee = document.getElementById('f-fee').value;
            if (!name || !cat || !date || !fee) { toast('Completá todos los campos', 'warning'); return; }
            torneos.unshift({ id: nextId++, name, category: cat, date, fee: parseInt(fee), status: 'pending' });
            closeModal();
            renderTorneos();
            toast(`Torneo "${name}" creado exitosamente`, 'success');
        }

        function deleteTorneo(id) {
            const t = torneos.find(x => x.id === id);
            torneos = torneos.filter(x => x.id !== id);
            renderTorneos();
            toast(`Torneo "${t.name}" cancelado`, 'danger');
        }

        function changeStatus(id, newStatus) {
            const t = torneos.find(x => x.id === id);
            t.status = newStatus;
            renderTorneos();
            const msgs = { ready: 'Inscripción abierta ✓', active: '¡Torneo iniciado!', ended: 'Torneo finalizado' };
            const types = { ready: 'info', active: 'success', ended: 'warning' };
            toast(`${t.name}: ${msgs[newStatus]}`, types[newStatus]);
        }

        function acceptParticipante(id) {
            const p = participantes.find(x => x.id === id);
            participantes = participantes.filter(x => x.id !== id);
            renderParticipantes();
            toast(`${p.name} aceptado/a en ${p.torneo}`, 'success');
        }

        function rejectParticipante(id) {
            const p = participantes.find(x => x.id === id);
            participantes = participantes.filter(x => x.id !== id);
            renderParticipantes();
            toast(`Solicitud de ${p.name} rechazada`, 'danger');
        }

        function toast(msg, type = 'info') {
            const el = document.createElement('div');
            el.className = `toast ${type}`;
            const icons = { success: '✓', danger: '✕', warning: '!', info: 'i' };
            el.innerHTML = `<span style="font-weight:700;font-size:15px">${icons[type] || 'i'}</span><span style="color:var(--text)">${msg}</span>`;
            const c = document.getElementById('toastContainer');
            c.appendChild(el);
            setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .3s'; setTimeout(() => el.remove(), 300) }, 3000);
        }

        document.getElementById('modalOverlay').addEventListener('click', function (e) { if (e.target === this) closeModal() });

        renderTorneos();
        renderParticipantes();
    </script>
</body>

</html>