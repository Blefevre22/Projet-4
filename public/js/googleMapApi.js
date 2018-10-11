var map;
function initMap(){
    var louvre = {
        lat: 48.8607307,
        lng: 2.3335045000000036
    };
    var content = '<h1>Musée du Louvre, Paris</h1>';
    map = new google.maps.Map(document.getElementById('map'), {
        center: louvre,
        zoom : 14
    });
    var infos = new google.maps.InfoWindow({
        content : content
    });
    var marker = new google.maps.Marker({
        position: louvre,
        map: map
    });
    marker.addListener('mouseover', function(){
        infos.open(map, marker);
    });
}