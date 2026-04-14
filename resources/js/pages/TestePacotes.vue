<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { useClipboard, useDark, useToggle, useDebounce } from '@vueuse/core'
import { format, formatDistanceToNow, addDays, subDays } from 'date-fns'
import { ptBR } from 'date-fns/locale'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import { vMaska } from 'maska/vue'
import {
    Chart as ChartJS,
    BarElement,
    LineElement,
    ArcElement,
    CategoryScale,
    LinearScale,
    PointElement,
    Tooltip,
    Legend,
} from 'chart.js'
import { Bar, Line, Doughnut } from 'vue-chartjs'

ChartJS.register(
    BarElement, LineElement, ArcElement,
    CategoryScale, LinearScale, PointElement,
    Tooltip, Legend
)

import type { BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Testes de Pacotes', href: '/teste-pacotes' },
]

// ── VueUse ─────────────────────────────────────────────
const isDark = useDark()
const toggleDark = useToggle(isDark)

const { copy, copied } = useClipboard()
const textToCopy = ref('ServiçoFácil — texto copiado!')

const searchRaw = ref('')
const search = useDebounce(searchRaw, 500)

// ── date-fns ───────────────────────────────────────────
const hoje = new Date()
const dataFormatada        = format(hoje, "dd 'de' MMMM 'de' yyyy", { locale: ptBR })
const dataHora             = format(hoje, "dd/MM/yyyy 'às' HH:mm", { locale: ptBR })
const distancia            = formatDistanceToNow(subDays(hoje, 3), { locale: ptBR, addSuffix: true })
const proximaSemana        = format(addDays(hoje, 7), 'dd/MM/yyyy', { locale: ptBR })

// ── DatePicker ─────────────────────────────────────────
const dataSelecionada      = ref(new Date())
const dataRange            = ref<[Date, Date] | null>(null)

// ── Máscaras ───────────────────────────────────────────
const cpf      = ref('')
const cnpj     = ref('')
const telefone = ref('')
const celular  = ref('')
const cep      = ref('')
const data     = ref('')
const placa    = ref('')
const moeda    = ref('')

// ── Toast manual ───────────────────────────────────────
type ToastTipo = 'sucesso' | 'erro' | 'aviso' | 'info'
const toasts = ref<Array<{ id: number; tipo: ToastTipo; msg: string }>>([])
let toastId = 0

function showToast(tipo: ToastTipo, msg: string) {
    const id = ++toastId
    toasts.value.push({ id, tipo, msg })
    setTimeout(() => {
        toasts.value = toasts.value.filter(t => t.id !== id)
    }, 3500)
}

const toastConfig: Record<ToastTipo, { border: string; bg: string; text: string }> = {
    sucesso: { border: '#166534', bg: '#052e16', text: '#86efac' },
    erro:    { border: '#991b1b', bg: '#450a0a', text: '#fca5a5' },
    aviso:   { border: '#854d0e', bg: '#1c1400', text: '#fcd34d' },
    info:    { border: '#1e40af', bg: '#0c1a3d', text: '#93c5fd' },
}

// ── Chart.js ───────────────────────────────────────────
const barData = {
    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
    datasets: [{
        label: 'Receita (R$)',
        data: [4200, 5800, 3900, 7100, 6500, 8450],
        backgroundColor: '#7C3AED',
        borderRadius: 6,
    }],
}

const lineData = {
    labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
    datasets: [{
        label: 'OS abertas',
        data: [3, 7, 5, 9, 6, 2, 4],
        borderColor: '#22C55E',
        backgroundColor: 'rgba(34,197,94,0.1)',
        tension: 0.4,
        fill: true,
    }],
}

const doughnutData = {
    labels: ['Recebido', 'Em reparo', 'Pronto', 'Entregue'],
    datasets: [{
        data: [12, 8, 5, 22],
        backgroundColor: ['#64748B', '#3B82F6', '#22C55E', '#475569'],
        borderWidth: 0,
    }],
}

const chartOptions = {
    responsive: true,
    plugins: {
        legend: { labels: { color: '#94A3B8' } },
    },
    scales: {
        x: { grid: { color: '#1e293b' }, ticks: { color: '#94A3B8' } },
        y: { grid: { color: '#1e293b' }, ticks: { color: '#94A3B8' } },
    },
}

const doughnutOptions = {
    responsive: true,
    plugins: {
        legend: { position: 'bottom' as const, labels: { color: '#94A3B8' } },
    },
}
</script>

<template>
    <Head title="Testes de Pacotes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-10">

            <div>
                <h1 class="text-2xl font-bold text-white">Testes de Pacotes</h1>
                <p class="text-sm text-muted-foreground mt-1">
                    Página de demonstração — chart.js, vue-the-mask, @vueuse/core, date-fns, vue-datepicker
                </p>
            </div>

            <!-- ── SECTION: Toast ──────────────────────────── -->
            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-white border-b border-border pb-2">
                    Toast Notifications
                </h2>
                <p class="text-sm text-muted-foreground">
                    Implementado manualmente — aparece no canto inferior direito e some após 3.5s.
                </p>
                <div class="flex flex-wrap gap-3">
                    <button
                        class="rounded-lg bg-green-700 px-4 py-2 text-sm text-white hover:bg-green-600 transition-colors"
                        @click="showToast('sucesso', 'Cliente cadastrado com sucesso!')"
                    >
                        Toast Sucesso
                    </button>
                    <button
                        class="rounded-lg bg-red-700 px-4 py-2 text-sm text-white hover:bg-red-600 transition-colors"
                        @click="showToast('erro', 'Erro ao salvar. Verifique os campos.')"
                    >
                        Toast Erro
                    </button>
                    <button
                        class="rounded-lg bg-yellow-700 px-4 py-2 text-sm text-white hover:bg-yellow-600 transition-colors"
                        @click="showToast('aviso', 'Membro desativado. Histórico mantido.')"
                    >
                        Toast Aviso
                    </button>
                    <button
                        class="rounded-lg bg-blue-700 px-4 py-2 text-sm text-white hover:bg-blue-600 transition-colors"
                        @click="showToast('info', 'Estoque atualizado automaticamente.')"
                    >
                        Toast Info
                    </button>
                </div>
            </section>

            <!-- ── SECTION: vue-the-mask ───────────────────── -->
            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-white border-b border-border pb-2">
                    vue-the-mask — Máscaras de input
                </h2>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">

                    <div class="space-y-1">
                        <label class="text-xs text-muted-foreground">CPF</label>
                        <input
                            v-model="cpf"
                            v-maska="'###.###.###-##'"
                            placeholder="000.000.000-00"
                            class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm text-white placeholder-muted-foreground focus:outline-none focus:border-violet-500"
                        />
                        <p class="text-xs text-muted-foreground">Valor: {{ cpf || '—' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs text-muted-foreground">CNPJ</label>
                        <input
                            v-model="cnpj"
                            v-maska="'##.###.###/####-##'"
                            placeholder="00.000.000/0000-00"
                            class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm text-white placeholder-muted-foreground focus:outline-none focus:border-violet-500"
                        />
                        <p class="text-xs text-muted-foreground">Valor: {{ cnpj || '—' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs text-muted-foreground">Telefone fixo</label>
                        <input
                            v-model="telefone"
                            v-maska="'(##) ####-####'"
                            placeholder="(00) 0000-0000"
                            class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm text-white placeholder-muted-foreground focus:outline-none focus:border-violet-500"
                        />
                        <p class="text-xs text-muted-foreground">Valor: {{ telefone || '—' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs text-muted-foreground">Celular</label>
                        <input
                            v-model="celular"
                            v-maska="'(##) #####-####'"
                            placeholder="(00) 00000-0000"
                            class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm text-white placeholder-muted-foreground focus:outline-none focus:border-violet-500"
                        />
                        <p class="text-xs text-muted-foreground">Valor: {{ celular || '—' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs text-muted-foreground">CEP</label>
                        <input
                            v-model="cep"
                            v-maska="'#####-###'"
                            placeholder="00000-000"
                            class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm text-white placeholder-muted-foreground focus:outline-none focus:border-violet-500"
                        />
                        <p class="text-xs text-muted-foreground">Valor: {{ cep || '—' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs text-muted-foreground">Data</label>
                        <input
                            v-model="data"
                            v-maska="'##/##/####'"
                            placeholder="DD/MM/AAAA"
                            class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm text-white placeholder-muted-foreground focus:outline-none focus:border-violet-500"
                        />
                        <p class="text-xs text-muted-foreground">Valor: {{ data || '—' }}</p>
                    </div>

                </div>
            </section>

            <!-- ── SECTION: date-fns ───────────────────────── -->
            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-white border-b border-border pb-2">
                    date-fns — Formatação de datas em português
                </h2>
                <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-4">

                    <div class="rounded-lg border border-border bg-card p-4">
                        <p class="text-xs text-muted-foreground">Data por extenso</p>
                        <p class="mt-1 text-sm text-white font-medium">{{ dataFormatada }}</p>
                    </div>

                    <div class="rounded-lg border border-border bg-card p-4">
                        <p class="text-xs text-muted-foreground">Data e hora</p>
                        <p class="mt-1 text-sm text-white font-medium">{{ dataHora }}</p>
                    </div>

                    <div class="rounded-lg border border-border bg-card p-4">
                        <p class="text-xs text-muted-foreground">Distância relativa</p>
                        <p class="mt-1 text-sm text-white font-medium">{{ distancia }}</p>
                    </div>

                    <div class="rounded-lg border border-border bg-card p-4">
                        <p class="text-xs text-muted-foreground">+7 dias</p>
                        <p class="mt-1 text-sm text-white font-medium">{{ proximaSemana }}</p>
                    </div>

                </div>
            </section>

            <!-- ── SECTION: VueDatePicker ──────────────────── -->
            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-white border-b border-border pb-2">
                    @vuepic/vue-datepicker — Seletor de datas
                </h2>
                <div class="grid gap-6 md:grid-cols-2">

                    <div class="space-y-2">
                        <label class="text-xs text-muted-foreground">Data única</label>
                        <VueDatePicker
                            v-model="dataSelecionada"
                            :locale="'pt-BR'"
                            :format="'dd/MM/yyyy'"
                            dark
                            auto-apply
                        />
                        <p class="text-xs text-muted-foreground">
                            Selecionado:
                            <span class="text-white">
                                {{ dataSelecionada ? format(dataSelecionada, 'dd/MM/yyyy') : '—' }}
                            </span>
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs text-muted-foreground">Intervalo de datas (para filtros)</label>
                        <VueDatePicker
                            v-model="dataRange"
                            :locale="'pt-BR'"
                            :format="'dd/MM/yyyy'"
                            dark
                            range
                            auto-apply
                        />
                        <p class="text-xs text-muted-foreground">
                            De:
                            <span class="text-white">
                                {{ dataRange?.[0] ? format(dataRange[0], 'dd/MM/yyyy') : '—' }}
                            </span>
                            até:
                            <span class="text-white">
                                {{ dataRange?.[1] ? format(dataRange[1], 'dd/MM/yyyy') : '—' }}
                            </span>
                        </p>
                    </div>

                </div>
            </section>

            <!-- ── SECTION: VueUse ────────────────────────── -->
            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-white border-b border-border pb-2">
                    @vueuse/core — Composables úteis
                </h2>
                <div class="grid gap-4 md:grid-cols-3">

                    <!-- useClipboard -->
                    <div class="rounded-lg border border-border bg-card p-4 space-y-3">
                        <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            useClipboard
                        </p>
                        <input
                            v-model="textToCopy"
                            class="w-full rounded border border-border bg-background px-3 py-2 text-sm text-white focus:outline-none focus:border-violet-500"
                        />
                        <button
                            class="w-full rounded-lg px-3 py-2 text-sm transition-colors"
                            :class="copied
                                ? 'bg-green-700 text-white'
                                : 'border border-border text-muted-foreground hover:text-white hover:border-violet-500'"
                            @click="copy(textToCopy)"
                        >
                            {{ copied ? '✓ Copiado!' : 'Copiar texto' }}
                        </button>
                    </div>

                    <!-- useDebounce -->
                    <div class="rounded-lg border border-border bg-card p-4 space-y-3">
                        <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            useDebounce (500ms)
                        </p>
                        <input
                            v-model="searchRaw"
                            placeholder="Digite algo..."
                            class="w-full rounded border border-border bg-background px-3 py-2 text-sm text-white placeholder-muted-foreground focus:outline-none focus:border-violet-500"
                        />
                        <div class="space-y-1">
                            <p class="text-xs text-muted-foreground">
                                Digitando: <span class="text-yellow-400">{{ searchRaw || '—' }}</span>
                            </p>
                            <p class="text-xs text-muted-foreground">
                                Debounced: <span class="text-green-400">{{ search || '—' }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- useDark -->
                    <div class="rounded-lg border border-border bg-card p-4 space-y-3">
                        <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            useDark + useToggle
                        </p>
                        <p class="text-sm text-muted-foreground">
                            Modo atual:
                            <span class="text-white font-medium">
                                {{ isDark ? 'Dark' : 'Light' }}
                            </span>
                        </p>
                        <button
                            class="w-full rounded-lg border border-border px-3 py-2 text-sm text-muted-foreground hover:text-white hover:border-violet-500 transition-colors"
                            @click="toggleDark()"
                        >
                            Alternar tema
                        </button>
                    </div>

                </div>
            </section>

            <!-- ── SECTION: Chart.js ───────────────────────── -->
            <section class="space-y-4">
                <h2 class="text-lg font-semibold text-white border-b border-border pb-2">
                    Chart.js + vue-chartjs — Gráficos
                </h2>
                <div class="grid gap-6 md:grid-cols-3">

                    <div class="md:col-span-2 rounded-lg border border-border bg-card p-4">
                        <p class="text-sm font-medium text-white mb-4">
                            Receita dos últimos 6 meses (Bar)
                        </p>
                        <Bar :data="barData" :options="chartOptions" />
                    </div>

                    <div class="rounded-lg border border-border bg-card p-4">
                        <p class="text-sm font-medium text-white mb-4">
                            OS por status (Doughnut)
                        </p>
                        <Doughnut :data="doughnutData" :options="doughnutOptions" />
                    </div>

                    <div class="md:col-span-3 rounded-lg border border-border bg-card p-4">
                        <p class="text-sm font-medium text-white mb-4">
                            OS abertas por dia (Line)
                        </p>
                        <Line :data="lineData" :options="chartOptions" />
                    </div>

                </div>
            </section>

        </div>
    </AppLayout>

    <!-- ── Toasts ─────────────────────────────────────── -->
    <Teleport to="body">
        <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2">
            <TransitionGroup
                enter-from-class="opacity-0 translate-y-4"
                enter-active-class="transition duration-300 ease-out"
                leave-to-class="opacity-0 translate-x-4"
                leave-active-class="transition duration-200 ease-in"
            >
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="flex items-center gap-3 rounded-lg border px-4 py-3 text-sm shadow-lg min-w-64 max-w-sm"
                    :style="{
                        background: toastConfig[toast.tipo].bg,
                        borderColor: toastConfig[toast.tipo].border,
                        color: toastConfig[toast.tipo].text,
                    }"
                >
                    <!-- ícone sucesso -->
                    <svg v-if="toast.tipo === 'sucesso'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <!-- ícone erro -->
                    <svg v-else-if="toast.tipo === 'erro'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <!-- ícone aviso -->
                    <svg v-else-if="toast.tipo === 'aviso'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <!-- ícone info -->
                    <svg v-else class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>

                    <span class="flex-1">{{ toast.msg }}</span>

                    <button
                        class="opacity-60 hover:opacity-100 transition-opacity"
                        @click="toasts = toasts.filter(t => t.id !== toast.id)"
                    >
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>