$(document).ready(function () {
    $("#formAuthentication").on("submit", function (event) {
        event.preventDefault(); // stop normal form submit

        // Clear old error messages
        $(".error-message").remove();

        const form = this;
        const url = $(form).attr("action");
        const email = $("#email").val().trim();
        const password = $("#password").val().trim();
        let isValid = true;

        // --- Validation ---
        if (email === "") {
            $("#email").after('<span class="error-message" style="color: red;">Type Email!</span>');
            isValid = false;
        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                $("#email").after('<span class="error-message" style="color: red;">Enter a valid Email!</span>');
                isValid = false;
            }
        }

        if (password === "") {
            $("#password-error").after('<span class="error-message" style="color: red;">Enter Password!</span>');
            isValid = false;
        }

        if (!isValid) {
            return; // stop here if validation failed
        }

        // --- Submit via AJAX ---
        var formData = new FormData(form);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    // success → redirect
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        location.reload(); // fallback
                    }
                } else if (response.message === "pending") {
                    // pending → show block message
                    $(".authentication-inner").hide();
                    $("#pending-message").show();
                } else {
                    // invalid login
                    $("#password-error").after(
                        '<span class="error-message" style="color: red;">' + response.message + "</span>"
                    );
                }
            },
            error: function (error) {
                console.error("Error submitting the form:", error);
                alert("Something went wrong. Please try again.");
            },
            complete: function (response) {
                console.log("Request complete:", response);
            }
        });
    });

    // Back to login button
    $("#back-home").on("click", function () {
        $("#pending-message").hide();
        $(".authentication-inner").show();
    });
});
