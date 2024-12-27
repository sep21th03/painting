<div class="modal fade" id="reply" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Phản hồi</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reviewUser" class="modal-reply-content"></div>
                <div class="mb-3">
                    <label for="replyContent" class="form-label">Trả lời</label>
                    <textarea name="replyContent" class="form-control" id="replyContent"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" name="submit_edit_user" class="btn btn-primary" onclick="submitReply()">Gửi</button>
            </div>
        </div>
    </div>
</div>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Thông báo</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Tính năng chưa ra mắt!
        </div>
    </div>
</div>

<script>
    function open_modal_reply(id) {
        console.log("Modal opened for ID:", id);

        var table = $("#list_review").DataTable();
        var rowData = table.row(`[id="${id}"]`).data();

        document.getElementById("reviewUser").innerHTML = `
        <strong>Tên người dùng:</strong> ${rowData.user.name} <br>
        <strong>Sản phẩm:</strong> <a href="{{ url('product-detail.html') }}?id=${rowData.product_id}">${rowData.product.name}</a> <br>
        <strong>Đánh giá:</strong> ${rowData.rating}/5 <br>
        <strong>Bình luận:</strong> ${rowData.comment}
    `;

        const modal = new bootstrap.Modal(document.getElementById("reply"));
        modal.show();

        CKEDITOR.replace('replyContent');
    }

    function submitReply() {
    const replyContent = CKEDITOR.instances.replyContent.getData();

    const toastEl = document.getElementById('liveToast');
    const toast = new bootstrap.Toast(toastEl);
    toast.show();

    const modal = bootstrap.Modal.getInstance(document.getElementById("reply"));
    modal.hide();
}

</script>