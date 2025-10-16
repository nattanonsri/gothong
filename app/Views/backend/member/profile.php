<?= $this->extend('front/template') ?>
<?= $this->section('content') ?>

<div class="main-content" id="mainContent">
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="dashboard-title">
                    <i class="fa-solid fa-user me-2"></i>
                    <?= lang('backend.profile') ?>
                </h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="<?= base_url('backend/profile/edit') ?>" class="btn btn-primary">
                    <i class="fa-solid fa-edit me-2"></i>
                    แก้ไขข้อมูล
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-user me-2"></i>
                        ข้อมูลส่วนตัว
                    </h5>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <div class="profile-image-container">
                                <?php if (!empty($user['image_profile'])): ?>
                                    <img src="<?= base_url('backend/profile/image/' . $user['image_profile']) ?>" 
                                         alt="Profile Image" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                        <i class="fa-solid fa-user text-white" style="font-size: 60px;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label class="form-label text-muted">ชื่อผู้ใช้</label>
                                    <p class="form-control-plaintext fw-bold"><?= $user['username'] ?? '-' ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label text-muted">อีเมล</label>
                                    <p class="form-control-plaintext"><?= $user['email'] ?? '-' ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label text-muted">เบอร์โทรศัพท์</label>
                                    <p class="form-control-plaintext"><?= $user['phone'] ?? '-' ?></p>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-sm-4">
                                    <label class="form-label text-muted">ชื่อ-นามสกุล (ไทย)</label>
                                    <p class="form-control-plaintext"><?= ($user['perfix_th'] ?? '') . ' ' . ($user['first_name_th'] ?? '') . ' ' . ($user['last_name_th'] ?? '') ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label text-muted">ชื่อ-นามสกุล (อังกฤษ)</label>
                                    <p class="form-control-plaintext"><?= ($user['perfix_en'] ?? '') . ' ' . ($user['first_name_en'] ?? '') . ' ' . ($user['last_name_en'] ?? '') ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label text-muted">เพศ</label>
                                    <p class="form-control-plaintext">
                                        <?php
                                        $gender = $user['gender'] ?? '';
                                        switch($gender) {
                                            case 'male': echo 'ชาย'; break;
                                            case 'female': echo 'หญิง'; break;
                                            case 'other': echo 'อื่นๆ'; break;
                                            default: echo '-'; break;
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <label class="form-label text-muted">วันเกิด</label>
                                    <p class="form-control-plaintext"><?= !empty($user['birth_date']) ? date('d/m/Y', strtotime($user['birth_date'])) : '-' ?></p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-muted">วันที่สร้างบัญชี</label>
                                    <p class="form-control-plaintext"><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></p>
                                </div>
                            </div>
                            
                            <!-- <div class="row mt-3">
                                <div class="col-sm-6">
                                    <label class="form-label text-muted">องค์กร</label>
                                    <p class="form-control-plaintext"><?= $organization ? $organization['name'] : '-' ?></p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-muted">บทบาท</label>
                                    <p class="form-control-plaintext"><?= $role ? $role['name'] : '-' ?></p>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-chart-line me-2"></i>
                        สถิติการใช้งาน
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">อัปเดตล่าสุด</span>
                        <span class="fw-bold"><?= date('d/m/Y H:i', strtotime($user['updated_at'])) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">สถานะ</span>
                        <span class="badge bg-success">ใช้งานได้</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>