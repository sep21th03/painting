<div class="modal fade" id="addHexModal" tabindex="-1" aria-labelledby="addHexModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addHexModalLabel">Thêm Mã Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addHexForm">
                    <input type="text" class="form-control" id="addhexproductID" value="{{ $product->id }}" hidden>
                    <div class="mb-3">
                        <label for="hexName" class="form-label">Tên Mã</label>
                        <input type="text" class="form-control" id="hexName" required>
                    </div>
                    <div class="mb-3">
                        <label for="sizeHex" class="form-label">Tên Size</label>
                        <input type="text" id="sizeHex" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="priceHex" class="form-label">Giá tiền</label>
                        <input type="text" class="form-control" id="priceHex" required>
                    </div>
                    <div class="mb-3">
                        <label for="stockHex" class="form-label">Số lượng</label>
                        <input type="text" class="form-control" id="stockHex" required>
                    </div>
                    <div class="mb-3">
                        <label for="imageHex" class="form-label">Chọn Ảnh</label>
                        <input type="file" class="form-control" id="imageHex" name="imageHex[]" accept="image/*"
                            multiple>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="submitHex">Lưu</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addSizeModal" tabindex="-1" aria-labelledby="addSizeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSizeModalLabel">Thêm Size Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSizeForm">
                    <input type="text" class="form-control" id="addhexID" value="" hidden>
                    <div class="mb-3">
                        <label for="sizeName" class="form-label">Tên size</label>
                        <input type="text" class="form-control" id="sizeName" required>
                    </div>
                    <div class="mb-3">
                        <label for="priceSize" class="form-label">Giá tiền</label>
                        <input type="text" class="form-control" id="priceSize" required>
                    </div>
                    <div class="mb-3">
                        <label for="stockSize" class="form-label">Số lượng</label>
                        <input type="text" class="form-control" id="stockSize" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="submitSize">Lưu</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#submitHex").click(function () {
            let nameHex = $("#hexName").val();
            let sizeHex = $("#sizeHex").val();
            let priceHex = parseInt(
                $("#priceHex").val().replace(/[\.,₫\s]/g, "").replace(" ₫", "")
            );
            let stockHex = $("#stockHex").val();
            let product_id = $("#addhexproductID").val();



            var formData = new FormData();
            formData.append("nameHex", nameHex);
            formData.append("sizeHex", sizeHex);
            formData.append("priceHex", priceHex);
            formData.append("stockHex", stockHex);
            formData.append("product_id", product_id);
            const files = $("#imageHex")[0].files;
            for (let i = 0; i < files.length; i++) {
                formData.append("imageHex[]", files[i]);
            }
            $.ajax({
                url: "{{ route('product.addHex') }}",
                type: "POST",
                data: formData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    Swal.fire({
                        icon: "success",
                        title: "Thành công!",
                        text: "Thêm màu thành công!",
                        timer: 1500,
                        showConfirmButton: false,
                    });
                    location.reload();
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Lỗi!",
                        text: error.message || "Có lỗi xảy ra, vui lòng thử lại!",
                    });
                },
            });
        });


        $("#submitSize").click(function () {
            let addhexID = $("#addhexID").val();
            let sizeName = $("#sizeName").val();
            let priceSize = parseInt(
                $("#priceSize").val().replace(/[\.,₫\s]/g, "").replace(" ₫", "")
            );
            let stockSize = $("#stockSize").val();

            var formData = new FormData();
            formData.append("sizeName", sizeName);
            formData.append("priceSize", priceSize);
            formData.append("stockSize", stockSize);
            formData.append("addhexID", addhexID);
           
            $.ajax({
                url: "{{ route('product.addSize') }}",
                type: "POST",
                data: formData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    Swal.fire({
                        icon: "success",
                        title: "Thành công!",
                        text: "Thêm màu thành công!",
                        timer: 1500,
                        showConfirmButton: false,
                    });
                    location.reload();
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Lỗi!",
                        text: error.message || "Có lỗi xảy ra, vui lòng thử lại!",
                    });
                },
            });
        });
    });
</script>