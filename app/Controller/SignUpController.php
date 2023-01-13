<?php

namespace App\Controller;

use PDO;

class RegisterController
{
    public static function post(PDO $conn, array $requestBody)
    {
        if (!isset($requestBody['cpf']) || !isset($requestBody['senha'])) {
            $message = 'Cpf/senha não consta no corpo da requisição';
            response($message, '400', '0');
        }

        if (strlen($requestBody['cpf']) < 1 || strlen($requestBody['cpf']) > 255 || strlen($requestBody['senha']) < 1 || strlen($requestBody['senha']) > 60) {
            $message = 'CPF/senha fora do padrão esperado';
            response($message, '400', '0');
        }

        $cpf = trim($requestBody['cpf']);
        $password = $requestBody['senha'];

        try {
            $query = $conn->prepare("SELECT id FROM user WHERE cpf=:cpf");
            $query->bindParam(':cpf', $cpf, PDO::PARAM_STR);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount !== 0) {
                $message = 'O CPF informado já está em uso';
                response($message, '409', '0');
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = $conn->prepare('INSERT INTO user (cpf, senha) VALUES (:cpf, :password)');
            $query->bindParam(':cpf', $cpf, PDO::PARAM_STR);
            $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $message = 'Algo deu errado - tente novamente';
                response($message, '500', '0');
            }
            $lastUserId = $conn->lastInsertId();

            $returnData = array();
            $returnData['usuario_id'] = $lastUserId;
            $returnData['cpf'] = $cpf;

            $message = 'Usuário criado com sucesso';
            response($message, '201', $returnData);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            response('Erro interno', '500', '0');
        }
    }
}
