<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import GenericTable from '@/components/tables/GenericTable.vue';
import ClienteForm, { type Cliente as ClienteFormCliente } from '@/components/cliente/ClienteForm.vue';
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

type Cliente = ClienteFormCliente & { id: number };

type Props = {
    clientes: {
        data: Cliente[];
    },
    filters: { search?: string; perPage?: number };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clientes',
        href: '/clientes',
    },
];

const columns = [
    { key: 'nome', header: 'Nome' },
    { key: 'email', header: 'Email' },
    { key: 'telefone', header: 'Telefone' },
    { key: 'actions', header: 'Ações' },
];

const activePane = ref<'list' | 'create' | 'edit'>('list');
const selectedCliente = ref<Cliente | null>(null);

const openCreate = () => {
    selectedCliente.value = null;
    activePane.value = 'create';
};

const openEdit = (cliente: any) => {
    selectedCliente.value = cliente as Cliente;
    activePane.value = 'edit';
};

const backToList = () => {
    activePane.value = 'list';
};

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

    router.get('/clientes', params, {
        preserveState: true,
        replace: true,
    })
}

watch(searchDebounced, navegarComFiltros)
watch(perPage, navegarComFiltros)
</script>

<template>
    <Head title="Clientes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold">Clientes</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Cadastre, edite e remova clientes.
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
                        :rows="props.clientes.data"
                        v-model:per-page="perPage"
                        :loading="loading"
                        :pagination="clientes"
                        v-model:search="search"
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
                                                Excluir cliente?
                                            </DialogTitle>
                                            <DialogDescription>
                                                Esta ação não pode ser desfeita.
                                            </DialogDescription>
                                        </DialogHeader>

                                        <Form
                                            :action="`/clientes/${row.id}?_method=DELETE`"
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
                    <ClienteForm
                        mode="create"
                        :key="'create-pane'"
                        @cancel="backToList"
                    />
                </div>

                <div v-else-if="activePane === 'edit'">
                    <ClienteForm
                        mode="edit"
                        :cliente="selectedCliente"
                        :key="selectedCliente?.id ?? 'edit-null'"
                        @cancel="backToList"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
