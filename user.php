<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['user_id'])) {
            $user_id_specific_user = $_GET['user_id'];
            $sql = "SELECT * FROM users WHERE user_id = :user_id";
        }

        if (!isset($_GET['user_id']) && !isset($_GET['product_id'])) {
            $sql = "SELECT * FROM users WHERE user_type = 'user' ORDER BY user_id DESC";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($user_id_specific_user)) {
                $stmt->bindParam(':user_id', $user_id_specific_user);
            }

            $stmt->execute();
            $product = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($product);
        }

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
