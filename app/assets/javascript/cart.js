function success(pos) {


    const crd = pos.coords;

    //console.log(`Latitude : ${crd.latitude}`);
    //console.log(`Longitude : ${crd.longitude}`);

    let map = L.map('mapid').setView([crd.latitude, crd.longitude], 14);

    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 28,
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1,
        accessToken: 'pk.eyJ1IjoibWJsaCIsImEiOiJja3FwZWhwOTUwZjF1MnNuYmFxMWpubjhyIn0.G2B_Cd-CMZEZoQ7wWYvckg'
    }).addTo(map);

    let marker_me = L.marker([crd.latitude, crd.longitude]).addTo(map);
    marker_me.bindPopup("<b style='color: #1b9448'>Vous êtes ici !</b>").openPopup();

    Array.from(document.querySelectorAll('.js-marker')).forEach((association) => {
        let marker = L.marker([association.dataset.lat, association.dataset.lng]).addTo(map);
        marker.bindPopup(association.dataset.name).openPopup();
        association.addEventListener('mouseover', function (){
            marker.getElement().classList.add('is-active')
        })
        //map.addMarker(association.dataset.lat, association.dataset.lng, association.dataset.name)
    })

}

function error(err) {
    console.warn(`ERREUR (${err.code}): ${err.message}`);
}

navigator.geolocation.getCurrentPosition(success, error);

/*
let $map = document.querySelector('#mapid')

class LeafletMap {

    constructor() {
        this.map = null
    }

    async load (element){
        return new Promise((resolve, reject) => {
            $script('https://unpkg.com/leaflet@1.7.1/dist/leaflet.js', () => {
                this.map = L.map(element);
                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                    id: 'mapbox/streets-v11',
                    accessToken: 'pk.eyJ1IjoibWJsaCIsImEiOiJja3FwZWhwOTUwZjF1MnNuYmFxMWpubjhyIn0.G2B_Cd-CMZEZoQ7wWYvckg'
                }).addTo(this.map)
                resolve()
            })
        })
    }


    addMarker (lat, lng, text) {
        L.popup({
            autoClose: false,
            closeOnEscapeKey: false,
            closeOnClick: false,
            closeButton: false,
            className: 'marker',
            maxWidth: 400,
        })
            .setLatLng([lat, lng])
            .setContent(text)
            .openOn(this.map)
    }
}

const initMap = async function () {
    let map = new LeafletMap()
    await map.load($map)
    Array.from(document.querySelectorAll('.js-marker')).forEach((association) => {
        map.addMarker(association.dataset.lat, association.dataset.lng, association.dataset.name)
    })
}

if ($map !== null){
    initMap()
}

 */