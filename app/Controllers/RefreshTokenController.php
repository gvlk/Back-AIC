<?php

namespace App\Controllers;

use App\Modules\Response;

use PDO;
use PDOException;

class RefreshTokenController
{
    public static function post(PDO $conn, array $requestBody)
    {
        try {
            $sql = 'SELECT * FROM sessions WHERE refreshToken=?';
            $stmt = $conn->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
            $stmt->execute([$requestBody['refreshToken']]);
            $response = $stmt->fetchAll();
        } catch (PDOException) {
            return Response::error('Erro interno');
        }

        // Checar errors
        if (sizeof($response) === 0) {
            return Response::error('Refresh token invalido');
        }
        if (sizeof($response) > 1) {
            return Response::error('Erro interno');
        }

        // Deletar sessão atual
        try {
            $sql = 'DELETE FROM sessions WHERE refreshToken=?';
            $stmt = $conn->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
            $stmt->execute([$requestBody['refreshToken']]);
        } catch (PDOException) {
            return Response::error('Erro interno');
        }

        // Gerar tokens
        $accessToken = base64_encode(bin2hex(random_bytes(24)).time());
        $refreshToken = $response['refreshToken'];
        $dueAccessToken = time()+1800;
        $dueRefreshToken = time()+36000;

        try {
            $sql = 'INSERT INTO sessions (accessToken, refreshToken, dueAccessToken, dueRefreshToken, user_id) VALUES (?, ?, ?, ?, ?)';
            $stmt = $conn->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
            $stmt->execute([$accessToken, $refreshToken, $dueAccessToken,$dueRefreshToken, $response['user_id']]);
        } catch (PDOException) {
            return Response::error('Erro interno');
        }

        return Response::success([
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken,
            'dueAccessToken' => $dueAccessToken,
            'dueRefreshToken' => $dueRefreshToken
        ]);
    }
}
