import type { Category, Product } from '@/types'

export const demoCategories: Category[] = [
  { id: 1, name: 'Processadores', slug: 'processadores', description: 'AMD Ryzen e Intel Core para AM4 e LGA1151' },
  { id: 2, name: 'Placas de vídeo', slug: 'placas-de-video', description: 'NVIDIA RTX e setups gamer' },
  { id: 3, name: 'Placas-mãe', slug: 'placas-mae', description: 'Placas TUF e plataformas Intel/AMD' },
  { id: 4, name: 'Memória e storage', slug: 'memoria-e-storage', description: 'RAM Kingston, G.SKILL e discos rígidos' },
  { id: 5, name: 'Gabinetes e cooling', slug: 'gabinetes-cooling', description: 'Torres RGB e water cooler' },
  { id: 6, name: 'Periféricos', slug: 'perifericos', description: 'Teclados e mouses' },
  { id: 7, name: 'Serviços', slug: 'servicos', description: 'Bancada de assistência técnica' },
]

const img = (file: string) => `/images/products/${file}`

export const demoProducts: Product[] = [
  {
    id: 1, category_id: 1, category: demoCategories[0],
    name: 'AMD Ryzen 7 3700X — 8 núcleos / AM4', slug: 'amd-ryzen-7-3700x',
    price: 899, stock_quantity: 7, is_active: true, image_url: img('cpu-ryzen.jpg'),
    description: '<p>Processador <strong>AMD Ryzen 7 3700X</strong> em soquete AM4. 8 núcleos / 16 threads, 3,6 GHz base / 4,4 GHz boost.</p>',
  },
  {
    id: 2, category_id: 1, category: demoCategories[0],
    name: 'Intel Core i9-9900K — 8 núcleos / 3.6 GHz', slug: 'intel-core-i9-9900k',
    price: 1099, stock_quantity: 4, is_active: true, image_url: img('cpu-intel.jpg'),
    description: '<p><strong>Intel Core i9-9900K</strong> — 8 núcleos / 16 threads, soquete LGA 1151.</p>',
  },
  {
    id: 3, category_id: 2, category: demoCategories[1],
    name: 'NVIDIA GeForce RTX Founders Edition', slug: 'nvidia-geforce-rtx-founders-edition',
    price: 2499, stock_quantity: 5, is_active: true, image_url: img('gpu-rtx.jpg'),
    description: '<p>Placa <strong>NVIDIA GeForce RTX Founders Edition</strong> com cooler axial duplo. Ray tracing e DLSS.</p>',
  },
  {
    id: 4, category_id: 3, category: demoCategories[2],
    name: 'ASUS TUF Gaming Z390 — LGA 1151', slug: 'asus-tuf-gaming-z390',
    price: 649, stock_quantity: 6, is_active: true, image_url: img('motherboard.jpg'),
    description: '<p>Placa-mãe <strong>ASUS TUF Gaming</strong> (chipset Z390), soquete LGA 1151, slots DDR4 e M.2.</p>',
  },
  {
    id: 5, category_id: 4, category: demoCategories[3],
    name: 'Memória Kingston DDR — módulos instalados', slug: 'memoria-kingston-ddr',
    price: 279, stock_quantity: 14, is_active: true, image_url: img('ram-ddr4.jpg'),
    description: '<p>Kit de memória <strong>Kingston</strong> para desktop e upgrade.</p>',
  },
  {
    id: 6, category_id: 4, category: demoCategories[3],
    name: 'G.SKILL Trident Z RGB — 16 GB', slug: 'gskill-trident-z-rgb-16gb',
    price: 389, stock_quantity: 9, is_active: true, image_url: img('ram-ddr5.jpg'),
    description: '<p>Módulos <strong>G.SKILL Trident Z RGB</strong> — kit 2x8 GB.</p>',
  },
  {
    id: 7, category_id: 4, category: demoCategories[3],
    name: 'HD interno 3,5" — 2 TB', slug: 'hd-interno-2tb',
    price: 419, stock_quantity: 11, is_active: true, image_url: img('hdd.jpg'),
    description: '<p>Disco rígido interno 3,5" <strong>2 TB</strong> SATA, 7200 RPM.</p>',
  },
  {
    id: 8, category_id: 5, category: demoCategories[4],
    name: 'Water Cooler Corsair AIO 240 mm', slug: 'water-cooler-corsair-240',
    price: 549, stock_quantity: 8, is_active: true, image_url: img('cooler.jpg'),
    description: '<p>Water cooler <strong>Corsair</strong> AIO 240 mm com bomba RGB.</p>',
  },
  {
    id: 9, category_id: 5, category: demoCategories[4],
    name: 'Gabinete gamer mid-tower vidro — RGB', slug: 'gabinete-gamer-mid-tower-rgb',
    price: 449, stock_quantity: 6, is_active: true, image_url: img('case.jpg'),
    description: '<p>Gabinete mid-tower com vidro temperado e fans RGB.</p>',
  },
  {
    id: 10, category_id: 6, category: demoCategories[5],
    name: 'Teclado slim wireless alumínio', slug: 'teclado-slim-wireless-aluminio',
    price: 429, stock_quantity: 10, is_active: true, image_url: img('keyboard.jpg'),
    description: '<p>Teclado compacto de alumínio, conexão sem fio.</p>',
  },
  {
    id: 11, category_id: 6, category: demoCategories[5],
    name: 'Teclado mecânico RGB + setup gamer', slug: 'teclado-mecanico-rgb',
    price: 319, stock_quantity: 13, is_active: true, image_url: img('repair-tech.jpg'),
    description: '<p>Teclado mecânico com iluminação RGB e anti-ghosting.</p>',
  },
  {
    id: 12, category_id: 6, category: demoCategories[5],
    name: 'Mouse wireless ergonômico', slug: 'mouse-wireless-ergonomico',
    price: 149, stock_quantity: 22, is_active: true, image_url: img('mouse.jpg'),
    description: '<p>Mouse sem fio com sensor óptico e DPI ajustável.</p>',
  },
  {
    id: 13, category_id: 7, category: demoCategories[6],
    name: 'Limpeza interna + pasta térmica (notebook)', slug: 'limpeza-pasta-termica-notebook',
    price: 179, stock_quantity: 12, is_active: true, image_url: img('repair-clean.jpg'),
    description: '<p>Abertura, limpeza, troca de pasta térmica e teste de temperatura. Prazo: 24–48 h.</p>',
  },
  {
    id: 14, category_id: 7, category: demoCategories[6],
    name: 'Formatação, backup e instalação do sistema', slug: 'formatacao-backup-sistema',
    price: 199, stock_quantity: 15, is_active: true, image_url: img('repair-format.jpg'),
    description: '<p>Backup, formatação, drivers e programas essenciais.</p>',
  },
  {
    id: 15, category_id: 7, category: demoCategories[6],
    name: 'Diagnóstico e reparo de placa', slug: 'diagnostico-reparo-placa',
    price: 120, stock_quantity: 8, is_active: true, image_url: img('repair-board.jpg'),
    description: '<p>Diagnóstico em bancada. O valor cobre a análise; o reparo é orçado à parte.</p>',
  },
  {
    id: 16, category_id: 7, category: demoCategories[6],
    name: 'Montagem de PC / setup gamer', slug: 'montagem-pc-gamer',
    price: 250, stock_quantity: 10, is_active: true, image_url: img('gpu-rx.jpg'),
    description: '<p>Montagem completa com teste de POST, BIOS e stress inicial.</p>',
  },
]
