<html>
    <!-------------------------------------------------------------------------------------------
        registration.php
        회원가입을 위한 페이지 입니다. 

        기능: 개인정보 입력, 회원가입 버튼 
    ------------------------------------------------------------------------------------------->
    <head>
        <meta charset="utf-8">
        <h1>회원가입 페이지</h1>
    </head>
    <body>
        <!-------------------------------------------------------------------------------------------
         회원가입 입력창, 아이디 중복 확인 버튼, 회원가입 버튼
         html의 form 기능을 이용하여 회원가입 입력창과 버튼을 한번에 만듭니다. 
         각각의 입력창에서 신규 회원의 정보를 입력받은 다음 아이디 중복 확인 버튼을 누르면 
         데이터베이스에서 아이디의 중복을 확인합니다. 
         중복 검사에서 통과한 후 회원가입 버튼을 누르면 신규 회원의 정보들을 데이터베이스에 등록합니다. 

         [변수]
         new_mem_name = 신규회원의 이름 
         new_mem_id = 신규회원의 아이디 
         new_mem_pw = 신규회원의 비밀번호 
         id_scan_bool = 아이디 중복상태 확인 변수 (검사 안함: 0, 중복됨: 1, 통과: 2)
        ------------------------------------------------------------------------------------------->
        <?php
            //GET 초기값 선언
            if(!isset($_GET['new_mem_name'])){$_GET['new_mem_name'] = "";}
            if(!isset($_GET['new_mem_id'])){$_GET['new_mem_id'] = "";}
            if(!isset($_GET['id_overlap_button'])){$_GET['id_overlap_button'] = "";}
            if(!isset($_GET['new_mem_pw'])){$_GET['new_mem_pw'] = "";}

            //GET 버튼 초기값 선언
            if(!isset($_GET['registration_button'])){$_GET['registration_button'] = "";}
            if(!isset($_GET['main_page_button'])){$_GET['main_page_button'] = "";}

            //쿠키 초기값 선언
            if(!isset($_COOKIE['id_scan_bool'])){setcookie("id_scan_bool", 0);}
        ?>

        <form method='get'>
            <table>
                <tr>
                    <th>이름</th>
                    <td><input type='text' name='new_mem_name' value='<?= $_GET['new_mem_name'] ?>'><td>
                    <td><!--기능 버튼--></td>
                    <td><!--상태 메시지--></td>
                </tr>
                <tr>
                    <th>아이디</th>
                    <td><input type='text' name='new_mem_id' value='<?= $_GET['new_mem_id'] ?>'><td>
                    <td><input type='submit' value='중복 확인' name='id_overlap_button'></td>
                    <td><?php id_overlap($_GET['new_mem_id']); ?></td>
                </tr>
                <tr>
                    <th>비밀번호</th>
                    <td><input type='text' name='new_mem_pw' value='<?= $_GET['new_mem_pw'] ?>'><td>
                    <td><!--기능 버튼--></td>
                    <td><!--상태 메시지--></td>
                </tr>
            </table>
            <input type='submit' value='회원 가입' name='registration_button'>
            <br>
            <?php registration() ?>
        </form>

        <br>
        테스트 이름: test_name_1111
        <br>
        테스트 아이디: test_id_2222
        <br>
        테스트 비밀번호: test_pw_3333
        <br>
        
        <form method='get'>
            <input type='submit' value='메인 페이지' name='main_page_button'>
        </form>
        
        <?php 
            
        ?>

        <?php
            //아이디 중복 확인
            function id_overlap($text)
            {
                if($_GET['id_overlap_button'] == '중복 확인')
                {
                    //데이터베이스 연결
                    $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');
                    if($sql_id == false)
                    {
                        echo "데이터베이스 연결 실패";
                    }
                    else
                    {
                        //아이디 검사 
                        $id_result = mysqli_query($sql_id, "select * from shopping_member where id = '$_GET[new_mem_id]'");
                        $id_row = mysqli_num_rows($id_result);
                        
                        if($text == "")
                        {
                            echo "아이디를 입력해주세요";
                        }
                        elseif($id_row > 0)
                        {
                            setcookie("id_scan_bool", 1);
                            echo "아이디가 중복됩니다";
                        }
                        else
                        {
                            setcookie("id_scan_bool", 2);
                            echo "사용 가능한 아이디입니다";
                        }
                        mysqli_close($sql_id);
                    }
                }
            }

            //회원가입 버튼 
            function registration()
            {
                $a = 0;

                if($_GET['registration_button'] == '회원 가입')
                {
                    if($_GET['new_mem_name'] == "")
                    {
                        echo "이름을 입력해주세요 <br>";
                        $a++;
                    }
                    if($_GET['new_mem_id'] == "")
                    {
                        echo "아이디를 입력해주세요 <br>";
                        $a++;
                    }
                    elseif($_COOKIE['id_scan_bool'] < 2)
                    {
                        echo "아이디 중복을 확인해주세요 <br>";
                        $a++;
                    }
                    if($_GET['new_mem_pw'] == "")
                    {
                        echo "비밀번호를 입력해주세요 <br>";
                        $a++;
                    }
                    if(($a==0) && ($_COOKIE['id_scan_bool'] == 2))
                    {
                        //회원가입 완료
                        $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');
                        
                        if($sql_id == false)
                        {
                            echo "데이터베이스 연결 실패";
                        }
                        else
                        {
                            //회원 정보 저장
                            mysqli_query($sql_id, "insert into shopping_member (name, id, pw, balance) values ('$_GET[new_mem_name]', '$_GET[new_mem_id]', '$_GET[new_mem_pw]', 0)");
                            mysqli_close($sql_id);
                        }
                        
                        //쿠키 삭제
                        setcookie("id_scan_bool", 0, time()-3600);

                        //회원가입 완료 페이지로 이동 
                        header("location: registration_complete.php");
                    }
                }
            }

        ?>

        <!-------------------------------------------------------------------------------------------
         로그인, 회원가입 버튼 이벤트 정의 
        ------------------------------------------------------------------------------------------->
        <?php
            if($_GET['main_page_button'] == "메인 페이지"){header("location: main_page.php");}
        ?>
    </body>
</html>