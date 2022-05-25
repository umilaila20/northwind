<?php
require_once "connect.php";

//definisi variabel
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

//pemrosesan data -> input data akun baru ke tabel user di database
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //validasi username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers and underscores.";
    } else {
        //siapkan query sql membandingkan  data apakah username sudah ada atau belum
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);

            //buat parameter
            $param_username = trim($_POST["username"]);

            //eksekusi statement
            if ($stmt->execute()) {
                //hasil disimpan di dalam stmt
                $stmt->store_result();

                if ($stmt->num_rows() == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later";
            }

            //close
            $stmt->close();
        }
    }

    //validasi password
    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    //validasi confirm password
    if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST['confirm_password']);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    //periksa jika ada error pada input
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        //siapkan query insert data
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $param_username, $param_password);

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            if ($stmt->execute()) {
                header("location: login.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <title>REGISTER</title>
</head>
<!-- pembuatan background -->
<body class="vh-100">
    <!-- pembuatan kotak hitam/konten -->
        <div class="card">
            
        <!-- pembuatan judul konten -->
            <div class="card-title">
                <h2>REGISTER</h2>
                
                <!-- pembuatan garis bawah -->
                <div class="underline-text"></div>
            </div>
            
            <!-- pembuatan form yang akan dimasukkan ke database -->
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="form">
                <label for="username" style="margin-top:-10%;">
                    &nbsp;Username
                </label>
                <input id="username" class="form-content <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" type="username" name="username" required />
                
                <!-- pengecekan/validasi username -->
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
                <label for="password" style="margin-top:4%;">
                    &nbsp;Password
                </label>
                <input id="password" class="form-content <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" type="password" name="password" required />
                
                <!-- pengecekan/validasi password -->
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                <label for="confirm-password" style="margin-top:4%;" >
                    &nbsp;Confirm Password
                </label>
                <input id="confirm-password" class="form-content <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>" type="password" name="confirm_password" required />
                
                <!-- pengecekan/validasi confirm-password -->
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>

                <!-- pembuatan tombol submit/registrasi -->
                <input class="submit-btn" style="margin-top:10%" type="submit" name="submit" value="REGISTER"> 
                <a href="login.php" class="sign-up">Back to <span style="color:red">Log In</span></a>
            </form>
        </div>
</body>

</html>