
function validateMobile() {
    const mobileInput = document.getElementById('mobileInput');
    const mobileError = document.getElementById('mobileError');
    const mobileValue = mobileInput.value;
    // Regular expression to check if it starts with 0 followed by 7-9
    const regex = /^0[7-9][0-9]{8}$/;
    // Check if the value matches the regex
    if (mobileValue.length > 0 && !regex.test(mobileValue)) {
        mobileError.textContent = "Mobile not valid";
        mobileInput.classList.add("is-invalid");
    } else {
        mobileError.textContent = ""; // Clear error message
        mobileInput.classList.remove("is-invalid");
    }

    // Remove non-numeric characters and update the input
    mobileInput.value = mobileValue.replace(/[^0-9]/g, '');

    // Validate the updated mobile value
    if (mobileInput.value.length > 0 && mobileInput.value[0] !== '0') {
        mobileError.textContent = "Mobile must start with 0."; // Set error message
        mobileInput.classList.add("is-invalid");
    } else if (mobileInput.value[0] === '0') {
        mobileError.textContent = ""; // Clear error message
        mobileInput.classList.remove("is-invalid");
    }
}


function validateEmail() {
    const emailInput = document.getElementById('emailInput');
    const emailError = document.getElementById('emailError');
    const emailValue = emailInput.value;
    // Regular expression for basic email validation
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // Check if the value matches the regex
    if (emailValue.length > 0 && !regex.test(emailValue)) {
        emailError.textContent = "Please enter a valid email address.";
        emailInput.classList.add("is-invalid");
    } else {
        emailError.textContent = ""; // Clear error message
        emailInput.classList.remove("is-invalid");
    }
}

function validateName(input) {
    const nameError = document.getElementById("nameError");
    const value = input.value;
    // Check if the first character is a number
    if (value.length > 0 && !isNaN(value[0])) {
        nameError.textContent = "The first character cannot be a number.";
        input.classList.add("is-invalid"); // Add Bootstrap invalid class
    } else {
        nameError.textContent = ""; // Clear error message
        input.classList.remove("is-invalid"); // Remove Bootstrap invalid class
    }
}

function validatePassword() {
    const passwordInput = document.querySelector('input[name="password"]');
    const passwordError = document.getElementById('passwordError');
    const passwordValue = passwordInput.value;
    // Regular expression to check password strength
    const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=!]).{8,}$/;
    // Check if the password matches the strength criteria
    if (passwordValue.length > 0 && !strongPasswordRegex.test(passwordValue)) {
        passwordError.textContent = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
        passwordInput.classList.add("is-invalid"); // Add Bootstrap invalid class
    } else {
        passwordError.textContent = ""; // Clear error message
        passwordInput.classList.remove("is-invalid"); // Remove Bootstrap invalid class
    }
}

function validateNotifyField(input) {
    const notifyError = document.getElementById("notifyError");
    const value = input.value;

    // Remove non-numeric characters (except decimal points)
    const cleanedValue = value.replace(/[^0-9.]/g, '');
    input.value = cleanedValue;

    // Check if the cleaned value is empty or starts with a decimal
    if (cleanedValue.length === 0) {
        notifyError.textContent = "Invalid notification threshold.";
        input.classList.add("is-invalid");
    } else if (cleanedValue[0] === '.') {
        notifyError.textContent = "The first character must be a number.";
        input.classList.add("is-invalid");
    } else {
        // Split on decimal to ensure only one decimal point
        const parts = cleanedValue.split('.');
        if (parts.length > 2) {
            notifyError.textContent = "Invalid input. Only one decimal point is allowed.";
            input.classList.add("is-invalid");
        } else {
            notifyError.textContent = ""; // Clear error message
            input.classList.remove("is-invalid");
        }
    }
}