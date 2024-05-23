<?php
// Supondo que $_POST["valor_total"] contém algo como 'R$ 200,00'
// No seu exemplo, você está atribuindo manualmente o valor com prefixo
$valor_total_com_prefixo = 'R$ 200,00';

// Remover o prefixo "R$ " e substituir a vírgula por ponto
$valor_total = str_replace(['R$ ', ','], ['', '.'], $valor_total_com_prefixo);

// Converter para float e formatar com duas casas decimais
$valor_total = number_format((float) $valor_total, 2, ',', '');

echo $valor_total;

// Aqui você pode usar o valor $valor_total para inserir no banco de dados
?>