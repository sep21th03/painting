@extends('layouts.app')
@section('title')
Danh sách hãng
@endsection
@section('content')
<style>
  #list_review th,
  #list_review td {
    font-size: 1rem;
  }

  #list_review th.sorting:before,
  #list_review th.sorting:after {
    display: none;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    color: white !important;
    border: 1px solid #111;
    background: #fff;
  }

  #list_review,
  #list_review th,
  #list_review td {
    border: none !important;
  }

  #list_review tbody tr {
    border-bottom: 1px solid #ccc !important;
    text-align: start !important;
  }

  #list_review tbody tr:last-child {
    border-bottom: none !important;
  }

  table.dataTable {
    border-collapse: collapse !important;
  }

  #customers-table-body tr {
    background-color: transparent !important;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    color: white !important;
    border: 1px solid #ffffff;
    background: #fff;
  }

  .dataTables_wrapper .dataTables_paginate .paginate_button:active {
    outline: none;
    background: #ffffff;
    box-shadow: inset 0 0 3px #ffffff;
  }

  .comment-text {
    max-height: 2em; 
    overflow: hidden; 
    display: -webkit-box;
    -webkit-line-clamp: 2; 
    -webkit-box-orient: vertical;
}
</style>
<div class="content">
  <div class="mb-9">
    <div class="row g-2 mb-4">
      <div class="col-auto">
        <h2 class="mb-0">Phản hồi khách hàng</h2>
      </div>
    </div>
    <div id="categories" data-list='{"valueNames":["customer","email","total-orders","total-spent","city","last-seen","last-order"],"page":10,"pagination":true}'>
      <div class="mb-4">
        <div class="row g-3 justify-content-between">
          <div class="col-auto">
            <div class="search-box">
              <form class="position-relative" id="searchForm"><input id="searchInput" class="form-control search-input search" type="search" placeholder="Tìm kiếm" aria-label="Search" />
                <span class="fas fa-search search-box-icon"></span>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative py-4">
        <div class="table-responsive mx-n1 px-1">
          <table id="list_review" class="table table-sm fs-9 mb-0">
            <thead>
              <tr>
                <th class="white-space-nowrap fs-9 ps-0 align-middle">
                  <div class="form-check mb-0 fs-8"><input class="form-check-input" id="checkbox-bulk-reviews-select" type="checkbox" data-bulk-select='{"body":"table-latest-review-body"}' /></div>
                </th>
                <th class="white-space-nowrap align-middle" scope="col" style="width: 30%" data-sort="product">Sản phẩm</th>
                <th class="align-middle text-start" scope="col" data-sort="customer" style="width: 10%">Khách hàng</th>
                <th class="align-middle text-start" scope="col" data-sort="rating" style="width: 10%">Đánh giá</th>
                <th class="align-middle text-start" scope="col" style="width: 30%" data-sort="review">Bình luận</th>
                <th class="text-start align-middle" scope="col" style="width: 20%" data-sort="time">Thời gian</th>
                <th class="text-start pe-0 align-middle" scope="col"></th>
              </tr>
            </thead>
            <tbody class="list text-center" id="customers-table-body">
              <!-- Hãng -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</main>
@endsection
@section('modal')
@include('review.modal.main')
@endsection
@section('script')
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
@include('review.index.js')
@endsection