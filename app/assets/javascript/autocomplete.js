var autocomplete;
autocomplete = new google.maps.places.Autocomplete((document.getElementById('association_adress_street')), {
    componentRestrictions: { country: "fr" },
    types: ['geocode'],
});

google.maps.event.addListener(autocomplete, 'place_changed', function () {
    var place = autocomplete.getPlace();
    const adress = place.address_components;
    if(adress.length === 7){
        document.getElementById("association_adress_street").value = adress[0].long_name + ' ' +  adress[1].long_name;
        document.getElementById("association_adress_city").value = adress[2].long_name;
        document.getElementById("association_adress_region").value = adress[4].long_name;
        document.getElementById("association_adress_country").value = adress[5].long_name;
        document.getElementById("association_adress_postalCode").value = adress[6].long_name;
        document.getElementById("association_adress_latitude").value = place.geometry.location.lat();
        document.getElementById("association_adress_longitude").value = place.geometry.location.lng();
    }
});