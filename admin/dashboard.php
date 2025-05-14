<?php
session_start();
include "../connection.php";
?>
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../styles.css">
  <title>Car Rental - Dashboard</title>
</head>

<body>
  <main style="height: 100vh; width: 100%;" class="row">
    <!-- SIDEBAR -->
    <?php include "sidebar.php"; ?>
    <section class="col bg-light-subtle">
      <!-- NAVBAR -->
      <nav class="navbar" style="height: 60px;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
          <span class="navbar-brand mb-0 fs-2 text-uppercase text-primary fw-bolder">Dashboard</span>
          <!-- Account -->
          <div class="btn-group">
            <button type="button" class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle fs-4"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <span class="dropdown-item" style="cursor:default;">
                  Signed as
                  <?= $_SESSION["userName"] ?? "Unknown" ?>
                </span>
              </li>
              <li>
                <form action="../functions.php" method="post" class="m-0 p-0">
                  <button type="submit" class="dropdown-item" name="logoutBtn">
                    <i class="bi bi-box-arrow-right"></i>
                    &nbsp;Sign out
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      <!-- CARD -->
      <div class="row py-3">
        <div class="card m-2 shadow-sm border-0 col">
          <div class="card-body">
            <h5 class="card-title text-uppercase fw-semibold">Active Rental</h5>
            <?php
            $sql = "SELECT COUNT(*) AS total FROM tbl_rental WHERE Status = 'pending'";
            $res = mysqli_query($con, $sql);
            $row = mysqli_fetch_array($res);
            ?>
            <p class="card-text fs-1 fw-bold text-primary">
              <?= $row[0] ?? 0 ?>
            </p>
          </div>
        </div>
        <div class="card m-2 shadow-sm border-0 col">
          <div class="card-body">
            <h5 class="card-title text-uppercase fw-semibold">Transactions</h5>
            <?php
            $sql = "SELECT COUNT(*) AS total FROM tbl_rental WHERE Status = 'done' OR Status = 'cancelled' OR Status = 'booked'";
            $res = mysqli_query($con, $sql);
            $row = mysqli_fetch_array($res);
            ?>
            <p class="card-text fs-1 fw-bold text-primary">
              <?= $row[0] ?? 0 ?>
            </p>
          </div>
        </div>
        <div class="card m-2 shadow-sm border-0 col">
          <div class="card-body">
            <h5 class="card-title text-uppercase fw-semibold">Registered Car</h5>
            <?php
            $sql = "SELECT COUNT(*) AS total FROM tbl_car";
            $res = mysqli_query($con, $sql);
            $row = mysqli_fetch_array($res);
            ?>
            <p class="card-text fs-1 fw-bold text-primary">
              <?= $row[0] ?? 0 ?>
            </p>
          </div>
        </div>
        <div class="card m-2 shadow-sm border-0 col">
          <div class="card-body">
            <h5 class="card-title text-uppercase fw-semibold">Total Revenue</h5>
            <?php
            $sql = "SELECT SUM(TotalCost) AS total FROM tbl_rental WHERE Status = 'done'";
            $res = mysqli_query($con, $sql);
            $row = mysqli_fetch_array($res);
            ?>
            <p class="card-text fs-1 fw-bold text-primary">
              ₱<?= number_format($row[0] ?? 0, 2) ?>
            </p>
          </div>
        </div>
      </div>

      <!-- GRAPH / CHART -->
    </section>
  </main>
</body>

</html>