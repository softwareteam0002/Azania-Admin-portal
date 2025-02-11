$('#reload').click(function () {
    $.ajax({
        type: 'GET',
        url: 'reload-captcha',
        success: function (data) {
            $(".captcha span").html(data.captcha);
        }
    });
});
$("body").on('click', '.toggle-password', function () {
    $(this).toggleClass("fa-eye fa-eye-slash");

    var input = $("#password").attr("type");
    if (input === "password") {
        $("#password").attr("type", "text");
    } else {
        $("#password").attr("type", "password");
    }
});

document.querySelectorAll('.toggle-password').forEach(item => {
    item.addEventListener('click', function () {
        const passwordField = document.querySelector(this.getAttribute('toggle'));
        const icon = this;

        // Toggle password field visibility
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});

$(document).ready(function () {
    // Fade out the alert after 4 seconds (4000 milliseconds)
    $('.alert').delay(4000).fadeOut(500);
});

document.querySelectorAll(".otp-box").forEach((box, index, boxes) => {
    box.addEventListener("keydown", function (e) {
        if (e.key === "Backspace" && this.value === "") {
            const prev = boxes[index - 1];
            if (prev && prev.classList.contains("otp-box")) {
                prev.focus();
            }
        }
    });
});

function isNumberKey(evt) {
    var charCode = evt.which ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault();
    }
}

function moveToNext(input, event) {
    if (input.nextElementSibling && input.value.length >= input.maxLength) {
        input.nextElementSibling.focus();
    }
}

function startTimer(duration, display) {
    let timer = duration,
        minutes,
        seconds;
    setInterval(function () {
        if (!display) return; // Check if display is not null
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        display.textContent = minutes + ":" + seconds;
        if (--timer < 0) {
            timer = duration;
        }
    }, 1000);
}

window.onload = function () {
    const fiveMinutes = 60 * 5,
        display = document.querySelector("#timer");
    startTimer(fiveMinutes, display);
};
