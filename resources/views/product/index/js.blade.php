<script>
    $(document).ready(function () {
        let base_url = window.location.origin;
        if ($("#list_product").length) {
            $("#list_product").DataTable({
                dom: 'rtpl',
                initComplete: function () {
                    var api = this.api();
                    $('#searchInput').on('input', function () {
                        api.search(this.value).draw();
                    });
                    var lengthDiv = $('#list_product_length');
                    lengthDiv.html(`
                <label>Hiển thị
                    <select name="list_product_length" aria-controls="list_product" class="form-select form-select-sm w-25">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select> 
                sản phẩm / trang</label>
            `);
                },
                ajax: {
                    url: " {{ route('product.index') }}",
                    type: "get",
                    dataSrc: "data",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", error);
                    },
                },
                columns: [{
                    data: "id",
                    render: function (data, type, row) {
                        return `<div class="form-check mb-0 fs-8">
                                    <input class="form-check-input" type="checkbox" data-bulk-select-row='${JSON.stringify(
                            row
                        )}' />
                                </div>`;
                    },
                },
                {
                    data: "",
                    render: function (data, type, row) {
                        const imagePath = row.product_hex && row.product_hex[0] && row.product_hex[0].galleries && row.product_hex[0].galleries[0]
                            ? row.product_hex[0].galleries[0].image_path
                            : 'default-image.png';
                        return `<img src="${base_url}/${imagePath}" alt="Product Image" style="width: 50px; height: auto;">`;
                    },
                },
                {
                    data: "name",
                    render: function (data, type, row) {
                        const productId = row.id;
                        return `<a class="float-start fw-semibold line-clamp-3 mb-0" href="{{ route('product.detail', '') }}/${productId}">${data}</a>`;
                    },
                },
                {
                    data: "set_category_name",
                    render: function (data) {
                        return `<div class="text-center mb-0 fs-8">${data}</div>`;
                    },
                },
                {
                    data: "id",
                    render: function (data) {
                        return `
                            <div class="btn-reveal-trigger position-relative">
                                <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window">
                                    <span class="fas fa-ellipsis-h fs-10"></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end py-2">
                                    <a class="dropdown-item" href="/admin/product/${data}">Xem chi tiết</a>
                                    <a class="dropdown-item" href="#!" onclick="exportProduct(${data})">Export</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#!" onclick="removeProduct(${data})">Xóa</a>
                                </div>
                            </div>`;
                    },
                },
                ],
                rowId: "id",
                language: {
                    lengthMenu: "Hiện thị _MENU_ sản phẩm mỗi trang",
                    zeroRecords: "Không tìm thấy dữ liệu phù hợp",
                    info: "Hiển thị _START_ đến _END_ trong tổng số _TOTAL_ nguồn dữ liệu",
                    infoEmpty: "Không hiển thị dữ liệu",
                    infoFiltered: "(được lọc từ tổng số _MAX_ nguồn dữ liệu)",
                    search: "Tìm kiếm:",
                    paginate: {
                        first: "Đầu",
                        last: "Cuối",
                        next: "Tiếp",
                        previous: "Trước",
                    },
                },
            });
        }

        $("#addBtnProduct").click(function () {
            window.location.href = "{{ route('product.add') }}";
        });

        $("#checkbox-bulk-products-select").on("change", function () {
            let isChecked = $(this).is(":checked");

            $("#products-table-body input.form-check-input").prop(
                "checked",
                isChecked
            );
        });

        $('#category-filter').on('change', function () {
            table.ajax.reload();
        });

    });

    function removeProduct(productID) {
        Swal.fire({
            title: "Xác nhận xóa",
            text: "Bạn có chắc muốn xóa sản phẩm này?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Xóa",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('product.destroy') }}",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    data: {
                        id: productID
                    },
                    success: function (response) {
                        console.log(response);
                        Swal.fire({
                            icon: "success",
                            title: "Xóa thành công",
                            text: "Sản phẩm đã được xóa!",
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => {
                            $("#list_product tr").each(function () {
                                let trID = parseInt($(this).attr("id"));
                                if (trID === productID) {
                                    $(this).remove();
                                }
                            });
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                        Swal.fire({
                            icon: "error",
                            title: "Lỗi",
                            text: error.responseJSON?.message ||
                                "Có lỗi xảy ra, vui lòng thử lại!",
                        });
                    },
                });
            }
        });
    }

    function deleteSelectedProducts() {
        let selectedProductIds = [];

        $("input.form-check-input:checked").each(function () {
            let row = $(this).data("bulk-select-row");

            if (row && row.id) {
                selectedProductIds.push(row.id);
            }
        });

        if (selectedProductIds.length > 0) {
            Swal.fire({
                title: "Xác nhận xóa",
                text: "Bạn có chắc muốn xóa các sản phẩm này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Xóa",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('product.deleteProducts') }}",
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        data: {
                            ids: selectedProductIds
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Xóa thành công",
                                text: "Các sản phẩm đã được xóa!",
                                timer: 1500,
                                showConfirmButton: false,
                            }).then(() => {
                                selectedProductIds.forEach((id) => {
                                    $(`#list_product tr[id="${id}"]`).remove();
                                });
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error(error);
                            Swal.fire({
                                icon: "error",
                                title: "Lỗi",
                                text: xhr.responseJSON?.message ||
                                    "Có lỗi xảy ra, vui lòng thử lại!",
                            });
                        },
                    });
                }
            });
        } else {
            Swal.fire({
                icon: "warning",
                title: "Không có sản phẩm nào được chọn",
                text: "Vui lòng chọn ít nhất một sản phẩm để xóa.",
            });
        }
    }



</script>