<script setup lang="ts">
import { Form } from '@inertiajs/vue3'
import InputError from '@/components/InputError.vue'
import Heading from '@/components/Heading.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'

export type Pedido = {
  id?: number
  descricao: string
  estoque_id: number | null
  fornecedor_id: number | null
  solicitado_por: number | null
  recebido_por: number | null
  quantidade: string
  preco_unitario: string
  status: 'pendente' | 'pedido' | 'recebido' | 'cancelado'
  numero_pedido: string | null
  data_solicitacao: string | null
  data_pedido: string | null
  data_previsao: string | null
  data_recebimento: string | null
  observacao: string
}

export type User = {
  id: number
  name: string
}

export type Estoque = {
  id: number
  nome: string
}

export type Fornecedor = {
  id: number
  nome: string
}

type Props = {
  mode: 'create' | 'edit'
  pedido?: Pedido | null
  users?: User[]
  estoques?: Estoque[]
  fornecedores?: Fornecedor[]
}

const props = defineProps<Props>()
const emit = defineEmits<{ cancel: [] }>()

const actionUrl = props.mode === 'create'
  ? '/pedidos'
  : `/pedidos/${props.pedido?.id}?_method=PATCH`

const submitLabel = props.mode === 'create' ? 'Salvar' : 'Atualizar'
const title = props.mode === 'create' ? 'Novo Pedido' : 'Editar Pedido'
</script>
<template>
  <div class="space-y-4">
    <Heading variant="small" :title="title" />
    <Form :action="actionUrl" method="post" class="space-y-6" @success="emit('cancel')" v-slot="{ errors, processing }">
      <div class="grid gap-6 md:grid-cols-2">
        <div class="grid gap-2">
          <Label for="descricao">Descrição *</Label>
          <Input id="descricao" name="descricao" type="text" required placeholder="Nome da peça ou item"
            :default-value="pedido?.descricao ?? ''" />
          <InputError :message="errors.descricao" />
        </div>
        <div class="grid gap-2">
          <Label for="quantidade">Quantidade *</Label>
          <Input id="quantidade" name="quantidade" type="number" min="1" required placeholder="1"
            :default-value="pedido?.quantidade ?? '1'" />
          <InputError :message="errors.quantidade" />
        </div>
        <div class="grid gap-2">
          <Label for="estoque_id">Peça do estoque</Label>
          <select id="estoque_id" name="estoque_id" class="border rounded-md px-3 py-2 bg-background text-sm">
            <option value="">— Selecionar ou digitar manualmente —</option>
            <option v-for="e in estoques" :key="e.id" :value="e.id" :selected="pedido?.estoque_id === e.id">
              {{ e.nome }}
            </option>
          </select>
          <InputError :message="errors.estoque_id" />
        </div>
        <div class="grid gap-2">
          <Label for="fornecedor_id">Fornecedor</Label>
          <select id="fornecedor_id" name="fornecedor_id" class="border rounded-md px-3 py-2 bg-background text-sm">
            <option value="">— Sem fornecedor definido —</option>
            <option v-for="f in fornecedores" :key="f.id" :value="f.id" :selected="pedido?.fornecedor_id === f.id">
              {{ f.nome }}
            </option>
          </select>
          <InputError :message="errors.fornecedor_id" />
        </div>
        <div class="grid gap-2">
          <Label for="solicitado_por">Solicitado por</Label>
          <select id="solicitado_por" name="solicitado_por" class="border rounded-md px-3 py-2 bg-background text-sm">
            <option value="">— Selecionar —</option>
            <option v-for="u in users" :key="u.id" :value="u.id" :selected="pedido?.solicitado_por === u.id">
              {{ u.name }}
            </option>
          </select>
          <InputError :message="errors.solicitado_por" />
        </div>
        <div class="grid gap-2">
          <Label for="recebido_por">Recebido por</Label>
          <select id="recebido_por" name="recebido_por" class="border rounded-md px-3 py-2 bg-background text-sm">
            <option value="">— Selecionar —</option>
            <option v-for="u in users" :key="u.id" :value="u.id" :selected="pedido?.recebido_por === u.id">
              {{ u.name }}
            </option>
          </select>
          <InputError :message="errors.recebido_por" />
        </div>
        <div class="grid gap-2">
          <Label for="preco_unitario">Preço unitário (R$)</Label>
          <Input id="preco_unitario" name="preco_unitario" type="number" step="0.01" min="0" placeholder="0,00"
            :default-value="pedido?.preco_unitario ?? ''" />
          <InputError :message="errors.preco_unitario" />
        </div>
        <div class="grid gap-2">
          <Label for="status">Status</Label>
          <select id="status" name="status" class="border rounded-md px-3 py-2 bg-background text-sm">
            <option value="pendente" :selected="(pedido?.status ?? 'pendente') === 'pendente'">Pendente</option>
            <option value="pedido" :selected="pedido?.status === 'pedido'">Pedido</option>
            <option value="recebido" :selected="pedido?.status === 'recebido'">Recebido</option>
            <option value="cancelado" :selected="pedido?.status === 'cancelado'">Cancelado</option>
          </select>
          <InputError :message="errors.status" />
        </div>
        <div class="grid gap-2">
          <Label for="numero_pedido">Nº do pedido (fornecedor)</Label>
          <Input id="numero_pedido" name="numero_pedido" type="text" placeholder="Ex: PED-2024-001"
            :default-value="pedido?.numero_pedido ?? ''" />
          <InputError :message="errors.numero_pedido" />
        </div>
        <div class="grid gap-2">
          <Label for="data_solicitacao">Data de solicitação</Label>
          <Input id="data_solicitacao" name="data_solicitacao" type="date"
            :default-value="pedido?.data_solicitacao ?? new Date().toISOString().split('T')[0]" />
          <InputError :message="errors.data_solicitacao" />
        </div>
        <div class="grid gap-2">
          <Label for="data_pedido">Data do pedido</Label>
          <Input id="data_pedido" name="data_pedido" type="date" :default-value="pedido?.data_pedido ?? ''" />
          <InputError :message="errors.data_pedido" />
        </div>
        <div class="grid gap-2">
          <Label for="data_previsao">Previsão de chegada</Label>
          <Input id="data_previsao" name="data_previsao" type="date" :default-value="pedido?.data_previsao ?? ''" />
          <InputError :message="errors.data_previsao" />
        </div>
        <div class="grid gap-2">
          <Label for="data_recebimento">Data de recebimento</Label>
          <Input id="data_recebimento" name="data_recebimento" type="date"
            :default-value="pedido?.data_recebimento ?? ''" />
          <InputError :message="errors.data_recebimento" />
        </div>
        <div class="grid gap-2 md:col-span-2">
          <Label for="observacao">Observação</Label>
          <textarea id="observacao" name="observacao" rows="3" placeholder="Observações sobre o pedido..."
            class="border rounded-md px-3 py-2 bg-background text-sm resize-none w-full">{{ pedido?.observacao ?? '' }}</textarea>
          <InputError :message="errors.observacao" />
        </div>
      </div>
      <div class="flex items-center gap-3">
        <Button type="submit" :disabled="processing">
          <Spinner v-if="processing" class="mr-2 inline" />
          {{ submitLabel }}
        </Button>
        <Button type="button" variant="secondary" :disabled="processing" @click="emit('cancel')">
          Cancelar
        </Button>
      </div>
    </Form>
  </div>
</template>