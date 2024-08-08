<?php
require(__DIR__ . '/config.php'); //เรียกใช้ไฟล์ config.php

//เขียน api
header('Access-Control-Allow-Origin: *'); //ตั้งให้ API ถูกเข้าถึงได้ทั้งหมด
header("Content-type: application/json; charset=utf-8"); //การเข้ารหัสเป็น UTF-8
$arr = []; //ตัวแปรสำหรับเก็บข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //check get POST
    if (isset($_REQUEST['type']) and $_REQUEST['type'] != '' and !empty($_REQUEST['type'])) {
        if ($_REQUEST['type'] == 'all') {
            $res = $connect->query("SELECT u_username, u_name, u_create_at, u_id FROM users");
            $fetch = $res->fetch_all(MYSQLI_ASSOC);
            $arr['data'] = $fetch;
            $arr['status'] = 'success';
            $arr['msg'] = 'success';
        } elseif ($_REQUEST['type'] == 'single') {
            if (isset($_REQUEST['username']) and isset($_REQUEST['password'])) {
                $res = $connect->query("SELECT * FROM users WHERE u_username = '" . $_REQUEST['username'] . "'");
                if ($res->num_rows >= 1) {
                    $row = $res->fetch_assoc();
                    if (password_verify($_REQUEST['password'] . $row['u_sult'], $row['u_password'])) {
                        unset($row['u_password']);
                        unset($row['u_sult']);
                        $arr['data'] = $row;
                        $arr['status'] = 'success';
                        $arr['msg'] = 'success';
                    } else {
                        http_response_code(404);
                        $arr['status'] = 'error';
                        $arr['msg'] = 'Password incorrect';
                    }
                }
            } else {
                http_response_code(404);
                $arr['status'] = 'error';
                $arr['msg'] = 'Username not found';
            }
            //แก้ไข
        } elseif ($_REQUEST['type'] == 'update') {
            if (isset($_REQUEST['id']) and isset($_REQUEST['username']) and isset($_REQUEST['name'])) {
                if ($_REQUEST['password'] != '' and isset($_REQUEST['password'])) {
                    $sult = rand(0, 999999999);
                    $password = password_hash($_REQUEST['password'] . $sult, PASSWORD_DEFAULT);
                    $res = $connect->query("UPDATE users SET u_name = '" . $_REQUEST['name'] . "',u_password = '" . $password . "',u_sult = '" . $sult . "' WHERE u_id = '" . $_REQUEST['id'] . "'");
                } else {
                    $res = $connect->query("UPDATE users SET u_name = '" . $_REQUEST['name'] . "',u_password = '" . $password . "',u_sult = '" . $sult . "' WHERE u_id = '" . $_REQUEST['id'] . "'");
                }
                if ($res) {
                    $arr['status'] = 'success';
                    $arr['msg'] = 'Insert data success';
                } else {
                    http_response_code(404);
                    $arr['status'] = 'error';
                    $arr['msg'] = 'Error in qurey!';
                }
            } else {
                http_response_code(404);
                $arr['status'] = 'error';
                $arr['msg'] = 'Username or password or name is Empty!';
            }
            //เพิ่ม
        } elseif ($_REQUEST['type'] == 'insert') {
            if (isset($_REQUEST['username']) and isset($_REQUEST['password']) and isset($_REQUEST['name'])) {
                $res_check = $connect->query("SELECT * FROM users WHERE u_username='" . $_REQUEST['username'] . "'");
                if ($res_check->num_rows <= 0) {
                    $sult = rand(0, 999999999);
                    $password = password_hash($_REQUEST['password'] . $sult, PASSWORD_DEFAULT);
                    $res = $connect->query("INSERT INTO users(u_username, u_password, u_sult, u_name)VALUES('" . $_REQUEST['username'] . "','" . $password . "','" . $sult . "','" . $_REQUEST['name'] . "')");
                    if ($res) {
                        $arr['status'] = 'success';
                        $arr['msg'] = 'Insert data success';
                    } else {
                        http_response_code(404);
                        $arr['status'] = 'error';
                        $arr['msg'] = 'Error in qurey!';
                    }
                } else {
                    http_response_code(404);
                    $arr['status'] = 'error';
                    $arr['msg'] = 'Username already exists!';
                }
            } else {
                http_response_code(404);
                $arr['status'] = 'error';
                $arr['msg'] = 'Username or password or name is Empty!';
            }
            //ลบ
        } elseif ($_REQUEST['type'] == 'delete') {
            $res_check = $connect->query("SELECT * FROM users WHERE u_id='" . $_REQUEST['id'] . "'");
            if ($res_check->num_rows >= 1) {
                $res = $connect->query(("DELETE FROM users WHERE u_id='".$_REQUEST['id']."' "));
                if ($res) {
                    $arr['status'] = 'success';
                    $arr['msg'] = 'Delete data success';
                } else {
                    http_response_code(404);
                    $arr['status'] = 'error';
                    $arr['msg'] = 'Error in qurey!';
                }
            } else {
                http_response_code(404);
                $arr['status'] = 'error';
                $arr['msg'] = 'Username not found';
            }
        } else {
            http_response_code(404);
            $arr['status'] = 'error';
            $arr['msg'] = 'Type not match';
        }
    } else {
        http_response_code(404);
        $arr['status'] = 'error';
        $arr['msg'] = 'Type is empty';
    }
} else {
    http_response_code(404);
    $arr['status'] = 'error';
    $arr['msg'] = 'Post only';
}
echo json_encode($arr, JSON_UNESCAPED_UNICODE);