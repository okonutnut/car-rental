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
  <title>Car Rental - Audit Logs</title>
</head>

<body class="overflow-x-hidden">
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
          <li><a href="transactions.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-plus-circle-dotted"></i> Transactions</a></li>
          <li><a href="manage-users.php" style="width: 100%;" class="btn btn-light text-start active"><i
                class="bi bi-people"></i> Manage Users</a>
          </li>
          <li><a href="manage-owners.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-person-circle"></i> Manage Owners</a>
          </li>
          <li><a href="manage-car.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-card-list"></i> Manage Vehicles</a>
          </li>
          <li><a href="manage-driver.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-person-vcard"></i>
              Manage Drivers</a></li>
          <li><a href="rental-history.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-table"></i>
              Rental History</a></li>
          <li><a href="audit-logs.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-table"></i>
              Audit Logs</a></li>
        </ul>
      </nav>
    </aside>
    <section class="col bg-tertiary">
      <!-- NAVBAR -->
      <nav class="navbar" style="height: 60px;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
          <span class="navbar-brand mb-0 text-uppercase text-primary fw-bolder fs-2">
            Audit Logs
          </span>
          <!-- Account -->
          <div class="btn-group">
            <button type="button" class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle fs-4"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <span class="dropdown-item" style="cursor:default;">
                  Signed as
                  <?= $_SESSION["userName"] ?? "User" ?>
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
            <h5 class="card-title">List of Logs</h5>
          </div>
          <hr>
          <!-- TABLE -->
          <?php
          include("../connection.php");

          $auditTblSql = "SELECT 
                            a.Action, 
                            a.Status,
                            a.IPAddress,
                            a.CreatedAt,
                            u.Name 
                          FROM tbl_audit a 
                          JOIN tbl_user u 
                          ON a.UserID = u.UserID 
                          ORDER BY 
                            a.CreatedAt 
                          DESC";
          $auditTblResult = mysqli_query($con, $auditTblSql);
          ?>
          <table class="table table-hover table-sm">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Action</th>
                <th scope="col">Status</th>
                <th scope="col">IP Address</th>
                <th scope=" col" class="text-end">Date Registered</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 0;
              if (mysqli_num_rows($auditTblResult) > 0) {
                while ($row = mysqli_fetch_array($auditTblResult)) {
                  $count++;
              ?>
                  <tr style="cursor: pointer;">
                    <th scope="row"><?= $count; ?></th>
                    <td><?= $row['Name'] ?></td>
                    <td><?= $row['Action'] ?></td>
                    <td>
                      <span class="badge text-bg-warning py-2">
                        <?= $row['Status'] ?>
                      </span>
                    </td>
                    <td><?= ($row['IPAddress'] == "::1" ? "127.0.0.1" : $row['IPAddress']) ?></td>
                    <td class="text-end">
                      <?php $date = new DateTime($row['CreatedAt']);
                      echo date_format($date, "M d, Y h:m a"); ?>
                    </td>
                  </tr>
                <?php
                }
              } else {
                ?>
                <tr>
                  <td colspan="7">
                    <h6 class="text-center my-auto">
                      No logs found.
                    </h6>
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