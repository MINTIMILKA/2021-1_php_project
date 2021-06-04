<html>
    <!--
        login.php
        로그인 페이지

        기능: 로그인 입력창, 로그인 버튼, 회원가입
    -->
    <head>
        <meta charset="utf-8">
        <h1>로그인 페이지</h1>
    </head>
    <body>
    <!-------------------------------------------------------------------------------------------
         로그인 입력창
         아이디와 비밀번호를 입력할 수 있는 창, 로그인 버튼을 생성합니다. 
         두 입력창에 아이디와 비밀번호가 있을 때, 로그인 버튼을 입력하면 
         데이터베이스에서 회원정보를 확인 후 로그인 성공 여부를 확인합니다. 
         로그인을 성공하면 다시 메인페이지로 돌아갑니다. 
         로그인을 실패하면 실패 메시지를 출력합니다. 

         [변수]
         login_bool = 로그인 상태를 확인하기 위한 변수 (성공: 1, 실패: 0)
         user_id = 세션에 저장된 회원의 아이디 
         user_pw = 세션에 저장된 회원의 비밀번호 
    ------------------------------------------------------------------------------------------->
        <form method='post'>
            <table>
                <tr>
                    <th>아이디</th>
                    <td><input type='text' name='user_id'><td>
                </tr>
                <tr>
                    <th>비밀번호</th>
                    <td><input type='text' name='user_pw'><td>
                </tr>
            </table>
            <input type='submit' value='로그인' name='name'>
            <br>
            아이디: id20160618
            비밀번호: pw20160618
            <br>
        </form>
        <form method='get'>
            <input type='submit' value='회원 가입' name='registration_button'>
            <input type='submit' value='메인 페이지' name='main_page_button'>
        </form>
        
        <?php
            //GET 초기값 선언 
            if(!isset($_GET['registration_button'])){$_GET['registration_button'] = "";}
            if(!isset($_GET['main_page_button'])){$_GET['main_page_button'] = "";}
        ?>

        <?php
                //로그인 시도
                if(isset($_POST['user_id']) && isset($_POST['user_pw']))
                {
                    session_start();
                    $_SESSION['user_id'] = $_POST['user_id'];
                    $_SESSION['user_pw'] = $_POST['user_pw'];
                    unset($_POST['user_id']);
                    unset($_POST['user_pw']);
                }
                if(isset($_SESSION['user_id']) && isset($_SESSION['user_pw']))
                {
                    //데이터베이스 연결
                    $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');
                    
                    if($sql_id == false)
                    {
                        echo "데이터베이스 연결 실패";
                    }
                    else
                    {
                        //데이터베이스 연결 성공
                        $id_result = mysqli_query($sql_id, "select * from shopping_member where id = '$_SESSION[user_id]'");
                        $pw_result = mysqli_query($sql_id, "select * from shopping_member where pw = '$_SESSION[user_pw]'");
                        
                        $id_row = mysqli_num_rows($id_result);
                        $pw_row = mysqli_num_rows($pw_result);

                        //로그인 성공
                        if(($id_row > 0) && ($pw_row > 0))
                        {
                            $_SESSION['login_bool'] = '1';
                            
                            //회원정보 가져오기 
                            $member_info = mysqli_fetch_array($id_result);
                            
                            if((!isset($_SESSION['name'])) || (!isset($_SESSION['balance'])))
                            {
                                $_SESSION['name'] = [];
                                $_SESSION['balance'] = [];
                            }
                            
                            $_SESSION['name'] = $member_info["name"];
                            $_SESSION['balance'] = $member_info["balance"];
                            
                            //메인 페이지로 이동 
                            header("location: main_page.php");
                        }
                        else
                        {
                            //로그인 실패 
                            $_SESSION['login_bool'] = '0';
                            echo "<h2>로그인 실패<h2>";
                        }
                        mysqli_close($sql_id);
                    }
                }
            ?>

            <?php
                if($_GET['registration_button'] == "회원 가입"){header("location: registration.php");}
                if($_GET['main_page_button'] == "메인 페이지"){header("location: main_page.php");}
            ?>
    </body>
</html>