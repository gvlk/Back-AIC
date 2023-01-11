<?php
  require_once('dtos/response.php');
  
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response('Método não aceito', '405', '0');
  }

  if (isset($requestBody['cpf']) && isset($requestBody['senha'])) {
    $cpf = $requestBody['cpf'];
    $senha = $requestBody['senha'];

    try {
      $sql = 'SELECT * FROM user WHERE cpf=? AND senha=?';
	    $stmt = 
	        $conn->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
	    $stmt->execute([$cpf, $senha]);
	    $response = $stmt->fetchAll();

	    // validado
	    if (sizeof($response) === 1) {
	      foreach ($response as $row) {
	        $responseCpf = $row['cpf'];
	        $responseSenha = $row['senha'];
	        $responseUserId = $row['id'];
				} 

			} else if (sizeof($response) === 0) {
		    response("credenciais erradas", '400', '0');
		    
		  } else {
		    response("erro interno", "500", "0");
		  }

      $accessToken = base64_encode(bin2hex(random_bytes(24)).time());
	    $refreshToken = base64_encode(bin2hex(random_bytes(24)).time());
	    $dueAccessToken = time()+1800;
	  	$dueRefreshToken = time()+36000;

		  $sql = 
		  'INSERT INTO sessions (accessToken, refreshToken, dueAccessToken, dueRefreshToken, user_id) VALUES (?, ?, ?, ?, ?)';
		  
		  $stmt = 
	        $conn->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);

	    $stmt->execute([$accessToken, $refreshToken, $dueAccessToken, 
	      	$dueRefreshToken, $responseUserId]);

	    $returnData = array();
      $returnData['accessToken'] = $accessToken;
      $returnData['refreshToken'] = $refreshToken;   
      $returnData['dueAccessToken'] = $dueAccessToken;
      $returnData['dueRefreshToken'] = $dueRefreshToken;    

      response('sucesso', '200', $returnData);
	  } catch (PDOException) {
	  	response('Erro interno', '500', '0');
	  }
    
  } else {
    response("Senha/CPF não preenchidos", "400", '0');
  }