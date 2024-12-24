<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title> </title>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
 
<script>
$(document).ready(function(){	
	//alert("test");  https://velog.io/@sanna422/jQuery-%EB%AC%B8%EB%B2%95-%EC%A0%95%EB%A6%AC-%EB%B0%8F-%EC%8B%A4%EC%8A%B5
	
	
	$("#p0").click(function(){
	$(this).hide();
	});	
	
	
	$("#p1").mouseenter(function(){ // 
	alert("You entered p1!");
	});	
	
	$("#p2").mouseleave(function(){ // 
	alert("You mouseleave p2!");
	});	
	
	$("#p3").mousedown(function(){ // 
	alert("You mousedown p3!");
	});	
	
	$("#p4").mouseup(function(){ // 
	alert("You mouseup");
	});	
	
	$("#p5").hover(function(){
	alert("You entered p5!");
	},
	function(){
	alert("Bye! You now leave p5!");
	});	
	
	$("input").focus(function(){
	  $(this).css("background-color", "#ff0000");
	});	
	
	$("input").blur(function(){
	  $(this).css("background-color", "#006677");
	});	
	
	
	$("#p8").on({
	mouseenter: function(){
	  $(this).css("background-color", "#ffff00");
	},
	mouseleave: function(){
	  $(this).css("background-color", "#ff0000");
	},
	click: function(){
	  $(this).css("background-color", "#5a8ff3");
	}
	});	
	
	$("#hide").click(function(){
	  $("#p8").hide();
	});
	$("#show").click(function(){
	  $("#p8").show();
	});	
	
	$("#p9").click(function(){
	  $("#p8").toggle();
	});	
	
	$("#p10").click(function(){
	  $("#p8").fadeIn();
	  $("#p7").fadeIn("slow");
	  $("#p6").fadeIn(3000);
	});	
	
		
	$("#p11").click(function(){
	   $("#p10").slideDown();
	});	
		
		
	$("#btn1").click(function(){
	  alert("Text: " + $("#test1").text());
	});
	$("#btn2").click(function(){
	  alert("HTML: " + $("#test2").html());
	});		
		
	$("#btn3").click(function(){
	  alert($("#test3").attr("href"));
	});		
			
});
</script>

<style>
    .bl{margin-bottom:3px}
    .ib{display:inline-block}
	.pub{ background:#ffd793;width:200px;height:50px;}
	.comment{width:900px;height:50px;background:#c2e8f8}
</style>

</head>
<body>
<h1>1. jQuery Selectors</h1>
<xmp>
Query 선택기는 이름, ID, 클래스, 유형, 속성, 속성 값 등을 기반으로 HTML 요소를 "찾기"(또는 선택)하는 데 사용됩니다.
jQuery의 모든 선택자는 달러 기호와 괄호 $()로 시작합니다.

요소 선택기
jQuery 요소 선택기는 요소 이름을 기반으로 요소를 선택합니다.
다음 <p>과 같이 페이지의 모든 요소를 선택할 수 있습니다 .

$("p")
#id 선택기
jQuery 선택기는 HTML 태그의 id 속성을 사용하여 특정 요소를 찾습니다.

$("#test")
.class 선택기
jQuery .class선택기는 특정 클래스의 요소를 찾습니다.

$(".test")
</xmp>

<hr>



<h1>2. jQuery Events</h1>
<xmp>
웹 페이지가 응답할 수 있는 모든 다양한 방문자의 작업을 이벤트라고 합니다.

다음은 몇 가지 일반적인 DOM 이벤트입니다.

Mouse Events	Keyboard Events	Form Events	Document/Window Events
click	keypress	submit	load
mouseenter	keyup	focus	scroll
mouseleave		blur	unload

일반적으로 사용되는 jQuery 이벤트 메서드
$(document).ready()
이 $(document).ready()메서드를 사용하면 문서가 완전히 로드되었을 때 함수를 실행할 수 있습니다.

click()
사용자가 HTML 요소를 클릭하면 함수가 실행됩니다.
</xmp>

<div class="bl">
	<div id="p0" class="pub ib">click</div> <div class="comment ib" >사용자가 HTML 요소를 클릭하면 함수가 실행됩니다.</div>
</div>
 
<div class="bl"> 
	 <div id="p1" class="pub ib">mouseenter </div> <div class="comment ib" >이 함수는 마우스 포인터가 HTML 요소에 들어갈 때 실행됩니다.</div>
</div>
<div class="bl"> 
	<div id="p2" class="pub ib">mouseleave</div> <div class="comment ib" >이 함수는 마우스 포인터가 HTML 요소를 떠날 때 실행됩니다.</div>
</div> 
<div class="bl"> 
	<div id="p3" class="pub ib">mousedown</div> <div class="comment ib" >HTML 요소 위에 마우스가 있는 동안 왼쪽, 가운데 또는 오른쪽 마우스 버튼을 누르면 함수가 실행됩니다.</div>
</div> 
<div class="bl"> 
	<div id="p4" class="pub ib">mouseup</div> <div class="comment ib" >HTML 요소 위에 마우스가 있는 동안 왼쪽, 가운데 또는 오른쪽 마우스 버튼을 놓으면 함수가 실행됩니다.</div>
</div> 
<div class="bl"> 
	<div id="p5" class="pub ib">hover</div> <div class="comment ib" > mouseenter()및 mouseleave() 메서드 의 조합입니다 .</div>
</div> 
 
<div class="bl"> 
	<div id="p6" class="pub ib">focus <input type="text" name=""></div> <div class="comment ib" >이 함수는 양식 필드에 포커스가 있을 때 실행됩니다.</div>
</div> 
 
 
<div class="bl"> 
	<div id="p7" class="pub ib">blur <input type="text" name=""></div> <div class="comment ib" >이 함수는 양식 필드가 포커스를 잃을 때 실행됩니다.</div>
</div> 
 
  
<div class="bl"> 
	<div id="p8" class="pub ib">on<input type="text" name=""></div> <div class="comment ib" >이 on()메서드는 선택한 요소에 대해 하나 이상의 이벤트 핸들러를 연결합니다.</div>
</div> 
 <h1>3. jQuery Effects</h1>
<div class="bl"> 
	<div id="hide" class="pub ib">hide()및 show()</div> <div class="comment ib" >jQuery를 사용하면 hide()를 사용하여 HTML 요소를 숨김 표시할 수 있습니다 .</div>
</div> 
 
 
<div class="bl"> 
	<div id="show" class="pub ib">hide()및 show()</div> <div class="comment ib" >jQuery를 사용하면 show()메서드를 사용하여 HTML 요소를 표시할 수 있습니다 .</div>
</div> 
 
   
<div class="bl"> 
	<div id="p9" class="pub ib">toggle </div> <div class="comment ib" >toggle()메서드 를 사용하여 요소를 숨기거나 표시하는 사이를 전환할 수도 있습니다 .
표시된 요소는 숨겨지고 숨겨진 요소는 표시됩니다.</div>
</div> 
 
   
<div class="bl"> 
	<div id="p10" class="pub ib">Fade </div> <div class="comment ib" >jQuery를 사용하면 요소를 페이드 인 및 페이드 아웃할 수 있습니다.
jQuery에는 다음과 같은 페이드 메서드가 있습니다.

fadeIn() -숨겨진 요소를 페이드 인하는 데 사용됩니다.
fadeOut()-보이는 요소를 페이드 아웃하는 데 사용됩니다.
fadeToggle()- fadeIn()및 fadeOut() 메소드 사이를 토글 합니다.
fadeTo()- 주어진 불투명도(0과 1 사이의 값)로 페이드를 허용합니다.</div>
</div> 
 
 
   
<div class="bl"> 
	<div id="p11" class="pub ib">Slide </div> <div class="comment ib" >jQuery를 사용하면 요소에 슬라이딩 효과를 만들 수 있습니다.
jQuery에는 다음과 같은 슬라이드 메서드가 있습니다.

slideDown()- 요소를 아래로 슬라이드하는 데 사용됩니다.
slideUp()- 요소를 위로 슬라이드하는 데 사용됩니다.
slideToggle()-slideDown()및 slideUp() 메소드 사이를 토글 합니다.</div>
</div> 


<h1>4. jQuery HTML</h1>

<xmp>
jQuery Get
jQuery에는 HTML 요소와 속성을 변경하고 조작하는 강력한 방법이 포함되어 있습니다.

콘텐츠 가져오기 - text(), html() 및 val()
DOM 조작을 위한 간단하지만 유용한 세 가지 jQuery 메서드는 다음과 같습니다.

text() - 선택한 요소의 텍스트 내용을 설정하거나 반환합니다.
html() - 선택한 요소(HTML 마크업 포함)의 내용을 설정하거나 반환합니다.
val() - 양식 필드의 값을 설정하거나 반환합니다.	
	
</xmp>


<div class="bl"> 
	<div id="btn1" class="pub ib">콘텐츠 가져오기 - text() </div> <div class="comment ib" id="test1"> <b>abcdefg</b> </div>
</div> 

<div class="bl"> 
	<div id="btn2" class="pub ib">콘텐츠 가져오기 - html() </div> <div class="comment ib" id="test2"> <b>abcdefg</b>  </div>
</div> 
 
<div class="bl"> 
	<div id="btn3" class="pub ib">attr()</div> <div class="comment ib" > jQuery attr()메서드는 속성 값을 가져오는 데 사용됩니다.   <a href="http://www.naver.com" id="test3">링크연습</a>  </div>
</div> 
 
 <xmp>
 jQuery Add
새 HTML 콘텐츠 추가
새 콘텐츠를 추가하는 데 사용되는 4가지 jQuery 메서드를 살펴보겠습니다.

append() - 선택한 요소의 끝에 내용을 삽입합니다.
prepend() - 선택한 요소의 시작 부분에 내용을 삽입합니다.
after() - 선택한 요소 뒤에 내용 삽입
before() - 선택한 요소 앞에 내용 삽입

jQuery Remove
요소/콘텐츠 제거
요소와 콘텐츠를 제거하기 위해 주로 두 가지 jQuery 메서드가 있습니다.

remove() - 선택한 요소(및 해당 하위 요소)를 제거합니다.
empty() - 선택한 요소에서 자식 요소를 제거합니다.
</xmp>

<h1>5. jQuery AJAX</h1>
<xmp>
jQuery load() 메서드
jQuery load()메서드는 간단하지만 강력한 AJAX 메서드입니다.
이 load()메서드는 서버에서 데이터를 로드하고 반환된 데이터를 선택한 요소에 넣습니다.

$(selector).load(URL,data,callback);
필수 URL 매개변수는 로드하려는 URL을 지정합니다.
선택적 data 매개변수는 요청과 함께 보낼 쿼리 문자열 키/값 쌍 세트를 지정합니다.
선택적 callback 매개변수는 load()메소드가 완료된 후 실행할 함수의 이름입니다 .
jQuery Get/Post
HTTP 요청: GET 대 POST
클라이언트와 서버 간의 요청-응답에 일반적으로 사용되는 두 가지 방법은 GET 및 POST입니다.

GET - 지정된 리소스에서 데이터를 요청합니다.
POST - 처리할 데이터를 지정된 리소스에 제출합니다.

GET은 기본적으로 서버에서 일부 데이터를 가져오기(검색)하는 데 사용됩니다.

POST를 사용하여 서버에서 일부 데이터를 가져올 수도 있습니다. 그러나 POST 메서드는 데이터를 캐시하지 않으며 요청과 함께 데이터를 보내는 데 자주 사용됩니다.

jQuery $.get() 메서드
이 $.get()메서드는 HTTP GET 요청으로 서버에서 데이터를 요청합니다.

$.get(URL,callback);
Query $.post() 메서드
이 $.post()메서드는 HTTP POST 요청을 사용하여 서버에서 데이터를 요청합니다.

$.post(URL,data,callback);
 </xmp>
 


</body>
</html>
