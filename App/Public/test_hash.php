// test_hash.php (MODIFICADO PARA DEBUG)
<?php
// Usamos el autoloader para poder acceder a la clase de seguridad
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Helpers/Autoloader.php';

use App\Helpers\Security\PasswordSecurity;

// 1. Contraseña plana que el usuario introduce
$clave_plana_admin = 'admin123';

// 2. Genera un hash limpio (el que DEBERÍA funcionar)
$hash_generado = PasswordSecurity::encriptar($clave_plana_admin);

// 3. Verifica el hash generado inmediatamente (ESTO DEBE SER TRUE)
$autotest_exitoso = PasswordSecurity::verificar($clave_plana_admin, $hash_generado);

// --- SALIDA DE DIAGNÓSTICO ---
echo "1. Contraseña Plana: " . $clave_plana_admin . "\n";
echo "2. Hash Generado: " . $hash_generado . "\n";
echo "3. Autotest (Generado vs Plano): " . ($autotest_exitoso ? '✅ ÉXITO' : '❌ FALLO') . "\n";
echo "--------------------------------------------------------\n";


// 4. PEGA AQUÍ ABAJO el hash que realmente tienes en tu base de datos
// (Cópialo directamente de la tabla 'usuarios' para el correo admin@portal.com)
$hash_de_la_bd = '$2y$10$Qvzl.IGNZ.6N./ZN/uNkPe7Y1/5c4vxrosJfXo4CLgiW4SVaAYjSq'; 

// 5. Verifica la contraseña plana contra el hash de la BD (ESTO DEBE SER TRUE si el login funciona)
$bd_verificacion = PasswordSecurity::verificar($clave_plana_admin, $hash_de_la_bd);

echo "4. Hash en la BD: " . $hash_de_la_bd . "\n";
echo "5. Resultado de verificación (BD vs Plano): " . ($bd_verificacion ? '✅ ÉXITO' : '❌ FALLO') . "\n";
?>