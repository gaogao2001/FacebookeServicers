@extends('admin.layouts.master')

@section('title', 'Quản lí link')

@section('head.scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="{{ asset('modules/link/css/link.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/toast.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/pagination.css') }}">

<script src="{{ asset('assets/admin/js/pagination.js') }}" defer></script>
<script src="{{ asset('modules/link/js/link.js') }}" defer></script>
<script src="{{ asset('assets/admin/js/toast.js') }}" defer></script>
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

  .wrapper {
    background: transparent;
    border: 2px solid rgba(225, 225, 225, .2);
    backdrop-filter: blur(10px);
    box-shadow: 0 0 10px rgba(0, 0, 0, .2);
    color: #FFFFFF;
    padding: 10px 10px;
  }

  table {
    table-layout: fixed;
    width: 100%;
  }



  table td:nth-child(3),
  table td:nth-child(4),
  table td:nth-child(5) {
    white-space: normal;
    word-break: break-word;
    overflow-wrap: break-word;
    /* max-width: ... nếu muốn khống chế thêm */
  }
</style>

@endsection
@section('content')
<div class="content-wrapper " style="background: #191c24;">

  <div class="container-fluid" style="padding-top:20px;">
    <div class="row" style="padding-top:20px;">
      <div class="col-md-12 grid-margin">
        <div class="card wrapper">
          <div class="card-body d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0" id="linkCountTitle" style="  color: #bfbfbf">Quản lí dữ liệu URL</h4>
            <div class="button-container" style="display: flex; justify-content: flex-end; margin-right:50px;">
              <a href="#" class="btn btn-outline-primary btn-fw" id="addLink">Thêm Link</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card wrapper">
          <div class="card-body">
            <div class="table-responsive" style="max-height: 600px;">
              <table id="linkTable" class="table " style="table-layout: fixed; width: 100%;">
                <thead>
                  <tr>
                    <th style="width: 40px;"><input type="checkbox" id="selectAll"></th>
                    <th style="min-width: 60px;">Domain</th>
                    <th style="min-width: 60px;">UID</th>
                    <th style="min-width: 150px;">Hash ID</th>
                    <th style="min-width: 200px;">URL</th>
                    <th style="min-width: 120px;">Được thêm bởi</th>
                    <th style="min-width: 100px;">Action</th>
                  </tr>
                </thead>
                <tbody id="linkList">
                  <!-- Dữ liệu ở đây -->
                </tbody>
              </table>
            </div>
            <ul id="pagination" class="page-numbers pagination"></ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->
  <div class="modal fade" id="editLink" tabindex="-1" role="dialog" aria-labelledby="editLinkModalLabel" aria-hidden="true" style="padding-top:80px">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content" style="background-color: #191c24;">
        <div class="modal-header">
          <h5 class="modal-title" id="editLinkModalLabel">Edit URL</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="editLinkForm">
            @csrf
            <div class="form-group">
              <label for="editUrl">URL</label>
              <input type="url" class="form-control" id="editUrl" name="url" placeholder="URL">
            </div>
            <button type="submit" class="btn btn-primary">Xác nhận</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection