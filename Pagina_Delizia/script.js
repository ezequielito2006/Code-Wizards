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
          alert('Registro exitoso');
          localStorage.setItem('nombre_usuario', resultado.nombre_usuario);
          window.location.href = 'index.html';
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

        // Guardamos el usuario_id en localStorage
        localStorage.setItem('usuario_id', resultado.usuario_id);

        // Redirigimos a la página de verificación
        window.location.href = 'verificacionDosPasos.html';
      } else {
        alert(resultado.error);
      }
    });
  }
});

async function verificarCodigo(usuario_id, codigo) {
  const res = await fetch('http://localhost:8000/api/verificar-codigo', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ usuario_id, codigo })
  });

  const resultado = await res.json();
  if (res.ok) {
    alert('Inicio de sesión exitoso');
  } else {
    alert(resultado.error);
  }
}
