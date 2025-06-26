<?php

ini_set('display_errors',1);

error_reporting(E_ALL);



session_start();

if(!isset($_SESSION['Empid']))

{

    header("Location:login.php");

    exit;

}

if(!isset($_POST['CardId'])|| !isset($_POST['CardId']))

{

    die("projectid is missing");

}

$projectId=$_POST['ProjectId'];

$cardId=$_POST['CardId'];

?>

<html>

    <head>

        <title> card</title>

        <link href="stylep2.css" rel="stylesheet">

    </head>

    <body>

        <?php include("header.php");?>

        <div class="main-content">

            <div class="container-fliud1">

                <div class="row">

                    <div class="col-md-4 card-section">

                                        <?php

                $servername="localhost";

                $username="name";

                $password="password";

                $dbname="project";

                $conn=new mysqli($servername,$username,$password,$dbname);

                if($conn->connect_error)

                {

                    die("connection failed" .$conn->connect_error);

                }

                $select_query=mysqli_query($conn,"select * from Projects where ProjectId=$ProjectId");

                while($data=mysqli_fetch_array($select_query))

                {

                    ?>

                    <div class="card mb-3 mt-4" style="max-width:60px;">

                        <div class="card" id="card <?php echo $data['cardId'];?>">

                            <div class="row g-0 mr-0 ml-0">

                                <div class="col-md-4">

                                    <img src="microsat.png" class="img-fluid rounded-start" alt="image" width="155px">

                                </div>

                                <div class="col-md-8-p-2">

                                    <div class="card-title">

                                        <span style="font-size:2rem;font-weight:bold;"><?php echo $data["ProjectName"];?></span>

                                    </div>

                                    <div class="card-text">

                                        <b>Associated Team Members</b>

                                        <?php echo $data["Team members"];?><br>

                                        <a href="http" class="text-dark">TM document</a>

                                        <a href="http" class="text-dark">TC document</a><br>

                                        codepath:/home/raghuma/project

                                    </div>

                                </div>

                            </div>

                    </div>

                    <?php }?>

                    </div>

                    <div class="col-md-8 info-section" id="info section" >

                        <div id="cardTttle">

                            <h2>microsat details</h2>

                        </div>

                        <table class="table table-bordered" id="data Table">

                            <thead>

                                <tr>

                                    <th>

                                        Attribute

                                    </th>

                                    <th>value</th>

                                </tr>

                            </thead>

                            <tbody>

                                <tr>

                                    <td>

                                        No of NCs from QA

                                    </td>

                                    <td class="dynamic-column">value1</td>

                                </tr>

                                same for other 7 rows

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <script>

            const cardData={

              1:["value1A","value2A","Value3A","Value4A","Value5A","Value6A","Value7A","Value8A"],

              2:["value1B","value2B","Value3B","Value4B","Value5B","Value6B","Value7B","Value8B"],

              3:["value1C","value2C","Value3C","Value4C","Value5C","Value6C","Value7C","Value8C"]

            };

            const cardNames=

            {

                1:"Microsat2A",

                2:"Microsat2B",

                3:"Microsat2C"

            };

            function highlight(CardId)

             {

                   Console.log(“Highlight_card with id:”,(CardId);

                     Const Cards=document.querySelectorAll(.’card’ );

Cards=for each(Card=>{

Const id=parse Int(card.id.replace(‘card’ “”),10);

If(id===CardId)

{

Console.log(‘matching card’,id);

Card.classlist.add(‘highlighted’);

Card.classlist.remove(‘dimmed’);

}

Else

{

Console.log(‘non-matching card’,id);

Card.classlist.add(‘highlighted’);

Card.classlist.remove(‘dimmed’);

}

});

changeCardData(CardId);

}

 Function changeCardData(CardId)

{

document .getElementById(‘CardId’).inner text=Card names[CardId];

const newvalues=Card data[CardId];

const tableRows=document.querySelectorAll(“#data Table tbody tr”);

tableRows.forEach((row,index=>

{

Row.queryselector(‘dynamic-column’).inner text=newvalues[index];

});

$(‘dataTable’).Data table().destroy ();

$(‘dataTable’).Data table();

}

$(document.ready(function ()

{

$(‘dataTable’).Data table();

});

document .addEventListner(‘DOMContentLoaded’, function ()

{

Const urlParams=new URLSearchParams(window.location.search);

Const CardId=<?php echo $CardId;?>;

If(CardId)

{

 highlight(Number(CardId));

}

});

        </script>

    </body>

</html>


