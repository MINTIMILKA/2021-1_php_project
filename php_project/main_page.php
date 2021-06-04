<html>
    <!-------------------------------------------------------------------------------------------
        main_page.php
        사용자에게 쇼핑몰을 맨 처음으로 보여주는 메인 페이지입니다. 
        
        기능
        검색창(버튼포함), 로그인/로그아웃 버튼, 카테고리 메뉴, 제품 광고 링크(사진)
    ------------------------------------------------------------------------------------------->
    <head>
        <meta charset="utf-8">
        <h1>메인 페이지</h1>
    </head>
    <body>
        <!-------------------------------------------------------------------------------------------
         로그인 기능
         세션을 검사하여 아이디, 비밀번호의 내용이 존재하지 않을 경우 로그인 버튼을 생성합니다. 
         아이디, 비밀번호의 내용이 존재하면 로그아웃 버튼을 만듭니다. 

         [변수]
         login_bool = 로그인 상태를 확인하기 위한 변수 (성공: 1, 실패: 0)
         user_id = 세션에 저장된 회원의 아이디 
         user_pw = 세션에 저장된 회원의 비밀번호 
        ------------------------------------------------------------------------------------------->
        <?php 
            session_start(); 

            //GET 초기값 선언 
            if(!isset($_GET['login_button'])){$_GET['login_button'] = "";}
            if(!isset($_GET['registration_button'])){$_GET['registration_button'] = "";}
        ?>
        
        <?php
            //세션 초기값 선언
            if(!isset($_SESSION['login_bool']))
            {
                $_SESSION['login_bool'] = [];
                $_SESSION['user_id'] = [];
                $_SESSION['user_pw'] = [];
            }
            
            if($_SESSION['login_bool'] == '1')
            {
                echo "로그인 성공<br>";
                echo "환영합니다 $_SESSION[name]님<br>";
                echo "<form action='member_info.php'><input type='submit' value='회원 정보'></form>";
                //로그아웃 버튼(GET 방식으로 치환 불가)
                echo "<form action='logout.php'><input type='submit' value='로그아웃'></form>";
            }
            else
            {
                session_abort();
                echo "로그인이 필요합니다";
                echo "<form method='get'>";
                echo "<input type='submit' value='로그인' name='login_button'>";
                echo "<input type='submit' value='회원 가입' name='registration_button'>";
                echo "</form>";
            }

        ?>
        
        <!-------------------------------------------------------------------------------------------
         로그인, 회원가입 버튼 이벤트 정의 
        ------------------------------------------------------------------------------------------->
        <?php
            if($_GET['login_button'] == "로그인"){header("location: login.php");}
            if($_GET['registration_button'] == "회원 가입"){header("location: registration.php");}
        ?>

        <form method='get' action='search_result.php'>
            검색창
            <input type='text' name='serch_text'>
            <input type='submit' value='검색' name='serch_button'>
        </form>
        
        <form method='get'>
            제품 목록<br>
            <input type='submit' value='테스트' name='product_list'><br>
            <input type='submit' value='생활용품' name='product_list'><br>
            <input type='submit' value='의류' name='product_list'><br>
            <input type='submit' value='전자제품' name='product_list'><br>
            <input type='submit' value='식품' name='product_list'><br>
            <input type='submit' value='스포츠' name='product_list'><br>
            <input type='submit' value='가구' name='product_list'><br>
        </form>
        
        <?php
            //검색결과를 쿠키에 저장(다른 페이지에서도 사용 가능)
            if(isset($_GET['product_list']) && ($_GET['product_list'] != ""))
            {
                //검색결과는 12시간동안 유효함
                setcookie("research_result", $_GET['product_list'], time()+(3600*12));
                $offset = 5;
                setcookie("end_count", $offset, time()+(3600*12));
                header("location: search_result.php");
            }
            
        ?>
    </body>
</html>