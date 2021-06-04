<html>
    <!-------------------------------------------------------------------------------------------
        search_result.php
        상품의 검색결과를 보여주는 페이지입니다. 

        기능
        검색창(버튼포함), 검색된 제품 사진
    ------------------------------------------------------------------------------------------->
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <!-------------------------------------------------------------------------------------------
         검색결과 출력 기능
         검색창 또는 제품목록 리스트에서 선택 결과들을 데이터베이스에 검색하여
         관련 상품들의 정보들을 모두 출력합니다. 
        ------------------------------------------------------------------------------------------->
        
        <?php
            //로그인 확인
            session_start(); 
            if(!isset($_SESSION['login_bool'])){ $_SESSION['login_bool'] = '0';}

            if(!isset($_COOKIE['research_result'])){$_COOKIE['research_result'] = "";}
            if(!isset($_COOKIE['count_offset'])){$_COOKIE['count_offset'] = 0;}
            if(!isset($_COOKIE['end_count'])){setcookie("end_count", $offset, time()+(3600*12));}

            //상품 선택 후 저장되는 데이터
            if(!isset($_COOKIE['goods_action'])){setcookie("goods_action", "", time()+(3600*12));}
            if(!isset($_COOKIE['goods_num'])){setcookie("goods_num", "", time()+(3600*12));}
            if(!isset($_COOKIE['goods_cost'])){setcookie("goods_cost", "", time()+(3600*12));}

            if(!isset($_GET['main_page'])){$_GET['main_page'] = "";}
            if(!isset($_GET['goods_select_message'])){$_POST['goods_select_message'] = "";}
        ?>

        <?php
            if($_SESSION['login_bool'] == '0')
            {
                echo "<form method='get' action='login.php'>";
                echo "<input type='submit' value='로그인' name='login_button'>";
                echo "</form>";
            }
        ?>

        <form method='get' action='search_result.php'>
            검색창
            <input type='text' name='serch_text'>
            <input type='submit' value='검색' name='serch_button'>
        </form>

        <?php
            //보여줄 상품 갯수 간격
            $offset = 5;
            
            //데이터베이스 연결
            $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');

            if($sql_id == false)
            {
                echo "데이터베이스 연결 실패";
            }
            else
            {
                //데이터베이스 연결 성공
                //데이터베이스 검색(아래의 함수 참고)
                $product_total_result = search_goods($sql_id, $_COOKIE['research_result']);
                    
                //검색된 상품 갯수
                $product_count = mysqli_num_rows($product_total_result);
                    
                //일부 상품만 보여주기
                $current_count = 0;

                //검색된 상품 번호가 없을 때까지 반복 
                echo "<form method='post'><table>";
                echo "<tr> <td>상품 번호</td> <th>사진</th> <td>이름</td> <td>가격</td> </tr>";
                while($product_unit_result = mysqli_fetch_array($product_total_result))
                {
                    if(($current_count >= $_COOKIE['end_count'] - $offset) && ($current_count < $_COOKIE['end_count']))
                    {
                        echo "<tr>";
                        //상품 번호
                        echo "<td>"."[".$product_unit_result['num']."]"."</td>";
                        //상품 사진
                        echo "<th>".$product_unit_result['num'].".png"."</th>";
                        //상품 이름
                        echo "<td>".$product_unit_result['name']."<td>";
                        //상품 가격
                        echo "<td>".$product_unit_result['cost']."원"."<td>";
                        //장바구니에 담기, 구매 버튼
                        echo "<input type='submit' value='담기' name='basket-".$current_count."'><input type='submit' value='구매' name='purchase-".$current_count."'>";
                        echo "</tr>";
                    }
                    $current_count += 1;
                }
                echo "</table></form>";

                echo "current_count: ".$current_count." / end_count: ".$_COOKIE['end_count'];

                if($_COOKIE['end_count'] >= $product_count)
                {
                    echo "<br>";
                    echo "<h3>원하는 상품이 찾을 수 없나요? </h3>";
                    echo "<h4>아래의 링크를 통해 계속 찾을 수 있습니다 </h4>";

                    echo "<table>";
                    
                    echo "<tr>";
                    //지마켓
                    echo "<td><a href='https://browse.gmarket.co.kr/search?keyword=".$_COOKIE['research_result']."'>G마켓에서 찾기</a></td>";
                    echo "</tr>";
                    
                    echo "<tr>";
                    //위메프
                    echo "<td><a href='https://search.wemakeprice.com/search?search_cate=top&keyword=".$_COOKIE['research_result']."&_service=5&_type=3'>위메프에서 찾기</a></td>";
                    echo "</tr>";
                    
                    echo "<tr>";
                    //쿠팡
                    echo "<td><a href='https://www.coupang.com/np/search?q=".$_COOKIE['research_result']."&channel=auto'>쿠팡에서 찾기</a></td>";
                    echo "</tr>";
                    
                    echo "</table>";
                }

                //모든 상품의 수
                $total_count = mysqli_num_rows($product_total_result);

                //상품 목록 이동버튼 생성(버튼 기능은 search_result_move.php에 정의됨)
                echo "<form method='get' action='search_result_move.php'>";
                if($_COOKIE['end_count'] > $offset)
                {
                    echo "<input type='submit' value='이전' name='prev_button'>";
                }
                if($_COOKIE['end_count'] < $total_count)
                {
                    echo "<input type='submit' value='다음' name='next_button'>";
                }

                mysqli_close($sql_id);
            }
            echo "</form>";
        ?>
        
        <form method='get'>
            <input type='submit' value='메인 페이지' name='main_page'>
        </form>
        
        <?php
            if($_GET['main_page'] == "메인 페이지"){header("location: main_page.php");}

            //어느 상품의 버튼이 입력되었는지 검사 
            for($i=$_COOKIE['end_count'] - $offset;$i<=$_COOKIE['end_count'];$i++)
            {
                if(isset($_POST['basket-'.$i]))
                {
                    if($_POST['basket-'.$i] == "담기")
                    {
                        setcookie("goods_action", "basket", time()+(3600*12));
                        get_goods_info($_COOKIE['research_result'], $i);
                    }
                    
                    header("location: goods_select.php");
                }
                if(isset($_POST['purchase-'.$i]))
                {
                    if($_POST['purchase-'.$i] == "구매")
                    {
                        setcookie("goods_action", "purchase", time()+(3600*12));
                        get_goods_info($_COOKIE['research_result'], $i);
                    }
                    
                    header("location: goods_select.php");
                }
            }
        ?>

        <?php
            //간이 검색 엔진 구현
            function search_goods($sql_id, $search_content)
            {
                return mysqli_query($sql_id, "select num, name, cost from product_unit_info where type_1 = '$search_content'");
            }
            
            function get_goods_info($search_content, $i)
            {
                //데이터베이스 연결
                $sql_id = mysqli_connect('localhost', 'php_project_mananger', '0000', 'php_project_db');
                if($sql_id == false)
                {
                    echo "데이터베이스 연결 실패";
                }
                else
                {
                    //연결 성공
                    //이전과 똑같이 검색 후 같은 항목 번호의 정보 검색 >> 실전에서는 시간차로 인한 오류 때문에 사용할 수 없음 
                    $product_total_result = search_goods($sql_id, $search_content);
                    
                    $j=0;

                    while($product_unit_result = mysqli_fetch_array($product_total_result))
                    {
                        if($i == $j)
                        {
                            setcookie("goods_num", $product_unit_result['num'], time()+(3600*12));
                            setcookie("goods_cost", $product_unit_result['cost'], time()+(3600*12));
                        }
                        $j++;
                    }

                    mysqli_close($sql_id);
                }
            }
        ?>
    </body>
</html>