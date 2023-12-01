<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['post_id'])) {
            $post_id = $_GET['post_id'];
            $sql = "SELECT * FROM post_like WHERE post_id = :post_id ORDER BY like_id DESC";
        }

        if (!isset($_GET['post_id']) && !isset($_GET['user_id'])) {
            $sql = "SELECT * FROM post_like ORDER BY like_id DESC";
        }

        if (isset($sql)) {
            $stmt = $conn->prepare($sql);



            if (isset($post_id)) {
                $stmt->bindParam(':post_id', $post_id);
            }

            $stmt->execute();
            $post_like = $stmt->fetchAll(PDO::FETCH_ASSOC);


            echo json_encode($post_like);
        }


        break;

    case "POST":
        $post_like = json_decode(file_get_contents('php://input'));

        if ($post_like->type == 'upvote') {
            $sql = "INSERT INTO post_like (user_id, post_id, type) VALUES (:user_id, :post_id, :type)";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':user_id', $post_like->user_id);
            $stmt->bindParam(':post_id', $post_like->post_id);
            $stmt->bindParam(':type', $post_like->type);


            if ($stmt->execute()) {

                $sql2 = "INSERT INTO notifications (sender_id, receiver_id, notification_message, created_at) VALUES (:sender_id, :receiver_id, :notification_message, :created_at)";
                $stmt2 = $conn->prepare($sql2);

                $created_at = date('Y-m-d H:i:s');
                $message = $post_like->user_name . " upvoted your post";

                $stmt2->bindParam(':sender_id', $post_like->user_id);
                $stmt2->bindParam(':receiver_id', $post_like->post_user_id);
                $stmt2->bindParam(':notification_message', $message);
                $stmt2->bindParam(':created_at', $created_at);

                $stmt2->execute();

                $response = [
                    "status" => "success",
                    "message" => "like successfully"
                ];
            } else {
                $response = [
                    "status" => "error",
                    "message" => "like failed"
                ];
            }

            echo json_encode($response);
        } else {
            $sql = "INSERT INTO post_like (user_id, post_id, type) VALUES (:user_id, :post_id, :type)";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':user_id', $post_like->user_id);
            $stmt->bindParam(':post_id', $post_like->post_id);
            $stmt->bindParam(':type', $post_like->type);


            if ($stmt->execute()) {

                $sql2 = "INSERT INTO notifications (sender_id, receiver_id, notification_message, created_at) VALUES (:sender_id, :receiver_id, :notification_message, :created_at)";
                $stmt2 = $conn->prepare($sql2);

                $created_at = date('Y-m-d H:i:s');
                $message = $post_like->user_name . " downvoted your post";

                $stmt2->bindParam(':sender_id', $post_like->user_id);
                $stmt2->bindParam(':receiver_id', $post_like->post_user_id);
                $stmt2->bindParam(':notification_message', $message);
                $stmt2->bindParam(':created_at', $created_at);

                $stmt2->execute();


                $response = [
                    "status" => "success",
                    "message" => "like successfully"
                ];
            } else {
                $response = [
                    "status" => "error",
                    "message" => "like failed"
                ];
            }

            echo json_encode($response);
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
