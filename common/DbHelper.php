<?php

namespace common;

use Exception;
use mysqli;

class DbHelper
{
    private const dbName = "pages";
    private static ?DbHelper $instance = null;
    private $conn;

    public static function getInstance($host = null, $port = null, $user = null, $pass = null): DbHelper {
        if (self::$instance === null) self::$instance = new DbHelper($host, $port, $user, $pass);
        return self::$instance;
    }

    private function __construct(
        $host, $port, $user, $pass
    ){
        $this->conn = new mysqli();
        $this->conn->connect(
            hostname: $host,
            username: $user,
            password: $pass,
            database: self::dbName,
            port: $port
        );
    }

    public function getTitle($url): string{
        $sql = "SELECT title FROM pages WHERE url=? or alias=?";
        $this->conn->begin_transaction();
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $url, $url);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_row();
        $stmt->close();
        $this->conn->commit();
        return ($row !== null && $row !== false) ? $row[0] : "";
    }

    public function getPagesInfo(): array{
        $sql = "SELECT * FROM pages";
        $this->conn->begin_transaction();
        $result = $this->conn->query($sql);
        $res_arr = $result->fetch_all(MYSQLI_ASSOC);
        $result->free_result();
        $this->conn->commit();
        return $res_arr;
    }

    public function getUserPassword(string $user): ?string{
        $sql = "SELECT password FROM users WHERE login = ?";
        $this->conn->begin_transaction();
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $this->conn->commit();
        return ($row === null) ? $row : $row['password'];
    }

    public function isSecure(string $page){
        $sql = "SELECT secure FROM pages WHERE url=? or alias=?";
        $this->conn->begin_transaction();
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $page, $page);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $this->conn->commit();
        return $row !== null && $row['secure'] == 1;
    }

    public function getUserName(string $user){
        $sql = "SELECT `name` FROM users WHERE login = ?";
        $this->conn->begin_transaction();
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $this->conn->commit();
        return ($row === null) ? $row : $row['name'];
    }

    public function saveUser(string $login, string $password, string $name): bool
    {
        $sql = "INSERT INTO `users` (login, password, name) VALUES(?, ?, ?)";
        try {
            $this->conn->begin_transaction();
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", $login, $password, $name);
            if (!$stmt->execute()) throw new Exception("Ошибка добавления пользователя");
            $this->conn->commit();
            return true;
        } catch (\Throwable $ex){
            $this->conn->rollback();
            return false;
        }
    }

    public function getCountOfProducts():int{
        $sql = "SELECT * FROM products";
        $this->conn->begin_transaction();
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $prodCount=$result->num_rows;
        $stmt->close();
        $this->conn->commit();
        return $prodCount;
    }

    public function getProducts($search=""):array{
        if ($search!=""){
            $sql = "SELECT * FROM products WHERE name LIKE '%$search%'";
        }
        else{
            $sql = "SELECT * FROM products";
        }
        $this->conn->begin_transaction();
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $res_arr = $result->fetch_all(MYSQLI_ASSOC);
        $result->free_result();
        $this->conn->commit();
        return $res_arr;
    }
    public function getSortProducts($categoryId):array{
        $sql = "SELECT * FROM products JOIN types ON products.type=types.Id WHERE types.Id = ?";
        $this->conn->begin_transaction();
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $res_arr = $result->fetch_all(MYSQLI_ASSOC);
        $result->free_result();
        $this->conn->commit();
        return $res_arr;
    }

    public function getBasket(string $user):array{
        $sql = "SELECT * FROM basket JOIN products ON basket.productId = products.id WHERE login = ?";
        $this->conn->begin_transaction();
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $res_arr = $result->fetch_all(MYSQLI_ASSOC);
        $result->free_result();
        $this->conn->commit();
        return $res_arr;
    }
    public function addToBasket(string $login, int $productId,int $quantity): bool
    {
        $sql = "INSERT INTO `basket` (login, productId,quantity) VALUES(?, ?, ?)";
        try {
            $this->conn->begin_transaction();
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sii", $login,$productId,$quantity);
            if (!$stmt->execute()) throw new Exception("Ошибка добавления в корзину");
            $this->conn->commit();
            return true;
        } catch (\Throwable $ex){
            $this->conn->rollback();
            return false;
        }
    }

    public function removeFromBasket(int $productId,string $login): bool
    {
        $sql = "DELETE FROM `basket` WHERE productId=? AND login=?";
        try {
            $this->conn->begin_transaction();
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $productId,$login);
            if (!$stmt->execute()) throw new Exception("Ошибка добавления в корзину");
            $this->conn->commit();
            return true;
        } catch (\Throwable $ex){
            $this->conn->rollback();
            return false;
        }
    }

    public function getUserData(string $user):array{
        $sql = "SELECT * FROM users WHERE login = ?";
        $this->conn->begin_transaction();
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $this->conn->commit();
        return  $row;
    }

    public function getOrders(string $user):array{
        $sql = "SELECT * FROM orders JOIN ordered ON orders.orderId = ordered.orderId JOIN products on ordered.productId=products.Id WHERE orders.login = ? ORDER BY orders.orderDate DESC ";
        $this->conn->begin_transaction();
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $res_arr = $result->fetch_all(MYSQLI_ASSOC);
        $result->free_result();
        $this->conn->commit();
        return $res_arr;
    }

    public function Buy(string $login, int $productId,int $quantity): bool
    {
        $sql = "INSERT INTO `orders` (login) VALUES(?)";
        try {
            $this->conn->begin_transaction();
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $login);
            if (!$stmt->execute()) throw new Exception("Ошибка добавления в корзину");
            $this->conn->commit();

        } catch (\Throwable $ex){
            $this->conn->rollback();
            return false;
        }

        $sql = "INSERT INTO `ordered` (productId,quantity) VALUES(?,?)";
        try {
            $this->conn->begin_transaction();
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $productId,$quantity);
            if (!$stmt->execute()) throw new Exception("Ошибка добавления в корзину");
            $this->conn->commit();
            return true;
        } catch (\Throwable $ex){
            $this->conn->rollback();
            return false;
        }
    }
}