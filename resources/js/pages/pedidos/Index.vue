<script setup lang="ts">
import { ref, watch } from 'vue';
import { Form, router } from '@inertiajs/vue3'
import {
  Dialog,
  DialogTrigger,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
  DialogClose,
} from '@/components/ui/dialog'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'
import GenericTable from '@/components/tables/GenericTable.vue'
import PedidoForm from '@/components/pedidos/PedidoForm.vue'
import type { BreadcrumbItem } from '@/types'
import { useDebounce } from '@vueuse/core';

type Pedido = {
  id: number
  descricao: string
  quantidade: number
  status: 'pendente' | 'pedido' | 'cancelado' | 'recebido'
  preco_unitario: number | null
  data_solicitacao: string | null
  data_pedido: string | null
  data_recebimento: string | null
  observacao: string | null
  estoque: { id: number; nome: string } | null
  fornecedor: { id: number; nome: string } | null
  solicitado_por: { id: number; name: string } | null
  recebido_por: { id: number; name: string } | null
}

type Resumo = {
  pendentes: number
  pedidos: number
  recebidos_mes: number
  atrasados: number
}

type Props = {
  pedidos: { data: Pedido[] }
  resumo: Resumo
  users: Array<{ id: number; name: string }>
  estoques: Array<{ id: number; nome: string }>
  fornecedores: Array<{ id: number; nome: string }>
  filters: { search?: string; perPage?: number }
}

const props = defineProps<Props>()
console.log(props.resumo)

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Pedidos', href: '/pedidos' },
]

const columns = [
  { key: 'descricao', header: 'Descrição' },
  { key: 'quantidade', header: 'Qtd' },
  { key: 'fornecedor', header: 'Fornecedor' },
  { key: 'solicitado_por', header: 'Solicitado por' },
  { key: 'status', header: 'Status' },
  { key: 'actions', header: 'Ações' },
]

const activePane = ref<'list' | 'create' | 'edit'>('list')
const selectedPedido = ref<Pedido | null>(null)

const backToList = () => {
  activePane.value = 'list'
}

const openCreate = () => {
  selectedPedido.value = null
  activePane.value = 'create'
}

const openEdit = (row: Pedido) => {
  selectedPedido.value = row
  activePane.value = 'edit'
}

const loading = ref(false)
router.on('start', () => {
    loading.value = true
})

router.on('finish', () => {
    loading.value = false
})
const perPage = ref(props.filters.perPage ?? 10)
const search = ref(props.filters.search ?? '')
const searchDebounced = useDebounce(search, 500)

function navegarComFiltros() {
    const params: Record<string, string | number> = {
        perPage: perPage.value,
    }

    if (searchDebounced.value) {
        params.search = searchDebounced.value
    }

    router.get('/pedidos', params, {
        preserveState: true,
        replace: true,
    })
}

watch(searchDebounced, navegarComFiltros)
watch(perPage, navegarComFiltros)
</script>
<template>
  <Head title="Pedidos" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 space-y-6">
      <!-- Header -->
      <div class="flex justify-between">
        <div>
          <h1 class="text-xl font-semibold">Pedidos</h1>
          <p class="mt-1 text-sm text-muted-foreground">
            Cadastre, edite e remova os pedidos.
          </p>
        </div>
        <div class="flex gap-2">
          <Button type="button" :variant="activePane === 'list' ? 'default' : 'secondary'" size="sm"
            @click="activePane = 'list'">
            Lista
          </Button>
          <Button type="button" :variant="activePane === 'create' ? 'default' : 'secondary'" size="sm"
            @click="openCreate()">
            Adicionar
          </Button>
        </div>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-lg border p-4">
          <p class="text-sm text-muted-foreground">Pendentes</p>
          <p class="text-2xl font-bold text-yellow-500">{{ props.resumo.pendentes ?? 0 }}</p>
        </div>
        <div class="rounded-lg border p-4">
          <p class="text-sm text-muted-foreground">Pedidos</p>
          <p class="text-2xl font-bold text-blue-500">{{ props.resumo.pedidos ?? 0 }}</p>
        </div>
        <div class="rounded-lg border p-4">
          <p class="text-sm text-muted-foreground">Recebidos no mês</p>
          <p class="text-2xl font-bold text-green-500">{{ props.resumo.recebidos_mes ?? 0 }}</p>
        </div>
        <div class="rounded-lg border p-4">
          <p class="text-sm text-muted-foreground">Atrasados</p>
          <p class="text-2xl font-bold text-red-500">{{ props.resumo.atrasados ?? 0 }}</p>
        </div>
      </div>
      <div v-if="activePane === 'list'">
        <GenericTable :columns="columns" :rows="pedidos.data" v-model:per-page="perPage"
          :loading="loading"
          v-model:search="search"
          :pagination="pedidos">
          <template #cell-fornecedor="{ value }">
            {{ value?.nome ?? '—' }}
          </template>
          <template #cell-solicitado_por="{ value }">
            {{ value?.name ?? '—' }}
          </template>
          <template #cell-actions="{ row }">
            <div class="flex items-center gap-2">
              <!-- EDITAR -->
              <Button type="button" size="sm" @click="openEdit(row)">
                Editar
              </Button>
              <!-- EXCLUIR -->
              <Dialog>
                <DialogTrigger as-child>
                  <Button type="button" variant="destructive" size="sm">
                    Excluir
                  </Button>
                </DialogTrigger>
                <DialogContent>
                  <DialogHeader class="space-y-2">
                    <DialogTitle>
                      Excluir pedido?
                    </DialogTitle>
                    <DialogDescription>
                      Esta ação não pode ser desfeita.
                    </DialogDescription>
                  </DialogHeader>
                  <Form :action="`/pedidos/${row.id}?_method=DELETE`" method="post" class="space-y-6"
                    @success="backToList" v-slot="{ processing }">
                    <DialogFooter class="gap-2">
                      <DialogClose as-child>
                        <Button type="button" variant="secondary" :disabled="processing">
                          Cancelar
                        </Button>
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
      <!-- CREATE -->
      <div v-else-if="activePane === 'create'">
        <PedidoForm mode="create" :users="props.users" :estoques="props.estoques" :fornecedores="props.fornecedores"
          @cancel="backToList" />
      </div>
      <!-- EDIT -->
      <div v-else-if="activePane === 'edit'">
        <PedidoForm mode="edit" :pedido="selectedPedido" :users="props.users" :estoques="props.estoques"
          :fornecedores="props.fornecedores" :key="selectedPedido?.id ?? 'edit-null'" @cancel="backToList" />
      </div>
    </div>
  </AppLayout>
</template>