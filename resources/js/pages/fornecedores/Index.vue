<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import GenericTable from '@/components/tables/GenericTable.vue';
import FornecedoresForm, { type Fornecedores } from '@/components/fornecedores/FornecedoresForm.vue';
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
import { useDebounce } from '@vueuse/core';

type Props = {
    fornecedores: {
        data: Fornecedores[];
    },
    filters: { search?: string; perPage?: number };
};
const props = defineProps<Props>();
  console.log(props.filters)

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Fornecedores',
        href: '/fornecedores',
    },
];

const columns = [
    { key: 'nome', header: 'Nome' },
    { key: 'contato', header: 'Contato' },
    { key: 'telefone', header: 'Telefone' },
    { key: 'email', header: 'E-mail' },
    { key: 'site', header: 'Site' },
    { key: 'status', header: 'Status' },
    { key: 'actions', header: 'Ações' },
];

const activePane = ref<'list' | 'create' | 'edit'>('list');
const selectedFornecedores = ref<Fornecedores | null>(null);

const openCreate = () => {
    selectedFornecedores.value = null;
    activePane.value = 'create';
};

const openEdit = (fornecedor: any) => {
  console.log(fornecedor)
    selectedFornecedores.value = fornecedor as Fornecedores;
    activePane.value = 'edit';
};

const backToList = () => {
    activePane.value = 'list';
};

// ── Busca com debounce ──────────────────────────────────
const search = ref(props.filters.search ?? '')
const searchDebounced = useDebounce(search, 500)

// ── Quantidade por página ───────────────────────────────
const perPage = ref(props.filters.perPage ?? 10)

// ── Loading ─────────────────────────────────────────────
const loading = ref(false)

router.on('start', () => {
    loading.value = true
})

router.on('finish', () => {
    loading.value = false
})

// ── Navegação com filtros ───────────────────────────────
function navegarComFiltros() {
    const params: Record<string, string | number> = {
        perPage: perPage.value,
    }

    if (searchDebounced.value) {
        params.search = searchDebounced.value
    }

    router.get('/fornecedores', params, {
        preserveState: true,
        replace: true,
    })
}

watch(searchDebounced, navegarComFiltros)
watch(perPage, navegarComFiltros)

</script>
<template>
    <Head title="Fornecedores" />

    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="p-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold">Fornecedores</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Cadastre, edite e remova os fornecedores.
                    </p>
                </div>

                <div class="flex gap-2">
                    <Button
                        type="button"
                        :variant="activePane === 'list' ? 'default' : 'secondary'"
                        size="sm"
                        @click="activePane = 'list'"
                    >
                        Lista
                    </Button>
                    <Button
                        type="button"
                        :variant="activePane === 'create' ? 'default' : 'secondary'"
                        size="sm"
                        @click="openCreate()"
                    >
                        Adicionar
                    </Button>
                </div>
            </div>

            <div class="mt-6">
                <div v-if="activePane === 'list'">
                    <GenericTable
                        :columns="columns"
                        :rows="props.fornecedores.data"
                        :loading="loading"
                        v-model:search="search"
                        v-model:per-page="perPage"
                    >
                        <template #cell-email="{ value }">
                            {{ value || '-' }}
                        </template>

                        <template #cell-telefone="{ value }">
                            {{ value || '-' }}
                        </template>

                        <template #cell-actions="{ row }">
                            <div class="flex items-center gap-2">
                                <Button
                                    type="button"
                                    size="sm"
                                    @click="openEdit(row)"
                                >
                                    Editar
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button
                                            type="button"
                                            variant="destructive"
                                            size="sm"
                                        >
                                            Excluir
                                        </Button>
                                    </DialogTrigger>

                                    <DialogContent>
                                        <DialogHeader class="space-y-2">
                                            <DialogTitle>
                                                Excluir item do fornecedores?
                                            </DialogTitle>
                                            <DialogDescription>
                                                Esta ação não pode ser desfeita.
                                            </DialogDescription>
                                        </DialogHeader>

                                        <Form
                                            :action="`/fornecedores/${row.id}?_method=DELETE`"
                                            method="post"
                                            class="space-y-6"
                                            @success="backToList"
                                            v-slot="{ processing }"
                                        >
                                            <DialogFooter class="gap-2">
                                                <DialogClose as-child>
                                                    <Button
                                                        type="button"
                                                        variant="secondary"
                                                        :disabled="processing"
                                                    >
                                                        Cancelar
                                                    </Button>
                                                </DialogClose>

                                                <Button
                                                    type="submit"
                                                    variant="destructive"
                                                    :disabled="processing"
                                                >
                                                    <Spinner
                                                        v-if="processing"
                                                        class="mr-2 inline"
                                                    />
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
                    <FornecedoresForm
                        mode="create"
                        :key="'create-pane'"
                        @cancel="backToList"
                    />
                </div>

                <div v-else-if="activePane === 'edit'">
                    <FornecedoresForm
                        mode="edit"
                        :fornecedores="selectedFornecedores"
                        :key="selectedFornecedores?.id ?? 'edit-null'"
                        @cancel="backToList"
                    />
                </div>
            </div>
      </div>
    </AppLayout>
</template>