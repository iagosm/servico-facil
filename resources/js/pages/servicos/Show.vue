<script setup lang="ts">
import { Head, Link, Form } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import {
    Dialog, DialogTrigger, DialogContent, DialogHeader,
    DialogTitle, DialogDescription, DialogFooter, DialogClose,
} from '@/components/ui/dialog'

type Problema = { id: number; descricao: string; laudo_tecnico?: string; resolvido: boolean }
type Peca = { id: number; descricao: string; quantidade: number; preco_custo: number; preco_cobrado: number; estoque?: { nome: string } | null }
type Equipamento = { id: number; tipo: string; marca?: string; modelo?: string; numero_serie?: string; condicao_entrada?: string; problemas: Problema[]; pecas: Peca[] }
type StatusEntry = { id: number; status_anterior?: string; status_novo: string; observacao?: string; created_at: string; user: { id: number; name: string } }

type Servico = {
    id: number; numero: string; tipo: string; status: string; prioridade: string
    obs_internas?: string; obs_cliente?: string; valor_cobrado?: number; custo_total?: number
    data_entrada: string; data_previsao?: string; data_conclusao?: string; data_entrega?: string
    cliente: { id: number; nome: string; telefone: string }
    supervisor?: { id: number; name: string } | null
    equipamentos: Equipamento[]
    itensCliente: { id: number; descricao: string }[]
    users: { id: number; name: string; pivot: { papel: string } }[]
    statusTimeline: StatusEntry[]
}

type Props = {
    servico: Servico
    tecnicos: { id: number; name: string; cargo: string }[]
    estoque: { id: number; nome: string; sku: string; preco_venda: number }[]
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Serviços', href: '/servicos' },
    { title: props.servico.numero, href: `/servicos/${props.servico.id}` },
]

// ─── Config de badges — compatível white/dark ──────────────────────
const statusConfig: Record<string, { label: string; badge: string; dot: string; ring: string }> = {
    recebido:        { label: 'Recebido',     badge: 'bg-slate-100 text-slate-600 dark:bg-slate-700/60 dark:text-slate-300',      dot: 'bg-slate-400',    ring: 'ring-slate-400' },
    diagnostico:     { label: 'Diagnóstico',  badge: 'bg-violet-100 text-violet-700 dark:bg-violet-600/20 dark:text-violet-300',  dot: 'bg-violet-500',   ring: 'ring-violet-500' },
    aguardando_peca: { label: 'Ag. Peça',     badge: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300', dot: 'bg-yellow-400',   ring: 'ring-yellow-400' },
    em_reparo:       { label: 'Em Reparo',    badge: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',         dot: 'bg-blue-500',     ring: 'ring-blue-500' },
    pronto:          { label: 'Pronto',       badge: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',     dot: 'bg-green-500',    ring: 'ring-green-500' },
    entregue:        { label: 'Entregue',     badge: 'bg-slate-100 text-slate-400 dark:bg-slate-800/80 dark:text-slate-500',     dot: 'bg-slate-500',    ring: 'ring-slate-500' },
}

const prioridadeConfig: Record<string, { label: string; classes: string }> = {
    normal:               { label: 'Normal',        classes: 'bg-slate-100 text-slate-500 dark:bg-slate-700/40 dark:text-slate-400' },
    urgente:              { label: 'Urgente',        classes: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300' },
    aguardando_aprovacao: { label: 'Ag. Aprovação', classes: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300' },
}

const tipoLabel: Record<string, string> = {
    reparo: 'Reparo', diagnostico: 'Diagnóstico', orcamento: 'Orçamento',
}

const fluxoStatus = ['recebido', 'diagnostico', 'aguardando_peca', 'em_reparo', 'pronto', 'entregue']

function formatDate(d?: string) {
    if (!d) return '—'
    return new Date(d).toLocaleDateString('pt-BR')
}
function formatDateTime(d?: string) {
    if (!d) return '—'
    return new Date(d).toLocaleString('pt-BR')
}
function formatMoney(v?: number) {
    if (v === null || v === undefined) return '—'
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}

const novoStatus = ref(props.servico.status)
const obsStatus  = ref('')
const dialogStatusOpen = ref(false)

const statusAtualIdx = fluxoStatus.indexOf(props.servico.status)
</script>

<template>
    <Head :title="`OS ${servico.numero}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-6 max-w-5xl">

            <!-- Cabeçalho -->
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-bold tracking-tight">{{ servico.numero }}</h1>
                        <span :class="['inline-flex items-center rounded-full px-3 py-1 text-xs font-medium', statusConfig[servico.status]?.badge]">
                            {{ statusConfig[servico.status]?.label ?? servico.status }}
                        </span>
                        <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', prioridadeConfig[servico.prioridade]?.classes]">
                            {{ prioridadeConfig[servico.prioridade]?.label }}
                        </span>
                    </div>
                    <p class="text-sm text-muted-foreground">
                        {{ tipoLabel[servico.tipo] }} · Entrada {{ formatDate(servico.data_entrada) }}
                    </p>
                </div>

                <div class="flex gap-2">
                    <Dialog v-model:open="dialogStatusOpen">
                        <DialogTrigger as-child>
                            <Button size="sm" variant="outline">Alterar status</Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-md">
                            <DialogHeader>
                                <DialogTitle>Alterar status da OS</DialogTitle>
                                <DialogDescription>Selecione o novo status e adicione uma observação opcional.</DialogDescription>
                            </DialogHeader>

                            <Form
                                :action="`/servicos/${servico.id}/status?_method=PATCH`"
                                method="post"
                                class="space-y-4 py-2"
                                @success="dialogStatusOpen = false"
                                v-slot="{ processing }"
                            >
                                <div class="space-y-2">
                                    <Label>Novo status</Label>
                                    <div class="flex flex-wrap gap-2">
                                        <label
                                            v-for="s in fluxoStatus"
                                            :key="s"
                                            :class="[
                                                'flex cursor-pointer items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium transition-all border',
                                                novoStatus === s
                                                    ? statusConfig[s]?.badge + ' border-transparent ring-2 ring-offset-1 ring-offset-background ' + statusConfig[s]?.ring
                                                    : 'border-border text-muted-foreground hover:bg-accent'
                                            ]"
                                        >
                                            <input type="radio" name="status" :value="s" v-model="novoStatus" class="sr-only" />
                                            {{ statusConfig[s]?.label }}
                                        </label>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label>Observação</Label>
                                    <textarea
                                        name="observacao"
                                        rows="2"
                                        v-model="obsStatus"
                                        placeholder="Opcional..."
                                        class="w-full rounded-md border border-input bg-background text-foreground px-3 py-2 text-sm outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]"
                                    />
                                </div>

                                <input type="hidden" name="status" :value="novoStatus" />

                                <DialogFooter class="gap-2">
                                    <DialogClose as-child>
                                        <Button type="button" variant="secondary" :disabled="processing">Cancelar</Button>
                                    </DialogClose>
                                    <Button type="submit" :disabled="processing">
                                        <Spinner v-if="processing" class="mr-2 inline" />
                                        Salvar
                                    </Button>
                                </DialogFooter>
                            </Form>
                        </DialogContent>
                    </Dialog>

                    <Link :href="`/servicos/${servico.id}/edit`">
                        <Button size="sm" variant="secondary">Editar</Button>
                    </Link>
                </div>
            </div>

            <!-- Barra de progresso do fluxo -->
            <div class="rounded-xl border border-border bg-card p-4">
                <div class="flex items-center">
                    <template v-for="(s, i) in fluxoStatus" :key="s">
                        <div class="flex flex-col items-center flex-1 min-w-0">
                            <div :class="[
                                'h-3 w-3 rounded-full ring-2 ring-offset-2 ring-offset-background transition-all',
                                statusAtualIdx >= i
                                    ? (statusConfig[s]?.dot ?? 'bg-violet-500') + ' ' + (statusConfig[s]?.ring ?? 'ring-violet-500')
                                    : 'bg-muted-foreground/30 ring-border'
                            ]" />
                            <p class="mt-2 text-center text-[10px] text-muted-foreground leading-tight px-1 truncate w-full">
                                {{ statusConfig[s]?.label }}
                            </p>
                        </div>
                        <div
                            v-if="i < fluxoStatus.length - 1"
                            :class="[
                                'h-0.5 flex-1 -mt-4 transition-all',
                                statusAtualIdx > i ? 'bg-violet-500' : 'bg-border'
                            ]"
                        />
                    </template>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">

                <!-- Coluna principal -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Info geral -->
                    <div class="rounded-xl border border-border bg-card p-5">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 mb-4">
                            Informações
                        </h2>
                        <dl class="grid gap-3 sm:grid-cols-2 text-sm">
                            <div>
                                <dt class="text-muted-foreground">Cliente</dt>
                                <dd class="font-medium">
                                    <Link :href="`/clientes/${servico.cliente.id}`" class="hover:text-violet-600 dark:hover:text-violet-400 transition-colors">
                                        {{ servico.cliente.nome }}
                                    </Link>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground">Telefone</dt>
                                <dd class="font-medium">{{ servico.cliente.telefone }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground">Supervisor</dt>
                                <dd class="font-medium">{{ servico.supervisor?.name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground">Previsão</dt>
                                <dd class="font-medium">{{ formatDate(servico.data_previsao) }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground">Conclusão</dt>
                                <dd class="font-medium">{{ formatDate(servico.data_conclusao) }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground">Entrega</dt>
                                <dd class="font-medium">{{ formatDate(servico.data_entrega) }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground">Valor cobrado</dt>
                                <dd class="font-medium">{{ formatMoney(servico.valor_cobrado) }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground">Custo total</dt>
                                <dd class="font-medium">{{ formatMoney(servico.custo_total) }}</dd>
                            </div>
                        </dl>

                        <template v-if="servico.obs_cliente">
                            <hr class="my-4 border-border" />
                            <p class="text-xs text-muted-foreground uppercase tracking-wider mb-1">Obs. cliente</p>
                            <p class="text-sm">{{ servico.obs_cliente }}</p>
                        </template>

                        <template v-if="servico.obs_internas">
                            <hr class="my-4 border-border" />
                            <p class="text-xs text-muted-foreground uppercase tracking-wider mb-1">Obs. internas</p>
                            <p class="text-sm">{{ servico.obs_internas }}</p>
                        </template>
                    </div>

                    <!-- Equipamentos -->
                    <div
                        v-for="eq in servico.equipamentos"
                        :key="eq.id"
                        class="rounded-xl border border-border bg-card p-5 space-y-4"
                    >
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-violet-100 dark:bg-violet-600/20 flex items-center justify-center text-violet-700 dark:text-violet-400 text-sm font-bold flex-shrink-0">
                                {{ eq.tipo.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="font-semibold">{{ eq.tipo }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ [eq.marca, eq.modelo].filter(Boolean).join(' · ') || 'Sem detalhes adicionais' }}
                                    <span v-if="eq.numero_serie"> · S/N: {{ eq.numero_serie }}</span>
                                </p>
                            </div>
                        </div>

                        <p v-if="eq.condicao_entrada" class="text-sm">
                            <span class="text-muted-foreground">Condição: </span>{{ eq.condicao_entrada }}
                        </p>

                        <!-- Problemas -->
                        <div v-if="eq.problemas.length" class="space-y-2">
                            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Problemas</p>
                            <ul class="space-y-1.5">
                                <li v-for="prob in eq.problemas" :key="prob.id" class="flex items-start gap-2 text-sm">
                                    <span :class="['mt-1.5 h-2 w-2 flex-shrink-0 rounded-full', prob.resolvido ? 'bg-green-500' : 'bg-yellow-400']" />
                                    <div>
                                        <p :class="prob.resolvido ? 'line-through text-muted-foreground' : ''">
                                            {{ prob.descricao }}
                                        </p>
                                        <p v-if="prob.laudo_tecnico" class="text-xs text-muted-foreground mt-0.5">
                                            Laudo: {{ prob.laudo_tecnico }}
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Peças -->
                        <div v-if="eq.pecas.length" class="space-y-2">
                            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Peças utilizadas</p>
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs text-muted-foreground border-b border-border">
                                        <th class="pb-1 font-medium">Descrição</th>
                                        <th class="pb-1 font-medium">Qtd</th>
                                        <th class="pb-1 font-medium">Custo</th>
                                        <th class="pb-1 font-medium">Cobrado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    <tr v-for="peca in eq.pecas" :key="peca.id">
                                        <td class="py-1.5">
                                            {{ peca.descricao }}
                                            <span v-if="peca.estoque" class="text-muted-foreground text-xs"> (estoque)</span>
                                        </td>
                                        <td class="py-1.5">{{ peca.quantidade }}</td>
                                        <td class="py-1.5">{{ formatMoney(peca.preco_custo) }}</td>
                                        <td class="py-1.5">{{ formatMoney(peca.preco_cobrado) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Itens do cliente -->
                    <div v-if="servico.itensCliente?.length" class="rounded-xl border border-border bg-card p-5">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 mb-3">
                            Itens do cliente
                        </h2>
                        <ul class="space-y-1.5">
                            <li v-for="item in servico.itensCliente" :key="item.id" class="flex items-center gap-2 text-sm">
                                <span class="h-1.5 w-1.5 rounded-full bg-muted-foreground flex-shrink-0" />
                                {{ item.descricao }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Coluna lateral -->
                <div class="space-y-6">

                    <!-- Equipe -->
                    <div class="rounded-xl border border-border bg-card p-5">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 mb-3">
                            Equipe
                        </h2>
                        <div v-if="servico.users?.length" class="space-y-2">
                            <div v-for="user in servico.users" :key="user.id" class="flex items-center gap-2">
                                <div class="h-7 w-7 rounded-full bg-muted flex items-center justify-center text-xs font-bold">
                                    {{ user.name?.charAt(0)?.toUpperCase() ?? '?' }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ user.name ?? '—' }}</p>
                                    <p class="text-xs text-muted-foreground capitalize">{{ user.pivot?.papel ?? '—' }}</p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-xs text-muted-foreground italic">Nenhum técnico vinculado.</p>
                    </div>

                    <!-- Timeline -->
                    <div class="rounded-xl border border-border bg-card p-5">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 mb-4">
                            Histórico
                        </h2>
                        <div v-if="servico.statusTimeline?.length" class="relative space-y-4 before:absolute before:left-3 before:top-1 before:h-[calc(100%-8px)] before:w-px before:bg-border">
                            <div
                                v-for="entry in [...(servico.statusTimeline ?? [])].reverse()"
                                :key="entry.id"
                                class="relative pl-8"
                            >
                                <div class="absolute left-0 top-0.5 h-6 w-6 rounded-full border border-border bg-card flex items-center justify-center">
                                    <div :class="['h-2 w-2 rounded-full', statusConfig[entry.status_novo]?.dot ?? 'bg-muted-foreground']" />
                                </div>
                                <p class="text-xs font-medium">
                                    {{ statusConfig[entry.status_novo]?.label ?? entry.status_novo }}
                                </p>
                                <p class="text-[11px] text-muted-foreground">
                                    {{ entry.user?.name ?? '—' }} · {{ formatDateTime(entry.created_at) }}
                                </p>
                                <p v-if="entry.observacao" class="text-[11px] text-muted-foreground mt-0.5 italic">
                                    "{{ entry.observacao }}"
                                </p>
                            </div>
                        </div>
                        <p v-else class="text-xs text-muted-foreground italic">Nenhuma movimentação registrada.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>