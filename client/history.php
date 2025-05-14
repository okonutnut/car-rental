<?php
session_start();
include("../connection.php");
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
  <title>Car Rental - My History</title>
</head>

<body>
  <main class="row" style="height: 100vh; width: 100%;">
    <!-- SIDEBAR -->
    <aside class="col-2 h-100 border">
      <section class="my-5">
        <img src="../image/logo.png" class="rounded mx-auto d-block" alt="LOGO" width="170">
      </section>
      <hr>
      <nav>
        <ul class="d-flex flex-column gap-2 px-2">
          <li><a href="browse.php" class="btn btn-light w-100 text-start"><i class="bi bi-house"></i> Browse List</a></li>
          <li><a href="history.php" class="btn btn-light w-100 text-start active"><i class="bi bi-table"></i> My History</a></li>
        </ul>
      </nav>
    </aside>

    <section class="col bg-tertiary">
      <!-- NAVBAR -->
      <nav class="navbar bg-light">
        <div class="container-fluid d-flex justify-content-between align-items-center">
          <span class="navbar-brand text-uppercase text-primary fw-bold fs-2">My History</span>
          <div class="d-flex gap-2 align-items-center">
            <h5 class="text-uppercase"><?= $_SESSION["userName"] ?? "User" ?></h5>
            <div class="btn-group">
              <button type="button" class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-4"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item">Signed in as <?= $_SESSION["userName"] ?? "User" ?></span></li>
                <li>
                  <form action="../functions.php" method="post">
                    <button type="submit" class="dropdown-item" name="logoutBtn">
                      <i class="bi bi-box-arrow-right"></i>&nbsp;Sign out
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </nav>

      <!-- CARD -->
      <div class="card m-2 shadow-sm border-0">
        <div class="card-body">
          <?php
          $customerID = $_SESSION["UserID"] ?? 0;
          $whereClause = "";

          // Apply Date Range Filters
          if (!empty($_POST['dateFrom']) && !empty($_POST['dateTo'])) {
            $dateFrom = $_POST['dateFrom'];
            $dateTo = $_POST['dateTo'];
            $whereClause = "AND tbl_rental.RentalDate BETWEEN '$dateFrom' AND '$dateTo'";
          }

          // Calculate Total Revenue
          $totalRevenueSql = "SELECT SUM(tbl_rental.TotalCost) AS TotalRevenue
                              FROM tbl_rental
                              WHERE tbl_rental.CustomerID = $customerID $whereClause";
          $totalRevenueResult = mysqli_query($con, $totalRevenueSql);
          $totalRevenue = mysqli_fetch_assoc($totalRevenueResult)['TotalRevenue'] ?? 0;

          $rentalSql = "SELECT
                          tbl_rental.RentalID,
                          tbl_rental.Status,
                          tbl_rental.RentalDate,
                          tbl_rental.ReturnDate,
                          tbl_car.Make,
                          tbl_car.Model,
                          tbl_car.PlateNumber,
                          tbl_rental.TotalCost,
                          tbl_rental.CreatedAt,
                          tbl_drivers.Name AS DriverName
                        FROM tbl_rental
                        INNER JOIN tbl_car ON tbl_rental.CarID = tbl_car.CarID
                        LEFT JOIN tbl_drivers ON tbl_rental.DriverID = tbl_drivers.DriversID
                        WHERE tbl_rental.CustomerID = $customerID $whereClause";
          $rentalSqlResult = mysqli_query($con, $rentalSql);
          ?>
          <div class="d-flex justify-content-between">
            <h5 class="text-primary">Total Revenue: ₱<?= number_format($totalRevenue, 2) ?></h5>
            <a href="history_print.php?from=<?= $_POST['dateFrom'] ?? '' ?>&to=<?= $_POST['dateTo'] ?? '' ?>" target="_blank" class="btn btn-outline-secondary">
              <i class="bi bi-printer"></i> Print
            </a>
          </div>

          <form method="POST" class="my-3 d-flex gap-3">
            <input type="date" name="dateFrom" value="<?= $_POST['dateFrom'] ?? '' ?>" class="form-control" placeholder="From">
            <input type="date" name="dateTo" value="<?= $_POST['dateTo'] ?? '' ?>" class="form-control" placeholder="To">
            <button type="submit" class="btn btn-primary">Filter</button>
          </form>

          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Vehicle</th>
                <th>Driver</th>
                <th>Rental Date</th>
                <th>Return Date</th>
                <th>Total Cost</th>
                <th>Status</th>
                <th>Date Submitted</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 0;
              if (mysqli_num_rows($rentalSqlResult) > 0) {
                while ($row = mysqli_fetch_assoc($rentalSqlResult)) {
                  $count++;
              ?>
                  <tr>
                    <td><?= $count ?></td>
                    <td><?= $row['Make'] ?> <?= $row['Model'] ?> (Plate: <?= $row['PlateNumber'] ?>)</td>
                    <td><?= $row['DriverName'] ?? "N/A" ?></td>
                    <td><?= date('M d, Y', strtotime($row['RentalDate'])) ?></td>
                    <td><?= date('M d, Y', strtotime($row['ReturnDate'])) ?></td>
                    <td>₱<?= number_format($row['TotalCost'], 2) ?></td>
                    <td><?= ucfirst($row['Status']) ?></td>
                    <td><?= date('M d, Y h:i a', strtotime($row['CreatedAt'])) ?></td>
                  </tr>
                <?php
                }
              } else {
                ?>
                <tr>
                  <td colspan="8" class="text-center">No transactions found for the selected range.</td>
                </tr>
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>
</body>

</html>