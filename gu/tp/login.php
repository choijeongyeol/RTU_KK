<!DOCTYPE html>
<html lang="ko">
	<head>
	<title>관리자 로그인</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            padding: 20px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input:focus {
            border-color: #007BFF;
        }
        .login-btn {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-btn:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>

	</head>
	<body>                                                     
 
    <div class="login-container">
        <h2>관리자 로그인</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <form action="process_login.php" method="POST">
            <div class="form-group">
                <label for="partner_id">기관 코드</label>
                <input type="text" id="partner_id" name="partner_id" placeholder="부여받은 기관코드 번호를 입력하세요" required>
            </div>
            <div class="form-group">
                <label for="admin_id">관리자 ID</label>
                <input type="text" id="admin_id" name="admin_id" placeholder="관리자 ID를 입력하세요" required>
            </div>
            <div class="form-group">
                <label for="admin_pw">비밀번호</label>
                <input type="password" id="admin_pw" name="admin_pw" placeholder="비밀번호를 입력하세요" required>
            </div>
            <button type="submit" class="login-btn">로그인</button>
        </form>
    </div>
  </body>
 </html> 