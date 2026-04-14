<?php
session_start();

if (isset($_SESSION['userId']) && $_SESSION['isAdmin'] === "1") {
    $userId = $_SESSION['userId'];
    $isadmin = $_SESSION['isAdmin'];
} else {
    // Redirect to login page if no token is found
    header("Location: landing.php");
    exit();
}
?>
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

        .tab-nav {
            display: flex;
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

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .toolbar-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: .5px
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

        .btn-refresh {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border2);
            padding: 9px 14px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-family: 'Barlow', sans-serif;
            font-weight: 600;
            transition: all .15s;
        }

        .btn-refresh:hover {
            color: var(--text);
            border-color: var(--border)
        }

        .btn-refresh svg {
            width: 14px;
            height: 14px;
            transition: transform .1s
        }

        .btn-refresh.spinning svg {
            animation: spin .7s linear infinite
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        .filter-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .filter-btn {
            background: transparent;
            border: 1px solid var(--border2);
            color: var(--text-muted);
            font-family: 'Barlow', sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .4px;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 20px;
            cursor: pointer;
            transition: all .15s;
        }

        .filter-btn:hover {
            border-color: var(--border);
            color: var(--text)
        }

        .filter-btn.active {
            background: var(--green-glow);
            border-color: rgba(3, 255, 3, .35);
            color: var(--green)
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.25rem
        }

        .t-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: border-color .2s, transform .2s;
        }

        .t-card:hover {
            border-color: var(--border2);
            transform: translateY(-2px)
        }

        .t-card-accent {
            height: 3px;
            width: 100%
        }

        .t-card-accent.s0 {
            background: var(--warning)
        }

        .t-card-accent.s1 {
            background: var(--info)
        }

        .t-card-accent.s2 {
            background: var(--text-muted)
        }

        .t-card-body {
            padding: 1.25rem
        }

        .t-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1rem;
            gap: 8px;
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

        .status-badge.s0 {
            background: rgba(255, 165, 2, .1);
            color: var(--warning);
            border: 1px solid rgba(255, 165, 2, .3)
        }

        .status-badge.s1 {
            background: rgba(61, 155, 255, .1);
            color: var(--info);
            border: 1px solid rgba(61, 155, 255, .3)
        }

        .status-badge.s2 {
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

        .btn-action:disabled {
            opacity: .45;
            cursor: not-allowed
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

        .btn-cancel:hover:not(:disabled) {
            background: rgba(255, 71, 87, .2)
        }

        .btn-ok {
            background: rgba(61, 155, 255, .1);
            color: var(--info);
            border: 1px solid rgba(61, 155, 255, .3)
        }

        .btn-ok:hover:not(:disabled) {
            background: rgba(61, 155, 255, .2)
        }

        .btn-end {
            background: rgba(255, 165, 2, .1);
            color: var(--warning);
            border: 1px solid rgba(255, 165, 2, .3)
        }

        .btn-end:hover:not(:disabled) {
            background: rgba(255, 165, 2, .2)
        }

        .btn-done {
            background: rgba(136, 152, 170, .08);
            color: var(--text-muted);
            border: 1px solid rgba(136, 152, 170, .15);
            cursor: default;
        }

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

        .avatar-wrap {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            border: 2px solid;
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 16px;
            font-weight: 800;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
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
            font-size: 15px;
            font-weight: 800;
            flex-shrink: 0;
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

        .btn-reject:hover:not(:disabled) {
            background: rgba(255, 71, 87, .2)
        }

        .btn-accept {
            background: var(--green-glow);
            color: var(--green);
            border: 1px solid rgba(3, 255, 3, .3)
        }

        .btn-accept:hover:not(:disabled) {
            background: rgba(3, 255, 3, .25)
        }

        .accepted-badge {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            color: var(--green);
            background: var(--green-glow);
            border: 1px solid rgba(3, 255, 3, .2);
            border-radius: var(--radius-sm);
            padding: 9px;
        }

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
                transform: none
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
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-modal-confirm:hover:not(:disabled) {
            background: #00ee00
        }

        .btn-modal-confirm:disabled {
            opacity: .6;
            cursor: not-allowed
        }

        .btn-spin {
            width: 14px;
            height: 14px;
            display: none;
            animation: spin .7s linear infinite
        }

        .loading-state {
            text-align: center;
            padding: 4rem 2rem;
            grid-column: 1/-1
        }

        .spinner {
            width: 36px;
            height: 36px;
            border: 3px solid var(--border);
            border-top-color: var(--green);
            border-radius: 50%;
            animation: spin .7s linear infinite;
            margin: 0 auto 1rem;
        }

        .loading-text {
            color: var(--text-muted);
            font-size: 14px
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            border: 1px dashed var(--border2);
            border-radius: var(--radius);
            grid-column: 1/-1
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

        .error-state {
            text-align: center;
            padding: 3rem 2rem;
            border: 1px solid rgba(255, 71, 87, .25);
            border-radius: var(--radius);
            background: rgba(255, 71, 87, .05);
            grid-column: 1/-1
        }

        .error-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--danger);
            margin-bottom: .5rem
        }

        .error-sub {
            font-size: 13px;
            color: var(--text-muted)
        }

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
            max-width: 360px;
        }

        .ap-user-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            cursor: pointer;
            border-bottom: 1px solid var(--border);
            transition: background .12s;
        }

        .ap-user-row:last-child {
            border-bottom: none
        }

        .ap-user-row:hover {
            background: var(--surface2)
        }

        .ap-user-row.selected {
            background: var(--green-glow)
        }

        .ap-user-mini-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 12px;
            font-weight: 800;
        }

        .ap-user-name {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.2
        }

        .ap-user-sub {
            font-size: 11px;
            color: var(--text-muted)
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(20px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .toast.success {
            border-left: 3px solid var(--green)
        }

        .toast.danger {
            border-left: 3px solid var(--danger)
        }

        .toast.warning {
            border-left: 3px solid var(--warning)
        }

        .toast.info {
            border-left: 3px solid var(--info)
        }

        .toast-icon {
            font-weight: 700;
            font-size: 15px;
            flex-shrink: 0
        }

        .toast.success .toast-icon {
            color: var(--green)
        }

        .toast.danger .toast-icon {
            color: var(--danger)
        }

        .toast.warning .toast-icon {
            color: var(--warning)
        }

        .toast.info .toast-icon {
            color: var(--info)
        }

        .toast-msg {
            color: var(--text);
            font-size: 13px
        }
    </style>
</head>

<body>
    <div class="toast-container" id="toastContainer"></div>

    <!-- HEADER -->
    <div class="header">
        <div class="logo">
            <div class="logo-icon">
                <img src="./img/logot.png" alt="Logo" style="width:16px;height:16px">
            </div>
            <span class="logo-text">GO<span>PADEL</span></span>
        </div>
        <div class="header-badge">Panel de Gestión</div>
    </div>

    <!-- TABS -->
    <div class="tab-nav">
        <button class="tab-btn active" id="tabBtnTorneos" onclick="switchTab('torneos',this)">
            Torneos <span class="tab-count" id="countTorneos">–</span>
        </button>
        <button class="tab-btn" id="tabBtnPart" onclick="switchTab('participantes',this)">
            Participantes <span class="tab-count" id="countParticipantes">–</span>
        </button>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <!-- TAB TORNEOS -->
        <div class="tab-panel active" id="tab-torneos">
            <div class="toolbar">
                <div class="toolbar-left">
                    <div class="toolbar-title">Torneos</div>
                    <button class="btn-refresh" id="btnRefreshTorneos" onclick="loadTorneos()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round">
                            <polyline points="23 4 23 10 17 10" />
                            <polyline points="1 20 1 14 7 14" />
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15" />
                        </svg>
                        Actualizar
                    </button>
                </div>
                <button class="btn-create" onclick="openModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Crear Torneo
                </button>
            </div>
            <div class="filter-bar">
                <button class="filter-btn active" onclick="setFilter(null,this)">Todos</button>
                <button class="filter-btn" onclick="setFilter(0,this)">Pendientes</button>
                <button class="filter-btn" onclick="setFilter(1,this)">Abiertos</button>
                <button class="filter-btn" onclick="setFilter(2,this)">Cerrados</button>
            </div>
            <div class="cards-grid" id="torneosGrid">
                <div class="loading-state">
                    <div class="spinner"></div>
                    <div class="loading-text">Cargando torneos…</div>
                </div>
            </div>
        </div>

        <!-- TAB PARTICIPANTES -->
        <div class="tab-panel" id="tab-participantes">
            <div class="toolbar">
                <div class="toolbar-left">
                    <div class="toolbar-title">Participantes</div>
                    <button class="btn-refresh" id="btnRefreshPart" onclick="partLoaded=false;loadParticipantes()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round">
                            <polyline points="23 4 23 10 17 10" />
                            <polyline points="1 20 1 14 7 14" />
                            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15" />
                        </svg>
                        Actualizar
                    </button>
                    <button class="btn-create" onclick="openAddPartModal()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke - width="2.5" stroke-line
                            cap="round">
                            <line x1="12" y1="5" x2="1
                                2" y2="19" />
                            <line x1="5" y1="12" x2="1
                            9" y2="12" />
                        </svg>
                        Agregar Participante
                    </button>
                </div>
            </div>
            <div class="filter-bar" id="partFilterBar">
                <button class="filter-btn active" onclick="setPartFilter(null,this)">Todos los torneos</button>
            </div>
            <div class="cards-grid" id="participantesGrid">
                <div class="loading-state">
                    <div class="spinner"></div>
                    <div class="loading-text">Esperando…</div>
                </div>
            </div>
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
                            <option value="1">1ra</option>
                            <option value="2">2da</option>
                            <option value="3">3ra</option>
                            <option value="4">4ta</option>
                            <option value="5">5ta</option>
                            <option value="6">6ta</option>
                            <option value="7">7ma</option>
                            <option value="8">8va</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha</label>
                        <input class="form-input" id="f-date" type="date" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Cuota de Inscripción</label>
                    <input class="form-input" id="f-fee" type="number" placeholder="Ej: 25" min="0" />
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-cancel" onclick="closeModal()">Cancelar</button>
                <button class="btn-modal-confirm" id="btnConfirm" onclick="createTorneo()">
                    <svg class="btn-spin" id="confirmSpinner" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                    </svg>
                    Crear Torneo
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL AGREGAR PARTICIPANTE -->
    <div class="modal-overlay" id="addPartOverlay">
        <div class="modal" style="max-width:520px">
            <div class="modal-header">
                <div class="modal-title">Agregar <span>Participante</span></div>
                <button class="modal-close" onclick="closeAddPartModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Torneo</label>
                    <select class="form-select" id="ap-torneo">
                        <option value="">Seleccionar torneo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Buscar usuario</label>
                    <input class="form-input" id="ap-search" placeholder="Nombre o usuario…" oninput="filterPerfiles()"
                        autocomplete="off" />
                </div>
                <div id="ap-results" style="
        max-height:240px;overflow-y:auto;
        border:1px solid var(--border2);border-radius:var(--radius-sm);
        background:var(--bg);display:none;
      "></div>
                <div id="ap-selected" style="display:none;
        padding:10px 14px;border:1px solid rgba(3,255,3,.3);
        border-radius:var(--radius-sm);background:var(--green-glow);
        display:none;align-items:center;justify-content:space-between;gap:8px;
      ">
                    <span id="ap-selected-name" style="font-weight:600;font-size:14px;color:var(--green)"></span>
                    <button onclick="clearSelectedUser()"
                        style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:18px;line-height:1;padding:0 2px;">&times;</button>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-cancel" onclick="closeAddPartModal()">Cancelar</button>
                <button class="btn-modal-confirm" id="btnAddPart" onclick="addParticipante()">
                    <svg class="btn-spin" id="addPartSpinner" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                    </svg>
                    Agregar
                </button>
            </div>
        </div>
    </div>

    <script>
        /* ─── CONFIG ─────────────────────────────────────────── */
        const BASE = '../accion';

        const CATS = { 1: '1ra', 2: '2da', 3: '3ra', 4: '4ta', 5: '5ta', 6: '6ta', 7: '7ma', 8: '8va' };
        const S_LBL = { 0: 'Pendiente', 1: 'Inscripción Abierta', 2: 'Cerrado' };
        const AV_CLR = [
            { bg: 'rgba(61,155,255,.15)', bd: 'rgba(61,155,255,.4)', tx: '#3d9bff' },
            { bg: 'rgba(3,255,3,.1)', bd: 'rgba(3,255,3,.35)', tx: '#03ff03' },
            { bg: 'rgba(255,71,87,.12)', bd: 'rgba(255,71,87,.35)', tx: '#ff4757' },
            { bg: 'rgba(255,165,2,.12)', bd: 'rgba(255,165,2,.35)', tx: '#ffa502' },
            { bg: 'rgba(180,140,255,.12)', bd: 'rgba(180,140,255,.35)', tx: '#b48cff' },
            { bg: 'rgba(255,100,180,.12)', bd: 'rgba(255,100,180,.35)', tx: '#ff64b4' },
        ];

        /* ─── STATE ──────────────────────────────────────────── */
        let torneos = [];
        let participantes = [];
        let profileCache = {};   // idUsuario → profile object
        let activeFilter = null;
        let partFilter = null;   // idTorneo filter for participants tab
        let partLoaded = false;

        /* ─── HTTP ───────────────────────────────────────────── */
        async function postForm(endpoint, data) {
            const fd = new FormData();
            Object.entries(data).forEach(([k, v]) => {
                if (v !== null && v !== undefined && v !== '') fd.append(k, v);
            });
            const res = await fetch(`${BASE}/${endpoint}`, { method: 'POST', body: fd });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        }

        const isOk = j => j?.consultaResponse?.codigoError === '0';

        /* ─── TOURNAMENTS ────────────────────────────────────── */
        async function loadTorneos() {
            spinning('btnRefreshTorneos', true);
            grid('torneosGrid', loading('Cargando torneos…'));
            const payload = {};
            if (activeFilter !== null) payload.estado = activeFilter;
            try {
                const j = await postForm('getTorneos.php', payload);
                if (!isOk(j)) throw new Error(j?.consultaResponse?.detalleError || 'Error');
                torneos = j.consultaResponse.torneos || [];
                renderTorneos();
            } catch (e) {
                grid('torneosGrid', errState(e.message));
                toast('Error al cargar torneos: ' + e.message, 'danger');
            } finally {
                spinning('btnRefreshTorneos', false);
            }
        }

        function renderTorneos() {
            document.getElementById('countTorneos').textContent = torneos.length;
            if (!torneos.length) {
                grid('torneosGrid', `<div class="empty-state"><div class="empty-icon">🏆</div>
      <div class="empty-title">Sin torneos</div>
      <div class="empty-sub">Creá el primero o cambiá el filtro</div></div>`);
                return;
            }
            grid('torneosGrid', torneos.map(torneoCard).join(''));
        }

        function torneoCard(t) {
            const s = parseInt(t.estado);
            const catName = CATS[parseInt(t.categoria)] || `Categoría ${t.categoria}`;
            const tid = t.id;
            const tnombre = esc(t.nombre);
            const tdata = `data-id="${tid}" data-cat="${t.categoria}" data-fecha="${t.fecha}" data-nombre="${tnombre}" data-entre="${t.entre}"`;

            let actions = '';
            if (s === 0) {
                actions = `
      <button class="btn-action btn-cancel" ${tdata} onclick="changeTorneoEstado(this, 2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>Cerrar
      </button>
      <button class="btn-action btn-ok" ${tdata} onclick="changeTorneoEstado(this, 1)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Abrir Inscripción
      </button>`;
            } else if (s === 1) {
                actions = `
      <button class="btn-action btn-cancel" ${tdata} onclick="changeTorneoEstado(this, 2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>Cancelar
      </button>
      <button class="btn-action btn-end" ${tdata} onclick="changeTorneoEstado(this, 2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>Cerrar Torneo
      </button>`;
            } else {
                actions = `<button class="btn-action btn-done" disabled>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Torneo Cerrado
    </button>`;
            }

            return `<div class="t-card" id="tcard-${tid}">
    <div class="t-card-accent s${s}"></div>
    <div class="t-card-body">
      <div class="t-card-header">
        <div>
          <div class="t-card-name">${tnombre}</div>
          <span class="t-card-category">${catName}</span>
        </div>
        <span class="status-badge s${s}">${S_LBL[s] || s}</span>
      </div>
      <div class="t-card-meta">
        <div class="t-meta-item">
          <div class="t-meta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
          <div><div class="t-meta-label">Fecha</div><div class="t-meta-val">${fmtDate(t.fecha)}</div></div>
        </div>
        <div class="t-meta-item">
          <div class="t-meta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 1 0 0 7h5a3.5 3.5 0 1 1 0 7H6"/></svg></div>
          <div><div class="t-meta-label">Cuota</div><div class="t-meta-val">${t.entre}</div></div>
        </div>
        <div class="t-meta-item">
          <div class="t-meta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div>
          <div><div class="t-meta-label">ID</div><div class="t-meta-val">#${tid}</div></div>
        </div>
      </div>
    </div>
    <div class="t-card-actions">${actions}</div>
  </div>`;
        }

        async function changeTorneoEstado(btn, newEstado) {
            const d = btn.dataset;
            const id = d.id;
            const card = document.getElementById('tcard-' + id);
            if (card) card.querySelectorAll('.btn-action').forEach(b => b.disabled = true);

            try {
                const j = await postForm('putTorneos.php', {
                    id,
                    categoria: d.cat,
                    fecha: d.fecha,
                    nombre: d.nombre,
                    entre: d.entre,
                    estado: newEstado
                });
                if (!isOk(j)) throw new Error(j?.consultaResponse?.detalleError || 'Error');
                const msg = newEstado === 1 ? 'Inscripción abierta ✓' : 'Torneo cerrado';
                const type = newEstado === 1 ? 'info' : 'warning';
                toast(`${d.nombre}: ${msg}`, type);
                await loadTorneos();
                partLoaded = false; // invalidate participants cache
            } catch (e) {
                toast('Error: ' + e.message, 'danger');
                if (card) card.querySelectorAll('.btn-action').forEach(b => b.disabled = false);
            }
        }

        /* ─── MODAL ──────────────────────────────────────────── */
        function openModal() { document.getElementById('modalOverlay').classList.add('open'); }
        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('open');
            ['f-name', 'f-cat', 'f-date', 'f-fee'].forEach(id => document.getElementById(id).value = '');
            setBusy(false);
        }

        function setBusy(on) {
            const btn = document.getElementById('btnConfirm');
            const sp = document.getElementById('confirmSpinner');
            btn.disabled = on;
            sp.style.display = on ? 'inline-block' : 'none';
        }

        async function createTorneo() {
            const nombre = document.getElementById('f-name').value.trim();
            const categoria = document.getElementById('f-cat').value;
            const fecha = document.getElementById('f-date').value;
            const entre = document.getElementById('f-fee').value;

            if (!nombre || !categoria || !fecha || !entre) { toast('Completá todos los campos', 'warning'); return; }

            setBusy(true);
            try {
                const j = await postForm('putTorneos.php', { nombre, categoria, fecha, entre, estado: 0 });
                if (!isOk(j)) throw new Error(j?.consultaResponse?.detalleError || 'Error al crear');
                toast(`Torneo "${nombre}" creado exitosamente`, 'success');
                closeModal();
                await loadTorneos();
            } catch (e) {
                toast('Error: ' + e.message, 'danger');
                setBusy(false);
            }
        }

        /* ─── PARTICIPANTS ───────────────────────────────────── */
        async function loadParticipantes() {
            spinning('btnRefreshPart', true);
            grid('participantesGrid', loading('Cargando participantes…'));
            profileCache = {};
            partFilter = null;

            try {
                const tj = await postForm('getTorneos.php', {});
                if (!isOk(tj)) throw new Error(tj?.consultaResponse?.detalleError || 'Error');
                const allTorneos = tj.consultaResponse.torneos || [];

                if (!allTorneos.length) { participantes = []; renderParticipantes(); return; }

                const aspResults = await Promise.all(
                    allTorneos.map(t =>
                        postForm('getTorneoAspirantes.php', { idTorneo: t.id })
                            .then(j => ({ j, t })).catch(() => null)
                    )
                );

                participantes = [];
                aspResults.forEach(r => {
                    if (!r || !isOk(r.j)) return;
                    (r.j.consultaResponse.aspirantes || []).forEach(a => {
                        participantes.push({
                            id: a.id,
                            idTorneo: a.idTorneo,
                            idUsuario: a.idUsuario,
                            estado: parseInt(a.Estado),
                            torneoNombre: r.t.nombre,
                            torneoCategoria: CATS[parseInt(r.t.categoria)] || `Cat. ${r.t.categoria}`,
                        });
                    });
                });

                // Fetch profiles for all unique users in parallel
                const uniqueIds = [...new Set(participantes.map(p => p.idUsuario))];
                if (uniqueIds.length) {
                    grid('participantesGrid', loading(`Cargando perfiles (${uniqueIds.length})…`));
                    await Promise.all(
                        uniqueIds.map(id =>
                            postForm('getPerfil.php', { idPerfil: id })
                                .then(j => { if (isOk(j)) profileCache[id] = j.consultaResponse; })
                                .catch(() => null)
                        )
                    );
                }

                rebuildPartFilters();
                renderParticipantes();
                partLoaded = true;
            } catch (e) {
                grid('participantesGrid', errState(e.message));
                toast('Error al cargar participantes: ' + e.message, 'danger');
            } finally {
                spinning('btnRefreshPart', false);
            }
        }

        function renderParticipantes() {
            const visible = partFilter !== null
                ? participantes.filter(p => String(p.idTorneo) === String(partFilter))
                : participantes;
            document.getElementById('countParticipantes').textContent = participantes.length;
            if (!visible.length) {
                grid('participantesGrid', `<div class="empty-state"><div class="empty-icon">👥</div>
      <div class="empty-title">Sin participantes</div>
      <div class="empty-sub">${partFilter ? 'No hay aspirantes en este torneo' : 'No hay aspirantes registrados en ningún torneo'}</div></div>`);
                return;
            }
            grid('participantesGrid', visible.map(participanteCard).join(''));
        }

        function rebuildPartFilters() {
            const bar = document.getElementById('partFilterBar');
            // Collect unique tournaments present in participants
            const seen = {};
            participantes.forEach(p => { seen[p.idTorneo] = p.torneoNombre; });
            const entries = Object.entries(seen);

            let html = `<button class="filter-btn${partFilter === null ? ' active' : ''}" onclick="setPartFilter(null,this)">Todos los torneos</button>`;
            entries.forEach(([id, nombre]) => {
                const active = String(partFilter) === String(id) ? ' active' : '';
                html += `<button class="filter-btn${active}" onclick="setPartFilter('${id}',this)">${nombre}</button>`;
            });
            bar.innerHTML = html;
        }

        function setPartFilter(idTorneo, btn) {
            partFilter = idTorneo;
            document.querySelectorAll('#partFilterBar .filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            renderParticipantes();
        }

        function participanteCard(p) {
            const prof = profileCache[p.idUsuario] || null;
            const c = AV_CLR[parseInt(p.idUsuario) % AV_CLR.length];
            const ak = `${p.idTorneo}-${p.idUsuario}`;
            const accepted = p.estado === 1;

            const nombre = prof ? esc(prof.nombre) : `Usuario #${p.idUsuario}`;
            const celular = prof ? esc(prof.celular) : '—';
            const catId = prof ? parseInt(prof.categoria) : null;
            const catName = catId ? (CATS[catId] || `Cat. ${catId}`) : p.torneoCategoria;
            const imgFile = prof?.imgperfil;
            const imgSrc = imgFile ? `../accion/imgPerfilUser/${esc(imgFile)}` : '';

            // Avatar: photo if available, else coloured initials
            const initials = prof
                ? prof.nombre.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
                : `U${p.idUsuario}`;
            const avatarHtml = imgSrc
                ? `<div class="avatar-wrap" style="border-color:${c.bd}">
         <img src="${imgSrc}" alt="${nombre}" class="avatar-img"
              onerror="this.parentElement.innerHTML='<span style=\'color:${c.tx}\'>${initials}</span>';this.parentElement.style.background='${c.bg}'">
       </div>`
                : `<div class="avatar" style="background:${c.bg};border-color:${c.bd};color:${c.tx}">${initials}</div>`;

            const actions = accepted
                ? `<div class="accepted-badge">
         <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
         Aceptado
       </div>`
                : `<button class="btn-action btn-reject" onclick="updateAspirante('${ak}',${p.id},${p.idTorneo},${p.idUsuario},'${esc(p.torneoNombre)}',2)">
         <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Rechazar
       </button>
       <button class="btn-action btn-accept" onclick="updateAspirante('${ak}',${p.id},${p.idTorneo},${p.idUsuario},'${esc(p.torneoNombre)}',1)">
         <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>Aceptar
       </button>`;

            return `<div class="p-card" id="pcard-${ak}">
    <div class="p-card-body">
      <div class="p-card-profile">
        ${avatarHtml}
        <div>
          <div class="p-name">${nombre}</div>
          <span class="p-category-badge">${catName}</span>
        </div>
      </div>
      <div class="p-details">
        <div class="p-detail">
          <div class="p-detail-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.59 1.18h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9a16 16 0 0 0 6.29 6.29l.81-.81a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
          <span class="p-detail-label">Celular</span>
          <span class="p-detail-val">${celular}</span>
        </div>
        <div class="p-detail">
          <div class="p-detail-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg></div>
          <span class="p-detail-label">Torneo</span>
          <span class="tourney-tag">${esc(p.torneoNombre)}</span>
        </div>
        <div class="p-detail">
          <div class="p-detail-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
          <span class="p-detail-label">Estado</span>
          <span class="p-detail-val" style="color:${accepted ? 'var(--green)' : 'var(--warning)'}">
            ${accepted ? 'Aceptado' : 'Pendiente'}
          </span>
        </div>
      </div>
    </div>
    <div class="p-card-actions">${actions}</div>
  </div>`;
        }

        async function updateAspirante(ak, id, idTorneo, idUsuario, torneoNombre, estado) {
            const card = document.getElementById('pcard-' + ak);
            if (card) card.querySelectorAll('.btn-action').forEach(b => b.disabled = true);

            try {
                const j = await postForm('putTorneoAspirante.php', {
                    id,
                    idTorneo,
                    idUsuario,
                    estado
                });
                if (!isOk(j)) throw new Error(j?.consultaResponse?.detalleError || 'Error');

                if (estado === 1) {
                    // Accepted — update badge in-place
                    toast(`Usuario #${idUsuario} aceptado en "${torneoNombre}"`, 'success');
                    const p = participantes.find(x => x.idTorneo == idTorneo && x.idUsuario == idUsuario);
                    if (p) p.estado = 1;
                    if (card) {
                        card.querySelector('.p-card-actions').innerHTML =
                            `<div class="accepted-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>Aceptado</div>`;
                        const statusVal = card.querySelector('.p-detail-val[style]');
                        if (statusVal) { statusVal.style.color = 'var(--green)'; statusVal.textContent = 'Aceptado'; }
                    }
                } else {
                    // Rejected — fade and remove
                    toast(`Solicitud de Usuario #${idUsuario} rechazada`, 'danger');
                    participantes = participantes.filter(x => !(x.idTorneo == idTorneo && x.idUsuario == idUsuario));
                    if (card) {
                        card.style.transition = 'opacity .3s, transform .3s';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(.96)';
                        setTimeout(() => {
                            card.remove();
                            document.getElementById('countParticipantes').textContent = participantes.length;
                        }, 300);
                    }
                }
            } catch (e) {
                toast('Error: ' + e.message, 'danger');
                if (card) card.querySelectorAll('.btn-action').forEach(b => b.disabled = false);
            }
        }

        /* ─── ADD PARTICIPANT MODAL ──────────────────────────── */
        let allPerfiles = [];
        let selectedUser = null;

        async function openAddPartModal() {
            selectedUser = null;
            document.getElementById('ap-search').value = '';
            document.getElementById('ap-results').style.display = 'none';
            document.getElementById('ap-selected').style.display = 'none';
            document.getElementById('ap-torneo').value = '';
            setAddPartBusy(false);

            // Populate tournament selector from already-loaded torneos
            // (only non-closed ones make sense, but show all)
            const sel = document.getElementById('ap-torneo');
            sel.innerHTML = '<option value="">Seleccionar torneo</option>';
            torneos.filter(t => parseInt(t.estado) === 1).forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.nombre;
                sel.appendChild(opt);
            });

            // Pre-select active filter if set
            if (partFilter) sel.value = partFilter;

            document.getElementById('addPartOverlay').classList.add('open');

            // Lazy-load perfiles once
            if (!allPerfiles.length) {
                try {
                    const j = await postForm('getPerfiles.php', {});
                    if (isOk(j)) allPerfiles = j.consultaResponse.registros || [];
                } catch (e) {
                    toast('No se pudo cargar la lista de usuarios', 'danger');
                }
            }
        }

        function closeAddPartModal() {
            document.getElementById('addPartOverlay').classList.remove('open');
        }

        function filterPerfiles() {
            const q = document.getElementById('ap-search').value.trim().toLowerCase();
            const box = document.getElementById('ap-results');

            if (!q) { box.style.display = 'none'; return; }

            const matches = allPerfiles.filter(u =>
                u.nombre.toLowerCase().includes(q) ||
                (u.usuario || '').toLowerCase().includes(q)
            ).slice(0, 20);

            if (!matches.length) {
                box.innerHTML = `<div style="padding:12px 14px;color:var(--text-muted);font-size:13px">Sin resultados</div>`;
                box.style.display = 'block';
                return;
            }

            box.innerHTML = matches.map(u => {
                const c = AV_CLR[parseInt(u.id) % AV_CLR.length];
                const initials = u.nombre.trim().split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
                const sub = u.usuario ? `@${esc(u.usuario)} · ${CATS[parseInt(u.categoria)] || 'Cat.' + u.categoria}` : (CATS[parseInt(u.categoria)] || 'Cat.' + u.categoria);
                return `<div class="ap-user-row" onclick="selectUser(${u.id},'${esc(u.nombre)}')">
      <div class="ap-user-mini-avatar" style="background:${c.bg};border:1.5px solid ${c.bd};color:${c.tx}">${initials}</div>
      <div>
        <div class="ap-user-name">${esc(u.nombre)}</div>
        <div class="ap-user-sub">${sub}</div>
      </div>
    </div>`;
            }).join('');
            box.style.display = 'block';
        }

        function selectUser(id, nombre) {
            selectedUser = id;
            document.getElementById('ap-search').value = '';
            document.getElementById('ap-results').style.display = 'none';
            document.getElementById('ap-selected-name').textContent = nombre;
            const sel = document.getElementById('ap-selected');
            sel.style.display = 'flex';
        }

        function clearSelectedUser() {
            selectedUser = null;
            document.getElementById('ap-selected').style.display = 'none';
            document.getElementById('ap-search').focus();
        }

        function setAddPartBusy(on) {
            document.getElementById('btnAddPart').disabled = on;
            document.getElementById('addPartSpinner').style.display = on ? 'inline-block' : 'none';
        }

        async function addParticipante() {
            const idTorneo = document.getElementById('ap-torneo').value;
            if (!idTorneo) { toast('Seleccioná un torneo', 'warning'); return; }
            const torneo = torneos.find(t => String(t.id) === String(idTorneo));
            if (!torneo || parseInt(torneo.estado) !== 1) { toast('Este torneo no está abierto para inscripciones', 'warning'); return; }
            if (!selectedUser) { toast('Seleccioná un usuario', 'warning'); return; }

            setAddPartBusy(true);
            try {
                const j = await postForm('putTorneoAspirante.php', {
                    accion: 'insert',
                    idTorneo,
                    idUsuario: selectedUser,
                });
                if (!isOk(j)) throw new Error(j?.consultaResponse?.detalleError || 'Error');
                toast('Participante agregado correctamente', 'success');
                closeAddPartModal();
                partLoaded = false;
                await loadParticipantes();
            } catch (e) {
                toast('Error: ' + e.message, 'danger');
                setAddPartBusy(false);
            }
        }

        document.getElementById('addPartOverlay').addEventListener('click', e => {
            if (e.target === document.getElementById('addPartOverlay')) closeAddPartModal();
        });

        /* ─── FILTERS ────────────────────────────────────────── */
        function setFilter(estado, btn) {
            activeFilter = estado;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            loadTorneos();
        }

        /* ─── TABS ───────────────────────────────────────────── */
        function switchTab(tab, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + tab).classList.add('active');
            if (tab === 'participantes' && !partLoaded) loadParticipantes();
        }

        /* ─── UTILS ──────────────────────────────────────────── */
        function grid(id, html) { document.getElementById(id).innerHTML = html; }

        function loading(txt) {
            return `<div class="loading-state"><div class="spinner"></div><div class="loading-text">${txt}</div></div>`;
        }

        function errState(msg) {
            return `<div class="error-state"><div class="error-title">No se pudo conectar</div><div class="error-sub">${esc(msg)}</div></div>`;
        }

        function spinning(id, on) {
            document.getElementById(id).classList.toggle('spinning', on);
        }

        function fmtDate(d) {
            if (!d) return '–';
            const [y, m, day] = d.split('-');
            const M = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            return `${parseInt(day)} ${M[parseInt(m) - 1]} ${y}`;
        }

        function esc(s) {
            return String(s)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        function toast(msg, type = 'info') {
            const icons = { success: '✓', danger: '✕', warning: '!', info: 'i' };
            const el = document.createElement('div');
            el.className = `toast ${type}`;
            el.innerHTML = `<span class="toast-icon">${icons[type] || 'i'}</span><span class="toast-msg">${esc(msg)}</span>`;
            const c = document.getElementById('toastContainer');
            c.appendChild(el);
            setTimeout(() => {
                el.style.transition = 'opacity .3s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 300);
            }, 3500);
        }

        document.getElementById('modalOverlay').addEventListener('click', e => {
            if (e.target === document.getElementById('modalOverlay')) closeModal();
        });

        /* ─── INIT ───────────────────────────────────────────── */
        loadTorneos();
    </script>
</body>

</html>