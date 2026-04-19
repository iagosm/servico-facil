<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Tooltip,
  Legend,
} from 'chart.js'
import { computed } from 'vue'
import { Bar } from 'vue-chartjs'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import type { BreadcrumbItem } from '@/types'

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip, Legend)

// ── Tipos ─────────────────────────────────────────────────────────────────────

type Cards = {
  os_abertas_hoje: number
  os_prontas_retirada: number
  faturamento_mes: number
  lucro_mes: number
  estoque_baixo_count: number
  faturamento_delta: number | null
  lucro_delta: number | null
}
type FaturamentoMes = { mes: string; faturamento: number; lucro: number }
type OsStatus       = { status: string; total: number }
type UltimaOs       = {
  id: number; numero: string; status: string; prioridade: string
  cliente_nome: string; equipamento: string; data_entrada: string; valor: number
}
type AlertaEstoque  = {
  id: number; nome: string; sku: string; quantidade: number; quantidade_minima: number
}
type TopTecnico     = {
  id: number; name: string; cargo: string; os_concluidas: number; faturamento_gerado: number
}

// ── Props ─────────────────────────────────────────────────────────────────────

const props = defineProps<{
  cards: Cards
  faturamentoMeses: FaturamentoMes[]
  osPorStatus: OsStatus[]
  ultimasOs: UltimaOs[]
  alertaEstoque: AlertaEstoque[]
  topTecnicos: TopTecnico[]
}>()

// ── Breadcrumb ────────────────────────────────────────────────────────────────

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: dashboard() }]

// ── Helpers ───────────────────────────────────────────────────────────────────

const formatBRL = (v: number) =>
  v.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })

const statusLabel: Record<string, string> = {
  recebido:        'Recebido',
  diagnostico:     'Diagnóstico',
  aguardando_peca: 'Ag. Peça',
  em_reparo:       'Em Reparo',
  pronto:          'Pronto',
  entregue:        'Entregue',
}

const statusBadge: Record<string, string> = {
  recebido:        'bg-zinc-500/15 text-zinc-500 dark:text-zinc-400',
  diagnostico:     'bg-violet-500/15 text-violet-600 dark:text-violet-400',
  aguardando_peca: 'bg-amber-500/15 text-amber-600 dark:text-amber-400',
  em_reparo:       'bg-sky-500/15 text-sky-600 dark:text-sky-400',
  pronto:          'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400',
  entregue:        'bg-zinc-500/10 text-zinc-400 dark:text-zinc-500',
}

const statusDot: Record<string, string> = {
  recebido:        'bg-zinc-400',
  diagnostico:     'bg-violet-400',
  aguardando_peca: 'bg-amber-400',
  em_reparo:       'bg-sky-400',
  pronto:          'bg-emerald-400',
  entregue:        'bg-zinc-500',
}

const cargoLabel: Record<string, string> = {
  tecnico:    'Técnico',
  supervisor: 'Supervisor',
  atendente:  'Atendente',
  gerente:    'Gerente',
  admin:      'Admin',
}

// ── Estoque — severidade ──────────────────────────────────────────────────────

const estoqueSeverity = (qtd: number, min: number): 'critical' | 'warning' | 'low' => {
  if (qtd === 0)     {
    return 'critical'
  } 
  if (qtd <= min * 0.5)  return 'warning'
  return 'low'
}

const estoquePercent = (qtd: number, min: number) =>
  min === 0 ? 0 : Math.min(Math.round((qtd / min) * 100), 100)

const estoqueSeverityLabel: Record<string, string> = {
  critical: 'Sem estoque',
  warning:  'Crítico',
  low:      'Baixo',
}

// Borda lateral do card
const estoqueBorderColor: Record<string, string> = {
  critical: 'border-l-red-500',
  warning:  'border-l-orange-400',
  low:      'border-l-amber-400',
}

// Badge qtd/min
const estoqueBadge: Record<string, string> = {
  critical: 'bg-red-500/10 text-red-600 dark:text-red-400',
  warning:  'bg-amber-500/15 text-amber-600 dark:text-amber-400',
  low:      'bg-amber-500/10 text-amber-500 dark:text-amber-300',
}

// Barra de progresso
const estoqueBarra: Record<string, string> = {
  critical: 'bg-red-500',
  warning:  'bg-orange-400',
  low:      'bg-amber-400',
}

// Label de severidade
const estoqueLabelColor: Record<string, string> = {
  critical: 'text-red-500 dark:text-red-400',
  warning:  'text-orange-500 dark:text-orange-400',
  low:      'text-amber-500 dark:text-amber-400',
}

// ── OS por Status ─────────────────────────────────────────────────────────────

const totalEmAberto = computed(() =>
  props.osPorStatus
    .filter(i => i.status !== 'entregue')
    .reduce((a, i) => a + i.total, 0)
)

// ── Chart.js — Barras agrupadas ───────────────────────────────────────────────

const chartData = computed(() => ({
  labels: props.faturamentoMeses.map(m => m.mes),
  datasets: [
    {
      label: 'Faturamento',
      data: props.faturamentoMeses.map(m => m.faturamento),
      backgroundColor: 'rgba(167,139,250,0.7)',
      borderRadius: 4,
      borderSkipped: false,
    },
    {
      label: 'Lucro',
      data: props.faturamentoMeses.map(m => m.lucro),
      backgroundColor: 'rgba(16,185,129,0.65)',
      borderRadius: 4,
      borderSkipped: false,
    },
  ],
}))

const chartOptions = computed(() => {
  const isDark = document.documentElement.classList.contains('dark')
  const tickColor = isDark ? '#71717a' : '#9ca3af'
  const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)'

  return {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index' as const, intersect: false },
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: (ctx: any) => ` ${ctx.dataset.label}: ${formatBRL(ctx.parsed.y)}`,
        },
      },
    },
    scales: {
      x: {
        ticks: { color: tickColor, font: { size: 11 } },
        grid: { display: false },
        border: { color: 'transparent' },
      },
      y: {
        ticks: {
          color: tickColor,
          font: { size: 11 },
          callback: (v: any) => 'R$ ' + (v / 1000).toFixed(0) + 'k',
        },
        grid: { color: gridColor },
        border: { color: 'transparent' },
      },
    },
  }
})
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-4 p-4">

      <!-- ── Linha 1: 5 Cards ───────────────────────────────────────────── -->
      <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">

        <!-- OS Abertas Hoje -->
        <div class="rounded-xl border border-border bg-card p-4 flex flex-col gap-1">
          <span class="text-[10px] font-medium uppercase tracking-widest text-muted-foreground">
            OS Abertas Hoje
          </span>
          <span class="text-3xl font-semibold text-foreground">{{ cards.os_abertas_hoje }}</span>
          <span class="text-[11px] text-muted-foreground">recebidas hoje</span>
        </div>

        <!-- Prontas p/ Retirada -->
        <div class="rounded-xl border border-border bg-card p-4 flex flex-col gap-1">
          <span class="text-[10px] font-medium uppercase tracking-widest text-muted-foreground">
            Prontas p/ Retirada
          </span>
          <span class="text-3xl font-semibold text-emerald-500 dark:text-emerald-400">
            {{ cards.os_prontas_retirada }}
          </span>
          <span class="text-[11px] text-muted-foreground">aguardando cliente</span>
        </div>

        <!-- Faturamento do Mês -->
        <div class="rounded-xl border border-border bg-card p-4 flex flex-col gap-1">
          <span class="text-[10px] font-medium uppercase tracking-widest text-muted-foreground">
            Faturamento do Mês
          </span>
          <span class="text-lg font-semibold text-violet-500 dark:text-violet-400 leading-tight mt-0.5">
            {{ formatBRL(cards.faturamento_mes) }}
          </span>
          <span
            v-if="cards.faturamento_delta !== null"
            class="mt-1 inline-flex w-fit items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium"
            :class="cards.faturamento_delta >= 0
              ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
              : 'bg-red-500/10 text-red-500 dark:text-red-400'"
          >
            {{ cards.faturamento_delta >= 0 ? '↑' : '↓' }}
            {{ Math.abs(cards.faturamento_delta) }}% vs mês ant.
          </span>
          <span v-else class="text-[11px] text-muted-foreground">OS concluídas</span>
        </div>

        <!-- Lucro do Mês -->
        <div class="rounded-xl border border-border bg-card p-4 flex flex-col gap-1">
          <span class="text-[10px] font-medium uppercase tracking-widest text-muted-foreground">
            Lucro do Mês
          </span>
          <span class="text-lg font-semibold text-amber-500 dark:text-amber-400 leading-tight mt-0.5">
            {{ formatBRL(cards.lucro_mes) }}
          </span>
          <span
            v-if="cards.lucro_delta !== null"
            class="mt-1 inline-flex w-fit items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium"
            :class="cards.lucro_delta >= 0
              ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
              : 'bg-red-500/10 text-red-500 dark:text-red-400'"
          >
            {{ cards.lucro_delta >= 0 ? '↑' : '↓' }}
            {{ Math.abs(cards.lucro_delta) }}% vs mês ant.
          </span>
          <span v-else class="text-[11px] text-muted-foreground">faturamento − custos</span>
        </div>

        <!-- Estoque Baixo -->
        <div class="rounded-xl border border-border bg-card p-4 flex flex-col gap-1">
          <span class="text-[10px] font-medium uppercase tracking-widest text-muted-foreground">
            Estoque Baixo
          </span>
          <span class="text-3xl font-semibold text-red-500 dark:text-red-400">
            {{ cards.estoque_baixo_count }}
          </span>
          <span class="text-[11px] text-muted-foreground">itens abaixo do mín.</span>
        </div>

      </div>

      <!-- ── Linha 2: Gráfico de Barras ─────────────────────────────────── -->
      <div class="rounded-xl border border-border bg-card p-4">
        <div class="mb-3 flex items-center justify-between">
          <h2 class="text-[10px] font-medium uppercase tracking-widest text-muted-foreground">
            Faturamento & Lucro — Últimos 6 Meses
          </h2>
          <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5 text-[10px] text-muted-foreground">
              <span class="h-2 w-2 rounded-sm bg-violet-400" />
              Faturamento
            </span>
            <span class="flex items-center gap-1.5 text-[10px] text-muted-foreground">
              <span class="h-2 w-2 rounded-sm bg-emerald-400" />
              Lucro
            </span>
          </div>
        </div>
        <div class="h-32">
          <Bar :data="chartData" :options="chartOptions" />
        </div>
      </div>

      <!-- ── Linha 3: OS por Status (1/3) + Últimas OS (2/3) ────────────── -->
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        <!-- OS por Status -->
        <div class="rounded-xl border border-border bg-card p-4 flex flex-col">
          <h2 class="mb-3 text-[10px] font-medium uppercase tracking-widest text-muted-foreground">
            OS por Status
          </h2>

          <div class="flex flex-col">
            <div
              v-for="item in osPorStatus"
              :key="item.status"
              class="flex items-center justify-between border-b border-border py-2 last:border-0"
            >
              <div class="flex items-center gap-2">
                <span class="h-1.5 w-1.5 rounded-full shrink-0" :class="statusDot[item.status]" />
                <span class="text-xs text-foreground">{{ statusLabel[item.status] }}</span>
              </div>
              <span
                class="text-[10px] font-semibold rounded-full px-2 py-0.5"
                :class="statusBadge[item.status]"
              >
                {{ item.total }}
              </span>
            </div>
          </div>

          <div class="mt-auto pt-3 border-t border-border flex justify-between items-center">
            <span class="text-[11px] text-muted-foreground">Total em aberto</span>
            <span class="text-sm font-semibold text-foreground">{{ totalEmAberto }}</span>
          </div>
        </div>

        <!-- Últimas OS -->
        <div class="lg:col-span-2 rounded-xl border border-border bg-card p-4">
          <h2 class="mb-3 text-[10px] font-medium uppercase tracking-widest text-muted-foreground">
            Últimas OS
          </h2>

          <div class="overflow-x-auto">
            <table class="w-full text-xs">
              <thead>
                <tr class="border-b border-border">
                  <th class="pb-2 pr-3 text-left font-medium text-muted-foreground">Número</th>
                  <th class="pb-2 pr-3 text-left font-medium text-muted-foreground">Cliente</th>
                  <th class="pb-2 pr-3 text-left font-medium text-muted-foreground hidden md:table-cell">Equipamento</th>
                  <th class="pb-2 pr-3 text-left font-medium text-muted-foreground">Status</th>
                  <th class="pb-2 text-right font-medium text-muted-foreground">Valor</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="os in ultimasOs"
                  :key="os.id"
                  class="border-b border-border last:border-0 hover:bg-muted/40 transition-colors"
                >
                  <td class="py-2 pr-3 font-mono text-violet-500 dark:text-violet-400 font-medium">
                    {{ os.numero }}
                  </td>
                  <td class="py-2 pr-3 text-foreground max-w-[90px] truncate">
                    {{ os.cliente_nome }}
                  </td>
                  <td class="py-2 pr-3 text-muted-foreground max-w-[90px] truncate hidden md:table-cell">
                    {{ os.equipamento }}
                  </td>
                  <td class="py-2 pr-3">
                    <span
                      class="rounded-full px-2 py-0.5 font-medium"
                      :class="statusBadge[os.status]"
                    >
                      {{ statusLabel[os.status] }}
                    </span>
                  </td>
                  <td class="py-2 text-right font-medium text-foreground tabular-nums">
                    {{ os.valor > 0 ? formatBRL(os.valor) : '—' }}
                  </td>
                </tr>

                <tr v-if="ultimasOs.length === 0">
                  <td colspan="5" class="py-8 text-center text-muted-foreground">
                    Nenhuma OS encontrada.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!-- ── Linha 4: Estoque (2/3) + Top Técnicos (1/3) ───────────────── -->
      <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        <!-- Estoque Baixo — ocupa 2 colunas -->
        <div class="lg:col-span-2 rounded-xl border border-border bg-card p-4">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-[10px] font-medium uppercase tracking-widest text-muted-foreground">
              Estoque Baixo
            </h2>
            <span class="text-[10px] font-semibold rounded-full px-2.5 py-0.5 bg-amber-500/15 text-amber-600 dark:text-amber-400">
              {{ alertaEstoque.length }} {{ alertaEstoque.length === 1 ? 'item' : 'itens' }}
            </span>
          </div>

          <p v-if="alertaEstoque.length === 0" class="text-xs text-muted-foreground">
            Nenhum item abaixo do mínimo ✓
          </p>

          <!-- Grid de cards individuais por item -->
          <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
            <div
              v-for="item in alertaEstoque"
              :key="item.id"
              class="rounded-lg border border-border border-l-2 bg-background p-3 flex flex-col gap-2.5"
              :class="estoqueBorderColor[estoqueSeverity(item.quantidade, item.quantidade_minima)]"
            >
              <!-- Topo: nome + badge qtd -->
              <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                  <p class="text-xs font-medium text-foreground leading-snug truncate">{{ item.nome }}</p>
                  <p class="text-[10px] text-muted-foreground font-mono mt-0.5">{{ item.sku }}</p>
                </div>
                <span
                  class="shrink-0 text-[10px] font-semibold rounded-full px-2 py-0.5 tabular-nums"
                  :class="estoqueBadge[estoqueSeverity(item.quantidade, item.quantidade_minima)]"
                >
                  {{ item.quantidade }}/{{ item.quantidade_minima }}
                </span>
              </div>

              <!-- Barra + meta -->
              <div class="flex flex-col gap-1.5">
                <div class="h-1.5 w-full rounded-full bg-muted overflow-hidden">
                  <div
                    class="h-full rounded-full transition-all duration-300"
                    :class="estoqueBarra[estoqueSeverity(item.quantidade, item.quantidade_minima)]"
                    :style="{ width: estoquePercent(item.quantidade, item.quantidade_minima) + '%' }"
                  />
                </div>
                <div class="flex items-center justify-between">
                  <span
                    class="text-[10px] font-medium"
                    :class="estoqueLabelColor[estoqueSeverity(item.quantidade, item.quantidade_minima)]"
                  >
                    {{ estoqueSeverityLabel[estoqueSeverity(item.quantidade, item.quantidade_minima)] }}
                  </span>
                  <span class="text-[10px] text-muted-foreground tabular-nums">
                    {{ estoquePercent(item.quantidade, item.quantidade_minima) }}%
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Técnicos — ocupa 1 coluna -->
        <div class="rounded-xl border border-border bg-card p-4">
          <h2 class="mb-3 text-[10px] font-medium uppercase tracking-widest text-muted-foreground">
            Top Técnicos do Mês
          </h2>

          <p v-if="topTecnicos.length === 0" class="text-xs text-muted-foreground">
            Nenhuma OS concluída este mês ainda.
          </p>

          <div class="flex flex-col">
            <div
              v-for="(tec, index) in topTecnicos"
              :key="tec.id"
              class="flex items-center gap-3 border-b border-border py-2 last:border-0"
            >
              <!-- Medalha -->
              <span
                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-bold"
                :class="{
                  'bg-amber-400/20 text-amber-500 dark:text-amber-400':    index === 0,
                  'bg-zinc-400/20 text-zinc-500 dark:text-zinc-300':        index === 1,
                  'bg-orange-600/20 text-orange-600 dark:text-orange-400':  index === 2,
                  'bg-muted text-muted-foreground':                          index > 2,
                }"
              >
                {{ index + 1 }}
              </span>

              <!-- Nome + cargo -->
              <div class="flex flex-col flex-1 min-w-0">
                <span class="text-xs text-foreground truncate">{{ tec.name }}</span>
                <span class="text-[10px] text-muted-foreground">{{ cargoLabel[tec.cargo] ?? tec.cargo }}</span>
              </div>

              <!-- Métricas -->
              <div class="flex flex-col items-end shrink-0">
                <span class="text-xs font-semibold text-violet-500 dark:text-violet-400">
                  {{ tec.os_concluidas }} OS
                </span>
                <span class="text-[10px] text-muted-foreground">{{ formatBRL(tec.faturamento_gerado) }}</span>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </AppLayout>
</template>