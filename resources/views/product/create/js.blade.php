<script type="module">
    import CustomEditor from '{{ asset("vendors/ckeditor5.js") }}';
    $(document).ready(function () {
        let editor3Instance, editor4Instance;
        const customEditor = new CustomEditor();

        // Khởi tạo editor cho một form cụ thể
        async function initializeEditors() {
            const uploadUrl = '{{ route("upload.image") }}';
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Khởi tạo editor1 nếu element tồn tại
            const editor3Element = document.querySelector('#editor3');
            if (editor3Element) {
                editor3Instance = await customEditor.initEditor('#editor3', uploadUrl, csrfToken);
            }

            // Khởi tạo editor2 nếu element tồn tại
            const editor4Element = document.querySelector('#editor4');
            if (editor4Element) {
                editor4Instance = await customEditor.initEditor('#editor4', uploadUrl, csrfToken);
            }
        }

        // Khởi tạo các editor
        initializeEditors();


        $("#addProduct").click(function (event) {
            event.preventDefault(); // Ngăn gửi form mặc định

            // Thu thập dữ liệu form
            let formValues = {
                name: $("input[name='add_title']").val(),
                info: editor4Instance.getData(),
                description: editor3Instance.getData(),
                discount: $("input[name='add_discount']").val(),
                setcategory_select: parseInt($("select[name='setcategory']").val()),
                code: $("input[name='add_code']").val(),
                sizeProduct: $("input[name='add_size']").val(),
                stock: $("input[name='add_stock']").val(),
                price: $("input[name='add_price']").val(),
            };

            var formData = new FormData();
            for (const key in formValues) {
                formData.append(key, formValues[key]);
            }

            // Thêm file ảnh
            const files = $("#file-add-product")[0].files;
            for (let i = 0; i < files.length; i++) {
                formData.append("gallery[]", files[i]);
            }

            $.ajax({
                url: "{{ route('product.store') }}",
                type: "POST",
                data: formData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    try {
                        let data = typeof response === "string" ? JSON.parse(response) : response; 

                        if (data.status === "success") {
                            Swal.fire({
                                icon: "success",
                                title: "Thành công!",
                                text: "Thêm sản phẩm thành công!",
                                timer: 1500,
                                showConfirmButton: false,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Lỗi khi thêm sản phẩm",
                                text: data.errors || "Dữ liệu không hợp lệ!",
                            });
                        }
                    } catch (e) {
                        console.error("Invalid JSON response:", response);
                        Swal.fire({
                            icon: "error",
                            title: "Lỗi!",
                            text: "Có lỗi xảy ra khi xử lý phản hồi từ server!",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", error);
                    let errorMessage = "Có lỗi xảy ra, vui lòng thử lại!";
                    if (xhr.status === 422) {
                        // Lỗi xác thực
                        const errors = JSON.parse(xhr.responseText).errors;
                        errorMessage = Object.values(errors).flat().join(", ");
                    } else if (xhr.status === 500) {
                        errorMessage = "Lỗi server, vui lòng liên hệ quản trị viên!";
                    }

                    Swal.fire({
                        icon: "error",
                        title: "Lỗi!",
                        text: errorMessage,
                    });
                },
            });
        });
    });


    let dropZone = document.getElementById("drop-zone");
    let fileInput = document.getElementById("file-add-product");
    let preview = document.getElementById("preview");

    ["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    ["dragenter", "dragover"].forEach((eventName) => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ["dragleave", "drop"].forEach((eventName) => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    dropZone.addEventListener("drop", handleDrop, false);

    dropZone.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", handleFiles);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dropZone.classList.add("highlight");
    }

    function unhighlight(e) {
        dropZone.classList.remove("highlight");
    }

    function handleDrop(e) {
        let dt = e.dataTransfer;
        let files = dt.files;
        handleFiles(files);
    }

    function handleFiles(input) {
        let files = input instanceof FileList ? input : input.target.files;
        if (!files) {
            return;
        }
        Array.from(files).forEach(uploadFile);
    }

    function uploadFile(file) {
        if (file.type.startsWith("image/")) {
            let reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function () {
                let img = document.createElement("img");
                img.src = reader.result;
                img.onclick = () => openModal(img.src);
                preview.appendChild(img);
            };
        } else {
            console.log("Vui lòng chọn file ảnh");
        }
    }

    let modal = document.getElementById('image-modal');
    let modalImg = document.getElementById('modal-image');
    let closeBtn = document.getElementsByClassName("close-btn")[0];

    // Đóng modal khi nhấn vào nút "X"
    closeBtn.onclick = function () {
        modal.style.display = "none";
    }

    // Xử lý sự kiện nhấn vào ảnh để phóng to
    function openModal(imageSrc) {
        modal.style.display = "block";
        modalImg.src = imageSrc;
    }


    document.addEventListener('DOMContentLoaded', function () {
        const setSelect = document.getElementById('set-select');
        const setcategorySelect = document.getElementById('setcategory-select');

        function filterSetCategories() {
            const selectedSetId = setSelect.value;

            for (let option of setcategorySelect.options) {
                if (option.getAttribute('data-set-id') === selectedSetId || selectedSetId === "") {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }
        }

        setSelect.addEventListener('change', filterSetCategories);

        filterSetCategories();
    });

</script>