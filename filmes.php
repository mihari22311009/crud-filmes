<?php

$filmes = [
    ["id" => 1, "titulo" => "Matrix",          "ano" => 1999, "genero" => "Ficção Científica"],
    ["id" => 2, "titulo" => "O Poderoso Chefão","ano" => 1972, "genero" => "Drama"],
    ["id" => 3, "titulo" => "Interestelar",     "ano" => 2014, "genero" => "Ficção Científica"],
    ["id" => 4, "titulo" => "ta dando onda",     "ano" => 2007, "genero" => "comedia"],
    ["id" => 5, "titulo" => "Parasita",         "ano" => 2019, "genero" => "Drama"],
];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'incluir') {
    $novoId = count($filmes) > 0 ? max(array_column($filmes, 'id')) + 1 : 1;
    $filmes[] = [
        "id"     => $novoId,
        "titulo" => htmlspecialchars($_POST['titulo']),
        "ano"    => (int)$_POST['ano'],
        "genero" => htmlspecialchars($_POST['genero']),
    ];
    $mensagem = "✅ Filme '{$_POST['titulo']}' adicionado com sucesso!";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'editar') {
    foreach ($filmes as &$filme) {
        if ($filme['id'] == $_POST['id']) {
            $filme['titulo'] = htmlspecialchars($_POST['titulo']);
            $filme['ano']    = (int)$_POST['ano'];
            $filme['genero'] = htmlspecialchars($_POST['genero']);
            $mensagem = "✏️ Filme atualizado com sucesso!";
            break;
        }
    }
    unset($filme);
}


if (isset($_GET['remover'])) {
    $idRemover = (int)$_GET['remover'];
    foreach ($filmes as $chave => $filme) {
        if ($filme['id'] === $idRemover) {
            $tituloRemovido = $filme['titulo'];
            unset($filmes[$chave]);
            $mensagem = "🗑️ Filme '{$tituloRemovido}' removido!";
            break;
        }
    }
    $filmes = array_values($filmes);
}


$modoEdicao = false;
$filmeEditando = null;
if (isset($_GET['editar'])) {
    $idEditar = (int)$_GET['editar'];
    foreach ($filmes as $filme) {
        if ($filme['id'] === $idEditar) {
            $modoEdicao = true;
            $filmeEditando = $filme;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Filmes</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0f0f1a;
            color: #e0e0e0;
            min-height: 100vh;
            padding: 30px 20px;
        }
        h1 {
            text-align: center;
            font-size: 2rem;
            color: #e2b96f;
            margin-bottom: 30px;
            letter-spacing: 2px;
        }
        .container { max-width: 900px; margin: 0 auto; }


        .mensagem {
            background: #1a2e1a;
            border-left: 4px solid #4caf50;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }


        .form-card {
            background: #1a1a2e;
            border: 1px solid #2a2a4a;
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 30px;
        }
        .form-card h2 { color: #e2b96f; margin-bottom: 16px; font-size: 1.1rem; }
        .form-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 12px; }
        .form-grid input {
            background: #0f0f1a;
            border: 1px solid #3a3a5a;
            color: #e0e0e0;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 0.95rem;
            width: 100%;
        }
        .form-grid input:focus { outline: none; border-color: #e2b96f; }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-primary { background: #e2b96f; color: #0f0f1a; }
        .btn-danger  { background: #c0392b; color: #fff; font-size: 0.8rem; padding: 6px 12px; }
        .btn-warning { background: #e67e22; color: #fff; font-size: 0.8rem; padding: 6px 12px; }
        .btn-cancel  { background: #555; color: #fff; }
        .form-actions { display: flex; gap: 10px; margin-top: 12px; }


        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #1a1a2e; }
        thead th {
            padding: 12px 16px;
            text-align: left;
            color: #e2b96f;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        tbody tr { border-bottom: 1px solid #1a1a2e; transition: background 0.15s; }
        tbody tr:hover { background: #1a1a2e; }
        tbody td { padding: 12px 16px; font-size: 0.95rem; }
        .acoes { display: flex; gap: 8px; }
        .badge {
            background: #2a2a4a;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #a0a0d0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🎬 CRUD de Filmes</h1>

    <?php if (isset($mensagem)): ?>
        <div class="mensagem"><?= $mensagem ?></div>
    <?php endif; ?>


    <div class="form-card">
        <h2><?= $modoEdicao ? '✏️ Editar Filme' : '➕ Adicionar Novo Filme' ?></h2>
        <form method="POST">
            <input type="hidden" name="acao" value="<?= $modoEdicao ? 'editar' : 'incluir' ?>">
            <?php if ($modoEdicao): ?>
                <input type="hidden" name="id" value="<?= $filmeEditando['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <input type="text"   name="titulo" placeholder="Título do filme"
                       value="<?= $modoEdicao ? htmlspecialchars($filmeEditando['titulo']) : '' ?>" required>
                <input type="number" name="ano"    placeholder="Ano"
                       value="<?= $modoEdicao ? $filmeEditando['ano'] : '' ?>"
                       min="1888" max="2099" required>
                <input type="text"   name="genero" placeholder="Gênero"
                       value="<?= $modoEdicao ? htmlspecialchars($filmeEditando['genero']) : '' ?>" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= $modoEdicao ? 'Salvar Alterações' : 'Adicionar Filme' ?>
                </button>
                <?php if ($modoEdicao): ?>
                    <a href="filmes.php" class="btn btn-cancel">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>


    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Ano</th>
                <th>Gênero</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($filmes)): ?>
                <tr><td colspan="5" style="text-align:center; color:#666; padding:30px;">
                    Nenhum filme cadastrado.
                </td></tr>
            <?php else: ?>
                <?php foreach ($filmes as $filme): ?>
                <tr>
                    <td><?= $filme['id'] ?></td>
                    <td><?= htmlspecialchars($filme['titulo']) ?></td>
                    <td><?= $filme['ano'] ?></td>
                    <td><span class="badge"><?= htmlspecialchars($filme['genero']) ?></span></td>
                    <td>
                        <div class="acoes">
                            <a href="filmes.php?editar=<?= $filme['id'] ?>" class="btn btn-warning">Editar</a>
                            <a href="filmes.php?remover=<?= $filme['id'] ?>"
                               onclick="return confirm('Remover este filme?')"
                               class="btn btn-danger">Remover</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
