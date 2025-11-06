<?php
$integrantes = [
    ["nome" => "Ailton Lustosa de Sousa", "ra" => "23208653", "descricao" => "POLO: PRAIA GRANDE"],
    ["nome" => "Natiele Portela Araujo", "ra" => "23209445", "descricao" => "POLO: PRAIA GRANDE"],
    ["nome" => "Danilo Alves Faria", "ra" => "23211175", "descricao" => "POLO: SANTOS"],
    ["nome" => "Erick Ribeiro de Aragão", "ra" => "23210111", "descricao" => "POLO: SÃO VICENTE"],
    ["nome" => "Glaucio Dias de Jesus", "ra" => "23224964", "descricao" => "POLO: SÃO VICENTE"],
    ["nome" => "Tatiane Venancio dos Santos", "ra" => "23226311", "descricao" => "POLO: ITARIRI"],
    // ["nome" => "Claudiane Ferreira de Santana", "ra" => "2204838", "descricao" => "POLO: GUARUJÁ"],
    ["nome" => "Ricardo Nonato Oliveira da Silva", "ra" => "23202002", "descricao" => "POLO: PRAIA GRANDE"]
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Projeto Integrador 2 - Baixada em Cena</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <style>
    html { scroll-behavior: smooth; }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 text-sm">

  <!-- Menu -->
  <header class="fixed top-0 left-0 w-full bg-white shadow-md z-50">
    <nav class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
      <span class="text-base font-semibold text-blue-700">🎭 Projeto Integrador II - Baixada em Cena</span>
      <ul class="flex space-x-5 text-xs font-medium">
        <li><a href="#sobre" class="hover:text-blue-600">Sobre</a></li>
        <li><a href="#integrantes" class="hover:text-blue-600">Integrantes</a></li>
        <li><a href="index.php" class="hover:text-blue-600">Home</a></li>
      </ul>
    </nav>
  </header>

  <div class="h-6"></div> <!-- Espaço para o menu fixo -->

  <!-- Seção Sobre -->
  <section id="sobre" class="bg-white py-16 px-6">
    <div class="max-w-4xl mx-auto text-center">
      <h2 class="text-2xl font-semibold text-blue-700 mb-5">Sobre o Projeto</h2>
      <div class="text-sm text-gray-700 leading-relaxed text-left">
        <p class="mb-3"><strong class="text-blue-700">🎓 Título do Trabalho:</strong> <em>Baixada em Cena: Mapeamento e Promoção de Eventos Culturais na Baixada Santista + inclusiva</em></p>
        <p class="mb-3"><strong class="text-blue-700">👨‍🏫 Orientador do PI:</strong> Matheus Sanches de Sá Bergamo </p>
        <p class="mb-3"><strong class="text-blue-700">🎯 Objetivo Geral:</strong> Mapear e promover eventos culturais na Baixada Santista de maneira acessível.</p>
        <p><strong class="text-blue-700">📌 Objetivos Específicos:</strong></p>
        <ul class="list-disc list-inside ml-4 text-gray-600 mt-2">
          <li>Identificar os principais desafios de acesso para pessoas com deficiência;</li>
          <li>Desenvolver uma plataforma digital responsiva e acessível para centralizar informações sobre eventos culturais;</li>
          <li>Promover a participação da comunidade em atividades culturais.</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Seção Integrantes -->
  <section id="integrantes" class="bg-gray-100 py-16 px-6">
    <div class="max-w-7xl mx-auto">
      <h2 class="text-xl font-semibold text-center text-blue-700 mb-8">👥 Integrantes do Projeto</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($integrantes as $index => $integrante): ?>
          <div class="bg-white rounded-xl shadow hover:shadow-md transition-transform transform hover:-translate-y-1 p-4 text-center">
            <img src="uploads/integrante<?= $index + 1 ?>.jpg" alt="<?= $integrante['nome'] ?>" class="w-28 h-36 object-cover mx-auto mb-3 rounded-lg border border-gray-300 shadow-sm" />
            <p class="font-medium text-gray-900 text-sm"><?= $integrante['nome'] ?></p>
            <p class="text-xs text-gray-500 mb-1">RA: <?= $integrante['ra'] ?></p>
            <p class="text-xs text-gray-700 italic"><?= $integrante['descricao'] ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Seção Contato -->
  <section id="contato" class="bg-white py-16 px-6">
    <div class="max-w-2xl mx-auto text-center">
      <h2 class="text-xl font-semibold text-blue-700 mb-4">📬 Contato</h2>
      <p class="text-sm text-gray-700 mb-3">Entre em contato conosco para dúvidas, sugestões ou parcerias:</p>
      <p class="text-blue-600 font-semibold text-base">projetointegrador@email.com</p>
    </div>
  </section>

  <!-- Rodapé -->
  <footer class="bg-gray-200 text-center text-xs text-gray-600 py-3">
    &copy; <?= date("Y") ?> Baixada em Cena — Todos os direitos reservados.
  </footer>

</body>
</html>
