<?php 
session_start();
// O arquivo 'db.php' é incluído aqui
include('db.php');

$cidade = $_GET['cidade'] ?? '';
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$query = $_GET['query'] ?? '';

// Montagem da Query SQL principal
$sql = "SELECT * FROM eventos WHERE 1=1";
$params = [];

if (!empty($query)) {
    $sql .= " AND (titulo LIKE :query OR descricao LIKE :query)";
    $params[':query'] = "%$query%";
}

if (!empty($cidade)) {
    $sql .= " AND cidade LIKE :cidade";
    $params[':cidade'] = "%$cidade%";
}

if (!empty($data_inicio)) {
    $sql .= " AND data >= :data_inicio";
    $params[':data_inicio'] = $data_inicio;
}

if (!empty($data_fim)) {
    $sql .= " AND data <= :data_fim";
    $params[':data_fim'] = $data_fim;
}
$sql .= " ORDER BY (data < CURDATE()), data ASC"; // eventos futuros/ativos primeiro, encerrados por último

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$eventos = $stmt->fetchAll();

// Seleciona apenas eventos futuros para o carrossel
$sql_carrossel = "SELECT * FROM eventos WHERE data >= CURDATE() ORDER BY data ASC LIMIT 5";
$stmt_carrossel = $pdo->prepare($sql_carrossel);
$stmt_carrossel->execute();
$eventos_carrossel = $stmt_carrossel->fetchAll();

// Seleciona todas as cidades distintas para o filtro
$sql_cidades = "SELECT DISTINCT cidade FROM eventos ORDER BY cidade";
$stmt_cidades = $pdo->prepare($sql_cidades);
$stmt_cidades->execute();
$cidades = $stmt_cidades->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Eventos Baixada Santista</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="styles.css">
<style>
/* Configuração global da fonte */
body {
    font-family: Verdana, Geneva, Tahoma, sans-serif;
}

/* Estilo global para foco */
:focus {
    outline: 3px solid #f97316 !important; /* Cor laranja de destaque */
    outline-offset: 2px !important;
}

/* Estilo específico para inputs e selects */
input:focus, select:focus, textarea:focus {
    outline: 3px solid #f97316 !important;
    outline-offset: 2px !important;
    border-color: #f97316 !important;
}

/* Remove outline quando não é foco via teclado */
.js-focus-visible :focus:not(.focus-visible) {
    outline: none !important;
}

/* Visually hidden (screen-reader only) utility + reveal on focus */
.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
.sr-only:focus { position: static; width: auto; height: auto; margin: 0; overflow: visible; clip: auto; white-space: normal; }

.swiper {
    max-width: 600px;
    margin: 0 auto;
    position: relative;
}

.swiper-slide img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 1rem;
    margin: 0 auto;
}

.swiper-button-next, .swiper-button-prev {
    top: 50%;
    width: 36px;
    height: 36px;
    background-color: rgba(255, 255, 255, 0.9);
    color: #4f46e5;
    border-radius: 9999px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    z-index: 10;
    font-size: 18px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.swiper-button-next:hover, .swiper-button-prev:hover {
    background-color: #e0e7ff;
}

.swiper-button-prev {
    left: 8px;
}

.swiper-button-next {
    right: 8px;
}


.swiper-pagination {
    display: none !important; /* esconder bullets do carrossel conforme solicitado */
}

.swiper-pagination-bullet {
    background: #c4b5fd;
    opacity: 1;
    width: 12px;
    height: 12px;
    margin: 0 5px;
}

.swiper-pagination-bullet:focus {
    outline: 3px solid #f97316 !important;
    outline-offset: 2px !important;
}

.swiper-pagination-bullet-active {
    background: #4f46e5;
    width: 16px;
    height: 16px;
}

/* Botões de navegação do carrossel */
.swiper-button-next:focus,
.swiper-button-prev:focus {
    outline: 3px solid #f97316 !important;
    outline-offset: 2px !important;
}
</style>
</head>
<body class="bg-gray-50 min-h-screen">
    <a href="#main-content" class="sr-only">Pular para o conteúdo</a>

<header class="bg-[#4C32CC] **text-white** p-4 w-full shadow-md">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex-shrink-1">
            <span class="text-base font-semibold **text-white** flex flex-col w-full group" tabindex="0" aria-label="Título do projeto">
                <span>🎭 Projeto Integrador II - Baixada em Cena</span>
                <span class="text-right ml-auto">
                    <span class="font-serif italic **text-indigo-300** opacity-0 transform translate-y-1 transition-all duration-200 group-hover:opacity-100 group-hover:translate-y-0 group-focus:opacity-100 group-focus:translate-y-0" aria-hidden="false">+ inclusiva</span>
                </span>
            </span>
        </div>
<form method="GET" action="index.php" class="flex flex-1 justify-center mx-2">
            <div class="flex items-center w-full max-w-md border border-gray-300 rounded-lg px-3 py-1 bg-white shadow-sm hover:shadow-md transition">
                <label for="search-query" class="sr-only">Pesquisar eventos, teatros, shows e cursos</label>
                <input id="search-query" type="text" name="query" placeholder="Pesquisar eventos, teatros, shows, cursos" 
                        class="flex-1 border-none outline-none text-sm pl-2 text-gray-800"
                        value="<?php echo htmlspecialchars($query); ?>" />
                <button type="submit" class="bg-indigo-600 text-white px-3 py-1 text-sm rounded-md shadow hover:bg-indigo-700 transition ml-2 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Buscar
                </button>
            </div>
        </form>

        <div class="flex items-center space-x-3" role="navigation" aria-label="Menu principal">
    <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
        <span class="text-white text-sm">
            Bem-vindo, <?php echo isset($_SESSION['usuario_nome']) ? htmlspecialchars($_SESSION['usuario_nome']) : 'Visitante'; ?>
        </span>
    <a href="criar-eventos.php" class="bg-indigo-600 text-white px-3 py-1 text-sm rounded-md shadow hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Criar Evento</a>
    <a href="gerenciar-eventos.php" class="bg-indigo-600 text-white px-3 py-1 text-sm rounded-md shadow hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Gerenciar</a>
    <a href="integrantes.php" class="bg-indigo-600 text-white px-3 py-1 text-sm rounded-md shadow hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Integrantes</a>
    <a href="logout.php" class="bg-red-100 text-red-600 px-3 py-1 text-sm rounded-md shadow hover:bg-red-200 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Sair</a>
    <?php else: ?>
    <a href="login.php" class="bg-indigo-600 text-white px-3 py-1 text-sm rounded-md shadow hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Login</a>
    <?php endif; ?>
</div>
</header>


    <main id="main-content" role="main" tabindex="-1">
<?php if ($eventos_carrossel): ?>
<section id="carrossel-destaques" class="max-w-7xl mx-auto mt-8" aria-label="Carrossel de eventos em destaque">
    <div class="sr-only">Carrossel automático com eventos em destaque da Baixada Santista. Use as setas esquerda e direita para navegar ou o botão de pausa para controlar a rotação automática.</div>
    <div id="carrossel-status" class="sr-only" aria-live="polite"></div>
    <div class="swiper mySwiper" role="region" aria-roledescription="carrossel">
        <div class="swiper-wrapper" aria-atomic="false">
            <?php foreach ($eventos_carrossel as $evento): ?>
            <div class="swiper-slide bg-white rounded-xl shadow-lg" role="group" aria-roledescription="slide">
                <?php if (!empty($evento['imagem']) && file_exists(__DIR__ . '/' . $evento['imagem'])): ?>
                    <?php
                        // Texto alternativo mais descritivo para o evento específico "Tardezinha da noite"
                        $alt_text = $evento['titulo'];
                        if (stripos($evento['titulo'], 'tardezinha') !== false) {
                            $alt_text = 'Cartaz do evento "Tardezinha da Noite" — imagem do palco com iluminação roxa e público, destacando a programação musical.';
                        }
                    ?>
                    <img src="<?php echo $evento['imagem']; ?>" 
                            alt="<?php echo htmlspecialchars($alt_text); ?>"
                            role="img"
                            aria-label="<?php echo htmlspecialchars($evento['titulo']) . ' em ' . htmlspecialchars($evento['cidade']) . ' no dia ' . date('d/m/Y', strtotime($evento['data'])); ?>">
                <?php else: ?>
                    <div class="w-full h-72 bg-gray-300 flex items-center justify-center rounded-xl" role="img" aria-label="Imagem não disponível">
                        <span class="text-gray-700">Sem imagem</span>
                    </div>
                <?php endif; ?>
                <div class="p-4 text-center">
                    <h2 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($evento['titulo']); ?></h2>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($evento['cidade']); ?> - <?php echo date('d/m/Y', strtotime($evento['data'])); ?></p>
                    <a href="evento.php?id=<?php echo $evento['id']; ?>" 
                        class="inline-block mt-2 text-indigo-600 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                        aria-label="Ver detalhes do evento <?php echo htmlspecialchars($evento['titulo']); ?>">
                        Ver detalhes
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination" aria-hidden="false"></div>
        <div class="swiper-button-next" aria-label="Próximo slide"></div>
        <div class="swiper-button-prev" aria-label="Slide anterior"></div>
    </div>
    <div id="pause-button-container" class="text-center mt-4 mb-8"></div>
</div>
<?php endif; ?>

<div class="max-w-7xl mx-auto p-6">
    <form method="GET" class="flex flex-wrap justify-center gap-6 bg-white p-6 rounded-lg shadow max-w-4xl mx-auto **items-end**">
    <div>
            <label for="select-cidade" class="block text-sm font-medium text-gray-700">Cidade:</label>
            <select id="select-cidade" name="cidade" class="border-2 border-gray-300 rounded-lg px-4 py-2 w-56">
                <option value="">Selecione uma cidade</option>
                <?php foreach ($cidades as $cidade_opcao): ?>
                    <option value="<?php echo htmlspecialchars($cidade_opcao['cidade']); ?>" <?php if ($cidade == $cidade_opcao['cidade']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cidade_opcao['cidade']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="data-inicio" class="block text-sm font-medium text-gray-700">Data de Início:</label>
            <input type="date" id="data-inicio" name="data_inicio" value="<?php echo htmlspecialchars($data_inicio); ?>" class="border-2 border-gray-300 rounded-lg px-4 py-2 w-56">
        </div>
        <div>
            <label for="data-fim" class="block text-sm font-medium text-gray-700">Data de Fim:</label>
            <input type="date" id="data-fim" name="data_fim" value="<?php echo htmlspecialchars($data_fim); ?>" class="border-2 border-gray-300 rounded-lg px-4 py-2 w-56">
        </div>
    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Filtrar</button>
    </form>
</div>

<div class="max-w-7xl mx-auto p-6">
    <?php if ($eventos): ?>
        <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($eventos as $evento): ?>
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="relative mb-4">
    <?php if (!empty($evento['imagem']) && file_exists(__DIR__ . '/' . $evento['imagem'])): ?>
        <?php
            $card_alt = $evento['titulo'];
            if (stripos($evento['titulo'], 'tardezinha') !== false) {
                $card_alt = 'Cartaz do evento "Tardezinha da Noite" mostrando artistas ao vivo e público no teatro.';
            }
        ?>
        <img src="<?php echo $evento['imagem']; ?>" alt="<?php echo htmlspecialchars($card_alt); ?>" class="w-full h-48 object-cover rounded-lg">
    <?php else: ?>
        <div class="w-full h-48 bg-gray-300 rounded-lg flex items-center justify-center">
             <span class="text-gray-700">Sem imagem</span>
        </div>
    <?php endif; ?>

    <?php if (strtotime($evento['data']) < time()): ?>
        <div class="absolute inset-0 bg-black bg-opacity-60 rounded-lg flex items-center justify-center">
            <span class="text-white text-lg font-bold transform -rotate-12">Evento Encerrado</span>
        </div>
    <?php endif; ?>
</div>

                    <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($evento['titulo']); ?></h3>
                    <p class="text-sm text-gray-600"><?php echo date('d/m/Y', strtotime($evento['data'])); ?></p>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($evento['cidade']); ?></p>
                                 <a href="evento.php?id=<?php echo $evento['id']; ?>" class="block mt-4 text-center text-indigo-600 font-semibold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Ver detalhes</a>
                    
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-600">Nenhum evento encontrado.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    // 1. Inicialização do Swiper
    const swiper = new Swiper('.swiper', {
        slidesPerView: 1,
        spaceBetween: 10,
        autoplay: {
            delay: 4000, // Aumentado para 4 segundos para melhor acessibilidade
            disableOnInteraction: false,
        },
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            bulletElement: 'button',
            renderBullet: function (index, className) {
                // Melhoria: Garante que os bullets sejam botões acessíveis
                return `<button class="${className}" aria-label="Ir para slide ${index + 1}"></button>`;
            },
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },
        keyboard: {
            enabled: true,
            onlyInViewport: true,
        },
        a11y: {
            enabled: true,
            prevSlideMessage: 'Slide anterior',
            nextSlideMessage: 'Próximo slide',
            firstSlideMessage: 'Este é o primeiro slide',
            lastSlideMessage: 'Este é o último slide',
            paginationBulletMessage: 'Ir para slide {{index}}',
            slideLabelMessage: 'Slide {{index}} de {{slidesLength}}',
            containerMessage: 'Carrossel de slides',
            containerRoleDescriptionMessage: 'carrossel',
            itemRoleDescriptionMessage: 'slide',
        },
        on: {
            slideChange: function() {
                // Anuncia o slide atual para leitores de tela
                const status = document.getElementById('carrossel-status');
                // Verifica se o slide ativo é válido (evita slides duplicados do loop)
                const realIndex = this.realIndex; 
                const activeSlide = document.querySelectorAll('.swiper-slide-duplicate, .swiper-slide')[realIndex];

                if (activeSlide) {
                    const titulo = activeSlide.querySelector('h2').textContent;
                    const cidadeData = activeSlide.querySelector('p').textContent;
                    status.textContent = `Exibindo slide ${realIndex + 1}: ${titulo} - ${cidadeData}`;
                }
            }
        }
    });


</script>
    </main>
</body>
</html>