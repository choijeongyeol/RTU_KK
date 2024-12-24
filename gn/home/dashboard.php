<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/gn/inc/fn.php');
// 사용자 권한 확인

$user_role = $userManager->checkUserRole();

?>

<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/gn/inc/head.php'); ?>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/gn/inc/topmenu.php'); ?>


<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/gn/inc/sidebar_menu.php'); ?>    
<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/gn/inc/top_navigation.php'); ?>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/gn/home/home_permission.php'); ?>

<!-- 게시판 리스트 계산 start -->
<?php
// 검색결과 추가조건 sql
$add_condition = "";
if (!empty($_POST['SearchString'])) {
    $add_condition = " and a." . $_POST['search'] . " like '%" . $_POST['SearchString'] . "%'";
}

if ($_SESSION['admin_role'] < 100) {
    $list_condition = " wms_admin a join wms_admin_cate c on a.admin_role = c.cate_admin_role and a.admin_role < 100 " . $add_condition; // 개발모드 10 미만 전체조회
} else {
    $list_condition = " wms_admin a join wms_admin_cate c on a.admin_role = c.cate_admin_role " . $add_condition; // 개발모드 10 미만 전체조회
}
$totalcount = list_total_cnt($list_condition); // 목록 전체 카운트
?>      
<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/gn/inc/paging_cnt.php'); ?>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/gn/inc/search.php'); ?>            
<!-- 게시판 리스트 계산 end -->

<script>
    function popup_win(arg1, arg2 = null) {
        // 자식 창을 열고 창 객체를 저장
        var childWindow = window.open('ctrl_' + arg1 + '_input.php' + (arg2 ? '?arg2=' + arg2 : ''), '자식 창', 'width=400,height=300');

        // 부모 창에서 자식 창으로 데이터 전달
        if (childWindow) {
            var dataToSend = prompt('부모 창에서 전달할 데이터를 입력하세요:');
            childWindow.postMessage(dataToSend, '*');
        }
    }
</script>

<script src="/vendors/Chart.js/dist/Chart.js"></script><!-- 그래프 js -->

<!-- page content -->
<div class="right_col" role="main" style="background:#FFF">
    <div class="">
        <h2 style="margin-bottom:20px;margin-top:60px">Warehouse Management System Dashboard</h2>

        <!-- <h3>창고 Info </h3> -->
        <div class="container_flex">
            <? if ($pm_R_HOME_item_history == "T") { require_once($_SERVER['DOCUMENT_ROOT'] . '/gn/home/graph_input_history.php'); } ?>
            <? if ($pm_R_HOME_item_history == "T") { require_once($_SERVER['DOCUMENT_ROOT'] . '/gn/home/graph_output_history.php'); } ?>
        </div>
        <br><br><br><br>
        <!-- <hr> -->
        <a name="HISTORY_SEARCH"></a>
        <? if ($pm_R_history == 'T') { require_once($_SERVER['DOCUMENT_ROOT'] . '/gn/home/history_list.php'); } ?>

        <div class="container_flex">            
            <? if ($pm_R_warehouse == "T") { require_once($_SERVER['DOCUMENT_ROOT'] . '/gn/home/warehouse_list.php'); } ?>
            <? if ($pm_R_item == "T") { require_once($_SERVER['DOCUMENT_ROOT'] . '/gn/home/item_list.php'); } ?>     
        </div>
    </div><!--  class="" -->
</div><!--  class="right_col" role="main"> -->
<!-- /page content -->

<? 		include_once($_SERVER['DOCUMENT_ROOT'] . '/gn/inc/foot.php'); ?>
