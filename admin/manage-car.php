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
	<title>Car Rental - Manage Vehicles</title>
</head>

<body class="overflow-x-hidden">
	<main style="height: 100vh; width: 100%;" class="row">
		<!-- SIDEBAR -->
		<?php include "sidebar.php"; ?>
		<section class="col bg-tertiary">
			<!-- NAVBAR -->
			<nav class="navbar" style="height: 60px;">
				<div class="container-fluid d-flex justify-content-between align-items-center">
					<span class="navbar-brand mb-0 fs-2 text-uppercase text-primary fw-bolder">Manage vehicles</span>
					<!-- Account -->
					<div class="btn-group">
						<button type="button" class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="bi bi-person-circle fs-4 "></i>
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
						<h5 class="card-title">List of Available Vehicles</h5>
						<!-- Add Vehicle Offcanvas -->
						<button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop" aria-controls="staticBackdrop">
							<i class="bi bi-plus-square-fill"></i>
							&nbsp;&nbsp;Add Vehicle
						</button>
						<div class="offcanvas offcanvas-end" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop" aria-labelledby="staticBackdropLabel">
							<div class="offcanvas-header">
								<h5 class="offcanvas-title" id="staticBackdropLabel">Add Car Entry</h5>
								<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
							</div>
							<div class="offcanvas-body">
								<form action="../functions.php" method="post"
									class="d-flex flex-column gap-2">
									<h5>Vehicle Owner</h5>
									<!-- Button trigger modal -->
									<input type="hidden" name="ownerID" id="ownerID">
									<div class="d-flex gap-2">
										<input type="text" id="ownerName" placeholder="Owner" class="form-control" readonly>
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
													<h1 class="modal-title fs-5" id="driverModalLabel">List of Vehicle Owners</h1>
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
															$driverSql = "SELECT * FROM tbl_vehicleowner";
															$driverSqlRes = mysqli_query($con, $driverSql);
															$count = 0;
															while ($row = mysqli_fetch_array($driverSqlRes)) {
																$count++;
															?>
																<tr>
																	<th scope="row"><?= $count ?></th>
																	<td><?= $row["Name"] ?></td>
																	<td class="text-end">
																		<button
																			type="button"
																			class="btn btn-sm btn-primary"
																			data-bs-dismiss="modal"
																			onclick="setOwner('<?= $row['OwnerID'] ?>', '<?= $row['Name'] ?>')">
																			Select
																		</button>
																	</td>
																</tr>
															<?php
															}
															?>
															<script>
																function setOwner(ownerID, ownerName) {
																	// Set the value of the hidden input field
																	document.getElementById("ownerID").value = ownerID;

																	// Set the value of the customerName input field
																	document.getElementById("ownerName").value = ownerName;
																}
															</script>

														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
									<h5>Vehicle Info</h5>
									<input type="text" placeholder="Model" class="form-control" name="model" required>
									<input type="text" placeholder="Year Model" class="form-control" name="yearModel" required>
									<input type="text" placeholder="Make" class="form-control" name="make" required>
									<input type="text" placeholder="Plate Number" class="form-control" name="plateNumber" required>
									<input type="number" placeholder="Base Price" class="form-control" name="basePrice" required>
									<h5>Preview</h5>
									<input type="file" id="image-input" />
									<input type="hidden" name="image-binary" id="image-binary" required>
									<script src="../scripts/binaryimage.js"></script>
									<hr>
									<button style="width: 100%" type="submit" name="addCarBtn"
										class="btn btn-primary shadow-sm">Add</button>
								</form>
							</div>
						</div>

						<div class="modal fade" id="addCarModal" tabindex="-1" aria-labelledby="addCarModalLabel"
							aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered">
								<form class="modal-content" method="post" action="../functions.php">
									<div class="modal-header">
										<h1 class="modal-title fs-5" id="addCarModalLabel">Add Vehicle Entry</h1>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									</div>
									<div class="modal-body d-flex flex-column gap-2">
										<h5>Vehicle Info</h5>
										<input type="text" placeholder="Model" class="form-control" name="model" required>
										<input type="text" placeholder="Year Model" class="form-control" name="yearModel" required>
										<input type="text" placeholder="Make" class="form-control" name="make" required>
										<input type="text" placeholder="Plate Number" class="form-control" name="plateNumber" required>
										<input type="number" placeholder="Base Price" class="form-control" name="basePrice" required>
										<h5>Preview</h5>
										<input type="file" id="image-input" />
										<input type="hidden" name="image-binary" id="image-binary" required>
										<script src="../scripts/binaryimage.js"></script>
									</div>
									<div class="modal-footer">
										<button style="width: 100%" type="submit" name="addCarBtn"
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
					$carTableSql = "SELECT * FROM tbl_car";
					$carTableResult = mysqli_query($con, $carTableSql);
					?>
					<table class="table table-hover">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">Model</th>
								<th scope="col">Year Model</th>
								<th scope="col">Make</th>
								<th scope="col" class="text-end">Plate Number</th>
								<th scope="col" class="text-end">Base Price</th>
								<th scope="col" class="text-end">Status</th>
								<th scope="col" class="text-end">Date Submitted</th>
								<th scope="col" class="text-end">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 0;
							if (mysqli_num_rows($carTableResult) > 0) {
								while ($row = mysqli_fetch_array($carTableResult)) {
									$count++;
							?>
									<tr style="cursor: pointer;">
										<th scope="row"><?= $count; ?></th>
										<td><?= $row['Model'] ?></td>
										<td><?= $row['YearModel'] ?></td>
										<td><?= $row['Make'] ?></td>
										<td class="text-end"><?= $row['PlateNumber'] ?></td>
										<td class="text-end"><?= number_format($row['BasePrice'], 2) ?></td>

										<td class="text-end">
											<span class="badge text-bg-warning text-uppercase text-white">
												<?php
												if ($row['Status'] == 0) {
													echo "Unavailable";
												} else {
													echo "Available";
												}
												?>
											</span>
										</td>
										<td class="text-end">
											<?php $date = new DateTime($row['CreatedAt']);
											echo date_format($date, "M d, Y h:m a"); ?>
										</td>
										<td class="text-end">
											<div class="d-flex justify-content-end gap-1">
												<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
													data-bs-target="#editCarModal<?= $row["CarID"] ?>">
													<i class="bi bi-pencil"></i>
												</button>
												<!-- EDIT MODAL -->
												<div class="modal fade" id="editCarModal<?= $row["CarID"] ?>" tabindex="-1"
													aria-labelledby="editCarModalLabel" aria-hidden="true">
													<div class="modal-dialog modal-dialog-centered modal-xl">
														<div class="modal-content">
															<div class="modal-header justify-content-between">
																<h1 class="modal-title fs-5" id="editCarModalLabel">Modify Car Entry
																	&quot;<?= $row["Model"] ?> <?= $row["YearModel"] ?>&quot;
																</h1>
																<!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
																<form action="../functions.php" method="post">
																	<input type="hidden" name="carID" value="<?= $row["CarID"] ?>" />
																	<button class="btn btn-outline-warning" name="toggleCarStatus">
																		Mark as <?php
																						if ($row["Status"] == 1) {
																							echo "Unavailable";
																						} else {
																							echo "Available";
																						}
																						?>
																	</button>
																</form>
															</div>
															<form method="post" action="../functions.php">
																<div class="modal-body d-flex gap-3 text-start">
																	<div style="width: 500px;">
																		<h5>Preview</h5>
																		<div>
																			<img src="<?= $row["Preview"] ?>" alt="Preview" class="img-fluid img-thumbnail">
																		</div>
																	</div>
																	<div class="w-100">
																		<input type="hidden" name="carID" value="<?= $row["CarID"] ?>">
																		<h5>Model</h5>
																		<input type="text" placeholder="Model" class="form-control" value="<?= $row["Model"] ?>" name="model" required>

																		<h5>Year Model</h5>
																		<input type="text" placeholder="Year Model" class="form-control" name="yearModel" value="<?= $row["YearModel"] ?>" required>

																		<h5>Make</h5>
																		<input type="text" placeholder="Make" class="form-control" name="make" value="<?= $row["Make"] ?>" required>

																		<h5>Plate Number</h5>
																		<input type="number" placeholder="Plate Number" class="form-control" name="plateNumber" value="<?= $row["PlateNumber"] ?>">

																		<h5>Base Price</h5>
																		<input type="text" placeholder="Base Price" class="form-control" name="basePrice" value="<?= number_format($row["BasePrice"], 2) ?>" required>

																	</div>

																</div>
																<div class="modal-footer">
																	<button style="width: 100%" type="submit" name="editCarBtn"
																		class="btn btn-primary shadow-sm"><i class="bi bi-floppy"></i> Submit</button>
																	<button style="width: 100%" type="button" class="btn btn-light border"
																		data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Close</button>
																</div>
															</form>
														</div>
													</div>
												</div>
												<form action="../functions.php" method="post">
													<input type="hidden" value="<?= $row['CarID'] ?>" name="CarID">
													<button type="submit" name="deleteCarBtn" class="btn btn-sm btn-outline-danger">
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
									<td colspan="9">
										<h6 class="text-center my-auto">No car registered.</h6>
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