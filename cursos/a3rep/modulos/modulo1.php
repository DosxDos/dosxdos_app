<?php
// Get the list of topics for this module
$temas = getTemas($modulo);
$moduloName = getModuloName($modulo);
?>

<div class="py-6">
    <div class="flex items-center mb-6">
        <a href="formacion.php?curso=<?php echo $curso; ?>" class="text-red-600 hover:text-red-800 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold"><?php echo $moduloName; ?></h1>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-bold mb-4">Vídeos disponibles</h2>
        </div>
        
        <ul class="divide-y divide-gray-200">
            <?php foreach ($temas as $tema): 
                $temaName = getTemaName($tema);
            ?>
                <li>
                    <a href="formacion.php?curso=<?php echo $curso; ?>&modulo=<?php echo $modulo; ?>&tema=<?php echo $tema; ?>" 
                       class="flex items-center p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="mr-4 text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium"><?php echo $temaName; ?></h3>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>