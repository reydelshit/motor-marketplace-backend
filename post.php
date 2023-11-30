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


        if (!isset($_GET['user_id'])) {
            $sql = "SELECT * FROM post ORDER BY post_id DESC";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($user_id_specific_user)) {
                $stmt->bindParam(':user_id', $user_id_specific_user);
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

        $product = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE product
                SET product_name = :product_name, 
                    product_price = :product_price, 
                    quantity = :quantity, 
                    product_image = :product_image, 
                    product_description = :product_description, 
                    tags = :tags, 
                    product_category = :product_category
                WHERE product_id = :product_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':product_id', $product->product_id);
        $stmt->bindParam(':product_name', $product->product_name);
        $stmt->bindParam(':product_price', $product->product_price);
        $stmt->bindParam(':quantity', $product->quantity);
        $stmt->bindParam(':product_image', $product->product_image);
        $stmt->bindParam(':product_description', $product->product_description);
        $stmt->bindParam(':tags', $product->tags);
        $stmt->bindParam(':product_category', $product->product_category);


        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Admin added product and images successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Admin added product successfully, but failed to add images"
            ];
        }

        break;

    case "DELETE":
        $sql = "DELETE FROM product WHERE product_id = :product_id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':product_id', $path[3]);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "product deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "product deletion failed"
            ];
        }
}
