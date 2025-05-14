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
    <title>Print - My History</title>
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary">Transaction History</h2>
            <button onclick="window.print();" class="btn btn-secondary no-print">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>

        <!-- Fetch Rental History -->
        <?php
        $customerID = $_SESSION["UserID"];
        $whereClause = "";

        // Check if date range is provided
        if (!empty($_GET['from']) && !empty($_GET['to'])) {
            $dateFrom = $_GET['from'];
            $dateTo = $_GET['to'];
            $whereClause = "AND tbl_rental.RentalDate BETWEEN '$dateFrom' AND '$dateTo'";
        }

        // Calculate total revenue within the date range
        $totalRevenueSql = "SELECT SUM(tbl_rental.TotalCost) AS TotalRevenue
                            FROM tbl_rental
                            WHERE tbl_rental.CustomerID = $customerID $whereClause";
        $totalRevenueResult = mysqli_query($con, $totalRevenueSql);
        $totalRevenue = mysqli_fetch_assoc($totalRevenueResult)['TotalRevenue'] ?? 0;

        // Fetch transaction history within the date range
        $rentalSql = "SELECT
                          tbl_rental.RentalID,
                          tbl_rental.`Status`,
                          tbl_rental.RentalDate,
                          tbl_rental.ReturnDate,
                          tbl_rental.CreatedAt,
                          tbl_car.PlateNumber AS Plate,
                          tbl_car.Model,
                          tbl_car.YearModel,
                          tbl_car.Make,
                          tbl_rental.TotalCost 
                      FROM
                          tbl_rental
                          INNER JOIN tbl_customer ON tbl_rental.CustomerID = tbl_customer.CustomerID
                          INNER JOIN tbl_car ON tbl_rental.CarID = tbl_car.CarID 
                      WHERE
                          tbl_rental.CustomerID = $customerID $whereClause";
        $rentalSqlResult = mysqli_query($con, $rentalSql);
        ?>

        <!-- Total Revenue -->
        <div class="mb-4">
            <h5>Total Revenue: ₱<?= number_format($totalRevenue, 2) ?></h5>
        </div>

        <!-- Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Vehicle Rented</th>
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
                    while ($row = mysqli_fetch_array($rentalSqlResult)) {
                        $count++;
                ?>
                        <tr>
                            <td><?= $count; ?></td>
                            <td><?= $row['Make'] ?> <?= $row['Model'] ?> Plate#<?= $row['Plate'] ?></td>
                            <td><?php $rentalDate = new DateTime($row['RentalDate']);
                                echo date_format($rentalDate, "M d, Y"); ?></td>
                            <td><?php $returnDate = new DateTime($row['ReturnDate']);
                                echo date_format($returnDate, "M d, Y"); ?></td>
                            <td>₱<?= number_format($row['TotalCost'], 2) ?></td>
                            <td><?= ucfirst($row['Status']) ?></td>
                            <td><?php $createdAt = new DateTime($row['CreatedAt']);
                                echo date_format($createdAt, "M d, Y h:i a"); ?></td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="7" class="text-center">No rental history available</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

        <!-- Indicator -->
        <?php if (empty($dateFrom) || empty($dateTo)) { ?>
            <div class="alert alert-info">Showing all records (no filter applied).</div>
        <?php } else { ?>
            <div class="alert alert-info">Showing records from <?= $dateFrom ?> to <?= $dateTo ?>.</div>
        <?php } ?>
    </div>
</body>

</html>