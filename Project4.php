<?php
include 'db.php';
session_start();

if (!isset($_GET['project_id'])) {
    die("No project selected.");
}

$project_id = $_GET['project_id'];

$query = "SELECT o.ObservationId, o.Subject, o.Description, s.Type AS Severity, o.remarks
          FROM observations o
          JOIN severity s ON o.severityid = s.SeverityId
          JOIN project_module_association pma ON o.projectmoduleid = pma.id
          WHERE pma.project_id = '$project_id'";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>QA Observations</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
            margin-top: 10px;
        }

        .severity-filter {
            text-align: center;
            margin-bottom: 10px;
        }

        .datatable-container {
            width: 90%;
            margin: 0 auto;
            height: 85vh;
            display: flex;
            flex-direction: column;
        }

        div.dataTables_wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        div.dataTables_filter {
            text-align: right;
        }

        div.dataTables_scrollBody {
            flex: 1;
            overflow-y: auto !important;
        }

        div.dataTables_buttons {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2>QA Observations for Project ID <?= $project_id ?></h2>

<div class="severity-filter">
    <label><strong>Filter by Severity:</strong></label><br>
    <select id="severityFilter">
        <option value="">All</option>
        <option value="High">High</option>
        <option value="Medium">Medium</option>
        <option value="Normal">Normal</option>
    </select>
</div>

<div class="datatable-container">
    <table id="obsTable" class="display nowrap">
        <thead>
            <tr>
                <th>Observation ID</th>
                <th>Subject</th>
                <th>Description</th>
                <th>Severity</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['ObservationId'] ?></td>
                    <td><?= $row['Subject'] ?></td>
                    <td><?= $row['Description'] ?></td>
                    <td><?= $row['Severity'] ?></td>
                    <td><?= $row['remarks'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        var table = $('#obsTable').DataTable({
            scrollY: "calc(100vh - 280px)",
            scrollCollapse: true,
            paging: true,
            dom: '<"top"f>rt<"bottom"ipB><"clear">',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });

        // Severity filter
        $('#severityFilter').on('change', function () {
            const severity = $(this).val();
            table.column(3).search(severity).draw(); // Severity column is index 3
        });

        // Restrict global search to Subject column only (index 1)
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            let input = $('.dataTables_filter input').val();
            if (!input) return true;

            const subject = data[1]; // Subject column
            return subject.toLowerCase().includes(input.toLowerCase());
        });

        $('.dataTables_filter input').on('keyup', function () {
            table.draw();
        });

        // Optional: Change search label
        $('.dataTables_filter label').contents().filter(function(){
            return this.nodeType === 3;
        }).first().replaceWith('Search Subject: ');
    });
</script>

</body>
</html>
