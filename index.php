<?php

//Global variables-------------------------------------
$insert = false;
$update = false;
$delete = false;
$servername = "localhost";
$username = "root";
$password = "";
$database = "task_manager";
$duplicate_record=false;

//Connecting to database-----------------------------------------
$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) 
{
    die("Sorry we failed to connect: " . mysqli_connect_error());
}

//Managing delete requests-----------------------------------------
if (isset($_GET['delete'])) 
{
    $sno = $_GET['delete'];
    $delete = true;
    $sql = "DELETE FROM `notes` WHERE `sno` = $sno";
    $result = mysqli_query($conn, $sql);
}

//Managing POST requests---------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    //Data Update----------------------------------------------------
    if (isset($_POST['snoEdit'])) 
    {
        $sno = $_POST["snoEdit"];
        $title = $_POST["titleEdit"];
        $description = $_POST["descriptionEdit"];
        $sql = "UPDATE `notes` SET `title` = '$title' , `description` = '$description' WHERE `notes`.`sno` = $sno";
        $result = mysqli_query($conn, $sql);
        if ($result) 
        {
            $update = true;
        } else 
        {
            echo "We could not update the record successfully";
        }
    } 
    else 
    {
        $title = $_POST["title"];
        $description = $_POST["description"];
        $matching = false;
        $qry = "SELECT * FROM notes";
        $result = mysqli_query($conn, $qry);
        while ($row = mysqli_fetch_assoc($result)) 
        {
            if ($row['title'] == $title and $row['description'] == $description) 
            {
                $matching = true;
            }
        }

        if (!$matching) 
        {
            $sql = "INSERT INTO `notes` (`title`, `description`) VALUES ('$title', '$description')";
            $result = mysqli_query($conn, $sql);
            if ($result) 
            {
                $insert = true;
            } 
            else 
            {
                echo "The record was not inserted successfully because of this error ---> " . mysqli_error($conn);
            }
        }
        else
        {
            $duplicate_record=true;
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <title>SmartNotes</title>

</head>

<style>
    body 
    {
       background-color:#ACB1D6;
    }
    .heading
    {
        text-align: center;
    }
</style>

<body>


    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit this Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="/Notes/index.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="snoEdit" id="snoEdit">
                        <div class="form-group">
                            <label for="title">Note Title</label>
                            <input type="text" class="form-control" id="titleEdit" name="titleEdit" aria-describedby="emailHelp">
                        </div>

                        <div class="form-group">
                            <label for="desc">Note Description</label>
                            <textarea class="form-control" id="descriptionEdit" name="descriptionEdit" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer d-block mr-auto">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/Notes/index.php">SmartNotes</a>
    </nav>

    <?php
    //If data is inserted into database-------------------------
    if ($insert) 
    {
         echo "<div class='alert alert-success alert-dismissible fade    show' role='alert'>
            <strong>Success!</strong> Your note has been inserted successfully
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span>
            </button>
        </div>";
        $insert = false;
    }
    ?>

    <?php
    //If data is deleted from database----------------------------
    if ($delete) 
    {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong>Success!</strong> Your note has been deleted successfully
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>×</span>
            </button>
        </div>";
    }
    ?>


    <?php
    //If data is updated -------------------------------------
    if ($update) 
    {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong>Success!</strong> Your note has been updated successfully
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>×</span>
            </button>
        </div>";
    }

    //If duplicate data exists in databse
    if($duplicate_record)
    {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong>Failed!</strong> Title and description already exists
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>×</span>
                </button>
            </div>";
    }
    ?>

    <!-- Notes form ------------------------------------ -->
    <div class="container my-4">
        <h2 class="heading">Add Note</h2>
        <form action="/Notes/index.php" method="POST">
            <div class="form-group">
                <label for="title">Note Title</label>
                <input type="text" class="form-control" id="title" name="title" aria-describedby="emailHelp">
            </div>

            <div class="form-group">
                <label for="desc">Note Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Note</button>
        </form>
    </div>

    <!-- Display all notes from database --------------------- -->
    <div class="container my-4">
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to select all notes from database -------------
                $sql = "SELECT * FROM `notes`";
                $result = mysqli_query($conn, $sql);
                //Variable sno is used to get continuous numbers in index
                $sno = 0;
                while ($row = mysqli_fetch_assoc($result)) 
                {
                    $sno = $sno + 1;
                    echo "<tr>
                        <th scope='row'>" . $sno . "</th>
                        <td>" . $row['title'] . "</td>
                        <td>" . $row['description'] . "</td>
                        <td> <button class='edit btn btn-sm btn-primary' id=" . $row['sno'] . ">Edit</button> <button class='delete btn btn-sm btn-primary' id=d" . $row['sno'] . ">Delete</button>  </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <hr>

    <!-- jQuery -------------------------   -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>

    <!-- Popper.js----------------------------  -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

    <!-- Bootstrap JS ------------------------------- -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <!-- Datatables JS --------------------------------  -->
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();

        });
    </script>

    <!-- Processing update request made by user  -->
    <script>
        edits = document.getElementsByClassName('edit');
        Array.from(edits).forEach((element) => 
        {
            element.addEventListener("click", (e) => 
            {
                console.log("edit ");
                tr = e.target.parentNode.parentNode;
                title = tr.getElementsByTagName("td")[0].innerText;
                description = tr.getElementsByTagName("td")[1].innerText;
                console.log(title, description);
                titleEdit.value = title;
                descriptionEdit.value = description;
                snoEdit.value = e.target.id;
                console.log(e.target.id)
                $('#editModal').modal('toggle');
            })
        })

        // processing delete request made by user 
        deletes = document.getElementsByClassName('delete');
        Array.from(deletes).forEach((element) => 
        {
            element.addEventListener("click", (e) =>
             {
                console.log("edit ");
                sno = e.target.id.substr(1);

                if (confirm("Delete note ?"))
                {
                    console.log("yes");
                    window.location = `/Notes/index.php?delete=${sno}`;
                } 
                else 
                {
                    console.log("no");
                }
            })
        })
    </script>
</body>

</html>