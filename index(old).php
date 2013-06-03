<?php 

    require_once('includes/bootstrap.php');
    require_once('includes/iheader.html');

    // Authorized Login Check
    // If session value is present, redirect the user. Also validate the HTTP_USER_AGENT    
    if(isset($_SESSION['agent']) AND ($_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']))) {
        $role = $_SESSION['role'];
        
        //Redirect User
        switch($role) {
            case 'A':
                $url = BASE_URL . 'admin/admin_home.php';
                break;
            case 'M':
                $url = BASE_URL . 'manager/manager_home.php';
                break;
            case 'P':
                $url = BASE_URL . 'player/player_home.php';
                break;
            default:
                $url = BASE_URL . 'index.php';
                break;
        }

        header("Location: $url");
        exit();            
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Validate email address
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $e = $_POST['email'];
        }
        else {
            $e = FALSE;
            echo '<p class="error"> Please enter valid email address!</p>';
        }

        // Validate password
        if(!empty($_POST['pass'])) {
            $p = $_POST['pass'];
        }
        else {
            $p = FALSE;
            echo '<p class="error">You forgot to enter your password!</p>';
        }

        // Check if email and password entered are valid before proceeding to login procedure.
        if($e && $p) {
            $user = new UserAuth();
            $user->login($e, $p);
            unset($user);
        }
    }
?>
</head>
<body>
    <div id="Header">
        <h1>digoro</h1>
    </div>
    
    <!-- Existing Member Login Form -->
    <h2>Log In</h2>
    <p id="no-script">You must have JavaScript enabled!</p>
    <p>Your browser must allow cookies in order to log in.</p>
    <form action="index.php" method="post" id="loginform">
        <fieldset>
        <div>
            <label for="email"><b>Email Address:</b></label>
            <input type="text" name="email" id="email" size="30" maxlength="60" />
        </div>
        <div>
            <label for="pass"><b>Password:</b></label>
            <input type="password" name="pass" id="pass" size="20" maxlength="20" />
        </div>
        <input type="submit" name="submit" id="submit" value="Log In" />
        </fieldset>
    </form>

    <p>Click <a href="core/register.php">here</a> to create an account.</p>
    <p><a href="core/forgot_password.php">Forgot your password?</a></p>
<?php require_once('../includes/ifooter.html'); ?>