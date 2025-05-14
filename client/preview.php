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
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../styles.css">
  <title>Car Rental - View Details</title>
</head>

<body>
  <main class="row" style="height: 100vh; width: 100%;">
    <!-- Sidebar -->
    <aside class="col-2 h-100 border">
      <section class="my-5">
        <img src="../image/logo.png" class="rounded mx-auto d-block" alt="LOGO" width="170">
      </section>
      <hr>
      <nav class="text-decoration-none">
        <ul class="d-flex flex-column gap-2 px-2">
          <li><a href="browse.php" class="btn btn-light text-start active" style="width: 100%;"><i
                class="bi bi-house"></i> Browse List</a></li>
          <li><a href="history.php" class="btn btn-light text-start" style="width: 100%;"><i class="bi bi-table"></i> My History</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <section class="col bg-light-subtle" style="width: 100%;">
      <!-- Navbar -->
      <nav class="navbar" style="height: 60px;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-baseline">
            <span class="navbar-brand fs-2 text-uppercase text-primary fw-bolder m-0">
              <a href="browse.php" class="nav-link">Car Preview</a>
            </span>
            <!-- Breadcrumb -->
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"></li>
                <li class="breadcrumb-item active" aria-current="page">Preview</li>
              </ol>
            </nav>
          </div>
          <!-- User Account -->
          <div class="d-flex gap-2 align-items-center">
            <h5 class="text-uppercase"><?= isset($_SESSION["userName"]) ? $_SESSION["userName"] : "Guest" ?></h5>
            <div class="btn-group">
              <button type="button" class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle fs-4"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item" style="cursor:default;">Signed as <?= $_SESSION["userName"] ?? "User" ?></span></li>
                <li>
                  <form action="../functions.php" method="post" class="m-0 p-0">
                    <button type="submit" class="dropdown-item" name="logoutBtn"><i class="bi bi-box-arrow-right"></i> &nbsp;Sign out</button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </nav>

      <!-- Car Details Card -->
      <div class="card m-2 shadow-sm border-0 gap-3 p-3">
        <div class="card-content h-100 row">
          <?php
          $carId = mysqli_real_escape_string($con, $_GET["id"]);
          $sql = "SELECT * FROM tbl_car WHERE CarID = '$carId'";
          $res = mysqli_query($con, $sql);
          $row = mysqli_fetch_array($res);
          ?>
          <div class="col">
            <img src="<?= htmlspecialchars($row["Preview"]) ?>" alt="CAR IMAGE" class="img-fluid img-thumbnail">
          </div>
          <div class="col d-flex flex-column justify-content-between">
            <div>
              <h1><?= htmlspecialchars($row["Make"]) ?> <?= htmlspecialchars($row["Model"]) ?></h1>
              <h6>Year: <?= htmlspecialchars($row["YearModel"]) ?></h6>
              <h6>Price: ₱<?= number_format($row["BasePrice"], 2) ?></h6>
            </div>
            <hr>
            <form action="../functions.php" method="post" class="d-flex flex-column gap-2" onsubmit="validateDates(event)">
              <input type="hidden" name="customerID" value="<?= htmlspecialchars($_SESSION["UserID"] ?? '') ?>" />
              <input type="hidden" name="carID" value="<?= htmlspecialchars($_GET["id"]) ?>" />
              <input type="hidden" name="totalCost" id="totalCost" value="<?= htmlspecialchars($row["BasePrice"]) ?>" />

              <!-- Driver Selection -->
              <h6>Available Driver</h6>
              <div class="d-flex gap-2">
                <input type="hidden" name="driverId" id="driverId">
                <input type="text" id="driverName" placeholder="Driver" class="form-control" readonly required>
                <button type="button" class="btn btn-sm btn-primary px-3" data-bs-toggle="modal" data-bs-target="#driverModal"><i class="bi bi-search"></i></button>
              </div>

              <!-- Modal for Driver List -->
              <div class="modal fade" id="driverModal" tabindex="-1" aria-labelledby="driverModalLabel" aria-hidden="true" data-bs-backdrop="false">
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
                              <td><?= htmlspecialchars($row["Driverslicense"]) ?></td>
                              <td><?= htmlspecialchars($row["Name"]) ?></td>
                              <td><?= htmlspecialchars($row["Availability"]) ?></td>
                              <td class="text-end">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal"
                                  onclick="setDriver('<?= htmlspecialchars($row['DriversID']) ?>', '<?= htmlspecialchars($row['Name']) ?>')">Select</button>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Rental and Return Dates -->
              <h6>Rental Date</h6>
              <input type="date" name="rentalDate" id="rentalDate" class="form-control" required onchange="calculateTotal()">
              <h6>Return Date</h6>
              <input type="date" name="returnDate" id="returnDate" class="form-control" required onchange="calculateTotal()">
              <h6>Total Cost: ₱
                <span id="totalCostDisplay">
                  <?= number_format($row["BasePrice"], 2) ?>
                </span>
              </h6>
              <button class="btn btn-primary w-100" type="submit" name="rentBtn">Rent</button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script>
    const basePrice = <?= $row["BasePrice"] ?>;

    function validateDates(event) {
      const rentalDate = new Date(document.getElementById('rentalDate').value);
      const returnDate = new Date(document.getElementById('returnDate').value);

      if (returnDate <= rentalDate) {
        event.preventDefault();
        alert('Return date must be after the rental date.');
      }
    }

    function calculateTotal() {
      const rentalDate = new Date(document.getElementById('rentalDate').value);
      const returnDate = new Date(document.getElementById('returnDate').value);

      if (rentalDate && returnDate && returnDate > rentalDate) {
        const timeDiff = returnDate - rentalDate;
        const days = timeDiff / (1000 * 60 * 60 * 24); // Convert milliseconds to days
        const totalCost = days * basePrice;

        document.getElementById('totalCost').value = totalCost;
        document.getElementById('totalCostDisplay').textContent = totalCost.toFixed(2);
      } else {
        document.getElementById('totalCostDisplay').textContent = basePrice.toFixed(2);
      }
    }

    function setDriver(driverId, driverName) {
      document.getElementById("driverId").value = driverId;
      document.getElementById("driverName").value = driverName;
    }
  </script>
</body>

</html>