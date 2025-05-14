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
  <title>Car Rental - Manage Customers</title>
</head>

<body class="overflow-x-hidden">
  <main style="height: 100vh; width: 100%;" class="row">
    <!-- SIDEBAR -->
    <?php include "sidebar.php"; ?>
    <section class="col bg-tertiary">
      <!-- NAVBAR -->
      <nav class="navbar" style="height: 60px;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
          <span class="navbar-brand mb-0 text-uppercase text-primary fw-bolder fs-2">Manage Users</span>
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
            <h5 class="card-title">List of Registered Users</h5>
            <!-- Add Vehicle trigger modal -->
            <button type="button" class="btn btn-primary" style="min-width: 120px;" data-bs-toggle="modal"
              data-bs-target="#addUserModal">
              <i class="bi bi-plus-square-fill"></i>
              &nbsp;&nbsp;Add User
            </button>

            <!-- Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel"
              aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="post" action="../functions.php">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addUserModalLabel">Add User Entry</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body d-flex flex-column gap-2">
                    <h6>Name</h6>
                    <input type="text" placeholder="Name" class="form-control" name="name" required>
                    <h6>Contact Number</h6>
                    <input type="number" placeholder="Contact Number" class="form-control" name="contact" required>
                    <h6>Email</h6>
                    <input type="email" placeholder="Email" class="form-control" name="email" required>
                    <h6>Address</h6>
                    <input type="text" placeholder="Address" class="form-control" name="address" required>
                    <h6>Username</h6>
                    <input type="text" placeholder="Username" class="form-control" name="username" required>
                    <h6>Password</h6>
                    <input type="password" placeholder="Password" class="form-control" name="password" required>

                    <h6>Role</h6>
                    <select name="role" class="form-select">
                      <option value="admin">Admin</option>
                      <option value="client">Client</option>
                    </select>
                  </div>
                  <div class="modal-footer">
                    <button style="width: 100%" type="submit" name="addUserBtn"
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

          $customerTblSql = "SELECT * FROM tbl_user";
          $customerTblResult = mysqli_query($con, $customerTblSql);
          ?>
          <table class="table table-hover table-sm">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Contact Number</th>
                <th scope="col">Email</th>
                <th scope="col">Address</th>
                <th scope="col">Role</th>
                <th scope="col" class="text-end">Account Status</th>
                <th scope=" col" class="text-end">Date Registered</th>
                <th scope="col" class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 0;
              if (mysqli_num_rows($customerTblResult) > 0) {
                while ($row = mysqli_fetch_array($customerTblResult)) {
                  $count++;
              ?>
                  <tr style="cursor: pointer;">
                    <th scope="row"><?= $count; ?></th>
                    <td><?= $row['Name'] ?></td>
                    <td><?= $row['Contact'] ?? "N/A" ?></td>
                    <td><?= $row['Email'] ?></td>
                    <td><?= $row['Address'] ?? "N/A" ?></td>
                    <td>
                      <span class="badge text-bg-warning text-uppercase">
                        <?= $row['Role'] ?>
                      </span>
                    </td>
                    <td class="text-end">
                      <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <?= $row['accountLocked'] == 1 ? "Locked" : "Unlocked" ?>
                        </button>
                        <ul class="dropdown-menu">
                          <li>
                            <form action="../functions.php" method="post">
                              <input type="hidden" name="userID" value="<?= $row['UserID'] ?>">
                              <button type="submit" name="unlockAccountBtn" class="dropdown-item" <?= $row['accountLocked'] == 0 ? "disabled" : "" ?>>
                                <i class="bi bi-unlock2"></i>
                                Set Unlock</button>
                            </form>
                          </li>
                          <li>
                            <form action="../functions.php" method="post">
                              <input type="hidden" name="userID" value="<?= $row['UserID'] ?>">
                              <button class="dropdown-item" name="lockAccountBtn" <?= $row['accountLocked'] == 0 ? "" : "disabled" ?>>
                                <i class="bi bi-lock"></i>
                                Set Lock</button>
                            </form>
                          </li>
                        </ul>
                      </div>
                    </td>
                    <td class="text-end">
                      <?php $date = new DateTime($row['CreatedAt']);
                      echo date_format($date, "M d, Y"); ?>
                    </td>
                    <td class="text-end" style="width: 140px;">
                      <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-primary w-25" data-bs-toggle="modal"
                          data-bs-target="#editUserModal<?= $row["UserID"] ?>">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <!-- EDIT MODAL -->
                        <div class="modal fade" id="editUserModal<?= $row["UserID"] ?>" tabindex="-1"
                          aria-labelledby="editUserModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <form class="modal-content" method="post" action="../functions.php">
                              <div class="modal-header">
                                <h1 class="modal-title fs-5" id="editUserModalLabel">Modify
                                  <?= $row["Name"] ?>&apos;s
                                  Profile
                                </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body d-flex flex-column gap-2 text-start">
                                <input type="hidden" name="userID" value="<?= $row["UserID"] ?>">

                                <h6>Name</h6>
                                <input type="text" placeholder="Name" class="form-control" name="name"
                                  value="<?= $row["Name"] ?>" required>

                                <h6>Contact Number</h6>
                                <input type="number" placeholder="Contact Number" class="form-control" name="contact"
                                  value="<?= $row["Contact"] ?>" required>

                                <h6>Email</h6>
                                <input type="email" placeholder="Email" class="form-control" name="email"
                                  value="<?= $row["Email"] ?>" required>

                                <h6>Address</h6>
                                <input type="text" placeholder="Address" class="form-control" name="address"
                                  value="<?= $row["Address"] ?>" required>

                                <h6>Username</h6>
                                <input type="text" placeholder="Username" class="form-control" name="username"
                                  value="<?= $row["Username"] ?>" required>

                                <h6>Password</h6>
                                <input type="password" placeholder="Password" class="form-control" name="password"
                                  value="<?= $row["Password"] ?>" required>

                                <h6>Role</h6>
                                <select name="role" class="form-select">
                                  <option value="admin">Admin</option>
                                  <option value="client">Client</option>
                                </select>
                              </div>

                              <div class="modal-footer">
                                <button type="submit" name="editUserBtn" class="btn btn-primary shadow-sm w-100"><i
                                    class="bi bi-floppy"></i>&nbsp;Save</button>
                              </div>
                            </form>
                          </div>
                        </div>
                        <form action="../functions.php" method="post">
                          <input type="hidden" value="<?= $row['UserID'] ?>" name="userID">
                          <button type="submit" name="deleteUserBtn" class="btn btn-sm btn-outline-danger"><i
                              class="bi bi-trash"></i>
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
                  <td colspan="7">
                    <h6 class="text-center my-auto">
                      No user registered.
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