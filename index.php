<?php
session_start();

if (isset($_SESSION["userID"])) {
  if (isset($_SESSION["role"])) {
    if ($_SESSION["role"] == "admin") {
      header("Location: admin/dashboard.php");
    }
    if ($_SESSION["role"] == "client") {
      header("Location: client/browse.php");
    }
  }
} else {
  header("Location: login/index.php");
}
