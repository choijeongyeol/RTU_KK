<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>입고지시 추가</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    // 제품 목록 가져오기
    $.get("get_products.php", function(data){
        var options = '';
        data.forEach(function(product){
            options += '<option value="' + product.item_id + '">' + product.item_name + '</option>';
        });
        $('#product_select').html(options);
    });

    // 창고 목록 가져오기
    $.get("get_warehouses.php", function(data){
        var options = '';
        data.forEach(function(warehouse){
            options += '<option value="' + warehouse.id + '">' + warehouse.warehouse_name + '</option>';
        });
        $('#warehouse_select').html(options);
    });

    // 입고지시 추가
    $('#add_order_button').click(function(){
        var product_id = $('#product_select').val();
        var warehouse_id = $('#warehouse_select').val();
        var planned_quantity = $('#planned_quantity').val();

        // 데이터 유효성 검사
        if(product_id == '' || warehouse_id == '' || planned_quantity == '') {
            alert('모든 필드를 입력하세요.');
            return;
        }

        // 서버로 데이터 전송
        $.post("add_inbound_order.php", { product_id: product_id, warehouse_id: warehouse_id, planned_quantity: planned_quantity }, function(response){
            //alert(response);
            // 추가 작업 수행 (예: 목록 갱신 등)
        });
    });
});
</script>
</head>
<body>

<h2>입고지시 추가</h2>

<div>
    <label for="product_select">제품 선택:</label>
    <select id="product_select">
        <option value="">제품을 선택하세요.</option>
    </select>
</div>
<div>
    <label for="warehouse_select">창고 선택:</label>
    <select id="warehouse_select">
        <option value="">창고를 선택하세요.</option>
    </select>
</div>
<div>
    <label for="planned_quantity">예정 수량:</label>
    <input type="text" id="planned_quantity" placeholder="예정 수량을 입력하세요.">
</div>
<div>
    <button id="add_order_button">입고지시 추가</button>
</div>

</body>
</html>
