<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

// ============================================================================
// General Page Functions
// ============================================================================

// Is GET request?
function is_get() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null) {
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null) {
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null) {
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null) {
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    }
    else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// Obtain uploaded file --> cast to object
function get_file($key) {
    $f = $_FILES[$key] ?? null;
    
    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

// Crop, resize and save photo
function save_photo($f, $folder, $width = 200, $height = 200) {
    $photo = uniqid() . '.jpg';
    
    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;
}

// Is money?
function is_money($value) {
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}

// Return base url (host + port)
function base($path = '') {
    return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/$path";
}


// ============================================================================
// HTML Helpers
// ============================================================================

// Placeholder for TODO
function TODO() {
    echo '<span>TODO</span>';
}

// Encode HTML special characters
function encode($value) {
    return htmlentities($value);
}

// Generate <input type='text'>
function html_text($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='number'>
function html_number($key, $min = '', $max = '', $step = '', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value'
                 min='$min' max='$max' step='$step' $attr>";
}

// Generate <input type='password'>
function html_password($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='search'>
function html_search($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='search' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false) {
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '') {
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

// Generate <textarea>
function html_textarea($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<textarea id='$key' name='$key' $attr>$value</textarea>";
}

// Generate SINGLE <input type='checkbox'>
function html_checkbox($key, $label = '', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    $status = $value == 1 ? 'checked' : '';
    echo "<label><input type='checkbox' id='$key' name='$key' value='1' $status $attr>$label</label>";
}

// Generate table headers <th>
function table_headers($fields, $sort, $dir, $href = '') {
    foreach ($fields as $k => $v) {
        $d = 'asc';
        $c = '';    
        
        if ($k == $sort) {
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir;
        }

        $arrow = '';
        if ($k == $sort) {
            $arrow = $dir == 'asc' ? ' ▲' : ' ▼';
        }

        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v";
    }
}

//Generate sort buttons
function sort_buttons($productName, $productCategory, $minPrice, $maxPrice, $fields, $sort, $dir, $href = '') {
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction
        $c = '';    // Default class
        
        if ($k == $sort) {
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir;
        }

        echo "<button><a href='?product_name=$productName&category_id=$productCategory&minprice=$minPrice&maxprice=$maxPrice&sort=$k&dir=$d&$href' class='$c'>$v</a></button>";
    }
}

function sort_buttons2($fields, $sort, $dir, $href = '') {
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction
        $c = '';    // Default class
        
        if ($k == $sort) {
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir;
        }

        echo "<button><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></button>";
    }
}

// ============================================================================
// Email Functions
// ============================================================================           
// Demo Accounts:
// --------------
// bait2173.email@gmail.com    ncom fsil wjzk ptre 
// aacs3173@gmail.com        xxna ftdu plga hzxl 
// liaw.casual@gmail.com        buvq yftx klma vezl 
// liawcv1@gmail.com        pztq znli gpjg tooe

function get_mail() {
    require_once 'lib/PHPMailer.php';
    require_once 'lib/SMTP.php';

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = 'aacs3173@gmail.com';
    $m->Password = 'xxna ftdu plga hzxl';
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username, 'TAR GROCER Admin');

    return $m;
}

//Is email?
function is_email($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class='err'>
function err($key) {
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";
    }
    else {
        echo '<span></span>';
    }
}

// ============================================================================
// Database Setups and Functions
// ============================================================================

// Global PDO object
$_db = new PDO('mysql:dbname=tar_grocer', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// Is unique?
function is_unique($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Check if email exists in either the member or admin tables
// Check if email exists in either the member or admin tables
function is_exists($value, $table, $field) {
    global $_db;
    
    // Check in member table
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    if ($stm->fetchColumn() > 0) {
        return true;
    }
}
// ============================================================================
// Security
// ============================================================================
// Global user object
$_user = $_SESSION['user'] ?? null;

// Login user (with table type)
function login($user, $type, $url = '/') {
    if ($user) {
        temp('info', 'Login successful as ' . $user->userType);
        $_SESSION['user'] = $user;
        $_SESSION['user_type'] = $user->userType;  // Store the user type ('member' or 'admin')
    
        // Redirect based on user type
        if ($user->userType === 'admin') {

            redirect('/page/chanyijing/admin/admin_management/admin_detail.php');
        } else {
            redirect('/index.php');
        }
        exit();
    } else {
        $_err['password'] = 'Incorrect email or password';
    }
}

// Logout user
function logout($url = '/') {
    unset($_SESSION['user']);
    unset($_SESSION['user_type']);
    redirect($url);
}


// Authorization by user type (admin, member, etc.)
function auth(...$types) {
    global $_user;

    // Check if the user is logged in
    if ($_user) {
        // If types are provided, check if the user's type matches one of the allowed types
        if ($types) {
            if (in_array($_SESSION['user_type'], $types)) {
                return; // Authorized
            }
        } else {
            return; // No types specified, just allow access
        }
    }
    
    // Redirect to login if the user is not authorized
    redirect('/login.php');
}

function validate_local_malaysian_phone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/\D/', '', $phone);

    // Check if it starts with 0 and is 10-11 digits long
    if (substr($phone, 0, 1) === '0' && (strlen($phone) === 10 || strlen($phone) === 11)) {
        return $phone; // Valid local number
    }

    return false; // Invalid number
}
// ============================================================================
// Global Constants and Variables
// ============================================================================

//Member and admin
$_genders = [
    'Male' => 'Male',
    'Female' => 'Female'
];

$_adminTiers = [
    'High' => 'High',
    'Low' => 'Low'
];

$_orderStatuses = [
    'Pending' => 'Pending',
    'Packed' => 'Packed',
    'Shipped' => 'Shipped',
    'Delivered' => 'Delivered'
];

$_members = $_db->query('SELECT member_id, member_name FROM member')
                  ->fetchAll(PDO::FETCH_KEY_PAIR);
                  
//  $_products = $_db->query('SELECT product_id, product_name, product_cover, product_resources,product_desc, product_price, product_stock FROM product WHERE product_status=1;');
//                    ->fetchAll(PDO::FETCH_KEY_PAIR);

 $_categories = $_db->query('SELECT category_id, category_name FROM category')
                  ->fetchAll(PDO::FETCH_KEY_PAIR);