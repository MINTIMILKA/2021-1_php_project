<html>
    <!-------------------------------------------------------------------------------------------
        search_result_move.php
        상품 목록의 새로고침 오작동을 방지하기 위한 php 파일입니다. 
    ------------------------------------------------------------------------------------------->
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <!--상품 목록 이동버튼 기능 정의-->
        <?php

            $offset = 5;

            if(!isset($_COOKIE['end_count'])){setcookie("end_count", 0, time()+(3600*12));}

            if($_GET['prev_button'] == "이전")
            {
                setcookie("end_count", $_COOKIE['end_count'] - $offset, time()+(3600*12));
                header("location: search_result.php");
            }
            if($_GET['next_button'] == "다음")
            {
                setcookie("end_count", $_COOKIE['end_count'] + $offset, time()+(3600*12));
                header("location: search_result.php");
            }
        ?>
    </body>
</html>

