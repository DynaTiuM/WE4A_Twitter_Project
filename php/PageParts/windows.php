<script>
    // fonction pour ouvrir la fenêtre
    function openWindow(window) {
        document.getElementById(window).style.display = "block";
    }
    // fonction pour fermer la fenêtre
    function closeWindow(window) {
        document.getElementById(window).style.display = "none";
    }

    function closeWindowToNewDestination(window) {
        document.getElementById(window).style.display = "none";
        location.href = 'explorer.php';
    }
</script>