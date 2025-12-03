// main.js

document.addEventListener("DOMContentLoaded", () => {
    const yearSpan = document.getElementById("year");
    if (yearSpan) {
        yearSpan.textContent = new Date().getFullYear().toString();
    }

    const loginForm = document.getElementById("loginForm");
    if (loginForm instanceof HTMLFormElement) {
        setupLoginForm(loginForm);
    }

    const studentRegisterForm = document.getElementById("studentRegisterForm");
    if (studentRegisterForm instanceof HTMLFormElement) {
        setupStudentRegisterForm(studentRegisterForm);
    }
});

/**
 * Attach validation logic to the login form.
 * @param {HTMLFormElement} form
 */
function setupLoginForm(form) {
    const emailInput = /** @type {HTMLInputElement | null} */ (
        form.querySelector("#email")
    );
    const passwordInput = /** @type {HTMLInputElement | null} */ (
        form.querySelector("#password")
    );
    const roleSelect = /** @type {HTMLSelectElement | null} */ (
        form.querySelector("#role")
    );

    if (!emailInput || !passwordInput || !roleSelect) {
        return;
    }

    form.addEventListener("submit", (event) => {
        const isValid = validateLoginForm(emailInput, passwordInput, roleSelect);
        if (!isValid) {
            event.preventDefault();
        }
    });
}

/**
 * Validate login form fields and show inline errors.
 * @param {HTMLInputElement} emailInput
 * @param {HTMLInputElement} passwordInput
 * @param {HTMLSelectElement} roleSelect
 * @returns {boolean}
 */
function validateLoginForm(emailInput, passwordInput, roleSelect) {
    let valid = true;

    const setError = (inputEl, message) => {
        const fieldEl = inputEl.closest(".form__field");
        if (!fieldEl) return;

        const errorEl = fieldEl.querySelector(".form__error");
        if (!errorEl) return;

        if (message) {
            fieldEl.classList.add("form__field--error");
            errorEl.textContent = message;
        } else {
            fieldEl.classList.remove("form__field--error");
            errorEl.textContent = "";
        }
    };

    // Email
    if (!emailInput.value.trim()) {
        setError(emailInput, "Email is required.");
        valid = false;
    } else if (!emailInput.checkValidity()) {
        setError(emailInput, "Please enter a valid email address.");
        valid = false;
    } else {
        setError(emailInput, "");
    }

    // Password
    const passwordValue = passwordInput.value;
    if (!passwordValue) {
        setError(passwordInput, "Password is required.");
        valid = false;
    } else if (passwordValue.length < 6) {
        setError(passwordInput, "Password must be at least 6 characters.");
        valid = false;
    } else {
        setError(passwordInput, "");
    }

    // Role
    if (!roleSelect.value) {
        setError(roleSelect, "Please select an account type.");
        valid = false;
    } else {
        setError(roleSelect, "");
    }

    return valid;
}

/**
 * Attach validation logic to the student registration form.
 * @param {HTMLFormElement} form
 */
function setupStudentRegisterForm(form) {
    const nameInput = /** @type {HTMLInputElement | null} */ (
        form.querySelector("#name")
    );
    const emailInput = /** @type {HTMLInputElement | null} */ (
        form.querySelector("#email")
    );
    const passwordInput = /** @type {HTMLInputElement | null} */ (
        form.querySelector("#password")
    );
    const confirmInput = /** @type {HTMLInputElement | null} */ (
        form.querySelector("#password_confirm")
    );
    const cvInput = /** @type {HTMLInputElement | null} */ (
        form.querySelector("#cv_file")
    );

    if (!nameInput || !emailInput || !passwordInput || !confirmInput) {
        return;
    }

    form.addEventListener("submit", (event) => {
        const isValid = validateStudentRegisterForm(
            nameInput,
            emailInput,
            passwordInput,
            confirmInput,
            cvInput
        );
        if (!isValid) {
            event.preventDefault();
        }
    });
}

/**
 * Validate student registration fields and show inline errors.
 * @param {HTMLInputElement} nameInput
 * @param {HTMLInputElement} emailInput
 * @param {HTMLInputElement} passwordInput
 * @param {HTMLInputElement} confirmInput
 * @param {HTMLInputElement | null} cvInput
 * @returns {boolean}
 */
function validateStudentRegisterForm(
    nameInput,
    emailInput,
    passwordInput,
    confirmInput,
    cvInput
) {
    let valid = true;

    const setError = (inputEl, message) => {
        if (!inputEl) return;
        const fieldEl = inputEl.closest(".form__field");
        if (!fieldEl) return;

        const errorEl = fieldEl.querySelector(".form__error");
        if (!errorEl) return;

        if (message) {
            fieldEl.classList.add("form__field--error");
            errorEl.textContent = message;
        } else {
            fieldEl.classList.remove("form__field--error");
            errorEl.textContent = "";
        }
    };

    // Name
    if (!nameInput.value.trim()) {
        setError(nameInput, "Full name is required.");
        valid = false;
    } else {
        setError(nameInput, "");
    }

    // Email
    if (!emailInput.value.trim()) {
        setError(emailInput, "Email is required.");
        valid = false;
    } else if (!emailInput.checkValidity()) {
        setError(emailInput, "Please enter a valid email address.");
        valid = false;
    } else {
        setError(emailInput, "");
    }

    // Password
    const password = passwordInput.value;
    if (!password) {
        setError(passwordInput, "Password is required.");
        valid = false;
    } else if (password.length < 6) {
        setError(passwordInput, "Password must be at least 6 characters.");
        valid = false;
    } else {
        setError(passwordInput, "");
    }

    // Confirm password
    const confirm = confirmInput.value;
    if (!confirm) {
        setError(confirmInput, "Please confirm your password.");
        valid = false;
    } else if (confirm !== password) {
        setError(confirmInput, "Passwords do not match.");
        valid = false;
    } else {
        setError(confirmInput, "");
    }

    // CV (PDF only, optional but must be PDF if chosen)
    if (cvInput && cvInput.files && cvInput.files.length > 0) {
        const file = cvInput.files[0];
        const fileName = file.name.toLowerCase();
        const mimeType = file.type;

        const isPdfByExt = fileName.endsWith(".pdf");
        const isPdfByMime = mimeType === "application/pdf";

        if (!isPdfByExt && !isPdfByMime) {
            setError(cvInput, "Please upload a PDF file.");
            valid = false;
        } else {
            setError(cvInput, "");
        }
    } else {
        // No file selected – okay for now, but clear any previous error
        setError(cvInput, "");
    }

    return valid;
}
