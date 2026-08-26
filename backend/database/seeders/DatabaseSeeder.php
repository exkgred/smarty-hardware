<?php

namespace Database\Seeders;

use App\Modules\Chatbot\Infrastructure\EloquentKnowledgeModel;
use App\Modules\Inventory\Domain\StockMovementTypeEnum;
use App\Modules\Inventory\Infrastructure\EloquentStockMovementModel;
use App\Modules\Product\Infrastructure\EloquentCategoryModel;
use App\Modules\Product\Infrastructure\EloquentProductModel;
use App\Modules\User\Infrastructure\EloquentUserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        EloquentUserModel::query()->create([
            'name' => 'Admin Smarty',
            'email' => 'admin@marketplace.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        EloquentUserModel::query()->create([
            'name' => 'Cliente Smarty',
            'email' => 'cliente@marketplace.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        EloquentUserModel::query()->create([
            'name' => 'Cliente balcão',
            'email' => 'balcao@smartyhardware.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        $categories = [
            ['name' => 'Processadores', 'slug' => 'processadores', 'description' => 'AMD Ryzen e Intel Core para AM4 e LGA1151'],
            ['name' => 'Placas de vídeo', 'slug' => 'placas-de-video', 'description' => 'NVIDIA RTX e setups gamer'],
            ['name' => 'Placas-mãe', 'slug' => 'placas-mae', 'description' => 'Placas TUF e plataformas Intel/AMD'],
            ['name' => 'Memória e storage', 'slug' => 'memoria-e-storage', 'description' => 'RAM Kingston, G.SKILL e discos rígidos'],
            ['name' => 'Gabinetes e cooling', 'slug' => 'gabinetes-cooling', 'description' => 'Torres RGB e water cooler'],
            ['name' => 'Periféricos', 'slug' => 'perifericos', 'description' => 'Teclados e mouses'],
            ['name' => 'Serviços', 'slug' => 'servicos', 'description' => 'Bancada de assistência técnica'],
        ];

        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds[$category['slug']] = EloquentCategoryModel::query()->create($category)->id;
        }

        $img = static fn (string $file) => '/images/products/'.$file;

        $products = [
            [
                'category' => 'processadores',
                'name' => 'AMD Ryzen 7 3700X — 8 núcleos / AM4',
                'slug' => 'amd-ryzen-7-3700x',
                'price' => 899.00,
                'stock' => 7,
                'image' => 'cpu-ryzen.jpg',
                'description' => '<p>Processador <strong>AMD Ryzen 7 3700X</strong> (código 100-000000071) em soquete <strong>AM4</strong>. 8 núcleos / 16 threads, 3,6 GHz base / 4,4 GHz boost, 65 W TDP.</p><ul><li>Compatível com placas B450, X470, B550 e X570</li><li>Ideal para edição, compilação e jogos em 1080p/1440p</li><li>Unidade fotografada na bancada Smarty</li></ul>',
            ],
            [
                'category' => 'processadores',
                'name' => 'Intel Core i9-9900K — 8 núcleos / 3.6 GHz',
                'slug' => 'intel-core-i9-9900k',
                'price' => 1099.00,
                'stock' => 4,
                'image' => 'cpu-intel.jpg',
                'description' => '<p><strong>Intel Core i9-9900K</strong> (SRELS) — 8 núcleos / 16 threads, 3,60 GHz, soquete LGA 1151 (Coffee Lake Refresh).</p><ul><li>Placas Z390 / Z370 com BIOS atualizada</li><li>Excelente para produtividade e jogos</li><li>Foto real do IHS na placa TUF da loja</li></ul>',
            ],
            [
                'category' => 'placas-de-video',
                'name' => 'NVIDIA GeForce RTX Founders Edition',
                'slug' => 'nvidia-geforce-rtx-founders-edition',
                'price' => 2499.00,
                'stock' => 5,
                'image' => 'gpu-rtx.jpg',
                'description' => '<p>Placa <strong>NVIDIA GeForce RTX Founders Edition</strong> com cooler axial duplo e chassi metálico. Na foto, ao lado de uma GTX 1080 Ti FE para referência de geração.</p><ul><li>Ray tracing e DLSS</li><li>Saídas DisplayPort / HDMI</li><li>Recomendamos fonte de 650 W 80 Plus</li></ul>',
            ],
            [
                'category' => 'placas-mae',
                'name' => 'ASUS TUF Gaming Z390 — LGA 1151',
                'slug' => 'asus-tuf-gaming-z390',
                'price' => 649.00,
                'stock' => 6,
                'image' => 'ram-ddr4.jpg',
                'description' => '<p>Placa-mãe <strong>ASUS TUF Gaming</strong> (chipset Z390) com camouflage militar, soquete LGA 1151 aberto, slots DDR4 e M.2 Ultra.</p><ul><li>Suporte a Core i5/i7/i9 8ª e 9ª geração</li><li>VRM com dissipador, PCIe x16 e M.2 Gen3</li><li>Ideal para o i9-9900K que também está no estoque</li></ul>',
            ],
            [
                'category' => 'memoria-e-storage',
                'name' => 'Memória Kingston DDR — módulos instalados',
                'slug' => 'memoria-kingston-ddr',
                'price' => 279.00,
                'stock' => 14,
                'image' => 'psu-real.jpg',
                'description' => '<p>Kit de memória <strong>Kingston</strong> fotografado nos slots da placa (chips pretos com silkscreen Kingston).</p><ul><li>Perfil estável para desktop e upgrade</li><li>Vendemos o par; instalação na bancada sob consulta</li></ul>',
            ],
            [
                'category' => 'memoria-e-storage',
                'name' => 'G.SKILL Trident Z RGB — 16 GB',
                'slug' => 'gskill-trident-z-rgb-16gb',
                'price' => 389.00,
                'stock' => 9,
                'image' => 'case.jpg',
                'description' => '<p>Módulos <strong>G.SKILL Trident Z RGB</strong> em um PC ROG da loja — dissipador alto, barra de LED e perfil gamer.</p><ul><li>Kit 2x8 GB (16 GB)</li><li>RGB sincronizável com ASUS Aura / MSI Mystic</li></ul>',
            ],
            [
                'category' => 'memoria-e-storage',
                'name' => 'HD interno 3,5" — 2 TB',
                'slug' => 'hd-interno-2tb',
                'price' => 419.00,
                'stock' => 11,
                'image' => 'hdd.jpg',
                'description' => '<p>Disco rígido interno 3,5" <strong>2 TB</strong> SATA. A foto mostra o prato e o atuador de uma unidade aberta na oficina (unidade de venda sai lacrada).</p><ul><li>Uso para mass storage, backup e DVR</li><li>7200 RPM classe desktop</li></ul>',
            ],
            [
                'category' => 'gabinetes-cooling',
                'name' => 'Water Cooler Corsair AIO 240 mm',
                'slug' => 'water-cooler-corsair-240',
                'price' => 549.00,
                'stock' => 8,
                'image' => 'ssd-nvme.jpg',
                'description' => '<p>Water cooler <strong>Corsair</strong> AIO com bomba RGB, mangueiras brancas e radiador 240 mm — o mesmo conjunto da build branca da vitrine.</p><ul><li>Suporte AM4 / LGA 115x / 1200 / 1700 (brackets na caixa)</li><li>Fans RGB inclusos</li></ul>',
            ],
            [
                'category' => 'gabinetes-cooling',
                'name' => 'Gabinete gamer mid-tower vidro — RGB',
                'slug' => 'gabinete-gamer-mid-tower-rgb',
                'price' => 449.00,
                'stock' => 6,
                'image' => 'psu.jpg',
                'description' => '<p>Gabinete mid-tower com vidro temperado, fans RGB, espaço para AIO no topo e GPU de três slots. Foto da torre montada na loja (Thermaltake + ROG).</p><ul><li>Painel frontal em malha / vidro</li><li>Gerenciamento de cabos traseiro</li></ul>',
            ],
            [
                'category' => 'perifericos',
                'name' => 'Teclado slim wireless alumínio',
                'slug' => 'teclado-slim-wireless-aluminio',
                'price' => 429.00,
                'stock' => 10,
                'image' => 'keyboard.jpg',
                'description' => '<p>Teclado compacto de alumínio com teclas chiclet de baixo perfil — o modelo da foto é layout internacional sem numérico.</p><ul><li>Uso escritório e setup limpo</li><li>Conexão sem fio</li></ul>',
            ],
            [
                'category' => 'perifericos',
                'name' => 'Teclado mecânico RGB + setup gamer',
                'slug' => 'teclado-mecanico-rgb',
                'price' => 319.00,
                'stock' => 13,
                'image' => 'repair-tech.jpg',
                'description' => '<p>Teclado mecânico com iluminação RGB vermelha, fotografado em uma bancada com mouse Logitech G502.</p><ul><li>Switches táteis, anti-ghosting</li><li>Cabo trançado</li></ul>',
            ],
            [
                'category' => 'perifericos',
                'name' => 'Mouse wireless ergonômico',
                'slug' => 'mouse-wireless-ergonomico',
                'price' => 149.00,
                'stock' => 22,
                'image' => 'mouse.jpg',
                'description' => '<p>Mouse sem fio com casco prateado, laterais texturizadas e sensor óptico para escritório e navegação.</p><ul><li>2,4 GHz / receptor USB</li><li>DPI ajustável no botão extra</li></ul>',
            ],
            [
                'category' => 'servicos',
                'name' => 'Limpeza interna + pasta térmica (notebook)',
                'slug' => 'limpeza-pasta-termica-notebook',
                'price' => 179.00,
                'stock' => 12,
                'image' => 'repair-clean.jpg',
                'description' => '<p>Abertura do notebook, remoção de poeira, troca de pasta térmica e teste de temperatura. Prazo médio: 24–48 h.</p><ul><li>Inclui relatório de temperaturas</li><li>Garantia de serviço: 30 dias</li></ul>',
            ],
            [
                'category' => 'servicos',
                'name' => 'Formatação, backup e instalação do sistema',
                'slug' => 'formatacao-backup-sistema',
                'price' => 199.00,
                'stock' => 15,
                'image' => 'repair-format.jpg',
                'description' => '<p>Backup dos arquivos da pasta do usuário, formatação, drivers e programas essenciais. Trazemos a máquina ou fazemos na loja.</p><ul><li>Windows 10/11 (mídia do cliente ou licença própria)</li><li>Não inclui recuperação de HD danificado</li></ul>',
            ],
            [
                'category' => 'servicos',
                'name' => 'Diagnóstico e reparo de placa',
                'slug' => 'diagnostico-reparo-placa',
                'price' => 120.00,
                'stock' => 8,
                'image' => 'repair-board.jpg',
                'description' => '<p>Diagnóstico em bancada com inspeção, medição e microparafusos. O valor cobre a análise; o reparo (solda, troca de CI) é orçado à parte.</p><ul><li>Notebook, desktop e placas de expansão</li><li>Laudo em até 3 dias úteis</li></ul>',
            ],
            [
                'category' => 'servicos',
                'name' => 'Montagem de PC / setup gamer',
                'slug' => 'montagem-pc-gamer',
                'price' => 250.00,
                'stock' => 10,
                'image' => 'gpu-rx.jpg',
                'description' => '<p>Montagem completa com teste de POST, BIOS, cabos e stress inicial. Traga as peças ou monte um kit com o nosso estoque.</p><ul><li>Cable management básico incluso</li><li>Instalação de Windows sob consulta</li></ul>',
            ],
            [
                'category' => 'servicos',
                'name' => 'Instalação de processador na placa',
                'slug' => 'instalacao-processador',
                'price' => 89.00,
                'stock' => 20,
                'image' => 'fonte.jpg',
                'description' => '<p>Encaixe do CPU no soquete, pasta térmica e cooler. Serviço fotografado em placa TUF com i9 — o mesmo procedimento da nossa bancada.</p><ul><li>AM4 ou LGA 1151</li><li>Não inclui o cooler, salvo compra na loja</li></ul>',
            ],
            [
                'category' => 'processadores',
                'name' => 'AMD Ryzen 5 3600 — 6 núcleos / AM4',
                'slug' => 'amd-ryzen-5-3600',
                'price' => 649.00,
                'stock' => 9,
                'image' => 'cpu-ryzen.jpg',
                'description' => '<p><strong>AMD Ryzen 5 3600</strong> AM4, 6 núcleos / 12 threads, 3,6 GHz / 4,2 GHz boost, 65 W. Foto ilustrativa da linha Ryzen na bancada Smarty.</p><ul><li>Excelente custo-benefício para 1080p</li><li>Cooler Wraith Stealth (caixa)</li></ul>',
            ],
            [
                'category' => 'processadores',
                'name' => 'Intel Core i7-9700K — 8 núcleos / LGA 1151',
                'slug' => 'intel-core-i7-9700k',
                'price' => 899.00,
                'stock' => 5,
                'image' => 'cpu-intel.jpg',
                'description' => '<p><strong>Intel Core i7-9700K</strong> Coffee Lake Refresh, 8 núcleos / 8 threads, 3,6 GHz. Foto ilustrativa da linha Intel na placa TUF da loja.</p><ul><li>Placas Z390 / Z370</li><li>Bom para jogos e produtividade</li></ul>',
            ],
            [
                'category' => 'placas-de-video',
                'name' => 'NVIDIA GeForce GTX 1080 Ti Founders',
                'slug' => 'nvidia-gtx-1080-ti-founders',
                'price' => 1299.00,
                'stock' => 3,
                'image' => 'gpu-rtx.jpg',
                'description' => '<p>GTX <strong>1080 Ti Founders Edition</strong> usada, testada na bancada. Na foto ao lado da RTX da geração seguinte.</p><ul><li>11 GB GDDR5X</li><li>Revisada, com garantia Smarty de 90 dias</li></ul>',
            ],
            [
                'category' => 'memoria-e-storage',
                'name' => 'Kingston ValueRAM 8 GB DDR4',
                'slug' => 'kingston-valueram-8gb',
                'price' => 159.00,
                'stock' => 18,
                'image' => 'psu-real.jpg',
                'description' => '<p>Módulo único <strong>Kingston 8 GB</strong> para upgrade simples. Foto dos chips Kingston instalados.</p><ul><li>DDR4 desktop</li><li>Instalação na loja sob consulta</li></ul>',
            ],
            [
                'category' => 'memoria-e-storage',
                'name' => 'G.SKILL Trident Z RGB — 32 GB (2x16)',
                'slug' => 'gskill-trident-z-rgb-32gb',
                'price' => 689.00,
                'stock' => 6,
                'image' => 'case.jpg',
                'description' => '<p>Kit <strong>2x16 GB</strong> Trident Z RGB — mesma família da foto da vitrine.</p><ul><li>Perfil alto, conferir folga do cooler</li><li>XMP para placas Z390 / B550</li></ul>',
            ],
            [
                'category' => 'memoria-e-storage',
                'name' => 'HD interno 3,5" — 1 TB',
                'slug' => 'hd-interno-1tb',
                'price' => 289.00,
                'stock' => 15,
                'image' => 'hdd.jpg',
                'description' => '<p>Disco 3,5" <strong>1 TB</strong> SATA para backup e arquivos. Foto ilustrativa do mecanismo (unidade de venda lacrada).</p>',
            ],
            [
                'category' => 'gabinetes-cooling',
                'name' => 'Water Cooler Corsair AIO 360 mm',
                'slug' => 'water-cooler-corsair-360',
                'price' => 749.00,
                'stock' => 4,
                'image' => 'ssd-nvme.jpg',
                'description' => '<p>AIO <strong>Corsair 360 mm</strong> — radiador triplo, mesma linha da build branca da loja.</p><ul><li>Confira folga do gabinete (mín. 360 mm no topo ou frente)</li></ul>',
            ],
            [
                'category' => 'gabinetes-cooling',
                'name' => 'Gabinete compacto vidro — RGB',
                'slug' => 'gabinete-compacto-rgb',
                'price' => 379.00,
                'stock' => 7,
                'image' => 'psu.jpg',
                'description' => '<p>Torre compacta com vidro e fans RGB. Foto da linha gamer da vitrine Smarty.</p>',
            ],
            [
                'category' => 'perifericos',
                'name' => 'Mouse gamer RGB (linha G502)',
                'slug' => 'mouse-gamer-rgb-g502',
                'price' => 249.00,
                'stock' => 11,
                'image' => 'repair-tech.jpg',
                'description' => '<p>Mouse gamer com sensor de alta DPI, fotografado na bancada junto ao teclado mecânico.</p>',
            ],
            [
                'category' => 'servicos',
                'name' => 'Upgrade de memória RAM na loja',
                'slug' => 'upgrade-memoria-ram-loja',
                'price' => 69.00,
                'stock' => 16,
                'image' => 'psu-real.jpg',
                'description' => '<p>Troca ou expansão de módulos DDR na bancada, teste de POST e memtest básico.</p><ul><li>Módulos podem ser os da loja ou os seus</li></ul>',
            ],
            [
                'category' => 'servicos',
                'name' => 'Inspeção de PCB / placa lógica',
                'slug' => 'inspecao-pcb-placa-logica',
                'price' => 149.00,
                'stock' => 8,
                'image' => 'ram-ddr5.jpg',
                'description' => '<p>Inspeção visual e óptica da PCB (trilha, solda, CI). Valor da análise; reparo orçado à parte.</p>',
            ],
            [
                'category' => 'servicos',
                'name' => 'Consultoria de setup gamer',
                'slug' => 'consultoria-setup-gamer',
                'price' => 99.00,
                'stock' => 20,
                'image' => 'gpu-rx.jpg',
                'description' => '<p>Sessão de 40 min para montar lista de peças no orçamento, compatibilidade de fonte, gabinete e GPU.</p>',
            ],
        ];

        foreach ($products as $item) {
            $product = EloquentProductModel::query()->create([
                'category_id' => $categoryIds[$item['category']],
                'name' => $item['name'],
                'slug' => $item['slug'],
                'description' => $item['description'],
                'price' => $item['price'],
                'stock_quantity' => $item['stock'],
                'image_url' => $img($item['image']),
                'is_active' => true,
            ]);

            EloquentStockMovementModel::query()->create([
                'product_id' => $product->id,
                'type' => StockMovementTypeEnum::IN->value,
                'quantity' => $item['stock'],
                'reason' => 'Estoque inicial da bancada',
            ]);
        }

        EloquentKnowledgeModel::query()->insert([
            [
                'title' => 'Quem somos',
                'type' => 'faq',
                'content' => 'A Smarty Hardware vende peças de computador e oferece assistência técnica: limpeza, formatação, diagnóstico de placa e montagem de PC. Atendemos São Paulo, seg–sex 9h–18h e sáb 9h–13h. A assistente virtual se chama Mia.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Processadores em estoque',
                'type' => 'faq',
                'content' => 'Temos AMD Ryzen 7 3700X, Ryzen 5 3600 (AM4), Intel Core i9-9900K e i7-9700K (LGA 1151). O Ryzen casa com B550/X570; o Intel casa com a TUF Z390 da loja.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Placas de vídeo',
                'type' => 'faq',
                'content' => 'Trabalhamos com NVIDIA GeForce RTX Founders Edition e GTX 1080 Ti FE revisada. Recomendamos fonte de no mínimo 650 W 80 Plus Bronze.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Política de troca',
                'type' => 'policy',
                'content' => 'Peças lacradas: 7 dias para arrependimento (CDC). Peças com defeito: 90 dias de garantia Smarty, com laudo da bancada. Serviços de assistência têm 30 dias de garantia sobre o serviço executado.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Frete e serviços',
                'type' => 'policy',
                'content' => 'Frete padrão de peças: R$ 15,00, prazo 3 a 7 dias úteis para capitais. Serviços de bancada não cobram frete — o cliente traz o equipamento ou retira na loja. Montagem de PC pode usar peças da loja ou as do cliente.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Formas de pagamento',
                'type' => 'faq',
                'content' => 'Aceitamos PIX, cartão de crédito, cartão de débito, boleto bancário, dinheiro no balcão e transferência TED/DOC. No site o checkout é sandbox: o pagamento é confirmado automaticamente após escolher a forma. Na loja o admin registra a venda no painel.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Quem é a Mia',
                'type' => 'faq',
                'content' => 'Mia é a assistente virtual da Smarty Hardware. Ela consulta o catálogo, estoque, horários, frete, garantia e formas de pagamento. Para orçamento de solda ou peças fora do site, fale com a bancada no horário comercial.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
