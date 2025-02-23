<div id="theme-panel" class="position-fixed shadow">
    <button id="toggle-panel" title="Tùy chỉnh giao diện">⚙</button>
    <div id="panel-content">
        <h4 class="panel-title">Tùy chỉnh Giao Diện</h4>
        <div class="panel-section">
            <label for="title-color">Màu Tiêu Đề</label>
            <input type="color" id="title-color" value="#ffffff">
        </div>
        <!-- Content-wrapper -->
        <div class="panel-section">
            <label for="custom-theme-color">Nền Chính</label>
            <input type="color" id="custom-theme-color" value="#ffffff">
        </div>

        <!-- Upload ảnh nền content-wrapper -->
        <div class="panel-section">
            <label for="background-image">Ảnh Nền Chính</label>
            <input type="file" id="background-image" accept="image/*">
        </div>

        <!-- Sidebar và Navbar -->
        <div class="panel-section">
            <label for="shared-color">Nền Sidebar & Navbar</label>
            <input type="color" id="shared-color" value="#343a40">
        </div>

        <!-- Card -->
        <div class="panel-section">
            <label for="card-color">Nền Card</label>
            <input type="color" id="card-color" value="#f8f9fa">
        </div>

        <div class="panel-section">
            <label for="text-color">Màu Chữ</label>
            <input type="color" id="text-color" value="#000000">
        </div>

        <!-- Reset -->
        <div class="panel-section text-center">
            <button id="reset-theme" class="btn btn-danger btn-sm">Reset</button>
        </div>
    </div>
</div>


