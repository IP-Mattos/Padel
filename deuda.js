// ─── Inject Styles ────────────────────────────────────────────────────────────
const style = document.createElement("style");
style.textContent = `
  @import url('https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700&display=swap');

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Barlow', sans-serif;
    background-color: #061425;
    color: #e8eaf0;
    min-height: 100vh;
  }

  header {
    padding: 16px 24px;
    background: #394b6ad7;
    border-bottom: 1px solid #1e2130;
    position: sticky;
    top: 0;
    z-index: 100;
  }

  .logo-image {
    height: 36px;
    display: block;
  }

  /* ── Page title ── */
  .page-header {
    padding: 36px 32px 16px;
    display: flex;
    align-items: baseline;
    gap: 16px;
  }

  .page-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #fff;
  }

  .debt-total-badge {
    background: #e84040;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    letter-spacing: 0.05em;
  }

  /* ── Grid ── */
  .cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 16px;
    padding: 16px 32px 48px;
  }

  /* ── Card ── */
  .debt-card {
    background: #161f37;
    border: 1px solid #1e285b;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    position: relative;
    overflow: hidden;
  }

  .debt-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 3px;
    height: 100%;
    background: #e84040;
    opacity: 0;
    transition: opacity 0.18s ease;
  }

  .debt-card:hover {
    transform: translateY(-3px);
    border-color: #3d4b86;
    box-shadow: 0 8px 24px rgba(0,0,0,0.35);
  }

  .debt-card:hover::before { opacity: 1; }

  .card-name {
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .card-ci {
    font-size: 0.78rem;
    color: #6b7280;
    margin-bottom: 14px;
    letter-spacing: 0.03em;
  }

  .card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .card-phone {
    font-size: 0.78rem;
    color: #8891a4;
  }

  .card-saldo {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.35rem;
    font-weight: 700;
    color: #e84040;
    letter-spacing: 0.02em;
  }

  .card-saldo span {
    font-size: 0.7rem;
    font-weight: 500;
    color: #6b7280;
    margin-right: 2px;
  }

  /* ── Loading / Error states ── */
  .state-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 300px;
    gap: 14px;
    color: #6b7280;
    font-size: 0.95rem;
  }

  .spinner {
    width: 36px;
    height: 36px;
    border: 3px solid #1e2130;
    border-top-color: #e84040;
    border-radius: 50%;
    animation: spin 0.75s linear infinite;
  }

  @keyframes spin { to { transform: rotate(360deg); } }

  /* ── Modal overlay ── */
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(4px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    opacity: 0;
    animation: fadeIn 0.2s ease forwards;
  }

  @keyframes fadeIn { to { opacity: 1; } }

  .modal-box {
    background: #161f37;
    border: 1px solid #1e285b;
    border-radius: 16px;
    padding: 28px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.5);
    transform: translateY(12px);
    animation: slideUp 0.22s ease forwards;
  }

  @keyframes slideUp { to { transform: translateY(0); } }

  .modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 22px;
  }

  .modal-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #fff;
  }

  .modal-subtitle {
    font-size: 0.78rem;
    color: #6b7280;
    margin-top: 3px;
  }

  .modal-close {
    background: none;
    border: none;
    color: #6b7280;
    font-size: 1.4rem;
    cursor: pointer;
    line-height: 1;
    transition: color 0.15s;
    padding: 2px 6px;
  }

  .modal-close:hover { color: #fff; }

  /* ── Form ── */
  .form-group {
    margin-bottom: 16px;
  }

  .form-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 600;
    color: #8891a4;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-bottom: 7px;
  }

  .form-label .optional {
    font-weight: 400;
    color: #4b5563;
    text-transform: none;
    letter-spacing: 0;
    margin-left: 4px;
  }

  .form-input,
  .form-select,
  .form-textarea {
    width: 100%;
    background: #0f1117;
    border: 1px solid #2a2f44;
    border-radius: 8px;
    color: #e8eaf0;
    font-family: 'Barlow', sans-serif;
    font-size: 0.95rem;
    padding: 10px 14px;
    outline: none;
    transition: border-color 0.15s;
    appearance: none;
  }

  .form-input:focus,
  .form-select:focus,
  .form-textarea:focus {
    border-color: #e84040;
  }

  .form-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 36px;
    cursor: pointer;
  }

  .form-select option {
    background: #171a23;
  }

  .form-textarea {
    resize: vertical;
    min-height: 80px;
  }

  /* ── Submit button ── */
  .btn-submit {
    width: 100%;
    background: #e84040;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 12px;
    cursor: pointer;
    margin-top: 8px;
    transition: background 0.15s, transform 0.1s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .btn-submit:hover { background: #c73232; }
  .btn-submit:active { transform: scale(0.98); }
  .btn-submit:disabled { background: #4b1f1f; cursor: not-allowed; }

  .btn-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.65s linear infinite;
  }

  /* ── Toast notification ── */
  .toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    background: #1a2e1a;
    border: 1px solid #2d5a2d;
    color: #6fcf6f;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 0.88rem;
    font-weight: 500;
    z-index: 9999;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    animation: toastIn 0.25s ease forwards;
  }

  .toast.error {
    background: #2e1a1a;
    border-color: #5a2d2d;
    color: #cf6f6f;
  }

  @keyframes toastIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

  @media (max-width: 600px) {
    .cards-grid { padding: 12px 16px 40px; gap: 12px; }
    .page-header { padding: 24px 16px 12px; }
  }
`;
document.head.appendChild(style);

// ─── State ────────────────────────────────────────────────────────────────────
let currentUser = null; // { idUsuario, nombreUsuario }

// ─── Main container ───────────────────────────────────────────────────────────
const app = document.createElement("div");
app.id = "deuda-app";
document.body.appendChild(app);

// ─── Toast helper ─────────────────────────────────────────────────────────────
function showToast(msg, type = "success") {
  const t = document.createElement("div");
  t.className = `toast${type === "error" ? " error" : ""}`;
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3200);
}

// ─── Fetch & render cards ─────────────────────────────────────────────────────
async function loadCards() {
  app.innerHTML = `
    <div class="page-header">
      <h1 class="page-title">Gestión de Deudas</h1>
    </div>
    <div class="state-container"><div class="spinner"></div><span>Cargando deudas...</span></div>
  `;

  let data;
  try {
    const res = await fetch("./accion/getDeudaUsuarios.php");
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    data = await res.json();
  } catch (err) {
    app.innerHTML = `
      <div class="page-header"><h1 class="page-title">Gestión de Deudas</h1></div>
      <div class="state-container">
        <span>⚠ No se pudo cargar la información.</span>
        <small>${err.message}</small>
      </div>`;
    return;
  }

  const deudas = data?.consultaResponse?.deudas ?? [];
  const totalSaldo = deudas.reduce((sum, d) => sum + Number(d.saldo), 0);

  app.innerHTML = `
    <div class="page-header">
      <h1 class="page-title">Gestión de Deudas</h1>
      <span class="debt-total-badge">${deudas.length} usuarios · $${totalSaldo.toLocaleString("es-UY")}</span>
    </div>
    <div class="cards-grid" id="cards-container"></div>
  `;

  const grid = document.getElementById("cards-container");

  if (deudas.length === 0) {
    grid.innerHTML = `<div class="state-container"><span>No hay deudas registradas.</span></div>`;
    return;
  }

  deudas.forEach((d) => {
    const card = document.createElement("div");
    card.className = "debt-card";
    card.dataset.idUsuario = d.idUsuario;
    card.dataset.nombre = d.nombreUsuario;

    const phone = d.celularUsuario.replace(/^598/, "+598 ");
    card.innerHTML = `
      <div class="card-name">${d.nombreUsuario}</div>
      <div class="card-ci">CI ${d.cedulaUsuario}</div>
      <div class="card-footer">
        <div class="card-phone">${phone}</div>
        <div class="card-saldo"><span>$</span>${Number(d.saldo).toLocaleString("es-UY")}</div>
      </div>
    `;

    card.addEventListener("click", () =>
      openModal({
        idUsuario: d.idUsuario,
        nombreUsuario: d.nombreUsuario,
        saldo: Number(d.saldo).toLocaleString("es-UY"),
      }),
    );
    grid.appendChild(card);
  });
}

// ─── Modal ────────────────────────────────────────────────────────────────────
function openModal(user) {
  currentUser = user;

  const overlay = document.createElement("div");
  overlay.className = "modal-overlay";
  overlay.id = "cobro-modal";

  overlay.innerHTML = `
    <div class="modal-box" role="dialog" aria-modal="true">
      <div class="modal-header">
        <div>
          <div class="modal-title">Registrar Cobro</div>
          <div class="modal-subtitle">${user.nombreUsuario}</div>
          <div class="modal-subtitle">Saldo actual: $${user.saldo}</div>
        </div>
        <button class="modal-close" id="close-modal" aria-label="Cerrar">×</button>
      </div>

      <div class="form-group">
        <label class="form-label" for="monto">Monto</label>
        <input class="form-input" type="number" id="monto" min="1" placeholder="0" />
      </div>

      <div class="form-group">
        <label class="form-label" for="origen">Forma de pago</label>
        <select class="form-select" id="origen">
          <option value="" disabled selected>Seleccionar...</option>
          <option value="EFECTIVO">Efectivo</option>
          <option value="TRANS">Transferencia</option>
          <option value="MERCPAGO">Mercado Pago</option>
          <option value="DEBITO">Débito</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" for="detalle">Detalle <span class="optional">(opcional)</span></label>
        <textarea class="form-textarea" id="detalle" placeholder="Descripción del cobro..."></textarea>
      </div>

      <button class="btn-submit" id="submit-cobro">Confirmar cobro</button>
    </div>
  `;

  document.body.appendChild(overlay);

  // Close on overlay click or × button
  document.getElementById("close-modal").addEventListener("click", closeModal);
  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) closeModal();
  });

  // Close on Escape
  document.addEventListener("keydown", handleEscape);

  document
    .getElementById("submit-cobro")
    .addEventListener("click", submitCobro);
}

function closeModal() {
  const overlay = document.getElementById("cobro-modal");
  if (overlay) overlay.remove();
  document.removeEventListener("keydown", handleEscape);
  currentUser = null;
}

function handleEscape(e) {
  if (e.key === "Escape") closeModal();
}

// ─── Submit cobro ─────────────────────────────────────────────────────────────
async function submitCobro() {
  const monto = document.getElementById("monto").value.trim();
  const origen = document.getElementById("origen").value;
  const detalle = document.getElementById("detalle").value.trim();

  if (!monto) {
    showToast("Ingresá un monto válido.", "error");
    return;
  }
  if (!origen) {
    showToast("Seleccioná una forma de pago.", "error");
    return;
  }

  const btn = document.getElementById("submit-cobro");
  btn.disabled = true;
  btn.innerHTML = `<div class="btn-spinner"></div> Enviando...`;

  const body = new URLSearchParams({
    idUsuario: currentUser.idUsuario,
    monto,
    origen,
    detalle,
  });

  try {
    const res = await fetch("./accion/putDeudaCobro.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body.toString(),
    });

    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    // Optionally read response for error codes
    const data = await res.json().catch(() => null);
    const codigoError =
      data?.consultaResponse?.codigoError ?? data?.codigoError ?? "0";

    if (codigoError !== "0") {
      throw new Error(
        data?.consultaResponse?.detalleError ?? "Error en el servidor.",
      );
    }

    closeModal();
    showToast("Cobro registrado correctamente.");
    loadCards(); // Refresh grid
  } catch (err) {
    btn.disabled = false;
    btn.innerHTML = "Confirmar cobro";
    showToast(err.message || "Error al registrar el cobro.", "error");
  }
}

// ─── Init ─────────────────────────────────────────────────────────────────────
loadCards();
