<?php
session_start();

if (isset($_SESSION['logging'])) {
?>

<!DOCTYPE html>
<html>

<head>
    <style>
        .welcome {
            position: absolute;
            top: 0;
            right: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    <title>logged in page</title>
</head>

<body>
    <div class="welcome">
        <h3>Welcome Pharmacist, <?php echo $_SESSION["pharmacist_fname"]; ?></h3>
    </div>
    <h2>What do you want to do?</h2>
    <input type="submit" value="Input Drugs" class="inputDrugs" onclick="redirectToDrugsView()">
    <script>
        function redirectToDrugsView() {
            window.location.href = "inputdrugs.html";
        }
    </script>

    <input type="submit" value="View drugs in the db" class="doctorbutton" onclick="redirectToViewDrugs()">
    <script>
        function redirectToViewDrugs() {
            window.location.href = "viewdrugs.php";
        }
    </script>

    <h3>This is all the patient's prescription</h3>
    <table id="patientstable">
        <th>SSN</th>
        <th>patient_fname</th>
        <th>diagnosis</th>
        <th>drug_name</th>
        <th>drug_prize</th>
        <th>dosage</th>
        <th>Dispense</th>

        <?php
        require_once("connection.php");
        echo "<br>";
        $sql = "SELECT * FROM tblprescriptions";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td> {$row['SSN']}</td>  
                    <td> {$row['f_name']} </td>
                    <td> {$row['diagnosis']}</td>
                    <td> {$row['drug_name']}</td>
                    <td> {$row['drug_prize']}</td> 
                    <td> {$row['dosage']}</td> 
                    <td><button onclick='dispenseMedicine(\"{$row['SSN']}\", \"{$row['f_name']}\", \"{$row['drug_name']}\", \"{$row['drug_prize']}\")'>Dispense</button></td>
                 </tr>";
            }
        } else {
            echo "No results";
        }
        $conn->close();
        ?>
    </table>
    <script type='text/javascript' src="pagination.js"></script>

    <script>
        function dispenseMedicine(SSN, f_name, drug_name, drug_prize) {
            // AJAX request to send the dispensing details to the server
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // The request was successful
                        alert("Medicine dispensed for patient with SSN: " + SSN);
                        window.location.reload(); // Reload the page to show updated data
                    } else {
                        // The request failed, handle the error
                        alert("Failed to dispense medicine. Please try again later.");
                    }
                }
            };

            xhr.open("POST", "dispensedrug.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("SSN=" + SSN + "&f_name=" + encodeURIComponent(f_name) + "&drug_name=" + encodeURIComponent(drug_name) + "&drug_prize=" + drug_prize);
        }
    </script>


    <!-- Dispensed drugs history table -->
    <h3>Dispensed Drugs History</h3>
    <table id="dispensedDrugstable">
        <th>SSN</th>
        <th>Patient Name</th>
        <th>Drug Name</th>
        <th>Drug Prize</th>

        <?php
        require_once("connection.php");

        $conn2 = new mysqli($servername, $username, $password, $dbName);
        if ($conn2->connect_error) {
            die("Connection failed: " . $conn2->connect_error);
        }

        $sql2 = "SELECT * FROM tbldrugsdispensed";
      
        $result2 = $conn2->query($sql2);

        if ($result2->num_rows > 0) {
            while ($row2 = $result2->fetch_assoc()) {
                echo "<tr>
                    <td> {$row2['SSN']}</td>  
                    <td> {$row2['f_name']} </td>
                    <td> {$row2['drug_name']}</td>
                    <td> {$row2['drug_prize']}</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No drugs have been dispensed yet.</td></tr>";
        }

        $conn2->close();
        ?>
    </table>
</body>

</html>

<?php
} else {
    header("Location: pharmlogin.html");
}
?>
