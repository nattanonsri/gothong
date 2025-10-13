<form id="frmEditItem">
    <?= csrf_field() ?>
    <div class="row">
        <input type="hidden" id="edit-uuid" name="uuid" value="<?= $uuid ?>">
        <div class="col-12 mb-2">
            <label for="edit-name" class="fs-5">ชื่อสินค้า</label>
            <input type="text" id="edit-name" class="form-control" name="name" value="<?= $name ?>"
                placeholder="กรุณากรอกชื่อสินค้า" />
        </div>

        <div class="col-6 mb-2">
            <label for="edit-category" class="fs-5">หมวดหมู่</label>
            <select id="cat-select" class="form-select" name="cat_uuid">
                <?php
                foreach ($categorys as $cat) { ?>
                    <option value="<?= $cat['uuid']  ?>" <?= $cat['id'] == $cat_id ? 'selected' : '' ?>> <?= $cat['name'] ?> </option>
                <?php }
                ?>
            </select>
        </div>
        <div class="col-6 mb-2">
            <label for="edit-brand" class="fs-5">แบรนด์</label>
            <select id="brand-select" class="form-select" name="brand_uuid">
                <?php
                foreach ($brands as $brand) {
                ?>
                    <option value="<?= $brand['uuid'] ?>" <?= $brand['id'] == $brand_id ? 'selected' : '' ?>> <?= $brand['name'] ?> </option>
                <?php }
                ?>
            </select>
        </div>

        <div class="col ">
            <label for="edit-quantity" class="fs-5">จำนวน</label>
            <input type="text" id="edit-quantity" class="form-control" name="quantity" value="<?= $quantity ?>"
                placeholder="กรุณากรอกจำนวน" />
        </div>
        <div class="col">
            <label for="edit-price" class="fs-5">ราคา</label>
            <input type="text" id="edit-price" class="form-control" name="price" value="<?= $price ?>"
                placeholder="กรุณากรอกราคา" />
        </div>
    </div>
    <div class="text-center mt-4">
        <button type="button" class="btn btn-outline-secondary md-0" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-warning" id="btnEdit">แก้ไขข้อมูล</button>
    </div>
</form>

<script>
    //    $("#cat-select").select2()

    $('#frmEditItem').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: `${base_url}backend/editItem`,
            type: 'POST',
            data: $('#frmEditItem').serialize(),
            headers: {
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            },
            beforeSend: function() {
                buttonLoading('#btnEdit');
            },
            success: function(data) {
                buttonReset('#btnEdit');
                if (data.status === 200) {
                    $('#editModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'แจ้งเตือน',
                        text: data.message,
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#FF7300',
                    }).then(() => {
                        window.location.href = `${base_url}backend/item`;
                    });
                }
            },
            error: function(jqXHR) {
                buttonReset('#btnEdit');
                if (jqXHR.responseJSON && jqXHR.responseJSON.status === 400) {
                    $('#editModal').modal('hide');
                    Swal.fire({
                        icon: 'warning',
                        title: 'แจ้งเตือน',
                        text: jqXHR.responseJSON.message,
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#FF7300',
                    });
                }
            }
        });
    });

</script>