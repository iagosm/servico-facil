<script setup lang="ts">
import { reactive, ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import Heading from '@/components/Heading.vue'
import InputError from '@/components/InputError.vue'
import ClienteQuickCreate, { type ClienteSimples } from '@/components/cliente/ClienteQuickCreate.vue'

export type Tecnico = { id: number; name: string; cargo: string }
export type EstoqueItem = { id: number; nome: string; sku: string; preco_venda: number }

type Equipamento = {
    tipo: string
    marca: string
    modelo: string
    numero_serie: string
    condicao_entrada: string
    problemas: { descricao: string }[]
}

type TecnicoSelecionado = {
    user_id: number | null
    papel: 'executor' | 'supervisor' | 'auxiliar'
}

type ItemClienteLocal = { descricao: string }

type Props = {
    clientes: ClienteSimples[]
    tecnicos: Tecnico[]
    supervisores: { id: number; name: string }[]
    mode?: 'create' | 'edit'
}

const props = defineProps<Props>()
const emit = defineEmits<{ cancel: [] }>()

const clienteId = ref<number | null>(null)
const clientesBuscados = ref<ClienteSimples[]>([...props.clientes])
const buscaCliente = ref('')
const mostrarDropdownCliente = ref(false)

const form = reactive({
    tipo: 'reparo' as 'diagnostico' | 'reparo' | 'orcamento',
    prioridade: 'normal' as 'normal' | 'urgente' | 'aguardando_aprovacao',
    supervisor_id: null as number | null,
    data_previsao: '',
    valor_cobrado: '',
    obs_internas: '',
    obs_cliente: '',
    validade_orcamento: '',
})

const equipamentos = reactive<Equipamento[]>([
    { tipo: '', marca: '', modelo: '', numero_serie: '', condicao_entrada: '', problemas: [{ descricao: '' }] }
])

const itensCliente = reactive<ItemClienteLocal[]>([])
const tecnicos = reactive<TecnicoSelecionado[]>([])
const processing = ref(false)
const errors = reactive<Record<string, string>>({})

const clienteSelecionado = computed(() =>
    clientesBuscados.value.find(c => c.id === clienteId.value)
)

const clientesFiltrados = computed(() => {
    if (!buscaCliente.value) return clientesBuscados.value.slice(0, 8)
    const q = buscaCliente.value.toLowerCase()
    return clientesBuscados.value
        .filter(c => c.nome.toLowerCase().includes(q) || c.telefone?.includes(q))
        .slice(0, 8)
})

function selecionarCliente(c: ClienteSimples) {
    clienteId.value = c.id
    buscaCliente.value = ''
    mostrarDropdownCliente.value = false
}

function onClienteCriado(cliente: ClienteSimples) {
    clientesBuscados.value.unshift(cliente)
    clienteId.value = cliente.id
}

// ✅ CORREÇÃO: setTimeout movido para função — não pode ser chamado inline no template
function fecharDropdownCliente() {
    setTimeout(() => {
        mostrarDropdownCliente.value = false
    }, 200)
}

function addEquipamento() {
    equipamentos.push({ tipo: '', marca: '', modelo: '', numero_serie: '', condicao_entrada: '', problemas: [{ descricao: '' }] })
}
function removeEquipamento(i: number) {
    if (equipamentos.length > 1) equipamentos.splice(i, 1)
}
function addProblema(eqIdx: number) {
    equipamentos[eqIdx].problemas.push({ descricao: '' })
}
function removeProblema(eqIdx: number, pIdx: number) {
    if (equipamentos[eqIdx].problemas.length > 1) equipamentos[eqIdx].problemas.splice(pIdx, 1)
}

function addItem() { itensCliente.push({ descricao: '' }) }
function removeItem(i: number) { itensCliente.splice(i, 1) }
function addTecnico() { tecnicos.push({ user_id: null, papel: 'executor' }) }
function removeTecnico(i: number) { tecnicos.splice(i, 1) }

function validate(): boolean {
    Object.keys(errors).forEach(k => delete errors[k])
    let ok = true
    if (!clienteId.value) { errors.cliente_id = 'Selecione um cliente.'; ok = false }
    equipamentos.forEach((eq, i) => {
        if (!eq.tipo.trim()) { errors[`equip_${i}_tipo`] = 'Tipo do equipamento é obrigatório.'; ok = false }
    })
    return ok
}

function submit() {
    if (!validate()) return
    processing.value = true
    router.post('/servicos', {
        cliente_id: clienteId.value,
        ...form,
        equipamentos: equipamentos.map(eq => ({
            ...eq,
            problemas: eq.problemas.filter(p => p.descricao.trim()),
        })),
        itens_cliente: itensCliente.filter(i => i.descricao.trim()),
        tecnicos: tecnicos.filter(t => t.user_id),
    }, {
        onError: (e) => { Object.assign(errors, e) },
        onFinish: () => { processing.value = false },
    })
}
</script>

<template>
    <div class="space-y-6">
        <Heading variant="small" title="Nova Ordem de Serviço" />

        <form @submit.prevent="submit" class="space-y-6">

            <!-- CLIENTE -->
            <section class="rounded-xl border border-border bg-card p-5 space-y-4">
                <h2 class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400">
                    Cliente
                </h2>

                <div class="flex items-start gap-3">
                    <div class="flex-1 space-y-2">
                        <Label>Cliente <span class="text-destructive">*</span></Label>

                        <div
                            v-if="clienteSelecionado"
                            class="flex items-center gap-2 rounded-lg border border-violet-500/40 bg-violet-50 dark:bg-violet-600/10 px-3 py-2"
                        >
                            <div class="h-8 w-8 flex-shrink-0 rounded-full bg-violet-100 dark:bg-violet-600/20 flex items-center justify-center text-violet-700 dark:text-violet-400 text-xs font-bold">
                                {{ clienteSelecionado.nome.charAt(0).toUpperCase() }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-foreground">{{ clienteSelecionado.nome }}</p>
                                <p class="text-xs text-muted-foreground">{{ clienteSelecionado.telefone }}</p>
                            </div>
                            <button type="button" class="text-xs text-muted-foreground hover:text-destructive transition-colors" @click="clienteId = null">
                                ✕ trocar
                            </button>
                        </div>

                        <div v-else class="relative">
                            <Input
                                v-model="buscaCliente"
                                placeholder="Buscar cliente por nome ou telefone..."
                                @focus="mostrarDropdownCliente = true"
                                @blur="fecharDropdownCliente"
                            />
                            <div
                                v-if="mostrarDropdownCliente && clientesFiltrados.length"
                                class="absolute z-50 mt-1 w-full rounded-lg border border-border bg-popover shadow-lg overflow-hidden"
                            >
                                <button
                                    v-for="c in clientesFiltrados"
                                    :key="c.id"
                                    type="button"
                                    class="flex w-full items-center gap-3 px-3 py-2.5 text-left hover:bg-accent transition-colors"
                                    @mousedown.prevent="selecionarCliente(c)"
                                >
                                    <div class="h-8 w-8 flex-shrink-0 rounded-full bg-violet-100 dark:bg-violet-600/20 flex items-center justify-center text-violet-700 dark:text-violet-400 text-xs font-bold">
                                        {{ c.nome.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-foreground">{{ c.nome }}</p>
                                        <p class="text-xs text-muted-foreground">{{ c.telefone }}</p>
                                    </div>
                                </button>
                            </div>
                            <p v-if="mostrarDropdownCliente && !clientesFiltrados.length" class="mt-1 text-xs text-muted-foreground">
                                Nenhum cliente encontrado.
                            </p>
                        </div>

                        <InputError :message="errors.cliente_id" />
                    </div>

                    <div class="pt-6">
                        <ClienteQuickCreate @criado="onClienteCriado" />
                    </div>
                </div>
            </section>

            <!-- DADOS DA OS -->
            <section class="rounded-xl border border-border bg-card p-5 space-y-4">
                <h2 class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400">
                    Dados da OS
                </h2>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="grid gap-2">
                        <Label>Tipo <span class="text-destructive">*</span></Label>
                        <select v-model="form.tipo" class="w-full rounded-md border border-input bg-background text-foreground px-3 py-2 text-sm outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]">
                            <option value="reparo">Reparo</option>
                            <option value="diagnostico">Diagnóstico</option>
                            <option value="orcamento">Orçamento</option>
                        </select>
                        <InputError :message="errors.tipo" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Prioridade</Label>
                        <select v-model="form.prioridade" class="w-full rounded-md border border-input bg-background text-foreground px-3 py-2 text-sm outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]">
                            <option value="normal">Normal</option>
                            <option value="urgente">Urgente</option>
                            <option value="aguardando_aprovacao">Aguardando aprovação</option>
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <Label>Supervisor</Label>
                        <select v-model="form.supervisor_id" class="w-full rounded-md border border-input bg-background text-foreground px-3 py-2 text-sm outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]">
                            <option :value="null">— nenhum —</option>
                            <option v-for="s in supervisores" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <Label>Data de previsão</Label>
                        <Input type="date" v-model="form.data_previsao" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Valor cobrado (R$)</Label>
                        <Input type="number" step="0.01" min="0" v-model="form.valor_cobrado" placeholder="0,00" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Validade do orçamento</Label>
                        <Input type="date" v-model="form.validade_orcamento" />
                    </div>

                    <div class="grid gap-2 md:col-span-3">
                        <Label>Observações para o cliente</Label>
                        <textarea v-model="form.obs_cliente" rows="2" placeholder="Informações visíveis para o cliente..." class="w-full rounded-md border border-input bg-background text-foreground px-3 py-2 text-sm outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]" />
                    </div>

                    <div class="grid gap-2 md:col-span-3">
                        <Label>Observações internas</Label>
                        <textarea v-model="form.obs_internas" rows="2" placeholder="Anotações internas da equipe..." class="w-full rounded-md border border-input bg-background text-foreground px-3 py-2 text-sm outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]" />
                    </div>
                </div>
            </section>

            <!-- EQUIPAMENTOS -->
            <section class="rounded-xl border border-border bg-card p-5 space-y-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400">
                        Equipamentos
                    </h2>
                    <Button type="button" size="sm" variant="outline" @click="addEquipamento">
                        + Equipamento
                    </Button>
                </div>

                <div
                    v-for="(eq, eqIdx) in equipamentos"
                    :key="eqIdx"
                    class="rounded-lg border border-border bg-muted/30 p-4 space-y-4 relative"
                >
                    <button
                        v-if="equipamentos.length > 1"
                        type="button"
                        class="absolute right-3 top-3 text-muted-foreground hover:text-destructive transition-colors text-sm"
                        @click="removeEquipamento(eqIdx)"
                    >
                        ✕
                    </button>

                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                        Equipamento {{ eqIdx + 1 }}
                    </p>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label>Tipo <span class="text-destructive">*</span></Label>
                            <Input v-model="eq.tipo" placeholder="Ex: Smartphone, Notebook, TV..." />
                            <InputError :message="errors[`equip_${eqIdx}_tipo`]" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Marca</Label>
                            <Input v-model="eq.marca" placeholder="Samsung, Apple, Dell..." />
                        </div>
                        <div class="grid gap-2">
                            <Label>Modelo</Label>
                            <Input v-model="eq.modelo" placeholder="Galaxy S23, MacBook Pro..." />
                        </div>
                        <div class="grid gap-2">
                            <Label>Número de série</Label>
                            <Input v-model="eq.numero_serie" placeholder="S/N..." />
                        </div>
                        <div class="grid gap-2 md:col-span-2">
                            <Label>Condição de entrada</Label>
                            <Input v-model="eq.condicao_entrada" placeholder="Ex: Tela trincada, liga mas sem imagem..." />
                        </div>
                    </div>

                    <!-- Problemas — área destacada com borda dashed -->
                    <div class="rounded-lg border border-dashed border-border bg-background/60 p-3 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                Problemas relatados
                            </span>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-md border border-violet-500/60 bg-violet-50 dark:bg-violet-500/10 px-2.5 py-1 text-xs font-semibold text-violet-700 dark:text-violet-400 hover:bg-violet-100 dark:hover:bg-violet-500/20 transition-colors"
                                @click="addProblema(eqIdx)"
                            >
                                + Problema
                            </button>
                        </div>

                        <div
                            v-for="(prob, pIdx) in eq.problemas"
                            :key="pIdx"
                            class="flex gap-2 items-center"
                        >
                            <span class="w-5 text-right text-xs text-muted-foreground flex-shrink-0">{{ pIdx + 1 }}.</span>
                            <Input v-model="prob.descricao" :placeholder="`Descreva o problema ${pIdx + 1}...`" class="flex-1" />
                            <button
                                v-if="eq.problemas.length > 1"
                                type="button"
                                class="flex-shrink-0 text-muted-foreground hover:text-destructive transition-colors text-sm px-1"
                                @click="removeProblema(eqIdx, pIdx)"
                            >
                                ✕
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ITENS DO CLIENTE -->
            <section class="rounded-xl border border-border bg-card p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400">
                            Itens do cliente
                        </h2>
                        <p class="text-xs text-muted-foreground mt-0.5">Carregador, capa, acessórios entregues junto...</p>
                    </div>
                    <Button type="button" size="sm" variant="outline" @click="addItem">+ Item</Button>
                </div>

                <p v-if="itensCliente.length === 0" class="text-xs text-muted-foreground italic">Nenhum item adicionado.</p>

                <div v-for="(item, i) in itensCliente" :key="i" class="flex gap-2">
                    <Input v-model="item.descricao" :placeholder="`Item ${i + 1}...`" class="flex-1" />
                    <button type="button" class="text-muted-foreground hover:text-destructive transition-colors text-sm px-1" @click="removeItem(i)">✕</button>
                </div>
            </section>

            <!-- TÉCNICOS -->
            <section class="rounded-xl border border-border bg-card p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400">
                        Técnicos
                    </h2>
                    <Button type="button" size="sm" variant="outline" @click="addTecnico">+ Técnico</Button>
                </div>

                <p v-if="tecnicos.length === 0" class="text-xs text-muted-foreground italic">Nenhum técnico vinculado.</p>

                <div v-for="(tec, i) in tecnicos" :key="i" class="flex gap-3 items-center">
                    <select v-model="tec.user_id" class="flex-1 rounded-md border border-input bg-background text-foreground px-3 py-2 text-sm outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]">
                        <option :value="null">— selecionar técnico —</option>
                        <option v-for="t in props.tecnicos" :key="t.id" :value="t.id">{{ t.name }} ({{ t.cargo }})</option>
                    </select>
                    <select v-model="tec.papel" class="w-40 rounded-md border border-input bg-background text-foreground px-3 py-2 text-sm outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]">
                        <option value="executor">Executor</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="auxiliar">Auxiliar</option>
                    </select>
                    <button type="button" class="text-muted-foreground hover:text-destructive transition-colors text-sm px-1" @click="removeTecnico(i)">✕</button>
                </div>
            </section>

            <!-- AÇÕES -->
            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="processing">
                    <Spinner v-if="processing" class="mr-2 inline" />
                    Criar OS
                </Button>
                <Button type="button" variant="secondary" :disabled="processing" @click="emit('cancel')">
                    Cancelar
                </Button>
            </div>
        </form>
    </div>
</template>