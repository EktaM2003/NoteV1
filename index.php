<?php
$insert = false;
$delete = false;
$update=false;
// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$database = "notes";

// Create a connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Die if connection was not successful
if (!$conn) {
    die("Sorry we failed to connect: " . mysqli_connect_error());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['snoEdit'])) {
        // Update a record
        $sno = $_POST["snoEdit"];
        $title = $_POST["titleEdit"];
        $description = $_POST["descriptionEdit"];
        $sql = "UPDATE notes SET title = '$title', description = '$description' WHERE sno = $sno";
        $result = mysqli_query($conn, $sql);
        if ($result) {
           // echo "Successfully updated";
           $update=true;
        } else {
            echo "ERROR: " . mysqli_error($conn);
        }
    } else {
        // Insert a new record
        $title = $_POST["title"];
        $description = $_POST["description"];
        $sql = "INSERT INTO notes (title, description) VALUES ('$title', '$description')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $insert = true;
        } else {
            echo "ERROR: " . mysqli_error($conn);
        }
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $sno = $_GET['delete'];
    $sql = "DELETE FROM notes WHERE sno = $sno";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $delete = true;
    } else {
        echo "ERROR: " . mysqli_error($conn);
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NoteTaker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
</head>
<body>

<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
    Edit Modal
</button> -->

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit this Note</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/crud/index.php" method="POST">
                    <input type="hidden" name="snoEdit" id="snoEdit">
                    <div class="form-group">
                        <label for="title" class="form-label">Note Title</label>
                        <input type="text" class="form-control" id="titleEdit" name="titleEdit" required>
                    </div>
                    <div class="form-group">
                        <label for="desc" class="form-label">Note Description</label>
                        <textarea class="form-control" id="descriptionEdit" name="descriptionEdit" rows="3" required></textarea>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Update Note</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">NoteTaker</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact Us</a>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>

<?php
if ($insert) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
    <strong>Success!</strong> Your note has been inserted successfully.
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
}
if ($update) {
  echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
  <strong>Success!</strong> Your note has been updated successfully.
  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>";
}

if ($delete) {
    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
    <strong>Deleted!</strong> Your note has been deleted successfully.
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
}
?>

<div class="container my-4">
    <h2>Add a Note</h2>
    <form action="/crud/index.php" method="POST">
        <div class="form-group">
            <label for="title" class="form-label">Note Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="desc" class="form-label">Note Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Add Note</button>
    </form>
</div>

<div class="container">
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
        $sql = "SELECT * FROM notes";
        $result = mysqli_query($conn, $sql);
        $sno = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $sno++;
            echo "<tr>
                <th scope='row'> $sno </th>
                <td>" . $row['title'] . "</td>
                <td>" . $row['description'] . "</td>
                <td>
                    <button class='edit btn btn-sm btn-primary' id='e" . $row['sno'] . "'>Edit</button>
                    <button class='delete btn btn-sm btn-danger' id='" . $row['sno'] . "'>Delete</button>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="//cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#myTable').DataTable();
    });

    // Edit functionality
    const editButtons = document.getElementsByClassName('edit');
    Array.from(editButtons).forEach((element) => {
        element.addEventListener("click", (e) => {
            const tr = e.target.parentNode.parentNode;
            const title = tr.getElementsByTagName("td")[0].innerText;
            const description = tr.getElementsByTagName("td")[1].innerText;
            document.getElementById('titleEdit').value = title;
            document.getElementById('descriptionEdit').value = description;
            document.getElementById('snoEdit').value = e.target.id.substr(1);
            $('#editModal').modal('toggle');
        });
    });

    // Delete functionality
    const deleteButtons = document.getElementsByClassName('delete');
    Array.from(deleteButtons).forEach((element) => {
        element.addEventListener("click", (e) => {
            const sno = e.target.id;
            if (confirm("Are you sure you want to delete this note?")) {
                window.location = "/crud/index.php?delete=" + sno;
            }
        });
    });
</script>
</body>
</html>
