<?php
/**
 * Archivo: footer.php
 * Ubicación: /app/views/shared/footer.php
 * Descripción: Contiene el cierre del HTML y la inclusión de scripts JS (Bootstrap y personalizado).
 * Debe ser incluido al final de cada vista.
 */
?>
            </div> <!-- Cierre del div .container-fluid py-4 -->
        </div> <!-- Cierre del div #page-content-wrapper -->
    </div> <!-- Cierre del div #wrapper -->

    <!-- Incluir Bootstrap 5 JS (Popper y JS Bundle) desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Incluir nuestro archivo JavaScript personalizado -->
    <!-- La ruta debe ser relativa desde la ubicación del archivo PHP que incluye este footer.
         Si el archivo PHP está en public/, entonces 'js/main.js' es correcto. -->
    <script src="js/main.js"></script>

    <!-- Script para el toggle de la barra lateral (duplicado en sidebar.php, pero aquí es seguro) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var sidebarToggle = document.getElementById('sidebarToggle');
            var wrapper = document.getElementById('wrapper');

            if (sidebarToggle && wrapper) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    wrapper.classList.toggle('toggled');
                });
            }
        });
    </script>
</body>
</html>
