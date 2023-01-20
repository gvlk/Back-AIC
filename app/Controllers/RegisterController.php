<?php

namespace App\Controllers;

use PDO;

use App\Modules\Response;

class RegisterController
{
    public static function post(PDO $conn, array $requestBody)
    {
        if (!isset($requestBody['cpf']) || !isset($requestBody['senha'])) {
            return Response::error('Cpf/senha não consta no corpo da requisição');
        }

        if (strlen($requestBody['cpf']) < 1 || strlen($requestBody['cpf']) > 255 || strlen($requestBody['senha']) < 1 || strlen($requestBody['senha']) > 60) {
            return Response::error('CPF/senha fora do padrão esperado');
        }

        $cpf = trim($requestBody['cpf']);
        $password = $requestBody['senha'];

        try {
            $query = $conn->prepare("SELECT id FROM user WHERE cpf=:cpf");
            $query->bindParam(':cpf', $cpf, PDO::PARAM_STR);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount !== 0) {
                return Response::error('O CPF informado já está em uso');
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = $conn->prepare('INSERT INTO user (cpf, senha) VALUES (:cpf, :password)');
            $query->bindParam(':cpf', $cpf, PDO::PARAM_STR);
            $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                return Response::error('Algo deu errado - tente novamente');
            }

            $lastUserId = $conn->lastInsertId();

            $returnData = array();
            $returnData['usuario_id'] = $lastUserId;
            $returnData['cpf'] = $cpf;

            return Response::success(['message' => 'Usuário criado com sucesso']);
        } catch (PDOException $ex) {
            return Response::error('Erro interno:' . $ex->getMessage());
        }
    }
}
