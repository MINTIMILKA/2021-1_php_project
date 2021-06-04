<html>
    <!-------------------------------------------------------------------------------------------
        goods_select.php
        상품 선택 페이지(search_result.php)에서 상품 선택 후 (장바구니에)담기 또는 구매 버튼을 
        클릭했을 때의 이벤트를 처리하기 위해 만든 페이지 입니다. 
        search_result.php에서 post 변수의 값을 읽고 버튼 내용에 따른 이벤트를 처리합니다.  
    ------------------------------------------------------------------------------------------->
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <!-------------------------------------------------------------------------------------------
         로그인을 하지 않았을 때 -> 오류 메시지를 post를 통해 전달 후 오류 메시지 출력

        ------------------------------------------------------------------------------------------->
        <?php
            session_start();
            //GET 초기값 선언 
            if(!isset($_SESSION['login_bool'])){$_SESSION['login_bool'] = [];}
            if(!isset($_POST['login_button'])){$_POST['login_button'] = "";}
            if(!isset($_POST['search_button'])){$_POST['search_button'] = "";}
            
            if(!isset($_COOKIE['goods_action'])){setcookie("goods_action", "", time()+(3600*12));}
            if(!isset($_COOKIE['goods_num'])){setcookie("goods_num", "", time()+(3600*12));}
            if(!isset($_COOKIE['goods_cost'])){setcookie("goods_cost", "", time()+(3600*12));}
            if(!isset($_COOKIE['basket_num'])){setcookie("basket_num", "", time()+(3600*12));}
        ?>
        
        <?php
            if($_SESSION['login_bool'] == '0')
            {
                echo "<h1>로그인을 해야합니다!</h1>";
                echo "<form method='post'>";
                echo "<input type='submit' value='로그인하러 가기' name='login_button'><input type='submit' value='이전으로 돌아가기' name='search_button'>";
                echo "</form>";
            }
            elseif($_SESSION['login_bool'] == '1')
            {
                if($_COOKIE['goods_action'] == "purchase")
                {
                    //구매

                    //데이터베이스 연결
                    $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');
                    
                    if($sql_id == false)
                    {
                        echo "데이터베이스 연결 실패";
                    }
                    else
                    {
                        $id_result = mysqli_query($sql_id, "select * from shopping_member where id = '$_SESSION[user_id]'");
                        $member_info = mysqli_fetch_array($id_result);
                        $_SESSION['balance'] = $member_info["balance"];

                        $user_balance = floatval($_SESSION['balance']) - floatval($_COOKIE['goods_cost']);
                        if($user_balance >= 0)
                        {
                            //상품 구매
                            mysqli_query($sql_id, "update shopping_member set balance = $user_balance where id = '$_SESSION[user_id]'");
                            echo "<h1>구매를 완료했습니다</h1>";
                            echo "현재 잔액: ".$user_balance;
                        }
                        else
                        {
                            //구매 실패
                            echo "<h1>잔액이 부족합니다</h1>";
                            echo "현재 잔액: ".$_SESSION['balance'];
                        }
                    }
                    mysqli_close($sql_id);
                }
                elseif($_COOKIE['goods_action'] == "basket")
                {
                    //장바구니

                    //데이터베이스 연결
                    $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');
                    
                    if($sql_id == false)
                    {
                        echo "데이터베이스 연결 실패";
                    }
                    else
                    {
                        //장바구니 담기
                        $i=1;
                        while($i<=30)
                        {
                            //회원 정보 가져오기 
                            $id_result = mysqli_query($sql_id, "select * from shopping_member where id = '$_SESSION[user_id]'");
                            $member_info = mysqli_fetch_array($id_result);
                            
                            //장바구니 비어있는지 확인 
                            if($member_info["basket_num_".$i] == null)
                            {
                                //장바구니에 상품 넣기 
                                mysqli_query($sql_id, "update shopping_member set basket_num_".$i." = '$_COOKIE[goods_num]' where id = '$_SESSION[user_id]'");
                                break;
                            }

                            $i++;
                        }
                    }
                    mysqli_close($sql_id);

                    echo "<h1>장바구니에 담았습니다</h1>";
                }
                elseif($_COOKIE['goods_action'] == "delete")
                {
                    //삭제 

                    //데이터베이스 연결
                    $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');
                    
                    if($sql_id == false)
                    {
                        echo "데이터베이스 연결 실패";
                    }
                    else
                    {
                        $id_result = mysqli_query($sql_id, "select * from shopping_member where id = '$_SESSION[user_id]'");
                        $member_info = mysqli_fetch_array($id_result);
                        
                        //장바구니 안의 해당 상품 삭제
                        $basket_num = "basket_num_".$_COOKIE['basket_num'];
                        //echo "basket_num: ".$_COOKIE['basket_num'];
                        mysqli_query($sql_id, "update shopping_member set ".$basket_num." = null where id = '$_SESSION[user_id]'");
                        
                        //다른 상품들 앞으로 정렬
                        for($i=$_COOKIE['basket_num']+1;$i<=30;$i++)
                        {
                            if($member_info["basket_num_".$i] != null)
                            {
                                $next_basket_num = "basket_num_".$i;
                                $basket_num = "basket_num_".($i-1);

                                mysqli_query($sql_id, "update shopping_member set ".$basket_num." = ".$member_info[$next_basket_num]." where id = '$_SESSION[user_id]'");
                                mysqli_query($sql_id, "update shopping_member set ".$next_basket_num." = null where id = '$_SESSION[user_id]'");
                            }
                            else
                            {
                                break;
                            }
                        }
                    }
                    mysqli_close($sql_id);

                    echo "<h1>삭제를 완료했습니다</h1>";
                }
                elseif($_COOKIE['goods_action'] == "purchase_and_delete")
                {
                    //구매 후 삭제

                    //데이터베이스 연결
                    $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');
                    
                    if($sql_id == false)
                    {
                        echo "데이터베이스 연결 실패";
                    }
                    else
                    {
                        $id_result = mysqli_query($sql_id, "select * from shopping_member where id = '$_SESSION[user_id]'");
                        $member_info = mysqli_fetch_array($id_result);
                        $_SESSION['balance'] = $member_info["balance"];
                        
                        $user_balance = floatval($_SESSION['balance']) - floatval($_COOKIE['goods_cost']);
                        if($user_balance >= 0)
                        {
                            //상품 구매
                            mysqli_query($sql_id, "update shopping_member set balance = $user_balance where id = '$_SESSION[user_id]'");
                            
                            //장바구니 안의 해당 상품 삭제
                            $basket_num = "basket_num_".$_COOKIE['basket_num'];
                            //echo "basket_num: ".$_COOKIE['basket_num'];
                            mysqli_query($sql_id, "update shopping_member set ".$basket_num." = null where id = '$_SESSION[user_id]'");
                            
                            //다른 상품들 앞으로 정렬
                            for($i=$_COOKIE['basket_num']+1;$i<=30;$i++)
                            {
                                if($member_info["basket_num_".$i] != null)
                                {
                                    $next_basket_num = "basket_num_".$i;
                                    $basket_num = "basket_num_".($i-1);

                                    mysqli_query($sql_id, "update shopping_member set ".$basket_num." = ".$member_info[$next_basket_num]." where id = '$_SESSION[user_id]'");
                                    mysqli_query($sql_id, "update shopping_member set ".$next_basket_num." = null where id = '$_SESSION[user_id]'");
                                }
                                else
                                {
                                    break;
                                }
                            }

                            echo "<h1>구매를 완료했습니다</h1>";
                            echo "현재 잔액: ".$user_balance;
                        }
                        else
                        {
                            //구매 실패
                            echo "<h1>잔액이 부족합니다</h1>";
                            echo "현재 잔액: ".$_SESSION['balance'];
                        }
                    }
                    mysqli_close($sql_id);
                }
                
                echo "<form method='post'>";
                echo "<input type='submit' value='메인으로 돌아가기' name='search_button'>";
                echo "</form>";
            }
        ?>

        <?php
            if(isset($_COOKIE['goods_action'])){setcookie("goods_action", "", time()-(3600*12));}
            if(isset($_COOKIE['goods_num'])){setcookie("goods_num", "", time()-(3600*12));}
            if(isset($_COOKIE['goods_cost'])){setcookie("goods_cost", "", time()-(3600*12));}
            if(isset($_COOKIE['basket_num'])){setcookie("basket_num", "", time()-(3600*12));}

            //버튼
            if($_POST['login_button'] == "로그인하러 가기"){header("location: login.php");}
            if($_POST['search_button'] == "메인으로 돌아가기"){header("location: main_page.php");}
        ?>
    </body>
</html>