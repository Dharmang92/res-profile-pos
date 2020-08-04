<?php
require_once "pdo.php";
session_start();

if (!isset($_SESSION["name"])) {
    die("User not logged in");
}

$stmt = $pdo->prepare("select profile_id, first_name, last_name, headline, email, summary from profile where profile_id=:pid");
$stmt->execute(array(":pid" => $_GET["profile_id"]));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header('Location: index.php');
    return;
}
$fn = htmlentities($row["first_name"]);
$ln = htmlentities($row["last_name"]);
$e = htmlentities($row["email"]);
$h = htmlentities($row["headline"]);
$s = htmlentities($row["summary"]);
$id = htmlentities($row["profile_id"]);

if (isset($_POST["cancel"])) {
    header("Location: index.php");
    return;
}

if (isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["headline"]) && isset($_POST["summary"])) {
    if (strlen($_POST["first_name"]) < 1 || strlen($_POST["last_name"]) < 1 || strlen($_POST["email"]) < 1 || strlen($_POST["headline"]) < 1 || strlen($_POST["summary"]) < 1) {
        $_SESSION["fail"] = "All fields are required";
        header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
        return;
    } else {
        if (strpos($_POST["email"], "@") !== false) {
            $stmt = $pdo->prepare("update profile set first_name=:fn, last_name=:ln, email=:em, headline=:hd, summary=:sm where profile_id=:pid;");
            $stmt->execute(array(
                ":pid" => $_POST["profile_id"],
                ":fn" => $_POST["first_name"],
                ":ln" => $_POST["last_name"],
                "em" => $_POST["email"],
                ":hd" => $_POST["headline"],
                ":sm" => $_POST["summary"]
            ));

            $_SESSION["success"] = "Profile edited";
            header("Location: index.php");
            return;
        } else {
            $_SESSION["fail"] = "Email address must contain @";
            header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
            return;
        }
    }
}

?>

<html>

<head>
    <title>Dharmang Gajjar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <h1>Editing Profile for <?= htmlentities($_SESSION["name"]) ?></h1>
        <?php
        if (isset($_SESSION["fail"])) {
            echo "<p style='color: red'>" . htmlentities($_SESSION["fail"]) . "</p>";
            unset($_SESSION["fail"]);
        }
        ?>
        <form method="post">
            <p>First Name: <input type="text" name="first_name" size="60" value="<?= $fn ?>" /></p>
            <p>Last Name: <input type="text" name="last_name" size="60" value="<?= $ln ?>" /></p>
            <p>Email: <input type="text" name="email" size="30" value="<?= $e ?>" /></p>
            <p>Headline: <br /><input type="text" name="headline" size="80" value="<?= $h ?>" /></p>
            <p>Summary: <br /><textarea name="summary" cols="80" rows="8"><?= $s ?></textarea>
                <input type="hidden" name="profile_id" value="<?= $id ?>" />
                <p><input type="submit" value="Save"> <input type="submit" name="cancel" value="Cancel"></p>
        </form>
    </div>
</body>

</html>