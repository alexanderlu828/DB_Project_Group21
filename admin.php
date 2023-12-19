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
    <h1>管理資料</h1>
    <div class="admin-container">
        <h2>新增接種地點</h2>
        <form id="adminForm" action="">
            <label for="id">地點編號:</label>
            <input type="text" id="id" name="id"><br><br>
            <label for="name">地點名稱:</label>
            <input type="text" id="name" name="name"><br><br>
            <label for="address">地址:</label>
            <input type="text" id="address" name="address"><br><br>
            <label for="doses">分配劑量:</label>
            <input type="number" id="doses" name="doses"><br><br>
            <label for="startTime">每日開始服務時間:</label>
            <input type="text" id="startTime" name="startTime" placeholder="hh:mm:ss" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]"><br><br>
            <label for="endTime">每日結束服務時間:</label>
            <input type="text" id="endTime" name="endTime" placeholder="hh:mm:ss" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]"><br><br>
            <input type="button" value="確定新增" onclick="saveData()">
            <div id="saveResult"></div>
        </form>
    </div><br>

<div class="admin-container">
    <h2>上傳 csv 檔</h2>
    <input type="file" id="csvFile" accept=".csv">
    <button type="button" onclick="uploadCSV()">確定上傳</button>
    <div id="uploadStatus"></div>

    <script>
        function uploadCSV() {
                const fileInput = document.getElementById('csvFile');
                const formData = new FormData();
                formData.append('csvFile', fileInput.files[0]);

                fetch('uploadFile.php', {
                        method: 'POST',
                        body: formData
                        })
                .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                            return response.json();
                        })
                .then(data => {
                        if (data.success) {
                        document.getElementById('uploadStatus').innerText = '上傳成功！';
                        } else {
                        document.getElementById('uploadStatus').innerText = '上傳失敗：' + data.message;
                        }
                        })
                .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('uploadStatus').innerText = '上傳出現錯誤。';
                        });
            }
    </script>
</div><br>



<script>
function saveData() {
    var id = document.getElementById('id').value;
    var name = document.getElementById('name').value;
    var address = document.getElementById('address').value;
    var doses = document.getElementById('doses').value;
    var startTime = document.getElementById('startTime').value;
    var endTime = document.getElementById('endTime').value;

    var data = new URLSearchParams();
    data.append('id', id);
    data.append('name', name);
    data.append('address', address);
    data.append('doses', doses);
    data.append('startTime', startTime);
    data.append('endTime', endTime);

    fetch('addLocation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: data
    })
    .then(response => response.text())
    .then(responseText => {
        console.log(responseText);
        
        document.getElementById('saveResult').innerText = responseText;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('saveResult').innerText = '保存失敗：' + error;
    });
}

</script><br><br>

<div class="admin-container">
    <h2>刪除接種地點</h2>
    <form id="deleteLocationForm" action="">
        <label for="locationId">地點編號:</label>
        <select id="locationId" name="locationId">
            <?php
            // 連接資料庫
            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 從資料庫中獲取 location_id
            $stmt = $pdo->query("SELECT location_id FROM vaccination_location ORDER BY CAST(location_id AS INTEGER) ASC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . $row['location_id'] . "'>" . $row['location_id'] . "</option>";
            }
            ?>
        </select><br><br>
        <input type="button" value="確認刪除" onclick="deleteLocation()">
        <div id="deleteResult"></div>
    </form>
</div>

<script>
function deleteLocation() {
    var locationId = document.getElementById('locationId').value;

    fetch('deleteLocation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `locationId=${locationId}`
    })
    .then(response => response.text())
    .then(responseText => {
        console.log(responseText);
        document.getElementById('deleteResult').innerText = responseText;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('deleteResult').innerText = '刪除失敗：' + error;
    });
}
</script>

<br><br><br>

<div class="admin-container">
    <h2>更新接種地點資訊</h2>
    <h5>註：若該資訊沒有要更改，輸入原資訊即可</h5>
    <form id="updateLocationForm" onsubmit="event.preventDefault(); updateLocationInfo();">
        <label for="locationId">欲更改的地點編號:</label>
        <select id="locationId" name="locationId">
            <?php
            // 從資料庫中獲取 location_id
            try {
                $stmt = $pdo->query("SELECT location_id FROM vaccination_location ORDER BY CAST(location_id AS INTEGER) ASC");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['location_id'] . "'>" . $row['location_id'] . "</option>";
                }
            } catch (PDOException $e) {
                echo "Database query failed: " . $e->getMessage();
            }
            ?>
        </select><br><br>

        <label for="locationName">名稱:</label>
        <input type="text" id="locationName" name="locationName"><br><br>

        <label for="locationAddress">地址:</label>
        <input type="text" id="locationAddress" name="locationAddress"><br><br>

        <label for="slotCapacity">容納人數上限:</label>
        <input type="number" id="slotCapacity" name="slotCapacity"><br><br>

        <label for="serviceStartTime">開始服務時間:</label>
        <input type="text" id="serviceStartTime" name="serviceStartTime" placeholder="hh:mm:ss" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]"><br><br>

        <label for="serviceEndTime">結束服務時間:</label>
        <input type="text" id="serviceEndTime" name="serviceEndTime" placeholder="hh:mm:ss" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]"><br><br>

        <button type="submit">確認更新</button>
    </form>
    <div id="updateResult"></div>
</div>

<script>
    function updateLocationInfo() {
        const formData = new FormData(document.getElementById('updateLocationForm'));
        fetch('updateLocation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('updateResult').innerText = data.message;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('updateResult').innerText = '更新失敗：' + error;
        });
    }
</script>


<br><br><br>

<div class="admin-container">
    <h2>更新疫苗存量</h2>
    <form id="updateInventoryForm" action="">
        <label for="vaccineId">疫苗編號:</label>
        <select id="vaccineId" name="vaccineId">
            <?php
            // 從資料庫中獲取 vaccine_id
            try {
                $stmt = $pdo->query("SELECT vaccine_id FROM vaccine_info");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['vaccine_id'] . "'>" . $row['vaccine_id'] . "</option>";
                }
            } catch (PDOException $e) {
                echo "Database query failed: " . $e->getMessage();
            }
            ?>
        </select><br><br>
        
        <label for="locationId">地點編號:</label>
        <select id="locationId" name="locationId">
            <?php
            // 從資料庫中獲取 location_id
            try {
                $stmt = $pdo->query("SELECT location_id FROM vaccination_location ORDER BY CAST(location_id AS INTEGER) ASC");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['location_id'] . "'>" . $row['location_id'] . "</option>";
                }
            } catch (PDOException $e) {
                echo "Database query failed: " . $e->getMessage();
            }
            ?>
        </select><br><br>

        <label for="quantityChange">更改量:</label>
        <input type="number" id="quantityChange" name="quantityChange"><br><br>
        
        <input type="button" value="確認更新" onclick="updateVaccineInventory()">

        <div id="updateResult"></div>
    </form>
</div>

<script>
function updateVaccineInventory() {
    var vaccineId = document.getElementById('vaccineId').value;
    var locationId = document.getElementById('locationId').value;
    var quantityChange = document.getElementById('quantityChange').value;

    fetch('updateVaccineInventory.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `locationId=${locationId}&vaccineId=${vaccineId}&quantityChange=${quantityChange}`
    })
    .then(response => response.text())
    .then(responseText => {
        console.log(responseText);
        // 在這裡更新網頁上的元素來顯示成功的消息
        document.getElementById('updateResult').innerText = responseText;
    })
    .catch(error => {
        console.error('Error:', error);
        // 也可以在這裡處理錯誤情況，顯示錯誤消息
        document.getElementById('updateResult').innerText = '更新失敗：' + error;
    });
}
</script>

<br>

</body>
</html>
