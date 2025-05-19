<?php
// Course information
$cursoInfo = [
    'titulo' => 'A3 REP',
    'descripcion' => 'Curso completo sobre A3 REP para personal interno'
];

// Module mapping to display names
$modulosMap = [
    'accesso-y-seguridad' => 'Acceso y Seguridad',
    'introducción' => 'Introducción'
    // Add other modules as they are created
];

// Topic mapping to display names
$temasMap = [
    'acceso-a-a3-rep' => 'Acceso a A3 REP',
    'cambio-tipo-contable' => 'Cambio de Tipo Contable',
    'configuracion-de-usuario' => 'Configuración de Usuario',
    'crear-nuevo-perfil-seguridad' => 'Crear Nuevo Perfil de Seguridad',
    'crear-usuario' => 'Crear Usuario',
    'definir-permisos-de-seguridad-del-perfil' => 'Definir Permisos de Seguridad del Perfil',
    'bienvenida_airam' => 'Bienvenida al Curso'
    // Add other topics as needed
];

// Function to scan for modules
function getModulos() {
    $modulos = [];
    $basePath = 'contenido/videos/';
    
    // Scan for module directories
    if (is_dir($basePath)) {
        $dirs = scandir($basePath);
        foreach ($dirs as $dir) {
            if ($dir != '.' && $dir != '..' && is_dir($basePath . $dir)) {
                $modulos[] = $dir;
            }
        }
    }
    
    return $modulos;
}

// Function to scan for topics within a module
function getTemas($modulo) {
    $temas = [];
    $basePath = 'contenido/videos/' . $modulo . '/';
    
    // Scan for topic directories
    if (is_dir($basePath)) {
        $dirs = scandir($basePath);
        foreach ($dirs as $dir) {
            if ($dir != '.' && $dir != '..' && is_dir($basePath . $dir)) {
                $temas[] = $dir;
            }
        }
    }
    
    return $temas;
}

// Function to get video file within a topic
function getVideo($modulo, $tema) {
    $videoPath = 'contenido/videos/' . $modulo . '/' . $tema . '/';
    
    // Find the first MP4 file
    if (is_dir($videoPath)) {
        $files = scandir($videoPath);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'mp4') {
                return $videoPath . $file;
            }
        }
    }
    
    return null;
}

// Function to get module name for display
function getModuloName($modulo) {
    global $modulosMap;
    return $modulosMap[$modulo] ?? ucfirst(str_replace('-', ' ', $modulo));
}

// Function to get topic name for display
function getTemaName($tema) {
    global $temasMap;
    return $temasMap[$tema] ?? ucfirst(str_replace('-', ' ', $tema));
}
?>