<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeto Integrador - UNIVESP</title>
    <link rel="stylesheet" href="styles.css"> <!-- Se estiver usando um CSS externo -->
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f4ea;
        }

        header {
            background-color: #2c3e50;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        header h1 {
            margin: 0;
            font-size: 1.4em;
        }

        nav a {
            color: #f1c40f;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #ffffff;
        }
        /* sr-only utility (revelar quando em foco) */
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
        .sr-only:focus { position: static; width: auto; height: auto; margin: 0; overflow: visible; clip: auto; white-space: normal; }
    </style>
</head>
<body>
    <a href="#main-content" class="sr-only">Pular para o conteúdo</a>

<header role="banner">
    <h1>Projeto Integrador - UNIVESP</h1>
    <nav role="navigation" aria-label="Menu principal">
        <a href="index.php" class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Início</a>
        <a href="integrantes.php" class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Integrantes</a>
        <a href="eventos.php" class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Eventos</a>
        <a href="contato.php" class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Contato</a>
    </nav>
</header>
