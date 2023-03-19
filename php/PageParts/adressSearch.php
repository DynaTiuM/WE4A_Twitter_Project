<script>
    address_global = null;
    // Fonction pour afficher la fenêtre flottante
    function showMap() {

        // Afficher la fenêtre flottante
        document.getElementById("map-container").style.display = "inline-block";
        // Récupérer l'élément de la carte
        const mapElement = document.getElementById("map");

        // Créer la carte avec les options
        const map = new google.maps.Map(mapElement, {
            center: { lat: 48.856614, lng: 2.3522219 },
            zoom: 13,
        });

        // Récupérer l'élément de recherche
        const searchElement = document.getElementById("search");
        const searchBox = new google.maps.places.SearchBox(searchElement);

        // Écouter les changements de la barre de recherche
        searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();

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

</script>