@extends('admin.layouts.master')

@section('title', 'Bản đồ')

@section('head.scripts')
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Checkin Location with Modal - (Bootstrap 4 Version)</title>

<!--
    Bạn KHÔNG cần import Bootstrap 5 tại đây.
    Layout master của bạn đã dùng Bootstrap 4, jQuery, v.v.
    => Chỉ import Leaflet là đủ.
-->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<style>
    /* Chiều cao tối thiểu cho #map */
    #map {
        height: 500px;
        width: 100%;
    }
</style>
@endsection

@section('content')
<!-- Nút Checkin Location (dùng data-toggle, data-target của Bootstrap 4) -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mapModal">
    Checkin Location
</button>

<!-- Modal (Bootstrap 4) -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn vị trí trên bản đồ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Vùng hiển thị bản đồ -->
                <div id="map"></div>

                <!-- Form ẩn lưu toạ độ -->
                <form id="coordinateForm" action="#" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="longitude" id="longitude" value="">
                    <input type="hidden" name="latitude" id="latitude" value="">
                </form>
            </div>

            <div class="modal-footer">
                <!-- Nút gửi dữ liệu lên server -->
                <button class="btn btn-success" id="btnSaveCoordinates">Lưu toạ độ lên Server</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    let map;
    let draggableMarker;
    let hasMapInitialized = false;

    // Toạ độ mặc định (TP.HCM)
    const defaultLat = 10.762622;
    const defaultLng = 106.660172;

    // Hàm khởi tạo map
    function initMap() {
        map = L.map('map').setView([defaultLat, defaultLng], 13);

        // Thêm tile OSM
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Tạo marker kéo thả
        draggableMarker = L.marker([defaultLat, defaultLng], {
                draggable: true
            })
            .addTo(map)
            .bindPopup("Kéo thả tôi để chọn vị trí khác")
            .openPopup();

        // Khi kéo xong
        draggableMarker.on('dragend', function(e) {
            const latlng = e.target.getLatLng();
            updateLocation(latlng.lat, latlng.lng);
        });

        // Click hoặc chuột phải -> Di chuyển marker
        map.on('click', onMapInteraction);
        map.on('contextmenu', onMapInteraction);

        // Định vị người dùng
        map.locate({
            setView: false,
            maxZoom: 16
        });
        map.on('locationfound', function(e) {
            draggableMarker.setLatLng(e.latlng);
            map.setView(e.latlng, 16);
            updateLocation(e.latlng.lat, e.latlng.lng);
        });
        map.on('locationerror', function() {
            console.warn("Không thể xác định vị trí của bạn.");
        });
    }

    function onMapInteraction(e) {
        draggableMarker.setLatLng(e.latlng);
        updateLocation(e.latlng.lat, e.latlng.lng);
    }

    // Cập nhật toạ độ
    function updateLocation(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
        console.log('Cập nhật:', {
            latitude: lat,
            longitude: lng
        });
    }

    // Nút Lưu -> submit form
    document.getElementById('btnSaveCoordinates').addEventListener('click', function() {
        document.getElementById('coordinateForm').submit();
    });

    // Sự kiện khi modal mở (Bootstrap 4 => 'shown.bs.modal')
    $('#mapModal').on('shown.bs.modal', function() {
        if (!hasMapInitialized) {
            initMap();
            hasMapInitialized = true;
        } else {
            // Nếu đã init => gọi invalidateSize() để map vẽ lại
            map.invalidateSize();
        }
    });
</script>

@endsection