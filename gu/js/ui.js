$(function(){

	function isMobile(){
	 var tmpUser = navigator.userAgent;
	 var isMobile=false;

	 // userAgent값에 iPhone, iPad, iPod, Android 라는 문자열이 하나라도 검색되면, 모바일로 간주함.
	 if (tmpUser.indexOf("iPhone") > 0 || tmpUser.indexOf("iPad") > 0 || tmpUser.indexOf("iPad") > 0 || tmpUser.indexOf("Android") > 0){
	  isMobile=true;
	 } //end if

	 return isMobile;
	 } //end isMobile

	 var isMobileWeb = isMobile();

	 if(isMobileWeb){
		$('html').addClass('mobile');
	 }else{
		 $('html').addClass('pc');
	 } //end if

});