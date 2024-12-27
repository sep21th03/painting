@extends('layouts.app')
@section('title')
Danh sách sản phẩm
@endsection
@section('content')
<style>
    #list_product th,
    #list_product td {
        font-size: 1rem;
    }

    #list_product th.sorting:before,
    #list_product th.sorting:after {
        display: none;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        color: white !important;
        border: 1px solid #111;
        background: #fff;
    }

    #list_product,
    #list_product th,
    #list_product td {
        border: none !important;
    }

    #list_product tbody tr {
        border-bottom: 1px solid #ccc !important;
    }

    #list_product tbody tr:last-child {
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

    .form-check {
        padding-left: 2.5em !important;
    }

    .mbs-btn-export:hover * {
        color: green;
    }
    .mbs-btn-export:hover {
        text-decoration: none;
    }
</style>
<div class="content">
    <div class="mb-9">
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <h2 class="mb-0">Products</h2>
            </div>
        </div>
        <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="#"><span>All</span><span
                        class="text-body-tertiary fw-semibold">({{ $total_products }})</span></a></li>
        </ul>
        <div id="products">
            <div class="mb-4">
                <div class="d-flex flex-wrap gap-3">
                    <div class="search-box">
                        <form class="position-relative"><input id="searchInput" class="form-control search-input search" type="search"
                                placeholder="Tìm kiếm" aria-label="Search" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                    <div class="ms-xxl-auto">
                        <button class="mbs-btn-export btn btn-link text-body me-4 px-0" onclick="exportToExcel()">
                            <span class="fa-solid fa-file-export fs-9 me-2"></span>
                            <span>Export Excel File</span>
                        </button>
                        <button class="btn btn-primary" id="addBtnProduct">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-plus-lg" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                            </svg>
                            <span>
                                Thêm sản phẩm
                            </span>
                        </button>
                        <button class="btn btn-danger" onclick="deleteSelectedProducts()">
                            <svg
                                xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-trash" viewBox="0 0 16 16">
                                <path
                                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                <path
                                    d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                            </svg>
                            <span>
                                Delete product
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div
                class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
                <div class="table-responsive scrollbar mx-n1 px-1">
                    <table id="list_product" class="table fs-9 mb-0">
                        <thead>
                            <tr>
                                <th class="white-space-nowrap fs-9 align-middle ps-0" data-orderable="false"
                                    style="width: 5%;">
                                    <div class="form-check mb-0 fs-8"><input class="form-check-input"
                                            id="checkbox-bulk-products-select" type="checkbox"
                                            data-bulk-select='{"body":"products-table-body"}' /></div>
                                </th>
                                <th class="sort white-space-nowrap align-middle fs-10" scope="col" style="width: 20%;">
                                </th>
                                <th class="sort white-space-nowrap align-middle text-start ps-4" scope="col"
                                    style="width:350px;" data-sort="product">TÊN SẢN PHẨM</th>
                                <th class="sort align-middle  text-center ps-4" scope="col" data-sort="category"
                                    style="width:150px;">DANH MỤC</th>
                                <th class="text-center align-middle pe-0 ps-4" scope="col" style="width: 7%;">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="list text-start fs-8" id="products-table-body">
                            <!-- Products -->
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
@include('product.modal.main')
@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
@include('product.index.js')
@endsection
