<!-- user表單 -->
<meta charset="UTF-8">
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
            echo "Connected successfully"; //顯示代表成功連接後端，之後可以刪掉
        } catch (PDOException $e) {
            echo "Database connection failed: " . $e->getMessage();
        }
    ?>

    <!-- 查醫院存量，當初是想說是選個時段，只要那時間之後有位置都會顯示，也可以改 -->
    <!-- 目前有對應到醫院選項，但我的電腦好像顯示不出中文...？ -->
    <h2>查詢醫院存量：</h2>
    <div class="user-container">
    <form id="searchForm" action="">
    <label for="hospital">Hospital:</label>
    <label for="hospital">Hospital:</label>
    <select id="hospital" name="hospital">
        <?php
            $selectedHospital = $_POST['hospital'] ?? ''; // 從表單提交後取得用戶選擇的醫院名稱
            try {
                $stmt = $pdo->query("SELECT Location_name FROM vaccination_location");
                if ($stmt) {
                     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($row['Location_name'] == $selectedHospital) ? 'selected' : '';
                        echo "<option value='" . $row['Location_name'] . "' $selected>" . $row['Location_name'] . "</option>";
                    }
                } else {
                    echo "No results found.";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        ?>
    </select><br><br>

    <label for="timeSlot">時段:</label>
    <select id="time" name="time">
        <!-- 下面JavaScript 生成時段選項 -->
    </select><br><br>


    <script>
        // JavaScript 生成時段選項
        const hospitalSelect = document.getElementById('hospital');
        const timeSelect = document.getElementById('time');

        hospitalSelect.addEventListener('change', () => {
            const selectedHospital = hospitalSelect.value;

            // 需要寫一個後端的 PHP 腳本來回傳對應醫院的時間範圍，我目前是直接列出選項
            const fakeTimeData = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00']; // 假設的時間範圍，實際應由後端提供

            // 清空原先的選項
            timeSelect.innerHTML = '';

            // 生成新的選項
            fakeTimeData.forEach(time => {
                const option = document.createElement('option');
                option.value = time;
                option.textContent = time;
                timeSelect.appendChild(option);
            });
        });
    </script>

    <input type="button" value="查詢 Slot Capacity" onclick="fetchSlotCapacity()">
    <p id="slotCapacityResult"></p>
    <div id="slotCapacityResult"></div>
    
    
    <script>
        // 上面按鈕的javascript
        function fetchSlotCapacity() {
            const selectedHospital = hospitalSelect.value; // 從前端取得用戶選擇的醫院名稱
            const selectedTime = timeSelect.value; // 從前端取得用戶選擇的時間範圍

            // 使用 fetch 函數向後端發送請求，這是模版，如果要用，your_backend_script.php記得要改！
            fetch(`your_backend_script.php?hospital=${selectedHospital}&time=${selectedTime}`)
                .then(response => response.json())
                .then(data => {
                    const slotCapacityResult = document.getElementById('slotCapacityResult');
                    slotCapacityResult.textContent = `Slot Capacity: ${data.slot_capacity}`;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script></div><br><br><br><br><br>
 




    <h2>預約疫苗表單：</h2>

    <div class="user-container">
        <form id="profileForm" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name"><br><br>
            <label for="number">健保卡號:</label>
            <input type="text" id="health_card_number" name="health_card_number"><br><br>
            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select><br><br>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone"><br><br>
            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" name="birthday"><br><br>
            <label for="address">Address:</label>
            <input type="text" id="address" name="address"><br><br>

            <input type="button" value="預約" onclick="saveReservation()">
        </form>
    </div>
    <script>
        //按鈕的javascript，按下後要上傳到後端
        function saveReservation() {
            // 預約時輸入資料，可能要改一下對應的後端名字
            var name = document.getElementById('name').value;
            var name = document.getElementById('health_card_number').value;
            var gender = document.getElementById('gender').value;
            var phone = document.getElementById('phone').value;
            var birthday = document.getElementById('birthday').value; 
            var address = document.getElementById('address').value; 

            console.log("Name: " + name);
            console.log("health_card_number: " + name);
            console.log("Gender: " + gender);
            console.log("Phone: " + phone);
            console.log("Birthday: " + birthday); 
            console.log("Address: " + address); 
    }
    </script><br><br><br><br><br>




    <!-- 這裡我想了一下，應該可以用user_id查詢記錄(我在admin2有寫)，只是後面回報副作用跟feedback要對應user_id上傳-->
    <!-- 目前可以查到user資料，後面對應feedback跟回饋還沒動-->
    <h2>預約記錄：</h2>
    <div class="user-container">
        <form id="appointmentForm" action="">
            <label for="userId">User ID:</label>
            <input type="text" id="userId" name="userId"><br><br>
            <input type="button" value="確定" onclick="getUserAppointment()">
        </form>

        <div id="appointmentResult"></div><br><br><br>

        <input type="text" placeholder="在這裡輸入您的feedback...">
        <input type="button" value="feedback">
        
        <input type="text" placeholder="在這裡輸入您的副作用...">
        <input type="button" value="回報副作用">  
    </div>

    <script>
        //這裡是查後端紀錄的程式碼
        function getUserAppointment() {
            const userId = document.getElementById('userId').value;
            const appointmentResult = document.getElementById('appointmentResult');

            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        const userData = JSON.parse(xhr.responseText);
                        appointmentResult.innerHTML = JSON.stringify(userData, null, 2);
                    } else {
                        appointmentResult.innerHTML = 'Error occurred.';
                    }
                }
            };

            xhr.open('GET', 'get_user_appointment.php?userId=' + userId, true);
            xhr.send();
        }
    </script>
</script>

