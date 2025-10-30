document.addEventListener('DOMContentLoaded', () => {
  resaltarTb();
  redirTb();
  validarFrm();
});

//botones iluminados al pasar el cursor (del menú por al menos)
function resaltarTb() {
  const enlacesTb = document.querySelectorAll('.toolbar .links button, .toolbar .links a');
  if (!enlacesTb) return;
  enlacesTb.forEach(btn => {
    btn.addEventListener('mouseenter', () => {
      if (btn.dataset.prevBg === undefined) {
        btn.dataset.prevBg = btn.style.backgroundColor || '';
        btn.dataset.prevColor = btn.style.color || '';
        btn.dataset.prevBoxShadow = btn.style.boxShadow || '';
      }
      btn.style.backgroundColor = '#fffbcc';
      btn.style.color = '#000';
      btn.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
      btn.style.borderRadius = '6px';
    });
    btn.addEventListener('mouseleave', () => {
      btn.style.backgroundColor = btn.dataset.prevBg || '';
      btn.style.color = btn.dataset.prevColor || '';
      btn.style.boxShadow = btn.dataset.prevBoxShadow || '';
    });
  });
}

//redirigir a el html para iniciar sesion
function redirTb() {
  const enlacesRedir = document.querySelectorAll('.toolbar .links button, .toolbar .links a');
  enlacesRedir.forEach(el => {
    const texto = (el.textContent || '').trim().toLowerCase();
    if (texto === 'iniciar sesión' || texto === 'iniciar sesion' || texto === 'registrarse') {
      el.addEventListener('click', (e) => {
        window.location.href = 'inicio.html';
      });
    }
  });
}

/* Validaciones para el registro e inicio de sesion:
   - Login: validar que email tenga '@' y que la contraseña no esté vacía.
   - Registro: validar que todos los campos estén llenos, que el email tenga '@' y que la contraseña tenga al menos 8 caracteres y 1 numero.
*/
function validarFrm() {
  const btnsLogin = Array.from(document.querySelectorAll('.card button, .card input[type="submit"]'))
    .filter(btn => (btn.textContent || btn.value || '').toString().trim().toLowerCase() === 'iniciar sesión');

  btnsLogin.forEach(btn => {
    btn.addEventListener('click', (e) => {
      const tarjeta = buscarCardcerca(btn);
      if (!tarjeta) return;
      const inputEmail = tarjeta.querySelector('input[type="email"]');
      const inputPw = tarjeta.querySelector('input[type="password"]');

      const email = inputEmail ? inputEmail.value.trim() : '';
      const pw = inputPw ? inputPw.value : '';

      if (!email || email.indexOf('@') === -1) {
        e.preventDefault();
        alert('Introduce un correo válido que contenga "@"');
        return;
      }
      if (!pw) {
        e.preventDefault();
        alert('Introduce la contraseña.');
        return;
      }
    });
  });

  const btnsCrear = Array.from(document.querySelectorAll('.card button, .card input[type="submit"]'))
    .filter(btn => {
      const txtBtn = (btn.textContent || btn.value || '').toString().trim().toLowerCase();
      return txtBtn === 'crear cuenta' || txtBtn === 'registrarse';
    });

  btnsCrear.forEach(btn => {
    btn.addEventListener('click', (e) => {
      const tarjeta = buscarCardcerca(btn);
      if (!tarjeta) return;

      const inputNombre = tarjeta.querySelector('input[type="text"]');
      const inputEmail = tarjeta.querySelector('input[type="email"]');
      const inputPw = tarjeta.querySelector('input[type="password"]');

      // Validacuiones
      if (inputNombre && inputNombre.value.trim() === '') {
        e.preventDefault();
        alert('Por favor completa el nombre.');
        return;
      }
      if (!inputEmail || inputEmail.value.trim() === '' || inputEmail.value.indexOf('@') === -1) {
        e.preventDefault();
        alert('Por favor introduce un correo válido que contenga "@".');
        return;
      }
      if (!inputPw || inputPw.value.length < 8) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 8 caracteres.');
        return;
      }
      if (!/\d/.test(inputPw.value)) {
        e.preventDefault();
        alert('La contraseña debe contener al menos un número.');
        return;
      }
    });
  });
}

function buscarCardcerca(el) {
  let nodo = el;
  while (nodo) {
    if (nodo.classList && nodo.classList.contains('card')) return nodo;
    nodo = nodo.parentNode;
  }
  return null;
}
