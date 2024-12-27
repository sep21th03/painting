@extends('layouts.app')
@section('title')
Thêm sản phẩm
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

    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        overflow: hidden;
        padding-top: 25px;
    }

    .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }

    .close-btn {
        position: absolute;
        top: 20px;
        right: 25px;
        color: white;
        font-size: 35px;
        font-weight: bold;
        cursor: pointer;
    }

    .close-btn:hover,
    .close-btn:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
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

    #file-add-product {
        display: none;
    }
</style>
<div class="content">

    <form class="mb-9">
        <div class="row g-3 flex-between-end mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Thêm sản phẩm</h2>
            </div>
            <div class="col-auto">
                <button class="btn btn-phoenix-secondary me-2 mb-2 mb-sm-0" type="button">Discard</button>
                <button class="btn btn-phoenix-primary me-2 mb-2 mb-sm-0" type="button">Save draft</button>
                <button id="addProduct" class="btn btn-primary mb-2 mb-sm-0">Thêm</button>
            </div>
        </div>
        <div class="row g-5">
            <div class="col-12 col-xl-8">
                <h4 class="mb-3">Tên sản phẩm</h4><input class="form-control mb-5" type="text" name="add_title" />
                <div class="mb-6">
                    <h4 class="mb-3">Giới thiệu sản phẩm</h4>
                    <div id="editor4" class="tinymce" name="add_info"></div>
                </div>
                <h4 class="mb-3">Mô tả sản phẩm</h4>
                <div id="editor3" name="add_description"></div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="row g-2">
                    <div class="col-12 col-xl-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Nguồn gốc</h4>
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
                                        <div class="col-12 col-sm-6 col-xl-12">
                                            <div class="mb-4">
                                                <h5 class="mb-2 text-body-highlight">Giảm giá %</h5><input
                                                    class="form-control mb-xl-3" name="add_discount" type="text" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-12">
                            <div class="card">
                                <div class="card-body">

                                    <div class="mb-3">
                                        <label for="codeProduct" class="form-label">Tên Mã</label>
                                        <input type="text" class="form-control" id="codeProduct" name="add_code"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="sizeProduct" class="form-label">Size</label>
                                        <input type="text" id="sizeProduct" class="form-control mb-3" name="add_size">
                                    </div>
                                    <div class="mt-3 mb-3">
                                        <label for="stock" class="mb-2 text-body-highlight">Số lượng:</label>
                                        <input id="stock" class="form-control mb-xl-3" name="add_stock" type="text" />
                                    </div>
                                    <div class="mt-3 mb-3">
                                        <label for="price" class="mb-2 text-body-highlight">Giá:</label>
                                        <input id="price" class="form-control mb-xl-3" name="add_price" type="text" />
                                        <h4 class="mb-3">Ảnh sản phẩm</h4>
                                        <div id="drop-zone">
                                            <p>Kéo và thả nhiều ảnh vào đây hoặc click để chọn files</p>
                                            <input type="file" id="file-add-product" name="gallery[]" accept="image/*"
                                                multiple>
                                        </div>
                                        <div id="preview" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="image-modal" class="modal">
    <span class="close-btn">&times;</span>
    <img class="modal-content" id="modal-image">
</div>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr"></script>
@include('product.create.js')
@endsection