<?php
if (!isset($_COOKIE['login'])) {
    header("location: index.html");
}
?>

<div class="px-4 pt-32 pb-12 bg-gray-100 min-h-screen">
    <section class="container mx-auto  w-full">
        <!-- Page Header -->
        <div class="rounded-xl shadow-lg overflow-hidden relative mb-6">
            <div class="absolute inset-0" style="background-image: url('https://dosxdos.app.iidos.com/img/texture-red.svg'); background-size: contain;"></div>
            <div class="relative p-10 flex justify-between items-start md:items-center text-white">
                <h1 class="text-2xl font-bold mb-2 md:mb-0">Gestión de Usuarios</h1>

                <!-- Create User Button -->
                <a href="https://dosxdos.app.iidos.com/dosxdos.php?modulo=crearUsuario"
                    class="flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-100 text-red-600 rounded-lg transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                    </svg>
                    <span class="hidden md:block">Crear Usuario</span>
                </a>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                <!-- Search Bar -->
                   <div class="relative flex items-center w-full mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <input type="text" id="customSearch"
                        class="shadow w-full h-12 pl-12 pr-4 bg-white/10 border border-white/20 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-red-600 transition-all duration-300 ease-in-out text-black placeholder-white/70"
                        placeholder="Buscar líneas..." />
                </div>

                <div class="table-container">
                    <table id="tablaUsuarios" class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3"></th>
                                <th class="px-4 py-3">Nombre</th>
                                <th class="px-4 py-3">Apellido</th>
                                <th class="px-4 py-3">Clase</th>
                                <th class="px-4 py-3">Correo</th>
                                <th class="px-4 py-3">Usuario</th>
                                <th class="px-4 py-3 hidden">ID</th> <!-- Hidden ID column -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!$conexion) {
                                echo "<tr><td colspan='7' class='text-center text-red-500'>Error de conexión</td></tr>";
                            } else {
                                $query = "SELECT * FROM usuarios WHERE eliminado = 0";
                                $result = $conexion->datos($query);
                                while ($row = $result->fetch_assoc()) {
                                    // Add JSON data for debugging
                                    $userDataJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            ?>
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 cursor-pointer" data-user-id="<?php echo $row['id']; ?>" data-user-info='<?php echo $userDataJson; ?>'>
                                        <td class="px-4 py-3">
                                            <img src="<?php echo $row['imagen'] ?: 'img/usuario.png'; ?>" class="shrink-0 w-12 h-12 rounded-full border border-gray-200 object-cover flex mx-auto" alt="Usuario">
                                        </td>
                                        <td class="px-4 py-3 font-medium"><?php echo $row['nombre']; ?></td>
                                        <td class="px-4 py-3"><?php echo $row['apellido']; ?></td>
                                        <td class="px-4 py-3">
                                            <?php
                                            $classColors = [
                                                'admon' => 'bg-red-100 text-red-800',
                                                'diseno' => 'bg-blue-100 text-blue-800',
                                                'estudio' => 'bg-green-100 text-green-800',
                                                'montador' => 'bg-yellow-100 text-yellow-800',
                                                'oficina' => 'bg-gray-100 text-gray-800',
                                                'cliente' => 'bg-gray-100 text-orange-800'
                                            ];
                                            $badgeClass = $classColors[$row['clase']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $badgeClass; ?>">
                                                <?php echo $row['clase']; ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3"><?php echo $row['correo']; ?></td>
                                        <td class="px-4 py-3 font-medium"><?php echo $row['usuario']; ?></td>
                                        <td class="px-4 py-3 hidden"><?php echo $row['id']; ?></td> <!-- Hidden ID column -->
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- User Details Modal -->
    <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[85vh] flex flex-col overflow-hidden">
            <!-- Modal Header -->
            <div class="text-white p-4 rounded-t-xl flex justify-between items-center sticky top-0 z-10"
                style="background-image: url('https://dosxdos.app.iidos.com/img/texture-red.svg'); background-size: contain;">
                <h2 class="text-xl font-bold pr-4" id="userModalTitle">Detalles del Usuario</h2>
                <button class="text-white hover:text-gray-100" id="closeUserModal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-4 space-y-4 overflow-y-auto flex-grow custom-scrollbar"
                style="max-height: calc(85vh - 120px);">
                <div class="space-y-6">
                    <!-- User Image -->
                    <div class="flex justify-center">
                        <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-gray-200 shadow-md">
                            <img id="userModalImage" src="https://dosxdos.app.iidos.com/img/usuario.png"
                                class="w-full h-full object-cover" alt="User Profile"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
                            <svg class="w-full h-full text-gray-400 hidden" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">USUARIO</h3>
                        <p id="userModalUsername" class="text-gray-900 font-medium">-</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">NOMBRE</h3>
                            <p id="userModalName" class="text-gray-900 font-medium">-</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">APELLIDO</h3>
                            <p id="userModalLastName" class="text-gray-900 font-medium">-</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">CÓDIGO</h3>
                            <p id="userModalCode" class="text-gray-900 font-medium">-</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">CLASE</h3>
                            <p id="userModalClass" class="text-gray-900 font-medium">-</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">CORREO</h3>
                        <p id="userModalEmail" class="text-gray-900 font-medium">-</p>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1">TELÉFONO</h3>
                        <p id="userModalPhone" class="text-gray-900 font-medium">-</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-gray-200 p-4 flex justify-end gap-3">
                <button id="cancelUserModal"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition-colors shadow-sm">
                    Cancelar
                </button>
                <button id="editUserModal"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                    Editar Usuario
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Add desktop-only scrollbar fix style
        const styleElement = document.createElement('style');
        styleElement.textContent = `
            @media (min-width: 1024px) {
                .dataTables_wrapper, 
                .dataTables_scrollBody,
                .table-container,
                .overflow-x-auto {
                    overflow-x: hidden !important;
                }
                
                table.dataTable {
                    width: 100% !important;
                    max-width: 100% !important;
                }
            }

            .custom-length-select {
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%234a5568' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat right 0.5rem center;
                border: 1px solid #d1d5db; 
                padding-left: 1rem; 
                padding-right: 1rem;                
                font-size: 1rem; 
                color: #2d3748;
                transition: all 0.2s ease-in-out;
                width: 50px;
                cursor: pointer;
                height: 2rem; 
                display: flex;
                align-items: center;
            }

            .custom-length-select:focus {
                outline: none;
                border-color: #e53e3e;
                border-width: 2px;
                border-style: solid;
            }

            .dataTables_length {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .dataTables_length label {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                margin-bottom: 0;
                font-size: 0.875rem;
                color: #4a5568;
            }

            .dataTables_info {
            padding-top: 0!important;
            }
        `;
        document.head.appendChild(styleElement);

        const table = $('#tablaUsuarios').DataTable({
            "paging": true,
            "searching": true,
            "lengthMenu": [10, 25, 50, 100],
            "pageLength": 10,
            "responsive": true,
            "scrollX": false,
            "autoWidth": false,
            "language": {
                url: "data_tables.json",
                lengthMenu: '_MENU_ registros por página'
            },
            "pagingType": "full_numbers",
            "dom": "<'flex flex-col md:flex-row justify-between items-center mb-4'" +
                "<'w-full md:w-auto flex items-center space-x-3'l>" +
                ">" +
                "'<'overflow-x-auto't>" +
                "'<'flex flex-col-reverse md:flex-row justify-between items-center mt-4'i<'ml-auto'p>>",
            "initComplete": function() {
                // Apply desktop-specific fixes
                if (window.innerWidth >= 1024) {
                    $('.dataTables_wrapper').css('overflow-x', 'hidden');
                    $('.dataTables_scrollBody').css('overflow-x', 'hidden');
                    $('.table-container').css('overflow-x', 'hidden');
                    $(this).css('width', '100%');
                }
            },
            "drawCallback": function(settings) {
                const api = this.api();
                const pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                const pageInfo = api.page.info();
                const pages = pageInfo.pages;
                const currentPage = pageInfo.page;

                // Enhance length menu
                const lengthMenu = $(this).closest('.dataTables_wrapper').find('.dataTables_length');
                lengthMenu.find('select').addClass('custom-length-select');

                // Custom pagination HTML
                let paginationHTML = `
                    <div class="flex items-center justify-center space-x-2">
                        <button class="pagination-prev p-2 rounded-md hover:bg-gray-100 ${currentPage === 0 ? 'text-gray-300 cursor-not-allowed' : 'text-red-600'}" 
                                ${currentPage === 0 ? 'disabled' : ''}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        
                        <div class="flex space-x-1">
                            ${generatePageButtons(currentPage, pages)}
                        </div>
                        
                        <button class="pagination-next p-2 rounded-md hover:bg-gray-100 ${currentPage === pages - 1 ? 'text-gray-300 cursor-not-allowed' : 'text-red-600'}" 
                                ${currentPage === pages - 1 ? 'disabled' : ''}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                `;

                pagination.html(paginationHTML);

                // Add event listeners for pagination
                pagination.find('.pagination-prev').on('click', function() {
                    if (currentPage > 0) {
                        table.page('previous').draw('page');
                    }
                });

                pagination.find('.pagination-next').on('click', function() {
                    if (currentPage < pages - 1) {
                        table.page('next').draw('page');
                    }
                });

                pagination.find('.page-button').on('click', function() {
                    const page = parseInt($(this).data('page'));
                    table.page(page).draw('page');
                });

                // Reapply desktop fixes after drawing
                if (window.innerWidth >= 1024) {
                    $('.dataTables_wrapper').css('overflow-x', 'hidden');
                    $('.dataTables_scrollBody').css('overflow-x', 'hidden');
                    $('.table-container').css('overflow-x', 'hidden');
                }
            }
        });

        // Apply fix on window resize as well
        $(window).resize(function() {
            if (window.innerWidth >= 1024) {
                $('.dataTables_wrapper').css('overflow-x', 'hidden');
                $('.dataTables_scrollBody').css('overflow-x', 'hidden');
                $('.table-container').css('overflow-x', 'hidden');
            }
        });

        // Custom search input
        $('#customSearch').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // Function to generate page buttons with smart truncation
        function generatePageButtons(currentPage, totalPages) {
            let buttons = '';

            // Always show first page
            buttons += createPageButton(0, currentPage);

            // Show ellipsis if more than 5 pages and current page is far from start
            if (totalPages > 5 && currentPage > 2) {
                buttons += '<span class="px-2 text-gray-500">...</span>';
            }

            // Show pages around current page
            const start = Math.max(1, Math.min(currentPage - 1, totalPages - 4));
            const end = Math.min(totalPages - 1, Math.max(currentPage + 2, 4));

            for (let i = start; i < end; i++) {
                buttons += createPageButton(i, currentPage);
            }

            // Show ellipsis if more than 5 pages and current page is far from end
            if (totalPages > 5 && currentPage < totalPages - 3) {
                buttons += '<span class="px-2 text-gray-500">...</span>';
            }

            // Always show last page if more than 1 page
            if (totalPages > 1) {
                buttons += createPageButton(totalPages - 1, currentPage);
            }

            return buttons;
        }

        // Helper function to create page buttons
        function createPageButton(page, currentPage) {
            const activeClass = page === currentPage ?
                'bg-red-600 text-white' :
                'text-gray-600 hover:bg-red-50';

            return `
                <button class="page-button px-3 py-1 rounded-md ${activeClass} text-sm font-medium"
                        data-page="${page}">
                    ${page + 1}
                </button>
            `;
        }

        // User Modal Setup
        const userModal = document.getElementById('userModal');
        const closeUserModal = document.getElementById('closeUserModal');
        const cancelUserModal = document.getElementById('cancelUserModal');
        const editUserModal = document.getElementById('editUserModal');

        // Store current user ID for edit button
        let currentUserId = null;

        // Add click event for table rows
        $('#tablaUsuarios tbody').on('click', 'tr', function() {
            const data = table.row(this).data();
            const rowElement = this;

            // Log the data to console for debugging
            console.log('User Data:', data);
            console.log('Row Element:', rowElement);

            // Get user ID from the data attribute or hidden column
            currentUserId = $(rowElement).data('user-id') || data[6]; // Index 6 is our hidden ID column

            // Try to get full user data from the data attribute
            let userData = null;
            try {
                userData = $(rowElement).data('user-info');
                console.log('Full User Data:', userData);
            } catch (e) {
                console.log('Could not parse user data:', e);
            }

            // Populate modal with user data
            populateUserModal(data, userData, rowElement);

            // Show modal
            userModal.classList.remove('hidden');
        });

        // Function to populate modal with user data
        function populateUserModal(data, userData, rowElement) {
            // If we have parsed userData, use it directly
            if (userData && typeof userData === 'object') {
                document.getElementById('userModalImage').src = userData.imagen || 'https://dosxdos.app.iidos.com/img/usuario.png';
                document.getElementById('userModalUsername').textContent = userData.usuario || '-';
                document.getElementById('userModalName').textContent = userData.nombre || '-';
                document.getElementById('userModalLastName').textContent = userData.apellido || '-';
                document.getElementById('userModalCode').textContent = userData.cod || '-';
                document.getElementById('userModalClass').textContent = userData.clase || '-';
                document.getElementById('userModalEmail').textContent = userData.correo || '-';
                document.getElementById('userModalPhone').textContent = userData.movil || '-';

                // Update modal title with user name
                document.getElementById('userModalTitle').textContent = `${userData.nombre} ${userData.apellido}`;
                return;
            }

            // Fallback to extracting from table data
            // Extract image URL
            let imgSrc = 'https://dosxdos.app.iidos.com/img/usuario.png';

            // Try to extract from HTML string if data[0] is a string
            if (typeof data[0] === 'string') {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data[0];
                const imgElement = tempDiv.querySelector('img');
                if (imgElement) {
                    imgSrc = imgElement.src;
                }
            }
            // Try to extract from row element
            else if (rowElement) {
                const imgElement = $(rowElement).find('td:first-child img');
                if (imgElement.length) {
                    imgSrc = imgElement.attr('src');
                }
            }

            document.getElementById('userModalImage').src = imgSrc;
            document.getElementById('userModalUsername').textContent = data[5] || '-';
            document.getElementById('userModalName').textContent = data[1] || '-';
            document.getElementById('userModalLastName').textContent = data[2] || '-';
            document.getElementById('userModalCode').textContent = '-'; // No cod in basic table data

            // For the class, we need to extract text from within span element if data[3] is HTML
            let userClass = '-';
            if (typeof data[3] === 'string' && data[3].includes('<span')) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data[3];
                const span = tempDiv.querySelector('span');
                if (span) {
                    userClass = span.textContent.trim();
                }
            } else {
                userClass = data[3] || '-';
            }

            document.getElementById('userModalClass').textContent = userClass;
            document.getElementById('userModalEmail').textContent = data[4] || '-';
            document.getElementById('userModalPhone').textContent = '-'; // Phone might not be available

            // Update modal title with user name
            document.getElementById('userModalTitle').textContent = `${data[1] || ''} ${data[2] || ''}`.trim() || 'Detalles del Usuario';
        }

        // Close modal events
        closeUserModal.addEventListener('click', () => {
            userModal.classList.add('hidden');
        });

        cancelUserModal.addEventListener('click', () => {
            userModal.classList.add('hidden');
        });

        // Edit user event
        editUserModal.addEventListener('click', () => {
            if (currentUserId) {
                window.location.href = `https://dosxdos.app.iidos.com/dosxdos.php?modulo=editarUsuario&id=${currentUserId}`;
            } else {
                console.error('No user ID available for edit action');
            }
        });

        // Close modal when clicking outside
        userModal.addEventListener('click', (e) => {
            if (e.target === userModal) {
                userModal.classList.add('hidden');
            }
        });

        // Close modal when pressing Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !userModal.classList.contains('hidden')) {
                userModal.classList.add('hidden');
            }
        });
    });
</script>