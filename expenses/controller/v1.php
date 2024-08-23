<?php
class ExpController {
    public function data() {
        global $conn;
        header('Cache-Control: no-cache');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle POST request for inserting data
            header('Content-Type: application/json');
            
            $response = array();

            // Read JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            $things = $input['things'] ?? '';
            $price = $input['price'] ?? '';

            if (empty($things)) {
                $response['status'] = 'error';
                $response['message'] = 'Item name is required.';
                echo json_encode($response);
                return;
            }

            if (empty($price)) {
                $response['status'] = 'error';
                $response['message'] = 'Price is required.';
                echo json_encode($response);
                return;
            }

            if (!is_numeric($price)) {
                $response['status'] = 'error';
                $response['message'] = 'Price must be a valid number.';
                echo json_encode($response);
                return;
            }            

            $result = $conn->query("SELECT MAX(CAST(SUBSTRING(id, 5) AS UNSIGNED)) AS max_id FROM `add`");
            $row = $result->fetch_assoc();
            $nextIdNumber = ($row['max_id'] ?? 0) + 1;
            $uniqueId = 'EXP-' . str_pad($nextIdNumber, 4, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO `add` (id, things, price) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $uniqueId, $things, $price);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Data inserted successfully.';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to insert data.';
            }

            $stmt->close();

            // Fetch updated data after insertion
            $result = $conn->query("SELECT id, things, price FROM `add`");
            if ($result) {
                $thingsArray = array();
                $totalPrice = 0;

                while ($row = $result->fetch_assoc()) {
                    $totalPrice += (int)$row['price'];

                    $exp_info = array(
                        'id' => $row['id'],
                        'things' => $row['things'],
                        'price' => $row['price']
                    );
                    $thingsArray[] = $exp_info;
                }

                $response['total_price'] = $totalPrice;
                $response['data'] = $thingsArray;
            } else {
                $response['total_price'] = 0;
                $response['data'] = [];
            }

            echo json_encode($response);

        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Content-Type: application/json');

            $response = array();

            $result = $conn->query("SELECT id, things, price FROM `add`");

            if ($result) {
                $thingsArray = array();
                $totalPrice = 0;

                while ($row = $result->fetch_assoc()) {
                    $totalPrice += (int)$row['price'];

                    $exp_info = array(
                        'id' => $row['id'],
                        'things' => $row['things'],
                        'price' => $row['price']
                    );
                    $thingsArray[] = $exp_info;
                }

                $response['status'] = 'success';
                $response['message'] = 'Data successfully displayed.';
                $response['total_price'] = $totalPrice;
                $response['data'] = $thingsArray;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to retrieve data.';
            }

            echo json_encode($response);

        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            header('Content-Type: application/json');

            $response = array();

            $id = $_GET['id'] ?? '';

            if (empty($id)) {
                $response['status'] = 'error';
                $response['message'] = 'ID is required.';
                echo json_encode($response);
                return;
            }

            $stmt = $conn->prepare("DELETE FROM `add` WHERE id = ?");
            $stmt->bind_param("s", $id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $response['status'] = 'success';
                    $response['message'] = 'Data deleted successfully.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'No record found with the given ID.';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to delete data.';
            }

            $stmt->close();

            echo json_encode($response);

        } else {
            http_response_code(405);
            echo json_encode(array('status' => 'error', 'message' => 'Method Not Allowed'));
        }
    }
}
?>
