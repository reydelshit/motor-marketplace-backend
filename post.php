<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['user_id'])) {
            $user_id_specific_user = $_GET['user_id'];
            $sql = "SELECT * FROM post WHERE user_id = :user_id";
        }

        if (isset($_GET['post_id'])) {
            $post_id = $_GET['post_id'];
            $sql = "SELECT * FROM post WHERE post_id = :post_id";
        }


        if (!isset($_GET['user_id']) && !isset($_GET['post_id'])) {
            $sql = "SELECT * FROM post INNER JOIN users ON post.user_id = users.user_id ORDER BY post_id DESC";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($user_id_specific_user)) {
                $stmt->bindParam(':user_id', $user_id_specific_user);
            }

            if (isset($post_id)) {
                $stmt->bindParam(':post_id', $post_id);
            }

            $stmt->execute();
            $user_post = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($user_post);
        }


        break;

    case "POST":
        $user_post = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO post (user_id, post_context, post_image, post_isForSale, post_location, post_price, created_at) VALUES (:user_id, :post_context, :post_image, :post_isForSale, :post_location, :post_price, :created_at)";
        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':user_id', $user_post->user_id);
        $stmt->bindParam(':post_context', $user_post->post_context);
        $stmt->bindParam(':post_image', $user_post->post_image);
        $stmt->bindParam(':post_isForSale', $user_post->post_isForSale);
        $stmt->bindParam(':post_location',  $user_post->post_location);
        $stmt->bindParam(':post_price',  $user_post->post_price);
        $stmt->bindParam(':created_at',  $created_at);


        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "post successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "post failed"
            ];
        }




        echo json_encode($response);
        break;

    case "PUT":
        $user_post = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE post SET post_context = :post_context, post_image = :post_image, post_isForSale = :post_isForSale, post_location = :post_location, post_price = :post_price WHERE post_id = :post_id";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':post_id', $user_post->post_id);
        $stmt->bindParam(':post_context', $user_post->post_context);
        $stmt->bindParam(':post_image', $user_post->post_image);
        $stmt->bindParam(':post_isForSale', $user_post->post_isForSale);
        $stmt->bindParam(':post_location', $user_post->post_location);
        $stmt->bindParam(':post_price', $user_post->post_price);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "post updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "post update failed"
            ];
        }

        break;
    case "DELETE":
        $user_post = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM post WHERE post_id = :post_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':post_id', $user_post->post_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "user_post deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "user_post delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
