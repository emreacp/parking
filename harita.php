<?php
session_start();
// .env dosyasından konfigürasyonu yükle
require_once 'config.php';

if (!isset($_SESSION['kullanici_email'])) {
    header("Location: giris_kayit.html");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Harita - ACP Parking</title>
  <link rel="stylesheet" href="style.css">
  <style>
    #map { width: 100%; height: 100vh; }
    #goToMe {
      position: absolute;
      bottom: 100px;
      right: 20px;
      background: #007bff;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 50%;
      font-size: 16px;
      cursor: pointer;
      z-index: 998;
    }
    #toggleNearest {
      position: absolute;
      top: 100px;
      right: 20px;
      background: #28a745;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 6px;
      cursor: pointer;
      z-index: 999;
    }
    #resultBox {
      display: none;
      position: absolute;
      top: 160px;
      right: 20px;
      width: 300px;
      max-height: 60vh;
      overflow-y: auto;
      background: white;
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 10px;
      z-index: 998;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .nearest-item {
      background: #f7f7f7;
      padding: 8px;
      margin-bottom: 8px;
      border-radius: 4px;
    }
    .nearest-item button {
      margin-top: 6px;
      background: #007bff;
      color: white;
      border: none;
      padding: 6px;
      border-radius: 4px;
      cursor: pointer;
    }
    .route-panel {
      position: absolute;
      bottom: 20px;
      left: 20px;
      background: white;
      padding: 16px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      display: none;
      z-index: 999;
    }
    .route-panel button {
      margin: 8px 4px;
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    #closeRoute { background: #ccc; }
    #googleRoute { background: #4285F4; color: white; }
    #yandexRoute { background: #ffcc00; }

    #searchBox {
      position: absolute;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1000;
    }
    #searchInput {
      width: 300px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px 6px 0 0;
    }
    #searchResults {
      width: 300px;
      background: white;
      border: 1px solid #ccc;
      border-top: none;
      max-height: 200px;
      overflow-y: auto;
      position: absolute;
      display: none;
    }
    #searchResults div {
      padding: 8px;
      cursor: pointer;
    }
    #searchResults div:hover {
      background: #eee;
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div id="searchBox">
  <input type="text" id="searchInput" placeholder="Otopark Ara...">
  <div id="searchResults"></div>
</div>

<div id="map"></div>
<button id="goToMe" title="Konumuma Git">📍</button>
<button id="toggleNearest">🧭 En Yakın Otoparklar</button>
<div id="resultBox"></div>

<div class="route-panel" id="routePanel">
  <p>Yol tarifi için platform seçin:</p>
  <button id="googleRoute">Google Maps</button>
  <button id="yandexRoute">Yandex</button>
  <button id="closeRoute">❌</button>
</div>

<script>
let directionsPolyline;
let directionsService, directionsRenderer;
let map, infoWindow;
let userMarker;
let userPos = null;
let markers = [];
let otoparkData = [];

window.initMap = function () {
  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 13,
    center: { lat: 39.9208, lng: 32.8541 },
    gestureHandling: "greedy"
  });

  const trafficLayer = new google.maps.TrafficLayer();
  trafficLayer.setMap(map);


  infoWindow = new google.maps.InfoWindow();

  document.getElementById("goToMe").onclick = () => {
    if (userMarker) {
      map.setCenter(userMarker.getPosition());
      map.setZoom(15);
    }
  };

  document.getElementById("toggleNearest").onclick = toggleNearestBox;

  Promise.all([
      Promise.all([
      fetch("otoparklar_db.php").then(res => res.json()),
      fetch("otoparklar_db.php").then(res => res.json())
    ]).then(([jsonData, dbData]) => {
      const allMap = new Map();
      jsonData.forEach(item => {
        const key = item.latitude + "," + item.longitude;
        allMap.set(key, item);
      });
      dbData.forEach(item => {
        const key = item.latitude + "," + item.longitude;
        allMap.set(key, item); 
      });
      const data = Array.from(allMap.values());
      otoparkData = data;
      loadOtoparks(data);
      initSearch(data);

  // URL'den lat, lon, ad çek
  const urlParams = new URLSearchParams(window.location.search);
  const lat = parseFloat(urlParams.get('lat'));
  const lon = parseFloat(urlParams.get('lon'));
  const ad = urlParams.get('ad');

  if (lat && lon && ad) {
    const target = otoparkData.find(o => o.ad === ad || o.ad === decodeURIComponent(ad));
    if (target) {
      const pos = { lat: lat, lng: lon };
      showInfo(target, pos);
      map.setCenter(pos);
      map.setZoom(17);
    }
  }

      loadOtoparks(data);
      initSearch(data);

 
  
    }).then(res => res.json()),
      fetch("otoparklar_db.php").then(res => res.json())
    ]).then(([jsonData, dbData]) => {
      const data = [...jsonData, ...dbData];
      otoparkData = data;
      loadOtoparks(data);
      initSearch(data);


  


 
    })
    .then(res => res.json())
    .then(data => {
      otoparkData = data;
      loadOtoparks(data);
      initSearch(data);


  if (lat && lon && ad) {
    const target = otoparkData.find(o => o.ad === ad || o.ad === decodeURIComponent(ad));
    if (target) {
      const pos = { lat: lat, lng: lon };
      showInfo(target, pos);
      map.setCenter(pos);
      map.setZoom(17);
    }
  }

      loadOtoparks(data);
      initSearch(data);




    });

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        userPos = { lat: pos.coords.latitude, lng: pos.coords.longitude };
        userMarker = new google.maps.Marker({
          position: userPos,
          map,
          title: "Benim Konumum",
          icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 6,
            fillColor: "#007bff",
            fillOpacity: 1,
            strokeWeight: 2,
            strokeColor: "white"
          }
        });
        map.setCenter(userPos);
      },
      () => console.log("Konum alınamadı.")
    );
  }

  document.getElementById("googleRoute").onclick = () => {
    if (infoWindow.getPosition()) {
      const pos = infoWindow.getPosition();
      window.open(`https://www.google.com/maps/dir/?api=1&destination=${pos.lat()},${pos.lng()}`, "_blank");
    }
  };
  document.getElementById("yandexRoute").onclick = () => {
    if (infoWindow.getPosition()) {
      const pos = infoWindow.getPosition();
      window.open(`https://yandex.com.tr/harita/?rtext=~${pos.lat()},${pos.lng()}&rtt=auto`, "_blank");
    }
  };
  document.getElementById("closeRoute").onclick = () => {
    document.getElementById("routePanel").style.display = "none";
  };
}

function loadOtoparks(data) {
  data.forEach(d => {
    const pos = { lat: parseFloat(d.latitude), lng: parseFloat(d.longitude) };
    const renk = getColorByRate(d);
    
const marker = new google.maps.Marker({
  position: pos,
  map,
  title: d.ad,
  icon: `https://maps.google.com/mapfiles/ms/icons/${renk}-dot.png`
});

    marker.addListener("click", () => {
      showInfo(d, pos);
    });
    markers.push({ marker, data: d });
  });
}

function showInfo(d, pos) {
  const html = `
    <strong>${d.ad}</strong><br>
    İl: ${d.il}<br>
    Ücret: ${d.ucret || "-"} ₺<br>
    Dolu: ${d.dolu_yer_sayisi || 0}<br>
    Boş: ${d.bos_yer_sayisi || 0}<br>
    Kapanış: ${d.kapanis_saati || "-"}<br><br>
    <button onclick="guzergahCiz(${pos.lat}, ${pos.lng})">🚗 Yol Tarifi Al</button>
  `;
  infoWindow.setContent(html);
  infoWindow.setPosition(pos);
  infoWindow.open(map);
}


function showRoutePanel() {
  document.getElementById("routePanel").style.display = "block";
}

function toggleNearestBox() {
  const box = document.getElementById("resultBox");
  if (box.style.display === "block") {
    box.style.display = "none";
    return;
  }
  const enriched = otoparkData.map(o => {
    const d = userPos ? getDistance(userPos.lat, userPos.lng, o.latitude, o.longitude) : 99999;
    return { ...o, mesafe: d };
  });
  const sorted = enriched.sort((a, b) => a.mesafe - b.mesafe).slice(0, 5);
 box.innerHTML = sorted.map(o => `
  <div class="nearest-item" style="background: ${getColorByRate(o)};">
    <strong>${o.ad}</strong><br>
    İl: ${o.il}<br>
    Ücret: ${o.ucret || "-"} ₺<br>
    Dolu: ${o.dolu_yer_sayisi || 0}<br>
    Boş: ${o.bos_yer_sayisi || 0}<br>
    <button onclick="gotoOtopark(${o.latitude}, ${o.longitude}, '${o.ad.replace(/'/g, "\\'")}')">Konuma Git</button>
  </div>
`).join("");

  box.style.display = "block";
}

function gotoOtopark(lat, lng, ad) {
  const pos = { lat, lng };
  const d = otoparkData.find(d => d.ad === ad);
  showInfo(d, pos);
  map.setCenter(pos);
  map.setZoom(17);
}

function initSearch(data) {
  const input = document.getElementById("searchInput");
  const box = document.getElementById("searchResults");
  input.addEventListener("input", () => {
    const val = input.value.toLowerCase();
    if (!val) return box.style.display = "none";
    const matched = data.filter(d => d.ad.toLowerCase().includes(val)).slice(0, 5);
    box.innerHTML = "";
    matched.forEach(m => {
      const div = document.createElement("div");
      div.textContent = m.ad;
      div.onclick = () => {
        const pos = { lat: parseFloat(m.latitude), lng: parseFloat(m.longitude) };
        showInfo(m, pos);
        map.setCenter(pos);
        map.setZoom(17);
        input.value = m.ad;
        box.style.display = "none";
      };
      box.appendChild(div);
    });
    box.style.display = "block";
  });
}

function getDistance(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const dLat = deg2rad(lat2 - lat1);
  const dLon = deg2rad(lon2 - lon1);
  const a = Math.sin(dLat / 2) ** 2 +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) ** 2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}
function deg2rad(d) {
  return d * (Math.PI / 180);
}
function getColorByRate(otopark) {
  const dolu = parseInt(otopark.dolu_yer_sayisi || 0);
  const bos = parseInt(otopark.bos_yer_sayisi || 0);
  const toplam = dolu + bos;

  if (toplam === 0) return "grey";
  const oran = dolu / toplam;

  if (oran < 0.3) return "green";
  if (oran < 0.7) return "yellow";
  return "red";
}

function guzergahCizApi(hedefLat, hedefLng) {
  if (!userMarker) {
    alert("Konumunuz alınamadı.");
    return;
  }

  const baslangic = userMarker.getPosition();
  const baslangicStr = `${baslangic.lat()},${baslangic.lng()}`;
  const bitisStr = `${hedefLat},${hedefLng}`;

  fetch(`/api/yol_tarifi.php?baslangic=${baslangicStr}&bitis=${bitisStr}`)
    .then(res => res.json())
    .then(data => {
      if (directionsPolyline) {
        directionsPolyline.setMap(null);
      }
      const routePath = data.route.map(p => new google.maps.LatLng(p.lat, p.lng));
      directionsPolyline = new google.maps.Polyline({
        path: routePath,
        geodesic: true,
        strokeColor: "#FF0000",
        strokeOpacity: 1.0,
        strokeWeight: 5
      });
      directionsPolyline.setMap(map);
    })
    .catch(err => alert("Yol verisi alınamadı."));
}

function openGoogleMaps(lat, lng) {
  if (!userMarker) return alert("Konum alınamadı.");
  const pos = userMarker.getPosition();
  window.open(`https://www.google.com/maps/dir/?api=1&origin=${pos.lat()},${pos.lng()}&destination=${lat},${lng}`, "_blank");
}

function openYandexMaps(lat, lng) {
  if (!userMarker) return alert("Konum alınamadı.");
  const pos = userMarker.getPosition();
  window.open(`https://yandex.com.tr/route?mode=auto&rtext=${pos.lat()},${pos.lng()}~${lat},${lng}`, "_blank");
}


let activeRoute;

function guzergahCiz(hedefLat, hedefLng) {
  if (!userMarker) return alert("Konum alınamadı.");

  if (activeRoute) activeRoute.setMap(null);

  const directionsService = new google.maps.DirectionsService();
  const directionsRenderer = new google.maps.DirectionsRenderer({
    suppressMarkers: true,
    preserveViewport: true,
    polylineOptions: {
      strokeColor: "#FF0000",
      strokeWeight: 5
    }
  });

  directionsRenderer.setMap(map);
  activeRoute = directionsRenderer;

  directionsService.route({
    origin: userMarker.getPosition(),
    destination: new google.maps.LatLng(hedefLat, hedefLng),
    travelMode: google.maps.TravelMode.DRIVING
  }, function(response, status) {
    if (status === google.maps.DirectionsStatus.OK) {
      directionsRenderer.setDirections(response);
      document.getElementById("routePanel").style.display = "block";
    } else {
      alert("Rota çizilemedi: " + status);
    }
  });
}



</script> 
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&callback=initMap" async defer>
function guzergahCizApi(hedefLat, hedefLng) {
  if (!userMarker) {
    alert("Konumunuz alınamadı.");
    return;
  }

  const baslangic = userMarker.getPosition();
  const baslangicStr = `${baslangic.lat()},${baslangic.lng()}`;
  const bitisStr = `${hedefLat},${hedefLng}`;

  fetch(`/api/yol_tarifi.php?baslangic=${baslangicStr}&bitis=${bitisStr}`)
    .then(res => res.json())
    .then(data => {
      if (directionsPolyline) {
        directionsPolyline.setMap(null);
      }
      const routePath = data.route.map(p => new google.maps.LatLng(p.lat, p.lng));
      directionsPolyline = new google.maps.Polyline({
        path: routePath,
        geodesic: true,
        strokeColor: "#FF0000",
        strokeOpacity: 1.0,
        strokeWeight: 5
      });
      directionsPolyline.setMap(map);
    })
    .catch(err => alert("Yol verisi alınamadı."));
}



</script>
</body>
</html>
