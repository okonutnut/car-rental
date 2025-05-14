<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="../styles.css">
  <title>Car Rental - Login</title>
</head>

<body
  style="background-image: linear-gradient(to right, #0c969c, #274d60); background-repeat: no-repeat; background-size: cover; height: 100vh;">
  <nav class="navbar bg-body-light sticky-top">
    <div class="container-fluid">
      <span class="navbar-brand h1 fw-bold text-white fs-2">
        <img src="../image/logo.png" width="64" alt="logo">
        CAR RENTAL SYSTEM
      </span>
    </div>
  </nav>
  <div class="container-fluid px-5 d-flex justify-content-end align-items-center h-75">
    <div class="card glass" style="width: 40rem; height: 30rem;">
      <div class="card-body py-5 d-flex flex-column justify-content-between gap-3">
        <h1 class="text-center text-white text-uppercase fw-bold">Login to your account</h1>
        <form method="POST" action="../functions.php">
          <div class="mb-3 w-100">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control glass" name="username" required>
          </div>
          <div class="mb-3 w-100">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control glass" name="password" required>
          </div>
          <?php
          if (isset($_GET["error"])) {
            $error = htmlspecialchars($_GET["error"]);
          ?>
            <center class="alert alert-danger text-danger text-capitalize my-3" role="alert">
              <?php echo $error; ?>
            </center>
          <?php
          }
          ?>
          <div class="d-flex justify-content-center">
            <button type="submit" name="loginBtn" class="btn btn-primary w-100">Sign in</button>
          </div>
        </form>
        <center>
          <span>Create an account? <a href="./create-account.php">Click here</a></span>
        </center>
      </div>
    </div>
  </div>
  </div>
  <script>
    const passwordInput = document.querySelector("input[name='password']");
    const loginForm = document.querySelector("form");
    const minLength = 8;
    const passwordError = document.createElement("small");
    passwordError.classList.add("text-danger");
    passwordInput.parentNode.appendChild(passwordError);

    function validatePasswordPolicy(password) {
      const hasUpper = /[A-Z]/.test(password);
      const hasLower = /[a-z]/.test(password);
      const hasNumber = /[0-9]/.test(password);
      const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
      const longEnough = password.length >= minLength;

      return hasUpper && hasLower && hasNumber && hasSpecial && longEnough;
    }

    passwordInput.addEventListener("input", () => {
      const password = passwordInput.value;
      if (!validatePasswordPolicy(password)) {
        passwordError.textContent = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
      } else {
        passwordError.textContent = "";
      }
    });

    loginForm.addEventListener("submit", (e) => {
      const password = passwordInput.value;
      if (!validatePasswordPolicy(password)) {
        e.preventDefault();
        alert("Please enter a valid password that meets all requirements.");
      }
    });
  </script>

</body>

</html>