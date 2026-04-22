<script setup lang="ts">
import { Form, Head, Link, router } from '@inertiajs/vue3'
import { useDebounce } from '@vueuse/core'
import { ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { Button } from '@/components/ui/button'
import GenericTable from '@/components/tables/GenericTable.vue'
import { Spinner } from '@/components/ui/spinner'
import {
    Dialog, DialogTrigger, DialogContent, DialogHeader,
    DialogTitle, DialogDescription, DialogFooter, DialogClose,
} from '@/components/ui/dialog'

type Servico = {
    id: number
    numero: string
    tipo: string
    status: string
    prioridade: string
    data_entrada: string
    data_previsao?: string
    valor_cobrado?: number
    cliente: { id: number; nome: string }
    supervisor?: { id: number; name: string } | null
}

type Props = {
    servicos: { data: Servico[] }
    filters: { search?: string; status?: string; prioridade?: string; perPage?: number }
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Serviços', href: '/servicos' },
]

const columns = [
    { key: 'numero',      header: 'OS' },
    { key: 'cliente',     header: 'Cliente' },
    { key: 'tipo',        header: 'Tipo' },
    { key: 'status',      header: 'Status' },
    { key: 'prioridade',  header: 'Prioridade' },
    { key: 'data_entrada', header: 'Entrada' },
    { key: 'actions',     header: 'Ações' },
]

const loading = ref(false)
router.on('start', () => { loading.value = true })
router.on('finish', () => { loading.value = false })

const perPage    = ref(props.filters.perPage ?? 15)
const search     = ref(props.filters.search ?? '')
const status     = ref(props.filters.status ?? '')
const prioridade = ref(props.filters.prioridade ?? '')
const searchDebounced = useDebounce(search, 500)

function navegar() {
    const params: Record<string, string | number> = { perPage: perPage.value }
    if (searchDebounced.value) params.search = searchDebounced.value
    if (status.value) params.status = status.value
    if (prioridade.value) params.prioridade = prioridade.value
    router.get('/servicos', params, { preserveState: true, replace: true })
}

watch(searchDebounced, navegar)
watch(perPage, navegar)
watch(status, navegar)
watch(prioridade, navegar)

// ─── Config de badges ─────────────────────────────────────────────
// Usamos classes que funcionam em AMBOS os temas via prefixo dark:
const statusConfig: Record<string, { label: string; pill: string; classes: string }> = {
    recebido:        { label: 'Recebido',     pill: 'bg-slate-100 text-slate-600 dark:bg-slate-700/60 dark:text-slate-300',        classes: 'bg-slate-100 text-slate-600 dark:bg-slate-700/60 dark:text-slate-300' },
    diagnostico:     { label: 'Diagnóstico',  pill: 'bg-violet-100 text-violet-700 dark:bg-violet-600/20 dark:text-violet-300',    classes: 'bg-violet-100 text-violet-700 dark:bg-violet-600/20 dark:text-violet-300' },
    aguardando_peca: { label: 'Ag. Peça',     pill: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',   classes: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300' },
    em_reparo:       { label: 'Em Reparo',    pill: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',           classes: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' },
    pronto:          { label: 'Pronto',       pill: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',       classes: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300' },
    entregue:        { label: 'Entregue',     pill: 'bg-slate-100 text-slate-400 dark:bg-slate-800/80 dark:text-slate-500',       classes: 'bg-slate-100 text-slate-400 dark:bg-slate-800/80 dark:text-slate-500' },
}

const prioridadeConfig: Record<string, { label: string; classes: string }> = {
    normal:               { label: 'Normal',        classes: 'bg-slate-100 text-slate-500 dark:bg-slate-700/40 dark:text-slate-400' },
    urgente:              { label: 'Urgente',        classes: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300' },
    aguardando_aprovacao: { label: 'Ag. Aprovação', classes: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300' },
}

const tipoLabel: Record<string, string> = {
    reparo: 'Reparo', diagnostico: 'Diagnóstico', orcamento: 'Orçamento',
}

function formatDate(d?: string) {
    if (!d) return '—'
    return new Date(d).toLocaleDateString('pt-BR')
}
</script>

<template>
    <Head title="Serviços" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4">

            <!-- Cabeçalho -->
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold">Ordens de Serviço</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Gerencie todas as OS da assistência técnica.
                    </p>
                </div>
                <Link href="/servicos/create">
                    <Button size="sm">+ Nova OS</Button>
                </Link>
            </div>

            <!-- Pills de filtro de status -->
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    :class="[
                        'rounded-full px-3 py-1 text-xs font-medium transition-all border',
                        !status
                            ? 'bg-violet-600 text-white border-violet-600'
                            : 'border-border text-muted-foreground hover:bg-accent'
                    ]"
                    @click="status = ''"
                >
                    Todos
                </button>
                <button
                    v-for="(cfg, key) in statusConfig"
                    :key="key"
                    type="button"
                    :class="[
                        'rounded-full px-3 py-1 text-xs font-medium transition-all border',
                        status === key
                            ? cfg.pill + ' border-transparent ring-2 ring-violet-500 ring-offset-1 ring-offset-background'
                            : 'border-border text-muted-foreground hover:bg-accent'
                    ]"
                    @click="status = status === key ? '' : key"
                >
                    {{ cfg.label }}
                </button>
            </div>

            <!-- Tabela -->
            <GenericTable
                :columns="columns"
                :rows="props.servicos.data"
                v-model:per-page="perPage"
                :loading="loading"
                v-model:search="search"
            >
                <template #cell-cliente="{ value }">
                    {{ value?.nome ?? '—' }}
                </template>

                <template #cell-tipo="{ value }">
                    {{ tipoLabel[value] ?? value }}
                </template>

                <template #cell-status="{ value }">
                    <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', statusConfig[value]?.classes ?? 'bg-muted text-muted-foreground']">
                        {{ statusConfig[value]?.label ?? value }}
                    </span>
                </template>

                <template #cell-prioridade="{ value }">
                    <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', prioridadeConfig[value]?.classes ?? 'bg-muted text-muted-foreground']">
                        {{ prioridadeConfig[value]?.label ?? value }}
                    </span>
                </template>

                <template #cell-data_entrada="{ value }">
                    {{ formatDate(value) }}
                </template>

                <template #cell-actions="{ row }">
                    <div class="flex items-center gap-2">
                        <Link :href="`/servicos/${row.id}`">
                            <Button type="button" size="sm" variant="secondary">Ver</Button>
                        </Link>

                        <Dialog>
                            <DialogTrigger as-child>
                                <Button type="button" variant="destructive" size="sm">Excluir</Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader class="space-y-2">
                                    <DialogTitle>Excluir OS {{ row.numero }}?</DialogTitle>
                                    <DialogDescription>Esta ação não pode ser desfeita.</DialogDescription>
                                </DialogHeader>
                                <Form :action="`/servicos/${row.id}?_method=DELETE`" method="post" class="space-y-6" v-slot="{ processing }">
                                    <DialogFooter class="gap-2">
                                        <DialogClose as-child>
                                            <Button type="button" variant="secondary" :disabled="processing">Cancelar</Button>
                                        </DialogClose>
                                        <Button type="submit" variant="destructive" :disabled="processing">
                                            <Spinner v-if="processing" class="mr-2 inline" />
                                            Confirmar
                                        </Button>
                                    </DialogFooter>
                                </Form>
                            </DialogContent>
                        </Dialog>
                    </div>
                </template>
            </GenericTable>
        </div>
    </AppLayout>
</template>