<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

// ── tipos ──────────────────────────────────────────────────────
type Metricas = {
  receita_total: number
  custo_total: number
  lucro_liquido: number
  os_concluidas: number
  ticket_medio: number
  os_abertas: number
  margem: number
}

const props = defineProps<{
  metricas: Metricas
  por_mes: { mes: number; ano: number; receita: number; custo: number }[]
  por_tipo: Record<string, number>
  por_status: Record<string, number>
  ranking_tecnicos: { id: number; name: string; total_os: number; receita_gerada: number }[]
  pecas_mais_usadas: { descricao: string; total_usado: number; custo_total: number }[]
  filters: { data_inicio: string; data_fim: string }
}>()

// ── filtros ─────────────────────────────────────────────────────
const dataInicio = ref(props.filters.data_inicio)
const dataFim    = ref(props.filters.data_fim)

function filtrar() {
  router.get('/financeiro', {
    data_inicio: dataInicio.value,
    data_fim:    dataFim.value,
  }, { preserveScroll: true, replace: true })
}

// ── helpers ──────────────────────────────────────────────────────
const mesesNomes = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']

function brl(valor: number) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor)
}

function iniciais(nome: string) {
  return nome.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
}

const statusLabel: Record<string, string> = {
  recebido:          'Recebido',
  diagnostico:       'Diagnóstico',
  aguardando_peca:   'Ag. Peça',
  em_reparo:         'Em Reparo',
  pronto:            'Pronto',
  entregue:          'Entregue',
}

const tipoLabel: Record<string, string> = {
  reparo:      'Reparo',
  diagnostico: 'Diagnóstico',
  orcamento:   'Orçamento',
}

const statusCores: Record<string, string> = {
  recebido:        '#64748b',
  diagnostico:     '#7C3AED',
  aguardando_peca: '#EAB308',
  em_reparo:       '#3B82F6',
  pronto:          '#22C55E',
  entregue:        '#94A3B8',
}

// ── gráficos ─────────────────────────────────────────────────────
const canvasMes    = ref<HTMLCanvasElement>()
const canvasTipo   = ref<HTMLCanvasElement>()
const canvasStatus = ref<HTMLCanvasElement>()

onMounted(() => {
  // Receita vs Custo por mês
  if (canvasMes.value && props.por_mes.length) {
    new Chart(canvasMes.value, {
      type: 'bar',
      data: {
        labels: props.por_mes.map(m => mesesNomes[m.mes]),
        datasets: [
          {
            label: 'Receita',
            data: props.por_mes.map(m => Number(m.receita)),
            backgroundColor: '#7C3AED',
            borderRadius: 4,
          },
          {
            label: 'Custo',
            data: props.por_mes.map(m => Number(m.custo)),
            backgroundColor: '#334155',
            borderRadius: 4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { color: '#94A3B8', font: { size: 12 } }, grid: { color: 'rgba(255,255,255,0.05)' } },
          y: {
            ticks: {
              color: '#94A3B8',
              font: { size: 11 },
              callback: (v) => 'R$ ' + (Number(v) / 1000).toFixed(0) + 'k',
            },
            grid: { color: 'rgba(255,255,255,0.05)' },
          },
        },
      },
    })
  }

  // OS por tipo
  if (canvasTipo.value && Object.keys(props.por_tipo).length) {
    new Chart(canvasTipo.value, {
      type: 'doughnut',
      data: {
        labels: Object.keys(props.por_tipo).map(k => tipoLabel[k] ?? k),
        datasets: [{
          data: Object.values(props.por_tipo),
          backgroundColor: ['#7C3AED', '#3B82F6', '#22C55E'],
          borderWidth: 0,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: { color: '#94A3B8', font: { size: 12 }, boxWidth: 10, padding: 12 },
          },
        },
      },
    })
  }

  // OS por status
  if (canvasStatus.value && Object.keys(props.por_status).length) {
    const statusKeys = Object.keys(props.por_status)
    new Chart(canvasStatus.value, {
      type: 'bar',
      data: {
        labels: statusKeys.map(k => statusLabel[k] ?? k),
        datasets: [{
          data: Object.values(props.por_status),
          backgroundColor: statusKeys.map(k => statusCores[k] ?? '#64748b'),
          borderRadius: 4,
        }],
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { color: '#94A3B8', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.05)' } },
          y: { ticks: { color: '#94A3B8', font: { size: 11 } }, grid: { display: false } },
        },
      },
    })
  }
})
</script>

<template>
  <AppLayout>
    <div class="p-6 space-y-6">

      <!-- cabeçalho + filtros -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <!-- lado esquerdo -->
        <div>
          <h1 class="text-2xl font-semibold tracking-tight">
            Financeiro
          </h1>
          <p class="text-sm text-muted-foreground mt-1">
            Visão geral de receitas, custos e desempenho financeiro
          </p>
        </div>

        <!-- filtros -->
        <div class="flex flex-wrap items-center gap-2">
          <span class="text-xs text-muted-foreground">De</span>

          <input
            type="date"
            v-model="dataInicio"
            class="h-9 rounded-md border border-border bg-background px-3 text-sm"
          />

          <span class="text-xs text-muted-foreground">Até</span>

          <input
            type="date"
            v-model="dataFim"
            class="h-9 rounded-md border border-border bg-background px-3 text-sm"
          />

          <button
            @click="filtrar"
            class="h-9 rounded-md bg-violet-600 hover:bg-violet-700 px-4 text-sm text-white transition"
          >
            Filtrar
          </button>
        </div>

      </div>

      <!-- métricas -->
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="rounded-lg bg-muted/40 p-4 space-y-1">
          <p class="text-xs text-muted-foreground">Receita total</p>
          <p class="text-xl font-medium text-green-500">{{ brl(metricas.receita_total) }}</p>
        </div>
        <div class="rounded-lg bg-muted/40 p-4 space-y-1">
          <p class="text-xs text-muted-foreground">Custo total</p>
          <p class="text-xl font-medium text-red-500">{{ brl(metricas.custo_total) }}</p>
        </div>
        <div class="rounded-lg bg-muted/40 p-4 space-y-1">
          <p class="text-xs text-muted-foreground">Lucro líquido</p>
          <p class="text-xl font-medium text-violet-400">{{ brl(metricas.lucro_liquido) }}</p>
          <p class="text-xs text-muted-foreground">{{ metricas.margem }}% margem</p>
        </div>
        <div class="rounded-lg bg-muted/40 p-4 space-y-1">
          <p class="text-xs text-muted-foreground">OS concluídas</p>
          <p class="text-xl font-medium">{{ metricas.os_concluidas }}</p>
        </div>
        <div class="rounded-lg bg-muted/40 p-4 space-y-1">
          <p class="text-xs text-muted-foreground">Ticket médio</p>
          <p class="text-xl font-medium">{{ brl(metricas.ticket_medio) }}</p>
        </div>
        <div class="rounded-lg bg-muted/40 p-4 space-y-1">
          <p class="text-xs text-muted-foreground">OS abertas</p>
          <p class="text-xl font-medium">{{ metricas.os_abertas }}</p>
        </div>
      </div>

      <!-- gráficos superiores -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-lg border border-border bg-card p-4">
          <p class="text-sm font-medium mb-4">Receita vs custo — mensal</p>
          <div class="flex gap-4 mb-3 text-xs text-muted-foreground">
            <span class="flex items-center gap-1.5">
              <span class="inline-block w-2.5 h-2.5 rounded-sm bg-violet-600"></span> Receita
            </span>
            <span class="flex items-center gap-1.5">
              <span class="inline-block w-2.5 h-2.5 rounded-sm bg-slate-700"></span> Custo
            </span>
          </div>
          <div class="relative h-52">
            <canvas ref="canvasMes"></canvas>
            <p v-if="!por_mes.length" class="absolute inset-0 flex items-center justify-center text-sm text-muted-foreground">
              Nenhum dado no período
            </p>
          </div>
        </div>

        <div class="rounded-lg border border-border bg-card p-4">
          <p class="text-sm font-medium mb-4">OS por tipo de serviço</p>
          <div class="relative h-52">
            <canvas ref="canvasTipo"></canvas>
            <p v-if="!Object.keys(por_tipo).length" class="absolute inset-0 flex items-center justify-center text-sm text-muted-foreground">
              Nenhum dado no período
            </p>
          </div>
        </div>
      </div>

      <!-- ranking + peças + status -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- ranking técnicos -->
        <div class="rounded-lg border border-border bg-card p-4">
          <p class="text-sm font-medium mb-4">Ranking de técnicos</p>
          <div v-if="ranking_tecnicos.length" class="space-y-1">
            <div
              v-for="(tec, i) in ranking_tecnicos"
              :key="tec.id"
              class="flex items-center gap-3 py-2 border-b border-border last:border-0"
            >
              <span class="text-xs text-muted-foreground w-4">#{{ i + 1 }}</span>
              <div class="w-8 h-8 rounded-full bg-violet-900/40 flex items-center justify-center text-xs font-medium text-violet-400 shrink-0">
                {{ iniciais(tec.name) }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm truncate">{{ tec.name }}</p>
                <p class="text-xs text-muted-foreground">{{ tec.total_os }} OS</p>
              </div>
              <span class="text-sm font-medium whitespace-nowrap">{{ brl(tec.receita_gerada) }}</span>
            </div>
          </div>
          <p v-else class="text-sm text-muted-foreground">Nenhum dado no período</p>
        </div>

        <!-- peças mais usadas -->
        <div class="rounded-lg border border-border bg-card p-4">
          <p class="text-sm font-medium mb-4">Peças mais usadas</p>
          <div v-if="pecas_mais_usadas.length" class="space-y-2">
            <div
              v-for="peca in pecas_mais_usadas"
              :key="peca.descricao"
              class="flex items-center gap-2"
            >
              <span class="text-xs text-muted-foreground truncate w-28 shrink-0" :title="peca.descricao">
                {{ peca.descricao }}
              </span>
              <div class="flex-1 h-1.5 rounded-full bg-muted overflow-hidden">
                <div
                  class="h-full rounded-full bg-violet-500"
                  :style="{ width: (peca.total_usado / pecas_mais_usadas[0].total_usado * 100) + '%' }"
                ></div>
              </div>
              <span class="text-xs text-muted-foreground w-6 text-right">{{ peca.total_usado }}</span>
            </div>
          </div>
          <p v-else class="text-sm text-muted-foreground">Nenhum dado no período</p>
        </div>

        <!-- OS por status -->
        <div class="rounded-lg border border-border bg-card p-4">
          <p class="text-sm font-medium mb-4">OS por status atual</p>
          <div class="relative h-52">
            <canvas ref="canvasStatus"></canvas>
            <p v-if="!Object.keys(por_status).length" class="absolute inset-0 flex items-center justify-center text-sm text-muted-foreground">
              Nenhum dado
            </p>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>