<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ứng dụng Laravel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: rgb(232, 237, 251);
            min-height: 100vh;
        }

        .card-login {
            border-radius: 1rem;
            overflow: hidden;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #2575fc;
        }

        .btn-login {
            background-color: #2575fc;
            border: none;
            transition: background-color .3s;
        }

        .btn-login:hover {
            background-color: #1a5bb8;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #999;
        }

        .form-input-with-icon {
            position: relative;
        }

        .form-input-with-icon .form-control {
            padding-left: 2.5rem;
        }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
        }

        .nav-link.active {
            background-color: #2575fc;
            color: #fff !important;
            border-radius: .375rem;
        }

        .nav-link:hover {
            background-color: #e9ecef;
            border-radius: .375rem;
        }
    </style>
    @yield('css')
</head>

<body>

    <div class="container-fluid">
        <div class="row vh-100">
            <nav class="col-12 col-md-3 col-lg-2 bg-light sidebar py-4 d-flex flex-column">
                <h5 class="text-center mb-4">Chào, {{ Auth::user()->name }}!</h5>
                {{-- <h5>Chào, {{ Auth::user()->name }}!</h5> --}}


                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-2">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-dark' }}">
                            <i class="bi bi-house-door-fill me-2"></i> Trang chủ
                        </a>
                    </li>

                    @role('admin')
                        <li class="nav-item mb-2">
                            <a href="{{ route('employees.index') }}" class="nav-link">
                                <i class="bi bi-droplet-fill me-2"></i> Thêm mới mã thiết bị TĐH
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="{{ route('admin.overview.index') }}"
                                class="nav-link {{ request()->routeIs('admin.overview.*') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-bar-chart-line me-2"></i> Check Log Bơm Tất Cả Mã TĐH
                            </a>
                        </li>
                    @endrole

                    @hasanyrole('employee')
                        {{-- @hasanyrole('admin') --}}
                        {{-- <li class="nav-item mb-2">
                            <a href="{{ route('tanks.index') }}"
                                class="nav-link {{ request()->routeIs('tanks.*') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-droplet-fill me-2"></i> Bể chứa
                            </a>
                        </li> --}}

                        <li class="nav-item mb-2">
                            <a href="{{ route('pumps.index') }}"
                                class="nav-link {{ request()->routeIs('pumps.*') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-funnel-fill me-2"></i> Vòi bơm
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="{{ route('overview.index') }}"
                                class="nav-link {{ request()->routeIs('overview.*') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-bar-chart-line me-2"></i> Check Log Tổng Quan
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="{{ route('fuel-settings.index') }}"
                                class="nav-link {{ request()->routeIs('fuel-settings.*') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-gear-fill me-2"></i> Cài đặt nhiên liệu
                            </a>
                        </li>


                        <li class="nav-item mb-2">
                            <a href="{{ route('reports.inventory') }}"
                                class="nav-link {{ request()->routeIs('reports.inventory') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-journal-text me-2"></i> Báo cáo Nhập-Xuất-Tồn
                            </a>
                        </li>
                    @endhasanyrole


                    @hasanyrole('employee')
                        <li class="nav-item mb-2">
                            <a href="{{ route('entries.create') }}"
                                class="nav-link {{ request()->routeIs('entries.create') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-box-arrow-in-down me-2"></i> Nhập hàng
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="{{ route('entries.stats.form') }}"
                                class="nav-link {{ request()->routeIs('entries.stats.form') || request()->routeIs('entries.stats') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-bar-chart-line me-2"></i> Thống kê nhập hàng
                            </a>
                        </li>
                    @endhasanyrole

                    @role('admin')
                        <li class="nav-item mb-2">
                            <a href="{{ route('admin.fuel_settings.index') }}"
                                class="nav-link {{ request()->routeIs('admin.fuel_settings.*') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-gear-fill me-2"></i> Thiết lập tồn kho & hao hụt
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="{{ route('admin.stores.index') }}"
                                class="nav-link {{ request()->routeIs('admin.stores.*') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-people-fill me-2"></i> Quản lý CH
                            </a>
                        </li>

                        <li class="nav-item mb-2">
                            <a href="{{ route('admin.users.index') }}"
                                class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-people-fill me-2"></i> Quản lý Users
                            </a>
                        </li>
                    @endrole


                    @role('employee')
                        <li class="nav-item mb-2">
                            <a href="{{ route('employee.password.change') }}"
                                class="nav-link {{ request()->routeIs('employee.password.*') ? 'active' : 'text-dark' }}">
                                <i class="bi bi-key-fill me-2"></i> Đổi mật khẩu
                            </a>
                        </li>
                    @endrole


                    {{-- Đăng xuất --}}
                    <li class="nav-item mt-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-danger w-100">
                                <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
                            </button>
                        </form>
                    </li>
                </ul>

                <div class="mt-auto text-center text-muted small">
                    © {{ date('Y') }} YourCompany
                </div>
            </nav>

            <main class="col px-4 py-4 overflow-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('js')

</body>

</html>
