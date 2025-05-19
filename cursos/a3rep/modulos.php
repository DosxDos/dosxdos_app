<div class="py-8">
    <h1 class="text-3xl font-bold text-center mb-8"><?php echo $cursoInfo['titulo']; ?></h1>
    <p class="text-lg text-center mb-10"><?php echo $cursoInfo['descripcion']; ?></p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php 
        $modulos = getModulos();
        foreach ($modulos as $modulo): 
            $moduloName = getModuloName($modulo);
            $temas = getTemas($modulo);
        ?>
            <a href="formacion.php?curso=<?php echo $curso; ?>&modulo=<?php echo $modulo; ?>" 
               class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <h2 class="text-xl font-bold mb-2"><?php echo $moduloName; ?></h2>
                <p class="mb-4">
                    <?php echo count($temas); ?> vídeos disponibles
                </p>
            </a>
        <?php endforeach; ?>
    </div>
</div>