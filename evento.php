<?php
session_start();
include('db.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    http_response_code(400);
    echo "<p>ID de evento inválido.</p>";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$evento = $stmt->fetch();

if (!$evento) {
    http_response_code(404);
    echo "<p>Evento não encontrado.</p>";
    exit;
}

// formatações seguras
function e($v) { return htmlspecialchars($v); }

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title><?php echo e($evento['titulo']); ?> — Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <!-- Container para notificações acessíveis -->
    <div id="notifications" role="status" aria-live="polite" class="sr-only"></div>
    <style>
        :focus {
            outline: 3px solid #f97316; /* Cor laranja de destaque */
            outline-offset: 2px;
        }

        .js-focus-visible :focus:not(.focus-visible) {
            outline: none;
        }

        /* sr-only utility (revelar quando em foco) */
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
        .sr-only:focus { position: static; width: auto; height: auto; margin: 0; overflow: visible; clip: auto; white-space: normal; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <a href="#main-content" class="sr-only">Pular para o conteúdo</a>
    <header class="bg-[#4C32CC] text-white p-4 shadow-md">
        <div class="max-w-6xl mx-auto flex items-center justify-between" role="navigation" aria-label="Menu principal">
            <h1 class="text-xl font-bold"><?php echo e($evento['titulo']); ?></h1>
            <a href="index.php" class="bg-indigo-600 text-white px-3 py-1 text-sm rounded-md shadow hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Voltar</a>
        </div>
    </header>

    <main id="main-content" role="main" tabindex="-1" class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <?php if (!empty($evento['imagem']) && file_exists(__DIR__ . '/' . $evento['imagem'])): ?>
                <div class="relative">
                    <img src="<?php echo e($evento['imagem']); ?>" 
                         alt="<?php echo e($evento['titulo']); ?>" 
                         aria-describedby="img-desc-<?php echo $evento['id']; ?>"
                         class="w-full h-64 object-cover">
                    <p id="img-desc-<?php echo $evento['id']; ?>" class="sr-only">
                        Imagem do evento <?php echo e($evento['titulo']); ?>, que acontece em <?php echo e($evento['cidade']); ?> 
                        no dia <?php echo date('d/m/Y', strtotime($evento['data'])); ?>
                        <?php if (!empty($evento['horario'])): ?> às <?php echo date('H:i', strtotime($evento['horario'])); ?><?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="w-full h-64 bg-gray-200 flex items-center justify-center" role="img" aria-label="Evento sem imagem">
                    <span class="text-gray-600">Sem imagem disponível</span>
                </div>
            <?php endif; ?>

        <div class="p-6">
            <p class="text-sm text-gray-600 mb-2">
                <?php echo (!empty($evento['cidade']) ? e($evento['cidade']) . ' - ' : ''); ?>
                <?php echo date('d/m/Y', strtotime($evento['data'])); ?>
                <?php if (!empty($evento['horario'])): ?> às <?php echo date('H:i', strtotime($evento['horario'])); ?><?php endif; ?>
            </p>

            <h2 class="text-2xl font-semibold mb-4"><?php echo e($evento['titulo']); ?></h2>

            <div class="prose max-w-none text-gray-800">
                <?php echo nl2br(e($evento['descricao'])); ?>
            </div>

        <div class="mt-6 flex space-x-3">
        <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] === true && isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $evento['usuario_id']): ?>
        <a href="editar-evento.php?id=<?php echo $evento['id']; ?>" class="bg-yellow-500 text-white px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Editar</a>
        <a href="excluir-evento.php?id=<?php echo $evento['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este evento?');" class="bg-red-600 text-white px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Excluir</a>
        <?php endif; ?>

            <a href="index.php" class="bg-indigo-600 text-white px-3 py-1 text-sm rounded-md shadow hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">Voltar à lista</a>
        </div>

        <?php
         // URL completa do evento
              $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
             $share_text = rawurlencode($evento['titulo'] . ' - Confira este evento: ' . $current_url);
            $share_url = rawurlencode($current_url);
            ?>

         <div class="mt-4 flex justify-end">
             <div class="text-right">
         <p class="text-sm font-medium text-gray-700 mb-2">Compartilhe nas redes sociais!</p>
        <div class="inline-flex items-center bg-gray-50 p-2 rounded-lg space-x-2">
         <!-- WhatsApp  -->
         <a href="https://api.whatsapp.com/send?text=<?php echo $share_text; ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center w-10 h-10 rounded bg-green-500 shadow-md transform transition hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500" aria-label="Compartilhar no WhatsApp" title="Compartilhar no WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-5 h-5" aria-hidden="true"><path d="M20.52 3.48A11.83 11.83 0 0012.02.5C6.86.5 2.5 4.86 2.5 10c0 1.77.47 3.5 1.36 5.06L2 22l6.72-1.77A11.45 11.45 0 0012 21.5c5.14 0 9.5-4.36 9.5-9.5 0-1.86-.52-3.6-1.0-5.02zM12.02 19c-.7 0-1.38-.18-1.98-.5l-.14-.07-3.98 1.05 1.07-3.86-.09-.14A7.45 7.45 0 014.5 10c0-4.14 3.36-7.5 7.52-7.5 4.16 0 7.52 3.36 7.52 7.5 0 4.14-3.36 7.5-7.52 7.5z"/></svg>
        </a>

         <!-- Facebook  -->
         <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center w-10 h-10 rounded bg-blue-600 shadow-md transform transition hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500" aria-label="Compartilhar no Facebook" title="Compartilhar no Facebook">
             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-5 h-5" aria-hidden="true"><path d="M22 12a10 10 0 10-11.5 9.9v-7h-2.2V12h2.2V9.8c0-2.2 1.3-3.4 3.2-3.4.9 0 1.8.16 1.8.16v2h-1c-1 0-1.3.62-1.3 1.2V12h2.3l-.37 2.9h-1.93v7A10 10 0 0022 12z"/></svg>
         </a>

        <!-- X  -->
    <a href="https://twitter.com/intent/tweet?text=<?php echo rawurlencode($evento['titulo']); ?>&url=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center w-10 h-10 rounded bg-black shadow-md transform transition hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500" aria-label="Compartilhar no X" title="Compartilhar no X">
        <!-- ícone X  -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" aria-hidden="true" role="img">
         <!--  X (branco) -->
        <path fill="white" d="M3.6 3.6 L9.4 3.6 L12 8.0 L14.6 3.6 L20.4 3.6 L14.6 10.4 L20.4 20.4 L14.6 20.4 L12 15.6 L9.4 20.4 L3.6 20.4 L9.4 10.4 Z" />
        </svg>
        </a>

        <!-- Copiar link  -->
    <button id="copyLinkBtn" class="inline-flex items-center justify-center w-10 h-10 rounded bg-gray-700 shadow-md transform transition hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500" aria-label="Copiar link do evento" title="Copiar link do evento">
         <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-5 h-5" aria-hidden="true"><path d="M3.9 12.5a3.6 3.6 0 010-5.1l3.5-3.5a3.6 3.6 0 015.1 5.1l-1.1 1.1a1 1 0 01-1.4-1.4l1.1-1.1a1.6 1.6 0 10-2.2-2.2L8.5 9.9a1.6 1.6 0 102.2 2.2l1.1-1.1a1 1 0 011.4 1.4l-1.1 1.1a3.6 3.6 0 01-5.1 0l-3.5-3.5z"/></svg>
        </button>
                  </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </main>
</body>
</html>

<script>
// Copiar link para a área de transferência
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('copyLinkBtn');
    if (!btn) return;
    btn.addEventListener('click', function() {
        const url = window.location.href;
        const notifyEl = document.getElementById('notifications');
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function(){
                // Notificação acessível
                notifyEl.textContent = 'Link copiado para a área de transferência';
                // Alerta visual
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg transition-opacity duration-300';
                toast.textContent = 'Link copiado!';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }, function(){
                prompt('Copie o link manualmente:', url);
                notifyEl.textContent = 'Não foi possível copiar automaticamente. Use o diálogo para copiar manualmente.';
            });
        } else {
            prompt('Copie o link manualmente:', url);
            notifyEl.textContent = 'Seu navegador não suporta cópia automática. Use o diálogo para copiar manualmente.';
        }
    });
});
</script>
