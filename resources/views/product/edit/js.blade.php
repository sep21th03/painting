<script type="module">
    import CustomEditor from '{{ asset("vendors/ckeditor5.js") }}';
    $(document).ready(function () {
        let editor1Instance, editor2Instance;
        const customEditor = new CustomEditor();

        async function initializeEditors() {
            const uploadUrl = '{{ route("upload.image") }}';
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            const editor1Element = document.querySelector('#editor1');
            if (editor1Element) {
                editor1Instance = await customEditor.initEditor('#editor1', uploadUrl, csrfToken);
            }

            const editor2Element = document.querySelector('#editor2');
            if (editor2Element) {
                editor2Instance = await customEditor.initEditor('#editor2', uploadUrl, csrfToken);
            }
        }

        initializeEditors();



        $("#reloadDetalProduct").click(function () {
            location.reload();
        });

        $("#addProduct").click(function () {
            window.location.href = "{{ route('product.add') }}"
        });

        $("#editBtnProduct").click(function () {
            let formValues = {
                id: $("input[name='edit_id']").val(),
                name: $("input[name='edit_title']").val(),
                info: editor2Instance.getData(),
                description: editor1Instance.getData(),
                discount: $("input[name='edit_discount']").val(),
                setcategory: $("select[name='setcategory']")
                    .find("option:selected")
                    .val(),
            };

            var formData = new FormData();

            for (const key in formValues) {
                formData.append(key, formValues[key]);
            }

            $.ajax({
                url: "{{ route('product.update') }}",
                type: "POST",
                data: formData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "Thành công!",
                            text: "Sửa sản phẩm thành công!",
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Sửa sản phẩm thất bại!",
                            text: error.message,
                        });
                    }
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


        $("#updateBtn").click(function () {
            let formValues = {
                code: $("select[name='edit_code']")
                    .find("option:selected")
                    .val(),
                size: $("select[name='edit_size']")
                    .find("option:selected")
                    .val(),
                stock: $("input[name='edit_stock']").val(),
                price: parseInt(
                    $("input[name='edit_price']").val().replace(/[\.,₫\s]/g, "").replace(" ₫", "")
                ),

            };

            var formData = new FormData();

            for (const key in formValues) {
                formData.append(key, formValues[key]);
            }
            const files = $("#file-update-product")[0].files;
            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    formData.append("gallery[]", files[i]);
                }
            }

            $.ajax({
                url: "{{ route('product.updateHex') }}",
                type: "POST",
                data: formData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "Thành công!",
                            text: "Sửa sản phẩm thành công!",
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Sửa sản phẩm thất bại!",
                            text: error.message,
                        });
                    }
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

    })






    document.addEventListener('DOMContentLoaded', function () {
        const setSelect = document.getElementById('set-select');
        const setcategorySelect = document.getElementById('setcategory-select');

        function updateSetCategories() {
            const selectedSetId = setSelect.value;
            const setcategoryOptions = setcategorySelect.querySelectorAll('option');

            setcategoryOptions.forEach(option => {
                const setId = option.getAttribute('data-set-id');
                option.style.display = setId === selectedSetId ? '' : 'none';
            });

            const firstVisibleOption = Array.from(setcategoryOptions).find(
                option => option.style.display !== 'none'
            );

            if (firstVisibleOption) {
                setcategorySelect.value = firstVisibleOption.value;
            }
        }

        updateSetCategories();

        setSelect.addEventListener('change', updateSetCategories);

        const currentSetCategoryId = '{{ $product->set_category_id ?? "" }}';
        if (currentSetCategoryId) {
            const setcategoryOption = setcategorySelect.querySelector(`option[value="${currentSetCategoryId}"]`);
            if (setcategoryOption) {
                const setId = setcategoryOption.getAttribute('data-set-id');
                setSelect.value = setId;
                setcategorySelect.value = currentSetCategoryId;
                updateSetCategories();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const baseURL = "{{ asset('') }}";
        const hexSelect = document.getElementById('code');
        const sizeSelect = document.getElementById('size');
        const priceInput = document.getElementById('price');
        const stockInput = document.getElementById('stock');
        const preview = document.getElementById('preview');
        const hexs = @json($product->productHex);

        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(price);
        }

        function updateGallery(galleries) {
            preview.innerHTML = '';

            if (galleries && galleries.length > 0) {
                galleries.forEach(gallery => {
                    const img = document.createElement('img');
                    img.src = baseURL + gallery.image_path;
                    img.classList.add('img-fluid', 'mt-3', 'me-2');
                    img.style.maxWidth = '100px';
                    img.onclick = () => openModal(img.src);
                    preview.appendChild(img);
                });
            }
        }

        function updateSizesForHex(hexId) {
            const selectedHex = hexs.find(hex => hex.id === parseInt(hexId));
            sizeSelect.innerHTML = '';

            if (selectedHex && selectedHex.sizes) {
                selectedHex.sizes.forEach(size => {
                    const option = document.createElement('option');
                    option.value = size.id;
                    option.textContent = size.size;
                    option.dataset.price = size.price;
                    option.dataset.stock = size.stock;
                    sizeSelect.appendChild(option);
                });

                if (selectedHex.sizes.length > 0) {
                    const firstSize = selectedHex.sizes[0];
                    priceInput.value = formatPrice(firstSize.price);
                    stockInput.value = firstSize.stock;
                    document.getElementById('deleteSize').setAttribute('data-size', firstSize.id);
                }

                updateGallery(selectedHex.galleries);
            }
        }

        if (hexs.length > 0) {
            hexSelect.value = hexs[0].id;
            const deleteHexBtn = document.getElementById('deleteHex');
            deleteHexBtn.setAttribute('data-productid', hexs[0].id);
            updateSizesForHex(hexs[0].id);
            deleteHexBtn.onclick = function () {
                deleteHex(deleteHexBtn.dataset.productid);
            }
            document.getElementById('addSize').setAttribute('data-hexid', hexs[0].id);
            const deleteSizebtn = document.getElementById('deleteSize');
            deleteSizebtn.onclick = function () {
                deleteSize(deleteSizebtn.dataset.size);
            }
        }

        hexSelect.addEventListener('change', function () {
            updateSizesForHex(this.value);
            const deleteHexButton = document.getElementById('deleteHex');
            deleteHexButton.setAttribute('data-productid', this.value);

            deleteHexButton.onclick = function () {
                deleteHex(deleteHexButton.dataset.productid);
            }
            document.getElementById('addSize').setAttribute('data-hexid', this.value);
        });

        sizeSelect.addEventListener('change', function () {
            const selectedSize = this.options[this.selectedIndex];
            priceInput.value = formatPrice(selectedSize.dataset.price);
            stockInput.value = selectedSize.dataset.stock;
            const deleteSizeButton = document.getElementById('deleteSize');
            deleteSizeButton.setAttribute('data-size', this.value);
            deleteSizeButton.onclick = function () {
                deleteSize(deleteSizeButton.dataset.size);
            }
        });
        document.getElementById('addSize').addEventListener('click', function () {
            const gethexId = this.getAttribute('data-hexid');
            document.getElementById('addhexID').value = gethexId;
        });
        function deleteHex(hexId) {
            Swal.fire({
                title: "Xác nhận xóa",
                text: "Bạn có chắc muốn xóa mã này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Xóa",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('product.deleteProductHex') }}",
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: { id: hexId },
                        success: function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Xóa thành công",
                                text: "Mã này đã được xóa!",
                                timer: 1500,
                                showConfirmButton: false,
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error(error);
                            Swal.fire({
                                icon: "error",
                                title: "Lỗi",
                                text: xhr.responseJSON?.message || "Có lỗi xảy ra, vui lòng thử lại!",
                            });
                        },
                    });
                }
            });
        }

        function deleteSize(sizeId) {
            Swal.fire({
                title: "Xác nhận xóa",
                text: "Bạn có chắc muốn xóa size này?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Xóa",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('product.deleteProductSize') }}",
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: { id: sizeId },
                        success: function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Xóa thành công",
                                text: "Size này đã được xóa!",
                                timer: 1500,
                                showConfirmButton: false,
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error(error);
                            Swal.fire({
                                icon: "error",
                                title: "Lỗi",
                                text: xhr.responseJSON?.message || "Có lỗi xảy ra, vui lòng thử lại!",
                            });
                        },
                    });
                }
            });
        }


        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-update-product');
        const modal = document.getElementById('image-modal');
        const modalImg = document.getElementById('modal-image');
        const closeBtn = document.getElementsByClassName('close-btn')[0];
        let selectedFiles = [];
        const dragEvents = ['dragenter', 'dragover', 'dragleave', 'drop'];
        const highlightEvents = ['dragenter', 'dragover'];
        const unhighlightEvents = ['dragleave', 'drop'];

        dragEvents.forEach(event => {
            dropZone.addEventListener(event, (e) => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        highlightEvents.forEach(event => {
            dropZone.addEventListener(event, () => dropZone.classList.add('highlight'), false);
        });

        unhighlightEvents.forEach(event => {
            dropZone.addEventListener(event, () => dropZone.classList.remove('highlight'), false);
        });

        dropZone.addEventListener('drop', (e) => handleFiles(e.dataTransfer.files), false);
        dropZone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

        function handleFiles(files) {
            if (!files) return;
            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    selectedFiles.push(file);
                } else {
                    console.log('Vui lòng chọn file ảnh');
                }
            });
            updatePreview();
        }

        function updatePreview() {
    // Xóa tất cả preview hiện tại
    preview.innerHTML = '';
    
    // Tạo preview cho mỗi file
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onloadend = () => {
            const wrapper = document.createElement('div');
            wrapper.classList.add('position-relative', 'd-inline-block', 'me-2', 'mb-2');
            
            const img = document.createElement('img');
            img.src = reader.result;
            img.classList.add('img-fluid', 'mt-3');
            img.style.maxWidth = '100px';
            img.style.cursor = 'pointer';
            img.onclick = () => openModal(img.src);
            
            const deleteBtn = document.createElement('button');
            deleteBtn.innerHTML = '×';
            deleteBtn.classList.add('position-absolute', 'top-0', 'end-0', 'btn', 'btn-danger', 'btn-sm', 'rounded-circle', 'p-0');
            deleteBtn.style.width = '20px';
            deleteBtn.style.height = '20px';
            deleteBtn.style.lineHeight = '16px';
            deleteBtn.style.fontSize = '16px';
            deleteBtn.onclick = (e) => {
                e.stopPropagation();
                removeFile(index);
            };
            
            wrapper.appendChild(img);
            wrapper.appendChild(deleteBtn);
            preview.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
    
    updateFileInput();
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updatePreview();
}

function updateFileInput() {
    const dataTransfer = new DataTransfer();
    
    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });
    
    fileInput.files = dataTransfer.files;
}
        function openModal(imageSrc) {
            modal.style.display = 'flex';
            modalImg.src = imageSrc;
        }

        closeBtn.onclick = () => modal.style.display = 'none';
    });
</script>