const pointSlotsContainer = document.getElementById("container");

async function fetchProfile(userId) {
  const params = new URLSearchParams();
  params.append("idPerfil", userId);

  const res = await fetch("./accion/getPerfil.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params,
  });

  const profileData = await res.json();
  return profileData.consultaResponse;
}

function getProfileImage(pic) {
  const DEFAULT_IMG = "./accion/imgPerfilUser/profile.png";

  const valid =
    pic &&
    pic.trim() !== "" &&
    pic !== "0" &&
    pic.toLowerCase() !== "profile.png" &&
    pic.toLowerCase() !== "default.png";

  return valid ? `./accion/imgPerfilUser/${pic}` : DEFAULT_IMG;
}

function attachHandlers() {
  document.querySelectorAll(".cancel-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = btn.dataset.id;
      if (!id) return;

      const result = await Swal.fire({
        title: "¿Cancelar este canje?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cancelar",
        cancelButtonText: "No, mantener",
      });

      if (result.isConfirmed) {
        await updateCanje("putCanjeCancel.php", id);
      }
    });
  });

  document.querySelectorAll(".confirm-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = btn.dataset.id;
      if (!id) return;

      const result = await Swal.fire({
        title: "¿Confirmar canje?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, confirmar",
        cancelButtonText: "No, volver",
      });

      if (result.isConfirmed) {
        await updateCanje("putCanjeConfirm.php", id);
      }
    });
  });
}

async function updateCanje(url, idCanje) {
  const params = new URLSearchParams();
  params.append("idCanje", idCanje);

  try {
    const res = await fetch(`./accion/${url}`, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params,
    });

    const result = await res.json();

    if (result.consultaResponse?.codigoError === "0") {
      await Swal.fire({
        icon: "success",
        title: "Éxito",
        text: "La acción se realizó correctamente.",
        timer: 1500,
        showConfirmButton: false,
      });
      loadSlots();
    } else {
      Swal.fire("Error", "No se pudo modificar el canje.", "error");
    }
  } catch (error) {
    console.error("Error updating exchange:", error);
    Swal.fire("Error", "Ocurrió un error en la conexión.", "error");
  }
}

async function loadSlots() {
  pointSlotsContainer.innerHTML = "";

  try {
    const response = await fetch("./accion/getCanjeUser.php", {
      method: "POST",
    });

    const data = await response.json();
    const slots = data.consultaResponse.datos || [];

    for (const slot of slots) {
      const div = document.createElement("div");
      div.className = "card";

      const profile = await fetchProfile(slot.usuario);
      const img = getProfileImage(profile.imgperfil);

      div.innerHTML = `
        <div class="profiles-container">
          <div class="profile">
            <img class="profile-img" src="${img}" alt="${profile.nombre}"
              onerror="this.src='./accion/imgPerfilUser/profile.png'">
            <p>${profile.nombre}</p>
            <p>${profile.celular.replace(598, 0)}</p>
            <p>Puntos: <strong>${slot.puntos}</strong></p>
          </div>
        </div>

        <div class="actions">
          <img class="card-ico cancel-btn" src="./img/cancelar.png" data-id="${slot.id}" alt="Cancelar">
          <img class="card-ico confirm-btn" src="./img/confirmar.png" data-id="${slot.id}" alt="Confirmar">
        </div>
      `;

      pointSlotsContainer.appendChild(div);
    }

    attachHandlers();
  } catch (error) {
    console.error("Error loading slots:", error);
    pointSlotsContainer.innerHTML = "<p>No se encontraron horarios.</p>";
  }
}

loadSlots();
