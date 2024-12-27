@extends('layouts.app')
@section('title')
Chi tiết sản phẩm
@endsection
@section('content')
<style>
    #cke_description {
        width: 100%;
    }


    #drop-zone {
        border: 2px dashed #ccc;
        border-radius: 5px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
    }

    #drop-zone.highlight {
        border-color: #007bff;
        background-color: #eaf4fc;
    }

    #preview img {
        margin-top: 10px;
        max-width: 100px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    p {
        margin-top: 0;
    }

    .button {
        display: inline-block;
        padding: 10px;
        background: #ccc;
        cursor: pointer;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .button:hover {
        background: #ddd;
    }

    #file-update-product {
        display: none;
    }

    #image-modal {
        justify-content: center;
        align-items: center;
        max-width: 100%;
    }

    #modal-image {
        max-width: 500px;
    }

    .close-btn {
        font-size: 40px;
    }

    .highlight {
        border-color: #4CAF50 !important;
        background-color: #E8F5E9 !important;
    }

    #preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    #preview .position-relative:hover .btn-danger {
        opacity: 1;
    }

    #preview .btn-danger {
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    #preview img {
        border-radius: 4px;
        border: 1px solid #ddd;
    }
</style>
<div class="content">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#!">Page 1</a></li>
            <li class="breadcrumb-item"><a href="#!">Page 2</a></li>
            <li class="breadcrumb-item active">Default</li>
        </ol>
    </nav>
    <form class="mb-9">
        <input class="form-control mb-5" type="text" value="{{ $product->id }}" name="edit_id" hidden />
        <div class="row g-3 flex-between-end mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Chi tiết sản phẩm</h2>
            </div>
            <div class="col-auto">
                <button id="addProduct" class="btn btn-phoenix-secondary me-2 mb-2 mb-sm-0" type="button">Thêm sản
                    phẩm</button>
                <button id="reloadDetalProduct" class="btn btn-phoenix-primary me-2 mb-2 mb-sm-0"
                    type="button">Reload</button>
                <button id="editBtnProduct" class="btn btn-primary mb-2 mb-sm-0" type="button">Lưu</button>
            </div>
        </div>
        <div class="row g-5">
            <div class="col-12 col-xl-8">
                <h4 class="mb-3">Tên sản phẩm</h4><input class="form-control mb-5" type="text"
                    value="{{ $product->name }}" name="edit_title" />
                <div class="mb-6">
                    <h4 class="mb-3">Giới thiệu sản phẩm</h4>
                    <div id="editor2" class="tinymce" name="edit_info">{{ $product->info }}</div>
                </div>
                <h4 class="mb-3">Mô tả sản phẩm</h4>
                <div id="editor1" name="edit_description">{{ $product->description }}</div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="row g-2">
                    <div class="col-12 col-xl-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Danh mục</h4>
                                <div class="row gx-3">
                                    <div class="col-12 col-sm-6 col-xl-12">
                                        <div class="mb-4">
                                            <div class="d-flex flex-wrap mb-2">
                                                <h5 class="mb-0 text-body-highlight me-2">Danh mục</h5>
                                                <a class="fw-bold fs-9" href="{{ route('category.list') }}">Thêm danh
                                                    mục mới</a>
                                            </div>
                                            <select class="form-select mb-3" aria-label="set" name="set"
                                                id="set-select">
                                                @foreach ($sets as $set)
                                                    <option value="{{ $set->id }}">{{ $set->name }}</option>
                                                @endforeach
                                            </select>

                                            <label for="rom" class="mb-2 text-body-highlight">Chọn loại danh
                                                mục:</label>
                                            <select id="setcategory-select" name="setcategory" class="form-select mb-3"
                                                id="setcategory-select">
                                                @foreach ($setcategories as $setcategory)
                                                    <option value="{{ $setcategory->id }}"
                                                        data-set-id="{{ $setcategory->set_id }}">
                                                        {{ $setcategory->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 col-xl-12">
                                        <div class="mb-4">
                                            <h5 class="mb-2 text-body-highlight">Giảm giá %</h5><input
                                                class="form-control mb-xl-3" name="edit_discount" type="text"
                                                value="{{ $product->discount }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="mt-3 mb-3">
                                    <label for="code" class="mb-2 text-body-highlight">Mã sản phẩm:</label>
                                    <a class="fw-bold fs-9 ms-5" href="#!" data-bs-toggle="modal"
                                        data-bs-target="#addHexModal">Thêm Hex Mới</a><a id="deleteHex"
                                        class="fw-bold fs-9 ms-5" href="#!"> Xóa Hex</a>
                                    <select id="code" name="edit_code" class="form-select mb-3">
                                        @foreach ($product->productHex as $hex)
                                            <option value="{{ $hex->id }}" data-hex="{{ $hex->hex_code }}">
                                                {{ $hex->hex_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="size" class="mb-2 text-body-highlight">Chọn Size:</label>
                                <a id="addSize" class="fw-bold fs-9 ms-5" href="#!" data-bs-toggle="modal"
                                    data-bs-target="#addSizeModal">Thêm Size Mới</a><a id="deleteSize"
                                    class="fw-bold fs-9 ms-5" href="#!"> Xóa Size</a>
                                <select id="size" name="edit_size" class="form-select mb-3">

                                </select>
                                <div class="mt-3 mb-3">
                                    <label for="stock" class="mb-2 text-body-highlight">Số lượng:</label>
                                    <input id="stock" class="form-control mb-xl-3" name="edit_stock" type="text" />
                                </div>
                                <div class="mt-3 mb-3">
                                    <label for="price" class="mb-2 text-body-highlight">Giá:</label>
                                    <input id="price" class="form-control mb-xl-3" name="edit_price" type="text" />
                                    <h4 class="mb-3">Ảnh sản phẩm</h4>
                                    <div id="drop-zone">
                                        <p>Kéo và thả nhiều ảnh vào đây hoặc click để chọn files</p>
                                        <input type="file" id="file-update-product" name="gallery[]" accept="image/*"
                                            multiple>
                                    </div>
                                    <div id="preview" class="mt-3"></div>
                                </div>
                            </div><button id="updateBtn" class="btn btn-phoenix-primary w-100"
                                type="button">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</form>
</div>


<div id="image-modal" class="modal">
    <img class="modal-content" id="modal-image">
    <span class="close-btn">&times;</span>
</div>
@endsection
@section('modal')
@include('product.edit.modal')
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr"></script>
@include('product.edit.js')
@endsection