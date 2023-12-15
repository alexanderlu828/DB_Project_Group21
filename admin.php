<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="ad.css"> 
    <?php
        $host = 'localhost';
        $port = 5432; // remember to replace your own connection port
        $dbname = 'postgres'; // remember to replace your own database name
        $user = 'postgres'; // remember to replace your own username 
        $password = trim(file_get_contents('db_password.txt')); // remember to replace your own password 

        $pdo = null;
        try {
            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully";
        } catch (PDOException $e) {
            echo "Database connection failed: " . $e->getMessage();
        }
    ?>

<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
    <link rel="stylesheet" href="vvv.css"> <!-- 引入 CSS 文件 -->
</head>

<body>
<!-- 管理資料表單 -->
    <h1>管理資料：</h1>
    <div class="admin-container">
        <h2>加上新地點:</h2>
        <form id="adminForm" action="">
            <label for="id">地點編號:</label>
            <input type="text" id="id" name="id"><br><br>
            <label for="name">名稱:</label>
            <input type="text" id="name" name="name"><br><br>
            <label for="address">地址:</label>
            <input type="text" id="address" name="address"><br><br>
            <input type="button" value="Save" onclick="saveData()">
        </form>
    </div><br>

    <div class="admin-container">
        <h2>上傳csv檔案:</h2>
        <input type="file" id="csvFile" accept=".csv">
        <button type="button" onclick="uploadCSV()">Upload CSV</button>

        <script>
            function uploadCSV() {
                const fileInput = document.getElementById('csvFile');
                const formData = new FormData();
                // Append the selected file to the FormData object
                formData.append('csvFile', fileInput.files[0]);
                // Make a POST request to the Flask backend
                fetch('/upload_csv', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    // Handle the response from the backend
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        </script>
</div><br>





<script>
function saveData() {
    var id = document.getElementById('id').value;
    var name = document.getElementById('name').value;
    var address = document.getElementById('address').value;

    console.log("地點編號: " + id);
    console.log("名稱: " + name);
    console.log("地址: " + address);
    // 要將資料發送到後端進行處理
}
</script><br><br><br><br><br>

<div class="admin-container">
    <h2>刪除地點:</h2>
    <form id="adminForm" action="">
        <label for="id">地點編號:</label>
        <select id="id" name="id">
            <?php
            // 連接資料庫
            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 從資料庫中獲取 location_id
            $stmt = $pdo->query("SELECT location_id FROM vaccination_location");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . $row['location_id'] . "'>" . $row['location_id'] . "</option>";
            }
            ?>
        </select><br><br>
        <input type="button" value="刪除地點">
    </form>
</div><br><br><br><br><br>



<div class="admin-container">
    <h2>更改特定地點存量:</h2>
    <form id="adminForm" action="">
        <label for="id">地點編號:</label>
        <select id="id" name="id">
            <?php
            // 連接資料庫
            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 從資料庫中獲取 location_id
            $stmt = $pdo->query("SELECT location_id FROM vaccination_location");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . $row['location_id'] . "'>" . $row['location_id'] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="numericInput">更改存量:</label>
        <input type="number" id="numericInput" name="numericInput"><br><br>
        
        <input type="button" value="更改存量">
    </form>
</div><br><br><br><br><br>

</body>
</html>
