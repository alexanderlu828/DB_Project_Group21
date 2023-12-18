<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="ad.css">

<head>
    <meta charset="UTF-8">
    <title>User Appointments</title>
</head>

<body>
    <h2>輸入使用者編號查詢預約記錄</h2>
    <div class="admin-container">
        <form id="appointmentForm" action="">
            <label for="userId">使用者編號:</label>
            <input type="text" id="userId" name="userId"><br><br>
            <input type="button" value="查詢" onclick="getUserAppointment()">
        </form>

        <div id="appointmentResult"></div>
    </div>

    <script>
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
    </script><br><br>


    <h2>查詢施打地點的詳細資訊</h2>
    <div class="admin-container">
        <form id="locationForm" action="">
            <label for="locationId">地點編號:</label>
            <input type="text" id="locationId" name="locationId"><br><br>
            <input type="button" value="查詢" onclick="getLocationDetails()">
        </form>

        <div id="locationResult"></div>
    </div><br><br><br>

    <script>
        

        function getLocationDetails() {
            const locationId = document.getElementById('locationId').value;
            const locationResult = document.getElementById('locationResult');

            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        const locationData = JSON.parse(xhr.responseText);
                        locationResult.innerHTML = JSON.stringify(locationData, null, 2);
                    } else {
                        locationResult.innerHTML = 'Error occurred.';
                    }
                }
            };

            xhr.open('GET', 'get_location_details.php?locationId=' + locationId, true);
            xhr.send();
        }
    </script>
</body>
</html>
