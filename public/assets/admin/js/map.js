let maps = {};

// Hàm khởi tạo map cho từng modal
function initMap(modalId) {
    console.log(`Initializing map for modalId: ${modalId}`);
    const mapId = `map-${modalId}`;
    maps[modalId] = L.map(mapId).setView([10.762622, 106.660172], 13);

    // Thêm tile OSM
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(maps[modalId]);

    // Tạo marker kéo thả
    maps[modalId].draggableMarker = L.marker([10.762622, 106.660172], {
        draggable: true
    })
        .addTo(maps[modalId])
        .bindPopup("Kéo thả tôi để chọn vị trí khác")
        .openPopup();

    // Khi kéo xong
    maps[modalId].draggableMarker.on('dragend', function (e) {
        const { lat, lng } = e.target.getLatLng();
        updateLocation(modalId, lat, lng);
    });

    // Click để di chuyển marker
    maps[modalId].on('click', function (e) {
        maps[modalId].draggableMarker.setLatLng(e.latlng);
        updateLocation(modalId, e.latlng.lat, e.latlng.lng);
    });

    // Định vị người dùng
    maps[modalId].locate({
        setView: false,
        maxZoom: 16
    });
    maps[modalId].on('locationfound', function (e) {
        maps[modalId].draggableMarker.setLatLng(e.latlng);
        updateLocation(modalId, e.latlng.lat, e.latlng.lng);
    });
    maps[modalId].on('locationerror', function () {
        console.log('Không thể định vị vị trí của bạn.');
    });
}

// Hàm cập nhật tọa độ
function updateLocation(modalId, lat, lng) {
    document.getElementById(`latitude-${modalId}`).value = lat.toFixed(6);
    document.getElementById(`longitude-${modalId}`).value = lng.toFixed(6);
    console.log('Cập nhật:', {
        latitude: lat,
        longitude: lng
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const messageElement = document.getElementById('swalMessageMessager');
    if (messageElement) {
        const message = messageElement.value;
        if (message) {
            // Hiển thị thông báo bằng Toastr hoặc phương thức bạn sử dụng
            toastr.success(message);
        }
    }
});

// Event listener cho nút Save
document.addEventListener('click', function (event) {
    if (event.target && event.target.classList.contains('btnSaveCoordinates')) {
        const modalId = event.target.getAttribute('data-modal-id');
        document.getElementById(`coordinateForm-${modalId}`).submit();
    }
});

// Khởi tạo map khi modal được hiển thị
$(document).on('click', '.btnShowMapModal', function (e) {
    e.preventDefault();
    const modalId = $(this).data('modal-id');
    console.log(`Button clicked for modalId: ${modalId}`);
    $('#mapModal-' + modalId).modal('show'); // Hiển thị modal

    // Khởi tạo map sau khi modal đã hiển thị
    $('#mapModal-' + modalId).on('shown.bs.modal', function () {
        console.log(`Modal shown for modalId: ${modalId}`);
        if (!maps[modalId]) {
            initMap(modalId); // Gọi hàm khởi tạo map
        }
        setTimeout(function () {
            if (maps[modalId]) {
                maps[modalId].invalidateSize();
                console.log(`Map invalidated for modalId: ${modalId}`);
            }
        }, 200);
    });
});

$(document).on('click', '.aside-btnShowMapModal', function (e) {
    e.preventDefault();
    const modalId = $(this).data('modal-id');
    console.log(`Aside button clicked for modalId: ${modalId}`);
    $('#mapModal-' + modalId).modal('show'); // Hiển thị modal

    // Khởi tạo map sau khi modal đã hiển thị
    $('#mapModal-' + modalId).on('shown.bs.modal', function () {
        console.log(`Modal shown for modalId: ${modalId}`);
        if (!maps[modalId]) {
            initMap(modalId); // Gọi hàm khởi tạo map
        }
        setTimeout(function () {
            if (maps[modalId]) {
                maps[modalId].invalidateSize();
                console.log(`Map invalidated for modalId: ${modalId}`);
            }
        }, 200);
    });
});



let mapInstance = null;
let draggableMarker = null;

// Hàm khởi tạo bản đồ trong add-location-post
function initInlineMap() {
    if (mapInstance) {
        return; // Tránh khởi tạo lại nếu mapInstance đã được khởi tạo
    }

    // Tạo bản đồ với tọa độ mặc định
    mapInstance = L.map('us3').setView([10.762622, 106.660172], 13);

    // Thêm tile từ OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(mapInstance);

    // Tạo marker kéo thả
    draggableMarker = L.marker([10.762622, 106.660172], {
        draggable: true
    }).addTo(mapInstance);

    // Cập nhật tọa độ ban đầu vào input
    updateInlineLocation(10.762622, 106.660172);

    // Khi người dùng kéo thả marker
    draggableMarker.on('dragend', function (e) {
        const { lat, lng } = e.target.getLatLng();
        updateInlineLocation(lat, lng);
    });

    // Khi người dùng click trên bản đồ
    mapInstance.on('click', function (e) {
        const { lat, lng } = e.latlng;
        draggableMarker.setLatLng([lat, lng]);
        updateInlineLocation(lat, lng);
    });
}

// Hàm cập nhật tọa độ vào input và hiển thị giao diện
function updateInlineLocation(lat, lng) {
    const latInput = document.getElementById('us3-lat');
    const lngInput = document.getElementById('us3-lon');

    if (latInput && lngInput) {
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
    }

    // Hiển thị giá trị ra console (hoặc bất kỳ UI nào bạn muốn)
    console.log('Tọa độ được cập nhật:', { lat, lng });
}

// Hiển thị bản đồ khi người dùng nhấn vào .add-loc
$(document).on('click', '.add-loc', function (e) {
    e.preventDefault();

    const locationPostContainer = $('.add-location-post');
    if (locationPostContainer.is(':hidden')) {
        locationPostContainer.slideDown(); // Hiển thị add-location-post
    }

    // Khởi tạo map trong #us3
    initInlineMap();

    // Điều chỉnh kích thước bản đồ sau khi hiển thị
    setTimeout(() => {
        if (mapInstance) {
            mapInstance.invalidateSize();
        }
    }, 200);
});


let inlineMapInstance = null;
let inlineDraggableMarker = null;

// Hàm khởi tạo bản đồ cho us3-inline
function initUs3InlineMap() {
    if (inlineMapInstance) {
        return; // Tránh khởi tạo lại nếu inlineMapInstance đã được khởi tạo
    }

    // Tạo bản đồ với tọa độ mặc định
    inlineMapInstance = L.map('us3-inline').setView([10.762622, 106.660172], 13);

    // Thêm tile từ OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(inlineMapInstance);

    // Tạo marker kéo thả
    inlineDraggableMarker = L.marker([10.762622, 106.660172], {
        draggable: true
    }).addTo(inlineMapInstance)
        .bindPopup("Kéo thả tôi để chọn vị trí khác")
        .openPopup();

    // Cập nhật tọa độ ban đầu vào input
    updateus3InlineLocation(10.762622, 106.660172);

    // Khi người dùng kéo thả marker
    inlineDraggableMarker.on('dragend', function (e) {
        const { lat, lng } = e.target.getLatLng();
        updateus3InlineLocation(lat, lng);
    });

    // Khi người dùng click trên bản đồ
    inlineMapInstance.on('click', function (e) {
        const { lat, lng } = e.latlng;
        inlineDraggableMarker.setLatLng([lat, lng]);
        updateus3InlineLocation(lat, lng);
    });
}

// Hàm cập nhật tọa độ vào input và hiển thị giao diện
function updateus3InlineLocation(lat, lng) {
    const latInput = document.getElementById('us3-inline-lat');
    const lngInput = document.getElementById('us3-inline-lon');

    if (latInput && lngInput) {
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
    }

    // Hiển thị giá trị ra console hoặc UI
    console.log('Tọa độ được cập nhật:', { lat, lng });
}

// Hiển thị bản đồ khi người dùng nhấn vào .add-loc
$(document).on('click', '.add-loc', function (e) {
    e.preventDefault();

    const locationPostContainer = $('.add-location-post');
    if (locationPostContainer.is(':hidden')) {
        locationPostContainer.slideDown(); // Hiển thị add-location-post
    }

    // Khởi tạo map trong #us3-inline
    initUs3InlineMap();

    // Điều chỉnh kích thước bản đồ sau khi hiển thị
    setTimeout(() => {
        if (inlineMapInstance) {
            inlineMapInstance.invalidateSize();
        }
    }, 200);
});
