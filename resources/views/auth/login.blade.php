<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

  <style>
    .gradient-custom {
      background: linear-gradient(to right, #6a11cb, #2575fc);
    }
  </style>
</head>
<body class="gradient-custom">

  <section class="vh-100 d-flex justify-content-center align-items-center">
    <div class="container">
      <div class="row justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
          <div class="card text-dark bg-white shadow mx-auto w-100" style="border-radius: 1rem; max-width: 400px;">
            <div class="card-body p-5 text-center">
              <form id="loginForm">
                @csrf

                <h2 class="fw-bold mb-2 text-uppercase">LOGIN</h2>
                <p class="text-muted mb-4">Please enter your login and password!</p>

                <div id="alert" class="alert alert-danger d-none" role="alert"></div>

                <div class="form-outline form-dark mb-4">
                  <input type="email" name="email" id="email" class="form-control form-control-lg" required autofocus />
                  <label class="form-label" for="email">Email</label>
                </div>

                <div class="form-outline form-dark mb-4">
                  <input type="password" name="password" id="password" class="form-control form-control-lg" required />
                  <label class="form-label" for="password">Password</label>
                </div>

                <button class="btn btn-primary btn-lg px-5" type="submit">LOGIN</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

  <script>
    const form = document.getElementById('loginForm');
    const alertDiv = document.getElementById('alert');

    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      alertDiv.classList.add('d-none');
      alertDiv.innerText = '';

      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;

      try {
        const response = await fetch('/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (response.ok) {
          sessionStorage.setItem('token', data.user.token);
          sessionStorage.setItem('user', JSON.stringify(data.user));
          window.location.href = '/dashboard';
        } else {
          alertDiv.innerText = data.message || 'Login gagal. Coba lagi.';
          alertDiv.classList.remove('d-none');
        }
      } catch (error) {
        alertDiv.innerText = 'Terjadi kesalahan saat login.';
        alertDiv.classList.remove('d-none');
      }
    });
  </script>
</body>
</html>
