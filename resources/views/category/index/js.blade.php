<script>
$(document).ready(function () {
    var table = $('#list_category').DataTable({
        initComplete: function() {
            var api = this.api();
            $('#searchInputBrand').on('input', function() {
                api.search(this.value).draw(); 
            });
        },
        ajax: {
            url: '{{ route("category.index") }}',
            method: 'GET',
            dataSrc: 'data'
        },
        rowId: function (row) {
            return `${row.id}`; 
        },
        columns: [
            {
                data: 'name',
                render: function (data) {
                    return `<span class="category-name">${data}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return `
                    <button class="btn btn-sm btn-primary" onclick="open_modal_add_setcategory(${row.id})">Thêm</button>
                    <button class="btn btn-sm btn-primary" onclick="open_modal_edit_category(${row.id})">Sửa</button>
                    <button class="btn btn-sm btn-danger" onclick="delete_category(${row.id})">Xóa</button>`;
                }
            }
        ],
        paging: false,
        info: false,
        searching: false
    });

    $('#list_category tbody').on('click', '.sorting_1', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            if (row.data().categories && row.data().categories.length > 0) {
                var childRowsHtml = row.data().categories.map(function(category) {
                    return `
                        <tr class="child-row">
                            <td style="padding-left: 40px;">${category.name}</td>
                            <td>
                                <button class="btn btn-sm btn-secondary view-child-details" data-id="${category.id}" onclick="open_modal_update_setcategory(${row.data().id}, ${category.id})">Sửa</button>
                                <button class="btn btn-sm btn-secondary view-child-details" onclick="delete_setcategory(${category.id})">Xóa</button>
                            </td>
                        </tr>
                    `;
                }).join('');

                row.child(childRowsHtml).show();
                tr.addClass('shown');
            }
        }
    });

});


    function delete_category(categoryId) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger",
            },
            buttonsStyling: false,
        });

        swalWithBootstrapButtons
            .fire({
                title: "Bạn có chắc chắn xóa loại sản phẩm này?",
                text: "Bạn sẽ không thể khôi phục lại!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Có, xóa nó!",
                cancelButtonText: "Không, hủy!",
                reverseButtons: true,
            })
            .then((result) => {
                if (result.isConfirmed) {
                    swalWithBootstrapButtons.fire({
                        title: "Đang xóa...",
                        timer: 2000,
                        timerProgressBar: true,
                    });

                    fetch("{{ route('category.destroy') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                    "content"
                                ),
                            },
                            body: JSON.stringify({
                                id: categoryId,
                            }),
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.status === "success") {
                                var table = $("#list_category").DataTable();
                                table.row(`#${categoryId}`).remove().draw();
                                swalWithBootstrapButtons.fire({
                                    title: "Đã xóa!",
                                    text: "Loại sản phẩm của bạn đã được xóa.",
                                    icon: "success",
                                });
                            } else {
                                swalWithBootstrapButtons.fire({
                                    title: "Xóa thất bại!",
                                    text: "Không thể xóa loại sản phẩm!",
                                    icon: "error",
                                });
                            }
                        });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Đã hủy",
                        text: "Loại sản phẩm của bạn vẫn an toàn :)",
                        icon: "error",
                    });
                }
            });
    }

    function delete_setcategory(setcategoryId) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger",
            },
            buttonsStyling: false,
        });

        swalWithBootstrapButtons
            .fire({
                title: "Bạn có chắc chắn xóa loại sản phẩm này?",
                text: "Bạn sẽ không thể khôi phục lại!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Có, xóa nó!",
                cancelButtonText: "Không, hủy!",
                reverseButtons: true,
            })
            .then((result) => {
                if (result.isConfirmed) {
                    swalWithBootstrapButtons.fire({
                        title: "Đang xóa...",
                        timer: 2000,
                        timerProgressBar: true,
                    });

                    fetch("{{ route('category.setcategory') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                    "content"
                                ),
                            },
                            body: JSON.stringify({
                                id: setcategoryId,
                            }),
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.status === "success") {
                                swalWithBootstrapButtons.fire({
                                    title: "Đã xóa!",
                                    text: "Loại sản phẩm của bạn đã được xóa.",
                                    icon: "success",
                                });
                            location.reload();
                            } else {
                                swalWithBootstrapButtons.fire({
                                    title: "Xóa thất bại!",
                                    text: "Không thể xóa loại sản phẩm!",
                                    icon: "error",
                                });
                            }
                        });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Đã hủy",
                        text: "Loại sản phẩm của bạn vẫn an toàn :)",
                        icon: "error",
                    });
                }
            });
    }
</script>