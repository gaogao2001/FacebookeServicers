{{-- filepath: /var/www/FacebookService/app/Modules/Facebook/Views/Facebook/partials/map_modal.blade.php --}}
<div class="modal fade" id="mapModal-{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel-{{ $modalId }}" aria-hidden="true">
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
                <div id="map-{{ $modalId }}" style="height: 500px;"></div>

                <!-- Form ẩn lưu tọa độ -->
                <form id="coordinateForm-{{ $modalId }}" action="{{ $formAction }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="longitude" id="longitude-{{ $modalId }}" value="">
                    <input type="hidden" name="latitude" id="latitude-{{ $modalId }}" value="">
                </form>
            </div>

            <div class="modal-footer">
                <!-- Nút gửi dữ liệu lên server -->
                <button class="btn btn-success btnSaveCoordinates" data-modal-id="{{ $modalId }}">Lưu tọa độ lên Server</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>