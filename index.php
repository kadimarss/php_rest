<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Paragma, Authorization, Accept, Accept-Encoding");
// include createdAt, updatedAt in json_encode

$NOT_FOUND_ERROR = '{
    id: "go.micro.client", code: 500, detail: "not found", status: "Internal Server Error"
}';

// open .con to db
$conn = mysqli_connect("localhost", "root", "", "php_rest")
or die("Error " . mysqli_error($conn));

$email = "";
$username = "";
$password = "";

$pieces = explode('/', $_SERVER['REQUEST_URI']);
$feature = array_pop($pieces);
$request_body = json_decode(file_get_contents('php://input'));

if ($feature == 'List') {

    // fetch from db

    $sql = "SELECT * FROM users";
    $result = mysqli_query($conn, $sql) or die("Error in Selecting"
        . mysqli_error($conn));


    while ($row = mysqli_fetch_assoc($result)) {
        $userArr[] = $row;
    }
    echo json_encode($userArr);

    // close db conn
    mysqli_close($conn);
} elseif ($feature == 'Update') {
    // db query

    $sql = "UPDATE users SET email='{$request_body->email}', username='{$request_body->username}', password='{$request_body->password}' WHERE id='{$request_body->id}'";

    if (mysqli_query($conn, $sql)) {
        $data = array("id" => $request_body->id, "email" => $request_body->email, "username" => $request_body->username, "password" => $request_body->password);
        echo json_encode($data);
    } else {
        http_response_code(404);
    }
    mysqli_close($conn);

} elseif ($feature == 'Create') {

    $email = $request_body->email;
    $username = $request_body->username;
    $password = $request_body->password;


    // db query
    $sql = "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$password')";

    if (mysqli_query($conn, $sql)) {

        $data = array("email" => $email, "username" => $username, "password" => $password);

        echo json_encode($data);

    } else {
        echo "Could not create a new user";
    }

    mysqli_close($conn);

} elseif ($feature == 'Delete') {

    // db query

    $sql = "DELETE FROM users WHERE id='{$request_body -> id}'";


    if (mysqli_query($conn, $sql)) {
        http_response_code(204);
    } else {
        http_response_code(404);
    }

    mysqli_close($conn);

} else {
    echo json_encode(array('message' => 'method unknown')
    );
}


