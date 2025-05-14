<?php session_start();
include("../connection.php"); ?>
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
  <title>Car Rental - History</title>
</head>

<body>
  <main style="height: 100vh; width: 100%" class="row">
    <!-- SIDEBAR -->
    <aside class="h-100 border col-2">
      <section class="my-5">
        <img src="../image/logo.png" class="rounded mx-auto d-block" alt="LOGO" width="170">
      </section>
      <hr>
      <nav class="text-decoration-none">
        <ul class="d-flex flex-column gap-2 px-2">
          <li><a href="dashboard.php" style="width: 100%;" class="btn btn-light text-start"><i class="bi bi-house"></i>
              Dashboard</a></li>
          <li><a href="transactions.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-plus-circle-dotted"></i> Transactions</a></li>
          <li><a href="manage-users.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-people"></i> Manage Users</a>
          </li>
          <li><a href="manage-owners.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-person-circle"></i> Manage Owners</a>
          </li>
          <li><a href="manage-car.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-card-list"></i> Manage Vehicles</a>
          </li>
          <li><a href="manage-driver.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-person-vcard"></i> Manage
              Drivers</a></li>
          <li><a href="rental-history.php" style="width: 100%;" class="btn btn-light text-start active"><i
                class="bi bi-table"></i> Rental
              History</a></li>
        </ul>
      </nav>
    </aside>
    <section class="col bg-tertiary">
      <!-- NAVBAR -->
      <nav class="navbar" style="height: 60px;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
          <span class="navbar-brand mb-0 text-uppercase text-primary fw-bolder fs-2">Rental History</span>
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
      <div class="card m-2 shadow-sm border-0 h-75">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">List of Transactions History</h5>
            <a href="print.php" target="_blank" class="btn btn-secondary">
              <i class="bi bi-printer"></i> Print
            </a>
          </div>

          <hr>
          <!-- TABLE -->
          <?php
          $rentalSql = "SELECT 
                            r.RentalID,
                            u.Name AS CustomerName,
                            v.Make AS Make,
                            v.Model AS Model,
                            v.PlateNumber AS Plate,
                            v.PlateNumber AS VehiclePlateNumber,
                            d.Name AS DriverName,
                            r.RentalDate,
                            r.ReturnDate,
                            r.TotalCost,
                            r.Status
                        FROM 
                            tbl_rental r
                        JOIN 
                            tbl_user u ON r.CustomerID = u.UserID
                        JOIN 
                            tbl_car v ON r.CarID = v.CarID
                        JOIN 
                            tbl_drivers d ON r.DriverID = d.DriversID
                        WHERE 
                            r.Status IN ('done', 'cancelled');
                        ";
          $rentalSqlResult = mysqli_query($con, $rentalSql);
          ?>
          <table class="table table-hover table-sm">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Customer</th>
                <th scope="col">Vehicle Rented</th>
                <th scope="col">Driver Rented</th>
                <th scope="col">Rental Date</th>
                <th scope="col">Return Date</th>
                <th scope="col">Total Cost</th>
                <th scope="col">Payment Method</th>
                <th scope="col" class="text-end" style="width: 120px;">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 0;
              if (mysqli_num_rows($rentalSqlResult) > 0) {
                while ($row = mysqli_fetch_array($rentalSqlResult)) {
                  $count++;
              ?>
                  <tr style="cursor: pointer;">
                    <th scope="row"><?= $count; ?></th>
                    <td><?= $row['CustomerName'] ?></td>
                    <td><?= $row['Make'] ?> <?= $row['Model'] ?> Plate#<?= $row['Plate'] ?></td>
                    <td><?= $row['DriverName'] ?></td>
                    <td><?php $rentalDate = new DateTime($row['RentalDate']);
                        echo date_format($rentalDate, "M d, Y"); ?></td>
                    <td><?php $returnDate = new DateTime($row['ReturnDate']);
                        echo date_format($returnDate, "M d, Y"); ?></td>
                    <td>₱<?= number_format($row['TotalCost'], 2) ?></td>
                    <td class="text-uppercase">
                      <?php
                      $paymentSql = "SELECT PaymentMethod FROM tbl_payment WHERE RentalID = " . $row['RentalID'];
                      $paymentSqlResult = mysqli_query($con, $paymentSql);
                      $paymentRow = mysqli_fetch_array($paymentSqlResult);
                      echo $paymentRow['PaymentMethod'] ?? 'N/A';
                      ?>
                    </td>
                    <td class="text-end text-uppercase">
                      <span class="badge <?php
                                          if ($row['Status'] == 'done') {
                                            echo 'bg-success';
                                          } else {
                                            echo 'bg-danger';
                                          }
                                          ?> w-75 py-2">
                        <?= $row['Status'] ?>
                      </span>
                    </td>
                  </tr>
                <?php
                }
              } else {
                ?>
                <tr>
                  <td colspan="9">
                    <h6 class="text-center my-auto">No rental history</h6>
                  </td>
                </tr>
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          <div class="d-flex justify-content-between">
            <h5>Completed Transactions:
              <?php
              $sql = "SELECT COUNT(*) AS TotalDone FROM tbl_rental WHERE Status = 'done'";
              $result = mysqli_query($con, $sql);
              $row = mysqli_fetch_array($result);
              echo $row['TotalDone'];
              ?>
            </h5>
            <h5>Cancelled Transactions:
              <?php
              $sql = "SELECT COUNT(*) AS TotalCancelled FROM tbl_rental WHERE Status = 'cancelled'";
              $result = mysqli_query($con, $sql);
              $row = mysqli_fetch_array($result);
              echo $row['TotalCancelled'];
              ?>
            </h5>
            <h5>Total Transactions: <?= $count ?></h5>
            <h5>Total Revenue:
              <?php
              $sql = "SELECT SUM(TotalCost) AS TotalRevenue FROM tbl_rental WHERE Status = 'done'";
              $result = mysqli_query($con, $sql);
              $row = mysqli_fetch_array($result);
              echo "₱" . number_format($row['TotalRevenue'], 2);
              ?>
            </h5>
          </div>
        </div>
      </div>
    </section>
  </main>
</body>

</html>