<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {


    case "POST":
        $user = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO users (name, email, password, birthday, gender, created_at, address) VALUES (:name, :email, :password, :birthday, :gender, :created_at, :address)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':password', $user->password);
        $stmt->bindParam(':birthday', $user->birthday);
        $stmt->bindParam(':gender', $user->gender);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':address', $user->address);


        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "User created successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User creation failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $user = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE users 
                SET name = :name, 
                    email = :email, 
                    password = :password, 
                    birthday = :birthday, 
                    gender = :gender, 
                    created_at = :created_at, 
                    address = :address,
                    profile_picture = :profile_picture
                WHERE user_id = :user_id";

        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d');
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':password', $user->password);
        $stmt->bindParam(':birthday', $user->birthday);
        $stmt->bindParam(':gender', $user->gender);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':address', $user->address);
        $stmt->bindParam(':user_id', $user->user_id);
        $stmt->bindParam(':profile_picture', $user->profile_picture);



        if ($stmt->execute()) {

            $response = [
                "status" => "success",
                "message" => "User updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User update failed"
            ];
        }

        echo json_encode($response);
        break;

    case "DELETE":
        $sql = "DELETE FROM users WHERE id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $path[2]);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "User deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User deletion failed"
            ];
        }
}
