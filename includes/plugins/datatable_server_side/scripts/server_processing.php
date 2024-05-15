<?php
function DBConnectMy()
{	
	$link = mysql_connect('mysql27.alexandrejr.com.br','alexandrejr25', 'raj15finsoldes');
	mysql_select_db('alexandrejr25', $link);
	
	return $link;
}

$ConexaoMy = DBConnectMy();

require( 'ssp.class.php' );
$SSP = new SSP();

$request = $_GET;

$limit = $SSP->limit($request, $columns );
$order = $SSP->order($request, $columns );
$where = $SSP->filter($request, $columns, $bindings );

$dados = array();
$resTotalLength = 0;
$recordsFiltered = 0;

//* QTD TOTAL *//
$SQL = " SELECT count(*) as qtd FROM ncm WHERE 1=1 ";
if(trim($_POST['filtro_descricao']))
{
	$SQL .= " AND nome_ncm like '%".$_POST['filtro_descricao']."%' ";	
}
if(trim($_POST['filtro_situacao']) != "")
{
	$SQL .= " AND situacao = ".$_POST['filtro_situacao']." ";
}
$Query = mysql_query($SQL, $ConexaoMy);
while($Aux = mysql_fetch_assoc($Query))
{
	$recordsTotal = $Aux['qtd'];
}

//* QTD *//
$SQL .= " ".$where;
$Query = mysql_query($SQL, $ConexaoMy);
while($Aux = mysql_fetch_assoc($Query))
{
	$recordsFiltered = $Aux['qtd'];
}

//* DADOS *//
$SQL .= " ".$order;
$SQL .= " ".$limit;
$Query = mysql_query($SQL, $ConexaoMy);
while($Aux = mysql_fetch_assoc($Query))
{
	$Aux = array_map("utf8_encode", $Aux);
	$dados[] = $Aux;
}

$Arr = array(
	"draw"            => isset ( $request['draw'] ) ?
		intval( $request['draw'] ) :
		0,
	"recordsTotal"    => intval( $recordsTotal ),
	"recordsFiltered" => intval( $recordsFiltered ),
	"data"            => $dados
);

echo json_encode($Arr);