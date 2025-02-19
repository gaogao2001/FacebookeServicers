@extends('admin.layouts.master')

@section('title', 'Site Manager')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* .form-control {
        color: white;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .form-control:focus {
        color: white;
    } */

    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
       
        padding: 30px 40px;
    }

    .edit-form-control {
        width: 100%;
        background-color: transparent;
        border: none;
        outline: none;
        border: 2px solid rgba(255, 255, 255, .2);
        font-size: 15px;
        padding: 10px;
        box-shadow: #000000 0 0 10px;
    }

    .edit-form-control:hover,
    .edit-form-control:focus {
        background-color: #FFFFFF;
       
    }
</style>
@endsection

@section('content')

<div class="content-wrapper " style="background: #191c24;">
    <div class="container-fluid " style="padding-top: 20px;">
        <div class="row">
            <div class="col-12">
                <div class="card wrapper">
                    <div class="card-body ">
                        <h4 class="card-title">Site Manager</h4>
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif
                        <form class="form" action="{{ route('site-manager.update', $siteManager->_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name"><i class="fas fa-heading"></i> Tiêu đề trang</label>
                                <input name="name" class="edit-form-control" placeholder="Nhập tiêu đề" value="{{ $siteManager->name }}" required>
                            </div>

                            <!-- Meta Tags Inputs -->
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="meta_title"><i class="fas fa-tag"></i> Meta Title</label>
                                    <input type="text" class="edit-form-control" name="meta_title" placeholder="Nhập meta title" value="{{ $siteManager->meta_title }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="meta_description"><i class="fas fa-align-left"></i> Meta Description</label>
                                    <input type="text" class="edit-form-control" name="meta_description" placeholder="Nhập meta description" value="{{ $siteManager->meta_description }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="meta_keywords"><i class="fas fa-key"></i> Meta Keywords</label>
                                    <input type="text" class="edit-form-control" name="meta_keywords" placeholder="Nhập meta keywords" value="{{ $siteManager->meta_keywords }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="og_site_name"><i class="fas fa-globe"></i> OG Site Name</label>
                                    <input type="text" class="edit-form-control" name="og_site_name" placeholder="Nhập OG site name" value="{{ $siteManager->og_site_name }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="og_type"><i class="fas fa-cogs"></i> OG Type</label>
                                    <input type="text" class="edit-form-control" name="og_type" placeholder="Nhập OG type" value="{{ $siteManager->og_type }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="og_locale"><i class="fas fa-language"></i> OG Locale</label>
                                    <input type="text" class="edit-form-control" name="og_locale" placeholder="Nhập OG locale" value="{{ $siteManager->og_locale }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="og_locale_alternate"><i class="fas fa-exchange-alt"></i> OG Locale Alternate</label>
                                    <input type="text" class="edit-form-control" name="og_locale_alternate" placeholder="Nhập OG locale alternate" value="{{ $siteManager->og_locale_alternate }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="robots"><i class="fas fa-robot"></i> Robots</label>
                                    <input type="text" class="edit-form-control" name="robots" placeholder="Nhập robots" value="{{ $siteManager->robots }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="favicon"><i class="fas fa-icons"></i> Favicon</label>
                                    <input type="file" class="edit-form-control" name="favicon">
                                    <hr>
                                    @if ($siteManager->favicon)
                                    <img src="{{ $siteManager->favicon }}" alt="Favicon" width="400px" height="200px">
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="logo"><i class="fas fa-image"></i> Logo</label>
                                    <input type="file" class="edit-form-control" name="logo">
                                    <hr>
                                    @if ($siteManager->logo)
                                    <img src="{{ $siteManager->logo }}" alt="Logo" width="400px" height="200px">
                                    @endif
                                </div>
                            </div>
                            <div class="button-container">
                                <button type="submit" class="btn btn-outline-primary btn-fw">Lưu lại</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection