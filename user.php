<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vaccine System</title>
</head>
<body>
<!-- user表單 -->
<meta charset="UTF-8">
<link rel="stylesheet" href="ad.css"> 
    <?php
        $host = 'localhost';
        $port = 5432; // remember to replace your own connection port
        $dbname = 'Vaccine_system'; // remember to replace your own database name
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

    <h2>查詢醫院存量：</h2>

    <div class="user-container">
    <form id="searchForm" action="">
    <label for="hospital">預約接種地點：</label>
    <select id="hospital" name="hospital">
        <?php
            $selectedHospital = $_POST['hospital'] ?? ''; // 從表單提交後取得用戶選擇的醫院名稱
            try {
                $stmt = $pdo->query("SELECT location_name FROM vaccination_location");
                if ($stmt) {
                     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($row['location_name'] == $selectedHospital) ? 'selected' : '';
                        echo "<option value='" . $row['location_name'] . "' $selected>" . $row['location_name'] . "</option>";
                    }
                } else {
                    echo "No results found.";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        ?>
    </select><br><br>

    <label for="timeSlot">時段：</label>
    <select id="time" name="time">
        <!-- JavaScript 生成時段選項 -->
    </select><br><br>

    <script>
        const hospitalSelect = document.getElementById('hospital');
        const timeSelect = document.getElementById('time');

        hospitalSelect.addEventListener('change', () => {
            const selectedHospital = hospitalSelect.value;

            // 使用 fetch 函數向後端發送請求
            fetch(`get_time_slots.php?hospital=${selectedHospital}`)
                .then(response => response.json())
                .then(data => {
                    // 清空原先的選項
                    timeSelect.innerHTML = '';

                    // 生成新的選項
                    data.forEach(time => {
                        const option = document.createElement('option');
                        option.value = time;
                        option.textContent = time;
                        timeSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>

    <input type="button" value="查詢目前剩餘劑量" onclick="fetchSlotCapacity()">
    <div id="slotCapacityResult"></div>
    

    <script>
    function fetchSlotCapacity() {
    const selectedHospital = hospitalSelect.value; // 從前端取得用戶選擇的醫院名稱

    // 使用 fetch 函數向後端發送請求
    fetch(`get_vaccine_inventory.php?hospital=${selectedHospital}`)
        .then(response => response.json())
        .then(data => {
            const slotCapacityResult = document.getElementById('slotCapacityResult');
            
            // 清空現有的顯示結果
            slotCapacityResult.innerHTML = '';

            // 建立一個表格來顯示疫苗和剩餘存量
            const table = document.createElement('table');
            data.forEach(vaccine => {
                const row = table.insertRow();
                const cell1 = row.insertCell(0);
                const cell2 = row.insertCell(1);
                cell1.innerHTML = vaccine.vaccine_name;
                cell2.innerHTML = vaccine.current_inventory;
            });

            slotCapacityResult.appendChild(table);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

    </script></div><br><br>
 




     <h2>預約疫苗表單：</h2>

    <div class="user-container">
        <form id="vaccineReservationForm" action="">
            <label for="name">姓名:</label>
            <input type="text" id="name" name="name"><br><br>

            <label for="userId">身分證字號:</label>
            <input type="text" id="userId" name="userId"><br><br>

            <label for="health_card_number">健保卡號:</label>
            <input type="text" id="health_card_number" name="health_card_number"><br><br>

            <label for="gender">性別:</label>
            <select id="gender" name="gender">
                <option value="male">男性</option>
                <option value="female">女性</option>
                <option value="other">其他</option>
            </select><br><br>

            <label for="phone">電話:</label>
            <input type="text" id="phone" name="phone"><br><br>

            <label for="birthday">生日:</label>
            <input type="date" id="birthday" name="birthday"><br><br>

            <label for="address">住家地址:</label>
            <input type="text" id="address" name="address"><br><br>

            <label for="location_name">接種地點:</label>
            <select id="location_name" name="location_name">
                <?php
                    $selectedHospital = $_POST['hospital'] ?? '';
                    try {
                        $stmt = $pdo->query("SELECT location_name FROM vaccination_location");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $selected = ($row['location_name'] == $selectedHospital) ? 'selected' : '';
                            echo "<option value='" . $row['location_name'] . "' $selected>" . $row['location_name'] . "</option>";
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                ?>
            </select><br><br>

            <label for="vaccination_date">接種日期:</label>
            <input type="date" id="vaccination_date" name="vaccination_date"><br><br>

            <label for="vaccination_time">接種時段:</label>
            <select id="vaccination_time" name="vaccination_time">
                <!-- 從 get_time_slots.php 加載時段 -->
            </select><br><br>

            <label for="vaccine_name">接種疫苗:</label>
            <select id="vaccine_name" name="vaccine_name">
                <!-- 从 get_vaccines.php 加载疫苗选项 -->
            </select><br><br>

            <input type="button" value="提交預約" onclick="submitVaccineReservation()">
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    const locationSelect = document.getElementById('location_name');
    locationSelect.addEventListener('change', function() {
        loadVaccinationTimes();
        loadVaccines(); // 调用加载疫苗列表的函数
    });
    });

    function loadVaccinationTimes() {
        const selectedHospital = document.getElementById('location_name').value;
        const timeSelect = document.getElementById('vaccination_time');

        fetch(`get_time_slots.php?hospital=${selectedHospital}`)
            .then(response => response.json())
            .then(data => {
                timeSelect.innerHTML = '';
                data.forEach(time => {
                    const option = document.createElement('option');
                    option.value = time;
                    option.textContent = time;
                    timeSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function loadVaccines() {
    const selectedHospital = document.getElementById('location_name').value;
    const vaccineSelect = document.getElementById('vaccine_name');

    fetch(`get_vaccines.php?hospital=${selectedHospital}`)
        .then(response => response.json())
        .then(data => {
            vaccineSelect.innerHTML = ''; // 清空当前的选项
            data.forEach(vaccine => {
                const option = document.createElement('option');
                option.value = vaccine;
                option.textContent = vaccine;
                vaccineSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    function submitVaccineReservation() {
    // 获取表单输入字段的值
    var name = document.getElementById('name').value;
    var userId = document.getElementById('userId').value;
    var healthCardNumber = document.getElementById('health_card_number').value;
    var gender = document.getElementById('gender').value;
    var phone = document.getElementById('phone').value;
    var birthday = document.getElementById('birthday').value;
    var address = document.getElementById('address').value;
    var locationName = document.getElementById('location_name').value;
    var vaccinationDate = document.getElementById('vaccination_date').value;
    var vaccinationTime = document.getElementById('vaccination_time').value;
    var vaccineName = document.getElementById('vaccine_name').value;

    // 创建一个 FormData 对象来存储表单数据
    var formData = new FormData();
    formData.append('name', name);
    formData.append('userId', userId);
    formData.append('healthCardNumber', healthCardNumber);
    formData.append('gender', gender);
    formData.append('phone', phone);
    formData.append('birthday', birthday);
    formData.append('address', address);
    formData.append('locationName', locationName);
    formData.append('vaccinationDate', vaccinationDate);
    formData.append('vaccinationTime', vaccinationTime);
    formData.append('vaccineName', vaccineName);

    // 使用 fetch 函数向后端发送请求
    fetch('process_vaccine_reservation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Success:', data);
        // 在这里处理后端响应
        if (data.success) {
            // 预约成功
            document.getElementById('vaccineReservationForm').innerHTML = '<p>預約成功！</p>';
        } else {
            // 预约失败
            alert("預約失敗: " + data.error);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
    }


    </script><br><br>


    <h2>預約記錄：</h2>
    <div class="user-container">
        <form id="appointmentForm">
            <label for="userIdAppointment">身分證字號:</label>
            <input type="text" id="userIdAppointment" name="userId"><br><br>
            <input type="button" value="查詢" onclick="getAppointmentRecords()">
        </form>
        <div id="appointmentRecordsResult"></div>
    </div>

    <script>
        function getAppointmentRecords() {
            const userId = document.getElementById('userIdAppointment').value;
            fetch(`get_appointment_records.php?userId=${userId}`)
            .then(response => response.json())
            .then(data => {
                const resultContainer = document.getElementById('appointmentRecordsResult');
                resultContainer.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(record => {
                        resultContainer.innerHTML += `接種日期：${record.vaccination_date}<br>接種時段：${record.vaccination_time}<br>接種地點：${record.location_name}<br>接種疫苗：${record.vaccine_name}<br><br>`;
                    });
                } else {
                    resultContainer.innerHTML = '未找到預約記錄';
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script><br><br>



    <h2>施打回饋</h2>
    <div class="feedback-container">
        <form id="feedbackForm">
            <label for="userIdFeedback">身分證字號:</label>
            <input type="text" id="userIdFeedback" name="userId"><br><br>

            <label for="feedbackScore">回饋分數:</label>
            <select id="feedbackScore" name="score">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select><br><br>

            <input type="button" value="提交回饋" onclick="submitFeedback()">
        </form>
    </div>

    <script>
        function submitFeedback() {
            const userId = document.getElementById('userIdFeedback').value;
            const score = document.getElementById('feedbackScore').value;

            // AJAX request to a PHP script to handle the submission
            fetch('submit_feedback.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `userId=${userId}&score=${score}`
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                // Handle response here
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script><br><br>


    <h2>副作用回報</h2>
    <div class="side-effect-container">
        <form id="sideEffectForm">
            <label for="userIdSideEffect">身分證字號:</label>
            <input type="text" id="userIdSideEffect" name="userId"><br><br>

            <label for="hadSideEffects">產生副作用:</label>
            <select id="hadSideEffects" name="hadSideEffects">
                <option value="Yes">是</option>
                <option value="No">否</option>
            </select><br><br>

            <label for="severityLevel">嚴重程度:</label>
            <select id="severityLevel" name="severityLevel">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select><br><br>

            <label for="sideEffectType">副作用種類:</label>
            <select id="sideEffectType" name="sideEffectType">
                <option value="Injection site pain">注射部位紅腫痛</option>
                <option value="Fatigue">疲倦</option>
                <option value="Headache">頭痛</option>
                <option value="Muscle pain">肌肉痛</option>
                <option value="Chills">畏寒</option>
                <option value="Joint pain">關節痛</option>
                <option value="Fever">發燒</option>
            </select><br><br>

            <label for="sideEffectStartDate">副作用開始日期:</label>
            <input type="date" id="sideEffectStartDate" name="sideEffectStartDate"><br><br>

            <label for="sideEffectEndDate">副作用結束日期:</label>
            <input type="date" id="sideEffectEndDate" name="sideEffectEndDate"><br><br>

            <input type="button" value="提交回報" onclick="submitSideEffectReport()">
        </form>
    </div>

    <script>
        function submitSideEffectReport() {
            const userId = document.getElementById('userIdSideEffect').value;
            const hadSideEffects = document.getElementById('hadSideEffects').value;
            const severityLevel = document.getElementById('severityLevel').value;
            const sideEffectType = document.getElementById('sideEffectType').value;
            const sideEffectStartDate = document.getElementById('sideEffectStartDate').value;
            const sideEffectEndDate = document.getElementById('sideEffectEndDate').value;

            fetch('submit_side_effect.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `userId=${userId}&hadSideEffects=${hadSideEffects}&severityLevel=${severityLevel}&sideEffectType=${sideEffectType}&sideEffectStartDate=${sideEffectStartDate}&sideEffectEndDate=${sideEffectEndDate}`
            })
            .then(response => response.json())
            .then(data => {
                // 在這裡處理成功或失敗的回應
                console.log('Success:', data);
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
