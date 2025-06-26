<?php
Ini_set(‘display_errors’,1);
Error_reporting(E_ALL);
Session_start();
If(!isset($_SESSION[‘Empid’]))
{
    Header(“Location:login.php”);
    Exit;
}

If(!isset($_POST[‘projec_series_id’]))
{
    Die(“series id is missing”);
}
$projec_series_id=$_post[‘projec_series_id’];
?>
<html>
    <head>
        <title>
            Card1
        </title>
        <style>
            *{
                Font-family:Times new Roman;
            }
            .card1
            {
                Display: grid;
                Grid-template-rows:repeat(3,1fr);
                Justify-content: center;
                Align-items: center;
                Margin-block-end: 60px;
                Margin-top: 25px;
            }
            .btn
            {
                Margin:18px;
                Padding:18px
            }
            H2
            {
                Text-align: center;
            }
            .col-md-4
            {
                Display:grid;
                Justify-content: center;
                Align-items: center;
                Overflow:hidden;
            }
            Body
            {
                Display:flex;
                Flex-direction:column;
                Min-height: 100vh;
            }
            #main_content
            {
                Flex-grow: 1;
                Padding:20px;
            }
            .btn-success
            {
                Color:#fff;
                Background-color: #1c7feo;
                Border-color:#1c7feo;
            }
        </style>
        <link href=””/>
    </head>
    <body>
        <?php include(“header.php”);?>
        <div id=”main-content”>
            <div class=”card1”>
                <?php
                $servername=”localhost”;
                $username=”name”;
                $password=”password”;
                $dbname=”project”;
                $conn=new mysqli($servername,$username,$password,$dbname);
                If($conn->connect_error)
                {
                    Die(“connection failed” .$conn->connect_error);
                }
                $select_query=mysqli_query($conn,”select * from Project p
                Join Project_Series_Association psa on p.Projects=psa.ProjectId
                Where psa.projec_series_id=$projec_series_id”);
                While($data=mysqli_fetch_array($select_query))
                {
                    ?>
                    <div class=”card mb-3 mt-4” style=”max-width:60px;”>
                        <div class=”card” id=”card <?php echo $data[‘cardId’];?>”>
                            <div class=”row g-0 mr-0 ml-0”>
                                <div class=”col-md-4”>
                                   <img src=”microsat.png” class=”img-fluid rounded-start” alt=”image” width=”155px”>
                                </div>
                                <div class=”col-md-8-p-2”>
                                    <div class=”card-title”>
                                        <span style=”font-size:2rem;font-weight:bold;”><?php echo $data[“ProjectName”];?></span>
                                    </div>
                                    <div class=”card-text”>
                                       <b>Associated Team Members</b>
                                        <?php echo $data[“Team members”];?><br>
                                        <a href=”http” class=”text-dark”>TM document</a>
                                        <a href=”http” class=”text-dark”>TC document</a><br>
                                        Codepath:/home/raghuma/project
                                    </div>
                                    <form action=”project2.php” method=”POST” style=”display:inline;”>
                                        <input type=”hidden” name=”ProjectId” value=”<?php echo $data[‘ProjectId’];?>”>
                                        <input type=”hidden” name=”CardId” value=”<?php echo $data[‘CardId’];?>”>
                                        <button type=”submit” class=”btn btn-primary float-right”>More</button>
                                    </form>
                                </div>
                            </div>
                   </div>
                    <?php }?>
            </div>
        </div>
        <script type=”text/javascript” src=”jquery.datatables.min.js”></script>
        <script src=”bootstrap/bundle.js”></script>
    </body>
</html>



