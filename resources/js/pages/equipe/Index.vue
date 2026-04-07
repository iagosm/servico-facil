<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import GenericTable from '@/components/tables/GenericTable.vue';
import EquipeForm, { type Equipe } from '@/components/equipe/EquipeForm.vue';
import { Spinner } from '@/components/ui/spinner';
import {
  Dialog,
  DialogTrigger,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
  DialogClose,
} from '@/components/ui/dialog';

type Equipe = EquipeForm & { id: number };

type Props = {
  equipe: {
    data: Equipe[];
  };
  supervisores: Array<{
    id: number;
    name: string;
  }>;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Equipe',
    href: '/equipe',
  },
];

const columns = [
  { key: 'name', header: 'Nome' },
  { key: 'email', header: 'E-mail' },
  { key: 'cargo', header: 'Cargo' },
  { key: 'telefone', header: 'Telefone' },
  { key: 'ativo', header: 'Ativo' },
  { key: 'supervisor', header: 'Supervisor' },
  { key: 'actions', header: 'Ações' },
];

const rowsWithSupervisor = computed(() =>
  props.equipe.data.map(e => ({
    ...e,
    supervisor_name: e.supervisor?.name || '-',
  }))
);

const activePane = ref<'list' | 'create' | 'edit'>('list');
const selectedCliente = ref<Equipe | null>(null);

const openCreate = () => {
  selectedCliente.value = null;
  activePane.value = 'create';
};

const openEdit = (cliente: Equipe) => {
  selectedCliente.value = cliente;
  activePane.value = 'edit';
};

const backToList = () => {
  activePane.value = 'list';
};
</script>

<template>
  <Head title="Equipe" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4">
      <div class="flex items-start justify-between gap-4">
        <div>
          <h1 class="text-xl font-semibold">Equipe</h1>
          <p class="mt-1 text-sm text-muted-foreground">
            Cadastre, edite e remova itens do equipe.
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

      <div class="mt-6">
        <div v-if="activePane === 'list'">
          <GenericTable :columns="columns" :rows="rowsWithSupervisor">
            <!-- Custom cell slots -->
            <template #cell-email="{ value }">
              {{ value || '-' }}
            </template>

            <template #cell-telefone="{ value }">
              {{ value || '-' }}
            </template>

            <template #cell-supervisor="{ row }">
              {{ row.supervisor_name }}
            </template>

            <template #cell-actions="{ row }">
              <div class="flex items-center gap-2">
                <Button type="button" size="sm" @click="openEdit(row)">
                  Editar
                </Button>

                <Dialog>
                  <DialogTrigger as-child>
                    <Button type="button" variant="destructive" size="sm">
                      Excluir
                    </Button>
                  </DialogTrigger>

                  <DialogContent>
                    <DialogHeader class="space-y-2">
                      <DialogTitle>Excluir item do equipe?</DialogTitle>
                      <DialogDescription>Esta ação não pode ser desfeita.</DialogDescription>
                    </DialogHeader>

                    <Form :action="`/equipe/${row.id}?_method=DELETE`" method="post" class="space-y-6"
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

        <div v-else-if="activePane === 'create'">
          <EquipeForm mode="create" :key="'create-pane'" @cancel="backToList" />
        </div>

        <div v-else-if="activePane === 'edit'">
          <EquipeForm mode="edit" :cliente="selectedCliente" :supervisor="props.supervisores" :key="selectedCliente?.id ?? 'edit-null'"
            @cancel="backToList" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>