<script>
    $(document).ready(function () {
        if ($("#list_review").length) {
            $("#list_review").DataTable({
                dom: 'rtp',
                initComplete: function () {
                    var api = this.api();
                    $('#searchInput').on('input', function () {
                        api.search(this.value).draw();
                    });
                },
                ajax: {
                    url: "{{ route('review.index') }}",
                    type: "GET",
                    dataSrc: "data",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("Error get data from ajax: " + textStatus);
                    },
                },
                columns: [

                    {
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
                        data: "product.name",
                        render: function (data, type, row) {
                            return '<a href="{{ url('product-detail.html ') }}?id=' + row.product_id + '">' + data + '</a>';
                        }
                    },
                    {
                        data: "user.name",
                        render: function (data, type, row) {
                            return data;
                        }
                    },
                    {
                        data: "rating",
                        render: function (data, type, row) {
                            let stars = '';
                            for (let i = 1; i <= 5; i++) {
                                if (i <= data) {
                                    stars += '<i class="fas fa-star text-warning"></i>';
                                } else {
                                    stars += '<i class="far fa-star text-warning"></i>';
                                }
                            }
                            return stars;
                        }
                    },
                    {
                        data: "comment",
                        render: function (data, type, row) {
                            return `
            <div class="comment-text" style="max-height: 3em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                ${data}
            </div>
        `;
                        }
                    },
                    {
                        data: "created_at",
                        render: function (data, type, row) {
                            return moment(data).fromNow();
                        }
                    },
                    {
                        data: "id",
                        render: function (data, type, row) {
                            return `
                            <div class="btn-reveal-trigger position-relative">
                                <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window">
                                    <span class="fas fa-ellipsis-h fs-10"></span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end py-2">
                                    <a class="dropdown-item" onclick="open_modal_reply(${data})">Trả lời</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#!" onclick="removeReview(${data})">Xóa</a>
                                </div>
                            </div>`;
                        },
                        className: "text-center",
                    },
                ],
                rowId: "id",
                language: {
                    lengthMenu: "Hiện thị _MENU_ loại sản phẩm mỗi trang",
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
                order: [
                    [0, "asc"]
                ],
            });
        }
    });



    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        let interval = Math.floor(seconds / 31536000);
        if (interval >= 1) {
            return interval + ' năm trước';
        }

        interval = Math.floor(seconds / 2592000);
        if (interval >= 1) {
            return interval + ' tháng trước';
        }

        interval = Math.floor(seconds / 86400);
        if (interval >= 1) {
            return interval + ' ngày trước';
        }

        interval = Math.floor(seconds / 3600);
        if (interval >= 1) {
            return interval + ' giờ trước';
        }

        interval = Math.floor(seconds / 60);
        if (interval >= 1) {
            return interval + ' phút trước';
        }

        if (seconds < 10) return 'vừa xong';

        return Math.floor(seconds) + ' giây trước';
    }

    function removeReview(productID) {
        Swal.fire({
            title: "Xác nhận xóa",
            text: "Bạn có chắc muốn xóa bình luận này?",
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



</script>