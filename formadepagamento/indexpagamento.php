<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Finalizar Compra — PeçaAQ</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root {
      --red:      #e8192c;
      --red-dk:   #b01020;
      --dark:     #080808;
      --dark2:    #111111;
      --dark3:    #1a1a1a;
      --border:   rgba(255,255,255,0.07);
      --text:     #f0f0f0;
      --muted:    #888;
      --header-h: 72px;
    }

    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior:smooth; }

    body {
      background: var(--dark);
      color: var(--text);
      font-family: 'Barlow', sans-serif;
      min-height: 100vh;
      overflow-x: hidden;
    }

    ::-webkit-scrollbar { width:4px; }
    ::-webkit-scrollbar-track { background: var(--dark); }
    ::-webkit-scrollbar-thumb { background: var(--red); border-radius:2px; }

    /* ── HEADER ── */
    header {
      position: fixed;
      inset: 0 0 auto 0;
      height: var(--header-h);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 60px;
      background: rgba(8,8,8,0.94);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid var(--border);
      z-index: 999;
    }

    .logo-area {
      display: flex; align-items: center; gap: 12px; text-decoration: none;
    }
    .logo-area img { width:44px; height:44px; object-fit:contain; }
    .logo-area span {
      font-family: 'Barlow Condensed', sans-serif;
      font-size:1.5rem; font-weight:800; letter-spacing:3px; color:var(--text);
    }
    .logo-area span em { color:var(--red); font-style:normal; }

    header nav ul { display:flex; list-style:none; gap:38px; }
    header nav a {
      color:#ccc; text-decoration:none; font-size:.88rem;
      font-weight:500; letter-spacing:.5px; text-transform:uppercase;
      position:relative; transition:color .25s;
    }
    header nav a::after {
      content:''; position:absolute; left:0; bottom:-4px;
      width:0; height:2px; background:var(--red); transition:width .3s;
    }
    header nav a:hover { color:#fff; }
    header nav a:hover::after { width:100%; }

    .btn-header {
      background:var(--red); color:#fff; border:none;
      padding:10px 22px; font-family:'Barlow',sans-serif;
      font-size:.85rem; font-weight:600; letter-spacing:.5px;
      text-transform:uppercase; border-radius:4px; cursor:pointer;
      transition:background .25s, transform .2s, box-shadow .25s;
    }
    .btn-header:hover {
      background:var(--red-dk); transform:translateY(-2px);
      box-shadow:0 6px 20px rgba(232,25,44,.4);
    }

    /* ── PAGE LAYOUT ── */
    .page-wrap {
      padding-top: calc(var(--header-h) + 48px);
      padding-bottom: 80px;
      min-height: 100vh;
    }

    .page-eyebrow {
      display: flex;
      align-items: center;
      gap: 8px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .8rem;
      font-weight: 600;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--red);
      padding: 0 60px;
      margin-bottom: 10px;
    }
    .page-eyebrow::before {
      content:''; display:block; width:24px; height:2px; background:var(--red);
    }

    .page-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(2rem, 4vw, 3rem);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 0 60px;
      margin-bottom: 40px;
    }

    /* ── BREADCRUMB ── */
    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 0 60px;
      margin-bottom: 48px;
      font-size: .8rem;
      color: var(--muted);
    }
    .breadcrumb span { color: var(--text); }
    .breadcrumb i { font-size: .65rem; }

    /* ── MAIN GRID ── */
    .checkout-grid {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 32px;
      padding: 0 60px;
      max-width: 1200px;
    }

    /* ── PAYMENT PANEL ── */
    .payment-panel {
      background: var(--dark2);
      border: 1px solid var(--border);
      border-radius: 4px;
      overflow: hidden;
    }

    /* Method tabs */
    .method-tabs {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      border-bottom: 1px solid var(--border);
    }

    .method-tab {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      padding: 20px 12px;
      background: transparent;
      border: none;
      border-right: 1px solid var(--border);
      color: var(--muted);
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .85rem;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      cursor: pointer;
      transition: background .2s, color .2s;
      position: relative;
    }
    .method-tab:last-child { border-right: none; }
    .method-tab i { font-size: 1.4rem; }
    .method-tab::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 2px;
      background: var(--red);
      transform: scaleX(0);
      transition: transform .25s;
    }
    .method-tab.active {
      background: var(--dark3);
      color: var(--text);
    }
    .method-tab.active::after { transform: scaleX(1); }
    .method-tab:hover:not(.active) { background: rgba(255,255,255,.03); color: #ccc; }

    .method-content { display: none; padding: 36px; }
    .method-content.active { display: block; }

    /* ─── CARTÃO ─── */
    /* Card 3D flip preview */
    .card-preview-wrap {
      perspective: 1000px;
      margin-bottom: 32px;
    }

    .card-3d {
      width: 100%;
      max-width: 340px;
      height: 200px;
      margin: 0 auto;
      position: relative;
      transform-style: preserve-3d;
      transition: transform .6s ease;
    }
    .card-3d.flipped { transform: rotateY(180deg); }

    .card-face {
      position: absolute;
      inset: 0;
      border-radius: 16px;
      padding: 24px 28px;
      backface-visibility: hidden;
      -webkit-backface-visibility: hidden;
    }

    .card-front {
      background: linear-gradient(135deg, #1a1a1a 0%, #2d1010 50%, #1a0a0a 100%);
      border: 1px solid rgba(232,25,44,.3);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .card-back {
      background: linear-gradient(135deg, #1a0a0a 0%, #2d1010 50%, #1a1a1a 100%);
      border: 1px solid rgba(232,25,44,.3);
      transform: rotateY(180deg);
    }

    .card-chip {
      width: 38px; height: 28px;
      background: linear-gradient(135deg, #c9a84c, #f0d060, #c9a84c);
      border-radius: 5px;
    }

    .card-number-display {
      font-family: 'Share Tech Mono', monospace;
      font-size: 1.25rem;
      letter-spacing: 4px;
      color: rgba(255,255,255,.9);
    }

    .card-bottom-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }

    .card-label {
      font-size: .6rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: rgba(255,255,255,.5);
      margin-bottom: 2px;
    }

    .card-value {
      font-family: 'Share Tech Mono', monospace;
      font-size: .9rem;
      color: rgba(255,255,255,.9);
      letter-spacing: 1px;
    }

    .card-brand-logo {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.2rem;
      font-weight: 800;
      color: var(--red);
      text-align: right;
    }

    .card-stripe {
      height: 44px;
      background: rgba(0,0,0,.8);
      margin: 0 -28px;
      margin-top: 24px;
    }

    .card-cvv-area {
      margin-top: 14px;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 12px;
    }

    .card-cvv-label {
      font-size: .65rem;
      color: rgba(255,255,255,.5);
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .card-cvv-box {
      background: rgba(255,255,255,.12);
      border-radius: 4px;
      padding: 6px 16px;
      font-family: 'Share Tech Mono', monospace;
      font-size: .9rem;
      color: #fff;
      letter-spacing: 4px;
    }

    /* Form */
    .form-row { margin-bottom: 18px; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px; }
    .form-row-3 { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 14px; margin-bottom: 18px; }

    label.field-label {
      display: block;
      font-size: .75rem;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: var(--muted);
      margin-bottom: 8px;
    }

    input.field, select.field {
      width: 100%;
      background: var(--dark3);
      border: 1px solid var(--border);
      border-radius: 4px;
      padding: 12px 14px;
      color: var(--text);
      font-family: 'Barlow', sans-serif;
      font-size: .95rem;
      outline: none;
      transition: border-color .2s;
      appearance: none;
    }
    input.field:focus, select.field:focus {
      border-color: rgba(232,25,44,.5);
    }
    input.field::placeholder { color: rgba(255,255,255,.2); }

    input.field.mono { font-family: 'Share Tech Mono', monospace; letter-spacing: 2px; }

    .installment-note {
      font-size: .8rem;
      color: var(--muted);
      margin-top: 6px;
    }
    .installment-note strong { color: var(--red); }

    /* ─── PIX ─── */
    .pix-wrap {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 24px;
    }

    .pix-header {
      text-align: center;
    }

    .pix-header h3 {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.4rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 6px;
    }
    .pix-header p { font-size: .88rem; color: var(--muted); }

    .pix-timer {
      display: flex;
      align-items: center;
      gap: 8px;
      background: rgba(232,25,44,.08);
      border: 1px solid rgba(232,25,44,.2);
      border-radius: 4px;
      padding: 10px 20px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1rem;
      font-weight: 600;
      letter-spacing: 1px;
      color: var(--red);
    }

    .qr-container {
      position: relative;
      width: 200px;
      height: 200px;
      background: #fff;
      border-radius: 8px;
      padding: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .qr-container svg { width: 100%; height: 100%; }

    .pix-logo-overlay {
      position: absolute;
      width: 36px;
      height: 36px;
      background: #32bcad;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .65rem;
      font-weight: 800;
      color: #fff;
      letter-spacing: .5px;
    }

    .pix-key-box {
      width: 100%;
      background: var(--dark3);
      border: 1px solid var(--border);
      border-radius: 4px;
      padding: 14px 16px;
    }

    .pix-key-label {
      font-size: .7rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--muted);
      margin-bottom: 8px;
    }

    .pix-key-value {
      font-family: 'Share Tech Mono', monospace;
      font-size: .82rem;
      color: var(--text);
      word-break: break-all;
      line-height: 1.6;
    }

    .btn-copy {
      display: flex;
      align-items: center;
      gap: 8px;
      width: 100%;
      padding: 12px;
      background: transparent;
      border: 1px solid var(--border);
      border-radius: 4px;
      color: var(--muted);
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .85rem;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      cursor: pointer;
      transition: all .2s;
      justify-content: center;
    }
    .btn-copy:hover { border-color: rgba(232,25,44,.4); color: var(--red); }
    .btn-copy.copied { border-color: #22c55e; color: #22c55e; }

    .pix-steps {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .pix-step {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      font-size: .85rem;
      color: var(--muted);
      line-height: 1.5;
    }

    .pix-step-num {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: rgba(232,25,44,.15);
      border: 1px solid rgba(232,25,44,.3);
      color: var(--red);
      font-size: .7rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      margin-top: 1px;
    }

    /* ─── BOLETO ─── */
    .boleto-wrap {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .boleto-card {
      background: var(--dark3);
      border: 1px solid var(--border);
      border-radius: 4px;
      overflow: hidden;
    }

    .boleto-top {
      background: rgba(232,25,44,.06);
      border-bottom: 1px solid var(--border);
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .boleto-top-left {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .boleto-top-left span { color: var(--red); }

    .boleto-top-right {
      font-size: .8rem;
      color: var(--muted);
    }

    .boleto-barcode {
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .barcode-svg {
      width: 100%;
      height: 64px;
    }

    .boleto-linhas {
      display: flex;
      gap: 2px;
      height: 60px;
      overflow: hidden;
    }

    .boleto-code {
      font-family: 'Share Tech Mono', monospace;
      font-size: .78rem;
      color: var(--muted);
      text-align: center;
      word-break: break-all;
      line-height: 1.8;
      letter-spacing: 1px;
    }

    .boleto-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1px;
      background: var(--border);
      border-top: 1px solid var(--border);
    }

    .boleto-info-cell {
      background: var(--dark3);
      padding: 12px 16px;
    }

    .boleto-info-cell .lbl {
      font-size: .65rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--muted);
      margin-bottom: 3px;
    }

    .boleto-info-cell .val {
      font-size: .88rem;
      font-weight: 600;
      color: var(--text);
    }

    .boleto-warning {
      display: flex;
      gap: 10px;
      align-items: flex-start;
      background: rgba(234,179,8,.05);
      border: 1px solid rgba(234,179,8,.2);
      border-radius: 4px;
      padding: 14px 16px;
      font-size: .83rem;
      color: #a89030;
      line-height: 1.6;
    }

    .boleto-warning i { color: #ca8a04; margin-top: 2px; flex-shrink: 0; }

    /* ── BTN PRIMARY ── */
    .btn-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      padding: 15px;
      background: var(--red);
      border: none;
      border-radius: 4px;
      color: #fff;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      cursor: pointer;
      transition: background .25s, transform .2s, box-shadow .3s;
      text-decoration: none;
      margin-top: 8px;
    }
    .btn-primary:hover {
      background: var(--red-dk);
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(232,25,44,.3);
    }
    .btn-primary:active { transform: translateY(0); }

    /* ── ORDER SUMMARY ── */
    .order-summary {
      position: sticky;
      top: calc(var(--header-h) + 20px);
    }

    .summary-panel {
      background: var(--dark2);
      border: 1px solid var(--border);
      border-radius: 4px;
      overflow: hidden;
    }

    .summary-header {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .summary-header i { color: var(--red); }

    .summary-items {
      padding: 16px 24px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-height: 280px;
      overflow-y: auto;
    }

    .summary-item {
      display: flex;
      gap: 12px;
      align-items: center;
    }

    .summary-item-img {
      width: 48px;
      height: 48px;
      border-radius: 4px;
      background: var(--dark3);
      border: 1px solid var(--border);
      object-fit: cover;
      flex-shrink: 0;
    }

    .summary-item-info { flex: 1; min-width: 0; }

    .summary-item-name {
      font-size: .85rem;
      font-weight: 500;
      color: var(--text);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-bottom: 2px;
    }

    .summary-item-qty {
      font-size: .75rem;
      color: var(--muted);
    }

    .summary-item-price {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .95rem;
      font-weight: 700;
      color: var(--text);
      flex-shrink: 0;
    }

    .summary-divider {
      height: 1px;
      background: var(--border);
      margin: 0 24px;
    }

    .summary-totals {
      padding: 16px 24px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: .88rem;
    }

    .total-row .lbl { color: var(--muted); }
    .total-row .val { font-weight: 500; }

    .total-row.discount .val { color: #22c55e; }
    .total-row.shipping .val { color: #22c55e; }

    .total-final {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      padding: 16px 24px;
      border-top: 1px solid var(--border);
      background: rgba(232,25,44,.04);
    }

    .total-final .lbl {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .total-final .val {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--red);
    }

    .summary-badges {
      padding: 16px 24px;
      border-top: 1px solid var(--border);
      display: flex;
      gap: 8px;
    }

    .badge {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: .7rem;
      color: var(--muted);
      background: var(--dark3);
      border: 1px solid var(--border);
      border-radius: 3px;
      padding: 5px 9px;
    }
    .badge i { color: var(--red); font-size: .75rem; }

    /* ── SUCCESS MODAL ── */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.85);
      backdrop-filter: blur(6px);
      z-index: 2000;
      display: none;
      align-items: center;
      justify-content: center;
    }
    .modal-overlay.open { display: flex; }

    .success-modal {
      background: var(--dark2);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 48px 56px;
      max-width: 480px;
      width: 90%;
      text-align: center;
      animation: popIn .4s cubic-bezier(.34,1.56,.64,1) both;
    }

    @keyframes popIn {
      from { opacity:0; transform:scale(.85); }
      to   { opacity:1; transform:scale(1); }
    }

    .success-icon {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: rgba(34,197,94,.1);
      border: 2px solid rgba(34,197,94,.3);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      font-size: 2rem;
      color: #22c55e;
      animation: scaleIn .5s ease .2s both;
    }

    @keyframes scaleIn {
      from { transform:scale(0); }
      to   { transform:scale(1); }
    }

    .success-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 1.8rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 10px;
    }

    .success-sub { font-size: .9rem; color: var(--muted); line-height: 1.7; margin-bottom: 24px; }

    .success-code {
      background: var(--dark3);
      border: 1px solid var(--border);
      border-radius: 4px;
      padding: 14px;
      margin-bottom: 28px;
    }

    .success-code .sc-label {
      font-size: .7rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--muted);
      margin-bottom: 4px;
    }

    .success-code .sc-value {
      font-family: 'Share Tech Mono', monospace;
      font-size: 1.1rem;
      color: var(--red);
      letter-spacing: 3px;
    }

    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 28px;
      background: transparent;
      border: 1px solid var(--border);
      border-radius: 4px;
      color: var(--muted);
      font-family: 'Barlow Condensed', sans-serif;
      font-size: .9rem;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      cursor: pointer;
      text-decoration: none;
      transition: all .2s;
    }
    .btn-back:hover { border-color: var(--red); color: var(--red); }

    /* ── FOOTER ── */
    footer {
      background: #070707;
      padding: 40px 60px 24px;
      border-top: 1px solid var(--border);
      margin-top: 60px;
    }

    .footer-bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: .8rem;
      color: #555;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1024px) {
      header, .page-eyebrow, .page-title, .breadcrumb { padding-left: 30px; padding-right: 30px; }
      .checkout-grid { padding: 0 30px; grid-template-columns: 1fr; }
      .order-summary { position: static; }
      footer { padding: 30px 30px 20px; }
    }
    @media (max-width: 768px) {
      header nav { display: none; }
      .form-row-2, .form-row-3 { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<!-- HEADER -->
<header>
  <a href="../LandingPage/indexLandingPage.php" class="logo-area">
    <img src="../LandingPage/imgLandingPage/LogoPecaAq5.png" alt="Logo PeçaAQ">
    <span>PEÇA<em>AQ</em></span>
  </a>
  <nav>
    <ul>
      <li><a href="../LandingPage/indexLandingPage.php">Home</a></li>
      <li><a href="../Comprar/indexComprar.php">Comprar</a></li>
      <li><a href="../Sobre/index.php">Sobre</a></li>
      <li><a href="../lojas/lojas.php">Lojas</a></li>
    </ul>
  </nav>
  <button class="btn-header" id="headerLoginBtn">Faça seu login</button>
</header>

<!-- PAGE -->
<div class="page-wrap">

  <p class="page-eyebrow">Finalizar pedido</p>
  <h1 class="page-title">Forma de Pagamento</h1>

  <div class="breadcrumb">
    <a href="../Comprar/indexComprar.php" style="color:var(--muted);text-decoration:none">Carrinho</a>
    <i class="fas fa-chevron-right"></i>
    <span>Pagamento</span>
    <i class="fas fa-chevron-right"></i>
    <span style="color:var(--muted)">Confirmação</span>
  </div>

  <div class="checkout-grid">

    <!-- ═══ PAYMENT FORMS ═══ -->
    <div class="payment-panel">

      <!-- TABS -->
      <div class="method-tabs">
        <button class="method-tab active" data-tab="cartao">
          <i class="fas fa-credit-card"></i> Cartão
        </button>
        <button class="method-tab" data-tab="pix">
          <i class="fas fa-qrcode"></i> PIX
        </button>
        <button class="method-tab" data-tab="boleto">
          <i class="fas fa-barcode"></i> Boleto
        </button>
      </div>

      <!-- ─── CARTÃO ─── -->
      <div class="method-content active" id="tab-cartao">

        <!-- 3D Card preview -->
        <div class="card-preview-wrap">
          <div class="card-3d" id="card3d">
            <!-- FRONT -->
            <div class="card-face card-front">
              <div style="display:flex;justify-content:space-between;align-items:center">
                <div class="card-chip"></div>
                <div class="card-brand-logo" id="cardBrandDisplay">VISA</div>
              </div>
              <div class="card-number-display" id="cardNumDisplay">•••• •••• •••• ••••</div>
              <div class="card-bottom-row">
                <div>
                  <div class="card-label">Titular</div>
                  <div class="card-value" id="cardNameDisplay">NOME DO TITULAR</div>
                </div>
                <div>
                  <div class="card-label">Validade</div>
                  <div class="card-value" id="cardExpDisplay">MM/AA</div>
                </div>
              </div>
            </div>
            <!-- BACK -->
            <div class="card-face card-back">
              <div class="card-stripe"></div>
              <div class="card-cvv-area">
                <span class="card-cvv-label">CVV</span>
                <div class="card-cvv-box" id="cardCvvDisplay">•••</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="form-row">
          <label class="field-label">Número do Cartão</label>
          <input type="text" class="field mono" id="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19" autocomplete="cc-number">
        </div>

        <div class="form-row">
          <label class="field-label">Nome do Titular</label>
          <input type="text" class="field" id="cardName" placeholder="Como impresso no cartão" autocomplete="cc-name">
        </div>

        <div class="form-row-2">
          <div>
            <label class="field-label">Validade</label>
            <input type="text" class="field mono" id="cardExp" placeholder="MM/AA" maxlength="5" autocomplete="cc-exp">
          </div>
          <div>
            <label class="field-label">CVV</label>
            <input type="text" class="field mono" id="cardCvv" placeholder="•••" maxlength="4" autocomplete="cc-csc">
          </div>
        </div>

        <div class="form-row">
          <label class="field-label">Parcelas</label>
          <select class="field" id="installments">
            <option value="1">1x R$ 753,70 (à vista)</option>
            <option value="2">2x R$ 376,85 sem juros</option>
            <option value="3">3x R$ 251,23 sem juros</option>
            <option value="6">6x R$ 125,62 sem juros</option>
            <option value="12">12x R$ 66,84 com juros</option>
          </select>
          <p class="installment-note">Parcelamento sem juros até <strong>6x</strong></p>
        </div>

        <button class="btn-primary" id="payCardBtn">
          <i class="fas fa-lock"></i> Pagar com Cartão
        </button>
      </div>

      <!-- ─── PIX ─── -->
      <div class="method-content" id="tab-pix">
        <div class="pix-wrap">

          <div class="pix-header">
            <h3>Pague com <span style="color:#32bcad">PIX</span></h3>
            <p>Aprovação imediata após o pagamento</p>
          </div>

          <div class="pix-timer">
            <i class="fas fa-clock"></i>
            QR Code válido por <span id="pixTimer" style="margin-left:4px;font-size:1.1rem">30:00</span>
          </div>

          <!-- QR Code SVG gerado -->
          <div class="qr-container" id="qrContainer">
            <svg viewBox="0 0 37 37" xmlns="http://www.w3.org/2000/svg" shape-rendering="crispEdges">
              <!-- Corner TL -->
              <rect x="1" y="1" width="7" height="7" fill="#000"/>
              <rect x="2" y="2" width="5" height="5" fill="#fff"/>
              <rect x="3" y="3" width="3" height="3" fill="#000"/>
              <!-- Corner TR -->
              <rect x="29" y="1" width="7" height="7" fill="#000"/>
              <rect x="30" y="2" width="5" height="5" fill="#fff"/>
              <rect x="31" y="3" width="3" height="3" fill="#000"/>
              <!-- Corner BL -->
              <rect x="1" y="29" width="7" height="7" fill="#000"/>
              <rect x="2" y="30" width="5" height="5" fill="#fff"/>
              <rect x="3" y="31" width="3" height="3" fill="#000"/>
              <!-- Data modules (random-looking pattern) -->
              <rect x="9" y="1" width="1" height="1" fill="#000"/>
              <rect x="11" y="1" width="2" height="1" fill="#000"/>
              <rect x="14" y="1" width="1" height="1" fill="#000"/>
              <rect x="16" y="1" width="3" height="1" fill="#000"/>
              <rect x="20" y="1" width="1" height="1" fill="#000"/>
              <rect x="22" y="1" width="2" height="1" fill="#000"/>
              <rect x="9" y="3" width="2" height="1" fill="#000"/>
              <rect x="13" y="3" width="1" height="1" fill="#000"/>
              <rect x="15" y="3" width="2" height="1" fill="#000"/>
              <rect x="19" y="3" width="1" height="1" fill="#000"/>
              <rect x="21" y="3" width="3" height="1" fill="#000"/>
              <rect x="9" y="5" width="1" height="1" fill="#000"/>
              <rect x="12" y="5" width="3" height="1" fill="#000"/>
              <rect x="17" y="5" width="1" height="1" fill="#000"/>
              <rect x="20" y="5" width="2" height="1" fill="#000"/>
              <rect x="23" y="5" width="1" height="1" fill="#000"/>
              <rect x="9" y="7" width="3" height="1" fill="#000"/>
              <rect x="14" y="7" width="2" height="1" fill="#000"/>
              <rect x="18" y="7" width="1" height="1" fill="#000"/>
              <rect x="22" y="7" width="2" height="1" fill="#000"/>
              <rect x="1" y="9" width="1" height="1" fill="#000"/>
              <rect x="3" y="9" width="2" height="1" fill="#000"/>
              <rect x="7" y="9" width="1" height="1" fill="#000"/>
              <rect x="9" y="9" width="2" height="1" fill="#000"/>
              <rect x="13" y="9" width="3" height="1" fill="#000"/>
              <rect x="18" y="9" width="1" height="1" fill="#000"/>
              <rect x="20" y="9" width="2" height="1" fill="#000"/>
              <rect x="24" y="9" width="1" height="1" fill="#000"/>
              <rect x="27" y="9" width="1" height="1" fill="#000"/>
              <rect x="29" y="9" width="2" height="1" fill="#000"/>
              <rect x="33" y="9" width="2" height="1" fill="#000"/>
              <rect x="1" y="11" width="2" height="1" fill="#000"/>
              <rect x="5" y="11" width="1" height="1" fill="#000"/>
              <rect x="8" y="11" width="2" height="1" fill="#000"/>
              <rect x="12" y="11" width="1" height="1" fill="#000"/>
              <rect x="15" y="11" width="2" height="1" fill="#000"/>
              <rect x="19" y="11" width="3" height="1" fill="#000"/>
              <rect x="24" y="11" width="2" height="1" fill="#000"/>
              <rect x="28" y="11" width="1" height="1" fill="#000"/>
              <rect x="31" y="11" width="2" height="1" fill="#000"/>
              <rect x="35" y="11" width="1" height="1" fill="#000"/>
              <rect x="1" y="13" width="3" height="1" fill="#000"/>
              <rect x="6" y="13" width="2" height="1" fill="#000"/>
              <rect x="10" y="13" width="1" height="1" fill="#000"/>
              <rect x="13" y="13" width="2" height="1" fill="#000"/>
              <rect x="17" y="13" width="1" height="1" fill="#000"/>
              <rect x="20" y="13" width="3" height="1" fill="#000"/>
              <rect x="25" y="13" width="1" height="1" fill="#000"/>
              <rect x="28" y="13" width="2" height="1" fill="#000"/>
              <rect x="32" y="13" width="3" height="1" fill="#000"/>
              <rect x="36" y="13" width="1" height="1" fill="#000"/>
              <rect x="2" y="15" width="1" height="1" fill="#000"/>
              <rect x="5" y="15" width="3" height="1" fill="#000"/>
              <rect x="10" y="15" width="2" height="1" fill="#000"/>
              <rect x="14" y="15" width="1" height="1" fill="#000"/>
              <rect x="17" y="15" width="2" height="1" fill="#000"/>
              <rect x="21" y="15" width="1" height="1" fill="#000"/>
              <rect x="24" y="15" width="2" height="1" fill="#000"/>
              <rect x="27" y="15" width="3" height="1" fill="#000"/>
              <rect x="32" y="15" width="1" height="1" fill="#000"/>
              <rect x="35" y="15" width="2" height="1" fill="#000"/>
              <rect x="1" y="17" width="2" height="1" fill="#000"/>
              <rect x="4" y="17" width="1" height="1" fill="#000"/>
              <rect x="7" y="17" width="2" height="1" fill="#000"/>
              <rect x="11" y="17" width="3" height="1" fill="#000"/>
              <rect x="16" y="17" width="1" height="1" fill="#000"/>
              <rect x="19" y="17" width="2" height="1" fill="#000"/>
              <rect x="23" y="17" width="1" height="1" fill="#000"/>
              <rect x="26" y="17" width="2" height="1" fill="#000"/>
              <rect x="30" y="17" width="3" height="1" fill="#000"/>
              <rect x="35" y="17" width="1" height="1" fill="#000"/>
              <rect x="3" y="19" width="2" height="1" fill="#000"/>
              <rect x="7" y="19" width="1" height="1" fill="#000"/>
              <rect x="10" y="19" width="2" height="1" fill="#000"/>
              <rect x="14" y="19" width="3" height="1" fill="#000"/>
              <rect x="19" y="19" width="1" height="1" fill="#000"/>
              <rect x="22" y="19" width="2" height="1" fill="#000"/>
              <rect x="26" y="19" width="1" height="1" fill="#000"/>
              <rect x="29" y="19" width="3" height="1" fill="#000"/>
              <rect x="34" y="19" width="2" height="1" fill="#000"/>
              <rect x="1" y="21" width="1" height="1" fill="#000"/>
              <rect x="4" y="21" width="3" height="1" fill="#000"/>
              <rect x="9" y="21" width="1" height="1" fill="#000"/>
              <rect x="12" y="21" width="2" height="1" fill="#000"/>
              <rect x="16" y="21" width="1" height="1" fill="#000"/>
              <rect x="20" y="21" width="3" height="1" fill="#000"/>
              <rect x="25" y="21" width="2" height="1" fill="#000"/>
              <rect x="29" y="21" width="1" height="1" fill="#000"/>
              <rect x="32" y="21" width="2" height="1" fill="#000"/>
              <rect x="36" y="21" width="1" height="1" fill="#000"/>
              <rect x="2" y="23" width="2" height="1" fill="#000"/>
              <rect x="6" y="23" width="1" height="1" fill="#000"/>
              <rect x="9" y="23" width="2" height="1" fill="#000"/>
              <rect x="13" y="23" width="3" height="1" fill="#000"/>
              <rect x="18" y="23" width="1" height="1" fill="#000"/>
              <rect x="21" y="23" width="2" height="1" fill="#000"/>
              <rect x="25" y="23" width="1" height="1" fill="#000"/>
              <rect x="28" y="23" width="3" height="1" fill="#000"/>
              <rect x="33" y="23" width="2" height="1" fill="#000"/>
              <rect x="1" y="25" width="3" height="1" fill="#000"/>
              <rect x="5" y="25" width="2" height="1" fill="#000"/>
              <rect x="9" y="25" width="1" height="1" fill="#000"/>
              <rect x="12" y="25" width="2" height="1" fill="#000"/>
              <rect x="16" y="25" width="3" height="1" fill="#000"/>
              <rect x="21" y="25" width="1" height="1" fill="#000"/>
              <rect x="24" y="25" width="2" height="1" fill="#000"/>
              <rect x="28" y="25" width="1" height="1" fill="#000"/>
              <rect x="31" y="25" width="3" height="1" fill="#000"/>
              <rect x="35" y="25" width="2" height="1" fill="#000"/>
              <!-- Bottom right data area -->
              <rect x="29" y="27" width="1" height="1" fill="#000"/>
              <rect x="31" y="27" width="2" height="1" fill="#000"/>
              <rect x="34" y="27" width="1" height="1" fill="#000"/>
              <rect x="29" y="29" width="2" height="1" fill="#000"/>
              <rect x="33" y="29" width="3" height="1" fill="#000"/>
              <rect x="30" y="31" width="1" height="1" fill="#000"/>
              <rect x="32" y="31" width="2" height="1" fill="#000"/>
              <rect x="35" y="31" width="1" height="1" fill="#000"/>
              <rect x="29" y="33" width="3" height="1" fill="#000"/>
              <rect x="34" y="33" width="2" height="1" fill="#000"/>
              <rect x="30" y="35" width="2" height="1" fill="#000"/>
              <rect x="33" y="35" width="1" height="1" fill="#000"/>
              <rect x="35" y="35" width="2" height="1" fill="#000"/>
            </svg>
            <div class="pix-logo-overlay">PIX</div>
          </div>

          <div class="pix-key-box">
            <div class="pix-key-label">Código PIX — Copia e Cola</div>
            <div class="pix-key-value" id="pixCode">00020126580014br.gov.bcb.pix0136a3f1e8b2-4c9d-11ee-be56-0242ac120002520400005303986540575.705802BR5913PECAAQ LTDA6009SAO PAULO62070503***6304E1A3</div>
          </div>

          <button class="btn-copy" id="copyPixBtn">
            <i class="fas fa-copy"></i> Copiar código PIX
          </button>

          <div class="pix-steps">
            <div class="pix-step">
              <div class="pix-step-num">1</div>
              <span>Abra o app do seu banco e acesse a área PIX</span>
            </div>
            <div class="pix-step">
              <div class="pix-step-num">2</div>
              <span>Escaneie o QR code ou use o código Copia e Cola</span>
            </div>
            <div class="pix-step">
              <div class="pix-step-num">3</div>
              <span>Confirme o pagamento — aprovação é instantânea</span>
            </div>
          </div>

          <button class="btn-primary" id="payPixBtn" style="width:100%">
            <i class="fas fa-check-circle"></i> Já realizei o pagamento
          </button>

        </div>
      </div>

      <!-- ─── BOLETO ─── -->
      <div class="method-content" id="tab-boleto">
        <div class="boleto-wrap">

          <div class="boleto-card">
            <div class="boleto-top">
              <div class="boleto-top-left">Peça<span>AQ</span> Marketplace</div>
              <div class="boleto-top-right">Banco: <strong>001 — Banco do Brasil</strong></div>
            </div>
            <div class="boleto-barcode">
              <!-- Barras simuladas -->
              <div class="boleto-linhas" id="boletoLinhas"></div>
              <div class="boleto-code">001 9 34567.89012 34567.890123 4 56780000075370</div>
            </div>
            <div class="boleto-info-grid">
              <div class="boleto-info-cell">
                <div class="lbl">Beneficiário</div>
                <div class="val">PeçaAQ Marketplace LTDA</div>
              </div>
              <div class="boleto-info-cell">
                <div class="lbl">CNPJ</div>
                <div class="val">00.000.000/0001-00</div>
              </div>
              <div class="boleto-info-cell">
                <div class="lbl">Vencimento</div>
                <div class="val" id="boletoVenc">—</div>
              </div>
              <div class="boleto-info-cell">
                <div class="lbl">Valor</div>
                <div class="val" style="color:var(--red)">R$ 753,70</div>
              </div>
              <div class="boleto-info-cell" style="grid-column:1/-1">
                <div class="lbl">Pagador</div>
                <div class="val" id="boletoNome">Carregando...</div>
              </div>
            </div>
          </div>

          <button class="btn-copy" id="copyBoletoBtn">
            <i class="fas fa-copy"></i> Copiar linha digitável
          </button>

          <div class="boleto-warning">
            <i class="fas fa-triangle-exclamation"></i>
            <span>Boleto pode levar até <strong>3 dias úteis</strong> para compensar. Não pague após o vencimento. O pedido só será processado após a confirmação do pagamento.</span>
          </div>

          <button class="btn-primary" id="payBoletoBtn">
            <i class="fas fa-barcode"></i> Gerar e Baixar Boleto
          </button>

        </div>
      </div>
    </div>

    <!-- ═══ ORDER SUMMARY ═══ -->
    <div class="order-summary">
      <div class="summary-panel">

        <div class="summary-header">
          <i class="fas fa-receipt"></i> Resumo do Pedido
        </div>

        <div class="summary-items" id="summaryItems">
          <!-- populated by JS -->
        </div>

        <div class="summary-divider"></div>

        <div class="summary-totals">
          <div class="total-row">
            <span class="lbl">Subtotal</span>
            <span class="val">R$ 803,70</span>
          </div>
          <div class="total-row discount">
            <span class="lbl">Desconto</span>
            <span class="val">− R$ 50,00</span>
          </div>
          <div class="total-row shipping">
            <span class="lbl">Frete</span>
            <span class="val">Grátis</span>
          </div>
        </div>

        <div class="total-final">
          <span class="lbl">Total</span>
          <span class="val">R$ 753,70</span>
        </div>

        <div class="summary-badges">
          <div class="badge"><i class="fas fa-shield-halved"></i> Compra segura</div>
          <div class="badge"><i class="fas fa-truck"></i> Frete grátis</div>
          <div class="badge"><i class="fas fa-rotate-left"></i> 30 dias</div>
        </div>

      </div>
    </div>

  </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal-overlay" id="successModal">
  <div class="success-modal">
    <div class="success-icon">
      <i class="fas fa-check"></i>
    </div>
    <h2 class="success-title">Pedido Confirmado!</h2>
    <p class="success-sub">Seu pagamento foi processado com sucesso. Em breve você receberá a confirmação por e-mail.</p>
    <div class="success-code">
      <div class="sc-label">Número do Pedido</div>
      <div class="sc-value" id="orderCode">—</div>
    </div>
    <a href="../LandingPage/indexLandingPage.php" class="btn-back">
      <i class="fas fa-arrow-left"></i> Voltar para a loja
    </a>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="footer-bottom">
    <span>© 2025 PeçaAQ. Todos os direitos reservados.</span>
    <span>Feito com <span style="color:var(--red)">♥</span> por estudantes de TI</span>
  </div>
</footer>

<script>
(function () {

  /* ── HEADER LOGIN ── */
  const loginBtn = document.getElementById('headerLoginBtn');
  const userData = localStorage.getItem('usuarioLogado');
  if (userData) {
    try {
      const u = JSON.parse(userData);
      loginBtn.textContent = 'Meu Perfil';
      loginBtn.onclick = () => window.location.href =
        (u.tipo && u.tipo.toLowerCase() !== 'cliente')
          ? '../dashboard_empresa.php'
          : '../dashboard_cliente.php?tab=perfil';
    } catch(e) {}
  } else {
    loginBtn.onclick = () => window.location.href = '../login/indexLogin.php';
  }

  /* ── TABS ── */
  document.querySelectorAll('.method-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.method-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.method-content').forEach(c => c.classList.remove('active'));
      tab.classList.add('active');
      document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
    });
  });

  /* ── CARD PREVIEW ── */
  const numInput  = document.getElementById('cardNumber');
  const nameInput = document.getElementById('cardName');
  const expInput  = document.getElementById('cardExp');
  const cvvInput  = document.getElementById('cardCvv');
  const card3d    = document.getElementById('card3d');

  function formatCardNum(v) {
    return v.replace(/\D/g,'').slice(0,16).replace(/(.{4})/g,'$1 ').trim();
  }

  numInput.addEventListener('input', e => {
    e.target.value = formatCardNum(e.target.value);
    const raw = e.target.value.replace(/\s/g,'');
    const display = raw.padEnd(16,'•').replace(/(.{4})/g,'$1 ').trim();
    document.getElementById('cardNumDisplay').textContent = display;
    // detect brand
    let brand = 'VISA';
    if (/^4/.test(raw)) brand = 'VISA';
    else if (/^5[1-5]/.test(raw)) brand = 'MASTER';
    else if (/^3[47]/.test(raw)) brand = 'AMEX';
    else if (/^6/.test(raw)) brand = 'ELO';
    document.getElementById('cardBrandDisplay').textContent = brand;
  });

  nameInput.addEventListener('input', e => {
    document.getElementById('cardNameDisplay').textContent =
      e.target.value.toUpperCase() || 'NOME DO TITULAR';
  });

  expInput.addEventListener('input', e => {
    let v = e.target.value.replace(/\D/g,'');
    if (v.length >= 3) v = v.slice(0,2) + '/' + v.slice(2,4);
    e.target.value = v;
    document.getElementById('cardExpDisplay').textContent = v || 'MM/AA';
  });

  cvvInput.addEventListener('focus', () => card3d.classList.add('flipped'));
  cvvInput.addEventListener('blur',  () => card3d.classList.remove('flipped'));
  cvvInput.addEventListener('input', e => {
    document.getElementById('cardCvvDisplay').textContent =
      e.target.value || '•••';
  });

  /* ── PIX TIMER ── */
  let pixSeconds = 30 * 60;
  const timerEl = document.getElementById('pixTimer');
  const pixInterval = setInterval(() => {
    pixSeconds--;
    if (pixSeconds <= 0) { clearInterval(pixInterval); timerEl.textContent = 'Expirado'; return; }
    const m = Math.floor(pixSeconds / 60).toString().padStart(2,'0');
    const s = (pixSeconds % 60).toString().padStart(2,'0');
    timerEl.textContent = m + ':' + s;
  }, 1000);

  /* ── COPY PIX ── */
  document.getElementById('copyPixBtn').addEventListener('click', function() {
    const code = document.getElementById('pixCode').textContent;
    navigator.clipboard.writeText(code).then(() => {
      this.innerHTML = '<i class="fas fa-check"></i> Copiado!';
      this.classList.add('copied');
      setTimeout(() => {
        this.innerHTML = '<i class="fas fa-copy"></i> Copiar código PIX';
        this.classList.remove('copied');
      }, 2500);
    });
  });

  /* ── COPY BOLETO ── */
  document.getElementById('copyBoletoBtn').addEventListener('click', function() {
    navigator.clipboard.writeText('001934567.89012 34567.890123 4 56780000075370').then(() => {
      this.innerHTML = '<i class="fas fa-check"></i> Copiado!';
      this.classList.add('copied');
      setTimeout(() => {
        this.innerHTML = '<i class="fas fa-copy"></i> Copiar linha digitável';
        this.classList.remove('copied');
      }, 2500);
    });
  });

  /* ── BOLETO BARCODE (visual) ── */
  const linhas = document.getElementById('boletoLinhas');
  if (linhas) {
    for (let i = 0; i < 120; i++) {
      const bar = document.createElement('div');
      const w = Math.random() > 0.6 ? 2 : 1;
      bar.style.cssText = `width:${w}px;background:${Math.random()>0.45?'#fff':'#080808'};flex-shrink:0;`;
      linhas.appendChild(bar);
    }
  }

  /* ── BOLETO VENCIMENTO ── */
  const vencEl = document.getElementById('boletoVenc');
  if (vencEl) {
    const d = new Date();
    d.setDate(d.getDate() + 3);
    vencEl.textContent = d.toLocaleDateString('pt-BR');
  }

  /* ── BOLETO NOME ── */
  const nomeEl = document.getElementById('boletoNome');
  if (nomeEl && userData) {
    try { nomeEl.textContent = JSON.parse(userData).nome || 'Cliente PeçaAQ'; }
    catch(e) { nomeEl.textContent = 'Cliente PeçaAQ'; }
  } else if (nomeEl) { nomeEl.textContent = 'Cliente PeçaAQ'; }

  /* ── ORDER SUMMARY (carrinho do localStorage) ── */
  const summaryEl = document.getElementById('summaryItems');
  const cart = JSON.parse(localStorage.getItem('pecaaqCart') || '[]');
  const fallbackItems = [
    { name: 'Amortecedor Monroe', qty: 1, price: 'R$ 189,90' },
    { name: 'Bateria Moura 60Ah', qty: 1, price: 'R$ 459,90' },
    { name: 'Velas NGK Platinum', qty: 2, price: 'R$ 51,95' },
  ];
  const items = cart.length ? cart : fallbackItems;
  summaryEl.innerHTML = items.map(item => `
    <div class="summary-item">
      <div class="summary-item-img" style="background:var(--dark3);display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:.7rem">
        <i class="fas fa-cog"></i>
      </div>
      <div class="summary-item-info">
        <div class="summary-item-name">${item.name || item.nome || 'Produto'}</div>
        <div class="summary-item-qty">Qtd: ${item.qty || item.quantidade || 1}</div>
      </div>
      <div class="summary-item-price">${item.price || item.preco || '—'}</div>
    </div>
  `).join('');

  /* ── SUCCESS MODAL TRIGGER ── */
  function showSuccess() {
    const code = 'PAQ-' + Math.random().toString(36).slice(2,8).toUpperCase();
    document.getElementById('orderCode').textContent = code;
    document.getElementById('successModal').classList.add('open');
  }

  document.getElementById('payCardBtn').addEventListener('click', () => {
    const num  = numInput.value.replace(/\s/g,'');
    const name = nameInput.value.trim();
    const exp  = expInput.value;
    const cvv  = cvvInput.value;
    if (num.length < 16 || !name || exp.length < 5 || cvv.length < 3) {
      alert('Por favor, preencha todos os dados do cartão corretamente.');
      return;
    }
    showSuccess();
  });

  document.getElementById('payPixBtn').addEventListener('click', showSuccess);
  document.getElementById('payBoletoBtn').addEventListener('click', showSuccess);

})();
</script>
</body>
</html>