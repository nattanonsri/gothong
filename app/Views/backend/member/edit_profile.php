<?= $this->extend('front/template') ?>
<?= $this->section('content') ?>

<div class="main-content" id="mainContent">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="dashboard-title">
                    <i class="fa-solid fa-user-edit me-2"></i>
                    แก้ไขข้อมูลส่วนตัว
                </h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="<?= base_url('backend/profile') ?>" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    กลับ
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-user-pen me-2"></i>
                        ข้อมูลส่วนตัว
                    </h5>
                </div>
                <div class="card-body">
                    <form id="editProfileForm" enctype="multipart/form-data">
                        <div class="row">
                            <!-- รูปโปรไฟล์ -->
                            <div class="col-12 mb-4">
                                <div class="text-center">
                                    <div class="profile-image-container d-flex justify-content-center mb-3">
                                        <?php if (!empty($user['image_profile'])): ?>
                                            <img id="profileImagePreview" src="<?= base_url('backend/profile/image/' . $user['image_profile']) ?>"
                                                alt="Profile Image" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                        <?php else: ?>
                                            <div id="profileImagePreview" class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                                <i class="fa-solid fa-user text-white" style="font-size: 60px;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" id="image_profile" name="image_profile" accept="image/*" class="form-control" style="display: none;">
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('image_profile').click()">
                                        <i class="fa-solid fa-camera me-2"></i>
                                        เปลี่ยนรูปโปรไฟล์
                                    </button>
                                </div>
                            </div>

                            <!-- ข้อมูลภาษาไทย -->
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fa-solid fa-language me-2"></i>
                                    ข้อมูลภาษาไทย
                                </h6>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="perfix_th" class="form-label">คำนำหน้า (ไทย) <span class="text-danger">*</span></label>
                                <select class="form-select" id="perfix_th" name="perfix_th" required>
                                    <option value="">เลือกคำนำหน้า</option>
                                    <option value="นาย" <?= ($user['perfix_th'] == 'นาย') ? 'selected' : '' ?>>นาย</option>
                                    <option value="นาง" <?= ($user['perfix_th'] == 'นาง') ? 'selected' : '' ?>>นาง</option>
                                    <option value="นางสาว" <?= ($user['perfix_th'] == 'นางสาว') ? 'selected' : '' ?>>นางสาว</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="first_name_th" class="form-label">ชื่อ (ไทย) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name_th" name="first_name_th" oninput="this.value = this.value.replace(/[^\u0E00-\u0E7F]/g, '')" value="<?= $user['first_name_th'] ?? '' ?>" required>
                            </div>

                            <div class="col-md-5 mb-3">
                                <label for="last_name_th" class="form-label">นามสกุล (ไทย) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name_th" name="last_name_th" oninput="this.value = this.value.replace(/[^\u0E00-\u0E7F]/g, '')" value="<?= $user['last_name_th'] ?? '' ?>" required>
                            </div>

                            <!-- ข้อมูลภาษาอังกฤษ -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fa-solid fa-globe me-2"></i>
                                    ข้อมูลภาษาอังกฤษ
                                </h6>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="perfix_en" class="form-label">คำนำหน้า (อังกฤษ) <span class="text-danger">*</span></label>
                                <select class="form-select" id="perfix_en" name="perfix_en" required>
                                    <option value="">เลือกคำนำหน้า</option>
                                    <option value="Mr." <?= ($user['perfix_en'] == 'Mr.') ? 'selected' : '' ?>>Mr.</option>
                                    <option value="Mrs." <?= ($user['perfix_en'] == 'Mrs.') ? 'selected' : '' ?>>Mrs.</option>
                                    <option value="Miss" <?= ($user['perfix_en'] == 'Miss') ? 'selected' : '' ?>>Miss</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="first_name_en" class="form-label">ชื่อ (อังกฤษ) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name_en" name="first_name_en"
                                    oninput="this.value = this.value.replace(/[^\w\s\-]/g, '')"
                                    value="<?= $user['first_name_en'] ?? '' ?>" required>
                            </div>

                            <div class="col-md-5 mb-3">
                                <label for="last_name_en" class="form-label">นามสกุล (อังกฤษ) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name_en" name="last_name_en"
                                    oninput="this.value = this.value.replace(/[^\w\s\-]/g, '')"
                                    value="<?= $user['last_name_en'] ?? '' ?>" required>
                            </div>

                            <!-- ข้อมูลติดต่อ -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fa-solid fa-address-book me-2"></i>
                                    ข้อมูลติดต่อ
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">อีเมล (อังกฤษ) <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?? '' ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="<?= $user['phone'] ?? '' ?>" required>
                            </div>

                            <!-- ข้อมูลส่วนตัว -->
                            <div class="col-12 mt-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fa-solid fa-user me-2"></i>
                                    ข้อมูลส่วนตัว
                                </h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">เพศ <span class="text-danger">*</span></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">เลือกเพศ</option>
                                    <option value="male" <?= ($user['gender'] == 'male') ? 'selected' : '' ?>>ชาย</option>
                                    <option value="female" <?= ($user['gender'] == 'female') ? 'selected' : '' ?>>หญิง</option>
                                    <option value="other" <?= ($user['gender'] == 'other') ? 'selected' : '' ?>>อื่นๆ</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="birth_date" class="form-label">วันเกิด <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date"
                                    value="<?= $user['birth_date'] ?? '' ?>" required>
                            </div>

                            <div class="col-12 mt-4"></div>
                                <h6 class="text-primary mb-3">
                                    <i class="fa-solid fa-circle-check me-2"></i>
                                    องค์กร
                                </h6>
                                <!-- <div class="form-check">
                                    <select class="form-select" id="organization_id" name="organization_id" required>
                                        <option value="">เลือกองค์กร</option>
                                        <?php foreach ($organizations as $organization): ?>
                                            <option value="<?= $organization['id'] ?>" <?= ($user['organization_id'] == $organization['id']) ? 'selected' : '' ?>><?= $organization['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div> -->
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="button" class="btn btn-secondary me-2" onclick="window.location.href='<?= base_url('backend/profile') ?>'">
                                    <i class="fa-solid fa-times me-2"></i>
                                    ยกเลิก
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save me-2"></i>
                                    บันทึกข้อมูล
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {
        // Preview image when file is selected
        $('#image_profile').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // ถ้าเป็น div ให้เปลี่ยนเป็น img
                    if ($('#profileImagePreview').is('div')) {
                        $('#profileImagePreview').replaceWith('<img id="profileImagePreview" src="' + e.target.result + '" alt="Profile Image" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">');
                    } else {
                        $('#profileImagePreview').attr('src', e.target.result);
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        $('#editProfileForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Show loading
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>กำลังบันทึก...');

            $.ajax({
                url: '<?= base_url('backend/profile/update') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: response.message,
                            confirmButtonText: 'ตกลง'
                        }).then(() => {
                            window.location.href = '<?= base_url('backend/profile') ?>';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: response.message,
                            confirmButtonText: 'ตกลง'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: errorMessage,
                        confirmButtonText: 'ตกลง'
                    });
                },
                complete: function() {
                    // Reset button
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>