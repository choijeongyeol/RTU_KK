
	  <div class="main_container"  style="background:#2A3F54">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="/gu/home/dashboard.php" class="site_title"><!-- <i class="fa fa-paw"></i> --> <span>WMS Administrator</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="/gu/images/user.png" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2><?echo $_SESSION['admin_id'];?><!-- John Doe --></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu" >
              <div class="menu_section">
                <h3 style="color:#bab8b8">분류 : < <? $result = cate_name(); echo $result[0]['cate_name'];?> ></h3>
                <ul class="nav side-menu">
                  <li><a><i class="fa fa-home"></i> Home <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="/gu/home/dashboard.php">메인화면</a></li>
                      <li><a href="/gu/home/myinfo.php">개인정보수정</a></li>
                      <!-- <li><a href="index3.html">Dashboard3</a></li> -->
                    </ul>
                  </li>
					  
                  <li><a><i class="fa fa-edit"></i> 창고관리 <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="/gu/m02/list.php">창고목록</a></li>
                    </ul>
                  </li>
			  
                  <li><a><i class="fa fa-desktop"></i>제품관리<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="/gu/m03/list.php">제품목록</a></li>
                     <!-- <li><a href="/gu/m03/list_history.php">제품입고 history</a></li> -->
                      <li><a href="/gu/m03/cate_list.php">제품분류 관리</a></li>
                   </ul>
                  </li>
				  
                  <li><a><i class="fa fa-table"></i> 재고관리 <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="/gu/m04/list.php">재고목록</a></li>
                       <!-- <li><a href="/gu/m04/list_not_warehouse.php">재고목록(창고밖)</a></li>	 -->				  
                      <!-- <li><a href="/gu/m04/list_history.php?left_location=2">제품이동 history</a></li> -->
                    </ul>
                  </li>
				  
                  <li><a><i class="fa fa-bar-chart-o"></i> 입고지시관리 <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="/gu/m05/list.php">입고지시</a></li>
                    </ul>
                  </li>		
				  
                  <li><a><i class="fa fa-clone"></i>출고지시관리 <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="/gu/m06/list.php">출고지시</a></li>
                    </ul>
                  </li>
				  
                  <li><a><i class="fa fa-clone"></i>HISTORY 관리<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">

                    </ul>
                  </li>
                </ul>
              </div>
			  <? if ($_SESSION['admin_role'] > 90) {
			   ?>
              <div class="menu_section">
                <h3>Setting <span>분류 91~99전용</span></h3>
                <ul class="nav side-menu">
                  <li><a><i class="fa fa-bug"></i>운영자 관리<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li style="<?echo $permission_R_menu01_list_button?>"><a href="/gu/s01/list.php">운영자 목록</a></li>
                      <li style="<?echo $permission_R_menu01_list_button?>"><a href="/gu/s01/user_list.php">사용자 목록</a></li>
                      <li style="<?echo $permission_R_menu01_menu01_cate_list_button?>"><a href="/gu/s01/cate_list.php">분류명 관리</a></li>
                      <li><a href="/gu/s01/company_list.php">입출고 거래처명관리</a></li>					  
                    </ul>
                  </li>
                  <li><a><i class="fa fa-table"></i> 접근권한관리 <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li style="<?echo $permission_R_menu04_list_button?>"><a href="/gu/s02/list.php?left_location=1">권한관리목록</a></li>
                    </ul>
                  </li>				  
                  <li><a><i class="fa fa-windows"></i>시스템 관리<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="/gu/s03/list.php">시스템설정</a></li>
                      <!-- <li><a href="page_404.html">404 Error</a></li>
                      <li><a href="page_500.html">500 Error</a></li>
                      <li><a href="plain_page.html">Plain Page</a></li>
                      <li><a href="login.html">Login Page</a></li>
                      <li><a href="pricing_tables.html">Pricing Tables</a></li> -->
                    </ul>
                  </li>
                  <li><a><i class="fa fa-sitemap"></i>참고 매뉴얼<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="#level1_1">Level One</a>
                        <li><a>Level One<span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li class="sub_menu"><a href="level2.html">Level Two</a>
                            </li>
                            <li><a href="#level2_1">Level Two</a>
                            </li>
                            <li><a href="#level2_2">Level Two</a>
                            </li>
                          </ul>
                        </li>
                        <li><a href="#level1_2">Level One</a>
                        </li>
                    </ul>
                  </li>                  
                  <li><a href="javascript:void(0)"><i class="fa fa-laptop"></i> Landing Page <span class="label label-success pull-right">Coming Soon</span></a></li>
                </ul>
              </div>
			  <?} // if ($_SESSION['admin_role'] > 5) ?>

            </div>

          </div>
        </div>