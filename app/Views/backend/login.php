<?= $this->extend('front/template') ?>

<?= $this->section('content') ?>
<!-- Content -->

<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="d-flex justify-content-center align-items-center min-vh-100">
      <!-- login -->
      <div class="card-admin px-sm-5 px-4 py-4 position-absolute top-50 start-50 translate-middle">
        <div class="card-body p-0">

          <div class="app-brand text-center mb-4">
            <img class="img-fluid" src="<?php echo asset_url('assets/icons/logo.png') ?>" alt="logo" style="max-width: 75%;" />
          </div>

          <form id="login_form">
            <div class="row g-3">
              <div class="col-12">
                <? csrf_field(); ?>
                <label for="username" class="form-label fw-semibold">Username</label>
                <input type="text" id="username" name="username" class="form-control form-control-lg username group-text" placeholder="Username" required>
              </div>
              <div class="col-12">
                <label for="password" class="form-label fw-semibold">Password</label>
                <div class="input-group input-group-lg">
                  <input type="password" id="password" name="password" class="form-control group-text" placeholder="Password" required>
                  <button class="btn btn-outline-secondary group-text" type="button" onclick="togglePassword('password')">
                    <i class="fa-regular fa-eye-slash" id="passwordIcon"></i>
                  </button>
                </div>
              </div>
              <div class="col-12 text-center mt-4">
                <button type="submit" class="btn btn-gradient btn-lg px-5" id="btnLogin">
                  <span class="btn-text">Login</span>
                  <span class="btn-loading d-none">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Loading...
                  </span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .card-admin {
    width: 100%;
    max-width: 650px;
    background: rgb(250, 250, 250);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .group-text {
    border: none;
    box-shadow: 0 4px 12px rgba(149, 157, 165, 0.15);
    transition: all 0.3s ease;
  }

  .group-text:focus {
    box-shadow: 0 4px 20px rgba(149, 157, 165, 0.25);
    transform: translateY(-1px);
  }

  .form-control {
    border-radius: 8px;
  }

  .input-group .btn {
    border-radius: 0 8px 8px 0;
    border-left: none;
  }

  .btn-gradient {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  }

  .form-label {
    color: #495057;
    margin-bottom: 0.5rem;
  }

  .card-body {
    padding: 2rem;
  }

  @media (max-width: 576px) {
    .card-admin {
      margin: 1rem;
      width: calc(100% - 2rem);
    }
    
    .card-body {
      padding: 1.5rem;
    }
  }
</style>

<script>
  function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById('passwordIcon');
    
    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    } else {
      input.type = "password";
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    }
  }

  function buttonLoading(buttonSelector) {
    const button = $(buttonSelector);
    button.prop('disabled', true);
    button.find('.btn-text').addClass('d-none');
    button.find('.btn-loading').removeClass('d-none');
  }

  function buttonReset(buttonSelector) {
    const button = $(buttonSelector);
    button.prop('disabled', false);
    button.find('.btn-text').removeClass('d-none');
    button.find('.btn-loading').addClass('d-none');
  }

  $('#login_form').on('submit', function(e) {
    e.preventDefault();

    var username = $('#username').val().trim();
    var password = $('#password').val().trim();

    if (!username || !password) {
      Swal.fire({
        icon: 'warning',
        title: 'แจ้งเตือน',
        text: 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#FF7300',
      });
      return;
    }
    
    $.ajax({
      url: `${base_url}backend/adminAuth`,
      type: 'POST',
      data: $('#login_form').serialize(),
      datatype: 'JSON',
      beforeSend: function() {
        buttonLoading('#btnLogin');
      },
      success: function(data) {
        buttonReset('#btnLogin');
        if (data.status == 200) {
          window.location.href = data.redirect;
        }
      },
      error: function(jqXHR, status, error) {
        buttonReset('#btnLogin');
        if (jqXHR.responseJSON && jqXHR.responseJSON.status == 400) {
          Swal.fire({
            icon: 'error',
            title: 'ข้อผิดพลาด',
            text: jqXHR.responseJSON?.message,
            confirmButtonText: 'ตกลง',
            confirmButtonColor: '#FF7300',
          });
          return;
        }
      }
    });
  });

  // Add form validation styling
  $(document).ready(function() {
    $('.form-control').on('blur', function() {
      if ($(this).val().trim() === '') {
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid').addClass('is-valid');
      }
    });

    $('.form-control').on('input', function() {
      if ($(this).val().trim() !== '') {
        $(this).removeClass('is-invalid');
      }
    });
  });
</script>

<?= $this->include('backend/_css') ?>
<?= $this->include('backend/_js') ?>

<?= $this->endSection() ?>