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
  <title>Car Rental - Transactions</title>
</head>

<body>
  <main style="height: 100vh; width: 100%;" class="row">
    <!-- SIDEBAR -->
    <aside class="col-2 h-100 border">
      <section class="my-5">
        <img src="../image/logo.png" class="rounded mx-auto d-block" alt="LOGO" width="170">
      </section>
      <hr>
      <nav class="text-decoration-none">
        <ul class="d-flex flex-column gap-2 px-2">
          <li><a href="dashboard.php" style="width: 100%;" class="btn btn-light text-start"><i class="bi bi-house"></i>
              Dashboard</a></li>
          <li><a href="transactions.php" style="width: 100%;" class="btn btn-light text-start active"><i
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
          <li><a href="rental-history.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-table"></i> Rental
              History</a></li>
        </ul>
      </nav>
    </aside>
    <section class="col bg-tertiary">
      <!-- NAVBAR -->
      <nav class="navbar" style="height: 60px;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
          <span class="navbar-brand mb-0 text-uppercase text-primary fw-bolder fs-2">Transactions</span>
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
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">List of Active Rentals</h5>
            <!-- Add Transaction Offcanvas -->
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop"
              aria-controls="staticBackdrop">
              <i class="bi bi-plus-square-fill"></i>&nbsp;&nbsp;New Transaction
            </button>

            <div class="offcanvas offcanvas-end" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop"
              aria-labelledby="staticBackdropLabel">
              <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="staticBackdropLabel">New Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>
              <div class="offcanvas-body">
                <form action="../functions.php" method="post" class="d-flex flex-column gap-2">
                  <!-- CUSTOMER -->
                  <h6>Customer</h6>
                  <!-- Button trigger modal -->
                  <input type="hidden" name="customerID" id="customerId">
                  <div class="d-flex gap-2">
                    <input type="text" id="customerName" placeholder="Customer Name" class="form-control" readonly>
                    <button type="button" class="btn btn-sm btn-primary px-3" data-bs-toggle="modal"
                      data-bs-target="#customerModal">
                      <i class="bi bi-search"></i>
                    </button>
                  </div>
                  <!-- Modal -->
                  <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel"
                    aria-hidden="true" data-bs-backdrop="false">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5" id="customerModalLabel">List of Customers</h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <table class="table table-hover">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col" class="text-end">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $sql = "SELECT * FROM tbl_user WHERE Role = 'client'";
                              $result = mysqli_query($con, $sql);
                              $count = 0;
                              while ($row = mysqli_fetch_array($result)) {
                                $count++;
                              ?>
                                <tr>
                                  <th scope="row"><?= $count ?></th>
                                  <td><?= $row["Name"] ?></td>
                                  <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal" onclick="setCustomer('<?= $row['UserID'] ?>', '<?= $row['Name'] ?>'
                                      )">Select</button>
                                  </td>
                                </tr>
                              <?php
                              }
                              ?>
                              <script>
                                function setCustomer(customerId, customerName) {
                                  // Set the value of the hidden input field
                                  document.getElementById("customerId").value = customerId;

                                  // Set the value of the customerName input field
                                  document.getElementById("customerName").value = customerName;
                                }
                              </script>

                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- VEHICLE -->
                  <h6>Vehicle</h6>
                  <!-- Button trigger modal -->
                  <input type="hidden" name="vehicleId" id="vehicleId">
                  <div class="d-flex gap-2">
                    <input type="text" id="vehicleName" placeholder="Vehicle" class="form-control" readonly>
                    <button type="button" class="btn btn-sm btn-primary px-3" data-bs-toggle="modal"
                      data-bs-target="#vehicleModal">
                      <i class="bi bi-search"></i>
                    </button>
                  </div>
                  <!-- Modal -->
                  <div class="modal fade" id="vehicleModal" tabindex="-1" aria-labelledby="vehicleModalLabel"
                    aria-hidden="true" data-bs-backdrop="false">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5" id="vehicleModalLabel">List of Vehicles</h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <table class="table table-hover">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Make</th>
                                <th scope="col">Model</th>
                                <th scope="col">Year Model</th>
                                <th scope="col">Plate Number</th>
                                <th scope="col" class="text-end">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $vehicleSql = "SELECT * FROM tbl_car";
                              $vehicleSqlRes = mysqli_query($con, $vehicleSql);
                              $count = 0;
                              while ($row = mysqli_fetch_array($vehicleSqlRes)) {
                                $count++;
                              ?>
                                <tr>
                                  <th scope="row"><?= $count ?></th>
                                  <td><?= $row["Make"] ?></td>
                                  <td><?= $row["Model"] ?></td>
                                  <td><?= $row["YearModel"] ?></td>
                                  <td><?= $row["PlateNumber"] ?></td>
                                  <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal" onclick="setVehicle('<?= $row['CarID'] ?>', '<?= $row['Make'] ?> <?= $row['Model'] ?>'
                                      )">Select</button>
                                  </td>
                                </tr>
                              <?php
                              }
                              ?>
                              <script>
                                function setVehicle(vehicleId, vehicleName) {
                                  // Set the value of the hidden input field
                                  document.getElementById("vehicleId").value = vehicleId;

                                  // Set the value of the customerName input field
                                  document.getElementById("vehicleName").value = vehicleName;
                                }
                              </script>

                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- DRIVER -->
                  <h6>Driver</h6>
                  <!-- Button trigger modal -->
                  <input type="hidden" name="driverId" id="driverId">
                  <div class="d-flex gap-2">
                    <input type="text" id="driverName" placeholder="Driver" class="form-control" readonly>
                    <button type="button" class="btn btn-sm btn-primary px-3" data-bs-toggle="modal"
                      data-bs-target="#driverModal">
                      <i class="bi bi-search"></i>
                    </button>
                  </div>
                  <!-- Modal -->
                  <div class="modal fade" id="driverModal" tabindex="-1" aria-labelledby="driverModalLabel"
                    aria-hidden="true" data-bs-backdrop="false">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5" id="driverModalLabel">List of Available Drivers</h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <table class="table table-hover">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Driver License</th>
                                <th scope="col">Name</th>
                                <th scope="col">Availability</th>
                                <th scope="col" class="text-end">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $driverSql = "SELECT * FROM tbl_drivers WHERE Availability = 'Available'";
                              $driverSqlRes = mysqli_query($con, $driverSql);
                              $count = 0;
                              while ($row = mysqli_fetch_array($driverSqlRes)) {
                                $count++;
                              ?>
                                <tr>
                                  <th scope="row"><?= $count ?></th>
                                  <td><?= $row["Driverslicense"] ?></td>
                                  <td><?= $row["Name"] ?></td>
                                  <td><?= $row["Availability"] ?></td>
                                  <td class="text-end">
                                    <button
                                      type="button"
                                      class="btn btn-sm btn-primary"
                                      data-bs-dismiss="modal"
                                      onclick="setDriver('<?= $row['DriversID'] ?>', '<?= $row['Name'] ?>'
                                      )">Select</button>
                                  </td>
                                </tr>
                              <?php
                              }
                              ?>
                              <script>
                                function setDriver(driverId, driverName) {
                                  // Set the value of the hidden input field
                                  document.getElementById("driverId").value = driverId;

                                  // Set the value of the customerName input field
                                  document.getElementById("driverName").value = driverName;
                                }
                              </script>

                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>

                  <h6>Rental Date</h6>
                  <input type="date" name="rentalDate" class="form-control">
                  <h6>Return Date</h6>
                  <input type="date" name="returnDate" class="form-control">
                  <button type="submit" name="addTransactionBtn" class="btn btn-primary w-100">Add Transaction</button>
                </form>
              </div>
            </div>
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
                        r.Status IN ('pending', 'booked')";

          $rentalSqlResult = mysqli_query($con, $rentalSql);
          ?>
          <table class="table table-hover table-sm">
            <thead>
              <tr>
                <th scope="col">Customer</th>
                <th scope="col">Vehicle Rented</th>
                <th scope="col">Driver Rented</th>
                <th scope="col">Rental Date</th>
                <th scope="col">Return Date</th>
                <th scope="col">Total Cost</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 0;
              if (mysqli_num_rows($rentalSqlResult) > 0) {
                while ($row = mysqli_fetch_array($rentalSqlResult)) {
              ?>
                  <tr style="cursor: pointer;">
                    <td><?= $row['CustomerName'] ?></td>
                    <td><?= $row['Make'] ?> <?= $row['Model'] ?> Plate#<?= $row['Plate'] ?></td>
                    <td><?= $row['DriverName'] ?></td>
                    <td><?php $rentalDate = new DateTime($row['RentalDate']);
                        echo date_format($rentalDate, "M d, Y"); ?></td>
                    <td><?php $returnDate = new DateTime($row['ReturnDate']);
                        echo date_format($returnDate, "M d, Y"); ?></td>
                    <td>
                      ₱<?= number_format($row['TotalCost'], 2) ?>
                    </td>
                    <td class="text-uppercase">
                      <span class="badge text-bg-warning w-100 py-2">
                        <?= $row['Status'] ?>
                      </span>
                    </td>
                    <td>
                      <div class="d-flex justify-content-end gap-2">
                        <?php
                        if ($row["Status"] == 'booked') {
                        ?>
                          <form action="../functions.php" method="post">
                            <input type="hidden" value="<?= $row['RentalID'] ?>" name="rentalID">
                            <button type="submit" name="acceptTransactionBtn" class="btn btn-sm btn-outline-success"><i
                                class="bi bi-check-circle-fill"></i>
                              &nbsp;Confirm</button>
                          </form>
                        <?php
                        }
                        if ($row["Status"] == 'pending') {
                        ?>
                          <!-- Button trigger modal -->
                          <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#markAsDoneModal">
                            <i
                              class="bi bi-check-circle-fill"></i>
                            &nbsp;Mark as done
                          </button>
                          <!-- Modal -->
                          <div class="modal fade" id="markAsDoneModal" tabindex="-1" aria-labelledby="markAsDoneModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h1 class="modal-title fs-5" id="markAsDoneModalLabel">End Transaction</h1>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="../functions.php" method="post">
                                  <div class="modal-body">
                                    <input type="hidden" value="<?= $row['RentalID'] ?>" name="rentalID">
                                    <h6>Customer: <?= $row["CustomerName"] ?></h6>
                                    <h6>Total Cost: <?= $row["TotalCost"] ?></h6>
                                    <hr>
                                    <h6>Payment Method</h6>
                                    <select name="paymentMethod" class="form-select">
                                      <option value="cash" selected>Cash</option>
                                      <option value="ewallet">Ewallet</option>
                                    </select>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="finishTransactionBtn" class="btn btn-primary">Proceed</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <form action="../functions.php" method="post">
                            <input type="hidden" value="<?= $row['RentalID'] ?>" name="rentalID">
                            <button type="submit" name="cancelTransactionBtn" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i>
                              &nbsp;Cancel</button>
                          </form>
                        <?php
                        }
                        ?>
                      </div>
                    </td>
                  </tr>

                <?php
                }
              } else {
                ?>
                <tr>
                  <td colspan="9">
                    <h6 class="text-center my-auto">No active rentals</h6>
                  </td>
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