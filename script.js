/* loading js*/
console.log("script.js loaded");

/*  PASSWORD TOGGLE - REGISTER */
function togglePass() {
    const pass = document.getElementById("regPass");
    if (pass) {
        pass.type = pass.type === "password" ? "text" : "password";
    }
}

/*PASSWORD TOGGLE - LOGIN */
function toggleLoginPass() {
    const pass = document.getElementById("loginPass");
    if (pass) {
        pass.type = pass.type === "password" ? "text" : "password";
    }
}

/*  DOM READY */
document.addEventListener("DOMContentLoaded", function () {

    /* REGISTER  */
    const registerForm = document.getElementById("registerForm");

    if (registerForm) {
        registerForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const name = registerForm.querySelector("input[type='text']").value.trim();
            const email = registerForm.querySelector("input[type='email']").value.trim();
            const password = document.getElementById("regPass").value.trim();

            if (name === "" || email === "" || password === "") {
                alert("All fields are required!");
                return;
            }

            if (!email.includes("@")) {
                alert("Invalid email address!");
                return;
            }

            if (password.length < 6) {
                alert("Password must be at least 6 characters!");
                return;
            }

            alert("Registration successful! Redirecting to login...");
            window.location.href = "login.html";
        });
    }

    /*  LOGIN  */
    const loginForm = document.getElementById("loginForm");

    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const email = loginForm.querySelector("input[type='email']").value.trim();
            const password = document.getElementById("loginPass").value.trim();

            if (email === "" || password === "") {
                alert("Email and password required!");
                return;
            }

            if (!email.includes("@")) {
                alert("Invalid email!");
                return;
            }

            alert("Login successful!");
            // future redirect
            // window.location.href = "dashboard.html";
        });
    }

    /* RESET PASSWORD  */
    const resetBtn = document.getElementById("resetBtn");

    if (resetBtn) {
        resetBtn.addEventListener("click", function () {

            const emailInput = document.getElementById("resetEmail");
            const email = emailInput.value.trim();

            if (email === "") {
                alert("Please enter your email!");
                return;
            }

            if (!email.includes("@")) {
                alert("Invalid email address!");
                return;
            }

            alert("Reset link sent! Redirecting to login...");
            window.location.href = "login.html";
        });
    }

});
