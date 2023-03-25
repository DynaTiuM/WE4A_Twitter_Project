<script>
    // Fonction pour afficher la fenêtre flottante
    function showMap() {
        // Afficher la fenêtre flottante
        document.getElementById("map-container").style.display = "inline-block";
        document.getElementById("display-type").style.display = "none";
        document.getElementById("display-pet").style.display = "none";

        // Récupérer l'élément de la carte
        var mapElement = document.getElementById("map");

        // Créer la carte avec les options
        var map = new google.maps.Map(mapElement, {
            center: { lat: 48.856614, lng: 2.3522219 },
            zoom: 13,
        });

        // Récupérer l'élément de recherche
        var searchElement = document.getElementById("search");
        var searchBox = new google.maps.places.SearchBox(searchElement);

        // Écouter les changements de la barre de recherche
        searchBox.addListener("places_changed", () => {
            var places = searchBox.getPlaces();

            if (places.length === 0) {
                return;
            }

            // Récupérer l'adresse sélectionnée
            const selectedPlace = places[0];
            const address = selectedPlace.formatted_address;

            // Assigner l'adresse à la valeur de l'attribut "name" de l'élément HTML correspondant
            const addressInput = document.getElementById("localisation-input");
            addressInput.value = address;
        });

        // Ajouter l'élément de recherche à la carte
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(searchElement);
    }

    function closeMap() {
        document.getElementById("map-container").style.display = "none";
    }

</script>