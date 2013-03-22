<?php
    /* This page defines the UserAuth class.
     * Attributes:
     *  protected id_user
     *  protected dbc
     *  protected inv_case
     *  protected OAuth_case
     *     
     * Methods:
     *  setUserID()
     *  setinvCase()
     *  getDB()
     *  getUserID()
     *  getinvCase()
     *  isEmailAvailable()
     *  checkPass()
     *  checkUser()
     *  createUser()
     *  deleteUser()
     *  login()
     *  logout()
     *  valid()
     *  chgPassword()
     */
    
    
    class UserAuth {
         
        // Declare the attributes
        protected $id_user, $dbObject, $inv_case, $OAuth_case;

        // Constructor
        public function __construct($dbObject)
        {
            $this->dbObject = $dbObject;
        }

        // Set userID attribute
        public function setUserID($id)
        {
            $this->id_user = $id;
        }

        // Set inv_case attribute
        public function setinvCase($ivc)
        {
            $this->inv_case = $ivc;
        }
        
        // Set the OAuth_case attribute
        public function setOAuthCase($OAc)
        {
            $this->OAuth_case = $OAc;
        }

        public function getDB()
        {
            return $this->dbObject;
        }
        
        
        public function getUserID()
        {
            return $this->id_user;
        }


        public function getinvCase()
        {
            return $this->inv_case;
        }

        // Function to check if user email is available
        public function isEmailAvailable($e)
        {
            // Make the query to make sure User's new email is available    
            $q = "SELECT id_user, email
                  FROM users
                  WHERE email = '{$this->dbObject->realEscapeString($e)}'
                    AND id_user != {$this->id_user}
                  LIMIT 1";

            // Execute the query:
            $result = $this->dbObject->getRow($q);            

            // User login available, i.e. query found nothing
            return $result !== false;
        }

        // Method to check user against password entered
        public function checkPass($e, $p)
        {
            // Assign variable in case no matches
            $pass = '';

            // Make the query    
            $q = "SELECT pass, oauth_registered
                  FROM users
                  WHERE email = '{$this->dbObject->realEscapeString($e)}'
                  LIMIT 1";

            // Execute the query and store result
            $result = $this->dbObject->getRow($q);

            if($result !== false) {
                // Set the OAuth case
                $this->setOAuthCase($result['oauth_registered']);

                //$hasher = new PasswordHash($hash_cost_log2, $hash_portable);    
                $hasher = new PasswordHash(8, FALSE);

                if($hasher->CheckPassword($p, $result['pass'])) {
                    return true;
                }
            }

            return false;
        } // End of checkPass function

        // Method to check and set User registration status
        public function checkUser($userEmail)
        {

            // Make the query to make sure New User's email is available    
            $q = "SELECT id_user, invited
                  FROM users
                  WHERE email = '{$this->dbObject->realEscapeString($userEmail)}'
                  LIMIT 1";

            // Execute the query and store result
            $result = $this->dbObject->getRow($q);

            if($result !== false) {
                $this->setuserID($result['id_user']);

                if($result['invited'] == 1) {
                    $this->setinvCase(2); // Manager has already entered skeleton information about new user & invited player
                }
            }

            else {
                $this->setinvCase(1); // User login available & not invited by manager
            }
        } // End of checkUser function

        // Function to create users
        public function createUser($fn, $ln, $e, $p, $sex, $bdfrmat)
        {
            // Call checkUser function    
            $this->checkUser($e);

            $hasher = new PasswordHash(8, FALSE);

            // Encrypt the new password by making a new hash.
            $hash = $hasher->HashPassword($p);
            if(strlen($hash) < 20) {
                fail('Failed to hash new password'); //Custom function
                exit();
            }
            unset($hasher);

            // Determine registration method
            switch ($this->inv_case) {
                case 1: // User is new to the system & not invited by manager

                    // Create the activation code
                    $a = md5(uniqid(rand(), TRUE));    

                    // Make the query to add new user to database
                    $q = "INSERT INTO users
                          (
                            first_name,
                            last_name,
                            email,
                            pass,
                            sex,
                            activation,
                            birth_date,
                            invited,
                            registration_date
                          )
                          VALUES
                          ('{$this->dbObject->realEscapeString($fn)}',
                           '{$this->dbObject->realEscapeString($ln)}',
                           '{$this->dbObject->realEscapeString($e)}',
                           '{$this->dbObject->realEscapeString($hash)}',
                           '{$this->dbObject->realEscapeString($sex)}',
                           '{$this->dbObject->realEscapeString($a)}',
                           '{$this->dbObject->realEscapeString($bdfrmat)}',
                           {$this->inv_case},
                           NOW()
                          )";

                    // Execute the query:
                    $this->dbObject->query($q);

                    if($this->dbObject->getNumRowsAffected() == 1) // It ran OK.
                    {
                        // Send the activation email
                        $body = "Welcome to digoro and thank you for registering!\n\nTo activate your account, please click on this link:";
                        $body .= "\n" . BASE_URL . 'core/activate.php?x=' . urlencode($e) . "&y=$a";
                        mail($e, 'digoro.com - Registration Confirmation', $body);
                        
                        echo '<h3>Thank you for registering! A confirmation email has been sent to your address. Please
                            click on the link in that email in order to activate your account. </h3>';

                        include '../includes/ifooter.html';
                        exit();    
                    }
                    else 
                    {    // Registration process did not run OK.
                        echo '<p class="error">You could not be registered due to a system error. We apologize
                            for any inconvenience.</p>';
                    }
                    break;

                case 2: // User invited by manager
                
                    // Create the activation code
                    $a = md5(uniqid(rand(), TRUE));            
                
                    // Make the query to update user in database
                    $q = "UPDATE users
                          SET pass = '{$this->dbObject->realEscapeString($hash)}',
                              first_name = '{$this->dbObject->realEscapeString($fn)}',
                              last_name = '{$this->dbObject->realEscapeString($ln)}',
                              sex = '{$this->dbObject->realEscapeString($sex)}',
                              activation = '{$this->dbObject->realEscapeString($a)}',
                              birth_date = '{$this->dbObject->realEscapeString($bdfrmat)}',
                              registration_date = NOW() 
                          WHERE id_user= {$this->id_user}
                          LIMIT 1";

                    // Execute the query:
                    $this->dbObject->query($q);
        
                    if($this->dbObject->getNumRowsAffected() == 1) // It ran OK.
                    {
                        // Send the activation email
                        $body = "Welcome to digoro and thank you for registering!\n\nTo activate your account, please click on this link:";
                        $body .= "\n" . BASE_URL . 'core/activate.php?x=' . urlencode($e) . "&y=$a";
                        mail($e, 'digoro.com - Registration Confirmation', $body);

                        echo '<h3>Thank you for registering! A confirmation email has been sent to your address. Please
                            click on the link in that email in order to activate your account. </h3>';

                        include '../includes/ifooter.html';
                        exit();    
                    }
                    else 
                    {    // Registration process did not run OK.
                        echo '<p class="error">You could not be registered due to a system error. We apologize
                            for any inconvenience.</p>';
                    }
                    break;

                default:
                    // The email address is not available and player was not previously invited
                    echo '<p class="error">That email address has already been registered. If you have forgotten your password,
                        use the link below to have your password sent to you.</p>';
                    break;

            } // End of switch
        } // End of createUser function

        // Function to delete user
        public function deleteUser($id)
        {
            // Make the query    
            $q = "DELETE FROM users WHERE id_user = {$id} LIMIT 1";

            // Execute the query:
            $this->dbObject->query($q);

            // If the query ran ok.
            if($this->dbObject->getNumRowsAffected() == 1) {
                session_unset();
                session_destroy();
            }
            else {    // If the query did not run ok.
                echo '<div class="alert alert-error">This account could not be deleted due to a system error</div>';
            }
        } // End of deleteUser function

        // Function to log in users
        public function login($e, $p)
        {
            if($this->checkPass($e, $p)) // Call checkPass function    
            {
                // Make the query    
                $q = "SELECT role, id_user, login_before, default_teamID
                      FROM users
                      WHERE email = '{$this->dbObject->realEscapeString($e)}'
                        AND activation = ''
                      LIMIT 1";

                // Execute the query and store result
                $result = $this->dbObject->getRow($q);

                if($result !== false)
                {
                    session_regenerate_id(true);

                    // Set default team to session variable
                    $_SESSION['deftmID'] = $result['default_teamID'];

                    // Store the HTTP_USER_AGENT:
                    $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);            

                    // Redirect if user hasn't logged in before take them to welcome page 
                    if($result['login_before'] == false) {
                        $user = new User($this->dbObject, $result['id_user']);
                        $_SESSION['userObj'] = $user;
                        $url = BASE_URL . 'core/welcome.php';
                        header("Location: $url");
                        exit();
                    }

                    // Redirect if user is a registered manager
                    if($result['login_before'] == true && $result['role'] == 'm') {
                        $user = new User($this->dbObject, $result['id_user']);
                        $_SESSION['userObj'] = $user;
                        $_SESSION['role'] = $result['role'];
                        $url = BASE_URL . 'manager/home.php';
                        header("Location: $url");
                        exit();
                    }

                    // Redirect if user is a registered player
                    if($result['login_before'] == true && $result['role'] == 'p') {
                        $user = new User($this->dbObject, $result['id_user']);
                        $_SESSION['userObj'] = $user;
                        $_SESSION['role'] = $result['role'];
                        $url = BASE_URL . 'player/home.php';
                        header("Location: $url");
                        exit();
                    }

                    ob_end_clean();
                    header("Location: $url");

                    // Close hasher
                    unset($hasher);

                    exit();
                }
                else {
                    echo '<div class="alert alert-error">You could not be logged in. Please check that you have activated your account</div>';
                }
            }
            else if($this->OAuth_case == 1) {
                echo '<div class="alert alert-error">You are registered with facebook. You must login using the Facebook login feature</div>';
            }
            else {
                echo '<div class="alert alert-error">Either the email address and password entered do not match those
                    those on file or you have not yet activated your account</div>';                
            }

        } // End of login function

        // Function to log off users
        public function logoff()
        {
            session_unset();
            session_destroy();
        }

        // Function to check if user is authorized for access [**Currently not really using at moment**]
        public function valid($lvl)
        {
            switch ($lvl)
            {
                case 'G': // General level
                    if(!isset($_SESSION['agent']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
                    {
                        return False;
                    }
                    else 
                    {
                        return True;
                    }
                    break;
                    
                case 'A': // Administrator level
                    if(($_SESSION['role'] != 'A') || !isset($_SESSION['agent']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
                    {
                        return False;
                    }
                    else
                    {
                        return True;
                    }
                    break;
                    
                case 'M': // Manager level minimum
                    if(($_SESSION['role'] == 'P') || !isset($_SESSION['agent']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
                    {
                        return False;
                    }
                    else
                    {
                        return True;
                    }
                    break;
                    
                case 'P': // Player level minimum
                    if(($_SESSION['role'] == 'M') || !isset($_SESSION['agent']) || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])))
                    {
                        return False;
                    }
                    else
                    {
                        return True;
                    }
                    break;
                    
                default:
                    return False;
                    break;                
            }
        } // End of valid function

        // Function to change password
        public function chgPassword($e, $oldp, $pass1, $pass2)
        {
            // Make the query    
            $q = "SELECT pass
                  FROM users
                  WHERE email = '{$this->dbObject->realEscapeString($e)}'
                    AND activation = ''
                  LIMIT 1";

            // Execute the query and store result
            $pass = $this->dbObject->getOne($q);

            $hasher = new PasswordHash(8, FALSE);                    

            // Checks if old password matches current password in database. If so proceed to change password.
            if($hasher->CheckPassword($oldp, $pass)) {

                // Checks if new password matches confirm new password and also validates.
                $p = FALSE;
                if(strlen($pass1) > 5) {
                    if($pass1 == $pass2) {
                        $p = $pass1;
                    }    
                    else {
                        echo '<div class="alert alert-error">Your password did not match the confirmed password!</div>';
                    }
                }
                else {
                    echo '<div class="alert alert-error">Please enter a valid new password!</div>';
                }        

                // Encrypt the new password by making a new hash.
                $hash = $hasher->HashPassword($p);                
                if(strlen($hash) < 20) {
                    fail('Failed to hash new password'); // Custom function
                    exit();
                }
                unset($hasher);

                // If new password is valid, proceed to update database with new password.
                if($p) {

                    // Make the query
                    $q = "UPDATE users
                          SET pass = '{$this->dbObject->realEscapeString($hash)}'
                          WHERE email = '{$this->dbObject->realEscapeString($e)}'
                          LIMIT 1";

                    // Execute the query:
                    $this->dbObject->query($q);

                    if($this->dbObject->getNumRowsAffected() == 1) { // It ran OK.
                        $body = "Your password has been changed. If you feel you got this email in error please contact the system administrator.";
                        $body = wordwrap($body, 70);
                        mail($e, 'digoro.com - Password Changed', $body);

                        echo '<h3>Your password has been changed.</h3>';        
                    }
                    else {
                        echo '<p class="error">Your password was not changed. Make sure your new password
                            is different than the current password. Contact the system administrator if you think
                            and error occured.</p>';
                    }
                }
            }
        } // End of chgPassword function


        // Function to check if user already registered with OAuth
        public function isOAuthRegistered($email)
        {

            // Make the query    
            $q = "SELECT oauth_registered
                  FROM users
                  WHERE email = '{$this->dbObject->realEscapeString($email)}'
                  LIMIT 1";

            // Execute the query and store result
            $OAstatus = $this->dbObject->getOne($q);

            return ($OAstatus == 1);
        } // End of isOAuthRegistered function


        // Function to add OAuth Users
        public function addOAuthUser($e, $fn, $ln, $role, $gd, $bdfrmat)
        {

            if(!$this->isOAuthRegistered($e)) {

                // Define constant
                $oauth_reg = 1;

                // Call checkUser function    
                $this->checkUser($e);

                // Determine registration method
                switch($this->inv_case) {
                    case 1: // User is new to the system & not invited by manager        
                        $iv = 0; // Define invite constant in database to "brand new user"

                        // Make the query to add new user to database
                        $q = "INSERT INTO users
                              (
                                email,
                                first_name,
                                last_name,
                                role,
                                gender,
                                birth_date,
                                invited,
                                oauth_registered,
                                registration_date
                              ) 
                              VALUES
                              (
                                '{$this->dbObject->realEscapeString($e)}',
                                '{$this->dbObject->realEscapeString($fn)}',
                                '{$this->dbObject->realEscapeString($ln)}',
                                '{$this->dbObject->realEscapeString($role)}',
                                '{$this->dbObject->realEscapeString($gd)}',
                                '{$this->dbObject->realEscapeString($bdfrmat)}',
                                {$iv},
                                {$oauth_reg},
                                NOW()
                              )";

                        // Execute the query
                        $this->dbObject->query($q);

                        if($this->dbObject->getNumRowsAffected() == 1) { // It ran OK.

                            $userID = $this->dbObject->getLastInsertId();    
    
                            if($role == 'M') {
                                $user = new Manager($this->dbObject, $userID);
                                $_SESSION['userObj'] = $user;
                            }

                            if($role == 'P') {
                                $user = new Player($this->dbObject, $userID);
                                $_SESSION['userObj'] = $user;
                            }
                        }
                        else {
                            // Registration process did not run OK.
                            echo '<div class="alert alert-error">You could not be registered due to a system error. We apologize
                                for any inconvenience. [Case 1]</div>';
                        }

                        break;

                    case 2: // User invited by manager
    
                        // Make the query to select the user ID
                        $q = "SELECT id_user
                              FROM users
                              WHERE email = '{$this->dbObject->realEscapeString($e)}'
                              LIMIT 1";

                        // Execute the statement and store result
                        $userID = $this->dbObject->getOne($q);

                        if($userID !== false) { // Found match in database
                            
                            // Make the query to update user in database
                            $q = "UPDATE users
                                  SET first_name = '{$this->dbObject->realEscapeString($fn)}',
                                      last_name = '{$this->dbObject->realEscapeString($ln)}',
                                      role = '{$this->dbObject->realEscapeString($role)}',
                                      gender = '{$this->dbObject->realEscapeString($gd)}',
                                      birth_date = '{$this->dbObject->realEscapeString($bdfrmat)}',
                                      registration_date = NOW(),
                                      oauth_registered = {$oauth_reg}
                                  WHERE id_user = {$userID}
                                  LIMIT 1";

                            // Execute the query:
                            $this->dbObject->query($q);

                            if($this->dbObject->getNumRowsAffected() == 1) { // It ran OK.

                                if ($role == 'M') {
                                    $user = new Manager($this->dbObject, $userID);
                                    $_SESSION['userObj'] = $user;
                                }

                                if ($role == 'P') {
                                    $user = new Player($this->dbObject, $userID);
                                    $_SESSION['userObj'] = $user;
                                }
                            }
                            else {
                                //Update failed
                                echo '<div class="alert alert-error">You could not be registered due to a system error. We apologize
                                    for any inconvenience. [Case 2]</div>';
                            }
                        }
                        else {    
                            // Registration process did not run OK.
                            echo '<div class="alert alert-error">You could not be registered due to a system error. We apologize
                                for any inconvenience.</div>';
                        }

                        break;
                        
                    default:
                        break;
                } // End of switch
            }
        } // End of addOAuthUser function
        

        //    Function to login an OAuth User
        public function OAuthlogin($e) {

            // Make the query    
            $q = "SELECT id_user, login_before, default_teamID
                  FROM users 
                  WHERE email = '{$this->dbObject->realEscapeString($e)}'
                    AND activation = ''
                  LIMIT 1";
        
            // Execute the query and store result
            $result = $this->dbObject->getRow($q);

            if($result !== false) { // Found match in database

                session_regenerate_id(true);
        
                // Set default team to session variable
                $_SESSION['deftmID'] = $result['default_teamID'];

                // Store the HTTP_USER_AGENT:
                $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
                            
                if($this->isOAuthRegistered($e) && ($result['login_before'] == 1)) {
                        
                    //Redirect User
                    $user = new User($this->dbObject, $result['id_user']);
                    $_SESSION['userObj'] = $user;                            
                    $url = BASE_URL . 'manager/home.php';

                    ob_end_clean();
                    header("Location: $url");

                    exit();
                }
                else if(!$this->isOAuthRegistered($e) && ($result['login_before'] == 1)) { //User is not OAuth registered but has logged in before
                    // Set boolean logic to true
                    $bl = 1;

                    // Update the user's info in the database
                    $q = "UPDATE users SET oauth_registered = {$bl} WHERE id_user = {$userID} LIMIT 1";
        
                    // Execute the query:
                    $this->dbObject->query($q);

                    if($this->dbObject->getNumRowsAffected() !== 1) { // It didn't run ok
                        echo '<div class="alert alert-error">There was an error. Please contact the service administrator</div>';
                        exit();
                    }                        

                    //Redirect User                    
                    $user = new User($this->dbObject, $userID);
                    $_SESSION['userObj'] = $user;
                    $url = BASE_URL . 'manager/home.php';
                    
                    ob_end_clean();
                    header("Location: $url");                  
                    exit();
                }
                else {
                    
                    //Redirect User
                    $user = new User($this->dbObject, $userID);
                    $_SESSION['userObj'] = $user;                
                    $url = BASE_URL . 'core/oauth_welcome.php';
                    ob_end_clean();
                    header("Location: $url");
                    exit();                
                }
            }    
            else {
                //Redirect User
                $url = BASE_URL . 'core/oauth_welcome.php';
                ob_end_clean();
                header("Location: $url");
                exit();
            }

        } // End of OAuthlogin function
                        
        
    } // End of Class