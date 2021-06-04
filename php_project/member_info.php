<html>
    <!-------------------------------------------------------------------------------------------
        member_info.php
        회원 정보 페이지
        회원의 정보를 보여주고 보유 금액을 충전할 수 있습니다.  
    ------------------------------------------------------------------------------------------->
    <head>
        <meta charset="utf-8">
        <h1>회원 정보 페이지</h1>
    </head>
    <body>
        <!-------------------------------------------------------------------------------------------
         
        ------------------------------------------------------------------------------------------->
        <?php 
            session_start(); 

            //GET 버튼 초기값 선언
            if(!isset($_GET['charge_button'])){$_GET['charge_button'] = "";}

            //회원 정보 불러오기 
            $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');
            if($sql_id == false)
            {
                echo "데이터베이스 연결 실패";
            }
            else
            {
                $id_result = mysqli_query($sql_id, "select * from shopping_member where id = '$_SESSION[user_id]'");
                $member_info = mysqli_fetch_array($id_result);
                $_SESSION['name'] = $member_info["name"];
                $_SESSION['user_id'] = $member_info["id"];
                $_SESSION['user_pw'] = $member_info["pw"];
                $_SESSION['balance'] = $member_info["balance"];
                mysqli_close($sql_id);
            }
        ?>
        
        <table>
            <tr>
                <th>이름</th>
                <td><?=$_SESSION['name']?></td>
            </tr>
            <tr>
                <th>아이디</th>
                <td><?=$_SESSION['user_id']?></td>
            </tr>
            <tr>
                <th>비밀번호</th>
                <td><?=$_SESSION['user_pw']?></td>
            </tr>
            <tr>
                <th>보유 금액</th>
                <td><?=$_SESSION['balance']?>원</td>
            </tr>
        </table>
        
        <form method='get'>
            충전할 금액<br>
            <input type='text' name='charge_text'>
            <input type='submit' value='(원)충전하기' name='charge_button'>
        </form>

        <?php
            //GET 초기값 선언 
            if(!isset($_GET['main_page_button'])){$_GET['main_page_button'] = "";}
        ?>

        <?php
            if($_GET['charge_button'] == "(원)충전하기")
            {
                //$_GET['charge_text']이 숫자가 아니면 is_float($_GET['charge_text'])는 0이다.  
                if((floatval($_GET['charge_text']) >= 100))
                {
                    $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');
                    
                    if($sql_id == false)
                    {
                        echo "데이터베이스 연결 실패";
                    }
                    else
                    {
                        //금액 충전
                        $user_balance = floatval($_SESSION['balance']) + floatval($_GET['charge_text']);
                        mysqli_query($sql_id, "update shopping_member set balance = $user_balance where id = '$_SESSION[user_id]'");
                        
                        mysqli_close($sql_id);
                        
                        header("location: member_info.php?");
                    }
                }
                elseif(floatval($_GET['charge_text']) < 100)
                {
                    echo "최소 100원 이상의 금액을 충전해야 합니다. <br>";
                }
                
            }
        ?>

        <?php
            //데이터베이스 연결
            $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');

            if($sql_id == false)
            {
                echo "데이터베이스 연결 실패";
            }
            else
            {
                //데이터베이스 연결 성공
                echo "<h3>장바구니</h3>";
                echo "<form method='post'><table>";
                echo "<tr> <td>상품 번호</td> <th>사진</th> <td>이름</td> <td>가격</td> </tr>";

                //회원 정보 가져오기 
                $id_result = mysqli_query($sql_id, "select * from shopping_member where id = '$_SESSION[user_id]'");
                $member_info = mysqli_fetch_array($id_result);
                
                $i=1;
                while($i<=30)
                {
                    
                    $goods_num = $member_info["basket_num_".$i];

                    //장바구니 리스트 출력
                    if($goods_num != null)
                    {
                        //상품 찾기 
                        //echo "$goods_num"."<br>";
                        
                        $product_total_result = mysqli_query($sql_id, "select num, name, cost from product_unit_info where num = '$goods_num'");
                        $product_unit_result = mysqli_fetch_array($product_total_result);
                        //장바구니에 상품 넣기 
                        echo "<tr>";
                        //상품 번호
                        echo "<td>"."[".$product_unit_result['num']."]"."</td>";
                        //상품 사진
                        echo "<th>".$product_unit_result['num'].".png"."</th>";
                        //상품 이름
                        echo "<td>".$product_unit_result['name']."<td>";
                        //상품 가격
                        echo "<td>".$product_unit_result['cost']."원"."<td>";
                        //구매 버튼
                        echo "<input type='submit' value='삭제' name='delete-".$i."'><input type='submit' value='구매' name='purchase_and_delete-".$i."'>";
                        echo "</tr>";
                        
                    }
                    
                    $i++;
                }
                echo "</table></form>";

                mysqli_close($sql_id);
            }
        ?>

        <form method='get'>
            <input type='submit' value='메인 페이지' name='main_page_button'>
        </form>
        
        <?php
            if($_GET['main_page_button'] == "메인 페이지"){header("location: main_page.php");}
        ?>

        <?php
            $i=1;
            while($i<=30)
            {
                if(isset($_POST["delete-".$i]))
                {
                    if($_POST["delete-".$i] == "삭제")
                    {
                        setcookie("goods_action", "delete", time()+(3600*12));
                        setcookie("basket_num", $i, time()+(3600*12));
                        set_goods_info_cookie($i);
                        header("location: goods_select.php");
                    }
                }
                elseif(isset($_POST["purchase_and_delete-".$i]))
                {
                    if($_POST["purchase_and_delete-".$i] == "구매")
                    {
                        setcookie("goods_action", "purchase_and_delete", time()+(3600*12));
                        setcookie("basket_num", $i, time()+(3600*12));
                        set_goods_info_cookie($i);
                        header("location: goods_select.php");
                    }
                }
                $i++;
            }

            function set_goods_info_cookie($i)
            {
                //데이터베이스 연결
                $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');

                if($sql_id == false)
                {
                    echo "데이터베이스 연결 실패";
                }
                else
                {
                    //회원 정보 가져오기 
                    $id_result = mysqli_query($sql_id, "select * from shopping_member where id = '$_SESSION[user_id]'");
                    $member_info = mysqli_fetch_array($id_result);

                    //상품 정보 가져오기 
                    $goods_num = $member_info["basket_num_".$i];
                    $product_total_result =  mysqli_query($sql_id, "select num, name, cost from product_unit_info where num = '$goods_num'");
                    $product_unit_result = mysqli_fetch_array($product_total_result);

                    setcookie("goods_num", $product_unit_result['num'], time()+(3600*12));
                    setcookie("goods_cost", $product_unit_result['cost'], time()+(3600*12));

                    mysqli_close($sql_id);
                }
            }
            
        ?>
    </body>
</html>