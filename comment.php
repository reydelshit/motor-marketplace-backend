<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        $sql = "SELECT * FROM comment INNER JOIN users ON users.user_id = comment.user_id ORDER BY post_id DESC";


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $comment = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($comment);
        }


        break;

    case "POST":
        $user_comment = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO comment (user_id, post_id, comment_content, created_at) VALUES (:user_id, :post_id, :comment_content, :created_at)";
        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':user_id', $user_comment->user_id);
        $stmt->bindParam(':comment_content', $user_comment->comment_content);
        $stmt->bindParam(':post_id', $user_comment->post_id);
        $stmt->bindParam(':created_at',  $created_at);


        if ($stmt->execute()) {


            $sql2 = "INSERT INTO notifications (sender_id, receiver_id, notification_message, created_at) VALUES (:sender_id, :receiver_id, :notification_message, :created_at)";
            $stmt2 = $conn->prepare($sql2);

            $created_at = date('Y-m-d H:i:s');
            $message = $user_comment->user_name . " commented on your post";

            $stmt2->bindParam(':sender_id', $user_comment->user_id);
            $stmt2->bindParam(':receiver_id', $user_comment->post_user_id);
            $stmt2->bindParam(':notification_message', $message);
            $stmt2->bindParam(':created_at', $created_at);

            $stmt2->execute();


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


    case "DELETE":
        $user_comment = json_decode(file_get_contents('php://input'));
        $sql = "DELETE FROM comment WHERE comment_id = :comment_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':comment_id', $user_comment->comment_id);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "user_comment deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "user_comment delete failed"
            ];
        }

        echo json_encode($response);
        break;
}
