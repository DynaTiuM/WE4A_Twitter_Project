// Fonction pour afficher la fenêtre flottante
function showMap() {
    // Lorsqu'on clique sur la catégorie d'affichage de la localisation, on affiche donc la localisation :
    document.getElementById("map-container").style.display = "inline-block";
    // Et on masque les autres :
    document.getElementById("display-type").style.display = "none";
    document.getElementById("display-pet").style.display = "none";

    // Il faut ainsi récupérer l'élément de recherche
    var searchElement = document.getElementById("search");
    // Et utiliser directement les services proposés par google, par rapport à l'élément ajouté dans la barre de recherche :
    var searchBox = new google.maps.places.SearchBox(searchElement);

    // Ainsi, il est nécessaire d'écouter les changements de la barre de recherche :
    searchBox.addListener("places_changed", () => {
        // On stocke ceci directement dans une variable
        var places = searchBox.getPlaces();

        if (places.length === 0) {
            return;
        }

        // Et si l'adresse est sélectionnée, on la récupère
        const selectedPlace = places[0];
        // Ensuite, on utilise encore une fois une implémentation de l'API google maps permettant de formater l'adresse :
        const address = selectedPlace.formatted_address;

        // Il ne reste finalement plus qu'à assigner l'adresse à la valeur de l'élément HTML correspondant
        // On récupère l'élément HTML :
        const addressInput = document.getElementById("localisation-input");
        // Et on lui assigne l'adresse récupérée :
        addressInput.value = address;
    });

}

/**
 * Fonction permettant de fermer la page de localisation
 */
function closeMap() {
    document.getElementById("map-container").style.display = "none";
}