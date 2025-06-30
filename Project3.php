<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Redirect to login if session not set
if (!isset($_SESSION['Empid'])) {
    header("Location: login.php");
    exit;
}

// Check required POST data
if (!isset($_POST['ProjectId']) || !isset($_POST['CardId'])) {
    die("Project ID or Card ID is missing.");
}

// Store selected project and card
$projectId = $_POST['ProjectId'];
$cardId = $_POST['CardId'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Details</title>
    <link href="stylep2.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">

    <style>
        .highlighted {
            background-color: #d0f0ff !important;
        }
        .dimmed {
            opacity: 0.5;
        }
    </style>
</head>
<body>

<?php include("header.php"); ?>

<div class="main-content">
    <div class="container-fluid1">
        <div class="row">

            <!-- Card List Section -->
            <div class="col-md-4 card-section">
                <?php
                // DB connection
                $conn = new mysqli("localhost", "name", "password", "project");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch all projects
                $select_query = mysqli_query($conn, "SELECT * FROM Projects");
                while ($data = mysqli_fetch_array($select_query)) {
                    $pid = $data['ProjectId'];
                    $is_selected = ($pid == $projectId); // Check if selected
                ?>
                    <div class="card mb-3 mt-4 <?= $is_selected ? 'highlighted' : 'dimmed' ?>"
                         id="card<?= $pid ?>"
                         onclick="highlight(<?= $pid ?>)">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="microsat.png" class="img-fluid rounded-start" alt="image" width="100%">
                            </div>
                            <div class="col-md-8 p-2">
                                <div class="card-title">
                                    <span style="font-size:1.2rem;font-weight:bold;"><?php echo $data["ProjectName"]; ?></span>
                                </div>
                                <div class="card-text">
                                    <b>Team:</b> <?php echo $data["Team members"]; ?><br>
                                    <a href="#">TM doc</a> | <a href="#">TC doc</a><br>
                                    <small>Path: /home/raghuma/project</small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Info Table Section -->
            <div class="col-md-8 info-section" id="info-section">
                <div id="cardTitle">
                    <h2>Project Details</h2>
                </div>

                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>Attribute</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // List of rows
                        $attributes = [
                            "No of NCs from QA",
                            "Requirement coverage",
                            "Design completeness",
                            "Code quality",
                            "Testing status",
                            "Documentation status",
                            "Compliance checks",
                            "QA Observations"
                        ];

                        foreach ($attributes as $index => $attr) {
                            echo "<tr><td>$attr</td><td class='dynamic-column'>";
                            if ($attr == "QA Observations") {
                                // Count observations for selected project
                                $obs_query = "SELECT COUNT(*) AS obs_count 
                                              FROM observations o
                                              JOIN project_module_association pma ON o.projectmoduleid = pma.id
                                              WHERE pma.project_id = '$projectId'";
                                $obs_result = mysqli_fetch_assoc(mysqli_query($conn, $obs_query));
                                $obs_count = $obs_result['obs_count'];
                                echo "<a href='qa_observations.php?project_id=$projectId'>$obs_count Observations</a>";
                            } else {
                                echo "value" . ($index + 1); // placeholder
                            }
                            echo "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JS libraries -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#dataTable').DataTable();
    });

    // Highlight selected card and submit via POST
    function highlight(CardId) {
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            const id = parseInt(card.id.replace('card', ''), 10);
            if (id === CardId) {
                card.classList.add('highlighted');
                card.classList.remove('dimmed');
            } else {
                card.classList.remove('highlighted');
                card.classList.add('dimmed');
            }
        });

        // Send form data using POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'project2.php';

        const input1 = document.createElement('input');
        input1.type = 'hidden';
        input1.name = 'ProjectId';
        input1.value = CardId;

        const input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'CardId';
        input2.value = CardId;

        form.appendChild(input1);
        form.appendChild(input2);
        document.body.appendChild(form);
        form.submit();
    }
</script>

</body>
</html>
