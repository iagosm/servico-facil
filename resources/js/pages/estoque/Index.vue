<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3'
import { useDebounce } from '@vueuse/core'
import { ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { Button } from '@/components/ui/button'
import GenericTable from '@/components/tables/GenericTable.vue'
import EstoqueForm, { type Estoque } from '@/components/estoque/EstoqueForm.vue'
import { Spinner } from '@/components/ui/spinner'
import { TriangleAlert } from 'lucide-vue-next'
import {
    Dialog, DialogTrigger, DialogContent, DialogHeader,
    DialogTitle, DialogDescription, DialogFooter, DialogClose,
} from '@/components/ui/dialog'

type Props = {
    estoque: { data: Estoque[] }
    filters: { search?: string; perPage?: number; tipo?: string }
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Estoque', href: '/estoque' }]

const columns = [
    { key: 'nome',              header: 'Nome' },
    { key: 'sku',               header: 'SKU' },
    { key: 'condicao',          header: 'Condição' },
    { key: 'quantidade',        header: 'Qtd.' },
    { key: 'quantidade_minima', header: 'Mínimo' },
    { key: 'preco_custo',       header: 'Custo' },
    { key: 'preco_venda',       header: 'Venda' },
    { key: 'actions',           header: 'Ações' },
]

// ─── Badges de condição ───────────────────────────────────────────
const condicaoConfig: Record<string, { label: string; classes: string }> = {
    novo:        { label: 'Novo',        classes: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300' },
    conservado:  { label: 'Conservado',  classes: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' },
    com_defeito: { label: 'Com defeito', classes: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300' },
    para_pecas:  { label: 'Para peças', classes: 'bg-slate-100 text-slate-600 dark:bg-slate-700/60 dark:text-slate-300' },
}

// ─── Pane e seleção ───────────────────────────────────────────────
const activePane      = ref<'list' | 'create' | 'edit'>('list')
const selectedEstoque = ref<Estoque | null>(null)

const openCreate = () => { selectedEstoque.value = null; activePane.value = 'create' }
const openEdit   = (row: any) => { selectedEstoque.value = row as Estoque; activePane.value = 'edit' }
const backToList = () => { activePane.value = 'list' }

// ─── Aba de tipo (insumo / venda) ─────────────────────────────────
const tipoAtivo = ref<'insumo' | 'venda'>(
    (props.filters.tipo ?? 'insumo') as 'insumo' | 'venda'
)

// ─── Filtros ──────────────────────────────────────────────────────
const loading         = ref(false)
const perPage         = ref(props.filters.perPage ?? 10)
const search          = ref(props.filters.search ?? '')
const searchDebounced = useDebounce(search, 500)

router.on('start',  () => { loading.value = true })
router.on('finish', () => { loading.value = false })

function navegar() {
    const params: Record<string, string | number> = {
        perPage: perPage.value,
        tipo: tipoAtivo.value,
    }
    if (searchDebounced.value) params.search = searchDebounced.value
    router.get('/estoque', params, { preserveState: true, replace: true })
}

function trocarTipo(tipo: 'insumo' | 'venda') {
    tipoAtivo.value = tipo
    activePane.value = 'list'
    navegar()
}

// ─── rowClass — linha vermelha suave quando abaixo do mínimo ──────
function rowClass(row: Record<string, unknown>): string {
    return Number(row.quantidade) <= Number(row.quantidade_minima)
        ? 'bg-red-50/70 dark:bg-red-500/[0.06]'
        : ''
}

watch(searchDebounced, navegar)
watch(perPage, navegar)
</script>

<template>
    <Head title="Estoque" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4">

            <!-- Cabeçalho -->
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold">Estoque</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Cadastre, edite e remova itens do estoque.
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button
                        :variant="activePane === 'list' ? 'default' : 'secondary'"
                        size="sm"
                        @click="backToList"
                    >Lista</Button>
                    <Button
                        :variant="activePane === 'create' ? 'default' : 'secondary'"
                        size="sm"
                        @click="openCreate"
                    >+ Adicionar</Button>
                </div>
            </div>

            <!-- Tab Pane: Insumos / Venda -->
            <div
                v-if="activePane === 'list'"
                class="flex gap-1 rounded-lg border border-border bg-muted/40 p-1 w-fit"
            >
                <button
                    type="button"
                    :class="[
                        'rounded-md px-4 py-1.5 text-sm font-medium transition-all',
                        tipoAtivo === 'insumo'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    ]"
                    @click="trocarTipo('insumo')"
                >
                    Insumos
                    <span class="ml-1.5 text-xs text-muted-foreground">(peças)</span>
                </button>
                <button
                    type="button"
                    :class="[
                        'rounded-md px-4 py-1.5 text-sm font-medium transition-all',
                        tipoAtivo === 'venda'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    ]"
                    @click="trocarTipo('venda')"
                >
                    Venda
                    <span class="ml-1.5 text-xs text-muted-foreground">(revenda)</span>
                </button>
            </div>

            <!-- Conteúdo -->
            <div v-if="activePane === 'list'">
                <GenericTable
                    :columns="columns"
                    :rows="props.estoque.data"
                    :row-class="rowClass"
                    v-model:per-page="perPage"
                    v-model:search="search"
                    :loading="loading"
                    :pagination="estoque"
                >
                    <!-- Condição com badge colorido -->
                    <template #cell-condicao="{ value }">
                        <span
                            :class="[
                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                condicaoConfig[value as string]?.classes ?? 'bg-muted text-muted-foreground'
                            ]"
                        >
                            {{ condicaoConfig[value as string]?.label ?? value }}
                        </span>
                    </template>

                    <template #cell-preco_custo="{ value }">
                        R$ {{ Number(value).toFixed(2) }}
                    </template>

                    <template #cell-preco_venda="{ value }">
                        R$ {{ Number(value).toFixed(2) }}
                    </template>

                    <!-- Quantidade com alerta de mínimo -->
                    <template #cell-quantidade="{ value, row }">
                        <span
                            :class="[
                                'inline-flex items-center gap-1.5 font-medium',
                                Number(value) <= Number(row.quantidade_minima)
                                    ? 'text-red-500 dark:text-red-400'
                                    : '',
                            ]"
                        >
                            {{ value }}
                            <TriangleAlert
                                v-if="Number(value) <= Number(row.quantidade_minima)"
                                class="h-3.5 w-3.5 flex-shrink-0"
                            />
                        </span>
                    </template>

                    <template #cell-actions="{ row }">
                        <div class="flex items-center gap-2">
                            <Button size="sm" @click="openEdit(row)">Editar</Button>

                            <Dialog>
                                <DialogTrigger as-child>
                                    <Button variant="destructive" size="sm">Excluir</Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader class="space-y-2">
                                        <DialogTitle>Excluir "{{ row.nome }}"?</DialogTitle>
                                        <DialogDescription>Esta ação não pode ser desfeita.</DialogDescription>
                                    </DialogHeader>
                                    <Form
                                        :action="`/estoque/${row.id}?_method=DELETE`"
                                        method="post"
                                        @success="backToList"
                                        v-slot="{ processing }"
                                    >
                                        <DialogFooter class="gap-2">
                                            <DialogClose as-child>
                                                <Button variant="secondary" :disabled="processing">Cancelar</Button>
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

            <div v-else-if="activePane === 'create'">
                <EstoqueForm mode="create" :key="'create'" @cancel="backToList" />
            </div>

            <div v-else-if="activePane === 'edit'">
                <EstoqueForm
                    mode="edit"
                    :cliente="selectedEstoque"
                    :key="selectedEstoque?.id"
                    @cancel="backToList"
                />
            </div>
        </div>
    </AppLayout>
</template>