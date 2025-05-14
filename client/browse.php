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
  <title>Car Rental - Browse</title>
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
          <li><a href="browse.php" style="width: 100%;" class="btn btn-light text-start active"><i
                class="bi bi-house"></i> Browse List</a></li>
          <li><a href="history.php" style="width: 100%;" class="btn btn-light text-start"><i
                class="bi bi-table"></i> My History</a></li>
        </ul>
      </nav>
    </aside>
    <section class="col bg-light-subtle">
      <!-- NAVBAR -->
      <nav class="navbar" style="height: 60px;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
          <span class="navbar-brand mb-0 fs-2 text-uppercase text-primary fw-bolder">Browse Car List</span>

          <!-- Account -->
          <div class="d-flex gap-2 align-items-center">
            <h5 class="text-uppercase"><?= $_SESSION["userName"] ?></h5>
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
        </div>
      </nav>
      <!-- CARD -->
      <div class="container-fluid d-flex flex-wrap gap-3 p-3 h-75">
        <?php
        $sql = "SELECT * FROM tbl_car WHERE Status = 1";
        $res = mysqli_query($con, $sql);
        if (mysqli_num_rows($res) > 0) {
          while ($row = mysqli_fetch_array($res)) {
        ?>
            <div class="card shadow-sm border-0 w-25">
              <img src="<?= $row["Preview"] ?>" class="card-img-top img-fluid" alt="...">
              <div class="card-body">
                <h5 class="card-title"><?= $row["Make"] ?> <?= $row["Model"] ?></h5>
                <p class="card-text">
                  Year Model: <?= $row["YearModel"] ?>
                  <br>
                  Price: ₱<?= number_format($row["BasePrice"], 2) ?>
                </p>
              </div>
              <div class="card-footer">
                <a href="preview.php?id=<?= $row["CarID"] ?>" class="btn btn-primary">View</a>
              </div>
            </div>

          <?php
          }
        } else {
          ?>
          <div class="h-100 w-100 d-flex justify-content-center align-items-center">
            <h4>No available cars at the moment.</h4>
          </div>
        <?php
        }
        ?>
      </div>
    </section>
  </main>
  <h6>Developed By: Imee Rodriguez (©)</h6>
  <h7>Contact no:09216633631</h7>
</body>

</html>