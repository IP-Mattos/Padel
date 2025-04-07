<?php
session_start();
require_once "../api/utils.php";
$cod = $_POST['userInput'];
validaCodigo6($cod);
?>