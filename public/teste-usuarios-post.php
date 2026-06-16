<?php

$dados = [
    'nome' => 'Pedro Silva',
    'email' => 'pedro.silva@gmail.com',
    'senha' => 'senha123',
    'perfil' => 'atendente',
    'status' => 'ativo'
];

$ch = curl_init('http://localhost/atendelab/public/?controller=usuarios&action=criar');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dados));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

echo '<pre>';
print_r($response);
echo '</pre>';