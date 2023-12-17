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
        <!-- 加入新地點的表單 -->
        <h2>加上新地點:</h2>
        <form id="adminForm" action="">
            <label for="id">地點編號:</label>
            <input type="text" id="id" name="id"><br><br>
            <label for="name">名稱:</label>
            <input type="text" id="name" name="name"><br><br>
            <label for="address">地址:</label>
            <input type="text" id="address" name="address"><br><br>
            <label for="capacity">時段人數:</label>
            <input type="number" id="capacity" name="capacity"><br><br>
            <label for="startTime">開始時間:</label>
            <input type="time" id="startTime" name="startTime"><br><br>
            <label for="endTime">結束時間:</label>
            <input type="time" id="endTime" name="endTime"><br><br>
            <input type="button" value="Save" onclick="saveData()">
        </form>
    </div><br>
    
    <!-- 加入新地點的表單用的funtion -->
    <script>
        // 要將資料發送到後端進行處理
        function saveData() {
            const id = document.getElementById('id').value;
            const name = document.getElementById('name').value;
            const address = document.getElementById('address').value;
            const capacity = document.getElementById('capacity').value;
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
        
            const data = {
                location_id: id,
                location_name: name,
                location_address: address,
                slot_capacity: capacity,
                service_start_time: startTime,
                service_end_time: endTime
            };
        
            fetch('your_backend_endpoint', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                // Handle response from the backend
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script><br><br><br>

    
    <!-- 上傳csv的表單 已完成 -->
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


<br><br>



<!-- 刪除地點的表單 -->  
<!-- 這裡已經成功取到資料表中的location_id，要寫刪除選擇的location_id-->  
<div class="admin-container">
    <h2>刪除地點:</h2>
    <form id="adminForm" action="">
        <label for="id">地點編號:</label>
        <select id="id" name="id">
            <?php
            // 連接資料庫
            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 這裡是從資料庫中獲取 location_id
            $stmt = $pdo->query("SELECT location_id FROM vaccination_location");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . $row['location_id'] . "'>" . $row['location_id'] . "</option>";
            }
            ?>
        </select><br><br>
        <input type="button" value="刪除地點">
    </form>
</div><br><br><br><br><br>




    
<!-- 更改特定地點存量的表單 -->  
<!-- 這個也是可以存取location_id了(表單是vaccine_inventory)，但還不能取vaccine_id來改current_inventory --> 
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
            $stmt = $pdo->query("SELECT location_id FROM vaccine_inventory");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . $row['location_id'] . "'>" . $row['location_id'] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="vaccine_id">疫苗編號:</label>
        <select id="vaccine_id" name="vaccine_id">
            <!-- 這裡的內容可以直接取（跟上面一樣取法），或寫後端取所有對應的location_id的vaccine_id選項？可能前者比較好 -->
        </select><br><br>

        <label for="numericInput">更改存量:</label>
        <input type="number" id="numericInput" name="numericInput"><br><br>
        
        <input type="button" value="更改存量">
    </form>
</div><br><br><br><br><br>

</body>
</html>
