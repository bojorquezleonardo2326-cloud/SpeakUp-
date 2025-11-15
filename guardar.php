<?php
include("Conexion_B.php");
if (
    isset($_POST['Nombre_C']) && isset($_POST['Apellido_P']) && isset($_POST['Apellido_M']) &&
    isset($_POST['Num_Telefono']) && isset($_POST['Direccion']) && isset($_POST['Correo']) &&
    isset($_POST['Fecha_N']) && isset($_POST['Localidad']) && isset($_POST['Codigo_P']) &&
    isset($_POST['Estado'])
) {
    $Nombre_C = mysqli_real_escape_string($conexion, $_POST['Nombre_C']);
    $Apellido_P = mysqli_real_escape_string($conexion, $_POST['Apellido_P']);
    $Apellido_M = mysqli_real_escape_string($conexion, $_POST['Apellido_M']);
    $Num_Telefono = mysqli_real_escape_string($conexion, $_POST['Num_Telefono']);
    $Direccion = mysqli_real_escape_string($conexion, $_POST['Direccion']);
    $Correo = mysqli_real_escape_string($conexion, $_POST['Correo']);
    $Fecha_N = mysqli_real_escape_string($conexion, $_POST['Fecha_N']);
    $Localidad = mysqli_real_escape_string($conexion, $_POST['Localidad']);
    $Codigo_P = mysqli_real_escape_string($conexion, $_POST['Codigo_P']);
    $Estado = mysqli_real_escape_string($conexion, $_POST['Estado']);
    $sql_contactos = "INSERT INTO contactos (Nombre_C, Apellido_P, Apellido_M, Num_Telefono, Direccion, Correo, Fecha_N, Localidad, Codigo_P, Estado) 
                      VALUES ('$Nombre_C', '$Apellido_P', '$Apellido_M', '$Num_Telefono', '$Direccion', '$Correo', '$Fecha_N', '$Localidad', '$Codigo_P', '$Estado')";
    if (mysqli_query($conexion, $sql_contactos)) {
        echo "Datos guardados correctamente.";
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
} else {
    echo "Debes enviar todos los datos requeridos.";
}
mysqli_close($conexion);
?>
