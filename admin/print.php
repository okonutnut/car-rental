<?php
session_start();
include("../connection.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental History - Print</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-4">Rental History</h1>
            <button class="btn btn-primary no-print" onclick="window.print()">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>

        <!-- Total Revenue Section -->
        <div class="mb-4">
            <h4>Total Revenue:
                <?php
                // Calculate the total revenue
                $revenueSql = "SELECT SUM(TotalCost) AS TotalRevenue FROM tbl_rental WHERE Status = 'done'";
                $revenueResult = mysqli_query($con, $revenueSql);
                $revenueRow = mysqli_fetch_array($revenueResult);
                echo "₱" . number_format($revenueRow['TotalRevenue'], 2);
                ?>
            </h4>
        </div>

        <!-- Rental History Table -->
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Rental Date</th>
                    <th>Return Date</th>
                    <th>Total Cost</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT 
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
                            r.Status IN ('done', 'cancelled')";
                $result = mysqli_query($con, $sql);
                $count = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $count++;
                    echo "<tr>
                  <td>{$count}</td>
                  <td>{$row['CustomerName']}</td>
                  <td>{$row['Make']} {$row['Model']} (Plate# {$row['Plate']})</td>
                  <td>{$row['DriverName']}</td>
                  <td>" . date("M d, Y", strtotime($row['RentalDate'])) . "</td>
                  <td>" . date("M d, Y", strtotime($row['ReturnDate'])) . "</td>
                  <td>₱" . number_format($row['TotalCost'], 2) . "</td>
                  <td class='text-uppercase'>" . ucfirst($row['Status']) . "</td>
                </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>