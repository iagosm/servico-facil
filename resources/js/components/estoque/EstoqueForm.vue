<script setup lang="ts">
import { Form } from '@inertiajs/vue3'
import InputError from '@/components/InputError.vue'
import Heading from '@/components/Heading.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'

export type Estoque = {
    id?: number
    tipo: 'insumo' | 'venda'
    condicao: 'novo' | 'conservado' | 'com_defeito' | 'para_pecas'
    nome: string
    descricao: string
    sku: string
    quantidade: string
    quantidade_minima: string
    preco_custo: string
    preco_venda: string
}

type Props = {
    mode: 'create' | 'edit'
    cliente?: Estoque | null
}

const props = defineProps<Props>()
const emit = defineEmits<{ cancel: [] }>()

const actionUrl = props.mode === 'create'
    ? '/estoque'
    : `/estoque/${props.cliente?.id}?_method=PATCH`

const submitLabel = props.mode === 'create' ? 'Salvar' : 'Atualizar'
const title       = props.mode === 'create' ? 'Novo Item' : 'Editar Item'

const condicaoOpcoes = [
    { value: 'novo',        label: 'Novo' },
    { value: 'conservado',  label: 'Conservado' },
    { value: 'com_defeito', label: 'Com defeito' },
    { value: 'para_pecas',  label: 'Para peças' },
]
</script>

<template>
    <div class="space-y-4">
        <Heading :variant="'small'" :title="title" />

        <Form
            :action="actionUrl"
            method="post"
            class="space-y-6"
            @success="emit('cancel')"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-6 md:grid-cols-2">

                <!-- Nome -->
                <div class="grid gap-2">
                    <Label for="nome">Nome</Label>
                    <Input
                        id="nome"
                        name="nome"
                        type="text"
                        required
                        placeholder="Nome do item"
                        :default-value="cliente?.nome ?? ''"
                    />
                    <InputError :message="errors.nome" />
                </div>

                <!-- SKU -->
                <div class="grid gap-2">
                    <Label for="sku">SKU</Label>
                    <Input
                        id="sku"
                        name="sku"
                        type="text"
                        placeholder="SKU"
                        :default-value="cliente?.sku ?? ''"
                    />
                    <InputError :message="errors.sku" />
                </div>

                <!-- Tipo -->
                <div class="grid gap-2">
                    <Label for="tipo">Tipo</Label>
                    <select
                        id="tipo"
                        name="tipo"
                        class="w-full rounded-md border border-input bg-background text-foreground px-3 py-2 text-sm outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]"
                    >
                        <option
                            value="insumo"
                            :selected="(cliente?.tipo ?? 'insumo') === 'insumo'"
                        >
                            Insumo (peça de reparo)
                        </option>
                        <option
                            value="venda"
                            :selected="(cliente?.tipo ?? 'insumo') === 'venda'"
                        >
                            Venda (revenda)
                        </option>
                    </select>
                    <InputError :message="errors.tipo" />
                </div>

                <!-- Condição -->
                <div class="grid gap-2">
                    <Label for="condicao">Condição</Label>
                    <select
                        id="condicao"
                        name="condicao"
                        class="w-full rounded-md border border-input bg-background text-foreground px-3 py-2 text-sm outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]"
                    >
                        <option
                            v-for="op in condicaoOpcoes"
                            :key="op.value"
                            :value="op.value"
                            :selected="(cliente?.condicao ?? 'novo') === op.value"
                        >
                            {{ op.label }}
                        </option>
                    </select>
                    <InputError :message="errors.condicao" />
                </div>

                <!-- Descrição (largura total) -->
                <div class="grid gap-2 md:col-span-2">
                    <Label for="descricao">Descrição</Label>
                    <Input
                        id="descricao"
                        name="descricao"
                        type="text"
                        placeholder="Descrição"
                        :default-value="cliente?.descricao ?? ''"
                    />
                    <InputError :message="errors.descricao" />
                </div>

                <!-- Quantidade -->
                <div class="grid gap-2">
                    <Label for="quantidade">Quantidade</Label>
                    <Input
                        id="quantidade"
                        name="quantidade"
                        type="number"
                        min="0"
                        required
                        placeholder="0"
                        :default-value="cliente?.quantidade ?? ''"
                    />
                    <InputError :message="errors.quantidade" />
                </div>

                <!-- Quantidade Mínima -->
                <div class="grid gap-2">
                    <Label for="quantidade_minima">Quantidade mínima</Label>
                    <Input
                        id="quantidade_minima"
                        name="quantidade_minima"
                        type="number"
                        min="0"
                        required
                        placeholder="0"
                        :default-value="cliente?.quantidade_minima ?? ''"
                    />
                    <InputError :message="errors.quantidade_minima" />
                </div>

                <!-- Preço Custo -->
                <div class="grid gap-2">
                    <Label for="preco_custo">Preço custo (R$)</Label>
                    <Input
                        id="preco_custo"
                        name="preco_custo"
                        type="number"
                        step="0.01"
                        min="0"
                        required
                        placeholder="0,00"
                        :default-value="cliente?.preco_custo ?? ''"
                    />
                    <InputError :message="errors.preco_custo" />
                </div>

                <!-- Preço Venda -->
                <div class="grid gap-2">
                    <Label for="preco_venda">Preço venda (R$)</Label>
                    <Input
                        id="preco_venda"
                        name="preco_venda"
                        type="number"
                        step="0.01"
                        min="0"
                        required
                        placeholder="0,00"
                        :default-value="cliente?.preco_venda ?? ''"
                    />
                    <InputError :message="errors.preco_venda" />
                </div>

            </div>

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="processing">
                    <Spinner v-if="processing" class="mr-2 inline" />
                    {{ submitLabel }}
                </Button>
                <Button
                    type="button"
                    variant="secondary"
                    :disabled="processing"
                    @click="emit('cancel')"
                >
                    Cancelar
                </Button>
            </div>
        </Form>
    </div>
</template>