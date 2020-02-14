<?php

// Configuração inicial da página
require ('_config.php');

// Define o título "desta" página
$titulo = "Artigos";

// Opção ativa no menu principal
$menu = "artigos";

// Aponta para o CSS "desta" página. Ex.: /css/contatos.css
// Deixe vazio para não usar CSS adicional nesta página
$css = "/css/artigos.css";

// Aponta para o JavaScript "desta" página. Ex.: /js/contatos.js
// Deixe vazio para não usar JavaScript adicional nesta página
$js = "";

/*********************************************/
/*  SEUS CÓDIGOS PHP DESTA PÁGINA FICAM AQUI */
/*********************************************/

// 'Declarar' variáveis
$artigos = '';      // Armazena a view de artigos
$categorias = '';   // Armazena a view de categorias

// Monta query que obtém a lista de artigos
$sql = <<<SQL

SELECT id_artigo, thumb_artigo, titulo, resumo
FROM artigos
WHERE
    status_artigo = 'ativo'
    AND
    data_artigo <= NOW()
ORDER BY data_artigo DESC;

SQL;

// Executar a query
$res = $conn->query($sql);

// Cria subtítulo com total de artigos
$total = $res->num_rows;
if ( $total > 1) $subtitulo = "Total de {$total} artigos. Mais recentes primeiro.";
else $subtitulo = "Total de {$total} artigo. Mais recentes primeiro.";

// Obter cada registro e gerar a view
while ( $art = $res->fetch_assoc() ):

    $artigos .= <<<TEXTO

<div class="artigo">
    <a href="/artigo.php?id={$art['id_artigo']}">
        <img src="{$art['thumb_artigo']}" alt="{$art['titulo']}">
        <h3>{$art['titulo']}</h3>
    </a>
    <span>{$art['resumo']}</span>
</div>

TEXTO;

endwhile;

// Obtendo os nomes das categorias
$sql = "SELECT * FROM categorias";
$res = $conn->query($sql);

// Preparação da view
$categorias .= '<ul>';

// Obtendo cada registro
while ( $cat = $res->fetch_assoc()) :

    $sql2 = <<<SQL
SELECT COUNT(id_art_cat) AS totalart
FROM art_cat 
WHERE categoria_id = '{$cat['id_categoria']}';
SQL;
    $res2 = $conn->query($sql2);
    
    // Total de artigos nesta categoria
    $tot =  $res2->fetch_assoc() ;
    $totalart = intval( $tot['totalart'] );

    // Se o total de artigos for maior que 0
    if ( $totalart > 0 ) {
        $categorias .= <<<TEXTO
        <li>
            <a href="/artigos.php?cat={$cat['id_categoria']}">{$cat['categoria']}</a>
            <sup>{$totalart}</sup>
        </li>
TEXTO;
    }

endwhile;
$categorias .= <<<TEXTO
    
    <hr>
    <li>
        <a href="/artigos.php">Todas as categorias</a>
    </li>
</ul>
TEXTO;


/************************************************/
/*  SEUS CÓDIGOS PHP DESTA PÁGINA TERMINAM AQUI */
/************************************************/

// Inclui o cabeçalho do template
require ('_header.php');

?>

<div class="row">
    <div class="col1">

        <h2>Artigos</h2>
        <p class="totalart"><?php echo $subtitulo ?></p>
        <?php echo $artigos ?>

    </div>
    <div class="col2">

        <h3>Categorias</h3>
        <?php echo $categorias  ?>

    </div>
</div>

<?php

// Inclui o rodapé do template
require ('_footer.php');

?>
