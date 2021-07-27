function distance(user, assoc, unite) {
    var radlat1 = Math.PI * user.latitude / 180
    var radlat2 = Math.PI * assoc.dataset.lat / 180
    var theta = user.longitude - assoc.dataset.lng
    var radtheta = Math.PI * theta / 180
    var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
    if (dist > 1) {
        dist = 1;
    }
    dist = Math.acos(dist)
    dist = dist * 180 / Math.PI
    dist = dist * 60 * 1.1515
    if (unite == "K") {
        dist = dist * 1.609344
    }
    if (unite == "N") {
        dist = dist * 0.8684
    }
    return {
        distance: dist,
        name: assoc.dataset.name,
        longitude: assoc.dataset.lng,
        latitude: assoc.dataset.lat,
        address: assoc.dataset.assoc,
        id: assoc.dataset.id
    };
}

function compare(x, y) {
    return x.distance - y.distance;
}

function success(pos) {

    const crd = pos.coords;
    let calc = [];

    let map = L.map('map', {maxZoom: 20});
    let marker = L.marker([crd.latitude, crd.longitude]).addTo(map);
    marker.bindPopup("Vous êtes ici !").openPopup();

    let options_popup = {
        autoClose: false,
        closeOnEscapeKey: false,
        closeOnClick: false,
        closeButton: false,
        className: 'marker',
        maxWidth: 400
    }

    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1,
        accessToken: 'pk.eyJ1IjoibWJsaCIsImEiOiJja3FwZWhwOTUwZjF1MnNuYmFxMWpubjhyIn0.G2B_Cd-CMZEZoQ7wWYvckg'
    }).addTo(map);


    Array.from(document.querySelectorAll('.js-marker')).forEach((association) => {
        let assoc_popup = L.popup(options_popup)
            .setLatLng([association.dataset.lat, association.dataset.lng])
            .setContent(association.dataset.name)
        assoc_popup.openOn(map);
        map.setView([crd.latitude, crd.longitude], 14)
        calc.push(distance(crd, association, "K",));
    });

    const list = document.querySelector('.list');
    calc.sort(compare);
    list.innerHTML = '';
    calc.forEach((assoc) => {
        if (assoc.distance < 30) {
            const div = `
            <div class="association js-marker" data-lat="${assoc.latitude}" data-lng="${assoc.longitude}" data-name="${assoc.name}">
                <img src="http://espacedeviesociale30.org/wp-content/uploads/2020/11/logo.png" alt="">
                <h4>
                    <a href="association/${assoc.id}" class="link-assoc">${assoc.name}
                    </a>
                </h4>
                <p>${assoc.address}</p>
            </div>
        `;
            list.innerHTML += div;
        }
    });
}

function error() {
    iziToast.warning({
        message: 'Vous devez activer la géolocalisation de votre navigateur pour accèder à ce service !'
    })

}

navigator.geolocation.getCurrentPosition(success, error);