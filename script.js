let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const totalSlides = slides.length;

// Mostrar solo la imagen activa
function updateSlidePosition() {
    slides.forEach((slide, index) => {
        if (index === currentSlide) {
            slide.classList.add('active');
        } else {
            slide.classList.remove('active');
        }
    });
}

// Cambiar la imagen del carrusel
function changeSlide(direction) {
    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
    updateSlidePosition();
}

// Cambiar automáticamente cada 5 segundos
setInterval(() => {
    changeSlide(1);
}, 5000);

// Inicializar el primer slide
updateSlidePosition();

// Para menú hamburguesa
function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
}

// Mostrar nombre usuarios registrados o que hayan iniciado sesion
document.addEventListener('DOMContentLoaded', function () {
  const nombre = localStorage.getItem('nombre_usuario');
  const usuarioInfo = document.getElementById('usuario-info');
  const nombreSpan = document.getElementById('nombre-usuario');
  const btnLogin = document.querySelector('.btn.login');
  const btnRegistro = document.querySelector('.btn.register');
  const btnCerrarSesion = document.getElementById('cerrar-sesion');

  if (nombre) {
    // Mostrar saludo
    usuarioInfo.style.display = 'block';
    nombreSpan.textContent = nombre;

    // Ocultar botones de login y registro
    if (btnLogin) btnLogin.style.display = 'none';
    if (btnRegistro) btnRegistro.style.display = 'none';
  }

  // Cerrar sesión
  if (btnCerrarSesion) {
    btnCerrarSesion.addEventListener('click', function () {
      localStorage.removeItem('nombre_usuario');
      localStorage.removeItem('usuario_id');
      location.reload(); // recarga la página para actualizar la vista
    });
  }
});


/* Registro */
document.addEventListener('DOMContentLoaded', function () {
  const registroForm = document.getElementById('form-registro');

  if (registroForm) {
    registroForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      // Limpiar errores anteriores
      document.querySelectorAll('.error-text').forEach(el => el.remove());

      const datos = Object.fromEntries(new FormData(this));

      // Validación rápida en frontend
      const camposObligatorios = ['nombre', 'apellido', 'email', 'nombre_usuario', 'password', 'rol'];
      for (const campo of camposObligatorios) {
        if (!datos[campo]) {
          mostrarErrorCampo(campo, `El campo "${campo}" es obligatorio.`);
          return;
        }
      }

      try {
        const res = await fetch('http://localhost:8000/api/registro', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(datos)
        });

        const resultado = await res.json();

        if (res.ok) {
          if (res.ok) {
            alert('Registro exitoso');
            localStorage.setItem('nombre_usuario', resultado.nombre_usuario);
            localStorage.setItem('rol', resultado.rol);

            if (resultado.rol === 'administrador') {
              window.location.href = 'adminPanel.html';
            } else {
              window.location.href = 'index.html';
            }
          }
        } else if (res.status === 422 && resultado.errores) {
          mostrarErrores(resultado.errores);
        } else {
          alert(resultado.error || 'Error en el registro');
        }
      } catch (error) {
        console.error('Error inesperado:', error);
        alert('Error inesperado en el servidor. Revisa el log de Laravel.');
      }
    });
  } else {
    console.warn('Formulario de registro no encontrado');
  }

  function mostrarErrores(errores) {
    for (const campo in errores) {
      const input = document.querySelector(`[name="${campo}"]`);
      if (input) {
        errores[campo].forEach(msg => {
          const error = document.createElement('p');
          error.classList.add('error-text');
          error.textContent = msg;
          input.parentElement.appendChild(error);
        });
      }
    }
  }

  function mostrarErrorCampo(campo, mensaje) {
    const input = document.querySelector(`[name="${campo}"]`);
    if (input) {
      const error = document.createElement('p');
      error.classList.add('error-text');
      error.textContent = mensaje;
      input.parentElement.appendChild(error);
    }
  }
});

// Login con verificación
document.addEventListener('DOMContentLoaded', function () {
  const loginForm = document.getElementById('form-login');
  if (loginForm) {
    loginForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      const datos = Object.fromEntries(new FormData(loginForm));

      const res = await fetch('http://localhost:8000/api/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
      });

      const resultado = await res.json();
      if (res.ok) {
        alert(`Tu código de verificación es: ${resultado.codigo}`);

        localStorage.setItem('usuario_id', resultado.usuario_id);
        localStorage.setItem('rol', resultado.rol);

        window.location.href = 'verificacionDosPasos.html';
      }
      else {
        alert(resultado.error);
      }
    });
  }
});

async function verificarCodigo(usuario_id, codigo) {
  const res = await fetch('http://localhost:8000/api/verificar-codigo', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ usuario_id, codigo: String(codigo).trim() })
  });

  const resultado = await res.json();
  console.log('Respuesta backend:', resultado); // ✅ debug

  if (res.ok) {
    // Guardar nombre_usuario si viene en la respuesta
    if (resultado.nombre_usuario) {
      localStorage.setItem('nombre_usuario', resultado.nombre_usuario);
    }

    alert(resultado.mensaje || 'Inicio de sesión exitoso');
    window.location.href = 'index.html';
  } else {
    alert(resultado.error || 'Error verificando el código');
  }
}

//inventario
document.addEventListener('DOMContentLoaded', function () {
  const formProducto = document.getElementById('form-producto');
  const tablaBody = document.querySelector('#tabla-productos tbody');
  const formPedido = document.getElementById('form-pedido');
  const qrContainer = document.getElementById('qr-container');

  // ===================== PEDIDOS =====================
  if (formPedido && qrContainer) {
    formPedido.addEventListener('submit', async function (e) {
      e.preventDefault();
      const datos = Object.fromEntries(new FormData(this));

      const res = await fetch('http://localhost:8000/api/pedidos', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          cliente: datos.cliente,
          fecha: datos.fecha,
          estado: datos.estado,
          total: 0 // se actualiza luego
        })
      });

      const pedido = await res.json();
      if (res.ok) {
        alert('✅ Pedido creado');
        generarQR(pedido.idPedido, pedido.total);
      } else {
        alert(pedido.error || '❌ Error al crear pedido');
      }
    });

    async function generarQR(idPedido, total) {
      const res = await fetch('http://localhost:8000/api/qr', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_pedido: idPedido, monto: total })
      });

      const qr = await res.json();
      if (res.ok) {
        qrContainer.innerHTML = `<img src="${qr.imagen}" alt="QR de pago" />`;
      } else {
        alert(qr.error || '❌ Error generando QR');
      }
    }
  }

  // ===================== PRODUCTOS =====================
  if (!formProducto || !tablaBody) return;

  // Crear producto con imagen
  formProducto.addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(formProducto);

    const res = await fetch('http://localhost:8000/api/productos', {
      method: 'POST',
      body: formData
    });

    const resultado = await res.json();
    console.log('Respuesta backend:', resultado);

    if (res.ok) {
      alert('✅ Producto creado');
      formProducto.reset();
      cargarProductos();
    } else {
      alert(resultado.error || '❌ Error creando producto');
    }
  });

  // Cargar productos en la tabla
  async function cargarProductos() {
    try {
      const res = await fetch('http://localhost:8000/api/productos');
      const productos = await res.json();

      tablaBody.innerHTML = '';
      productos.forEach(p => {
        tablaBody.innerHTML += `
  <tr>
    <td>${p.idProducto}</td>
    <td>
      ${p.imagen 
        ? `<img src="http://localhost:8000/storage/productos/${p.imagen}" style="width:60px;">` 
        : 'Sin imagen'}
    </td>
    <td>${p.nombre}</td>
    <td>Bs ${p.precio}</td>
    <td>${p.stock}</td>
    <td>${p.categoria || '-'}</td>
    <td>${p.activo ? 'Sí' : 'No'}</td>
    <td>
      <button onclick="editarProducto(${p.idProducto})">Editar</button>
      <button onclick="eliminarProducto(${p.idProducto})">Eliminar</button>
      <button onclick="toggleActivo(${p.idProducto}, ${p.activo})">
        ${p.activo ? 'Desactivar' : 'Activar'}
      </button>
    </td>
  </tr>
`;

      });
    } catch (error) {
      console.error('Error al cargar productos:', error);
    }
  }

  // Eliminar producto
  window.eliminarProducto = async function (id) {
    if (confirm('¿Seguro que deseas eliminar este producto?')) {
      const res = await fetch(`http://localhost:8000/api/productos/${id}`, {
        method: 'DELETE'
      });
      const resultado = await res.json();
      alert(resultado.mensaje);
      cargarProductos();
    }
  };

  // Activar/Desactivar producto
  window.toggleActivo = async function (id, estadoActual) {
    const nuevoEstado = !estadoActual;
    const res = await fetch(`http://localhost:8000/api/productos/${id}/estado`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ activo: nuevoEstado })
    });

    const resultado = await res.json();
    alert(resultado.mensaje || 'Estado actualizado');
    cargarProductos();
  };

  




  // Editar producto (versión básica con prompt)
  window.editarProducto = async function (id) {
    const nuevoNombre = prompt('Nuevo nombre:');
    const nuevoPrecio = prompt('Nuevo precio:');
    const nuevoStock = prompt('Nuevo stock:');

    if (!nuevoNombre || !nuevoPrecio || !nuevoStock) return;

    const res = await fetch(`http://localhost:8000/api/productos/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        nombre: nuevoNombre,
        precio: parseFloat(nuevoPrecio),
        stock: parseInt(nuevoStock)
      })
    });

    const resultado = await res.json();
    alert(resultado.mensaje || 'Producto actualizado');
    cargarProductos();
  };

  // Inicializar tabla
  cargarProductos();
});






let carrito = [];

document.addEventListener('DOMContentLoaded', async function () {
  // Mostrar nombre de usuario
  const nombre = localStorage.getItem('nombre_usuario');
  const nombreSpan = document.getElementById('nombre-usuario');
  const usuarioInfo = document.getElementById('usuario-info');
  if (nombre && nombreSpan && usuarioInfo) {
    nombreSpan.textContent = nombre;
    usuarioInfo.style.display = 'block';
  }

  // Activar botones estáticos
  const botonesAgregar = document.querySelectorAll('.btn-agregar');
  botonesAgregar.forEach(btn => {
    btn.addEventListener('click', function () {
      const card = this.closest('.product-card');
      const id = parseInt(card.dataset.id);
      const nombre = card.dataset.nombre;
      const precio = parseFloat(card.dataset.precio);

      const existente = carrito.find(p => p.idProducto === id);
      if (existente) {
        existente.cantidad += 1;
      } else {
        carrito.push({ idProducto: id, nombre, precio, cantidad: 1 });
      }

      alert(`${nombre} agregado al carrito`);
    });
  });

  // Cargar productos dinámicos
  const grid = document.querySelector('.product-grid');
  try {
    const res = await fetch('http://localhost:8000/api/productos');
    const productos = await res.json();

const nombresHTML = Array.from(document.querySelectorAll('.product-card'))
  .map(card => card.dataset.nombre?.trim().toLowerCase());

    productos
      .filter(p => p.activo && !nombresHTML.includes(p.nombre.trim().toLowerCase()))
      .forEach(p => {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.setAttribute('data-id', p.idProducto);
        card.setAttribute('data-nombre', p.nombre);
        card.setAttribute('data-precio', p.precio);

        card.innerHTML = `
          <img src="${p.imagen 
            ? `http://localhost:8000/storage/productos/${p.imagen}` 
            : 'img/default.png'}" alt="${p.nombre}" />
          <p>${p.nombre}</p>
          <button class="btn-agregar">Agregar al carrito</button>
        `;

        card.querySelector('.btn-agregar').addEventListener('click', function () {
          const id = parseInt(p.idProducto);
          const nombre = p.nombre;
          const precio = parseFloat(p.precio);

          const existente = carrito.find(prod => prod.idProducto === id);
          if (existente) {
            existente.cantidad += 1;
          } else {
            carrito.push({ idProducto: id, nombre, precio, cantidad: 1 });
          }

          alert(`${nombre} agregado al carrito`);
        });

        grid.appendChild(card);
      });
  } catch (error) {
    console.error('Error cargando productos:', error);
  }
});




function mostrarCarrito() {
  const modal = document.getElementById('modal-carrito');
  const tbody = document.querySelector('#tabla-carrito tbody');
  const totalSpan = document.getElementById('total-carrito');
  tbody.innerHTML = '';
  let total = 0;

  carrito.forEach((p, i) => {
    const fila = document.createElement('tr');
    fila.innerHTML = `
      <td>${p.nombre}</td>
      <td><input type="number" min="1" value="${p.cantidad}" onchange="actualizarCantidad(${i}, this.value)" /></td>
      <td>${p.precio} Bs</td>
      <td>${p.precio * p.cantidad} Bs</td>
      <td><button onclick="eliminarProducto(${i})">❌</button></td>
    `;
    tbody.appendChild(fila);
    total += p.precio * p.cantidad;
  });

  totalSpan.textContent = `Total: ${total} Bs`;
  modal.style.display = 'flex';
}

function cancelarPedido() {
  const modal = document.getElementById('modal-carrito');
  modal.style.display = 'none';
  carrito = [];
  // si tienes una función que pinta la tabla, asegúrate de vaciarla:
  const tbody = document.querySelector('#tabla-carrito tbody');
  const totalSpan = document.getElementById('total-carrito');
  if (tbody) tbody.innerHTML = '';
  if (totalSpan) totalSpan.textContent = 'Total: 0 Bs';
}


function actualizarCantidad(index, nuevaCantidad) {
  carrito[index].cantidad = parseInt(nuevaCantidad);
  mostrarCarrito();
}

function eliminarProducto(index) {
  carrito.splice(index, 1);
  mostrarCarrito();
}

async function confirmarPedido() {
  try {
    if (carrito.length === 0) {
      alert("Tu carrito está vacío.");
      return;
    }

    const total = carrito.reduce((sum, p) => sum + p.precio * p.cantidad, 0);
    const idCliente = 1; // ajusta según tu login

    // 1) Crear pedido
    const resPedido = await fetch('http://localhost:8000/api/pedidos', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        fecha: new Date().toISOString().slice(0, 10),
        estado: 'pendiente',
        total: total,
        idCliente: idCliente,
        activo: true
      })
    });

    if (!resPedido.ok) {
      const err = await resPedido.json().catch(() => ({}));
      console.error('Error pedido:', err);
      alert('No se pudo crear el pedido.');
      return;
    }

    const dataPedido = await resPedido.json();
console.log('Respuesta del pedido:', dataPedido);
    const idPedido = dataPedido.idPedido;
    if (!idPedido) {
      console.error('Respuesta inesperada:', dataPedido);
      alert('No se obtuvo idPedido.');
      return;
    }

    // 2) Crear detalles
   for (const producto of carrito) {
  const resDetalle = await fetch('http://localhost:8000/api/detallepedido', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      idPedido: idPedido,
      idProducto: producto.idProducto,
      cantidad: producto.cantidad
    })
  });

  const dataDetalle = await resDetalle.json().catch(() => ({}));

  if (!resDetalle.ok) {
    console.error('Error al registrar producto:', dataDetalle);
    alert(`❌ No se pudo registrar el producto: ${producto.nombre}`);
    return;
  }

  console.log('✅ Producto registrado:', dataDetalle);
}


    // 3) Generar QR
    const resQR = await fetch('http://localhost:8000/api/qr', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ idPedido: idPedido })
});

const dataQR = await resQR.json().catch(() => ({}));

if (resQR.ok && dataQR.imagen) {
  document.getElementById('qrContainer').src = dataQR.imagen;
  alert("✅ Pedido confirmado. Escanea el QR para pagar.");
  carrito = [];
  cancelarPedido();
} else {
  console.error('❌ Error al generar QR:', dataQR);
  alert("❌ No se pudo generar el QR. Revisa si el pedido tiene productos.");
}
  } catch (e) {
    console.error('Error inesperado:', e);
    alert('Ocurrió un error inesperado. Revisa la consola.');
  }

  async function consultarHistorial(idUsuario) {
  try {
    const res = await fetch(`http://localhost:8000/api/usuarios/${idUsuario}/pedidos`);
    const pedidos = await res.json();

    if (!res.ok || !Array.isArray(pedidos)) {
      alert(pedidos.mensaje || "No se pudo obtener el historial.");
      return;
    }

    const contenedor = document.getElementById('historialContainer');
    contenedor.innerHTML = "<h3>Historial de pedidos</h3>";

    pedidos.forEach(pedido => {
      contenedor.innerHTML += `
        <div class="pedido-card">
          <p><strong>Pedido #${pedido.idPedido}</strong> - Fecha: ${pedido.fecha} - Estado: ${pedido.estado}</p>
          <ul>
            ${pedido.detalles.map(d => `
              <li>${d.producto.nombre} x ${d.cantidad} = Bs ${d.subTotal}</li>
            `).join('')}
          </ul>
          ${pedido.qr ? `<img src="/qr/qr_${pedido.idPedido}.svg" style="width:100px;">` : ''}
        </div>
      `;
    });
  } catch (e) {
    console.error("Error consultando historial:", e);
    alert("Error inesperado al consultar historial.");
  }
}
}

