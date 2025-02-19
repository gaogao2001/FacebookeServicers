<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Corona Admin - Register</title>
    <link rel="stylesheet" href="/admin/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="/admin/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/admin/assets/css/style.css">
    <link rel="shortcut icon" href="/admin/assets/images/favicon.png" />
    <style>
        .form-control {
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-control:focus {
            color: white;
        }

        .card {
            position: relative;
            z-index: 1;
            margin-top: 30px;
            padding-top: 90px;
            /* Tạo khoảng trống lớn hơn cho logo */
        }

        .logo-container {
            position: absolute;
            top: 30px;
            /* Kéo logo sát phía trên khung đăng nhập */
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            z-index: 10;
        }

        .logo-container svg {
            width: 350px;
            /* Tăng kích thước logo */
            height: auto;
            /* Tự động điều chỉnh chiều cao */
        }

        .login-bg {
            position: relative;
        }

        .clock-container {
            position: absolute;
            top: 20px;
            right: 20px;
            transform: scale(0.72);
            text-align: center;
            z-index: 1000;
            background: transparent;
        }

        .clock {
            position: relative;
            width: 270px;
            height: 270px;
            border: 10px solid #5c2c00;
            border-radius: 50%;
            background: white;
            overflow: hidden;
        }

        .number {
            position: absolute;
            font-size: 20px;
            font-weight: bold;
            color: #5c2c00;
            transform: translate(-50%, -50%);
            /* Canh giữa số */
        }


        /* Tay kim */
        .hand {
            position: absolute;
            bottom: 50%;
            left: 50%;
            transform-origin: bottom;
            transform: translateX(-50%) rotate(0deg);
        }

        .hour-hand {
            width: 6px;
            height: 54px;
            background: black;
        }

        .minute-hand {
            width: 4px;
            height: 90px;
            background: #ffa500;
        }

        .second-hand {
            width: 2px;
            height: 108px;
            background: red;
        }

        /* Tâm đồng hồ */
        .center {
            position: absolute;
            width: 10px;
            height: 10px;
            background: white;
            border: 2px solid black;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <div class="clock-container">
            <div class="clock">
                <div class="number number12">12</div>
                <div class="number number1">1</div>
                <div class="number number2">2</div>
                <div class="number number3">3</div>
                <div class="number number4">4</div>
                <div class="number number5">5</div>
                <div class="number number6">6</div>
                <div class="number number7">7</div>
                <div class="number number8">8</div>
                <div class="number number9">9</div>
                <div class="number number10">10</div>
                <div class="number number11">11</div>
                <div class="hand hour-hand"></div>
                <div class="hand minute-hand"></div>
                <div class="hand second-hand"></div>
                <div class="center"></div>
            </div>
        </div>

        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="row w-100 m-0">
                <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
                    <div class="card col-lg-4 mx-auto">
                        <div class="logo-container">
                            <a class="sidebar-brand brand-logo" href="index.html">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 100">
                                    <text x="50%" y="50%" font-family="Arial" font-size="60" font-weight="bold" fill="#FFFFFF" dominant-baseline="middle" text-anchor="middle">
                                        Mega Bot
                                    </text>
                                </svg>
                            </a>
                        </div>
                        <div class="card-body px-5 py-5">
                            <h3 class="card-title text-left mb-3">Đăng kí</h3>
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="form-group">
                                    <label>Họ và tên</label>
                                    <input type="text" class="form-control p_input" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control p_input" name="email" value="{{ old('email') }}" required autocomplete="username">
                                </div>
                                <div class="form-group">
                                    <label>Mật khẩu</label>
                                    <input type="password" class="form-control p_input" name="password" required autocomplete="new-password">
                                </div>
                                <!-- <div class="form-group">
                                    <label>Role</label>
                                    <select name="role" class="form-control p_input" required>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div> -->
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-block enter-btn" style="width: 100%">Đăng kí</button>
                                </div>
                                <p class="sign-up text-center">Already have an Account? <a href="{{ route('login') }}"> Đăng nhập</a></p>
                                <p class="terms">Bằng cách tạo tài khoản, bạn chấp nhận <a href="#">Điều khoản & Điều kiện</a> của chúng tôi</p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/admin/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="/admin/assets/js/off-canvas.js"></script>
    <script src="/admin/assets/js/hoverable-collapse.js"></script>
    <script src="/admin/assets/js/misc.js"></script>
    <script src="/admin/assets/js/settings.js"></script>
    <script src="/admin/assets/js/todolist.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const clock = document.querySelector('.clock');
            const radius = clock.offsetWidth / 2; // Bán kính của đồng hồ
            const centerX = radius; // Tọa độ X của tâm
            const centerY = radius; // Tọa độ Y của tâm
            const numberOffset = radius - 30; // Điều chỉnh khoảng cách từ tâm đến số
            const xOffset = -10; // Dịch sang trái
            const yOffset = -10; // Kéo lên trên

            // Xóa các số cũ nếu có
            clock.querySelectorAll('.number').forEach((el) => el.remove());

            // Thêm các con số vào đồng hồ
            for (let i = 1; i <= 12; i++) {
                const angle = ((i - 3) * 30) * (Math.PI / 180); // Góc theo radian (bắt đầu từ số 12)
                const x = centerX + numberOffset * Math.cos(angle) + xOffset; // Thêm xOffset
                const y = centerY + numberOffset * Math.sin(angle) + yOffset; // Thêm yOffset

                const numberElement = document.createElement('div');
                numberElement.classList.add('number');
                numberElement.style.left = `${x}px`;
                numberElement.style.top = `${y}px`;
                numberElement.textContent = i;

                clock.appendChild(numberElement);
            }

            // Đồng hồ: Cập nhật vị trí kim
            const hourHand = document.querySelector('.hour-hand');
            const minuteHand = document.querySelector('.minute-hand');
            const secondHand = document.querySelector('.second-hand');

            function setClock() {
                const now = new Date();
                const seconds = now.getSeconds();
                const minutes = now.getMinutes();
                const hours = now.getHours();

                const secondAngle = seconds * 6; // 360° / 60 giây
                const minuteAngle = minutes * 6 + seconds / 10; // 360° / 60 phút
                const hourAngle = hours * 30 + minutes / 2; // 360° / 12 giờ

                secondHand.style.transform = `translateX(-50%) rotate(${secondAngle}deg)`;
                minuteHand.style.transform = `translateX(-50%) rotate(${minuteAngle}deg)`;
                hourHand.style.transform = `translateX(-50%) rotate(${hourAngle}deg)`;
            }

            setInterval(setClock, 1000);
            setClock();
        });
    </script>
</body>

</html>