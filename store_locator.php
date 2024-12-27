
<?php
include '_base.php';

$_title = 'Find Our Store';
include '_head.php';
?>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDVw2OgzPYLZOIUwhbInlOSrXlQd1iR288&libraries=places&callback=initMap" async defer></script>

<style>
    #map {
        margin-top: 20px;
        margin-left: 20px;
        height: 450px;
        width: 60%;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    #store-list {
        position: absolute;
        top: 210px;
        right: 3%; /* Fix to the right */
        height: 400px;
        width: 30%;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    #store-list div {
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        font-family: Arial, sans-serif;
        margin: 20px 0;
        font-size: 24px;
    }
</style>
</head>
<body>
    <div style="margin-top: 150px;">
    <h1>Find Our Stores</h1>
    </div>
    <div id="map"></div>

    <div id="store-list"></div>

    <script>
        let map;
        let service;
        let infowindow;

        const stores = [
            {
                name: "Store 1",
                address: "Bangunan Tan Sri Khaw Kai Boh (Block A), Tunku Abdul Rahman University College, 53100 Kuala Lumpur, Federal Territory of Kuala Lumpur",
                lat: 3.2152056,
                lng: 101.7264908
            },
            {
                name: "Store 2",
                address: "38, Jalan Danau Niaga 1, Crystal Ville, 53300 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur",
                lat: 3.2027479,
                lng: 101.7176307
            }
            ,
            {
                name: "Store 3",
                address: "Unit 17, The Palette Commercial Hub, Jalan 3/23b, Taman Danau Kota, 53300 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur,",
                lat: 3.2058756,
                lng: 101.7169342
            }

        ];

        function initMap() {
            const center = { lat: 3.2152056, lng: 101.7264908 }; // Center of the map
            map = new google.maps.Map(document.getElementById("map"), {
                center: center,
                zoom: 14,
            });

            infowindow = new google.maps.InfoWindow();
            service = new google.maps.places.PlacesService(map);

            // Create a marker for each store
            stores.forEach(store => {
                const storeLocation = { lat: store.lat, lng: store.lng };
                const marker = new google.maps.Marker({
                    position: storeLocation,
                    map: map,
                    title: store.name
                });

                google.maps.event.addListener(marker, 'click', function () {
                    infowindow.setContent('<strong>' + store.name + '</strong><br>' + store.address);
                    infowindow.open(map, marker);
                });

                // Display stores in the list
                const storeList = document.getElementById("store-list");
                const storeItem = document.createElement("div");
                storeItem.innerHTML = `<strong>${store.name}</strong><br>${store.address}`;
                storeList.appendChild(storeItem);
            });
        }
    </script>
</body>
</html>
<?php
include '_foot.php';