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
  style="background-image: linear-gradient(to right, #0c969c, #274d60); background-repeat: no-repeat; background-size: cover; height: 100vh;"
  class="d-flex align-items-start">
  <nav class="navbar bg-body-light sticky-top">
    <div class="container-fluid">
      <span class="navbar-brand h1 fw-bold text-white fs-2">
        <img src="../image/logo.png" width="64" alt="logo">
        CAR RENTAL SYSTEM
      </span>
    </div>
  </nav>
  <div class="container-fluid px-5 d-flex justify-content-end align-items-center" style="height: 100vh; width: 100%;">
    <div class="card glass" style="width: 40rem;">
      <div class="card-body py-5 d-flex flex-column justify-content-between gap-3">
        <h2 class="text-center text-white text-uppercase fw-bold">Create an account</h2>
        <form method="POST" action="../functions.php">
          <div class="mb-1 w-100">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control glass" name="name" id="name" required>
          </div>
          <div class="mb-1 w-100">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control glass" name="email" required>
          </div>
          <div class="mb-1 w-100">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control glass" value="" name="username" required>
          </div>
          <div class="mb-1 w-100">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control glass" value="" name="password" required>
          </div>
          <div class="mb-1 w-100">
            <label for="confirm-password" class="form-label">Confirm Password</label>
            <input id="confirm-password" type="password" class="form-control glass" value="" name="confirm-password" required>
          </div>
          <small id="password-message" class="text-danger"></small>
          <hr>
          <div class="d-flex justify-content-center">
            <button type="submit" name="addAccountBtn" class="btn btn-primary w-100">Sign up</button>
          </div>
        </form>
        <center>
          <span>Already have an account? <a href="./index.php">Click here</a></span>
        </center>
      </div>
    </div>
  </div>
  </div>
  <script>
    const passwordInput = document.querySelector("input[name='password']");
    const confirmPasswordInput = document.querySelector("input[name='confirm-password']");
    const message = document.getElementById("password-message");
    const form = document.querySelector("form");

    function validatePasswordPolicy(password) {
      const lengthValid = password.length >= 8;
      const upperValid = /[A-Z]/.test(password);
      const lowerValid = /[a-z]/.test(password);
      const numberValid = /[0-9]/.test(password);
      const specialValid = /[!@#$%^&*(),.?":{}|<>]/.test(password);

      return lengthValid && upperValid && lowerValid && numberValid && specialValid;
    }

    function updatePasswordMessage() {
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;

      let errors = [];

      if (!validatePasswordPolicy(password)) {
        errors.push("Password must be at least 8 characters, include uppercase, lowercase, number, and special character.");
      }

      if (password !== confirmPassword) {
        errors.push("Passwords do not match.");
      }

      message.textContent = errors.join(" ");
      message.classList.toggle("text-danger", errors.length > 0);
      message.classList.toggle("text-success", errors.length === 0);
    }

    passwordInput.addEventListener("input", updatePasswordMessage);
    confirmPasswordInput.addEventListener("input", updatePasswordMessage);

    form.addEventListener("submit", function(e) {
      if (!validatePasswordPolicy(passwordInput.value) || passwordInput.value !== confirmPasswordInput.value) {
        e.preventDefault();
        alert("Please fix password errors before submitting.");
      }
    });
  </script>
</body>

</html>