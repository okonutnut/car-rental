<?php session_start() ?>
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
  <title>Car Rental - Manage Driver</title>
</head>

<body class="overflow-x-hidden">
  <main style="height: 100vh; width: 100%;" class="row">
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
          <li><a href="manage-driver.php" style="width: 100%;" class="btn btn-light text-start active"><i
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
          <span class="navbar-brand mb-0 text-uppercase text-primary fw-bolder fs-2">Manage Drivers</span>
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
            <h5 class="card-title">List of Available Drivers</h5>
            <!-- Add Vehicle trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
              data-bs-target="#addDriveModal">
              <i class="bi bi-plus-square-fill"></i>
              &nbsp;&nbsp;Add Driver
            </button>

            <!-- Modal -->
            <div class="modal fade" id="addDriveModal" tabindex="-1" aria-labelledby="addDriveModalLabel"
              aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="post" action="../functions.php">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addDriveModalLabel">Add Driver Entry</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body d-flex flex-column gap-2">
                    <input type="text" placeholder="Driver's License" class="form-control" name="driverLicense"
                      required>
                    <input type="text" placeholder="Name" class="form-control" name="name" required>
                    <input type="text" placeholder="Address" class="form-control" name="address" required>
                    <input type="number" placeholder="Contact Number" class="form-control" name="contactNumber"
                      required>
                    <select name="availability" class="form-select">
                      <option value="Available">Available</option>
                      <option value="Unavailable">Unavailable</option>
                    </select>
                  </div>
                  <div class="modal-footer">
                    <button style="width: 100%" type="submit" name="addDriverBtn"
                      class="btn btn-primary shadow-sm">Add</button>
                    <button style="width: 100%" type="button" class="btn btn-light border"
                      data-bs-dismiss="modal">Close</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <hr>
          <!-- TABLE -->
          <?php
          include("../connection.php");

          $driverTableSql = "SELECT * FROM tbl_drivers";
          $driverTableResult = mysqli_query($con, $driverTableSql);
          ?>
          <table class="table table-hover table-sm">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Driver's License</th>
                <th scope="col">Name</th>
                <th scope="col">Address</th>
                <th scope="col" class="text-end">Contact Number</th>
                <th scope="col" class="text-end">Availability</th>
                <th scope="col" class="text-end">Date Submitted</th>
                <th scope="col" class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 0;
              if (mysqli_num_rows($driverTableResult) > 0) {
                while ($row = mysqli_fetch_array($driverTableResult)) {
                  $count++;
              ?>
                  <tr style="cursor: pointer;">
                    <th scope="row"><?= $count; ?></th>
                    <td><?= $row['Driverslicense'] ?></td>
                    <td><?= $row['Name'] ?></td>
                    <td><?= $row['Address'] ?></td>
                    <td class="text-end"><?= $row['ContactNumber'] ?></td>
                    <td class="text-end">
                      <span class="badge text-bg-warning text-white text-uppercase">
                        <?= $row['Availability'] ?>
                      </span>
                    </td>
                    <td class="text-end">
                      <?php $date = new DateTime($row['CreatedAt']);
                      echo date_format($date, "M d, Y"); ?>
                    </td>
                    <td class="text-end" style="width: 140px;">
                      <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-primary w-25" data-bs-toggle="modal"
                          data-bs-target="#editDriverModal<?= $row["DriversID"] ?>">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <!-- EDIT MODAL -->
                        <div class="modal fade" id="editDriverModal<?= $row["DriversID"] ?>" tabindex="-1"
                          aria-labelledby="editDriverModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <form class="modal-content" method="post" action="../functions.php">
                              <div class="modal-header">
                                <h1 class="modal-title fs-5" id="editDriverModalLabel">Modify <?= $row["Name"] ?>&apos;
                                  Profile
                                </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body d-flex flex-column gap-2">
                                <input type="hidden" name="driverID" value="<?= $row["DriversID"] ?>">

                                <input type="text" placeholder="Driver's Licese" class="form-control" name="driverLicense"
                                  value="<?= $row["Driverslicense"] ?>" required>

                                <input type="text" placeholder="Name" class="form-control" name="name"
                                  value="<?= $row["Name"] ?>" required>

                                <input type="text" placeholder="Address" class="form-control" name="address"
                                  value="<?= $row["Address"] ?>" required>

                                <input type="number" placeholder="Contact Number" class="form-control" name="contactNumber"
                                  value="<?= $row["ContactNumber"] ?>" required>

                                <select name="availability" class="form-select">
                                  <option value="Available">Available</option>
                                  <option value="Unavailable">Unavailable</option>
                                </select>
                              </div>

                              <div class="modal-footer">
                                <button style="width: 100%" type="submit" name="editDriverBtn"
                                  class="btn btn-primary shadow-sm"><i class="bi bi-floppy"></i> Submit</button>
                                <button style="width: 100%" type="button" class="btn btn-light border"
                                  data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Close</button>
                              </div>
                            </form>
                          </div>
                        </div>
                        <form action="../functions.php" method="post">
                          <input type="hidden" value="<?= $row['DriversID'] ?>" name="driverID">
                          <button type="submit" name="deleteDriverBtn" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php
                }
              } else {
                ?>
                <tr>
                  <td colspan="8">
                    <h6 class="text-center my-auto">No driver registered</h6>
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