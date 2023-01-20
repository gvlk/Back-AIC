<?php

namespace App\Controllers;

use App\Modules\Response;
use PDO;
use PDOException;

class LoginController
{
    public static function post(PDO $conn, array $requestBody)
    {
        if (! isset($requestBody['cpf']) || !isset($requestBody['senha'])) {
            return Response::error("Senha/CPF não preenchidos");
        }

        try {
            $sql = 'SELECT * FROM user WHERE cpf=?';
            $stmt = $conn->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
            $stmt->execute([$requestBody['cpf']]);
            $response = $stmt->fetchAll();
        } catch (PDOException) {
            return Response::error('Erro interno');
        }

        // Checar errors
        if (sizeof($response) === 0) {
            return Response::error('Credenciais invalidas');
        }
        if (sizeof($response) > 1) {
            return Response::error('Erro interno');
        }

        // Obter dados
        $responseCpf = $response[0]['cpf'];
        $responseSenha = $response[0]['senha'];
        $responseUserId = $response[0]['id'];

        // Verificar se a senha esta correta
        if(! password_verify($requestBody['senha'], $responseSenha)) {
            return Response::error('Credenciais invalidas');
        }

        // Gerar tokens
        $accessToken = base64_encode(bin2hex(random_bytes(24)).time());
        $refreshToken = base64_encode(bin2hex(random_bytes(24)).time());
        $dueAccessToken = time()+1800;
        $dueRefreshToken = time()+36000;

        try {
            $sql = 'INSERT INTO sessions (accessToken, refreshToken, dueAccessToken, dueRefreshToken, user_id) VALUES (?, ?, ?, ?, ?)';
            $stmt = $conn->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
            $stmt->execute([$accessToken, $refreshToken, $dueAccessToken,$dueRefreshToken, $responseUserId]);
        } catch (PDOException $e) {
            return Response::error('Erro interno: ' . $e->getMessage());
        }

        return Response::success([
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken,
            'dueAccessToken' => $dueAccessToken,
            'dueRefreshToken' => $dueRefreshToken
        ]);
    }
}
