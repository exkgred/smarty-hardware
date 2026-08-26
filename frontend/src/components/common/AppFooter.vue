<template>
  <footer class="mt-16 border-t border-slate-800 bg-ink text-slate-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
      <div>
        <p class="text-white font-bold tracking-wide">SMARTY HARDWARE</p>
        <p class="mt-3 text-sm leading-relaxed text-slate-400">Peças de computador, upgrades e bancada de assistência técnica em São Paulo. Montamos, diagnosticamos e reparamos.</p>
      </div>
      <div>
        <p class="text-sm font-semibold text-white">Loja</p>
        <ul class="mt-3 space-y-2 text-sm">
          <li><RouterLink to="/catalog" class="hover:text-brand">Catálogo de peças</RouterLink></li>
          <li><RouterLink to="/catalog?category=servicos" class="hover:text-brand">Serviços de assistência</RouterLink></li>
          <li><RouterLink to="/cart" class="hover:text-brand">Carrinho</RouterLink></li>
        </ul>
      </div>
      <div>
        <p class="text-sm font-semibold text-white">Assistência</p>
        <ul class="mt-3 space-y-2 text-sm text-slate-400">
          <li>Limpeza e pasta térmica</li>
          <li>Formatação com backup</li>
          <li>Diagnóstico de placa</li>
          <li>Montagem de PC gamer</li>
        </ul>
      </div>
      <div>
        <p class="text-sm font-semibold text-white">Atendimento</p>
        <p class="mt-3 text-sm text-slate-400">Seg–Sex 9h–18h · Sáb 9h–13h</p>
        <p class="mt-1 text-sm text-slate-400">contato@smartyhardware.test</p>
        <p class="mt-1 text-sm text-slate-400">Frete padrão R$ 15 · serviços sem frete</p>
      </div>
    </div>
    <div class="border-t border-slate-800 py-4 text-center text-xs text-slate-500">
      © 2026 Smarty Hardware. Peças com garantia de 90 dias. Serviços com 30 dias de garantia.
    </div>
  </footer>
</template>
<script setup lang="ts"></script>
