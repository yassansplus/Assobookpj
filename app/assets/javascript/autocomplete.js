var autocomplete;

const addresStreet = document.getElementById('association_adress_street') ?? document.getElementById('address_street');
const city = document.getElementById('association_adress_city') ?? document.getElementById('address_city');
const region = document.getElementById('association_adress_region') ?? document.getElementById('address_region');
const country = document.getElementById('association_adress_country') ?? document.getElementById('address_country');
const postalCode = document.getElementById('association_adress_postalCode') ?? document.getElementById('address_postalCode');
const latitude = document.getElementById('association_adress_latitude') ?? document.getElementById('address_latitude');
const longitude = document.getElementById('association_adress_longitude') ?? document.getElementById('address_longitude');

autocomplete = new google.maps.places.Autocomplete((addresStreet), {
    componentRestrictions: { country: "fr" },
    types: ['geocode'],
});

google.maps.event.addListener(autocomplete, 'place_changed', function () {
    var place = autocomplete.getPlace();
    const adress = place.address_components;
    if(adress.length === 7){
        addresStreet.value = adress[0].long_name + ' ' +  adress[1].long_name;
        city.value = adress[2].long_name;
        region.value = adress[4].long_name;
        country.value = adress[5].long_name;
        postalCode.value = adress[6].long_name;
        latitude.value = place.geometry.location.lat();
        longitude.value = place.geometry.location.lng();
    }
});