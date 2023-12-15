<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="ad.css"> 

<head>
    <meta charset="UTF-8">
    <title>User Appointments</title>
</head>

<body>
    <h2>Select Appointment by User ID:</h2>
    <div class="admin-container">
        <form id="appointmentForm" action="">
            <label for="userId">User ID:</label>
            <input type="text" id="userId" name="userId"><br><br>
            <input type="button" value="確定" onclick="getUserAppointment()">
        </form><br>

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


    <h2>Select Location by ID:</h2>
    <div class="admin-container">
        <form id="locationForm" action="">
            <label for="locationId">Location ID:</label>
            <input type="text" id="locationId" name="locationId"><br><br>
            <input type="button" value="Get Location Details" onclick="getLocationDetails()">
        </form><br>

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
