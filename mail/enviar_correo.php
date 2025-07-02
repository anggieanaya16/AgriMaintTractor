<?php
function enviarCorreo($correoDestino, $nombre, $clave) {
    $asunto = "Bienvenido al sistema de tractores";
    $mensaje = "Hola $nombre,\n\nTu cuenta ha sido creada.\nCorreo: $correoDestino\nContraseña: $clave\n\nPor favor cambia la contraseña al iniciar sesión.";
    $cabeceras = "From: no-responder@tusistema.com";

    mail($correoDestino, $asunto, $mensaje, $cabeceras);
}
