<?php
date_default_timezone_set(timezoneId: 'Asia/Manila');
session_start();
function getUserIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // In case of multiple IPs, return the first one
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
include "connection.php";

// CREATE ACCOUNT
if (isset($_POST["addAccountBtn"])) {
    $name = htmlspecialchars($_POST["name"]);
    $email = $_POST["email"];
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);

    $hashPassword = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO tbl_user (Name, Email, Username, `Password`, Role)
        VALUES ('$name', '$email', '$username', '$hashPassword', 'client')";

    if (mysqli_query($con, $sql)) {
        $id = mysqli_insert_id($con);
        $_SESSION["userID"] = $id;
        $_SESSION["userName"] = $name;

        // Add to audit logs
        $ip = getUserIP();
        $auditSql = "INSERT INTO tbl_audit (UserID, `Action`, `Status`, IPAddress, CreatedAt) VALUES ({$_SESSION["userID"]}, 'An account has been created.', 'Account Created Successfully', '$ip', NOW())";
        mysqli_query($con, $auditSql);

        header("Location: ./client/browse.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        header("Location: ./client/create-account.php?error=adding-customer");
    }
}

//  NEW LOGIN
if (isset($_POST["loginBtn"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM tbl_user WHERE Username = '$username'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        // Check if account lock time has passed 5 mins
        if ($row["accountLocked"] == 1) {
            $lockTime = strtotime($row["lockTimestamp"]);
            $currentTime = time();
            $timeDiff = $currentTime - $lockTime;

            if ($timeDiff < 60) {
                // Add to audit logs
                $ip = getUserIP();
                $auditSql = "INSERT INTO tbl_audit (UserID, `Action`, `Status`, IPAddress, CreatedAt) VALUES ({$row['UserID']}, 'Failed login attempt', 'Account Locked', '$ip', NOW())";
                mysqli_query($con, $auditSql);
                header("Location: ./login/index.php?error=account-locked");
                exit();
            } else {
                // Unlock account
                $unlockSql = "UPDATE tbl_user SET accountLocked = 0, loginAttempts = 0, lockTimestamp = NULL WHERE UserID = {$row['UserID']}";
                mysqli_query($con, $unlockSql);

                // Add to audit logs
                $ip = getUserIP();
                $auditSql = "INSERT INTO tbl_audit (UserID, `Action`, `Status`, IPAddress, CreatedAt) VALUES ({$row['UserID']}, 'Account has been unlocked.', 'Account Unlocked', '$ip', NOW())";
                mysqli_query($con, $auditSql);

                // Re-fetch updated user info
                $result = mysqli_query($con, $sql);
                $row = mysqli_fetch_array($result);
            }
        }


        // Use password_verify to check the password
        if (password_verify($password, $row["Password"])) {
            $_SESSION["role"] = $row["Role"];
            $_SESSION["UserID"] = $row["UserID"];
            $_SESSION["userName"] = $row["Name"];

            // Reset login attempts on successful login
            $sql = "UPDATE tbl_user SET loginAttempts = 0, accountLocked = 0, lockTimestamp = NULL WHERE UserID = {$row['UserID']}";
            mysqli_query($con, $sql);

            // Add to audit logs
            $ip = getUserIP();
            $auditSql = "INSERT INTO tbl_audit (UserID, `Action`, `Status`, IPAddress, CreatedAt) VALUES ({$row['UserID']}, 'Account has been logged in.', 'Account Login', '$ip', NOW())";
            mysqli_query($con, $auditSql);

            if ($row["Role"] == "admin") {
                header("Location: ./admin/dashboard.php");
            } else {
                header("Location: ./client/browse.php");
            }
        } else {
            // If password is incorrect, increment the login attempts
            $sql = "UPDATE tbl_user SET loginAttempts = loginAttempts + 1 WHERE UserID = {$row['UserID']}";
            mysqli_query($con, $sql);

            // Add to audit logs
            $ip = getUserIP();
            $auditSql = "INSERT INTO tbl_audit (UserID, `Action`, `Status`, IPAddress, CreatedAt) VALUES ({$row['UserID']}, 'Account failed to login', 'Account Login Unsuccessfull', '$ip', NOW())";
            mysqli_query($con, $auditSql);

            if ($row["loginAttempts"] >= 3) {
                $lockSql = "UPDATE tbl_user SET accountLocked = 1, lockTimestamp = NOW() WHERE UserID = {$row['UserID']}";
                mysqli_query($con, $lockSql);

                // Add to audit logs
                $ip = getUserIP();
                $auditSql = "INSERT INTO tbl_audit (UserID, `Action`, `Status`, IPAddress, CreatedAt) VALUES ({$row['UserID']}, 'Account has been locked', 'Account Locked', '$ip', NOW())";
                mysqli_query($con, $auditSql);

                header("Location: ./login/index.php?error=account-locked");
            } else {
                header("Location: ./login/index.php?error=invalid-password");
            }
        }
    } else {
        header("Location: ./login/index.php?error=user-not-found");
    }
}

// LOCK ACCOUNT
if (isset($_POST["lockAccountBtn"])) {
    $userID = $_POST["userID"];

    $sql = "UPDATE tbl_user SET accountLocked = 1, lockTimestamp = NOW() WHERE UserID = '$userID'";
    if (mysqli_query($con, $sql)) {
        // Add to audit logs
        $ip = getUserIP();
        $auditSql = "INSERT INTO tbl_audit (UserID, `Action`, `Status`, IPAddress, CreatedAt) VALUES ({$_SESSION["UserID"]}, 'User ($userID) account has been locked.', 'Account Locked Successfully', '$ip', NOW())";
        mysqli_query($con, $auditSql);
        header("Location: ./admin/manage-users.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

// UNLOCK ACCOUNT
if (isset($_POST["unlockAccountBtn"])) {
    $userID = $_POST["userID"];

    $sql = "UPDATE tbl_user SET accountLocked = 0, lockTimestamp = NULL WHERE UserID = '$userID'";
    if (mysqli_query($con, $sql)) {
        // Add to audit logs
        $ip = getUserIP();
        $auditSql = "INSERT INTO tbl_audit (UserID, `Action`, `Status`, IPAddress, CreatedAt) VALUES ({$_SESSION["UserID"]}, 'User ($userID) account has been unlocked.', 'Account Unlocked Successfully', '$ip', NOW())";
        if (mysqli_query($con, $auditSql)) {
            header("Location: ./admin/manage-users.php");
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}


// LOGOUT FUNCTION
if (isset($_POST["logoutBtn"])) {
    session_destroy();
    header("Location: ./index.php");
}

// CUSTOMER CRUD
if (isset($_POST["addCustomerBtn"])) {
    $name = htmlspecialchars($_POST["name"]);
    $contact = $_POST["contact"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "INSERT INTO tbl_customer (Name, Address, ContactNumber, Email, Username, `Password`)
        VALUES ('$name', '$address', '$contact', '$email', '$username', '$password')";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-customer.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        header("Location: ./admin/manage-customer.php?error=adding-customer");
    }
}

if (isset($_POST["editCustomerBtn"])) {
    $customerID = $_POST["customerID"];
    $name = $_POST["name"];
    $contact = $_POST["contact"];
    $email = $_POST["email"];
    $address = $_POST["address"];

    $sql = "UPDATE tbl_customer SET Name = '$name', Address = '$address', ContactNumber = '$contact', Email = '$email' WHERE CustomerID = '$customerID'";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-customer.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

if (isset($_POST["deleteCustomerBtn"])) {
    $customerID = $_POST["customerID"];

    $sql = "DELETE FROM tbl_customer WHERE CustomerID = '$customerID'";
    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-customer.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

// USER CRUD
if (isset($_POST["addUserBtn"])) {
    $name = htmlspecialchars($_POST["name"]);
    $contact = $_POST["contact"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);
    $role = $_POST["role"];

    $sql = "INSERT INTO tbl_user (Name, Address, Contact, Email, Username, `Password`, Role)
        VALUES ('$name', '$address', '$contact', '$email', '$username', '$password', '$role')";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-users.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        header("Location: ./admin/manage-users.php?error=adding-user");
    }
}

if (isset($_POST["editUserBtn"])) {
    $userID = $_POST["userID"];
    $name = $_POST["name"];
    $contact = $_POST["contact"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    $sql = "UPDATE tbl_user SET Name = '$name', Address = '$address',       Contact = '$contact', Email = '$email', Username = '$username', `Password` = '$password', Role = '$role'
            WHERE UserID = '$userID'";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-users.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        header("Location: ./admin/manage-users.php?error=editing-user");
    }
}

if (isset($_POST["deleteUserBtn"])) {
    $userID = $_POST["userID"];

    $sql = "DELETE FROM tbl_user WHERE UserID = '$userID'";
    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-users.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        header("Location: ./admin/manage-users.php?error=delete-user");
    }
}

// OWNER CRUD
if (isset($_POST["addOwnerBtn"])) {
    $name = $_POST["name"];
    $contact = $_POST["contact"];
    $email = $_POST["email"];
    $address = $_POST["address"];

    $sql = "INSERT INTO tbl_vehicleowner (Name, Address, ContactNumber, Email) 
            VALUES ('$name', '$address', '$contact', '$email')";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-owners.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

if (isset($_POST["editOwnerBtn"])) {
    $ownerID = $_POST["ownerID"];
    $name = htmlspecialchars($_POST["name"]);
    $contact = $_POST["contact"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $vehicle = $_POST["vehicle"];

    $sql = "UPDATE tbl_vehicleowner SET Name = '$name', Address = '$address', ContactNumber = '$contact', Email = '$email', VehicleID = '$vehicle' WHERE OwnerID = '$ownerID'";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-owners.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

if (isset($_POST["deleteOwnerBtn"])) {
    $ownerID = $_POST["ownerID"];

    $sql = "DELETE FROM tbl_vehicleowner WHERE OwnerID = '$ownerID'";
    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-owners.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        header("Location: ./admin/manage-owners.php?error=delete-owner");
    }
}

// CAR CRUD
if (isset($_POST["addCarBtn"])) {
    $ownerID = $_POST["ownerID"];
    $model = $_POST["model"];
    $yearModel = $_POST["yearModel"];
    $make = $_POST["make"];
    $plateNumber = $_POST["plateNumber"];
    $basePrice = $_POST["basePrice"];
    $preview = $_POST["image-binary"];

    $sql = "INSERT INTO tbl_car (Model, YearModel, Make, PlateNumber, OwnerID, Preview, BasePrice, `Status`)
        VALUES ('$model', '$yearModel', '$make', '$plateNumber', '$ownerID', '$preview', '$basePrice', 1)";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-car.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

if (isset($_POST["editCarBtn"])) {
    $carID = $_POST["carID"];
    $model = $_POST["model"];
    $yearModel = $_POST["yearModel"];
    $make = $_POST["make"];
    $plateNumber = $_POST["plateNumber"];
    $basePrice = $_POST["basePrice"];

    $sql = "UPDATE tbl_car SET Model = '$model', YearModel = '$yearModel', 
        Make = '$make', PlateNumber = '$plateNumber', BasePrice = '$basePrice'
        WHERE CarID = '$carID'";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-car.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

if (isset($_POST["deleteCarBtn"])) {
    $carID = $_POST["CarID"];

    $sql = "DELETE FROM tbl_car WHERE CarID = '$carID'";
    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-car.php?success=delete");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        header("Location: ./admin/manage-car.php?error=delete");
    }
}

if (isset($_POST['toggleCarStatus'])) {
    $carID = $_POST["carID"];

    $sql = "UPDATE tbl_car 
        SET Status = CASE 
                        WHEN Status = 0 THEN 1 
                        ELSE 0
                    END
        WHERE CarID = $carID";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-car.php?status=success");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        header("Location: ./admin/manage-car.php?status=failed");
    }
}

// DRIVER CRUD
if (isset($_POST["addDriverBtn"])) {
    $driverLicense = $_POST["driverLicense"];
    $name = htmlspecialchars($_POST["name"]);
    $address = $_POST["address"];
    $contactNumber = $_POST["contactNumber"];
    $availability = $_POST["availability"];

    $sql = "INSERT INTO tbl_drivers (Driverslicense, `Name`, `Address`, ContactNumber, `Availability`) VALUES ('$driverLicense', '$name', '$address', '$contactNumber', '$availability')";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-driver.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

if (isset($_POST["editDriverBtn"])) {
    $driverID = $_POST["driverID"];
    $driverLicense = $_POST["driverLicense"];
    $name = $_POST["name"];
    $address = $_POST["address"];
    $contactNumber = $_POST["contactNumber"];
    $availability = $_POST["availability"];

    $sql = "UPDATE tbl_drivers SET Driverslicense = '$driverLicense', `Name` = '$name', `Address` = '$address', ContactNumber = '$contactNumber', `Availability` = '$availability' WHERE DriversID = '$driverID'";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-driver.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

if (isset($_POST["deleteDriverBtn"])) {
    $driverID = $_POST["driverID"];

    $sql = "DELETE FROM tbl_drivers WHERE DriversID = '$driverID'";
    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/manage-driver.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

// TRANSACTION CRUD
if (isset($_POST["addTransactionBtn"])) {
    $customerID = $_POST["customerID"];
    $vehicleID = $_POST["vehicleId"];
    $driverID = $_POST["driverId"];
    $rentalDate = $_POST["rentalDate"];
    $returnDate = $_POST["returnDate"];
    $userID = $_SESSION["UserID"];
    // $rate = $_POST["totalCost"];

    $rateSql = "SELECT * FROM tbl_car WHERE CarID = $vehicleID";
    $rateRes = mysqli_query($con, $rateSql);
    $rateRow = mysqli_fetch_array($rateRes);

    $price = $rateRow["BasePrice"];

    $rentDay = new DateTime($rentalDate);
    $returnDay = new DateTime($returnDate);
    $interval = $rentDay->diff($returnDay);
    $days = $interval->days;
    $total = $price * $days;

    $sql = "INSERT INTO tbl_rental (CustomerID, AdminID, CarID, DriverID, RentalDate, ReturnDate, TotalCost, `Status`)
        VALUES ('$customerID', '$userID', '$vehicleID', '$driverID', '$rentalDate', '$returnDate', '$total' , 'pending')";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/transactions.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        header("Location: ./admin/transactions.php?error=adding-transaction");
    }
}

if (isset($_POST["acceptTransactionBtn"])) {
    $rentalID = $_POST["rentalID"];

    $sql = "UPDATE tbl_rental SET `Status` = 'pending' WHERE RentalID = '$rentalID'";
    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/transactions.php?status=confirmed");
    } else {
        header("Location: ./admin/transactions.php?status=failed-confimed");
    }
}

if (isset($_POST["finishTransactionBtn"])) {
    $rentalID = $_POST["rentalID"];
    $payment = $_POST["paymentMethod"];

    $sql = "
        UPDATE tbl_rental SET `Status` = 'done' WHERE RentalID = '$rentalID';
        INSERT INTO tbl_payment (RentalID, PaymentMethod) VALUES ('$rentalID', '$payment')";

    if (mysqli_multi_query($con, $sql)) {
        header("Location: ./admin/transactions.php?status=success");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        header("Location: ./admin/transactions.php?error=updating-transaction");
    }
}

if (isset($_POST["cancelTransactionBtn"])) {
    $rentalID = $_POST["rentalID"];

    $sql = "UPDATE tbl_rental SET `Status` = 'cancelled'
            WHERE RentalID = $rentalID";

    if (mysqli_query($con, $sql)) {
        header("Location: ./admin/transactions.php?status=success");
    } else {
        header("Location: ./admin/transactions.php?status=failed");
    }
}


// RENTAL CRUD
if (isset($_POST["rentBtn"])) {
    $customerID = $_POST["customerID"];
    $carID = $_POST["carID"];
    $driverID = $_POST["driverId"];
    $rentalDate = $_POST["rentalDate"];
    $returnDate = $_POST["returnDate"];
    $rate = $_POST["totalCost"];

    $rentDay = new DateTime($rentalDate);
    $returnDay = new DateTime($returnDate);
    $interval = $rentDay->diff($returnDay);
    $days = $interval->days;
    $total = $rate * $days;

    $sql = "INSERT INTO tbl_rental (CustomerID, CarID, RentalDate, ReturnDate, TotalCost, DriverID, Status)
            VALUES ('$customerID', '$carID', '$rentalDate', '$returnDate', '$total', '$driverID', 'booked')";

    echo $sql;

    if (mysqli_query($con, $sql)) {
        header("Location: ./client/history.php?rental=success");
    } else {
        header("Location: ./client/history.php?rental=failed");
    }
}

if (isset($_POST["cancelRentalBtn"])) {
    $rentalID = $_POST["rentalID"];

    $sql = "UPDATE tbl_rental SET `Status` = 'cancelled'
            WHERE RentalID = $rentalID";

    if (mysqli_query($con, $sql)) {
        header("Location: ./client/history.php?status=success");
    } else {
        header("Location: ./client/history.php?status=failed");
    }
}



// DEPRACATED
// LOGIN ADMIN
// if (isset($_POST["adminLogin"])) {
//     $username = $_POST["username"];
//     $password = $_POST["password"];

//     $sql = "SELECT * FROM tbl_admin WHERE Username = '$username'";
//     $result = mysqli_query($con, $sql);

//     if (mysqli_num_rows($result) > 0) {
//         $row = mysqli_fetch_array($result);
//         if ($row["Password"] === $password) {
//             $_SESSION["role"] = "admin";
//             $_SESSION["adminID"] = $row["id"];
//             $_SESSION["user-name"] = $row["Name"];
//             header("Location: ./admin/dashboard.php");
//         } else {
//             header("Location: ./admin/login.php?error=invalid-password");
//         }
//     } else {
//         header("Location: ./admin/login.php?error=user-not-found");
//     }
// }

// // LOGIN CLIENT
// if (isset($_POST["clientLogin"])) {
//     $username = $_POST["username"];
//     $password = $_POST["password"];

//     $sql = "SELECT * FROM tbl_customer WHERE Username = '$username' AND Password = '$password'";

//     $result = mysqli_query($con, $sql);

//     if (mysqli_num_rows($result) > 0) {
//         $row = mysqli_fetch_array($result);
//         $_SESSION["role"] = "client";
//         $_SESSION["customerID"] = $row["CustomerID"];
//         $_SESSION["user-name"] = $row["Name"];
//         header("Location: ./client/browse.php");
//     } else {
//         header("Location: ./login/index.php?error=credentials");
//     }
// }