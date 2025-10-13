var cleanInvalidFeedback = () => {
    var feedback = document.querySelectorAll(".invalid-feedback");
    if (feedback) {
        feedback.forEach(function(element) {
            element.style.display = 'none';
        });
    }
}


// reload_captcha()

function reload_captcha() {
    grecaptcha.ready(function() {
        grecaptcha.execute(RECAPTCHA_SITEKEY, { action: 'validate_captcha' })
            .then(function(token) {
                if (document.getElementById('g-recaptcha-response')) {
                    document.getElementById('g-recaptcha-response').value = token;
                }
            });
    });
}



function buttonLoading(elem) {
    $(elem).attr("data-original-text", $(elem).html());
    $(elem).prop("disabled", true);
    $(elem).html('<i class="fa fa-spinner fa-spin"></i> รอสักครู่...');
}

function buttonReset(elem) {
    $(elem).prop("disabled", false);
    $(elem).html($(elem).attr("data-original-text"));
}


function updateButtonState(countdownElementId, requestBtn) {
    const countdownElement = document.getElementById(countdownElementId);
    const requestOTPButton = document.getElementById(requestBtn);
    if (countdownElement.textContent === '00:00') {
        requestOTPButton.disabled = false;
    } else {
        requestOTPButton.disabled = true;
        return false;
    }
}

function startCountdown(targetDateTime, countdownElementId, requestBtn) {
    const targetDateTimeObj = new Date(targetDateTime);
    const countdownElement = document.getElementById(countdownElementId);

    if (!targetDateTimeObj || !countdownElement) {
        console.error('Invalid targetDateTime or countdownElementId');
        return;
    }

    const countdownInterval = setInterval(updateCountdown, 1000);

    function updateCountdown() {
        const currentDateTime = new Date();
        const remainingTime = targetDateTimeObj - currentDateTime;

        const frmRequest = document.getElementById('frmRequest');
        if (remainingTime <= 0) {
            clearInterval(countdownInterval);
            countdownElement.textContent = '00:00';
            updateButtonState(countdownElementId, requestBtn)
            if (frmRequest) {
                reload_captcha_request();
                frmRequest.style.display = '';
            }
            return;
        } else {
            if (frmRequest) {
                frmRequest.style.display = 'none';
            }
        }

        const minutes = Math.floor(remainingTime / 60000);
        const seconds = Math.floor((remainingTime % 60000) / 1000);

        countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
}

function limitInputTextLength(inputElement, maxLength) {
    inputElement.addEventListener('input', function() {
        if (inputElement.value.length > maxLength) {
            inputElement.value = inputElement.value.slice(0, maxLength);
        }
    });
}

function limitTextareaLength(textareaElement, maxLength) {
    textareaElement.addEventListener('input', function() {
        if (textareaElement.value.length > maxLength) {
            textareaElement.value = textareaElement.value.slice(0, maxLength);
        }
    });
}

function toggleFormElements(formId, disable) {
    $(`#${formId} :input`).prop('disabled', disable);
}

const units = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

function niceBytes(x) {
    let l = 0,
        n = parseInt(x, 10) || 0;
    while (n >= 1024 && ++l) {
        n = n / 1024;
    }
    console.log(n)
    return (n.toFixed(n < 10 && l > 0 ? 1 : 0) + ' ' + units[l]);
}

function inputNumberOnlyByClass(className) {

    const inputElements = document.querySelectorAll(`.${className}`);

    inputElements.forEach(inputElement => {
        const maxLength = inputElement.dataset.maxlength;
        console.log(maxLength)
        inputElement.addEventListener('input', function() {
            // Remove any non-numeric characters using a regular expression
            this.value = this.value.replace(/\D/g, '');

            // Limit the length of the input
            if (this.value.length > maxLength) {
                this.value = this.value.slice(0, maxLength);
            }
        });
    });
}

function validateEmailFormatByClassName(className) {
    const inputElements = document.querySelectorAll(`.${className}`);

    inputElements.forEach(inputElement => {
        if (inputElement.type === 'text') {
            inputElement.addEventListener('input', function() {
                const isEnglish = /^[A-Za-z0-9@._%+-]*$/.test(this.value);
                const emailRegex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

                if (isEnglish) {
                    this.classList.remove('is-invalid');
                    const isValidEmail = emailRegex.test(this.value);
                    if (isValidEmail) {
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                    }
                } else {
                    this.value = this.value.replace(/[^A-Za-z0-9@._%+-]/g, '');
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }

                // Limit the length of the input
                const maxLength = 50; // Change this value to your desired maximum length
                if (this.value.length > maxLength) {
                    this.value = this.value.slice(0, maxLength);
                }
            });
        }
    });
}


function validateAndLimitEnglishByClassName(className) {
    const inputElements = document.querySelectorAll(`.${className}`);

    inputElements.forEach(inputElement => {
        inputElement.addEventListener('input', function() {
            // Remove Thai characters using a regular expression
            if (/[\u0E00-\u0E7F]/.test(this.value)) {
                this.value = this.value.replace(/[\u0E00-\u0E7F]/g, '');
            }
            // Limit the length of the input
            const maxLength = 50; // Change this value to your desired maximum length
            if (this.value.length > maxLength) {
                this.value = this.value.slice(0, maxLength);
            }
        });
    });
}

function hideErrorMessage(inputElement) {

    const errorElement = document.getElementById(inputElement.id + '_error');
    errorElement.style.display = 'none';
}

function randomPuzzleCaptcha(base_url) {
    return base_url + 'assets/img/puzzle/Pic' + Math.round(Math.random() * 4) + '.jpg';
}

var monthLongTH = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
var monthShortTH = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
var monthLongEN = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var monthShortEN = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

function ConvertDateInputBirthday(input_date) {
    // วัน-เดือน-ปีเกิด พ.ศ. ตัวอย่าง (12-05-2531)

    var split = input_date.split('/');
    var arr_month = monthShortTH;
    var y = parseInt(split[2]);
    y = y - 543;
    var m = parseInt(split[1]);

    var d = parseInt(split[0]);
    return new Date(y, (m - 1), d);

}