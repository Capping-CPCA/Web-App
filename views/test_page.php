<?php
authorizedPage();

include('header.php');
?>

<div class="page-wrapper">
    <div class="jumbotron" style="max-width: 700px; width: 100%; margin: 0 auto">
        <form>
            <div class="form-group">
                <label for="inputLastName">Last Name:</label>
                <input type="text" id="inputLastName" class="form-control">
            </div>
            <div class="form-group">
                <label for="inputFirstName">First Name:</label>
                <input type="text" id="inputFirstName" class="form-control">
            </div>
            <button class="btn btn-success btn-add-participant">Add Participant</button>
        </form>

        <table class="table table-stripped">
            <tr>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Status</th>
                <th class='delete-show'>Delete</th>
            </tr>
            <tr>
                <td>Smith</td>
                <td>John</td>
                <td>active</td>
                <td><button class='btn btn-danger delete-show text-center'>-</button></td>
            </tr>
        </table>
        <button class='btn btn-danger delete text-center'>Admin Delete</button>
    </div>
</div>

<script type="text/javascript">
    $(".btn-add-participant").click(function (e) {
        var randomStatus= ["active", "inactive", "unknown"];
        var indexR= Math.floor((Math.random() * randomStatus.length-1) + 1);
        var fName= $("#inputFirstName").val();
        var lName= $("#inputLastName").val();
        e.preventDefault();
        console.log("you added a participant");
        $("table").append("<tr><td>"+lName+"</td><td>"+fName+"</td><td>"+randomStatus[indexR]+"</td><td class='delete'><button class='btn btn-danger delete-show text-center'> - </button></td>")
    });

    $(".delete").click(function () {
        $(".delete-show").show("fast");
    });

    $(".delete-show").click(function () {
        var getParentRow = $(this).closest('tr');
        getParentRow.remove();
    });
</script>
<?php include('footer.php'); ?>