<?php function validateName($name, &$errors, $fieldName) {
    $nameRegex = '/^[a-zA-Z\s]{3,15}$/';
    if (trim($name) === "") {
        $errors[$fieldName] = "This field is required";
        return false;
    } elseif (!preg_match($nameRegex, $name)) {
        $errors[$fieldName] = "Name should only contain letters and spaces (3-15 characters)";
        return false;
    }
    return true;
}

function validateEmail($email, &$errors) {
    if (trim($email) === "") {
        $errors['email'] = "Email is required";
        return false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
        return false;
    }
    return true;
}

function validatePhone($phone, &$errors) {
    $phoneRegex = '/^\d{10}$/';
    if (trim($phone) === "") {
        $errors['phone'] = "Phone number is required";
        return false;
    } elseif (!preg_match($phoneRegex, preg_replace('/\s/', '', $phone))) {
        $errors['phone'] = "Please enter a valid 10-digit phone number";
        return false;
    }
    return true;
}

function validateAddress($address, &$errors) {
    if (trim($address) === "") {
        $errors['address'] = "Address is required";
        return false;
    } elseif (strlen(trim($address)) < 10) {
        $errors['address'] = "Please enter a complete address (at least 10 characters)";
        return false;
    }
    return true;
}

function validateCity($city, &$errors) {
    $cityRegex = '/^[a-zA-Z\s]{2,30}$/';
    if (trim($city) === "") {
        $errors['city'] = "City is required";
        return false;
    } elseif (!preg_match($cityRegex, $city)) {
        $errors['city'] = "Please enter a valid city name";
        return false;
    }
    return true;
}

function validateProvince($province, &$errors) {
    $provinceRegex = '/^[a-zA-Z\s]{2,30}$/';
    if (trim($province) === "") {
        $errors['province'] = "Province is required";
        return false;
    } elseif (!preg_match($provinceRegex, $province)) {
        $errors['province'] = "Please enter a valid Province name";
        return false;
    }
    return true;
}

function validatePassword($password, &$errors) {
    $passwordRegex = '/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]).{8,20}$/';
    if (trim($password) === "") {
        $errors['password'] = "Password is required";
        return false;
    } elseif (!preg_match($passwordRegex, $password)) {
        $errors['password'] = "Password must contain at least one uppercase letter, one special character, and be 8-20 characters long";
        return false;
    }
    return true;
}

function validateTerms($terms, &$errors) {
    if (empty($terms) || $terms !== 'on') {
        $errors['terms'] = "You must agree to the terms and conditions";
        return false;
    }
    return true;
}

?>